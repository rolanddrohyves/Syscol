<?php
// database/migrations/2026_02_19_114255_create_classes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etablissement_id')->constrained();
            $table->string('nom'); // 6ème A, Terminale S, etc.
            $table->enum('niveau', ['Primaire', 'Collège', 'Lycée']);
            $table->string('serie')->nullable(); // S, L, SE, etc.
            $table->integer('capacite')->default(30);
            
            $table->foreignId('annee_scolaire_id')->constrained('annees_scolaires');
            
            $table->foreignId('professeur_principal_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};