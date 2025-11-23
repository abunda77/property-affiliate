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

        $this->property = Property::factory()->create([
            'status' => PropertyStatus::PUBLISHED,
            'slug' => 'test-property',
        ]);

        $this->affiliate = User::factory()->create([
            'affiliate_code' => 'TEST123',
            'status' => UserStatus::ACTIVE,
            'whatsapp' => '081234567890',
        ]);
    }

    public function test_lead_created_from_contact_form_with_affiliate(): void
    {
        Livewire::withCookie('affiliate_id', (string) $this->affiliate->id)
            ->test('contact-form', ['property' => $this->property])
            ->set('name', 'John Doe')
            ->set('whatsapp', '081234567890')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('name', '')
            ->assertSet('whatsapp', '');

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
    }

    public function test_lead_created_event_dispatched_on_form_submission(): void
    {
        Event::fake();

        Livewire::withCookie('affiliate_id', (string) $this->affiliate->id)
            ->test('contact-form', ['property' => $this->property])
            ->set('name', 'Event Test')
            ->set('whatsapp', '081234567890')
            ->call('submit');

        Event::assertDispatched(LeadCreated::class, function ($event) {
            return $event->lead->name === 'Event Test' &&
                   $event->lead->whatsapp === '081234567890' &&
                   $event->lead->affiliate_id == $this->affiliate->id; // Use == for string comparison
        });
    }

    public function test_whatsapp_notification_sent_to_affiliate(): void
    {
        Http::fake([
            '*/send/message' => Http::response(['success' => true], 200),
        ]);

        $lead = Lead::create([
            'affiliate_id' => $this->affiliate->id,
            'property_id' => $this->property->id,
            'name' => 'WhatsApp Test',
            'whatsapp' => '081234567890',
            'status' => LeadStatus::NEW,
        ]);

        event(new LeadCreated($lead));
        $this->artisan('queue:work --once --stop-when-empty');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/send/message') &&
                   $request->hasHeader('Authorization');
        });
    }

    public function test_lead_assignment_to_affiliate_from_cookie(): void
    {
        Livewire::withCookie('affiliate_id', (string) $this->affiliate->id)
            ->test('contact-form', ['property' => $this->property])
            ->set('name', 'Cookie Test')
            ->set('whatsapp', '081234567890')
            ->call('submit');

        $lead = Lead::where('name', 'Cookie Test')->first();

        $this->assertNotNull($lead);
        $this->assertEquals($this->affiliate->id, $lead->affiliate_id);
        $this->assertEquals($this->property->id, $lead->property_id);
    }

    public function test_success_message_displayed_after_form_submission(): void
    {
        Livewire::test('contact-form', ['property' => $this->property])
            ->set('name', 'Success Test')
            ->set('whatsapp', '081234567890')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('name', '') // Check if form is reset after successful submission
            ->assertSet('whatsapp', ''); // Check if form is reset after successful submission
    }
}