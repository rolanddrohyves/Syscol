<?php
// database/migrations/2026_04_14_100000_create_paiements_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleve_id')->constrained()->onDelete('cascade');
            $table->foreignId('frais_id')->constrained('frais_scolarites')->onDelete('cascade');
            $table->foreignId('facture_id')->nullable()->constrained('factures')->onDelete('set null');
            $table->decimal('montant', 10, 2);
            $table->decimal('montant_paye', 10, 2)->default(0);
            $table->decimal('montant_restant', 10, 2)->default(0);
            $table->date('date_paiement');
            $table->date('date_echeance')->nullable();
            $table->date('date_limite')->nullable();
            $table->string('periode_concernee')->nullable();
            $table->boolean('est_echeance')->default(false);
            $table->integer('ordre_echeance')->default(1);
            $table->enum('mode_paiement', ['especes', 'cheque', 'virement', 'carte', 'mobile_money'])->default('especes');
            $table->string('reference')->nullable()->unique();
            $table->enum('statut', ['en_attente', 'partiel', 'paye', 'annule'])->default('en_attente');
            $table->text('commentaire')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('cree_par')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index('date_paiement');
            $table->index('date_echeance');
            $table->index('statut');
            $table->index(['eleve_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};