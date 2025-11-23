<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use App\Enums\UserStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InputValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    /** @test */
    public function it_validates_lead_name_format()
    {
        $property = Property::factory()->create(['status' => 'published']);

        // Test with HTML tags (should be stripped)
        $response = $this->post(route('leads.store'), [
            'name' => '<script>alert("xss")</script>John Doe',
            'whatsapp' => '081234567890',
            'property_id' => $property->id,
        ]);

        // Should pass validation after sanitization
        $response->assertSessionHasNoErrors();
    }

    /** @test */
    public function it_rejects_invalid_name_characters()
    {
        $property = Property::factory()->create(['status' => 'published']);

        $response = $this->post(route('leads.store'), [
            'name' => 'John123!@#',
            'whatsapp' => '081234567890',
            'property_id' => $property->id,
        ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function it_validates_whatsapp_number_format()
    {
        $property = Property::factory()->create(['status' => 'published']);

        // Test with invalid format
        $response = $this->post(route('leads.store'), [
            'name' => 'John Doe',
            'whatsapp' => '123', // Too short
            'property_id' => $property->id,
        ]);

        $response->assertSessionHasErrors('whatsapp');

        // Test with non-numeric characters
        $response = $this->post(route('leads.store'), [
            'name' => 'John Doe',
            'whatsapp' => '081234567abc',
            'property_id' => $property->id,
        ]);

        $response->assertSessionHasErrors('whatsapp');
    }

    /** @test */
    public function it_sanitizes_whatsapp_number()
    {
        $property = Property::factory()->create(['status' => 'published']);

        // WhatsApp with special characters should be sanitized
        $response = $this->post(route('leads.store'), [
            'name' => 'John Doe',
            'whatsapp' => '+62-812-3456-7890',
            'property_id' => $property->id,
        ]);

        // After sanitization, it should become valid
        $response->assertSessionHasNoErrors();
    }

    /** @test */
    public function it_validates_property_title_length()
    {
        $admin = User::factory()->create(['status' => UserStatus::ACTIVE]);
        $admin->assignRole('super_admin');

        $this->actingAs($admin);

        $response = $this->post(route('properties.store'), [
            'title' => str_repeat('a', 256), // Exceeds max length
            'slug' => 'test-property',
            'price' => 1000000,
            'location' => 'Jakarta',
            'description' => 'Test description',
            'status' => 'draft',
        ]);

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function it_sanitizes_property_description_html()
    {
        $admin = User::factory()->create(['status' => UserStatus::ACTIVE]);
        $admin->assignRole('super_admin');

        $this->actingAs($admin);

        $maliciousHtml = '<p>Safe content</p><script>alert("xss")</script><p>More content</p>';

        $response = $this->post(route('properties.store'), [
            'title' => 'Test Property',
            'slug' => 'test-property',
            'price' => 1000000,
            'location' => 'Jakarta',
            'description' => $maliciousHtml,
            'status' => 'draft',
        ]);

        // Should not have script tags in saved description
        $property = Property::where('slug', 'test-property')->first();
        if ($property) {
            $this->assertStringNotContainsString('<script>', $property->description);
            $this->assertStringContainsString('<p>Safe content</p>', $property->description);
        }
    }

    /** @test */
    public function it_validates_image_file_type()
    {
        Storage::fake('public');

        $admin = User::factory()->create(['status' => UserStatus::ACTIVE]);
        $admin->assignRole('super_admin');

        $this->actingAs($admin);

        // Create a fake text file disguised as image
        $fakeImage = UploadedFile::fake()->create('document.txt', 100);

        $response = $this->post(route('properties.store'), [
            'title' => 'Test Property',
            'slug' => 'test-property',
            'price' => 1000000,
            'location' => 'Jakarta',
            'description' => 'Test description',
            'status' => 'draft',
            'images' => [$fakeImage],
        ]);

        $response->assertSessionHasErrors('images.0');
    }

    /** @test */
    public function it_validates_image_file_size()
    {
        Storage::fake('public');

        $admin = User::factory()->create(['status' => UserStatus::ACTIVE]);
        $admin->assignRole('super_admin');

        $this->actingAs($admin);

        // Create a file larger than 5MB
        $largeImage = UploadedFile::fake()->image('large.jpg')->size(6000);

        $response = $this->post(route('properties.store'), [
            'title' => 'Test Property',
            'slug' => 'test-property',
            'price' => 1000000,
            'location' => 'Jakarta',
            'description' => 'Test description',
            'status' => 'draft',
            'images' => [$largeImage],
        ]);

        $response->assertSessionHasErrors('images.0');
    }

    /** @test */
    public function it_validates_user_email_format()
    {
        $admin = User::factory()->create(['status' => UserStatus::ACTIVE]);
        $admin->assignRole('super_admin');

        $this->actingAs($admin);

        $response = $this->post(route('users.store'), [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'status' => 'active',
            'roles' => [1],
        ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function it_validates_password_strength()
    {
        $admin = User::factory()->create(['status' => UserStatus::ACTIVE]);
        $admin->assignRole('super_admin');

        $this->actingAs($admin);

        // Weak password
        $response = $this->post(route('users.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'weak',
            'password_confirmation' => 'weak',
            'status' => 'active',
            'roles' => [1],
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function it_sanitizes_user_name_input()
    {
        $admin = User::factory()->create(['status' => UserStatus::ACTIVE]);
        $admin->assignRole('super_admin');

        $this->actingAs($admin);

        $response = $this->post(route('users.store'), [
            'name' => '<b>John</b> Doe',
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'status' => 'active',
            'roles' => [1],
        ]);

        // Name should be sanitized
        $user = User::where('email', 'john@example.com')->first();
        if ($user) {
            $this->assertStringNotContainsString('<b>', $user->name);
            $this->assertEquals('John Doe', $user->name);
        }
    }

    /** @test */
    public function it_validates_api_login_credentials()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'invalid-email',
            'password' => 'short',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    /** @test */
    public function it_sanitizes_api_login_email()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('Password123!'),
            'status' => UserStatus::ACTIVE,
        ]);

        // Email with extra spaces should be sanitized
        $response = $this->postJson('/api/login', [
            'email' => '  TEST@EXAMPLE.COM  ',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['access_token', 'token_type', 'user']);
    }
}
