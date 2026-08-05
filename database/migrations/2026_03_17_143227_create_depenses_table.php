<?php
// database/migrations/xxxx_create_depenses_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('depenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etablissement_id')->constrained()->onDelete('cascade');
            $table->string('libelle');
            $table->text('description')->nullable();
            $table->decimal('montant', 10, 2);
            $table->date('date');
            $table->string('categorie');
            $table->string('mode_paiement');
            $table->string('beneficiaire')->nullable();
            $table->string('piece_jointe')->nullable();
            $table->timestamps();
            
            $table->index(['etablissement_id', 'date']);
            $table->index('categorie');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depenses');
    }
};