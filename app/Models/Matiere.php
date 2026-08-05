<?php
// app/Models/Matiere.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Matiere extends Model
{
    protected $fillable = [
        'nom',
        'code',
        'coefficient',
        'niveau',
    ];

    /**
     * Relation avec les enseignants (many-to-many)
     */
    public function enseignants(): BelongsToMany
    {
        return $this->belongsToMany(Enseignant::class, 'enseignant_matiere', 'matiere_id', 'enseignant_user_id')
                    ->withTimestamps();
    }

    /**
     * Relation avec les classes (many-to-many)
     * Une matière peut être enseignée dans plusieurs classes
     * Une classe peut avoir plusieurs matières
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(Classe::class, 'classe_matiere')
                    ->withTimestamps();
    }

    /**
     * Relation avec les emplois du temps
     */
    public function emploisTemps(): HasMany
    {
        return $this->hasMany(EmploiTemps::class);
    }

    /**
     * Relation avec les notes
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }
}