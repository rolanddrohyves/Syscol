<?php
// database/migrations/2026_02_23_xxxxxx_create_configurations_table.php

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
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->index();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, boolean, integer, json, array, datetime
            $table->string('group')->default('general')->index();
            $table->string('description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();
            
            // Index composé pour les recherches fréquentes
            $table->index(['group', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configurations');
    }
};