{{-- resources/views/admin/configurations/system.blade.php --}}
@extends('layouts.app')

@section('title', 'Informations système - SYSCOL')
@section('page-title', 'Informations système')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex items-center space-x-3">
        <div class="w-12 h-12 bg-gradient-to-br from-gray-700 to-gray-900 rounded-2xl flex items-center justify-center shadow-lg">
            <i class="fas fa-server text-white text-xl"></i>
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Système</h2>
            <p class="text-sm text-gray-500">Informations techniques et métriques</p>
        </div>
    </div>

    <!-- Cartes système -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- PHP -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fab fa-php text-indigo-600 mr-2"></i>
                PHP
            </h3>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Version</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $metrics['php_version'] }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Memory limit</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $metrics['memory_limit'] }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Max execution time</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $metrics['max_execution_time'] }}s</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Upload max filesize</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $metrics['upload_max_filesize'] }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Post max size</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $metrics['post_max_size'] }}</dd>
                </div>
            </dl>
        </div>

        <!-- Laravel -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fab fa-laravel text-red-600 mr-2"></i>
                Laravel
            </h3>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Version</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $metrics['laravel_version'] }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Environnement</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ app()->environment() }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Debug mode</dt>
                    <dd class="text-sm">
                        <span class="px-2 py-1 text-xs rounded-full {{ config('app.debug') ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                            {{ config('app.debug') ? 'Activé' : 'Désactivé' }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Cache driver</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ config('cache.default') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Session driver</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ config('session.driver') }}</dd>
                </div>
            </dl>
        </div>

        <!-- Base de données -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-database text-green-600 mr-2"></i>
                Base de données
            </h3>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Base</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $metrics['database'] }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Taille</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $metrics['database_size'] }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Connexion</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ config('database.default') }}</dd>
                </div>
            </dl>
        </div>

        <!-- Serveur -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-hdd text-purple-600 mr-2"></i>
                Serveur
            </h3>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Logiciel</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $metrics['server_software'] }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Espace disque</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $metrics['disk_free'] }} / {{ $metrics['disk_total'] }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Utilisation disque</dt>
                    <dd class="text-sm">
                        <div class="flex items-center">
                            <span class="mr-2">{{ $metrics['disk_usage'] }}%</span>
                            <div class="w-24 bg-gray-200 rounded-full h-2">
                                <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $metrics['disk_usage'] }}%"></div>
                            </div>
                        </div>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Extensions PHP -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-puzzle-piece text-indigo-600 mr-2"></i>
            Extensions PHP chargées
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach(get_loaded_extensions() as $extension)
                <div class="flex items-center p-2 bg-gray-50 rounded-lg">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                    <span class="text-sm text-gray-700">{{ $extension }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Variables d'environnement (cachées) -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-key text-yellow-600 mr-2"></i>
            Variables d'environnement
        </h3>
        <p class="text-sm text-gray-500 mb-4">Les valeurs sensibles sont masquées</p>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            @foreach(['APP_NAME', 'APP_ENV', 'APP_DEBUG', 'APP_URL', 'DB_CONNECTION', 'DB_DATABASE', 'CACHE_STORE', 'SESSION_DRIVER', 'MAIL_MAILER'] as $key)
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500">{{ $key }}</p>
                    <p class="text-sm font-medium text-gray-800">{{ env($key, 'Non défini') }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection