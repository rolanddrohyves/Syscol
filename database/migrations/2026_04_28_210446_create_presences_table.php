<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            
            // Relations (sans clés étrangères avec 'constrained' pour éviter les erreurs)
            $table->unsignedBigInteger('eleve_id');
            $table->unsignedBigInteger('classe_id');
            $table->unsignedBigInteger('enseignant_id')->nullable();
            $table->unsignedBigInteger('annee_scolaire_id')->nullable();
            $table->unsignedBigInteger('trimestre_id')->nullable();
            
            // Informations de présence
            $table->date('date');
            $table->enum('statut', ['present', 'absent', 'retard', 'excuse'])->default('present');
            $table->boolean('justifiee')->default(false);
            $table->text('motif')->nullable();
            
            // Horaires
            $table->datetime('heure_arrivee')->nullable();
            $table->datetime('heure_depart')->nullable();
            
            // Métadonnées
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Index pour optimiser les recherches
            $table->index(['date', 'classe_id']);
            $table->index(['eleve_id', 'date']);
            $table->index('statut');
            $table->index('eleve_id');
            $table->index('classe_id');
            $table->index('enseignant_id');
            $table->index('annee_scolaire_id');
            $table->index('trimestre_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presences');
    }
};