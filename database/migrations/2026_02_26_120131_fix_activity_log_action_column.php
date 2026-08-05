<?php
// database/migrations/2026_02_26_120131_fix_activity_log_action_column.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Rendre la colonne 'action' nullable (problème principal)
        if (Schema::hasColumn('activity_log', 'action')) {
            DB::statement("ALTER TABLE activity_log MODIFY action VARCHAR(255) NULL;");
        }
        
        // Ajouter les colonnes Spatie manquantes sans recréer les index
        Schema::table('activity_log', function (Blueprint $table) {
            // Vérifier et ajouter les colonnes une par une
            if (!Schema::hasColumn('activity_log', 'log_name')) {
                $table->string('log_name')->nullable()->after('id');
            }
            
            if (!Schema::hasColumn('activity_log', 'description')) {
                $table->text('description')->nullable()->after('log_name');
            }
            
            if (!Schema::hasColumn('activity_log', 'subject_type')) {
                $table->string('subject_type')->nullable()->after('description');
            }
            
            if (!Schema::hasColumn('activity_log', 'subject_id')) {
                $table->unsignedBigInteger('subject_id')->nullable()->after('subject_type');
            }
            
            if (!Schema::hasColumn('activity_log', 'causer_type')) {
                $table->string('causer_type')->nullable()->after('subject_id');
            }
            
            if (!Schema::hasColumn('activity_log', 'causer_id')) {
                $table->unsignedBigInteger('causer_id')->nullable()->after('causer_type');
            }
            
            if (!Schema::hasColumn('activity_log', 'properties')) {
                $table->json('properties')->nullable()->after('causer_id');
            }
            
            if (!Schema::hasColumn('activity_log', 'batch_uuid')) {
                $table->uuid('batch_uuid')->nullable()->after('properties');
            }
            
            if (!Schema::hasColumn('activity_log', 'event')) {
                $table->string('event')->nullable()->after('batch_uuid');
            }
        });
        
        // Gérer les index séparément pour éviter les doublons
        $this->handleIndexes();
    }
    
    private function handleIndexes(): void
    {
        $indexes = $this->getExistingIndexes();
        
        // Index pour log_name
        if (!in_array('activity_log_log_name_index', $indexes)) {
            Schema::table('activity_log', function (Blueprint $table) {
                $table->index('log_name');
            });
        }
        
        // Index pour subject
        if (!in_array('activity_log_subject_id_subject_type_index', $indexes) && 
            Schema::hasColumn('activity_log', 'subject_id') && 
            Schema::hasColumn('activity_log', 'subject_type')) {
            Schema::table('activity_log', function (Blueprint $table) {
                $table->index(['subject_id', 'subject_type']);
            });
        }
        
        // Index pour causer
        if (!in_array('activity_log_causer_id_causer_type_index', $indexes) && 
            Schema::hasColumn('activity_log', 'causer_id') && 
            Schema::hasColumn('activity_log', 'causer_type')) {
            Schema::table('activity_log', function (Blueprint $table) {
                $table->index(['causer_id', 'causer_type']);
            });
        }
    }
    
    private function getExistingIndexes(): array
    {
        $database = env('DB_DATABASE');
        $table = 'activity_log';
        
        $results = DB::select("
            SELECT DISTINCT INDEX_NAME 
            FROM INFORMATION_SCHEMA.STATISTICS 
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
        ", [$database, $table]);
        
        return array_column($results, 'INDEX_NAME');
    }

    public function down(): void
    {
        // Remettre action comme non nullable si nécessaire
        if (Schema::hasColumn('activity_log', 'action')) {
            DB::statement("ALTER TABLE activity_log MODIFY action VARCHAR(255) NOT NULL;");
        }
    }
};