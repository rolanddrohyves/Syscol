<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presences', function (Blueprint $table) {
            // Ajouter les clés étrangères (en supposant que les colonnes existent déjà)
            $table->foreign('eleve_id')->references('id')->on('eleves')->onDelete('cascade');
            $table->foreign('classe_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('enseignant_id')->references('user_id')->on('enseignants')->onDelete('set null');
            $table->foreign('annee_scolaire_id')->references('id')->on('annees_scolaires')->onDelete('set null');
            $table->foreign('trimestre_id')->references('id')->on('trimestres')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('presences', function (Blueprint $table) {
            $table->dropForeign(['eleve_id']);
            $table->dropForeign(['classe_id']);
            $table->dropForeign(['enseignant_id']);
            $table->dropForeign(['annee_scolaire_id']);
            $table->dropForeign(['trimestre_id']);
        });
    }
};