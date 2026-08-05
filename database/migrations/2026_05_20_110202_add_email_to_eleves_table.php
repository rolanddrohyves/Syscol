<?php
// database/migrations/2024_01_20_000003_add_email_to_eleves_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eleves', function (Blueprint $table) {
            // Ajouter la colonne email si elle n'existe pas
            if (!Schema::hasColumn('eleves', 'email')) {
                $table->string('email')->nullable()->after('telephone_parent');
            }
            
            // Ajouter la colonne user_id si elle n'existe pas
            if (!Schema::hasColumn('eleves', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('eleves', function (Blueprint $table) {
            if (Schema::hasColumn('eleves', 'email')) {
                $table->dropColumn('email');
            }
            if (Schema::hasColumn('eleves', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};