<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Verify permissions exist
        $permissionCount = Permission::count();
        if ($permissionCount === 0) {
            $this->command->error('No permissions found! Run: php artisan shield:generate --all');
            return;
        }

        $this->command->info("Found {$permissionCount} permissions");

        // Create Super Admin role with all permissions
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super_admin'],
            ['guard_name' => 'web']
        );

        // Create Admin role
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['guard_name' => 'web']
        );

        // Create Affiliate role
        $affiliateRole = Role::firstOrCreate(
            ['name' => 'affiliate'],
            ['guard_name' => 'web']
        );

        // Super Admin gets all permissions
        $allPermissions = Permission::all();
        $superAdminRole->syncPermissions($allPermissions);
        $this->command->info("Super Admin: {$allPermissions->count()} permissions granted");

        // Admin gets most permissions except user management and critical settings
        $adminPermissions = Permission::all()->filter(function ($permission) {
            // Exclude user management and shield permissions
            return ! str_contains($permission->name, 'user') &&
                   ! str_contains($permission->name, 'shield') &&
                   ! str_contains($permission->name, 'role');
        });
        $adminRole->syncPermissions($adminPermissions);
        $this->command->info("Admin: {$adminPermissions->count()} permissions granted");

        // Affiliate gets limited permissions for dashboard and leads access
        $affiliatePermissionNames = [
            // Lead management permissions
            'view_any_lead',
            'view_lead',
            'update_lead',

            // Dashboard access (page permissions)
            'page_Dashboard',

            // Widget permissions (if any exist)
            'widget_AccountWidget',
            'widget_AffiliateStatsOverviewWidget',
            'widget_AffiliatePerformanceChartWidget',
        ];

        $affiliatePermissions = Permission::whereIn('name', $affiliatePermissionNames)->get();
        $affiliateRole->syncPermissions($affiliatePermissions);
        $this->command->info("Affiliate: {$affiliatePermissions->count()} permissions granted");

        $this->command->info('✓ Roles and permissions configured successfully!');
    }
}
