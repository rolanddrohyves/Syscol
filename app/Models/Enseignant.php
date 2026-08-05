<?php
// app/Models/Enseignant.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Enseignant extends Model
{
    protected $table = 'enseignants';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    
    protected $fillable = [
        'user_id',
        'matricule',
        'specialite',
        'date_embauche',
        'telephone',
        'adresse',
    ];

    protected $casts = [
        'date_embauche' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ✅ CORRIGÉ : Spécifier les bons noms de colonnes
    public function matieres(): BelongsToMany
    {
        return $this->belongsToMany(
            Matiere::class,
            'enseignant_matiere',          // Table pivot
            'enseignant_user_id',           // Clé étrangère de l'enseignant DANS la table pivot
            'matiere_id'                    // Clé étrangère de la matière DANS la table pivot
        )->withTimestamps();
    }

    public function classes()
    {
        return $this->hasMany(Classe::class, 'professeur_principal_id', 'user_id');
    }

    public function emploisTemps()
    {
        return $this->hasMany(EmploiTemps::class, 'enseignant_id', 'user_id');
    }
}