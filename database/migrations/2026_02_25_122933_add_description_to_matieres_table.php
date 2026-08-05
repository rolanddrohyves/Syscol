<?php
// database/migrations/2026_02_25_xxxxxx_add_description_to_matieres_table.php

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
        Schema::table('matieres', function (Blueprint $table) {
            // Ajouter plusieurs colonnes descriptives
            $table->text('description')->nullable()->after('niveau');
            $table->string('objectifs', 500)->nullable()->after('description');
            $table->integer('duree_heures')->nullable()->after('objectifs')->comment('Durée du programme en heures');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matieres', function (Blueprint $table) {
            // Supprimer toutes les colonnes ajoutées
            $table->dropColumn(['description', 'objectifs', 'duree_heures']);
        });
    }
};