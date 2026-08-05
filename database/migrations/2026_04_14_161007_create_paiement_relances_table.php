<?php
// database/migrations/2026_04_14_110000_create_paiement_relances_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paiement_relances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paiement_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['email', 'sms', 'courrier', 'appel']);
            $table->enum('statut', ['envoye', 'echec', 'en_attente'])->default('envoye');
            $table->text('message')->nullable();
            $table->string('contact')->nullable();
            $table->integer('niveau_relance')->default(1);
            $table->date('date_relance');
            $table->date('date_reponse')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('cree_par')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['paiement_id', 'statut']);
            $table->index('date_relance');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiement_relances');
    }
};