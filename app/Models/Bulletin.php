<?php
// app/Models/Bulletin.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bulletin extends Model
{
    use HasFactory;

    protected $fillable = [
        'eleve_id',
        'trimestre_id',
        'annee_scolaire_id',
        'moyenne_generale',
        'rang',
        'appreciation',
        'statut',
        'pdf_path'
    ];

    protected $casts = [
        'moyenne_generale' => 'decimal:2',
    ];

    // Relation avec l'élève
    public function eleve()
    {
        return $this->belongsTo(Eleve::class);
    }

    // Relation avec le trimestre
    public function trimestre()
    {
        return $this->belongsTo(Trimestre::class);
    }

    // Relation avec l'année scolaire
    public function anneeScolaire()
    {
        return $this->belongsTo(AnneeScolaire::class);
    }

    // Relation avec les notes du bulletin
    public function notes()
    {
        return $this->hasMany(BulletinNote::class);
    }

    // Scope pour les bulletins d'un élève
    public function scopeForEleve($query, $eleveId)
    {
        return $query->where('eleve_id', $eleveId);
    }

    // Scope pour les bulletins d'un trimestre
    public function scopeForTrimestre($query, $trimestreId)
    {
        return $query->where('trimestre_id', $trimestreId);
    }
}