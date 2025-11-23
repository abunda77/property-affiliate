<?php

namespace Tests\Unit;

use App\Enums\UserStatus;
use App\Models\Lead;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_correct_fillable_attributes()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'whatsapp' => '081234567890',
            'affiliate_code' => 'ABC12345',
            'status' => UserStatus::ACTIVE,
            'profile_photo' => 'photo.jpg',
        ]);

        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals('081234567890', $user->whatsapp);
        $this->assertEquals('ABC12345', $user->affiliate_code);
        $this->assertEquals(UserStatus::ACTIVE, $user->status);
        $this->assertEquals('photo.jpg', $user->profile_photo);
    }

    /** @test */
    public function it_casts_status_to_enum()
    {
        $user = User::factory()->create(['status' => UserStatus::ACTIVE]);

        $this->assertInstanceOf(UserStatus::class, $user->status);
        $this->assertEquals(UserStatus::ACTIVE, $user->status);
    }

    /** @test */
    public function it_hides_password_and_remember_token()
    {
        $user = User::factory()->create();
        $array = $user->toArray();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }

    /** @test */
    public function it_has_visits_relationship()
    {
        $user = User::factory()->create();
        $visit = Visit::factory()->create(['affiliate_id' => $user->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $user->visits);
        $this->assertCount(1, $user->visits);
        $this->assertTrue($user->visits->contains($visit));
    }

    /** @test */
    public function it_has_leads_relationship()
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create(['affiliate_id' => $user->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $user->leads);
        $this->assertCount(1, $user->leads);
        $this->assertTrue($user->leads->contains($lead));
    }

    /** @test */
    public function scope_affiliates_returns_only_users_with_affiliate_code()
    {
        User::factory()->create(['affiliate_code' => 'ABC12345']);
        User::factory()->create(['affiliate_code' => 'XYZ67890']);
        User::factory()->create(['affiliate_code' => null]);

        $affiliates = User::affiliates()->get();

        $this->assertCount(2, $affiliates);
        $this->assertTrue($affiliates->every(fn($user) => $user->affiliate_code !== null));
    }

    /** @test */
    public function scope_pending_returns_only_pending_users()
    {
        User::factory()->create(['status' => UserStatus::PENDING]);
        User::factory()->create(['status' => UserStatus::ACTIVE]);
        User::factory()->create(['status' => UserStatus::BLOCKED]);

        $pending = User::pending()->get();

        $this->assertCount(1, $pending);
        $this->assertEquals(UserStatus::PENDING, $pending->first()->status);
    }

    /** @test */
    public function scope_active_returns_only_active_users()
    {
        User::factory()->create(['status' => UserStatus::PENDING]);
        User::factory()->create(['status' => UserStatus::ACTIVE]);
        User::factory()->create(['status' => UserStatus::BLOCKED]);

        $active = User::active()->get();

        $this->assertCount(1, $active);
        $this->assertEquals(UserStatus::ACTIVE, $active->first()->status);
    }

    /** @test */
    public function it_generates_unique_affiliate_code()
    {
        $user = User::factory()->create(['affiliate_code' => null]);

        $code = $user->generateAffiliateCode();

        $this->assertNotNull($code);
        $this->assertEquals(8, strlen($code));
        $this->assertEquals(strtoupper($code), $code); // Should be uppercase
        $this->assertEquals($code, $user->affiliate_code);
    }

    /** @test */
    public function it_generates_different_affiliate_codes_for_different_users()
    {
        $user1 = User::factory()->create(['affiliate_code' => null]);
        $user2 = User::factory()->create(['affiliate_code' => null]);

        $code1 = $user1->generateAffiliateCode();
        $code2 = $user2->generateAffiliateCode();

        $this->assertNotEquals($code1, $code2);
    }

    /** @test */
    public function it_saves_affiliate_code_to_database()
    {
        $user = User::factory()->create(['affiliate_code' => null]);

        $code = $user->generateAffiliateCode();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'affiliate_code' => $code,
        ]);
    }

    /** @test */
    public function approve_method_activates_user()
    {
        $user = User::factory()->create(['status' => UserStatus::PENDING]);

        $user->approve();

        $this->assertEquals(UserStatus::ACTIVE, $user->status);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => UserStatus::ACTIVE->value,
        ]);
    }

    /** @test */
    public function approve_method_generates_affiliate_code_if_not_exists()
    {
        $user = User::factory()->create([
            'status' => UserStatus::PENDING,
            'affiliate_code' => null,
        ]);

        $user->approve();

        $this->assertNotNull($user->affiliate_code);
        $this->assertEquals(8, strlen($user->affiliate_code));
    }

    /** @test */
    public function approve_method_keeps_existing_affiliate_code()
    {
        $user = User::factory()->create([
            'status' => UserStatus::PENDING,
            'affiliate_code' => 'EXISTING1',
        ]);

        $user->approve();

        $this->assertEquals('EXISTING1', $user->affiliate_code);
    }

    /** @test */
    public function block_method_blocks_user()
    {
        $user = User::factory()->create(['status' => UserStatus::ACTIVE]);

        $user->block();

        $this->assertEquals(UserStatus::BLOCKED, $user->status);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => UserStatus::BLOCKED->value,
        ]);
    }
}
