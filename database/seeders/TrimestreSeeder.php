<?php
// database/seeders/TrimestreSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trimestre;
use App\Models\AnneeScolaire;
use Carbon\Carbon;

class TrimestreSeeder extends Seeder
{
    public function run(): void
    {
        $anneesScolaires = AnneeScolaire::all();

        if ($anneesScolaires->isEmpty()) {
            $this->command->warn('Aucune année scolaire trouvée. Créez d\'abord des années scolaires.');
            return;
        }

        $this->command->info('Création des trimestres...');

        foreach ($anneesScolaires as $annee) {
            // Vérifier si des trimestres existent déjà pour cette année
            $existingCount = Trimestre::where('annee_scolaire_id', $annee->id)->count();
            
            if ($existingCount > 0) {
                $this->command->warn("Des trimestres existent déjà pour l'année {$annee->libelle} ({$existingCount} trouvés)");
                continue;
            }

            // Calculer les dates approximatives des trimestres
            $debutAnnee = Carbon::parse($annee->date_debut);
            $finAnnee = Carbon::parse($annee->date_fin);
            
            // Durée totale en jours
            $dureeTotale = $debutAnnee->diffInDays($finAnnee);
            $dureeTrimestre = floor($dureeTotale / 3);

            $trimestres = [
                [
                    'numero' => 1,
                    'libelle' => 'Trimestre 1',
                    'date_debut' => $debutAnnee->copy(),
                    'date_fin' => $debutAnnee->copy()->addDays($dureeTrimestre),
                ],
                [
                    'numero' => 2,
                    'libelle' => 'Trimestre 2',
                    'date_debut' => $debutAnnee->copy()->addDays($dureeTrimestre + 1),
                    'date_fin' => $debutAnnee->copy()->addDays($dureeTrimestre * 2),
                ],
                [
                    'numero' => 3,
                    'libelle' => 'Trimestre 3',
                    'date_debut' => $debutAnnee->copy()->addDays($dureeTrimestre * 2 + 1),
                    'date_fin' => $finAnnee,
                ],
            ];

            foreach ($trimestres as $trimestre) {
                Trimestre::create([
                    'libelle' => $trimestre['libelle'],
                    'numero' => $trimestre['numero'],
                    'date_debut' => $trimestre['date_debut'],
                    'date_fin' => $trimestre['date_fin'],
                    'annee_scolaire_id' => $annee->id,
                    'is_current' => $annee->is_current && $trimestre['numero'] === 1, // Premier trimestre courant si année en cours
                ]);
                
                $this->command->line("{$trimestre['libelle']} créé");
            }

            $this->command->info("Trimestres créés pour l'année {$annee->libelle}");
        }

        $this->command->newLine();
        $this->command->info('Tous les trimestres ont été créés avec succès !');
        
        // Afficher un résumé
        $total = Trimestre::count();
        $this->command->info("Total des trimestres en base : {$total}");
    }
}