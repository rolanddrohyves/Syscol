<?php
// database/migrations/2026_02_19_114259_create_enseignant_matiere_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enseignant_matiere', function (Blueprint $table) {
            $table->id();
            
            // Note: 'enseignant_user_id' référence 'users.id'
            $table->foreignId('enseignant_user_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            
            // ✅ 'matiere_id' référence 'matieres.id' (avec un 's')
            $table->foreignId('matiere_id')
                  ->constrained('matieres')  // Note: 'matieres' avec un 's'
                  ->onDelete('cascade');
            
            $table->timestamps();
            
            // Empêcher les doublons
            $table->unique(['enseignant_user_id', 'matiere_id'], 'unique_ensmat');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enseignant_matiere');
    }
};