<?php

namespace Database\Seeders;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@pams.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'whatsapp' => '081234567890',
                'status' => UserStatus::ACTIVE,
            ]
        );

        // Assign super_admin role
        if (! $superAdmin->hasRole('super_admin')) {
            $superAdmin->assignRole('super_admin');
            $this->command->info('Super Admin role assigned to admin@pams.test');
        }

        // Create Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@pams.test'],
            [
                'name' => 'Admin Test',
                'password' => Hash::make('password'),
                'whatsapp' => '081234567891',
                'status' => UserStatus::ACTIVE,
            ]
        );

        // Assign admin role
        if (! $admin->hasRole('admin')) {
            $admin->assignRole('admin');
            $this->command->info('Admin role assigned to admin@pams.test');
        }

        $this->command->info('Test users created successfully!');
        $this->command->info('Super Admin: superadmin@pams.test / password');
        $this->command->info('Admin: admin@pams.test / password');
    }
}
