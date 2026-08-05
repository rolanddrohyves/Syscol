<?php
// database/migrations/2026_04_14_130000_add_echeance_details_to_paiements.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            // Ajouter les colonnes si elles n'existent pas
            if (!Schema::hasColumn('paiements', 'date_limite')) {
                $table->date('date_limite')->nullable()->after('date_echeance');
            }
            if (!Schema::hasColumn('paiements', 'periode_concernee')) {
                $table->string('periode_concernee')->nullable()->after('date_limite');
            }
            if (!Schema::hasColumn('paiements', 'est_echeance')) {
                $table->boolean('est_echeance')->default(false)->after('periode_concernee');
            }
            if (!Schema::hasColumn('paiements', 'ordre_echeance')) {
                $table->integer('ordre_echeance')->default(1)->after('est_echeance');
            }
        });
    }

    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropColumn(['date_limite', 'periode_concernee', 'est_echeance', 'ordre_echeance']);
        });
    }
};