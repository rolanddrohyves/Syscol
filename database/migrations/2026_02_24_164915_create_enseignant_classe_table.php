<?php
// database/migrations/xxxx_create_enseignant_classe_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enseignant_classe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enseignant_user_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('classe_id')
                  ->constrained('classes')
                  ->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['enseignant_user_id', 'classe_id'], 'unique_enseignant_classe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enseignant_classe');
    }
};