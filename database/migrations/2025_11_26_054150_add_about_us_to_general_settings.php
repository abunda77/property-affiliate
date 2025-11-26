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
        $exists = DB::table('settings')
            ->where('group', 'general')
            ->where('name', 'about_us')
            ->exists();

        if (!$exists) {
            DB::table('settings')->insert([
                'group' => 'general',
                'name' => 'about_us',
                'locked' => 0,
                'payload' => json_encode(null),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')
            ->where('group', 'general')
            ->where('name', 'about_us')
            ->delete();
    }
};
