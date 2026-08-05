<?php
// database/seeders/EleveSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Eleve;
use App\Models\Classe;
use App\Models\Etablissement;
use Faker\Factory as Faker;

class EleveSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🇨🇮 Création des élèves ivoiriens...');
        
        $faker = Faker::create('fr_FR');
        $classes = Classe::all();

        // Prénoms ivoiriens typiques
        $prenoms = [
            // Prénoms masculins
            'Kouadio', 'Konan', 'Koffi', 'N\'Guessan', 'Yao', 'Kra', 'Amani', 'Moussa', 'Ibrahim', 'Amadou',
            'Abdoulaye', 'Souleymane', 'Fousseyni', 'Lassina', 'Drissa', 'Zié', 'Arsène', 'Hervé', 'Michel',
            // Prénoms féminins
            'Aminata', 'Fatou', 'Mariam', 'Awa', 'Rokiatou', 'Salimata', 'Adjoua', 'Affoué', 'Amenan', 'Ahou',
            'Nadège', 'Josiane', 'Patricia', 'Mireille', 'Georgette', 'Thérèse', 'Marie'
        ];

        // Noms ivoiriens
        $noms = [
            'Kouassi', 'Koné', 'Traoré', 'Touré', 'Bamba', 'Coulibaly', 'Diaby', 'Ouattara', 'Soro', 'Yeo',
            'Tuo', 'Koffi', 'N\'Dri', 'Kassi', 'Aké', 'Loba', 'Diawara', 'Sangaré', 'Doumbia', 'Dembélé',
            'Camara', 'Sissoko', 'Kanté', 'Bakayoko', 'Gnépélé', 'Kpan', 'Yapi', 'Gohi', 'Vanié', 'Seka'
        ];

        // Villes de Côte d'Ivoire
        $villes = [
            'Abidjan', 'Bouaké', 'Daloa', 'Yamoussoukro', 'Korhogo', 'San-Pédro', 'Gagnoa', 'Man', 'Divo',
            'Anyama', 'Abengourou', 'Agboville', 'Grand-Bassam', 'Bingerville', 'Dabou', 'Ferkessédougou',
            'Soubré', 'Odienné', 'Bondoukou', 'Séguéla', 'Touba', 'Boundiali', 'Tengrela', 'Ouangolodougou',
            'Adzopé', 'Akoupe', 'Alepe', 'Bongouanou', 'Daoukro', 'M\'Bahiakro', 'Tanda', 'Agnibilekro'
        ];

        // Opérateurs téléphoniques ivoiriens
        $operateurs = ['07', '05', '01', '02', '03', '04', '06', '08']; // MTN, Moov, Orange etc.

        $totalEleves = 0;
        $totalParNiveau = [
            'Primaire' => 0,
            'Collège' => 0,
            'Lycée' => 0
        ];

        foreach ($classes as $classe) {
            // Nombre d'élèves variable selon le niveau
            $minEleves = $classe->niveau === 'Primaire' ? 20 : 25;
            $maxEleves = min($classe->capacite, $classe->niveau === 'Primaire' ? 30 : 40);
            
            $nbEleves = rand($minEleves, $maxEleves);
            $compteurClasse = 0;

            for ($i = 0; $i < $nbEleves; $i++) {
                // Calcul de l'âge selon le niveau
                $ageMin = $classe->niveau === 'Primaire' ? 6 : ($classe->niveau === 'Collège' ? 11 : 15);
                $ageMax = $classe->niveau === 'Primaire' ? 11 : ($classe->niveau === 'Collège' ? 15 : 19);
                
                $dateNaissance = now()->subYears(rand($ageMin, $ageMax))
                    ->subMonths(rand(0, 11))
                    ->subDays(rand(0, 28));

                $sexe = rand(0, 1) ? 'M' : 'F';
                $prenom = $faker->randomElement($prenoms);
                $nom = $faker->randomElement($noms);
                
                // Générer un matricule unique
                $matricule = 'CI' . date('Y') . str_pad($classe->id, 3, '0', STR_PAD_LEFT) . str_pad($i + 1, 3, '0', STR_PAD_LEFT);

                // Générer un numéro de téléphone ivoirien
                $telephone = $faker->randomElement($operateurs) . $faker->numberBetween(10000000, 99999999);

                // Nom du parent selon le sexe
                $nomParent = $sexe === 'M' 
                    ? "M. {$nom} {$prenom}" 
                    : "Mme {$nom} {$prenom}";

                Eleve::create([
                    'classe_id' => $classe->id,
                    'matricule' => $matricule,
                    'prenom' => $prenom,
                    'nom' => $nom,
                    'date_naissance' => $dateNaissance,
                    'lieu_naissance' => $faker->randomElement($villes),
                    'sexe' => $sexe,
                    'adresse' => $faker->randomElement(['Rue ', 'Avenue ', 'Boulevard ']) . $faker->numberBetween(1, 500) . ', ' . $faker->randomElement($villes),
                    'telephone_parent' => $telephone,
                    'nom_parent' => $nomParent,
                    'email_parent' => strtolower($prenom . '.' . $nom . '@' . $faker->randomElement(['gmail.com', 'yahoo.fr', 'orange.ci', 'mtn.ci'])),
                    'status' => $faker->randomElement(['actif', 'actif', 'actif', 'actif', 'redoublant']), // 80% actif, 20% redoublant
                    'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                    'updated_at' => now(),
                ]);

                $compteurClasse++;
                $totalParNiveau[$classe->niveau]++;
            }

            $totalEleves += $compteurClasse;
            $this->command->info("   ➜ Classe {$classe->nom}: \033[32m{$compteurClasse}\033[0m élèves créés");
        }

        // Statistiques finales
        $this->command->newLine();
        $this->command->info('╔════════════════════════════════════════════════════╗');
        $this->command->info('║   🇨🇮 ÉLÈVES DE CÔTE D\'IVOIRE                     ║');
        $this->command->info('╚════════════════════════════════════════════════════╝');
        $this->command->info("Total classes: \033[33m" . $classes->count() . "\033[0m");
        $this->command->info("Total élèves: \033[32m{$totalEleves}\033[0m");
        
        // Répartition par niveau
        $this->command->newLine();
        $this->command->info('RÉPARTITION PAR NIVEAU :');
        $this->command->table(
            ['Niveau', 'Effectif', 'Pourcentage'],
            [
                ['Primaire', $totalParNiveau['Primaire'], round(($totalParNiveau['Primaire'] / $totalEleves) * 100, 1) . '%'],
                ['Collège', $totalParNiveau['Collège'], round(($totalParNiveau['Collège'] / $totalEleves) * 100, 1) . '%'],
                ['Lycée', $totalParNiveau['Lycée'], round(($totalParNiveau['Lycée'] / $totalEleves) * 100, 1) . '%'],
            ]
        );

        // Répartition par sexe
        $filles = Eleve::where('sexe', 'F')->count();
        $garcons = Eleve::where('sexe', 'M')->count();
        
        $this->command->newLine();
        $this->command->info('RÉPARTITION PAR SEXE :');
        $this->command->table(
            ['Sexe', 'Effectif', 'Pourcentage'],
            [
                ['Filles', $filles, round(($filles / $totalEleves) * 100, 1) . '%'],
                ['Garçons', $garcons, round(($garcons / $totalEleves) * 100, 1) . '%'],
            ]
        );

        // Exemples d'élèves créés
        $this->command->newLine();
        $this->command->info('EXEMPLES D\'ÉLÈVES :');
        $exemples = Eleve::with('classe')->inRandomOrder()->take(5)->get();
        foreach ($exemples as $eleve) {
            $this->command->line("   • {$eleve->prenom} {$eleve->nom} - {$eleve->classe->nom} - Né(e) le {$eleve->date_naissance->format('d/m/Y')} à {$eleve->lieu_naissance}");
        }

        $this->command->newLine();
        $this->command->info('Élèves créés avec succès !');
    }
}