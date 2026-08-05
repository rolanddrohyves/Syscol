<?php
// database/migrations/xxxx_create_niveaux_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('niveaux', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etablissement_id')->constrained()->onDelete('cascade');
            $table->string('nom'); // 6ème, 5ème, 4ème, 3ème, Seconde, Première, Terminale
            $table->integer('ordre')->default(0); // Pour l'ordre d'affichage
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('niveaux');
    }
};