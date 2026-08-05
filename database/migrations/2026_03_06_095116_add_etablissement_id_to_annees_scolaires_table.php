<?php
// database/migrations/xxxx_add_etablissement_id_to_annees_scolaires_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('annees_scolaires', function (Blueprint $table) {
            $table->foreignId('etablissement_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('cascade')
                  ->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('annees_scolaires', function (Blueprint $table) {
            $table->dropForeign(['etablissement_id']);
            $table->dropColumn('etablissement_id');
        });
    }
};