<?php
// database/migrations/2026_04_14_200000_create_frais_scolarites_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('frais_scolarites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('etablissement_id');
            $table->unsignedBigInteger('annee_scolaire_id');
            $table->unsignedBigInteger('classe_id')->nullable();
            $table->string('libelle');
            $table->text('description')->nullable();
            $table->decimal('montant', 10, 2);
            $table->string('type')->default('scolarite');
            $table->string('periodicite')->default('unique');
            $table->boolean('obligatoire')->default(true);
            $table->timestamps();
            
            $table->index('etablissement_id');
            $table->index('annee_scolaire_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('frais_scolarites');
    }
};