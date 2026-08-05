<?php
// database/seeders/MatiereSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Matiere;

class MatiereSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🇨🇮 Création des matières du programme scolaire ivoirien...');
        
        $matieres = [
            // ============================================
            // ENSEIGNEMENT PRIMAIRE
            // ============================================
            [
                'nom' => 'Français (Lecture, Écriture)',
                'code' => 'FR-P',
                'coefficient' => 5,
                'niveau' => 'Primaire',
            ],
            [
                'nom' => 'Mathématiques',
                'code' => 'MATH-P',
                'coefficient' => 5,
                'niveau' => 'Primaire',
            ],
            [
                'nom' => 'Éveil du Milieu',
                'code' => 'EM',
                'coefficient' => 2,
                'niveau' => 'Primaire',
            ],
            [
                'nom' => 'Histoire-Géographie',
                'code' => 'HG-P',
                'coefficient' => 2,
                'niveau' => 'Primaire',
            ],
            [
                'nom' => 'Instruction Civique et Morale',
                'code' => 'ICM',
                'coefficient' => 1,
                'niveau' => 'Primaire',
            ],
            [
                'nom' => 'Éducation Physique et Sportive',
                'code' => 'EPS-P',
                'coefficient' => 1,
                'niveau' => 'Primaire',
            ],
            [
                'nom' => 'Activités d\'Éveil',
                'code' => 'AE',
                'coefficient' => 1,
                'niveau' => 'Primaire',
            ],
            
            // ============================================
            // ENSEIGNEMENT SECONDAIRE - TRONC COMMUN
            // ============================================
            [
                'nom' => 'Français',
                'code' => 'FR',
                'coefficient' => 4,
                'niveau' => 'Tous',
            ],
            [
                'nom' => 'Mathématiques',
                'code' => 'MATH',
                'coefficient' => 4,
                'niveau' => 'Tous',
            ],
            [
                'nom' => 'Anglais',
                'code' => 'ANG',
                'coefficient' => 3,
                'niveau' => 'Tous',
            ],
            [
                'nom' => 'Histoire-Géographie',
                'code' => 'HG',
                'coefficient' => 3,
                'niveau' => 'Tous',
            ],
            [
                'nom' => 'Physique-Chimie',
                'code' => 'PC',
                'coefficient' => 3,
                'niveau' => 'Tous',
            ],
            [
                'nom' => 'Sciences de la Vie et de la Terre',
                'code' => 'SVT',
                'coefficient' => 3,
                'niveau' => 'Tous',
            ],
            [
                'nom' => 'Éducation Physique et Sportive',
                'code' => 'EPS',
                'coefficient' => 2,
                'niveau' => 'Tous',
            ],
            
            // ============================================
            // COLLÈGE
            // ============================================
            [
                'nom' => 'Technologie',
                'code' => 'TECH',
                'coefficient' => 2,
                'niveau' => 'Collège',
            ],
            [
                'nom' => 'Arts Plastiques',
                'code' => 'ARTS',
                'coefficient' => 1,
                'niveau' => 'Collège',
            ],
            [
                'nom' => 'Éducation Musicale',
                'code' => 'MUS',
                'coefficient' => 1,
                'niveau' => 'Collège',
            ],
            [
                'nom' => 'Langue Vivante 2 (Espagnol)',
                'code' => 'ESP',
                'coefficient' => 2,
                'niveau' => 'Collège',
            ],
            [
                'nom' => 'Langue Vivante 2 (Allemand)',
                'code' => 'ALL',
                'coefficient' => 2,
                'niveau' => 'Collège',
            ],
            [
                'nom' => 'Langue Nationale (Baoulé)',
                'code' => 'BAO',
                'coefficient' => 1,
                'niveau' => 'Collège',
            ],
            [
                'nom' => 'Langue Nationale (Dioula)',
                'code' => 'DIO',
                'coefficient' => 1,
                'niveau' => 'Collège',
            ],
            [
                'nom' => 'Langue Nationale (Bété)',
                'code' => 'BET',
                'coefficient' => 1,
                'niveau' => 'Collège',
            ],
            
            // ============================================
            // LYCÉE - SÉRIES GÉNÉRALES
            // ============================================
            [
                'nom' => 'Philosophie',
                'code' => 'PHILO',
                'coefficient' => 4,
                'niveau' => 'Lycée',
            ],
            
            // SÉRIE A (Littéraire)
            [
                'nom' => 'Littérature Française',
                'code' => 'LIT',
                'coefficient' => 4,
                'niveau' => 'Lycée',
            ],
            [
                'nom' => 'Langues Anciennes (Latin)',
                'code' => 'LAT',
                'coefficient' => 2,
                'niveau' => 'Lycée',
            ],
            
            // SÉRIE C (Mathématiques et Sciences physiques)
            [
                'nom' => 'Mathématiques',
                'code' => 'MATH-C',
                'coefficient' => 5,
                'niveau' => 'Lycée',
            ],
            [
                'nom' => 'Physique-Chimie',
                'code' => 'PC-C',
                'coefficient' => 5,
                'niveau' => 'Lycée',
            ],
            [
                'nom' => 'Sciences de la Vie et de la Terre',
                'code' => 'SVT-C',
                'coefficient' => 3,
                'niveau' => 'Lycée',
            ],
            
            // SÉRIE D (Sciences biologiques)
            [
                'nom' => 'Mathématiques',
                'code' => 'MATH-D',
                'coefficient' => 4,
                'niveau' => 'Lycée',
            ],
            [
                'nom' => 'Physique-Chimie',
                'code' => 'PC-D',
                'coefficient' => 4,
                'niveau' => 'Lycée',
            ],
            [
                'nom' => 'Sciences de la Vie et de la Terre',
                'code' => 'SVT-D',
                'coefficient' => 5,
                'niveau' => 'Lycée',
            ],
            
            // SÉRIE E (Mathématiques et Techniques)
            [
                'nom' => 'Mathématiques',
                'code' => 'MATH-E',
                'coefficient' => 5,
                'niveau' => 'Lycée',
            ],
            [
                'nom' => 'Sciences Industrielles',
                'code' => 'SI',
                'coefficient' => 5,
                'niveau' => 'Lycée',
            ],
            [
                'nom' => 'Construction Mécanique',
                'code' => 'CM',
                'coefficient' => 3,
                'niveau' => 'Lycée',
            ],
            
            // SÉRIE F (Industrielle)
            [
                'nom' => 'Électronique',
                'code' => 'ELEC',
                'coefficient' => 4,
                'niveau' => 'Lycée',
            ],
            [
                'nom' => 'Électrotechnique',
                'code' => 'ELT',
                'coefficient' => 4,
                'niveau' => 'Lycée',
            ],
            
            // SÉRIE G (Commerciale et comptable)
            [
                'nom' => 'Économie',
                'code' => 'ECO',
                'coefficient' => 4,
                'niveau' => 'Lycée',
            ],
            [
                'nom' => 'Comptabilité',
                'code' => 'COMPTA',
                'coefficient' => 4,
                'niveau' => 'Lycée',
            ],
            [
                'nom' => 'Gestion',
                'code' => 'GEST',
                'coefficient' => 3,
                'niveau' => 'Lycée',
            ],
            
            // SÉRIE H (Artistique)
            [
                'nom' => 'Arts Plastiques',
                'code' => 'ARTS-H',
                'coefficient' => 4,
                'niveau' => 'Lycée',
            ],
            [
                'nom' => 'Histoire de l\'Art',
                'code' => 'HA',
                'coefficient' => 3,
                'niveau' => 'Lycée',
            ],
            
            // INFORMATIQUE ET OPTIONS
            [
                'nom' => 'Informatique',
                'code' => 'INFO',
                'coefficient' => 2,
                'niveau' => 'Tous',
            ],
            [
                'nom' => 'Arabe',
                'code' => 'AR',
                'coefficient' => 2,
                'niveau' => 'Tous',
            ],
        ];

        $count = 0;
        foreach ($matieres as $matiere) {
            try {
                Matiere::updateOrCreate(
                    ['code' => $matiere['code']],
                    [
                        'nom' => $matiere['nom'],
                        'coefficient' => $matiere['coefficient'],
                        'niveau' => $matiere['niveau'],
                    ]
                );
                $count++;
                $this->command->info("{$matiere['code']} - {$matiere['nom']}");
            } catch (\Exception $e) {
                $this->command->warn("Erreur sur {$matiere['code']}: " . $e->getMessage());
            }
        }

        // Statistiques
        $stats = [
            'total' => $count,
            'primaire' => Matiere::where('niveau', 'Primaire')->count(),
            'college' => Matiere::where('niveau', 'Collège')->count(),
            'lycee' => Matiere::where('niveau', 'Lycée')->count(),
            'tous' => Matiere::where('niveau', 'Tous')->count(),
        ];

        $this->command->newLine();
        $this->command->info('╔════════════════════════════════════════════════════╗');
        $this->command->info('║   🇨🇮 PROGRAMME SCOLAIRE IVOIRIEN                 ║');
        $this->command->info('╚════════════════════════════════════════════════════╝');
        $this->command->info("   Total matières: \033[32m{$stats['total']}\033[0m");
        
        $this->command->newLine();
        $this->command->info('RÉPARTITION PAR NIVEAU :');
        $this->command->table(
            ['Niveau', 'Nombre', 'Pourcentage'],
            [
                ['Primaire', $stats['primaire'], $stats['total'] > 0 ? round(($stats['primaire'] / $stats['total']) * 100, 1) . '%' : '0%'],
                ['Collège', $stats['college'], $stats['total'] > 0 ? round(($stats['college'] / $stats['total']) * 100, 1) . '%' : '0%'],
                ['Lycée', $stats['lycee'], $stats['total'] > 0 ? round(($stats['lycee'] / $stats['total']) * 100, 1) . '%' : '0%'],
                ['Tous', $stats['tous'], $stats['total'] > 0 ? round(($stats['tous'] / $stats['total']) * 100, 1) . '%' : '0%'],
            ]
        );

        $this->command->newLine();
        $this->command->info('Matières du programme ivoirien créées avec succès !');
    }
}