<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_messages_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            
            // Expéditeur et destinataire
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            
            // Contenu
            $table->string('sujet');
            $table->text('message');
            
            // Références
            $table->foreignId('eleve_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('parent_id')->nullable()->constrained('messages')->onDelete('cascade');
            
            // Statut
            $table->boolean('lu')->default(false);
            $table->timestamp('lu_at')->nullable();
            
            $table->timestamps();
            
            // Index pour optimiser les recherches
            $table->index(['sender_id', 'created_at']);
            $table->index(['receiver_id', 'lu', 'created_at']);
            $table->index(['eleve_id']);
            $table->index(['parent_id']);
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};