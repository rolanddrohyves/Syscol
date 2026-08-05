<?php
// app/Models/ActivityLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    // CORRECTION : Utiliser le nouveau nom de table
    protected $table = 'activity_log';  // ← Changé de 'activity_logs' à 'activity_log'
    
    protected $fillable = [
        'user_id',
        'action',
        'description',
        'model_type',
        'model_id',
        'ip_address',
        'user_agent',
        'old_values',
        'new_values',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope pour filtrer par utilisateur
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope pour filtrer par action
     */
    public function scopeWithAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope pour filtrer par modèle
     */
    public function scopeForModel($query, $modelType, $modelId = null)
    {
        $query->where('model_type', $modelType);
        
        if ($modelId) {
            $query->where('model_id', $modelId);
        }
        
        return $query;
    }

    /**
     * Scope pour filtrer par période
     */
    public function scopeInPeriod($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    /**
     * Scope pour les actions récentes
     */
    public function scopeRecent($query, $limit = 50)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /**
     * Récupère le libellé de l'action en français
     */
    public function getActionLabelAttribute()
    {
        $labels = [
            'create' => 'Création',
            'update' => 'Modification',
            'delete' => 'Suppression',
            'login' => 'Connexion',
            'logout' => 'Déconnexion',
            'export' => 'Export',
            'import' => 'Import',
            'view' => 'Consultation',
            'download' => 'Téléchargement',
        ];
        
        return $labels[$this->action] ?? ucfirst($this->action);
    }

    /**
     * Récupère la couleur selon l'action
     */
    public function getActionColorAttribute()
    {
        $colors = [
            'create' => 'green',
            'update' => 'blue',
            'delete' => 'red',
            'login' => 'indigo',
            'logout' => 'gray',
            'export' => 'purple',
            'import' => 'yellow',
        ];
        
        return $colors[$this->action] ?? 'gray';
    }

    /**
     * Récupère l'icône selon l'action
     */
    public function getActionIconAttribute()
    {
        $icons = [
            'create' => 'fas fa-plus-circle',
            'update' => 'fas fa-edit',
            'delete' => 'fas fa-trash-alt',
            'login' => 'fas fa-sign-in-alt',
            'logout' => 'fas fa-sign-out-alt',
            'export' => 'fas fa-file-export',
            'import' => 'fas fa-file-import',
        ];
        
        return $icons[$this->action] ?? 'fas fa-circle';
    }
}