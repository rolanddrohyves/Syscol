<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; 
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

class User extends Authenticatable
{
    use Notifiable, LogsActivity;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'etablissement_id',
        'telephone',
        'is_active',
        'last_login_at', 
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    // ============================================
    // CONFIGURATION ACTIVITY LOG
    // ============================================
    
    /**
     * Configure les options de logging pour ce modèle
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'role_id', 'etablissement_id', 'is_active', 'last_login_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('user')
            ->setDescriptionForEvent(fn(string $eventName) => "Utilisateur {$eventName}");
    }

    /**
     * Personnalisation des propriétés à enregistrer dans les logs
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->properties = $activity->properties->merge([
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'role' => $this->role->name ?? 'inconnu',
            'etablissement' => $this->etablissement?->nom ?? 'Aucun'
        ]);
    }

    // ============================================
    // RELATIONS
    // ============================================
    
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function enseignant(): HasOne
    {
        return $this->hasOne(Enseignant::class, 'user_id');
    }

    /**
     * Classes dont l'utilisateur est professeur principal
     */
    public function classes(): HasMany
    {
        return $this->hasMany(Classe::class, 'professeur_principal_id');
    }

    /**
     * Emplois du temps où l'utilisateur est enseignant
     */
    public function emploisTemps(): HasMany
    {
        return $this->hasMany(EmploiTemps::class, 'enseignant_id');
    }

    /**
     * Activités (logs) via Spatie Activitylog
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'causer_id');
    }

    /**
     * Relation Many-to-Many avec les classes enseignées
     * (via la table pivot enseignant_classe)
     */
    public function classesEnseignees(): BelongsToMany
    {
        return $this->belongsToMany(
            Classe::class, 
            'enseignant_classe', 
            'enseignant_user_id', 
            'classe_id'
        )->withTimestamps();
    }

    /**
     * Relation Many-to-Many avec les matières (via le modèle Enseignant)
     */
    public function matieres()
    {
        return $this->hasManyThrough(
            Matiere::class,
            Enseignant::class,
            'user_id',      // Clé étrangère sur enseignants
            'id',           // Clé locale sur matieres
            'id',           // Clé locale sur users
            'user_id'       // Clé étrangère sur enseignants
        );
    }

    // ============================================
    // VÉRIFICATIONS DE RÔLE
    // ============================================
    
    public function hasRole(string $roleName): bool
    {
        return $this->role?->name === $roleName;
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function isAdminEtablissement(): bool
    {
        return $this->hasRole('admin_etablissement');
    }

    public function isDirecteurEtudes(): bool
    {
        return $this->hasRole('directeur_etudes');
    }

    public function isCpe(): bool
    {
        return $this->hasRole('cpe');
    }

    public function isEnseignant(): bool
    {
        return $this->hasRole('enseignant');
    }

    public function isParent(): bool
    {
        return $this->hasRole('parent');
    }

    public function isEleve(): bool
    {
        return $this->hasRole('eleve');
    }

    // ============================================
    // MÉTHODES UTILITAIRES
    // ============================================

    /**
     * Récupère les classes enseignées par l'utilisateur (via emplois du temps)
     */
    public function getClassesEnseigneesViaEmploiTemps()
    {
        return Classe::whereHas('emploisTemps', function($q) {
            $q->where('enseignant_id', $this->id);
        })->get();
    }

    /**
     * Vérifie si l'utilisateur peut accéder à un module
     */
    public function canAccessModule(string $module): bool
    {
        if ($this->isSuperAdmin()) return true;
        
        return match($this->role?->name) {
            'admin_etablissement' => in_array($module, ['dashboard', 'users', 'classes', 'notes', 'emplois_temps', 'eleves', 'enseignants']),
            'directeur_etudes' => in_array($module, ['dashboard', 'classes', 'emplois_temps', 'professeurs', 'examens']),
            'cpe' => in_array($module, ['dashboard', 'eleves', 'absences', 'retards', 'disciplines']),
            'enseignant' => in_array($module, ['dashboard', 'mes_classes', 'notes', 'presences', 'emploi_temps']),
            'parent' => in_array($module, ['dashboard', 'suivi_enfant', 'bulletins', 'notes']),
            'eleve' => in_array($module, ['dashboard', 'mes_notes', 'emploi_temps', 'devoirs']),
            default => false,
        };
    }

    /**
     * Vérifie si l'utilisateur peut gérer un autre utilisateur
     */
    public function canManageUser(User $user): bool
    {
        if ($this->isSuperAdmin()) return true;
        
        if ($this->isAdminEtablissement()) {
            return $this->etablissement_id === $user->etablissement_id 
                && $this->role->level > $user->role->level;
        }
        
        if ($this->isDirecteurEtudes()) {
            return $this->etablissement_id === $user->etablissement_id 
                && in_array($user->role->name, ['enseignant', 'eleve']);
        }
        
        return $this->id === $user->id;
    }

    /**
     * Raccourci pour obtenir le nom du rôle affiché
     */
    public function getRoleDisplayNameAttribute(): string
    {
        return $this->role->display_name ?? $this->role->name ?? 'Inconnu';
    }

    /**
     * Raccourci pour obtenir les initiales
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        return substr($initials, 0, 2);
    }

    /**
     * Scope pour les utilisateurs actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour filtrer par rôle
     */
    public function scopeWithRole($query, $roleName)
    {
        return $query->whereHas('role', fn($q) => $q->where('name', $roleName));
    }

    /**
     * Scope pour les enseignants
     */
    public function scopeEnseignants($query)
    {
        return $query->whereHas('role', fn($q) => $q->where('name', 'enseignant'));
    }

    /**
     * Obtenir les logs récents de l'utilisateur
     */
    public function getRecentActivities(int $limit = 10)
    {
        return $this->activities()
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Obtenir les statistiques d'activité de l'utilisateur
     */
    public function getActivityStats()
    {
        return [
            'total_logins' => $this->activities()
                ->where('event', 'login')
                ->count(),
            'last_login' => $this->last_login_at,
            'total_activities' => $this->activities()->count(),
            'activities_by_event' => $this->activities()
                ->selectRaw('event, count(*) as count')
                ->groupBy('event')
                ->pluck('count', 'event')
        ];
    }
}