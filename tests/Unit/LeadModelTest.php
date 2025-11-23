<?php

namespace Tests\Unit;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_correct_fillable_attributes()
    {
        $user = User::factory()->create();
        $property = Property::factory()->create();

        $lead = Lead::factory()->create([
            'affiliate_id' => $user->id,
            'property_id' => $property->id,
            'name' => 'Jane Doe',
            'whatsapp' => '081234567890',
            'status' => LeadStatus::NEW,
            'notes' => 'Test notes',
        ]);

        $this->assertEquals($user->id, $lead->affiliate_id);
        $this->assertEquals($property->id, $lead->property_id);
        $this->assertEquals('Jane Doe', $lead->name);
        $this->assertEquals('081234567890', $lead->whatsapp);
        $this->assertEquals(LeadStatus::NEW, $lead->status);
        $this->assertEquals('Test notes', $lead->notes);
    }

    /** @test */
    public function it_casts_status_to_enum()
    {
        $lead = Lead::factory()->create(['status' => LeadStatus::NEW]);

        $this->assertInstanceOf(LeadStatus::class, $lead->status);
        $this->assertEquals(LeadStatus::NEW, $lead->status);
    }

    /** @test */
    public function it_belongs_to_affiliate()
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create(['affiliate_id' => $user->id]);

        $this->assertInstanceOf(User::class, $lead->affiliate);
        $this->assertEquals($user->id, $lead->affiliate->id);
    }

    /** @test */
    public function it_belongs_to_property()
    {
        $property = Property::factory()->create();
        $lead = Lead::factory()->create(['property_id' => $property->id]);

        $this->assertInstanceOf(Property::class, $lead->property);
        $this->assertEquals($property->id, $lead->property->id);
    }

    /** @test */
    public function it_can_have_null_affiliate()
    {
        $lead = Lead::factory()->create(['affiliate_id' => null]);

        $this->assertNull($lead->affiliate_id);
        $this->assertNull($lead->affiliate);
    }

    /** @test */
    public function mark_as_follow_up_updates_status()
    {
        $lead = Lead::factory()->create(['status' => LeadStatus::NEW]);

        $lead->markAsFollowUp();

        $this->assertEquals(LeadStatus::FOLLOW_UP, $lead->status);
        $this->assertDatabaseHas('leads', [
            'id' => $lead->id,
            'status' => LeadStatus::FOLLOW_UP->value,
        ]);
    }

    /** @test */
    public function mark_as_survey_updates_status()
    {
        $lead = Lead::factory()->create(['status' => LeadStatus::FOLLOW_UP]);

        $lead->markAsSurvey();

        $this->assertEquals(LeadStatus::SURVEY, $lead->status);
        $this->assertDatabaseHas('leads', [
            'id' => $lead->id,
            'status' => LeadStatus::SURVEY->value,
        ]);
    }

    /** @test */
    public function mark_as_closed_updates_status()
    {
        $lead = Lead::factory()->create(['status' => LeadStatus::SURVEY]);

        $lead->markAsClosed();

        $this->assertEquals(LeadStatus::CLOSED, $lead->status);
        $this->assertDatabaseHas('leads', [
            'id' => $lead->id,
            'status' => LeadStatus::CLOSED->value,
        ]);
    }

    /** @test */
    public function mark_as_lost_updates_status()
    {
        $lead = Lead::factory()->create(['status' => LeadStatus::FOLLOW_UP]);

        $lead->markAsLost();

        $this->assertEquals(LeadStatus::LOST, $lead->status);
        $this->assertDatabaseHas('leads', [
            'id' => $lead->id,
            'status' => LeadStatus::LOST->value,
        ]);
    }

    /** @test */
    public function status_transitions_work_from_any_status()
    {
        $lead = Lead::factory()->create(['status' => LeadStatus::CLOSED]);

        // Can transition from closed to follow_up
        $lead->markAsFollowUp();
        $this->assertEquals(LeadStatus::FOLLOW_UP, $lead->status);

        // Can transition from follow_up to lost
        $lead->markAsLost();
        $this->assertEquals(LeadStatus::LOST, $lead->status);

        // Can transition from lost to survey
        $lead->markAsSurvey();
        $this->assertEquals(LeadStatus::SURVEY, $lead->status);
    }
}
