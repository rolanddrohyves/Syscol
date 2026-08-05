<?php
// database/seeders/ConfigurationSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Configuration;
use Illuminate\Support\Facades\DB;

class ConfigurationSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🇨🇮 Configuration du système SYSCOL pour la Côte d\'Ivoire...');
        
        $configurations = [
            // ============================================
            // GÉNÉRAL - Paramètres de base
            // ============================================
            [
                'key' => 'app_name',
                'value' => 'SYSCOL CI',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Nom de l\'application - Système Scolaire Ivoirien',
                'is_public' => true,
            ],
            [
                'key' => 'app_url',
                'value' => env('APP_URL', 'http://localhost:8000'),
                'type' => 'string',
                'group' => 'general',
                'description' => 'URL de l\'application',
                'is_public' => true,
            ],
            [
                'key' => 'app_env',
                'value' => env('APP_ENV', 'local'),
                'type' => 'string',
                'group' => 'general',
                'description' => 'Environnement d\'exécution',
                'is_public' => false,
            ],
            [
                'key' => 'app_locale',
                'value' => 'fr',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Langue par défaut (français)',
                'is_public' => true,
            ],
            [
                'key' => 'app_timezone',
                'value' => 'Africa/Abidjan',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Fuseau horaire (GMT)',
                'is_public' => true,
            ],
            [
                'key' => 'app_debug',
                'value' => false,
                'type' => 'boolean',
                'group' => 'general',
                'description' => 'Mode debug (désactivé en production)',
                'is_public' => false,
            ],
            [
                'key' => 'app_logo',
                'value' => null,
                'type' => 'string',
                'group' => 'general',
                'description' => 'Logo de l\'application',
                'is_public' => true,
            ],
            [
                'key' => 'app_favicon',
                'value' => null,
                'type' => 'string',
                'group' => 'general',
                'description' => 'Favicon de l\'application',
                'is_public' => true,
            ],
            [
                'key' => 'app_phone_format',
                'value' => '+225 ## ## ## ##',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Format de téléphone ivoirien',
                'is_public' => true,
            ],
            [
                'key' => 'app_currency',
                'value' => 'FCFA',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Monnaie (Franc CFA)',
                'is_public' => true,
            ],
            
            // ============================================
            // AUTHENTIFICATION - Sécurité
            // ============================================
            [
                'key' => 'login_attempts',
                'value' => 5,
                'type' => 'integer',
                'group' => 'auth',
                'description' => 'Nombre de tentatives de connexion autorisées',
                'is_public' => false,
            ],
            [
                'key' => 'lockout_duration',
                'value' => 30,
                'type' => 'integer',
                'group' => 'auth',
                'description' => 'Durée de verrouillage après échec (minutes)',
                'is_public' => false,
            ],
            [
                'key' => 'password_min_length',
                'value' => 8,
                'type' => 'integer',
                'group' => 'auth',
                'description' => 'Longueur minimale du mot de passe',
                'is_public' => false,
            ],
            [
                'key' => 'password_require_uppercase',
                'value' => true,
                'type' => 'boolean',
                'group' => 'auth',
                'description' => 'Exiger une majuscule',
                'is_public' => false,
            ],
            [
                'key' => 'password_require_numbers',
                'value' => true,
                'type' => 'boolean',
                'group' => 'auth',
                'description' => 'Exiger un chiffre',
                'is_public' => false,
            ],
            [
                'key' => 'password_require_symbols',
                'value' => false,
                'type' => 'boolean',
                'group' => 'auth',
                'description' => 'Exiger un caractère spécial',
                'is_public' => false,
            ],
            [
                'key' => 'two_factor_auth',
                'value' => false,
                'type' => 'boolean',
                'group' => 'auth',
                'description' => 'Activer l\'authentification à deux facteurs',
                'is_public' => false,
            ],
            [
                'key' => 'session_lifetime',
                'value' => 120,
                'type' => 'integer',
                'group' => 'auth',
                'description' => 'Durée de session (minutes)',
                'is_public' => false,
            ],
            [
                'key' => 'email_verification',
                'value' => true,
                'type' => 'boolean',
                'group' => 'auth',
                'description' => 'Exiger la vérification par email',
                'is_public' => false,
            ],
            [
                'key' => 'password_expiry_days',
                'value' => 90,
                'type' => 'integer',
                'group' => 'auth',
                'description' => 'Expiration du mot de passe (jours)',
                'is_public' => false,
            ],
            
            // ============================================
            // ÉCOLE - Paramètres scolaires ivoiriens
            // ============================================
            [
                'key' => 'school_name',
                'value' => 'Ministère de l\'Éducation Nationale et de l\'Alphabétisation (MENA)',
                'type' => 'string',
                'group' => 'school',
                'description' => 'Nom de l\'institution',
                'is_public' => true,
            ],
            [
                'key' => 'school_acronym',
                'value' => 'MENA',
                'type' => 'string',
                'group' => 'school',
                'description' => 'Sigle du ministère',
                'is_public' => true,
            ],
            [
                'key' => 'school_system',
                'value' => 'Système éducatif ivoirien (6-3-3)',
                'type' => 'string',
                'group' => 'school',
                'description' => 'Type de système scolaire',
                'is_public' => true,
            ],
            [
                'key' => 'grading_system',
                'value' => '20',
                'type' => 'string',
                'group' => 'school',
                'description' => 'Système de notation (/20)',
                'is_public' => true,
            ],
            [
                'key' => 'passing_grade',
                'value' => 10,
                'type' => 'integer',
                'group' => 'school',
                'description' => 'Note de passage',
                'is_public' => true,
            ],
            [
                'key' => 'exam_periods',
                'value' => json_encode(['1er Trimestre', '2ème Trimestre', '3ème Trimestre']),
                'type' => 'json',
                'group' => 'school',
                'description' => 'Périodes d\'examens',
                'is_public' => true,
            ],
            [
                'key' => 'school_holidays',
                'value' => json_encode(['Noël', 'Pâques', 'Grandes vacances']),
                'type' => 'json',
                'group' => 'school',
                'description' => 'Périodes de vacances',
                'is_public' => true,
            ],
            
            // ============================================
            // COMMUNICATION - Paramètres de contact
            // ============================================
            [
                'key' => 'contact_email',
                'value' => 'contact@education.gouv.ci',
                'type' => 'string',
                'group' => 'communication',
                'description' => 'Email de contact du ministère',
                'is_public' => true,
            ],
            [
                'key' => 'contact_phone',
                'value' => '+225 20 22 33 44',
                'type' => 'string',
                'group' => 'communication',
                'description' => 'Téléphone du ministère',
                'is_public' => true,
            ],
            [
                'key' => 'contact_address',
                'value' => 'Plateau, Abidjan, Côte d\'Ivoire',
                'type' => 'string',
                'group' => 'communication',
                'description' => 'Adresse du ministère',
                'is_public' => true,
            ],
            [
                'key' => 'sms_enabled',
                'value' => true,
                'type' => 'boolean',
                'group' => 'communication',
                'description' => 'Activer les notifications SMS',
                'is_public' => false,
            ],
            [
                'key' => 'sms_provider',
                'value' => 'MTN CI',
                'type' => 'string',
                'group' => 'communication',
                'description' => 'Fournisseur SMS',
                'is_public' => false,
            ],
            
            // ============================================
            // MODULES - Fonctionnalités activées
            // ============================================
            [
                'key' => 'enabled_modules',
                'value' => json_encode([
                    'notes' => true,
                    'presences' => true,
                    'emplois_temps' => true,
                    'examens' => true,
                    'bulletins' => true,
                    'paiements' => true,
                    'communications' => true,
                    'api' => false,
                    'stats_avancees' => false
                ]),
                'type' => 'json',
                'group' => 'modules',
                'description' => 'Modules activés dans le système',
                'is_public' => true,
            ],
            
            // ============================================
            // SYSTÈME - Maintenance et sauvegardes
            // ============================================
            [
                'key' => 'last_backup',
                'value' => null,
                'type' => 'datetime',
                'group' => 'system',
                'description' => 'Date de la dernière sauvegarde',
                'is_public' => false,
            ],
            [
                'key' => 'maintenance_mode',
                'value' => false,
                'type' => 'boolean',
                'group' => 'system',
                'description' => 'Mode maintenance actif',
                'is_public' => false,
            ],
            [
                'key' => 'backup_frequency',
                'value' => 'daily',
                'type' => 'string',
                'group' => 'system',
                'description' => 'Fréquence des sauvegardes',
                'is_public' => false,
            ],
            [
                'key' => 'backup_retention_days',
                'value' => 30,
                'type' => 'integer',
                'group' => 'system',
                'description' => 'Durée de conservation des sauvegardes (jours)',
                'is_public' => false,
            ],
            [
                'key' => 'log_retention_days',
                'value' => 90,
                'type' => 'integer',
                'group' => 'system',
                'description' => 'Durée de conservation des logs (jours)',
                'is_public' => false,
            ],
            
            // ============================================
            // FINANCES - Paramètres financiers
            // ============================================
            [
                'key' => 'payment_methods',
                'value' => json_encode(['Espèces', 'Mobile Money (MTN)', 'Mobile Money (Moov)', 'Virement bancaire']),
                'type' => 'json',
                'group' => 'finance',
                'description' => 'Méthodes de paiement acceptées',
                'is_public' => true,
            ],
            [
                'key' => 'school_fees_currency',
                'value' => 'FCFA',
                'type' => 'string',
                'group' => 'finance',
                'description' => 'Devise pour les frais scolaires',
                'is_public' => true,
            ],
            [
                'key' => 'scholarship_enabled',
                'value' => true,
                'type' => 'boolean',
                'group' => 'finance',
                'description' => 'Activer la gestion des bourses',
                'is_public' => true,
            ],
            [
                'key' => 'late_payment_penalty',
                'value' => 5,
                'type' => 'integer',
                'group' => 'finance',
                'description' => 'Pénalité de retard (%)',
                'is_public' => true,
            ],
        ];

        $count = 0;
        foreach ($configurations as $config) {
            Configuration::updateOrCreate(
                ['key' => $config['key']],
                $config
            );
            $count++;
        }

        //CORRECTION : Utiliser des backticks pour le mot réservé 'group'
        $groups = DB::select("SELECT `group`, count(*) as total FROM `configurations` GROUP BY `group`");
        
        $this->command->newLine();
        $this->command->info('╔════════════════════════════════════════════════════╗');
        $this->command->info('║   🇨🇮 SYSCOL CI - CONFIGURATION IVOIRIENNE        ║');
        $this->command->info('╚════════════════════════════════════════════════════╝');
        $this->command->info("   {$count} configurations créées/mises à jour");
        $this->command->info("   Fuseau horaire: Africa/Abidjan (GMT)");
        $this->command->info("   Monnaie: Franc CFA (FCFA)");
        $this->command->info("   Format téléphone: +225 ## ## ## ##");
        
        if (!empty($groups)) {
            $this->command->newLine();
            $this->command->info('RÉPARTITION PAR GROUPE :');
            
            $data = [];
            foreach ($groups as $g) {
                $data[] = [$g->group, $g->total];
            }
            
            $this->command->table(['Groupe', 'Nombre'], $data);
        } else {
            $this->command->warn('Aucune configuration trouvée pour les statistiques');
        }
        
        $this->command->newLine();
        $this->command->info('Configuration système terminée avec succès !');
    }
}