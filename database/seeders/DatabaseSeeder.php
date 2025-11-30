<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // IMPORTANT: Generate permissions first before seeding roles and users
        $this->command->info('🔐 Generating Filament Shield permissions...');
        \Artisan::call('shield:generate', ['--all' => true, '--option' => 'policies_and_permissions']);
        $this->command->info('✅ Permissions generated successfully!');

        $this->call([
            // 1. Create roles and assign permissions (requires permissions to exist)
            RoleSeeder::class,

            // 2. Create users with roles (requires roles to exist)
            SuperAdminSeeder::class,
            UserSeeder::class,

            // 3. Seed content data (requires users to exist for foreign keys)
            PropertySeeder::class,
            LeadSeeder::class,
            VisitSeeder::class,

            // 4. Seed settings and legal documents
            LegalDocumentsSeeder::class,
        ]);

        $this->command->info('✅ Database seeding completed successfully!');
    }
}
