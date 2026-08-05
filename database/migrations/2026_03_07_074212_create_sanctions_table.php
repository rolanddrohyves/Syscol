<?php
// database/migrations/2024_xx_xx_create_sanctions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sanctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleve_id')->constrained()->onDelete('cascade');
            $table->string('type'); // exclusion, retenue, avertissement, etc.
            $table->date('date');
            $table->text('motif')->nullable();
            $table->text('description')->nullable();
            $table->integer('duree')->nullable(); // en heures ou jours
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->string('statut')->default('en_cours'); // en_cours, executee, annulee
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sanctions');
    }
};