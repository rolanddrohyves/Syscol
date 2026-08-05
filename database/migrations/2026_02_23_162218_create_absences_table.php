<?php
// database/migrations/xxxx_create_absences_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleve_id')->constrained()->onDelete('cascade');
            $table->foreignId('classe_id')->constrained()->onDelete('cascade');
            $table->foreignId('enseignant_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('date');
            $table->time('heure')->nullable();
            $table->enum('type', ['absence', 'retard', 'sortie_anticipée'])->default('absence');
            $table->string('motif')->nullable();
            $table->boolean('justifiee')->default(false);
            $table->text('justification')->nullable();
            $table->timestamps();

            // Index pour les recherches fréquentes
            $table->index(['date', 'classe_id']);
            $table->index(['eleve_id', 'date']);
            $table->index('justifiee');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absences');
    }
};