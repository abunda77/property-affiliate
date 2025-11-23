<?php

namespace Tests\Unit;

use App\Enums\PropertyStatus;
use App\Models\Lead;
use App\Models\Property;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_correct_fillable_attributes()
    {
        $property = Property::factory()->create([
            'title' => 'Test Property',
            'slug' => 'test-property',
            'price' => 1000000000,
            'location' => 'Jakarta',
            'description' => 'Test description',
            'features' => ['Pool', 'Garden'],
            'specs' => ['Bedrooms' => 3],
            'status' => PropertyStatus::PUBLISHED,
        ]);

        $this->assertEquals('Test Property', $property->title);
        $this->assertEquals('test-property', $property->slug);
        $this->assertEquals(1000000000, $property->price);
        $this->assertEquals('Jakarta', $property->location);
        $this->assertEquals('Test description', $property->description);
        $this->assertEquals(['Pool', 'Garden'], $property->features);
        $this->assertEquals(['Bedrooms' => 3], $property->specs);
        $this->assertEquals(PropertyStatus::PUBLISHED, $property->status);
    }

    /** @test */
    public function it_casts_features_to_array()
    {
        $property = Property::factory()->create([
            'features' => ['Swimming Pool', 'Garden', 'Parking'],
        ]);

        $this->assertIsArray($property->features);
        $this->assertCount(3, $property->features);
        $this->assertContains('Swimming Pool', $property->features);
    }

    /** @test */
    public function it_casts_specs_to_json()
    {
        $specs = [
            'Bedrooms' => 4,
            'Bathrooms' => 2,
            'Land Size' => '200 m²',
        ];

        $property = Property::factory()->create(['specs' => $specs]);

        $this->assertIsArray($property->specs);
        $this->assertEquals(4, $property->specs['Bedrooms']);
        $this->assertEquals(2, $property->specs['Bathrooms']);
    }

    /** @test */
    public function it_casts_price_to_integer()
    {
        $property = Property::factory()->create(['price' => 1500000000]);

        $this->assertIsInt($property->price);
        $this->assertEquals(1500000000, $property->price);
    }

    /** @test */
    public function it_casts_status_to_enum()
    {
        $property = Property::factory()->create(['status' => PropertyStatus::PUBLISHED]);

        $this->assertInstanceOf(PropertyStatus::class, $property->status);
        $this->assertEquals(PropertyStatus::PUBLISHED, $property->status);
    }

    /** @test */
    public function it_has_leads_relationship()
    {
        $property = Property::factory()->create();
        $lead = Lead::factory()->create(['property_id' => $property->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $property->leads);
        $this->assertCount(1, $property->leads);
        $this->assertTrue($property->leads->contains($lead));
    }

    /** @test */
    public function it_has_visits_relationship()
    {
        $property = Property::factory()->create();
        $visit = Visit::factory()->create(['property_id' => $property->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $property->visits);
        $this->assertCount(1, $property->visits);
        $this->assertTrue($property->visits->contains($visit));
    }

    /** @test */
    public function scope_published_returns_only_published_properties()
    {
        Property::factory()->create(['status' => PropertyStatus::PUBLISHED]);
        Property::factory()->create(['status' => PropertyStatus::DRAFT]);
        Property::factory()->create(['status' => PropertyStatus::SOLD]);

        $published = Property::published()->get();

        $this->assertCount(1, $published);
        $this->assertEquals(PropertyStatus::PUBLISHED, $published->first()->status);
    }

    /** @test */
    public function scope_available_returns_only_published_properties()
    {
        Property::factory()->create(['status' => PropertyStatus::PUBLISHED]);
        Property::factory()->create(['status' => PropertyStatus::DRAFT]);
        Property::factory()->create(['status' => PropertyStatus::SOLD]);

        $available = Property::available()->get();

        $this->assertCount(1, $available);
        $this->assertEquals(PropertyStatus::PUBLISHED, $available->first()->status);
    }

    /** @test */
    public function it_formats_price_correctly()
    {
        $property = Property::factory()->create(['price' => 1500000000]);

        $formatted = $property->formatted_price;

        $this->assertEquals('Rp 1.500.000.000', $formatted);
    }

    /** @test */
    public function it_returns_searchable_array()
    {
        $property = Property::factory()->create([
            'title' => 'Luxury Villa',
            'location' => 'Bali',
            'description' => 'Beautiful villa with ocean view',
        ]);

        $searchable = $property->toSearchableArray();

        $this->assertArrayHasKey('id', $searchable);
        $this->assertArrayHasKey('title', $searchable);
        $this->assertArrayHasKey('location', $searchable);
        $this->assertArrayHasKey('description', $searchable);
        $this->assertEquals('Luxury Villa', $searchable['title']);
        $this->assertEquals('Bali', $searchable['location']);
    }
}
