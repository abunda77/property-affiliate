<?php

namespace Tests\Feature;

use App\Enums\PropertyStatus;
use App\Enums\UserStatus;
use App\Models\Property;
use App\Models\User;
use App\Services\PromotionalPackageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PromotionalPackageTest extends TestCase
{
    use RefreshDatabase;

    protected User $affiliate;
    protected Property $property;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

        // Create affiliate
        $this->affiliate = User::factory()->create([
            'status' => UserStatus::ACTIVE,
            'affiliate_code' => 'TEST123',
        ]);
        $this->affiliate->assignRole('affiliate');

        // Create published property
        $this->property = Property::factory()->create([
            'status' => PropertyStatus::PUBLISHED,
            'title' => 'Test Property',
            'location' => 'Jakarta',
            'price' => 1000000000,
            'description' => 'This is a test property description',
            'features' => ['Swimming Pool', 'Garden', 'Garage'],
            'specs' => ['Bedrooms' => '3', 'Bathrooms' => '2', 'Land Size' => '200m2'],
        ]);
    }

    public function test_promotional_package_service_generates_zip(): void
    {
        $service = new PromotionalPackageService();
        
        $zipPath = $service->generatePackage($this->property, $this->affiliate);
        
        // Assert ZIP file was created
        $this->assertFileExists($zipPath);
        $this->assertStringEndsWith('.zip', $zipPath);
        
        // Clean up
        $service->cleanupZipFile($zipPath);
        $this->assertFileDoesNotExist($zipPath);
    }

    public function test_affiliate_can_download_promotional_package(): void
    {
        $this->actingAs($this->affiliate);

        $response = $this->get(route('affiliate.download-promo', ['property' => $this->property->id]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/zip');
    }

    public function test_non_affiliate_cannot_download_promotional_package(): void
    {
        $regularUser = User::factory()->create([
            'status' => UserStatus::ACTIVE,
        ]);

        $this->actingAs($regularUser);

        $response = $this->get(route('affiliate.download-promo', ['property' => $this->property->id]));

        $response->assertStatus(403);
    }
}
