<?php
// database/seeders/EmploiTempsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmploiTemps;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\User;
use Carbon\Carbon;

class EmploiTempsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🇨🇮 Création des emplois du temps ivoiriens...');
        
        $classes = Classe::all();
        $matieres = Matiere::all();
        $enseignants = User::whereHas('role', fn($q) => $q->where('name', 'enseignant'))->get();

        if ($classes->isEmpty() || $matieres->isEmpty() || $enseignants->isEmpty()) {
            $this->command->warn('Classes, matières ou enseignants manquants. Veuillez d\'abord exécuter les seeders appropriés.');
            return;
        }

        // Jours de classe en Côte d'Ivoire (du lundi au samedi)
        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        
        // Créneaux horaires typiques dans les établissements ivoiriens
        $creneaux = [
            ['debut' => '08:00', 'fin' => '09:00', 'periode' => 'Matin'],
            ['debut' => '09:00', 'fin' => '10:00', 'periode' => 'Matin'],
            ['debut' => '10:00', 'fin' => '11:00', 'periode' => 'Matin'],
            ['debut' => '11:00', 'fin' => '12:00', 'periode' => 'Matin'],
            ['debut' => '12:00', 'fin' => '13:00', 'periode' => 'Pause déjeuner'],
            ['debut' => '14:00', 'fin' => '15:00', 'periode' => 'Après-midi'],
            ['debut' => '15:00', 'fin' => '16:00', 'periode' => 'Après-midi'],
            ['debut' => '16:00', 'fin' => '17:00', 'periode' => 'Après-midi'],
            ['debut' => '17:00', 'fin' => '18:00', 'periode' => 'Après-midi'],
        ];

        $totalCours = 0;
        $coursParNiveau = [
            'Primaire' => 0,
            'Collège' => 0,
            'Lycée' => 0
        ];

        foreach ($classes as $classe) {
            $this->command->info("Génération de l'emploi du temps pour {$classe->nom}...");
            
            $coursCrees = 0;
            
            // Déterminer les matières appropriées pour ce niveau
            $matieresClasse = $matieres->filter(function($matiere) use ($classe) {
                return $matiere->niveau === 'Tous' || $matiere->niveau === $classe->niveau;
            })->values();

            if ($matieresClasse->isEmpty()) {
                $this->command->warn("Aucune matière trouvée pour le niveau {$classe->niveau}");
                continue;
            }

            // Pour chaque jour de la semaine
            foreach ($jours as $jourIndex => $jour) {
                // Pas de cours le samedi après-midi dans certains établissements
                $maxCreneaux = ($jour === 'Samedi') ? 4 : count($creneaux) - 1; // Moins de cours le samedi
                
                // Nombre de cours variable selon le niveau
                $nbCoursJour = rand(
                    $classe->niveau === 'Primaire' ? 4 : 5,
                    $classe->niveau === 'Primaire' ? 5 : 7
                );

                for ($i = 0; $i < $nbCoursJour; $i++) {
                    // Sélectionner un créneau aléatoire
                    $creneauIndex = rand(0, $maxCreneaux - 1);
                    $creneau = $creneaux[$creneauIndex];
                    
                    // Éviter la pause déjeuner
                    if ($creneau['periode'] === 'Pause déjeuner') {
                        continue;
                    }

                    // Sélectionner une matière aléatoire
                    $matiere = $matieresClasse->random();
                    
                    // Sélectionner un enseignant pour cette matière
                    $enseignant = $enseignants->random();

                    // Générer un numéro de salle (selon le type d'établissement)
                    $salle = $this->genererSalle($classe->niveau, $matiere);

                    // Vérifier les conflits potentiels (simplifié pour le seeder)
                    $conflit = EmploiTemps::where('classe_id', $classe->id)
                        ->where('jour', $jour)
                        ->where('heure_debut', $creneau['debut'])
                        ->exists();

                    if (!$conflit) {
                        EmploiTemps::create([
                            'classe_id' => $classe->id,
                            'matiere_id' => $matiere->id,
                            'enseignant_id' => $enseignant->id,
                            'jour' => $jour,
                            'heure_debut' => $creneau['debut'],
                            'heure_fin' => $creneau['fin'],
                            'salle' => $salle,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $coursCrees++;
                        $coursParNiveau[$classe->niveau]++;
                    }
                }
            }

            $totalCours += $coursCrees;
            $this->command->info("{$coursCrees} cours créés pour {$classe->nom}");
        }

        // Statistiques finales
        $this->command->newLine();
        $this->command->info('╔════════════════════════════════════════════════════╗');
        $this->command->info('║   🇨🇮 EMPLOIS DU TEMPS - CÔTE D\'IVOIRE           ║');
        $this->command->info('╚════════════════════════════════════════════════════╝');
        $this->command->info("Total classes: \033[33m" . $classes->count() . "\033[0m");
        $this->command->info("Total cours: \033[32m{$totalCours}\033[0m");
        
        // Répartition par niveau
        $this->command->newLine();
        $this->command->info('RÉPARTITION PAR NIVEAU :');
        $this->command->table(
            ['Niveau', 'Cours', 'Pourcentage'],
            [
                ['Primaire', $coursParNiveau['Primaire'], round(($coursParNiveau['Primaire'] / $totalCours) * 100, 1) . '%'],
                ['Collège', $coursParNiveau['Collège'], round(($coursParNiveau['Collège'] / $totalCours) * 100, 1) . '%'],
                ['Lycée', $coursParNiveau['Lycée'], round(($coursParNiveau['Lycée'] / $totalCours) * 100, 1) . '%'],
            ]
        );

        // Exemples d'emplois du temps
        $this->command->newLine();
        $this->command->info('EXEMPLES D\'EMPLOIS DU TEMPS :');
        $exemples = EmploiTemps::with(['classe', 'matiere', 'enseignant'])
            ->inRandomOrder()
            ->take(5)
            ->get();

        foreach ($exemples as $cours) {
            $this->command->line("   • {$cours->classe->nom}: {$cours->matiere->nom} le {$cours->jour} à {$cours->heure_debut} (Salle {$cours->salle}) - {$cours->enseignant->name}");
        }

        $this->command->newLine();
        $this->command->info('Emplois du temps créés avec succès !');
    }

    /**
     * Génère un numéro de salle selon le niveau et la matière
     */
    private function genererSalle($niveau, $matiere): string
    {
        $prefixes = [
            'Primaire' => 'P',
            'Collège' => 'C',
            'Lycée' => 'L'
        ];

        $prefixe = $prefixes[$niveau] ?? 'S';
        
        // Salles spécialisées
        $specialites = [
            'Mathématiques' => 'MAT',
            'Physique-Chimie' => 'PC',
            'SVT' => 'SVT',
            'Informatique' => 'INFO',
            'EPS' => 'GYM',
            'Musique' => 'MUS',
        ];

        if (isset($specialites[$matiere->nom])) {
            return $specialites[$matiere->nom] . rand(1, 5);
        }

        return $prefixe . rand(1, 20);
    }

    /**
     * Affiche un exemple d'emploi du temps pour une classe
     */
    private function afficherEmploiDuTemps($classeId): void
    {
        $cours = EmploiTemps::where('classe_id', $classeId)
            ->with(['matiere', 'enseignant'])
            ->orderBy('jour')
            ->orderBy('heure_debut')
            ->get()
            ->groupBy('jour');

        $this->command->info("   Emploi du temps pour la classe:");
        
        foreach ($cours as $jour => $coursDuJour) {
            $this->command->line("   {$jour}:");
            foreach ($coursDuJour as $c) {
                $this->command->line("      {$c->heure_debut}-{$c->heure_fin}: {$c->matiere->nom} (Salle {$c->salle}) - {$c->enseignant->name}");
            }
        }
    }
}