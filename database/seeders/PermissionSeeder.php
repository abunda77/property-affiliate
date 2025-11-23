<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // User permissions
        $userPermissions = [
            'ViewAny:User',
            'View:User', 
            'Create:User',
            'Update:User',
            'Delete:User',
            'Restore:User',
            'ForceDelete:User',
            'ForceDeleteAny:User',
            'RestoreAny:User',
            'Replicate:User',
            'Reorder:User',
        ];

        // Property permissions
        $propertyPermissions = [
            'ViewAny:Property',
            'View:Property',
            'Create:Property', 
            'Update:Property',
            'Delete:Property',
            'Restore:Property',
            'ForceDelete:Property',
            'ForceDeleteAny:Property',
            'RestoreAny:Property',
            'Replicate:Property',
            'Reorder:Property',
        ];

        // Lead permissions
        $leadPermissions = [
            'ViewAny:Lead',
            'View:Lead',
            'Create:Lead',
            'Update:Lead', 
            'Delete:Lead',
            'Restore:Lead',
            'ForceDelete:Lead',
            'ForceDeleteAny:Lead',
            'RestoreAny:Lead',
            'Replicate:Lead',
            'Reorder:Lead',
        ];

        // Additional permissions for affiliates (lowercase format for compatibility)
        $affiliatePermissions = [
            'view_any_lead',
            'view_lead',
            'update_lead',
            'page_Dashboard',
            'widget_AccountWidget',
        ];

        // Create all permissions
        $allPermissions = array_merge(
            $userPermissions,
            $propertyPermissions, 
            $leadPermissions,
            $affiliatePermissions
        );

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $this->command->info('Permissions created successfully!');
        $this->command->info('Total permissions: ' . count($allPermissions));
    }
}