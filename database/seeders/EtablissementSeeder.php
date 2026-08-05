<?php
// database/seeders/EtablissementSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Etablissement;

class EtablissementSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🇨🇮 Création des établissements scolaires ivoiriens...');
        
        $etablissements = [
            // ============================================
            // LYCÉES PUBLICS
            // ============================================
            [
                'nom' => 'Lycée Classique d\'Abidjan',
                'type' => 'Lycée',
                'adresse' => 'Cocody, Boulevard Latrille, Abidjan',
                'telephone' => '+225 27 22 44 11 22',
                'email' => 'lycee.classique@education.ci',
                'code_etablissement' => 'LCA001',
                'academie' => 'DRENET Abidjan 1',
                'inspectorat' => 'IEP Abidjan 1',
                'is_active' => true,
            ],
            [
                'nom' => 'Lycée Scientifique de Yamoussoukro',
                'type' => 'Lycée',
                'adresse' => 'Boulevard de l\'Université, Yamoussoukro',
                'telephone' => '+225 27 30 64 12 34',
                'email' => 'lycee.scientifique@education.ci',
                'code_etablissement' => 'LSY002',
                'academie' => 'DRENET Yamoussoukro',
                'inspectorat' => 'IEP Yamoussoukro',
                'is_active' => true,
            ],
            [
                'nom' => 'Lycée Municipal de Bouaké',
                'type' => 'Lycée',
                'adresse' => 'Avenue de la Paix, Bouaké',
                'telephone' => '+225 27 31 63 98 76',
                'email' => 'lycee.bouake@education.ci',
                'code_etablissement' => 'LMB003',
                'academie' => 'DRENET Bouaké',
                'inspectorat' => 'IEP Bouaké',
                'is_active' => true,
            ],
            
            // ============================================
            // LYCÉES TECHNIQUES
            // ============================================
            [
                'nom' => 'Lycée Technique d\'Abidjan',
                'type' => 'Lycée',
                'adresse' => 'Zone industrielle de Koumassi, Abidjan',
                'telephone' => '+225 27 21 75 43 21',
                'email' => 'lycee.technique@education.ci',
                'code_etablissement' => 'LTA004',
                'academie' => 'DRENET Abidjan 4',
                'inspectorat' => 'IEP Abidjan 4',
                'is_active' => true,
            ],
            [
                'nom' => 'Lycée Professionnel de San-Pédro',
                'type' => 'Lycée',
                'adresse' => 'Quartier Balmer, San-Pédro',
                'telephone' => '+225 27 34 56 78 90',
                'email' => 'lpsp@education.ci',
                'code_etablissement' => 'LPS005',
                'academie' => 'DRENET San-Pédro',
                'inspectorat' => 'IEP San-Pédro',
                'is_active' => true,
            ],
            
            // ============================================
            // COLLÈGES PUBLICS
            // ============================================
            [
                'nom' => 'Collège Moderne de Cocody',
                'type' => 'Collège',
                'adresse' => 'Cocody Angré, Abidjan',
                'telephone' => '+225 27 22 48 95 67',
                'email' => 'college.cocody@education.ci',
                'code_etablissement' => 'CMC006',
                'academie' => 'DRENET Abidjan 2',
                'inspectorat' => 'IEP Abidjan 2',
                'is_active' => true,
            ],
            [
                'nom' => 'Collège de Daloa',
                'type' => 'Collège',
                'adresse' => 'Quartier Soleil, Daloa',
                'telephone' => '+225 27 32 78 45 12',
                'email' => 'college.daloa@education.ci',
                'code_etablissement' => 'CDA007',
                'academie' => 'DRENET Daloa',
                'inspectorat' => 'IEP Daloa',
                'is_active' => true,
            ],
            [
                'nom' => 'Collège de Korhogo',
                'type' => 'Collège',
                'adresse' => 'Quartier Koko, Korhogo',
                'telephone' => '+225 27 35 67 89 01',
                'email' => 'college.korhogo@education.ci',
                'code_etablissement' => 'CKO008',
                'academie' => 'DRENET Korhogo',
                'inspectorat' => 'IEP Korhogo',
                'is_active' => true,
            ],
            
            // ============================================
            // ÉCOLES PRIMAIRES PUBLIQUES
            // ============================================
            [
                'nom' => 'École Primaire Publique Cocody',
                'type' => 'Primaire',
                'adresse' => 'Cocody Danga, Abidjan',
                'telephone' => '+225 27 22 41 23 45',
                'email' => 'epp.cocody@education.ci',
                'code_etablissement' => 'EPC009',
                'academie' => 'DRENET Abidjan 1',
                'inspectorat' => 'IEP Abidjan 1',
                'is_active' => true,
            ],
            [
                'nom' => 'École Primaire Publique de Yopougon',
                'type' => 'Primaire',
                'adresse' => 'Yopougon SIDECI, Abidjan',
                'telephone' => '+225 27 23 45 67 89',
                'email' => 'epp.yopougon@education.ci',
                'code_etablissement' => 'EPY010',
                'academie' => 'DRENET Abidjan 3',
                'inspectorat' => 'IEP Abidjan 3',
                'is_active' => true,
            ],
            
            // ============================================
            // ÉTABLISSEMENTS PRIVÉS
            // ============================================
            [
                'nom' => 'Groupe Scolaire La Farandole',
                'type' => 'Primaire/Secondaire',
                'adresse' => 'Cocody Riviera 3, Abidjan',
                'telephone' => '+225 27 22 47 85 96',
                'email' => 'contact@farandole.ci',
                'code_etablissement' => 'GSP011',
                'academie' => 'DRENET Abidjan 2',
                'inspectorat' => 'IEP Abidjan 2',
                'is_active' => true,
            ],
            [
                'nom' => 'Collège International Jean Mermoz',
                'type' => 'Collège',
                'adresse' => 'Cocody Ambassador, Abidjan',
                'telephone' => '+225 27 22 48 57 68',
                'email' => 'info@mermoz.ci',
                'code_etablissement' => 'CIM012',
                'academie' => 'DRENET Abidjan 1',
                'inspectorat' => 'IEP Abidjan 1',
                'is_active' => true,
            ],
            [
                'nom' => 'École Internationale d\'Abidjan',
                'type' => 'Primaire/Secondaire',
                'adresse' => 'Riviera Golf, Abidjan',
                'telephone' => '+225 27 22 48 12 34',
                'email' => 'admissions@eia.ci',
                'code_etablissement' => 'EIA013',
                'academie' => 'DRENET Abidjan 2',
                'inspectorat' => 'IEP Abidjan 2',
                'is_active' => true,
            ],
            [
                'nom' => 'Groupe Scolaire Les Phalènes',
                'type' => 'Primaire/Secondaire',
                'adresse' => 'Marcory Zone 4, Abidjan',
                'telephone' => '+225 27 21 35 79 46',
                'email' => 'contact@phalenes.ci',
                'code_etablissement' => 'GSP014',
                'academie' => 'DRENET Abidjan 4',
                'inspectorat' => 'IEP Abidjan 4',
                'is_active' => true,
            ],
            
            // ============================================
            // ÉTABLISSEMENTS RÉGIONAUX
            // ============================================
            [
                'nom' => 'Lycée Municipal de Man',
                'type' => 'Lycée',
                'adresse' => 'Quartier Dioulabougou, Man',
                'telephone' => '+225 27 33 79 46 13',
                'email' => 'lycee.man@education.ci',
                'code_etablissement' => 'LMA015',
                'academie' => 'DRENET Man',
                'inspectorat' => 'IEP Man',
                'is_active' => true,
            ],
            [
                'nom' => 'Collège Moderne de Gagnoa',
                'type' => 'Collège',
                'adresse' => 'Quartier Belle Ville, Gagnoa',
                'telephone' => '+225 27 32 77 88 99',
                'email' => 'college.gagnoa@education.ci',
                'code_etablissement' => 'CGA016',
                'academie' => 'DRENET Gagnoa',
                'inspectorat' => 'IEP Gagnoa',
                'is_active' => true,
            ],
        ];

        $count = 0;
        foreach ($etablissements as $etab) {
            try {
                Etablissement::updateOrCreate(
                    ['code_etablissement' => $etab['code_etablissement']],
                    $etab
                );
                $count++;
                $this->command->info("{$etab['nom']} créé");
            } catch (\Exception $e) {
                $this->command->warn("Erreur sur {$etab['code_etablissement']}: " . $e->getMessage());
            }
        }

        // Statistiques
        $stats = [
            'total' => $count,
            'lycees' => Etablissement::where('type', 'Lycée')->count(),
            'colleges' => Etablissement::where('type', 'Collège')->count(),
            'primaires' => Etablissement::where('type', 'Primaire')->count(),
            'primaire_secondaire' => Etablissement::where('type', 'Primaire/Secondaire')->count(),
        ];

        $this->command->newLine();
        $this->command->info('╔════════════════════════════════════════════════════╗');
        $this->command->info('║   🇨🇮 ÉTABLISSEMENTS SCOLAIRES DE CÔTE D\'IVOIRE  ║');
        $this->command->info('╚════════════════════════════════════════════════════╝');
        $this->command->info("Total établissements: \033[32m{$stats['total']}\033[0m");
        
        $this->command->newLine();
        $this->command->info('RÉPARTITION PAR TYPE :');
        $this->command->table(
            ['Type', 'Nombre', 'Pourcentage'],
            [
                ['Lycées', $stats['lycees'], $stats['total'] > 0 ? round(($stats['lycees'] / $stats['total']) * 100, 1) . '%' : '0%'],
                ['Collèges', $stats['colleges'], $stats['total'] > 0 ? round(($stats['colleges'] / $stats['total']) * 100, 1) . '%' : '0%'],
                ['Primaires', $stats['primaires'], $stats['total'] > 0 ? round(($stats['primaires'] / $stats['total']) * 100, 1) . '%' : '0%'],
                ['Primaire/Secondaire', $stats['primaire_secondaire'], $stats['total'] > 0 ? round(($stats['primaire_secondaire'] / $stats['total']) * 100, 1) . '%' : '0%'],
            ]
        );

        // Liste des DRENET
        $this->command->newLine();
        $this->command->info('DIRECTIONS RÉGIONALES REPRÉSENTÉES :');
        $drenetList = Etablissement::select('academie')->distinct()->pluck('academie')->toArray();
        foreach ($drenetList as $d) {
            $this->command->line("   • {$d}");
        }

        $this->command->newLine();
        $this->command->info('Établissements créés avec succès !');
    }
}