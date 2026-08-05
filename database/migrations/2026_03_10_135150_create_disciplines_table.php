<?php
// database/migrations/2024_xx_xx_create_disciplines_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disciplines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleve_id')->constrained()->onDelete('cascade');
            $table->string('type'); // incident, avertissement, retenue, exclusion
            $table->enum('gravite', ['faible', 'moyenne', 'elevee', 'critique'])->default('moyenne');
            $table->date('date');
            $table->time('heure')->nullable();
            $table->text('description');
            $table->timestamps();
            
            // Index pour améliorer les performances
            $table->index(['eleve_id', 'date']);
            $table->index('type');
            $table->index('gravite');
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disciplines');
    }
};