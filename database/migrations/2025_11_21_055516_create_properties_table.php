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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('price');
            $table->text('location');
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->json('specs')->nullable();
            $table->enum('status', ['draft', 'published', 'sold'])->default('draft');
            $table->timestamps();
            
            $table->index('slug');
            $table->index('status');
            $table->index('price');
            $table->fullText(['title', 'location', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
