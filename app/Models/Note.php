<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = [
        'eleve_id',
        'matiere_id',
        'enseignant_id',
        'trimestre_id',
        'note',
        'note_max',
        'appreciation',
        'date_evaluation',
    ];

    protected $casts = [
        'date_evaluation' => 'date',
    ];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class);
    }

    public function matiere()
    {
        return $this->belongsTo(Matiere::class);
    }

    public function enseignant()
    {
        return $this->belongsTo(User::class, 'enseignant_id');
    }

    public function trimestre()
    {
        return $this->belongsTo(AnneeScolaire::class, 'trimestre_id');
    }

    public function getNoteSur20Attribute(): float
    {
        return round(($this->note * 20) / $this->note_max, 2);
    }

    public function getAppreciationAutoAttribute(): string
    {
        $pourcentage = ($this->note / $this->note_max) * 100;
        
        return match(true) {
            $pourcentage >= 90 => 'Excellent',
            $pourcentage >= 75 => 'Très bien',
            $pourcentage >= 60 => 'Bien',
            $pourcentage >= 50 => 'Passable',
            $pourcentage >= 40 => 'Insuffisant',
            default => 'Faible',
        };
    }
}