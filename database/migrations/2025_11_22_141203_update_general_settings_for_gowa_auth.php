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
        // Update settings table to replace gowa_api_key with username/password
        DB::table('settings')
            ->where('group', 'general')
            ->where('name', 'gowa_api_key')
            ->delete();
        
        // Insert new settings if they don't exist
        $existingNames = DB::table('settings')
            ->where('group', 'general')
            ->whereIn('name', ['gowa_username', 'gowa_password'])
            ->pluck('name')
            ->toArray();

        if (!in_array('gowa_username', $existingNames)) {
            DB::table('settings')->insert([
                'group' => 'general',
                'name' => 'gowa_username',
                'locked' => 0,
                'payload' => json_encode(null),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (!in_array('gowa_password', $existingNames)) {
            DB::table('settings')->insert([
                'group' => 'general',
                'name' => 'gowa_password',
                'locked' => 0,
                'payload' => json_encode(null),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Add test_phone setting
        $testPhoneExists = DB::table('settings')
            ->where('group', 'general')
            ->where('name', 'test_phone')
            ->exists();

        if (!$testPhoneExists) {
            DB::table('settings')->insert([
                'group' => 'general',
                'name' => 'test_phone',
                'locked' => 0,
                'payload' => json_encode(null),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Update API URL default if it exists
        DB::table('settings')
            ->where('group', 'general')
            ->where('name', 'gowa_api_url')
            ->update([
                'payload' => json_encode('http://localhost:3000'),
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove new settings
        DB::table('settings')
            ->where('group', 'general')
            ->whereIn('name', ['gowa_username', 'gowa_password', 'test_phone'])
            ->delete();

        // Restore old API key setting
        $existingKey = DB::table('settings')
            ->where('group', 'general')
            ->where('name', 'gowa_api_key')
            ->exists();

        if (!$existingKey) {
            DB::table('settings')->insert([
                'group' => 'general',
                'name' => 'gowa_api_key',
                'locked' => 0,
                'payload' => json_encode(null),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Restore old API URL default
        DB::table('settings')
            ->where('group', 'general')
            ->where('name', 'gowa_api_url')
            ->update([
                'payload' => json_encode('https://api.gowa.id/v1'),
                'updated_at' => now(),
            ]);
    }
};
