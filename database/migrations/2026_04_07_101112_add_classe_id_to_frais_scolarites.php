<?php
// database/migrations/xxxx_add_classe_id_to_frais_scolarites.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('frais_scolarites', function (Blueprint $table) {
            if (!Schema::hasColumn('frais_scolarites', 'classe_id')) {
                $table->foreignId('classe_id')->nullable()->after('annee_scolaire_id')->constrained()->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('frais_scolarites', function (Blueprint $table) {
            if (Schema::hasColumn('frais_scolarites', 'classe_id')) {
                $table->dropForeign(['classe_id']);
                $table->dropColumn('classe_id');
            }
        });
    }
};