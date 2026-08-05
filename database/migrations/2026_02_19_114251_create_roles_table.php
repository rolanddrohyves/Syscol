<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // admin, enseignant, parent, etc.
            $table->string('display_name'); // Administrateur, Enseignant, Parent
            $table->text('description')->nullable();
            $table->integer('level')->default(1); // Hiérarchie des droits
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};