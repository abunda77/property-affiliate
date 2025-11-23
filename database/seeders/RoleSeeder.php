<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Super Admin role with all permissions
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super_admin'],
            ['guard_name' => 'web']
        );

        // Create Affiliate role
        $affiliateRole = Role::firstOrCreate(
            ['name' => 'affiliate'],
            ['guard_name' => 'web']
        );

        // Super Admin gets all permissions
        $superAdminRole->givePermissionTo(Permission::all());

        // Affiliate gets limited permissions for dashboard and leads access
        $affiliatePermissions = [
            // Lead management permissions
            'view_any_lead',
            'view_lead',
            'update_lead',
            
            // Dashboard access (page permissions)
            'page_Dashboard',
            
            // Widget permissions (if any exist)
            'widget_AccountWidget',
        ];

        foreach ($affiliatePermissions as $permission) {
            if (Permission::where('name', $permission)->exists()) {
                $affiliateRole->givePermissionTo($permission);
            }
        }

        $this->command->info('Roles and permissions configured successfully!');
        $this->command->info('Super Admin role: All permissions granted');
        $this->command->info('Affiliate role: Dashboard and lead management permissions granted');
    }
}
