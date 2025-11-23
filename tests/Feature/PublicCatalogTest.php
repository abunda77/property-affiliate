<?php

namespace Tests\Feature;

use App\Enums\PropertyStatus;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PublicCatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test properties with various attributes
        Property::factory()->create([
            'title' => 'Rumah Mewah Jakarta',
            'slug' => 'rumah-mewah-jakarta',
            'location' => 'Jakarta Selatan',
            'description' => 'Rumah mewah dengan fasilitas lengkap di area premium',
            'status' => PropertyStatus::PUBLISHED,
            'price' => 5000000000,
            'features' => ['Swimming Pool', 'Garden', 'Garage'],
            'specs' => [
                'Luas Tanah' => '500 m²',
                'Luas Bangunan' => '350 m²',
                'Kamar Tidur' => '5',
                'Kamar Mandi' => '4',
            ],
        ]);

        Property::factory()->create([
            'title' => 'Villa Modern Bali',
            'slug' => 'villa-modern-bali',
            'location' => 'Bali',
            'description' => 'Villa modern dengan pemandangan laut yang indah',
            'status' => PropertyStatus::PUBLISHED,
            'price' => 3000000000,
            'features' => ['Ocean View', 'Private Pool', 'Garden'],
            'specs' => [
                'Luas Tanah' => '300 m²',
                'Luas Bangunan' => '200 m²',
                'Kamar Tidur' => '3',
                'Kamar Mandi' => '3',
            ],
        ]);

        Property::factory()->create([
            'title' => 'Apartemen Strategis',
            'slug' => 'apartemen-strategis',
            'location' => 'Jakarta Pusat',
            'description' => 'Apartemen di lokasi strategis dekat MRT',
            'status' => PropertyStatus::PUBLISHED,
            'price' => 1500000000,
            'features' => ['Near MRT', 'Security 24/7'],
            'specs' => [
                'Luas Bangunan' => '80 m²',
                'Kamar Tidur' => '2',
                'Kamar Mandi' => '1',
            ],
        ]);

        // Draft property - should not appear
        Property::factory()->create([
            'title' => 'Rumah Draft',
            'slug' => 'rumah-draft',
            'status' => PropertyStatus::DRAFT,
            'price' => 2000000000,
        ]);

        // Import properties to Scout index
        Property::published()->searchable();
    }

    /** @test */
    public function it_displays_property_catalog_page()
    {
        $response = $this->get('/properties');

        $response->assertStatus(200);
        $response->assertSeeLivewire(\App\Livewire\PropertyCatalog::class);
        $response->assertSee('Rumah Mewah Jakarta');
        $response->assertSee('Villa Modern Bali');
        $response->assertSee('Apartemen Strategis');
        $response->assertDontSee('Rumah Draft');
    }

    /** @test */
    public function it_can_search_properties()
    {
        Livewire::test(\App\Livewire\PropertyCatalog::class)
            ->set('search', 'Villa')
            ->assertSee('Villa Modern Bali')
            ->assertDontSee('Rumah Mewah Jakarta')
            ->assertDontSee('Apartemen Strategis');
    }

    /** @test */
    public function it_can_filter_properties_by_location()
    {
        Livewire::test(\App\Livewire\PropertyCatalog::class)
            ->set('location', 'Jakarta')
            ->assertSee('Rumah Mewah Jakarta')
            ->assertSee('Apartemen Strategis')
            ->assertDontSee('Villa Modern Bali');
    }

    /** @test */
    public function it_can_filter_properties_by_price_range()
    {
        Livewire::test(\App\Livewire\PropertyCatalog::class)
            ->set('minPrice', 2000000000)
            ->set('maxPrice', 4000000000)
            ->assertSee('Villa Modern Bali')
            ->assertDontSee('Rumah Mewah Jakarta')
            ->assertDontSee('Apartemen Strategis');
    }

    /** @test */
    public function it_can_filter_by_minimum_price_only()
    {
        Livewire::test(\App\Livewire\PropertyCatalog::class)
            ->set('minPrice', 3000000000)
            ->assertSee('Rumah Mewah Jakarta')
            ->assertSee('Villa Modern Bali')
            ->assertDontSee('Apartemen Strategis');
    }

    /** @test */
    public function it_can_filter_by_maximum_price_only()
    {
        Livewire::test(\App\Livewire\PropertyCatalog::class)
            ->set('maxPrice', 2000000000)
            ->assertSee('Apartemen Strategis')
            ->assertDontSee('Rumah Mewah Jakarta')
            ->assertDontSee('Villa Modern Bali');
    }

    /** @test */
    public function it_can_sort_properties_by_newest()
    {
        Livewire::test(\App\Livewire\PropertyCatalog::class)
            ->set('sortBy', 'newest')
            ->assertSeeInOrder(['Apartemen Strategis', 'Villa Modern Bali', 'Rumah Mewah Jakarta']);
    }

    /** @test */
    public function it_can_sort_properties_by_lowest_price()
    {
        Livewire::test(\App\Livewire\PropertyCatalog::class)
            ->set('sortBy', 'price_low')
            ->assertSeeInOrder(['Apartemen Strategis', 'Villa Modern Bali', 'Rumah Mewah Jakarta']);
    }

    /** @test */
    public function it_can_sort_properties_by_highest_price()
    {
        Livewire::test(\App\Livewire\PropertyCatalog::class)
            ->set('sortBy', 'price_high')
            ->assertSeeInOrder(['Rumah Mewah Jakarta', 'Villa Modern Bali', 'Apartemen Strategis']);
    }

    /** @test */
    public function it_can_combine_search_and_filters()
    {
        Livewire::test(\App\Livewire\PropertyCatalog::class)
            ->set('search', 'Jakarta')
            ->set('maxPrice', 2000000000)
            ->assertSee('Apartemen Strategis')
            ->assertDontSee('Rumah Mewah Jakarta')
            ->assertDontSee('Villa Modern Bali');
    }

    /** @test */
    public function it_paginates_property_results()
    {
        // Create more properties to trigger pagination
        Property::factory()->count(15)->create([
            'status' => PropertyStatus::PUBLISHED,
        ]);

        $response = $this->get('/properties');
        
        $response->assertStatus(200);
        // Default pagination is 12 items per page
        $response->assertSee('pagination');
    }

    /** @test */
    public function it_displays_property_detail_page()
    {
        $property = Property::where('slug', 'rumah-mewah-jakarta')->first();

        $response = $this->get("/p/{$property->slug}");

        $response->assertStatus(200);
        $response->assertSeeLivewire(\App\Livewire\PropertyDetail::class);
        $response->assertSee('Rumah Mewah Jakarta');
        $response->assertSee('Jakarta Selatan');
        $response->assertSee('Rumah mewah dengan fasilitas lengkap');
        $response->assertSee('Rp 5.000.000.000');
    }

    /** @test */
    public function it_displays_property_features_on_detail_page()
    {
        $property = Property::where('slug', 'rumah-mewah-jakarta')->first();

        $response = $this->get("/p/{$property->slug}");

        $response->assertSee('Swimming Pool');
        $response->assertSee('Garden');
        $response->assertSee('Garage');
    }

    /** @test */
    public function it_displays_property_specifications_on_detail_page()
    {
        $property = Property::where('slug', 'rumah-mewah-jakarta')->first();

        $response = $this->get("/p/{$property->slug}");

        $response->assertSee('Luas Tanah');
        $response->assertSee('500 m²');
        $response->assertSee('Luas Bangunan');
        $response->assertSee('350 m²');
        $response->assertSee('Kamar Tidur');
        $response->assertSee('5');
        $response->assertSee('Kamar Mandi');
        $response->assertSee('4');
    }

    /** @test */
    public function it_returns_404_for_non_existent_property()
    {
        $response = $this->get('/p/non-existent-slug');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_404_for_draft_property()
    {
        $response = $this->get('/p/rumah-draft');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_displays_contact_form_on_property_detail_page()
    {
        $property = Property::where('slug', 'rumah-mewah-jakarta')->first();

        $response = $this->get("/p/{$property->slug}");

        $response->assertSeeLivewire(\App\Livewire\ContactForm::class);
        $response->assertSee('Hubungi Saya');
    }

    /** @test */
    public function it_can_submit_contact_form()
    {
        $property = Property::where('slug', 'rumah-mewah-jakarta')->first();

        Livewire::test(\App\Livewire\ContactForm::class, ['property' => $property])
            ->set('name', 'John Doe')
            ->set('whatsapp', '081234567890')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSee('Terima kasih');

        $this->assertDatabaseHas('leads', [
            'property_id' => $property->id,
            'name' => 'John Doe',
            'whatsapp' => '081234567890',
        ]);
    }

    /** @test */
    public function it_validates_contact_form_required_fields()
    {
        $property = Property::where('slug', 'rumah-mewah-jakarta')->first();

        Livewire::test(\App\Livewire\ContactForm::class, ['property' => $property])
            ->set('name', '')
            ->set('whatsapp', '')
            ->call('submit')
            ->assertHasErrors(['name', 'whatsapp']);
    }

    /** @test */
    public function it_validates_whatsapp_number_format()
    {
        $property = Property::where('slug', 'rumah-mewah-jakarta')->first();

        Livewire::test(\App\Livewire\ContactForm::class, ['property' => $property])
            ->set('name', 'John Doe')
            ->set('whatsapp', 'invalid-phone')
            ->call('submit')
            ->assertHasErrors(['whatsapp']);
    }

    /** @test */
    public function it_associates_lead_with_affiliate_from_cookie()
    {
        $affiliate = User::factory()->create([
            'affiliate_code' => 'TEST123',
        ]);

        $property = Property::where('slug', 'rumah-mewah-jakarta')->first();

        // Set affiliate cookie
        $this->withCookie('affiliate_id', $affiliate->id);

        Livewire::test(\App\Livewire\ContactForm::class, ['property' => $property])
            ->set('name', 'John Doe')
            ->set('whatsapp', '081234567890')
            ->call('submit');

        $this->assertDatabaseHas('leads', [
            'property_id' => $property->id,
            'affiliate_id' => $affiliate->id,
            'name' => 'John Doe',
        ]);
    }

    /** @test */
    public function it_creates_lead_without_affiliate_when_no_cookie()
    {
        $property = Property::where('slug', 'rumah-mewah-jakarta')->first();

        Livewire::test(\App\Livewire\ContactForm::class, ['property' => $property])
            ->set('name', 'John Doe')
            ->set('whatsapp', '081234567890')
            ->call('submit');

        $this->assertDatabaseHas('leads', [
            'property_id' => $property->id,
            'affiliate_id' => null,
            'name' => 'John Doe',
        ]);
    }

    /** @test */
    public function it_renders_responsive_layout_elements()
    {
        $response = $this->get('/properties');

        // Check for responsive utility classes
        $response->assertSee('grid');
        $response->assertSee('md:');
        $response->assertSee('lg:');
    }

    /** @test */
    public function property_cards_contain_essential_information()
    {
        $response = $this->get('/properties');

        $response->assertSee('Rumah Mewah Jakarta');
        $response->assertSee('Jakarta Selatan');
        $response->assertSee('Rp 5.000.000.000');
    }

    /** @test */
    public function it_displays_formatted_price()
    {
        $property = Property::where('slug', 'rumah-mewah-jakarta')->first();

        $response = $this->get("/p/{$property->slug}");

        // Check for formatted price with thousand separators
        $response->assertSee('Rp 5.000.000.000');
    }

    /** @test */
    public function it_shows_empty_state_when_no_properties_match_filters()
    {
        Livewire::test(\App\Livewire\PropertyCatalog::class)
            ->set('search', 'nonexistent property xyz')
            ->assertSee('Tidak ada properti ditemukan');
    }

    /** @test */
    public function it_clears_filters_when_reset_button_clicked()
    {
        Livewire::test(\App\Livewire\PropertyCatalog::class)
            ->set('search', 'Villa')
            ->set('location', 'Bali')
            ->set('minPrice', 1000000000)
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('location', '')
            ->assertSet('minPrice', null);
    }

    /** @test */
    public function property_detail_page_has_proper_meta_tags()
    {
        $property = Property::where('slug', 'rumah-mewah-jakarta')->first();

        $response = $this->get("/p/{$property->slug}");

        // Check for SEO meta tags
        $response->assertSee('Rumah Mewah Jakarta', false);
        $response->assertSee('Jakarta Selatan', false);
    }

    /** @test */
    public function it_displays_property_images_section()
    {
        $property = Property::where('slug', 'rumah-mewah-jakarta')->first();

        $response = $this->get("/p/{$property->slug}");

        // Check for image gallery container
        $response->assertSee('gallery');
    }

    /** @test */
    public function catalog_page_has_search_input()
    {
        $response = $this->get('/properties');

        $response->assertSee('search');
        $response->assertSee('Cari properti');
    }

    /** @test */
    public function catalog_page_has_filter_controls()
    {
        $response = $this->get('/properties');

        $response->assertSee('filter');
        $response->assertSee('Lokasi');
        $response->assertSee('Harga');
    }

    /** @test */
    public function catalog_page_has_sort_dropdown()
    {
        $response = $this->get('/properties');

        $response->assertSee('Urutkan');
    }

    /** @test */
    public function it_tracks_property_views_with_affiliate_cookie()
    {
        $affiliate = User::factory()->create([
            'affiliate_code' => 'TEST123',
        ]);

        $property = Property::where('slug', 'rumah-mewah-jakarta')->first();

        // Set affiliate cookie and visit property
        $response = $this->withCookie('affiliate_id', $affiliate->id)
            ->get("/p/{$property->slug}");

        $response->assertStatus(200);
        
        // Visit should be tracked by middleware
        $this->assertDatabaseHas('visits', [
            'affiliate_id' => $affiliate->id,
            'property_id' => $property->id,
        ]);
    }
}
