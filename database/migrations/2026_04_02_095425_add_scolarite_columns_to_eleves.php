<?php
// database/migrations/2026_03_24_xxxxxx_add_scolarite_columns_to_eleves.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eleves', function (Blueprint $table) {
            // Date d'inscription
            if (!Schema::hasColumn('eleves', 'date_inscription')) {
                $table->date('date_inscription')->nullable()->after('status');
            }
            
            // Montant total des frais pour l'année
            if (!Schema::hasColumn('eleves', 'montant_total_frais')) {
                $table->decimal('montant_total_frais', 10, 2)->default(0)->after('date_inscription');
            }
            
            // Montant déjà payé
            if (!Schema::hasColumn('eleves', 'montant_paye')) {
                $table->decimal('montant_paye', 10, 2)->default(0)->after('montant_total_frais');
            }
            
            // Montant restant
            if (!Schema::hasColumn('eleves', 'montant_restant')) {
                $table->decimal('montant_restant', 10, 2)->default(0)->after('montant_paye');
            }
        });
    }

    public function down(): void
    {
        Schema::table('eleves', function (Blueprint $table) {
            $table->dropColumn('date_inscription');
            $table->dropColumn('montant_total_frais');
            $table->dropColumn('montant_paye');
            $table->dropColumn('montant_restant');
        });
    }
};