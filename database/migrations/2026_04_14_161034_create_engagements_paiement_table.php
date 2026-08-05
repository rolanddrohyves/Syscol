<?php
// database/migrations/2026_04_14_120000_create_engagements_paiement_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('engagements_paiement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleve_id')->constrained()->onDelete('cascade');
            $table->foreignId('paiement_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('montant_total', 10, 3);
            $table->decimal('montant_paye', 10, 3)->default(0);
            $table->date('date_engagement');
            $table->date('date_echeance');
            $table->enum('statut', ['en_cours', 'respecte', 'non_respecte', 'renégocié'])->default('en_cours');
            $table->enum('frequence', ['unique', 'hebdomadaire', 'mensuel'])->default('unique');
            $table->text('conditions')->nullable();
            $table->foreignId('cree_par')->constrained('users');
            $table->timestamps();
            
            $table->index(['eleve_id', 'statut']);
            $table->index('date_echeance');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('engagements_paiement');
    }
};