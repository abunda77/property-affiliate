<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the key exists before inserting
        $exists = \Illuminate\Support\Facades\DB::table('settings')
            ->where('group', 'general')
            ->where('name', 'hero_background_image')
            ->exists();

        if (!$exists) {
            \Illuminate\Support\Facades\DB::table('settings')->insert([
                'group' => 'general',
                'name' => 'hero_background_image',
                'locked' => false,
                'payload' => json_encode(null),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::table('settings')
            ->where('group', 'general')
            ->where('name', 'hero_background_image')
            ->delete();
    }
};
