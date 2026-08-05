<?php
// app/Models/Role.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'name', 
        'display_name', 
        'description', 
        'level'
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // Vérifications pratiques
    public function isAdmin(): bool
    {
        return $this->name === 'super_admin' || $this->name === 'admin_etablissement';
    }

    public function isEnseignant(): bool
    {
        return $this->name === 'enseignant';
    }

    public function isParent(): bool
    {
        return $this->name === 'parent';
    }

    public function isEleve(): bool
    {
        return $this->name === 'eleve';
    }

    public function hasHigherLevelThan(Role $role): bool
    {
        return $this->level > $role->level;
    }
}