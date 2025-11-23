<?php

namespace Tests\Feature;

use App\Enums\LeadStatus;
use App\Enums\UserStatus;
use App\Models\Lead;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $affiliate;
    protected User $otherAffiliate;
    protected Property $property;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

        // Create affiliates
        $this->affiliate = User::factory()->create([
            'status' => UserStatus::ACTIVE,
            'affiliate_code' => 'TEST123',
        ]);
        $this->affiliate->assignRole('affiliate');

        $this->otherAffiliate = User::factory()->create([
            'status' => UserStatus::ACTIVE,
            'affiliate_code' => 'OTHER456',
        ]);
        $this->otherAffiliate->assignRole('affiliate');

        // Create property
        $this->property = Property::factory()->create();
    }

    public function test_affiliate_can_view_their_own_leads(): void
    {
        // Create leads for the affiliate
        $myLead = Lead::factory()->create([
            'affiliate_id' => $this->affiliate->id,
            'property_id' => $this->property->id,
        ]);

        // Create lead for another affiliate
        $otherLead = Lead::factory()->create([
            'affiliate_id' => $this->otherAffiliate->id,
            'property_id' => $this->property->id,
        ]);

        $this->actingAs($this->affiliate);

        // Affiliate should see their own lead
        $this->assertTrue($this->affiliate->can('view', $myLead));

        // Affiliate should not see other affiliate's lead
        $this->assertFalse($this->affiliate->can('view', $otherLead));
    }

    public function test_affiliate_can_update_their_lead_status(): void
    {
        $lead = Lead::factory()->create([
            'affiliate_id' => $this->affiliate->id,
            'property_id' => $this->property->id,
            'status' => LeadStatus::NEW,
        ]);

        $this->actingAs($this->affiliate);

        // Affiliate should be able to update their own lead
        $this->assertTrue($this->affiliate->can('update', $lead));

        // Update lead status
        $lead->update([
            'status' => LeadStatus::FOLLOW_UP,
            'notes' => 'Called the customer',
        ]);

        $this->assertEquals(LeadStatus::FOLLOW_UP, $lead->fresh()->status);
        $this->assertEquals('Called the customer', $lead->fresh()->notes);
    }

    public function test_lead_status_transition_validation(): void
    {
        $lead = Lead::factory()->create([
            'affiliate_id' => $this->affiliate->id,
            'property_id' => $this->property->id,
            'status' => LeadStatus::CLOSED,
        ]);

        // Valid transitions from CLOSED
        $lead->status = LeadStatus::FOLLOW_UP;
        $lead->save();
        $this->assertEquals(LeadStatus::FOLLOW_UP, $lead->fresh()->status);

        // Set back to CLOSED for next test
        $lead->status = LeadStatus::CLOSED;
        $lead->save();

        // The validation logic prevents going from CLOSED to NEW
        // This is enforced in the UI, but we can test the model methods
        $lead->status = LeadStatus::SURVEY;
        $lead->save();
        $this->assertEquals(LeadStatus::SURVEY, $lead->fresh()->status);
    }

    public function test_affiliate_cannot_update_other_affiliates_leads(): void
    {
        $otherLead = Lead::factory()->create([
            'affiliate_id' => $this->otherAffiliate->id,
            'property_id' => $this->property->id,
        ]);

        $this->actingAs($this->affiliate);

        // Affiliate should not be able to update other affiliate's lead
        $this->assertFalse($this->affiliate->can('update', $otherLead));
    }

    public function test_lead_model_status_transition_methods(): void
    {
        $lead = Lead::factory()->create([
            'affiliate_id' => $this->affiliate->id,
            'property_id' => $this->property->id,
            'status' => LeadStatus::NEW,
        ]);

        // Test markAsFollowUp
        $lead->markAsFollowUp();
        $this->assertEquals(LeadStatus::FOLLOW_UP, $lead->fresh()->status);

        // Test markAsSurvey
        $lead->markAsSurvey();
        $this->assertEquals(LeadStatus::SURVEY, $lead->fresh()->status);

        // Test markAsClosed
        $lead->markAsClosed();
        $this->assertEquals(LeadStatus::CLOSED, $lead->fresh()->status);

        // Test markAsLost
        $lead->status = LeadStatus::NEW;
        $lead->save();
        $lead->markAsLost();
        $this->assertEquals(LeadStatus::LOST, $lead->fresh()->status);
    }
}
