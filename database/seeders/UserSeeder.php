<?php

namespace Database\Seeders;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@pams.test'],
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
        }

        // Create 5 affiliate users with unique codes
        $affiliates = [
            [
                'name' => 'Ahmad Rizki',
                'email' => 'ahmad.rizki@pams.test',
                'whatsapp' => '081234567891',
                'affiliate_code' => 'AHMAD001',
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@pams.test',
                'whatsapp' => '081234567892',
                'affiliate_code' => 'SITI0002',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@pams.test',
                'whatsapp' => '081234567893',
                'affiliate_code' => 'BUDI0003',
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi.lestari@pams.test',
                'whatsapp' => '081234567894',
                'affiliate_code' => 'DEWI0004',
            ],
            [
                'name' => 'Eko Prasetyo',
                'email' => 'eko.prasetyo@pams.test',
                'whatsapp' => '081234567895',
                'affiliate_code' => 'EKO00005',
            ],
        ];

        foreach ($affiliates as $affiliateData) {
            $affiliate = User::firstOrCreate(
                ['email' => $affiliateData['email']],
                [
                    'name' => $affiliateData['name'],
                    'password' => Hash::make('password'),
                    'whatsapp' => $affiliateData['whatsapp'],
                    'affiliate_code' => $affiliateData['affiliate_code'],
                    'status' => UserStatus::ACTIVE,
                ]
            );

            // Assign affiliate role
            if (!$affiliate->hasRole('affiliate')) {
                $affiliate->assignRole('affiliate');
            }
        }

        // Create 3 pending users for approval testing
        $pendingUsers = [
            [
                'name' => 'Fajar Nugroho',
                'email' => 'fajar.nugroho@pams.test',
                'whatsapp' => '081234567896',
            ],
            [
                'name' => 'Gita Savitri',
                'email' => 'gita.savitri@pams.test',
                'whatsapp' => '081234567897',
            ],
            [
                'name' => 'Hendra Wijaya',
                'email' => 'hendra.wijaya@pams.test',
                'whatsapp' => '081234567898',
            ],
        ];

        foreach ($pendingUsers as $pendingData) {
            User::firstOrCreate(
                ['email' => $pendingData['email']],
                [
                    'name' => $pendingData['name'],
                    'password' => Hash::make('password'),
                    'whatsapp' => $pendingData['whatsapp'],
                    'status' => UserStatus::PENDING,
                    'affiliate_code' => null,
                ]
            );
        }

        $this->command->info('Users seeded successfully!');
        $this->command->info('Super Admin: admin@pams.test / password');
        $this->command->info('5 Affiliates created with codes: AHMAD001, SITI0002, BUDI0003, DEWI0004, EKO00005');
        $this->command->info('3 Pending users created for approval testing');
    }
}
