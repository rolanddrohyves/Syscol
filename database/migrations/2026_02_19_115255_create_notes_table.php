<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleve_id')->constrained();
            $table->foreignId('matiere_id')->constrained();
            $table->foreignId('enseignant_id')->constrained('users');
            
            $table->foreignId('trimestre_id')->constrained('annees_scolaires');
            
            $table->decimal('note', 5, 2);
            $table->decimal('note_max', 5, 2)->default(20);
            $table->string('appreciation')->nullable();
            $table->date('date_evaluation');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};