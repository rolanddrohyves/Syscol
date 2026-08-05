<?php
// app/Models/Message.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'etablissement_id',
        'eleve_id',
        'sujet',
        'message',
        'type',
        'status',
        'lu',
        'lu_at',
        'parent_message_id'
    ];

    protected $casts = [
        'lu' => 'boolean',
        'lu_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relation avec l'expéditeur
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Relation avec le destinataire
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    // Relation avec l'établissement
    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class);
    }

    // Relation avec l'élève
    public function eleve()
    {
        return $this->belongsTo(Eleve::class);
    }

    // Relation avec le message parent (pour les réponses)
    public function parentMessage()
    {
        return $this->belongsTo(Message::class, 'parent_message_id');
    }

    // Relation avec les réponses
    public function replies()
    {
        return $this->hasMany(Message::class, 'parent_message_id');
    }

    // Scope pour les messages non lus
    public function scopeUnread($query, $userId = null)
    {
        $query->where('lu', false);
        if ($userId) {
            $query->where('receiver_id', $userId);
        }
        return $query;
    }

    // Scope pour les messages reçus
    public function scopeReceived($query, $userId)
    {
        return $query->where('receiver_id', $userId);
    }

    // Scope pour les messages envoyés
    public function scopeSent($query, $userId)
    {
        return $query->where('sender_id', $userId);
    }

    // Scope pour les messages par type
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Marquer comme lu
    public function markAsRead()
    {
        $this->update([
            'lu' => true,
            'lu_at' => now()
        ]);
    }

    // Vérifier si le message est lu
    public function isRead()
    {
        return $this->lu;
    }

    // Récupérer le nombre de réponses
    public function getRepliesCountAttribute()
    {
        return $this->replies()->count();
    }
}