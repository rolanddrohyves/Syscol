<?php
// database/migrations/2026_04_14_235000_add_foreign_keys_to_echeances.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Vérifier que toutes les tables existent
        if (!Schema::hasTable('echeances') || !Schema::hasTable('eleves') || !Schema::hasTable('frais_scolarites')) {
            return;
        }
        
        Schema::table('echeances', function (Blueprint $table) {
            // Vérifier si les colonnes existent
            if (Schema::hasColumn('echeances', 'eleve_id')) {
                $table->foreign('eleve_id')->references('id')->on('eleves')->onDelete('cascade');
            }
            if (Schema::hasColumn('echeances', 'frais_id')) {
                $table->foreign('frais_id')->references('id')->on('frais_scolarites')->onDelete('cascade');
            }
            if (Schema::hasColumn('echeances', 'paiement_id') && Schema::hasTable('paiements')) {
                $table->foreign('paiement_id')->references('id')->on('paiements')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('echeances')) {
            Schema::table('echeances', function (Blueprint $table) {
                $table->dropForeign(['eleve_id']);
                $table->dropForeign(['frais_id']);
                $table->dropForeign(['paiement_id']);
            });
        }
    }
};