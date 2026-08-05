<?php
// app/Http/Controllers/Admin/ConfigurationController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\Etablissement;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail; // 👈 AJOUTÉ pour testMail

class ConfigurationController extends Controller
{
    /**
     * Affiche le tableau de bord de configuration
     */
    public function index()
    {
        $configs = Configuration::all()->groupBy('group');
        
        $stats = [
            'total_configs' => Configuration::count(),
            'groups' => Configuration::select('group')->distinct()->count(),
            'cache_size' => $this->getCacheSize(),
            'last_backup' => $this->getLastBackup(),
        ];
        
        return view('admin.configurations.index', compact('configs', 'stats'));
    }

    /**
     * Affiche le formulaire de configuration générale
     */
    public function general()
    {
        $configs = Configuration::where('group', 'general')->get()->keyBy('key');
        
        $timezones = timezone_identifiers_list();
        $locales = ['fr' => 'Français', 'en' => 'English', 'es' => 'Español', 'ar' => 'العربية'];
        
        return view('admin.configurations.general', compact('configs', 'timezones', 'locales'));
    }

    /**
     * Met à jour la configuration générale (AVEC GESTION DU LOGO)
     */
    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url',
            'app_env' => 'required|in:local,development,staging,production',
            'app_locale' => 'required|string|size:2',
            'app_timezone' => 'required|string',
            'app_debug' => 'boolean',
            'maintenance_mode' => 'boolean',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // 👈 AJOUTÉ
            'remove_logo' => 'boolean', // 👈 AJOUTÉ
        ]);

        // 👇 GESTION DU LOGO
        if ($request->hasFile('app_logo')) {
            // Supprimer l'ancien logo
            $oldLogo = Configuration::get('app_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            
            // Upload du nouveau logo
            $path = $request->file('app_logo')->store('logos', 'public');
            Configuration::set('app_logo', $path, 'general', 'string');
        }

        // 👇 SUPPRESSION DU LOGO
        if ($request->boolean('remove_logo')) {
            $oldLogo = Configuration::get('app_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            Configuration::set('app_logo', null, 'general', 'string');
        }

        // Mise à jour des autres configurations
        foreach ($validated as $key => $value) {
            if (!in_array($key, ['app_logo', 'remove_logo'])) {
                Configuration::set($key, $value, 'general', 
                    is_bool($value) ? 'boolean' : 'string'
                );
            }
        }

        // Mettre à jour le fichier .env
        $this->updateEnvFile($validated);

        // Vider le cache
        Cache::flush();

        return redirect()->route('admin.configurations.general')
            ->with('success', 'Configuration générale mise à jour avec succès.');
    }

    /**
     * Configuration de l'authentification
     */
    public function auth()
    {
        $configs = Configuration::where('group', 'auth')->get()->keyBy('key');
        
        return view('admin.configurations.auth', compact('configs'));
    }

    /**
     * Met à jour la configuration d'authentification
     */
    public function updateAuth(Request $request)
    {
        $validated = $request->validate([
            'login_attempts' => 'integer|min:1|max:10',
            'lockout_duration' => 'integer|min:1|max:60',
            'password_min_length' => 'integer|min:6|max:20',
            'password_require_uppercase' => 'boolean',
            'password_require_numbers' => 'boolean',
            'password_require_symbols' => 'boolean',
            'two_factor_auth' => 'boolean',
            'session_lifetime' => 'integer|min:60|max:10080',
            'email_verification' => 'boolean',
        ]);

        foreach ($validated as $key => $value) {
            Configuration::set($key, $value, 'auth', 
                is_bool($value) ? 'boolean' : 'integer'
            );
        }

        return redirect()->route('admin.configurations.auth')
            ->with('success', 'Configuration d\'authentification mise à jour.');
    }

    /**
     * Configuration des modules
     */
    public function modules()
    {
        $modules = [
            'notes' => ['name' => 'Gestion des notes', 'enabled' => true],
            'presences' => ['name' => 'Gestion des présences', 'enabled' => true],
            'emplois_temps' => ['name' => 'Emplois du temps', 'enabled' => true],
            'examens' => ['name' => 'Examens', 'enabled' => true],
            'bulletins' => ['name' => 'Bulletins', 'enabled' => true],
            'paiements' => ['name' => 'Paiements', 'enabled' => true],
            'communications' => ['name' => 'Communications', 'enabled' => false],
            'api' => ['name' => 'API externe', 'enabled' => false],
        ];
        
        return view('admin.configurations.modules', compact('modules'));
    }

    /**
     * Met à jour la configuration des modules
     */
    public function updateModules(Request $request)
    {
        $modules = $request->input('modules', []);
        
        Configuration::set('enabled_modules', json_encode($modules), 'modules', 'json');
        
        return redirect()->route('admin.configurations.modules')
            ->with('success', 'Configuration des modules mise à jour.');
    }

    /**
     * Configuration des emails
     */
    public function mail()
    {
        $configs = [
            'mail_mailer' => env('MAIL_MAILER', 'smtp'),
            'mail_host' => env('MAIL_HOST', ''),
            'mail_port' => env('MAIL_PORT', '587'),
            'mail_username' => env('MAIL_USERNAME', ''),
            'mail_password' => env('MAIL_PASSWORD', ''),
            'mail_encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'mail_from_address' => env('MAIL_FROM_ADDRESS', ''),
            'mail_from_name' => env('MAIL_FROM_NAME', ''),
        ];
        
        return view('admin.configurations.mail', compact('configs'));
    }

    /**
     * Met à jour la configuration email
     */
    public function updateMail(Request $request)
    {
        $validated = $request->validate([
            'mail_mailer' => 'required|in:smtp,sendmail,mailgun,ses,postmark,log',
            'mail_host' => 'nullable|string',
            'mail_port' => 'nullable|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|in:tls,ssl,null',
            'mail_from_address' => 'nullable|email',
            'mail_from_name' => 'nullable|string',
        ]);

        $envContent = File::get(base_path('.env'));
        
        foreach ($validated as $key => $value) {
            $envKey = strtoupper($key);
            $envContent = preg_replace(
                "/^{$envKey}=.*/m",
                "{$envKey}={$value}",
                $envContent
            );
        }
        
        File::put(base_path('.env'), $envContent);
        
        return redirect()->route('admin.configurations.mail')
            ->with('success', 'Configuration email mise à jour.');
    }

    /**
     * Test de configuration email (AJOUTÉ)
     */
    public function testMail(Request $request)
    {
        try {
            $to = $request->input('to', auth()->user()->email);
            
            Mail::raw('Ceci est un email de test de configuration SMTP.', function ($message) use ($to) {
                $message->to($to)
                        ->subject('Test de configuration SYSCOL');
            });
            
            return response()->json(['success' => true, 'message' => 'Email envoyé avec succès']);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Gestion de la maintenance
     */
    public function maintenance()
    {
        $isDown = file_exists(storage_path('framework/down'));
        
        return view('admin.configurations.maintenance', compact('isDown'));
    }

    /**
     * Active le mode maintenance
     */
    public function enableMaintenance(Request $request)
    {
        $secret = $request->input('secret', 'syscol-maintenance');
        $retry = $request->input('retry', 60);
        
        Artisan::call('down', [
            '--secret' => $secret,
            '--retry' => $retry,
            '--render' => 'errors.maintenance',
        ]);
        
        return redirect()->route('admin.configurations.maintenance')
            ->with('success', 'Mode maintenance activé. Secret : ' . $secret);
    }

    /**
     * Désactive le mode maintenance
     */
    public function disableMaintenance()
    {
        Artisan::call('up');
        
        return redirect()->route('admin.configurations.maintenance')
            ->with('success', 'Mode maintenance désactivé.');
    }

    /**
     * Nettoyage du cache
     */
    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        
        return redirect()->route('admin.configurations.index')
            ->with('success', 'Tous les caches ont été nettoyés.');
    }

    /**
     * Optimisation de l'application
     */
    public function optimize()
    {
        Artisan::call('optimize');
        
        return redirect()->route('admin.configurations.index')
            ->with('success', 'Application optimisée avec succès.');
    }

    /**
     * Sauvegarde de la base de données
     */
    public function backup()
    {
        try {
            $filename = 'backup-' . now()->format('Y-m-d-H-i-s') . '.sql';
            $command = sprintf(
                'mysqldump -u%s -p%s %s > %s',
                env('DB_USERNAME'),
                env('DB_PASSWORD'),
                env('DB_DATABASE'),
                storage_path('app/backups/' . $filename)
            );
            
            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }
            
            exec($command);
            
            Configuration::set('last_backup', now(), 'system', 'datetime');
            
            return redirect()->route('admin.configurations.index')
                ->with('success', 'Sauvegarde effectuée avec succès.');
                
        } catch (\Exception $e) {
            return redirect()->route('admin.configurations.index')
                ->with('error', 'Erreur lors de la sauvegarde : ' . $e->getMessage());
        }
    }

    /**
     * Liste des sauvegardes
     */
    public function backups()
    {
        $backups = [];
        $backupPath = storage_path('app/backups');
        
        if (file_exists($backupPath)) {
            $files = File::files($backupPath);
            foreach ($files as $file) {
                $backups[] = [
                    'name' => $file->getFilename(),
                    'size' => $this->formatBytes($file->getSize()),
                    'date' => date('d/m/Y H:i:s', $file->getMTime()),
                ];
            }
        }
        
        return view('admin.configurations.backups', compact('backups'));
    }

    /**
     * Télécharge une sauvegarde
     */
    public function downloadBackup($filename)
    {
        $path = storage_path('app/backups/' . $filename);
        
        if (!file_exists($path)) {
            abort(404);
        }
        
        return response()->download($path);
    }

    /**
     * Supprime une sauvegarde
     */
    public function deleteBackup($filename)
    {
        $path = storage_path('app/backups/' . $filename);
        
        if (file_exists($path)) {
            File::delete($path);
        }
        
        return redirect()->route('admin.configurations.backups')
            ->with('success', 'Sauvegarde supprimée.');
    }

    /**
     * Métriques système
     */
    public function system()
    {
        $metrics = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database' => DB::connection()->getDatabaseName(),
            'database_size' => $this->getDatabaseSize(),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'disk_free' => $this->formatBytes(disk_free_space(base_path())),
            'disk_total' => $this->formatBytes(disk_total_space(base_path())),
            'disk_usage' => round((disk_total_space(base_path()) - disk_free_space(base_path())) / disk_total_space(base_path()) * 100, 1),
        ];
        
        return view('admin.configurations.system', compact('metrics'));
    }

    /**
     * Met à jour le fichier .env
     */
    private function updateEnvFile($data)
    {
        $envPath = base_path('.env');
        $envContent = File::get($envPath);
        
        foreach ($data as $key => $value) {
            $envKey = strtoupper($key);
            $envValue = is_bool($value) ? ($value ? 'true' : 'false') : $value;
            
            if (strpos($envContent, "{$envKey}=") !== false) {
                $envContent = preg_replace(
                    "/^{$envKey}=.*/m",
                    "{$envKey}={$envValue}",
                    $envContent
                );
            } else {
                $envContent .= "\n{$envKey}={$envValue}";
            }
        }
        
        File::put($envPath, $envContent);
    }

    /**
     * Taille du cache
     */
    private function getCacheSize()
    {
        $size = 0;
        $cachePath = storage_path('framework/cache');
        
        if (file_exists($cachePath)) {
            foreach (File::allFiles($cachePath) as $file) {
                $size += $file->getSize();
            }
        }
        
        return $this->formatBytes($size);
    }

    /**
     * Dernière sauvegarde
     */
    private function getLastBackup()
    {
        $lastBackup = Configuration::get('last_backup');
        return $lastBackup ? \Carbon\Carbon::parse($lastBackup)->diffForHumans() : 'Jamais';
    }

    /**
     * Taille de la base de données
     */
    private function getDatabaseSize()
    {
        $database = env('DB_DATABASE');
        $result = DB::select("
            SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'size' 
            FROM information_schema.tables 
            WHERE table_schema = ?
            GROUP BY table_schema
        ", [$database]);
        
        $size = !empty($result) ? $result[0]->size : 0;
        return $size . ' MB';
    }

    /**
     * Formatage des bytes
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}