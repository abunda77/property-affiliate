<?php

namespace Tests\Feature;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_affiliate_can_access_profile_settings_page(): void
    {
        $affiliate = User::factory()->create([
            'affiliate_code' => 'TEST1234',
            'status' => UserStatus::ACTIVE,
        ]);

        $this->actingAs($affiliate);

        $response = $this->get('/admin/profile-settings');

        $response->assertStatus(200);
    }

    public function test_affiliate_can_update_profile_information(): void
    {
        $affiliate = User::factory()->create([
            'name' => 'Old Name',
            'whatsapp' => '08123456789',
            'affiliate_code' => 'TEST1234',
            'status' => UserStatus::ACTIVE,
        ]);

        $this->actingAs($affiliate);

        $response = $this->post('/admin/profile-settings', [
            'name' => 'New Name',
            'whatsapp' => '08987654321',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $affiliate->id,
            'name' => 'New Name',
            'whatsapp' => '08987654321',
        ]);
    }

    public function test_affiliate_can_upload_profile_photo(): void
    {
        $affiliate = User::factory()->create([
            'affiliate_code' => 'TEST1234',
            'status' => UserStatus::ACTIVE,
        ]);

        $this->actingAs($affiliate);

        $file = UploadedFile::fake()->image('profile.jpg');

        $response = $this->post('/admin/profile-settings', [
            'name' => $affiliate->name,
            'whatsapp' => $affiliate->whatsapp,
            'profile_photo' => $file,
        ]);

        $affiliate->refresh();

        $this->assertNotNull($affiliate->profile_photo);
    }

    public function test_profile_settings_validates_required_fields(): void
    {
        $affiliate = User::factory()->create([
            'affiliate_code' => 'TEST1234',
            'status' => UserStatus::ACTIVE,
        ]);

        $this->actingAs($affiliate);

        $response = $this->post('/admin/profile-settings', [
            'name' => '',
            'whatsapp' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'whatsapp']);
    }

    public function test_profile_settings_validates_whatsapp_format(): void
    {
        $affiliate = User::factory()->create([
            'affiliate_code' => 'TEST1234',
            'status' => UserStatus::ACTIVE,
        ]);

        $this->actingAs($affiliate);

        $response = $this->post('/admin/profile-settings', [
            'name' => 'Test Name',
            'whatsapp' => 'invalid-phone',
        ]);

        $response->assertSessionHasErrors(['whatsapp']);
    }

    public function test_non_affiliate_cannot_see_profile_settings_in_navigation(): void
    {
        $user = User::factory()->create([
            'affiliate_code' => null,
            'status' => UserStatus::ACTIVE,
        ]);

        $this->actingAs($user);

        $shouldShow = \App\Filament\Pages\ProfileSettings::shouldRegisterNavigation();

        $this->assertFalse($shouldShow);
    }

    public function test_affiliate_can_see_profile_settings_in_navigation(): void
    {
        $affiliate = User::factory()->create([
            'affiliate_code' => 'TEST1234',
            'status' => UserStatus::ACTIVE,
        ]);

        $this->actingAs($affiliate);

        $shouldShow = \App\Filament\Pages\ProfileSettings::shouldRegisterNavigation();

        $this->assertTrue($shouldShow);
    }
}
