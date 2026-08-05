<?php
// database/migrations/2026_04_02_095331_create_echeances_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('echeances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('eleve_id');
            $table->unsignedBigInteger('frais_id');
            $table->unsignedBigInteger('paiement_id')->nullable();
            $table->string('libelle');
            $table->text('description')->nullable();
            $table->decimal('montant', 10, 2);
            $table->decimal('montant_paye', 10, 2)->default(0);
            $table->date('date_echeance');
            $table->date('date_limite');
            $table->string('statut')->default('en_attente');
            $table->integer('ordre')->default(1);
            $table->string('periode')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Seulement des index, pas de clés étrangères
            $table->index('eleve_id');
            $table->index('frais_id');
            $table->index('paiement_id');
            $table->index('statut');
            $table->index('date_echeance');
            $table->index('date_limite');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('echeances');
    }
};