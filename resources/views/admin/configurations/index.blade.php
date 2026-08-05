{{-- resources/views/admin/configurations/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Configuration système - SYSCOL')
@section('page-title', 'Configuration système')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-gray-700 to-gray-900 rounded-2xl flex items-center justify-center shadow-lg animate-float">
                <i class="fas fa-cogs text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Configuration système</h2>
                <p class="text-sm text-gray-500">Gérez tous les paramètres de l'application</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.configurations.clear-cache') }}" 
               onclick="return confirm('Vider tous les caches ?')"
               class="flex items-center px-4 py-2 bg-yellow-600 text-white rounded-xl hover:bg-yellow-700 transition-all">
                <i class="fas fa-broom mr-2"></i>
                Vider les caches
            </a>
            <a href="{{ route('admin.configurations.optimize') }}" 
               class="flex items-center px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all">
                <i class="fas fa-rocket mr-2"></i>
                Optimiser
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-gray-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Configurations</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_configs'] }}</p>
                </div>
                <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-sliders-h text-gray-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Groupes</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['groups'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-folder text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Cache</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['cache_size'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-database text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Dernière sauvegarde</p>
                    <p class="text-lg font-bold text-gray-800">{{ $stats['last_backup'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-archive text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes de configuration -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Général -->
        <a href="{{ route('admin.configurations.general') }}" 
           class="bg-white rounded-2xl shadow-sm p-6 hover:shadow-lg transition-all hover:-translate-y-1 group">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-globe text-indigo-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Général</h3>
            </div>
            <p class="text-sm text-gray-600">Nom de l'application, URL, fuseau horaire, langue...</p>
            <span class="text-xs text-indigo-600 mt-4 inline-block">Configurer →</span>
        </a>

        <!-- Authentification -->
        <a href="{{ route('admin.configurations.auth') }}" 
           class="bg-white rounded-2xl shadow-sm p-6 hover:shadow-lg transition-all hover:-translate-y-1 group">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-lock text-green-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Authentification</h3>
            </div>
            <p class="text-sm text-gray-600">Sécurité, mots de passe, sessions, 2FA...</p>
            <span class="text-xs text-green-600 mt-4 inline-block">Configurer →</span>
        </a>

        <!-- Modules -->
        <a href="{{ route('admin.configurations.modules') }}" 
           class="bg-white rounded-2xl shadow-sm p-6 hover:shadow-lg transition-all hover:-translate-y-1 group">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-puzzle-piece text-purple-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Modules</h3>
            </div>
            <p class="text-sm text-gray-600">Activer/désactiver les fonctionnalités</p>
            <span class="text-xs text-purple-600 mt-4 inline-block">Configurer →</span>
        </a>

        <!-- Email -->
        <a href="{{ route('admin.configurations.mail') }}" 
           class="bg-white rounded-2xl shadow-sm p-6 hover:shadow-lg transition-all hover:-translate-y-1 group">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-envelope text-blue-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Email</h3>
            </div>
            <p class="text-sm text-gray-600">Configuration SMTP, notifications</p>
            <span class="text-xs text-blue-600 mt-4 inline-block">Configurer →</span>
        </a>

        <!-- Maintenance -->
        <a href="{{ route('admin.configurations.maintenance') }}" 
           class="bg-white rounded-2xl shadow-sm p-6 hover:shadow-lg transition-all hover:-translate-y-1 group">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-tools text-yellow-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Maintenance</h3>
            </div>
            <p class="text-sm text-gray-600">Mode maintenance, sauvegardes</p>
            <span class="text-xs text-yellow-600 mt-4 inline-block">Gérer →</span>
        </a>

        <!-- Système -->
        <a href="{{ route('admin.configurations.system') }}" 
           class="bg-white rounded-2xl shadow-sm p-6 hover:shadow-lg transition-all hover:-translate-y-1 group">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-server text-red-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Système</h3>
            </div>
            <p class="text-sm text-gray-600">Informations PHP, Laravel, métriques</p>
            <span class="text-xs text-red-600 mt-4 inline-block">Voir →</span>
        </a>
    </div>

    <!-- Actions rapides -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-bolt text-gray-600 mr-2"></i>
            Actions rapides
        </h3>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('admin.configurations.backup') }}" 
               onclick="return confirm('Lancer une sauvegarde maintenant ?')"
               class="flex flex-col items-center p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition-all">
                <i class="fas fa-database text-2xl text-blue-600 mb-2"></i>
                <span class="text-sm text-gray-700">Sauvegarder DB</span>
            </a>
            
            <a href="{{ route('admin.configurations.backups') }}" 
               class="flex flex-col items-center p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition-all">
                <i class="fas fa-archive text-2xl text-purple-600 mb-2"></i>
                <span class="text-sm text-gray-700">Voir sauvegardes</span>
            </a>
            
            <a href="{{ route('admin.configurations.clear-cache') }}" 
               onclick="return confirm('Vider tous les caches ?')"
               class="flex flex-col items-center p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition-all">
                <i class="fas fa-broom text-2xl text-yellow-600 mb-2"></i>
                <span class="text-sm text-gray-700">Vider cache</span>
            </a>
            
            <a href="{{ route('admin.configurations.optimize') }}" 
               class="flex flex-col items-center p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition-all">
                <i class="fas fa-rocket text-2xl text-green-600 mb-2"></i>
                <span class="text-sm text-gray-700">Optimiser</span>
            </a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .animate-float {
        animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
</style>
@endpush