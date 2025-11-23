<?php

namespace Database\Seeders;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            ['email' => 'erieputranto@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'whatsapp' => '081234567890',
                'status' => UserStatus::ACTIVE,
            ]
        );

        // Assign super_admin role
        if (!$superAdmin->hasRole('super_admin')) {
            $superAdmin->assignRole('super_admin');
            $this->command->info('Super Admin role assigned to admin@pams.test');
        }

        // Create sample Affiliate user
        $affiliate = User::firstOrCreate(
            ['email' => 'affiliate@pams.test'],
            [
                'name' => 'Test Affiliate',
                'password' => Hash::make('password'),
                'whatsapp' => '081234567891',
                'status' => UserStatus::ACTIVE,
                'affiliate_code' => 'TEST1234',
            ]
        );

        // Assign affiliate role
        if (!$affiliate->hasRole('affiliate')) {
            $affiliate->assignRole('affiliate');
            $this->command->info('Affiliate role assigned to affiliate@pams.test');
        }

        $this->command->info('Test users created successfully!');
        $this->command->info('Super Admin: admin@pams.test / password');
        $this->command->info('Affiliate: affiliate@pams.test / password');
    }
}
