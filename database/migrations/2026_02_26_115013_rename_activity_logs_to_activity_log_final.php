<?php
// database/migrations/2026_02_26_xxxxxx_rename_activity_logs_to_activity_log_final.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Vérifier que la source existe et que la destination n'existe pas
        if (Schema::hasTable('activity_logs') && !Schema::hasTable('activity_log')) {
            Schema::rename('activity_logs', 'activity_log');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('activity_log') && !Schema::hasTable('activity_logs')) {
            Schema::rename('activity_log', 'activity_logs');
        }
    }
};