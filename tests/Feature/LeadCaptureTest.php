<?php

namespace Tests\Feature;

use App\Enums\LeadStatus;
use App\Enums\PropertyStatus;
use App\Enums\UserStatus;
use App\Events\LeadCreated;
use App\Models\Lead;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class LeadCaptureTest extends TestCase
{
    use RefreshDatabase;

    protected Property $property;
    protected User $affiliate;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a published property
        $this->property = Property::factory()->create([
            'status' => PropertyStatus::PUBLISHED,
            'slug' => 'test-property',
        ]);

        // Create an active affiliate
        $this->affiliate = User::factory()->create([
            'affiliate_code' => 'TEST123',
            'status' => UserStatus::ACTIVE,
            'whatsapp' => '081234567890',
        ]);
    }

    public function test_lead_created_from_contact_form_with_affiliate(): void
    {
        // Set affiliate cookie
        Livewire::withCookie('affiliate_id', (string) $this->affiliate->id)
            ->test('contact-form', ['property' => $this->property])
            ->set('name', 'John Doe')
            ->set('whatsapp', '081234567890')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('name', '')
            ->assertSet('whatsapp', '');

        // Assert lead was created
        $this->assertDatabaseHas('leads', [
            'affiliate_id' => $this->affiliate->id,
            'property_id' => $this->property->id,
            'name' => 'John Doe',
            'whatsapp' => '081234567890',
            'status' => LeadStatus::NEW->value,
        ]);
    }

    public function test_lead_created_from_contact_form_without_affiliate(): void
    {
        Livewire::test('contact-form', ['property' => $this->property])
            ->set('name', 'Jane Smith')
            ->set('whatsapp', '089876543210')
            ->call('submit')
            ->assertHasNoErrors();

        // Assert lead was created with null affiliate_id
        $this->assertDatabaseHas('leads', [
            'affiliate_id' => null,
            'property_id' => $this->property->id,
            'name' => 'Jane Smith',
            'whatsapp' => '089876543210',
            'status' => LeadStatus::NEW->value,
        ]);
    }

    public function test_contact_form_validates_required_fields(): void
    {
        Livewire::test('contact-form', ['property' => $this->property])
            ->set('name', '')
            ->set('whatsapp', '')
            ->call('submit')
            ->assertHasErrors(['name', 'whatsapp']);
    }

    public function test_contact_form_validates_whatsapp_format(): void
    {
        Livewire::test('contact-form', ['property' => $this->property])
            ->set('name', 'Test User')
            ->set('whatsapp', 'invalid')
            ->call('submit')
            ->assertHasErrors(['whatsapp']);

        // Test too short
        Livewire::test('contact-form', ['property' => $this->property])
            ->set('name', 'Test User')
            ->set('whatsapp', '123')
            ->call('submit')
            ->assertHasErrors(['whatsapp']);

        // Test too long
        Livewire::test('contact-form', ['property' => $this->property])
            ->set('name', 'Test User')
            ->set('whatsapp', '12345678901234567890')
            ->call('submit')
            ->assertHasErrors(['whatsapp']);

        // Test valid format
        Livewire::test('contact-form', ['property' => $this->property])
            ->set('name', 'Test User')
            ->set('whatsapp', '081234567890')
            ->call('submit')
            ->assertHasNoErrors();
    }

    public function test_lead_created_event_dispatched_on_form_submission(): void
    {
        Event::fake([LeadCreated::class]);

        Livewire::withCookie('affiliate_id', (string) $this->affiliate->id)
            ->test('contact-form', ['property' => $this->property])
            ->set('name', 'Event Test')
            ->set('whatsapp', '081234567890')
            ->call('submit');

        // Assert LeadCreated event was dispatched
        Event::assertDispatched(LeadCreated::class, function ($event) {
            return $event->lead->name === 'Event Test' &&
                   $event->lead->whatsapp === '081234567890' &&
                   $event->lead->affiliate_id === $this->affiliate->id;
        });
    }

    public function test_whatsapp_notification_sent_to_affiliate(): void
    {
        // Fake HTTP requests
        Http::fake([
            '*/send/message' => Http::response([
                'success' => true,
                'message' => 'Message sent',
            ], 200),
        ]);

        // Create lead which triggers the event and listener
        $lead = Lead::create([
            'affiliate_id' => $this->affiliate->id,
            'property_id' => $this->property->id,
            'name' => 'WhatsApp Test',
            'whatsapp' => '081234567890',
            'status' => LeadStatus::NEW,
        ]);

        event(new LeadCreated($lead));

        // Wait for queued job to process
        $this->artisan('queue:work --once --stop-when-empty');

        // Assert HTTP request was made to GoWA API
        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/send/message') &&
                   $request->hasHeader('Authorization') &&
                   str_contains($request['message'], 'WhatsApp Test') &&
                   str_contains($request['message'], $this->property->title);
        });
    }

    public function test_whatsapp_notification_sent_to_visitor(): void
    {
        // Fake HTTP requests
        Http::fake([
            '*/send/message' => Http::response([
                'success' => true,
                'message' => 'Message sent',
            ], 200),
        ]);

        // Create lead
        $lead = Lead::create([
            'affiliate_id' => $this->affiliate->id,
            'property_id' => $this->property->id,
            'name' => 'Visitor Test',
            'whatsapp' => '089876543210',
            'status' => LeadStatus::NEW,
        ]);

        event(new LeadCreated($lead));

        // Wait for queued job to process
        $this->artisan('queue:work --once --stop-when-empty');

        // Assert two HTTP requests were made (one to affiliate, one to visitor)
        Http::assertSentCount(2);

        // Assert visitor confirmation was sent
        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/send/message') &&
                   str_contains($request['message'], 'Terima kasih') &&
                   str_contains($request['message'], 'Visitor Test');
        });
    }

    public function test_lead_assignment_to_affiliate_from_cookie(): void
    {
        // Create lead with affiliate cookie
        Livewire::withCookie('affiliate_id', (string) $this->affiliate->id)
            ->test('contact-form', ['property' => $this->property])
            ->set('name', 'Cookie Test')
            ->set('whatsapp', '081234567890')
            ->call('submit');

        // Get the created lead
        $lead = Lead::where('name', 'Cookie Test')->first();

        $this->assertNotNull($lead);
        $this->assertEquals($this->affiliate->id, $lead->affiliate_id);
        $this->assertEquals($this->property->id, $lead->property_id);
    }

    public function test_lead_created_without_affiliate_when_no_cookie(): void
    {
        Livewire::test('contact-form', ['property' => $this->property])
            ->set('name', 'No Cookie Test')
            ->set('whatsapp', '081234567890')
            ->call('submit');

        // Get the created lead
        $lead = Lead::where('name', 'No Cookie Test')->first();

        $this->assertNotNull($lead);
        $this->assertNull($lead->affiliate_id);
        $this->assertEquals($this->property->id, $lead->property_id);
    }

    public function test_notification_not_sent_when_affiliate_has_no_whatsapp(): void
    {
        // Create affiliate without WhatsApp
        $affiliateNoWhatsApp = User::factory()->create([
            'affiliate_code' => 'NOWHATSAPP',
            'status' => UserStatus::ACTIVE,
            'whatsapp' => null,
        ]);

        Http::fake([
            '*/send/message' => Http::response([
                'success' => true,
                'message' => 'Message sent',
            ], 200),
        ]);

        // Create lead
        $lead = Lead::create([
            'affiliate_id' => $affiliateNoWhatsApp->id,
            'property_id' => $this->property->id,
            'name' => 'No WhatsApp Test',
            'whatsapp' => '081234567890',
            'status' => LeadStatus::NEW,
        ]);

        event(new LeadCreated($lead));

        // Wait for queued job to process
        $this->artisan('queue:work --once --stop-when-empty');

        // Only visitor confirmation should be sent (1 request)
        Http::assertSentCount(1);
    }

    public function test_lead_creation_continues_when_whatsapp_api_fails(): void
    {
        // Fake HTTP to return error
        Http::fake([
            '*/send/message' => Http::response([
                'success' => false,
                'message' => 'API Error',
            ], 500),
        ]);

        // Create lead
        $lead = Lead::create([
            'affiliate_id' => $this->affiliate->id,
            'property_id' => $this->property->id,
            'name' => 'API Fail Test',
            'whatsapp' => '081234567890',
            'status' => LeadStatus::NEW,
        ]);

        event(new LeadCreated($lead));

        // Wait for queued job to process
        $this->artisan('queue:work --once --stop-when-empty');

        // Assert lead still exists in database
        $this->assertDatabaseHas('leads', [
            'name' => 'API Fail Test',
            'affiliate_id' => $this->affiliate->id,
        ]);
    }

    public function test_success_message_displayed_after_form_submission(): void
    {
        Livewire::test('contact-form', ['property' => $this->property])
            ->set('name', 'Success Test')
            ->set('whatsapp', '081234567890')
            ->call('submit')
            ->assertSessionHas('success', 'Terima kasih! Kami akan segera menghubungi Anda.');
    }

    public function test_form_resets_after_successful_submission(): void
    {
        Livewire::test('contact-form', ['property' => $this->property])
            ->set('name', 'Reset Test')
            ->set('whatsapp', '081234567890')
            ->call('submit')
            ->assertSet('name', '')
            ->assertSet('whatsapp', '');
    }

    public function test_multiple_leads_can_be_created_for_same_property(): void
    {
        // Create first lead
        Livewire::withCookie('affiliate_id', (string) $this->affiliate->id)
            ->test('contact-form', ['property' => $this->property])
            ->set('name', 'First Lead')
            ->set('whatsapp', '081111111111')
            ->call('submit');

        // Create second lead
        Livewire::withCookie('affiliate_id', (string) $this->affiliate->id)
            ->test('contact-form', ['property' => $this->property])
            ->set('name', 'Second Lead')
            ->set('whatsapp', '082222222222')
            ->call('submit');

        // Assert both leads exist
        $this->assertDatabaseHas('leads', [
            'name' => 'First Lead',
            'property_id' => $this->property->id,
        ]);

        $this->assertDatabaseHas('leads', [
            'name' => 'Second Lead',
            'property_id' => $this->property->id,
        ]);

        $this->assertEquals(2, Lead::where('property_id', $this->property->id)->count());
    }

    public function test_lead_status_set_to_new_on_creation(): void
    {
        Livewire::test('contact-form', ['property' => $this->property])
            ->set('name', 'Status Test')
            ->set('whatsapp', '081234567890')
            ->call('submit');

        $lead = Lead::where('name', 'Status Test')->first();

        $this->assertNotNull($lead);
        $this->assertEquals(LeadStatus::NEW, $lead->status);
    }

    public function test_whatsapp_notification_includes_correct_message_format(): void
    {
        Http::fake([
            '*/send/message' => Http::response([
                'success' => true,
                'message' => 'Message sent',
            ], 200),
        ]);

        $lead = Lead::create([
            'affiliate_id' => $this->affiliate->id,
            'property_id' => $this->property->id,
            'name' => 'Format Test',
            'whatsapp' => '081234567890',
            'status' => LeadStatus::NEW,
        ]);

        event(new LeadCreated($lead));

        // Wait for queued job to process
        $this->artisan('queue:work --once --stop-when-empty');

        // Assert affiliate notification has correct format
        Http::assertSent(function ($request) {
            $message = $request['message'];
            return str_contains($message, 'Halo, ada prospek baru') &&
                   str_contains($message, 'Format Test') &&
                   str_contains($message, $this->property->title) &&
                   str_contains($message, 'Segera follow up!');
        });
    }

    public function test_visitor_confirmation_includes_correct_message_format(): void
    {
        Http::fake([
            '*/send/message' => Http::response([
                'success' => true,
                'message' => 'Message sent',
            ], 200),
        ]);

        $lead = Lead::create([
            'affiliate_id' => $this->affiliate->id,
            'property_id' => $this->property->id,
            'name' => 'Confirmation Test',
            'whatsapp' => '081234567890',
            'status' => LeadStatus::NEW,
        ]);

        event(new LeadCreated($lead));

        // Wait for queued job to process
        $this->artisan('queue:work --once --stop-when-empty');

        // Assert visitor confirmation has correct format
        Http::assertSent(function ($request) {
            $message = $request['message'];
            return str_contains($message, 'Terima kasih') &&
                   str_contains($message, 'Confirmation Test') &&
                   str_contains($message, $this->property->title) &&
                   str_contains($message, 'Tim kami akan segera menghubungi Anda');
        });
    }

    public function test_lead_relationships_loaded_correctly(): void
    {
        $lead = Lead::create([
            'affiliate_id' => $this->affiliate->id,
            'property_id' => $this->property->id,
            'name' => 'Relationship Test',
            'whatsapp' => '081234567890',
            'status' => LeadStatus::NEW,
        ]);

        $lead->load(['affiliate', 'property']);

        $this->assertNotNull($lead->affiliate);
        $this->assertEquals($this->affiliate->id, $lead->affiliate->id);
        $this->assertNotNull($lead->property);
        $this->assertEquals($this->property->id, $lead->property->id);
    }

    public function test_different_affiliates_receive_their_own_leads(): void
    {
        // Create second affiliate
        $affiliate2 = User::factory()->create([
            'affiliate_code' => 'TEST456',
            'status' => UserStatus::ACTIVE,
            'whatsapp' => '089876543210',
        ]);

        // Create lead for first affiliate
        Livewire::withCookie('affiliate_id', (string) $this->affiliate->id)
            ->test('contact-form', ['property' => $this->property])
            ->set('name', 'Affiliate 1 Lead')
            ->set('whatsapp', '081111111111')
            ->call('submit');

        // Create lead for second affiliate
        Livewire::withCookie('affiliate_id', (string) $affiliate2->id)
            ->test('contact-form', ['property' => $this->property])
            ->set('name', 'Affiliate 2 Lead')
            ->set('whatsapp', '082222222222')
            ->call('submit');

        // Assert each affiliate has their own lead
        $this->assertDatabaseHas('leads', [
            'name' => 'Affiliate 1 Lead',
            'affiliate_id' => $this->affiliate->id,
        ]);

        $this->assertDatabaseHas('leads', [
            'name' => 'Affiliate 2 Lead',
            'affiliate_id' => $affiliate2->id,
        ]);
    }
}
