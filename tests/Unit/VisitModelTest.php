<?php

namespace Tests\Unit;

use App\Models\Property;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_correct_fillable_attributes()
    {
        $user = User::factory()->create();
        $property = Property::factory()->create();

        $visit = Visit::factory()->create([
            'affiliate_id' => $user->id,
            'property_id' => $property->id,
            'visitor_ip' => '192.168.1.1',
            'device' => 'mobile',
            'browser' => 'Chrome',
            'url' => 'https://example.com/property/test',
        ]);

        $this->assertEquals($user->id, $visit->affiliate_id);
        $this->assertEquals($property->id, $visit->property_id);
        $this->assertEquals('192.168.1.1', $visit->visitor_ip);
        $this->assertEquals('mobile', $visit->device);
        $this->assertEquals('Chrome', $visit->browser);
        $this->assertEquals('https://example.com/property/test', $visit->url);
    }

    /** @test */
    public function it_belongs_to_affiliate()
    {
        $user = User::factory()->create();
        $visit = Visit::factory()->create(['affiliate_id' => $user->id]);

        $this->assertInstanceOf(User::class, $visit->affiliate);
        $this->assertEquals($user->id, $visit->affiliate->id);
    }

    /** @test */
    public function it_belongs_to_property()
    {
        $property = Property::factory()->create();
        $visit = Visit::factory()->create(['property_id' => $property->id]);

        $this->assertInstanceOf(Property::class, $visit->property);
        $this->assertEquals($property->id, $visit->property->id);
    }

    /** @test */
    public function it_can_have_null_property()
    {
        $visit = Visit::factory()->create(['property_id' => null]);

        $this->assertNull($visit->property_id);
        $this->assertNull($visit->property);
    }

    /** @test */
    public function it_automatically_sets_created_at_on_creation()
    {
        $visit = Visit::create([
            'affiliate_id' => User::factory()->create()->id,
            'property_id' => Property::factory()->create()->id,
            'visitor_ip' => '192.168.1.1',
            'device' => 'desktop',
            'browser' => 'Firefox',
            'url' => 'https://example.com',
        ]);

        $this->assertNotNull($visit->created_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $visit->created_at);
    }

    /** @test */
    public function it_records_device_type()
    {
        $mobileVisit = Visit::factory()->create(['device' => 'mobile']);
        $desktopVisit = Visit::factory()->create(['device' => 'desktop']);

        $this->assertEquals('mobile', $mobileVisit->device);
        $this->assertEquals('desktop', $desktopVisit->device);
    }

    /** @test */
    public function it_records_browser_information()
    {
        $browsers = ['Chrome', 'Firefox', 'Safari', 'Edge'];

        foreach ($browsers as $browser) {
            $visit = Visit::factory()->create(['browser' => $browser]);
            $this->assertEquals($browser, $visit->browser);
        }
    }

    /** @test */
    public function it_records_visitor_ip_address()
    {
        $visit = Visit::factory()->create(['visitor_ip' => '203.0.113.42']);

        $this->assertEquals('203.0.113.42', $visit->visitor_ip);
    }

    /** @test */
    public function it_records_full_url()
    {
        $url = 'https://example.com/properties/luxury-villa?ref=ABC12345';
        $visit = Visit::factory()->create(['url' => $url]);

        $this->assertEquals($url, $visit->url);
    }

    /** @test */
    public function multiple_visits_can_be_created_for_same_affiliate()
    {
        $user = User::factory()->create();

        Visit::factory()->count(5)->create(['affiliate_id' => $user->id]);

        $this->assertCount(5, $user->visits);
    }

    /** @test */
    public function multiple_visits_can_be_created_for_same_property()
    {
        $property = Property::factory()->create();

        Visit::factory()->count(3)->create(['property_id' => $property->id]);

        $this->assertCount(3, $property->visits);
    }
}
