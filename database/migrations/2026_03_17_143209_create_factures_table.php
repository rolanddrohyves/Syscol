<?php
// database/migrations/xxxx_create_factures_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etablissement_id')->constrained()->onDelete('cascade');
            $table->foreignId('eleve_id')->constrained()->onDelete('cascade');
            $table->string('numero')->unique();
            $table->date('date_emission');
            $table->date('date_echeance');
            $table->decimal('montant_ht', 10, 2);
            $table->decimal('montant_ttc', 10, 2);
            $table->enum('statut', ['emise', 'envoyee', 'payee', 'impayee'])->default('emise');
            $table->string('fichier')->nullable();
            $table->timestamps();
            
            $table->index(['etablissement_id', 'statut']);
            $table->index('date_echeance');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};