<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = [
            ['group' => 'general', 'name' => 'gowa_api_key', 'locked' => false, 'payload' => json_encode(config('services.gowa.api_key'))],
            ['group' => 'general', 'name' => 'gowa_api_url', 'locked' => false, 'payload' => json_encode(config('services.gowa.api_url'))],
            ['group' => 'general', 'name' => 'logo_path', 'locked' => false, 'payload' => json_encode(null)],
            ['group' => 'general', 'name' => 'seo_meta_title', 'locked' => false, 'payload' => json_encode('PAMS - Property Affiliate Management System')],
            ['group' => 'general', 'name' => 'seo_meta_description', 'locked' => false, 'payload' => json_encode('Platform properti dengan sistem afiliasi terpercaya. Temukan properti impian Anda dan dapatkan komisi sebagai affiliate.')],
            ['group' => 'general', 'name' => 'seo_meta_keywords', 'locked' => false, 'payload' => json_encode('properti, affiliate, real estate, jual beli properti, komisi properti')],
            ['group' => 'general', 'name' => 'contact_email', 'locked' => false, 'payload' => json_encode('info@pams.com')],
            ['group' => 'general', 'name' => 'contact_whatsapp', 'locked' => false, 'payload' => json_encode('+62 xxx xxxx xxxx')],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->insert($setting);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->where('group', 'general')->delete();
    }
};
