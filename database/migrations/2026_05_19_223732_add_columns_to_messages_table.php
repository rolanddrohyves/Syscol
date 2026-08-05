<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_columns_to_messages_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Ajouter la colonne type
            if (!Schema::hasColumn('messages', 'type')) {
                $table->enum('type', [
                    'parent_to_admin',
                    'admin_to_superadmin',
                    'admin_reply',
                    'superadmin_reply'
                ])->default('parent_to_admin')->after('message');
            }
            
            // Ajouter la colonne status
            if (!Schema::hasColumn('messages', 'status')) {
                $table->enum('status', ['envoye', 'recu', 'lu', 'repondu'])->default('envoye')->after('type');
            }
            
            // Ajouter la colonne etablissement_id
            if (!Schema::hasColumn('messages', 'etablissement_id')) {
                $table->foreignId('etablissement_id')->nullable()->after('receiver_id')
                    ->constrained('etablissements')->onDelete('cascade');
            }
            
            // Ajouter la colonne parent_message_id (remplace parent_id)
            if (!Schema::hasColumn('messages', 'parent_message_id') && !Schema::hasColumn('messages', 'parent_id')) {
                $table->foreignId('parent_message_id')->nullable()->after('eleve_id')
                    ->constrained('messages')->onDelete('cascade');
            }
            
            // Renommer parent_id en parent_message_id si nécessaire
            if (Schema::hasColumn('messages', 'parent_id') && !Schema::hasColumn('messages', 'parent_message_id')) {
                $table->renameColumn('parent_id', 'parent_message_id');
            }
            
            // Ajouter les index
            if (!Schema::hasIndex('messages', 'messages_type_index')) {
                $table->index(['type']);
            }
            
            if (!Schema::hasIndex('messages', 'messages_etablissement_id_index')) {
                $table->index(['etablissement_id']);
            }
            
            if (!Schema::hasIndex('messages', 'messages_receiver_id_type_lu_index')) {
                $table->index(['receiver_id', 'type', 'lu']);
            }
            
            if (!Schema::hasIndex('messages', 'messages_sender_id_type_index')) {
                $table->index(['sender_id', 'type']);
            }
        });
    }
    
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['type', 'status', 'etablissement_id']);
            
            if (Schema::hasColumn('messages', 'parent_message_id')) {
                $table->dropColumn('parent_message_id');
            }
            
            $table->dropIndex(['type']);
            $table->dropIndex(['etablissement_id']);
            $table->dropIndex(['receiver_id', 'type', 'lu']);
            $table->dropIndex(['sender_id', 'type']);
        });
    }
};