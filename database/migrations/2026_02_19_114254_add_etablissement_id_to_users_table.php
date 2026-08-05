<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->after('password')->constrained();
            $table->foreignId('etablissement_id')->after('role_id')->nullable()->constrained();
            $table->string('telephone')->nullable()->after('email');
            $table->boolean('is_active')->default(true)->after('telephone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['etablissement_id']);
            $table->dropColumn(['role_id', 'etablissement_id', 'telephone', 'is_active']);
        });
    }
};