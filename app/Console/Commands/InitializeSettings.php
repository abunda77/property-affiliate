<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InitializeSettings extends Command
{
    protected $signature = 'settings:init';
    protected $description = 'Initialize all GeneralSettings properties with default values';

    public function handle()
    {
        $this->info('Initializing GeneralSettings...');

        $keys = [
            // Legal Documents
            'terms_and_conditions',
            'privacy_policy',
            'disclaimer',
            'about_us',
            // GoWA API Configuration
            'gowa_username',
            'gowa_password',
            'gowa_api_url',
            'test_phone',
            // Logo
            'logo_path',
            'logo_url',
            'favicon_path',
            // SEO Settings
            'seo_meta_title',
            'seo_meta_description',
            'seo_meta_keywords',
            // Contact Information
            'contact_email',
            'contact_whatsapp',
        ];

        $created = 0;
        foreach ($keys as $key) {
            if (!DB::table('settings')->where('group', 'general')->where('name', $key)->exists()) {
                DB::table('settings')->insert([
                    'group' => 'general',
                    'name' => $key,
                    'locked' => false,
                    'payload' => json_encode(null),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $created++;
            }
        }

        if ($created > 0) {
            $this->info("✓ Created {$created} missing settings properties");
        } else {
            $this->info('✓ All settings properties already exist');
        }

        $this->info('Settings initialization complete!');
        return 0;
    }
}
