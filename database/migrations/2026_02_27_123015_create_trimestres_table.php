<?php
// database/migrations/2026_02_27_xxxxxx_create_trimestres_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trimestres', function (Blueprint $table) {
            $table->id();
            $table->string('libelle'); // Trimestre 1, Trimestre 2, Trimestre 3
            $table->integer('numero'); // 1, 2, 3
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->boolean('is_current')->default(false);
            
            // Correction avec le bon nom de table 'annees_scolaires'
            $table->foreignId('annee_scolaire_id')
                  ->constrained('annees_scolaires')
                  ->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trimestres');
    }
};