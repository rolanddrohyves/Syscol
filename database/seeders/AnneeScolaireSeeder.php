<?php
// database/seeders/AnneeScolaireSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AnneeScolaire;
use Carbon\Carbon;

class AnneeScolaireSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🇨🇮 Création des années scolaires ivoiriennes...');
        
        // Calendrier scolaire ivoirien typique
        // La rentrée a lieu en septembre, les cours en octobre, fin en juillet
        $annees = [
            [
                'libelle' => '2025-2026',
                'date_debut' => '2025-09-15',
                'date_fin' => '2026-07-15',
                'is_current' => true,
                'description' => 'Année scolaire 2025-2026 en cours'
            ],
        ];

        foreach ($annees as $annee) {
            AnneeScolaire::updateOrCreate(
                ['libelle' => $annee['libelle']],
                [
                    'date_debut' => $annee['date_debut'],
                    'date_fin' => $annee['date_fin'],
                    'is_current' => $annee['is_current'],
                ]
            );
            
            $status = $annee['is_current'] ? 'En cours' : 'Créée';
            $this->command->info("   {$status} Année {$annee['libelle']} créée");
        }

        // Afficher l'année en cours
        $currentYear = AnneeScolaire::where('is_current', true)->first();
        
        $this->command->newLine();
        $this->command->info('╔════════════════════════════════════════════════╗');
        $this->command->info('║   🇨🇮 ANNÉES SCOLAIRES DE CÔTE D\'IVOIRE       ║');
        $this->command->info('╚════════════════════════════════════════════════╝');
        
        if ($currentYear) {
            $this->command->info("Année en cours : \033[32m{$currentYear->libelle}\033[0m");
            $this->command->info("Début: " . Carbon::parse($currentYear->date_debut)->format('d/m/Y'));
            $this->command->info("Fin: " . Carbon::parse($currentYear->date_fin)->format('d/m/Y'));
        }
        
        $this->command->newLine();
        $this->command->info('Années scolaires créées avec succès !');
        
        // Tableau récapitulatif
        $this->command->table(
            ['Année', 'Début', 'Fin', 'Statut'],
            AnneeScolaire::all()->map(fn($a) => [
                $a->libelle,
                Carbon::parse($a->date_debut)->format('d/m/Y'),
                Carbon::parse($a->date_fin)->format('d/m/Y'),
                $a->is_current ? 'En cours' : 'Passée',
            ])->toArray()
        );
    }
}