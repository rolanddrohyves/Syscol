<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etablissements', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->enum('type', ['Primaire', 'Collège', 'Lycée', 'Primaire/Secondaire']);
            $table->string('adresse');
            $table->string('telephone');
            $table->string('email')->unique();
            $table->string('code_etablissement')->unique();
            $table->string('academie');
            $table->string('inspectorat')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etablissements');
    }
};