<?php
// database/migrations/2026_03_24_xxxxxx_add_periodicite_to_frais_scolarites.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('frais_scolarites', function (Blueprint $table) {
            // Ajouter periodicite si elle n'existe pas
            if (!Schema::hasColumn('frais_scolarites', 'periodicite')) {
                $table->enum('periodicite', ['unique', 'mensuel', 'trimestriel', 'annuel'])->default('unique')->after('montant');
            }
            
            // Ajouter nombre_echeances
            if (!Schema::hasColumn('frais_scolarites', 'nombre_echeances')) {
                $table->integer('nombre_echeances')->default(1)->after('periodicite');
            }
        });
    }

    public function down(): void
    {
        Schema::table('frais_scolarites', function (Blueprint $table) {
            if (Schema::hasColumn('frais_scolarites', 'periodicite')) {
                $table->dropColumn('periodicite');
            }
            
            if (Schema::hasColumn('frais_scolarites', 'nombre_echeances')) {
                $table->dropColumn('nombre_echeances');
            }
        });
    }
};