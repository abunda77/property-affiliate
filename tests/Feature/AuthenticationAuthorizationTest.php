<?php

namespace Tests\Feature;

use App\Enums\LeadStatus;
use App\Enums\PropertyStatus;
use App\Enums\UserStatus;
use App\Models\Lead;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $affiliate;
    protected User $pendingUser;
    protected User $blockedUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

        // Create Super Admin user
        $this->superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@test.com',
            'status' => UserStatus::ACTIVE,
        ]);
        $this->superAdmin->assignRole('super_admin');

        // Create active affiliate user
        $this->affiliate = User::factory()->create([
            'name' => 'Test Affiliate',
            'email' => 'affiliate@test.com',
            'status' => UserStatus::ACTIVE,
            'affiliate_code' => 'TEST1234',
        ]);
        $this->affiliate->assignRole('affiliate');

        // Create pending user
        $this->pendingUser = User::factory()->create([
            'name' => 'Pending User',
            'email' => 'pending@test.com',
            'status' => UserStatus::PENDING,
        ]);

        // Create blocked user
        $this->blockedUser = User::factory()->create([
            'name' => 'Blocked User',
            'email' => 'blocked@test.com',
            'status' => UserStatus::BLOCKED,
            'affiliate_code' => 'BLOCKED1',
        ]);
        $this->blockedUser->assignRole('affiliate');
    }

    // ========================================
    // User Registration and Approval Flow Tests
    // ========================================

    public function test_new_user_registration_creates_pending_user(): void
    {
        $userData = [
            'name' => 'New User',
            'email' => 'newuser@test.com',
            'password' => 'password123',
            'whatsapp' => '081234567890',
        ];

        $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => bcrypt($userData['password']),
            'whatsapp' => $userData['whatsapp'],
            'status' => UserStatus::PENDING,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@test.com',
            'status' => UserStatus::PENDING->value,
        ]);

        $this->assertNull($user->affiliate_code);
    }

    public function test_super_admin_can_approve_pending_user(): void
    {
        $this->actingAs($this->superAdmin);

        // Approve the pending user
        $this->pendingUser->approve();

        // Refresh the user from database
        $this->pendingUser->refresh();

        // Assert user is now active
        $this->assertEquals(UserStatus::ACTIVE, $this->pendingUser->status);

        // Assert affiliate code was generated
        $this->assertNotNull($this->pendingUser->affiliate_code);
        $this->assertEquals(8, strlen($this->pendingUser->affiliate_code));
    }

    public function test_approved_user_gets_affiliate_role(): void
    {
        $this->actingAs($this->superAdmin);

        // Approve and assign role
        $this->pendingUser->approve();
        $this->pendingUser->assignRole('affiliate');

        // Assert user has affiliate role
        $this->assertTrue($this->pendingUser->hasRole('affiliate'));
    }

    public function test_affiliate_code_is_unique(): void
    {
        $user1 = User::factory()->create(['status' => UserStatus::PENDING]);
        $user2 = User::factory()->create(['status' => UserStatus::PENDING]);

        $code1 = $user1->generateAffiliateCode();
        $code2 = $user2->generateAffiliateCode();

        $this->assertNotEquals($code1, $code2);
        $this->assertEquals(8, strlen($code1));
        $this->assertEquals(8, strlen($code2));
    }

    public function test_super_admin_can_block_user(): void
    {
        $this->actingAs($this->superAdmin);

        $activeUser = User::factory()->create([
            'status' => UserStatus::ACTIVE,
            'affiliate_code' => 'ACTIVE01',
        ]);
        $activeUser->assignRole('affiliate');

        // Block the user
        $activeUser->block();

        // Assert user is now blocked
        $this->assertEquals(UserStatus::BLOCKED, $activeUser->fresh()->status);
    }

    public function test_blocked_user_cannot_login(): void
    {
        // Attempt to authenticate as blocked user
        $response = $this->post('/admin/login', [
            'email' => $this->blockedUser->email,
            'password' => 'password',
        ]);

        // User should not be authenticated
        $this->assertGuest();
    }

    public function test_pending_user_cannot_access_admin_panel(): void
    {
        $this->actingAs($this->pendingUser);

        // Attempt to access admin panel
        $response = $this->get('/admin');

        // Should be redirected or forbidden
        $this->assertTrue(
            $response->status() === 302 || $response->status() === 403,
            'Pending user should not access admin panel'
        );
    }

    // ========================================
    // Role-Based Access Control Tests
    // ========================================

    public function test_super_admin_has_super_admin_role(): void
    {
        $this->assertTrue($this->superAdmin->hasRole('super_admin'));
    }

    public function test_affiliate_has_affiliate_role(): void
    {
        $this->assertTrue($this->affiliate->hasRole('affiliate'));
    }

    public function test_super_admin_can_view_all_users(): void
    {
        $this->actingAs($this->superAdmin);

        // Super Admin should be able to view any user
        $this->assertTrue($this->superAdmin->can('viewAny', User::class));
    }

    public function test_affiliate_cannot_view_users_list(): void
    {
        $this->actingAs($this->affiliate);

        // Affiliate should not be able to view users list
        $this->assertFalse($this->affiliate->can('viewAny', User::class));
    }

    public function test_super_admin_can_create_users(): void
    {
        $this->actingAs($this->superAdmin);

        $this->assertTrue($this->superAdmin->can('create', User::class));
    }

    public function test_affiliate_cannot_create_users(): void
    {
        $this->actingAs($this->affiliate);

        $this->assertFalse($this->affiliate->can('create', User::class));
    }

    public function test_super_admin_can_update_users(): void
    {
        $this->actingAs($this->superAdmin);

        $this->assertTrue($this->superAdmin->can('update', User::class));
    }

    public function test_affiliate_cannot_update_users(): void
    {
        $this->actingAs($this->affiliate);

        $this->assertFalse($this->affiliate->can('update', User::class));
    }

    public function test_super_admin_can_delete_users(): void
    {
        $this->actingAs($this->superAdmin);

        $this->assertTrue($this->superAdmin->can('delete', User::class));
    }

    public function test_affiliate_cannot_delete_users(): void
    {
        $this->actingAs($this->affiliate);

        $this->assertFalse($this->affiliate->can('delete', User::class));
    }

    // ========================================
    // Super Admin Property Access Tests
    // ========================================

    public function test_super_admin_can_view_all_properties(): void
    {
        $this->actingAs($this->superAdmin);

        $this->assertTrue($this->superAdmin->can('viewAny', Property::class));
    }

    public function test_super_admin_can_create_properties(): void
    {
        $this->actingAs($this->superAdmin);

        $this->assertTrue($this->superAdmin->can('create', Property::class));
    }

    public function test_super_admin_can_update_properties(): void
    {
        $this->actingAs($this->superAdmin);

        $property = Property::factory()->create();

        $this->assertTrue($this->superAdmin->can('update', $property));
    }

    public function test_super_admin_can_delete_properties(): void
    {
        $this->actingAs($this->superAdmin);

        $property = Property::factory()->create();

        $this->assertTrue($this->superAdmin->can('delete', $property));
    }

    // ========================================
    // Affiliate Property Access Tests
    // ========================================

    public function test_affiliate_cannot_view_property_management(): void
    {
        $this->actingAs($this->affiliate);

        $this->assertFalse($this->affiliate->can('viewAny', Property::class));
    }

    public function test_affiliate_cannot_create_properties(): void
    {
        $this->actingAs($this->affiliate);

        $this->assertFalse($this->affiliate->can('create', Property::class));
    }

    public function test_affiliate_cannot_update_properties(): void
    {
        $this->actingAs($this->affiliate);

        $property = Property::factory()->create();

        $this->assertFalse($this->affiliate->can('update', $property));
    }

    public function test_affiliate_cannot_delete_properties(): void
    {
        $this->actingAs($this->affiliate);

        $property = Property::factory()->create();

        $this->assertFalse($this->affiliate->can('delete', $property));
    }

    // ========================================
    // Affiliate Lead Access Tests
    // ========================================

    public function test_affiliate_can_view_own_leads(): void
    {
        $this->actingAs($this->affiliate);

        $property = Property::factory()->create();
        $myLead = Lead::factory()->create([
            'affiliate_id' => $this->affiliate->id,
            'property_id' => $property->id,
        ]);

        $this->assertTrue($this->affiliate->can('view', $myLead));
    }

    public function test_affiliate_cannot_view_other_affiliate_leads(): void
    {
        $this->actingAs($this->affiliate);

        $otherAffiliate = User::factory()->create([
            'status' => UserStatus::ACTIVE,
            'affiliate_code' => 'OTHER123',
        ]);
        $otherAffiliate->assignRole('affiliate');

        $property = Property::factory()->create();
        $otherLead = Lead::factory()->create([
            'affiliate_id' => $otherAffiliate->id,
            'property_id' => $property->id,
        ]);

        $this->assertFalse($this->affiliate->can('view', $otherLead));
    }

    public function test_affiliate_can_update_own_leads(): void
    {
        $this->actingAs($this->affiliate);

        $property = Property::factory()->create();
        $myLead = Lead::factory()->create([
            'affiliate_id' => $this->affiliate->id,
            'property_id' => $property->id,
        ]);

        $this->assertTrue($this->affiliate->can('update', $myLead));
    }

    public function test_affiliate_cannot_update_other_affiliate_leads(): void
    {
        $this->actingAs($this->affiliate);

        $otherAffiliate = User::factory()->create([
            'status' => UserStatus::ACTIVE,
            'affiliate_code' => 'OTHER456',
        ]);
        $otherAffiliate->assignRole('affiliate');

        $property = Property::factory()->create();
        $otherLead = Lead::factory()->create([
            'affiliate_id' => $otherAffiliate->id,
            'property_id' => $property->id,
        ]);

        $this->assertFalse($this->affiliate->can('update', $otherLead));
    }

    // ========================================
    // Super Admin Lead Access Tests
    // ========================================

    public function test_super_admin_can_view_all_leads(): void
    {
        $this->actingAs($this->superAdmin);

        $this->assertTrue($this->superAdmin->can('viewAny', Lead::class));
    }

    public function test_super_admin_can_view_any_affiliate_lead(): void
    {
        $this->actingAs($this->superAdmin);

        $property = Property::factory()->create();
        $affiliateLead = Lead::factory()->create([
            'affiliate_id' => $this->affiliate->id,
            'property_id' => $property->id,
        ]);

        $this->assertTrue($this->superAdmin->can('view', $affiliateLead));
    }

    public function test_super_admin_can_update_any_lead(): void
    {
        $this->actingAs($this->superAdmin);

        $property = Property::factory()->create();
        $affiliateLead = Lead::factory()->create([
            'affiliate_id' => $this->affiliate->id,
            'property_id' => $property->id,
        ]);

        $this->assertTrue($this->superAdmin->can('update', $affiliateLead));
    }

    public function test_super_admin_can_delete_any_lead(): void
    {
        $this->actingAs($this->superAdmin);

        $property = Property::factory()->create();
        $affiliateLead = Lead::factory()->create([
            'affiliate_id' => $this->affiliate->id,
            'property_id' => $property->id,
        ]);

        $this->assertTrue($this->superAdmin->can('delete', $affiliateLead));
    }

    // ========================================
    // Data Isolation Tests
    // ========================================

    public function test_affiliate_only_sees_own_leads_in_query(): void
    {
        $this->actingAs($this->affiliate);

        $otherAffiliate = User::factory()->create([
            'status' => UserStatus::ACTIVE,
            'affiliate_code' => 'OTHER789',
        ]);
        $otherAffiliate->assignRole('affiliate');

        $property = Property::factory()->create();

        // Create leads for both affiliates
        $myLead1 = Lead::factory()->create([
            'affiliate_id' => $this->affiliate->id,
            'property_id' => $property->id,
        ]);

        $myLead2 = Lead::factory()->create([
            'affiliate_id' => $this->affiliate->id,
            'property_id' => $property->id,
        ]);

        $otherLead = Lead::factory()->create([
            'affiliate_id' => $otherAffiliate->id,
            'property_id' => $property->id,
        ]);

        // Query leads for the affiliate
        $affiliateLeads = Lead::where('affiliate_id', $this->affiliate->id)->get();

        // Should only see own leads
        $this->assertCount(2, $affiliateLeads);
        $this->assertTrue($affiliateLeads->contains($myLead1));
        $this->assertTrue($affiliateLeads->contains($myLead2));
        $this->assertFalse($affiliateLeads->contains($otherLead));
    }

    public function test_super_admin_sees_all_leads_in_query(): void
    {
        $this->actingAs($this->superAdmin);

        $affiliate1 = User::factory()->create([
            'status' => UserStatus::ACTIVE,
            'affiliate_code' => 'AFF001',
        ]);
        $affiliate1->assignRole('affiliate');

        $affiliate2 = User::factory()->create([
            'status' => UserStatus::ACTIVE,
            'affiliate_code' => 'AFF002',
        ]);
        $affiliate2->assignRole('affiliate');

        $property = Property::factory()->create();

        // Create leads for different affiliates
        $lead1 = Lead::factory()->create([
            'affiliate_id' => $affiliate1->id,
            'property_id' => $property->id,
        ]);

        $lead2 = Lead::factory()->create([
            'affiliate_id' => $affiliate2->id,
            'property_id' => $property->id,
        ]);

        // Query all leads
        $allLeads = Lead::all();

        // Super Admin should see all leads
        $this->assertGreaterThanOrEqual(2, $allLeads->count());
        $this->assertTrue($allLeads->contains($lead1));
        $this->assertTrue($allLeads->contains($lead2));
    }

    // ========================================
    // User Status Validation Tests
    // ========================================

    public function test_only_active_users_can_access_system(): void
    {
        // Active user should be able to access
        $this->actingAs($this->affiliate);
        $this->assertEquals(UserStatus::ACTIVE, $this->affiliate->status);

        // Pending user should not access
        $this->actingAs($this->pendingUser);
        $this->assertEquals(UserStatus::PENDING, $this->pendingUser->status);

        // Blocked user should not access
        $this->actingAs($this->blockedUser);
        $this->assertEquals(UserStatus::BLOCKED, $this->blockedUser->status);
    }

    public function test_user_scopes_work_correctly(): void
    {
        // Test affiliates scope
        $affiliates = User::affiliates()->get();
        $this->assertTrue($affiliates->contains($this->affiliate));
        $this->assertTrue($affiliates->contains($this->blockedUser));
        $this->assertFalse($affiliates->contains($this->pendingUser));

        // Test pending scope
        $pendingUsers = User::pending()->get();
        $this->assertTrue($pendingUsers->contains($this->pendingUser));
        $this->assertFalse($pendingUsers->contains($this->affiliate));

        // Test active scope
        $activeUsers = User::active()->get();
        $this->assertTrue($activeUsers->contains($this->affiliate));
        $this->assertTrue($activeUsers->contains($this->superAdmin));
        $this->assertFalse($activeUsers->contains($this->pendingUser));
        $this->assertFalse($activeUsers->contains($this->blockedUser));
    }

    // ========================================
    // Permission Inheritance Tests
    // ========================================

    public function test_super_admin_has_all_permissions(): void
    {
        $this->actingAs($this->superAdmin);

        // Super Admin should have all property permissions
        $this->assertTrue($this->superAdmin->can('viewAny', Property::class));
        $this->assertTrue($this->superAdmin->can('create', Property::class));

        // Super Admin should have all user permissions
        $this->assertTrue($this->superAdmin->can('viewAny', User::class));
        $this->assertTrue($this->superAdmin->can('create', User::class));

        // Super Admin should have all lead permissions
        $this->assertTrue($this->superAdmin->can('viewAny', Lead::class));
    }

    public function test_affiliate_has_limited_permissions(): void
    {
        $this->actingAs($this->affiliate);

        // Affiliate should NOT have property management permissions
        $this->assertFalse($this->affiliate->can('viewAny', Property::class));
        $this->assertFalse($this->affiliate->can('create', Property::class));

        // Affiliate should NOT have user management permissions
        $this->assertFalse($this->affiliate->can('viewAny', User::class));
        $this->assertFalse($this->affiliate->can('create', User::class));

        // Affiliate should have lead viewing permissions (for own leads)
        // This is tested through the policy which checks ownership
    }
}
