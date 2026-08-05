<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eleves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classe_id')->constrained();
            $table->string('matricule')->unique();
            $table->string('prenom');
            $table->string('nom');
            $table->date('date_naissance');
            $table->string('lieu_naissance');
            $table->enum('sexe', ['M', 'F']);
            $table->string('adresse');
            $table->string('telephone_parent');
            $table->string('nom_parent');
            $table->string('email_parent')->nullable();
            $table->string('photo')->nullable();
            $table->enum('status', ['actif', 'exclu', 'transferé', 'redoublant'])->default('actif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eleves');
    }
};