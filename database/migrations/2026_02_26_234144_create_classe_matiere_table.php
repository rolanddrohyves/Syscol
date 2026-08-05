<?php
// database/migrations/2026_02_26_xxxxxx_create_classe_matiere_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classe_matiere', function (Blueprint $table) {
            $table->id();
            
            // Référence vers la classe
            $table->foreignId('classe_id')
                  ->constrained('classes')
                  ->onDelete('cascade');
            
            // Référence vers la matière (matieres avec 's')
            $table->foreignId('matiere_id')
                  ->constrained('matieres')
                  ->onDelete('cascade');
            
            $table->timestamps();
            
            // Empêcher les doublons (une même matière ne peut pas être associée 2x à la même classe)
            $table->unique(['classe_id', 'matiere_id'], 'unique_classe_matiere');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classe_matiere');
    }
};