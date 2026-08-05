{{-- resources/views/dashboard/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard - SYSCOL')
@section('page-title', 'Tableau de bord')

@section('content')
<div class="space-y-6 animate-fade-in">
    <!-- Message de bienvenue personnalisé -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-lg p-6 text-white relative overflow-hidden group">
        <!-- Arrière-plan décoratif -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-20 -mt-20 group-hover:scale-150 transition-transform duration-1000"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full -ml-10 -mb-10 group-hover:scale-150 transition-transform duration-1000 delay-200"></div>
        
        <div class="relative z-10">
            <h2 class="text-2xl font-bold mb-2 flex items-center">
                <i class="fas fa-hand-wave mr-3 animate-bounce-slow"></i>
                Bonjour, {{ Auth::user()->name }} !
            </h2>
            <p class="text-indigo-100 text-lg">
                Ravi de vous revoir sur votre espace 
                <span class="font-semibold text-white">{{ Auth::user()->role->display_name ?? 'SYSCOL' }}</span>
            </p>
            <p class="text-indigo-200 text-sm mt-4">
                {{ now()->format('l d F Y') }} • Dernière connexion : {{ Auth::user()->last_login_at ?? 'Première connexion' }}
            </p>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-indigo-500 hover:shadow-lg transition-all duration-300 hover:-translate-y-1 group animate-slide-in-left" style="animation-delay: 0.1s;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Établissements</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">12</p>
                    <p class="text-xs text-green-600 mt-2 flex items-center">
                        <i class="fas fa-arrow-up mr-1"></i> +3 cette année
                    </p>
                </div>
                <div class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform group-hover:rotate-6">
                    <i class="fas fa-school text-2xl text-indigo-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500 hover:shadow-lg transition-all duration-300 hover:-translate-y-1 group animate-slide-in-left" style="animation-delay: 0.2s;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Utilisateurs</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">1,234</p>
                    <p class="text-xs text-green-600 mt-2 flex items-center">
                        <i class="fas fa-arrow-up mr-1"></i> +28 ce mois
                    </p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform group-hover:rotate-6">
                    <i class="fas fa-users text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500 hover:shadow-lg transition-all duration-300 hover:-translate-y-1 group animate-slide-in-left" style="animation-delay: 0.3s;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Élèves</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">856</p>
                    <p class="text-xs text-green-600 mt-2 flex items-center">
                        <i class="fas fa-arrow-up mr-1"></i> +15 cette semaine
                    </p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform group-hover:rotate-6">
                    <i class="fas fa-user-graduate text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500 hover:shadow-lg transition-all duration-300 hover:-translate-y-1 group animate-slide-in-left" style="animation-delay: 0.4s;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Enseignants</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">78</p>
                    <p class="text-xs text-yellow-600 mt-2 flex items-center">
                        <i class="fas fa-minus mr-1"></i> Stable
                    </p>
                </div>
                <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform group-hover:rotate-6">
                    <i class="fas fa-chalkboard-teacher text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et activités -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Graphique d'activité -->
        <div class="bg-white rounded-2xl shadow-sm p-6 hover:shadow-lg transition-all duration-300 animate-fade-in" style="animation-delay: 0.3s;">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-chart-line text-indigo-600 mr-2"></i>
                    Activité récente
                </h3>
                <select class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option>Cette semaine</option>
                    <option>Ce mois</option>
                    <option>Cette année</option>
                </select>
            </div>
            
            <!-- Graphique simulé -->
            <div class="h-64 flex items-end justify-between space-x-2">
                @foreach(['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $index => $day)
                <div class="flex-1 flex flex-col items-center group">
                    <div class="w-full bg-gradient-to-t from-indigo-500 to-purple-500 rounded-t-lg transition-all duration-300 group-hover:from-indigo-600 group-hover:to-purple-600 relative" style="height: {{ 40 + $index * 15 }}px;">
                        <span class="absolute -top-6 left-1/2 transform -translate-x-1/2 text-xs text-gray-600 opacity-0 group-hover:opacity-100 transition-opacity">
                            {{ 20 + $index * 5 }}
                        </span>
                    </div>
                    <span class="text-xs text-gray-500 mt-2">{{ $day }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Activités récentes -->
        <div class="bg-white rounded-2xl shadow-sm p-6 hover:shadow-lg transition-all duration-300 animate-fade-in" style="animation-delay: 0.4s;">
            <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-history text-indigo-600 mr-2"></i>
                Activités récentes
            </h3>
            
            <div class="space-y-4">
                @for($i = 1; $i <= 5; $i++)
                <div class="flex items-start space-x-3 group hover:bg-gray-50 p-2 rounded-lg transition-all cursor-pointer">
                    <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                        <i class="fas fa-user-plus text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-800 font-medium">Nouvel élève inscrit</p>
                        <p class="text-xs text-gray-500">Jean Dupont - 6ème A</p>
                    </div>
                    <span class="text-xs text-gray-400">Il y a {{ $i*5 }} min</span>
                </div>
                @endfor
            </div>
            
            <a href="#" class="block text-center text-sm text-indigo-600 hover:text-indigo-800 mt-6 font-medium group">
                Voir toutes les activités
                <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="bg-white rounded-2xl shadow-sm p-6 animate-fade-in" style="animation-delay: 0.5s;">
        <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-bolt text-indigo-600 mr-2"></i>
            Actions rapides
        </h3>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="#" class="flex flex-col items-center p-4 border-2 border-gray-100 rounded-xl hover:border-indigo-500 hover:bg-indigo-50 transition-all duration-300 group">
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-indigo-500 transition-all">
                    <i class="fas fa-user-plus text-indigo-600 group-hover:text-white text-xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-indigo-600">Nouvel élève</span>
            </a>
            
            <a href="#" class="flex flex-col items-center p-4 border-2 border-gray-100 rounded-xl hover:border-green-500 hover:bg-green-50 transition-all duration-300 group">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-green-500 transition-all">
                    <i class="fas fa-chalkboard-teacher text-green-600 group-hover:text-white text-xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-green-600">Nouvel enseignant</span>
            </a>
            
            <a href="#" class="flex flex-col items-center p-4 border-2 border-gray-100 rounded-xl hover:border-purple-500 hover:bg-purple-50 transition-all duration-300 group">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-purple-500 transition-all">
                    <i class="fas fa-door-open text-purple-600 group-hover:text-white text-xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-purple-600">Nouvelle classe</span>
            </a>
            
            <a href="#" class="flex flex-col items-center p-4 border-2 border-gray-100 rounded-xl hover:border-yellow-500 hover:bg-yellow-50 transition-all duration-300 group">
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-yellow-500 transition-all">
                    <i class="fas fa-calendar-plus text-yellow-600 group-hover:text-white text-xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-yellow-600">Nouvel événement</span>
            </a>
        </div>
    </div>

    <!-- Rappels et notifications -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl shadow-lg p-6 text-white animate-fade-in" style="animation-delay: 0.6s;">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-3">
                    <i class="fas fa-bell text-white"></i>
                </div>
                <h4 class="font-bold">Rappels importants</h4>
            </div>
            <ul class="space-y-3">
                <li class="flex items-center text-sm bg-white/10 p-2 rounded-lg">
                    <i class="fas fa-circle mr-2 text-xs"></i>
                    Réunion des professeurs - Demain 15h
                </li>
                <li class="flex items-center text-sm bg-white/10 p-2 rounded-lg">
                    <i class="fas fa-circle mr-2 text-xs"></i>
                    Conseil de classe - 3ème A - Vendredi
                </li>
                <li class="flex items-center text-sm bg-white/10 p-2 rounded-lg">
                    <i class="fas fa-circle mr-2 text-xs"></i>
                    Date limite des notes - 30 Février
                </li>
            </ul>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl shadow-lg p-6 text-white animate-fade-in" style="animation-delay: 0.7s;">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-3">
                    <i class="fas fa-chart-pie text-white"></i>
                </div>
                <h4 class="font-bold">Statistiques du jour</h4>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-white/10 p-3 rounded-lg">
                    <p class="text-xs opacity-90">Présences</p>
                    <p class="text-2xl font-bold">92%</p>
                </div>
                <div class="bg-white/10 p-3 rounded-lg">
                    <p class="text-xs opacity-90">Absences</p>
                    <p class="text-2xl font-bold">8%</p>
                </div>
                <div class="bg-white/10 p-3 rounded-lg">
                    <p class="text-xs opacity-90">Paiements</p>
                    <p class="text-2xl font-bold">75%</p>
                </div>
                <div class="bg-white/10 p-3 rounded-lg">
                    <p class="text-xs opacity-90">Notes saisies</p>
                    <p class="text-2xl font-bold">234</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .animate-fade-in {
        animation: fadeIn 0.5s ease-in-out forwards;
        opacity: 0;
    }
    
    .animate-slide-in-left {
        animation: slideInLeft 0.5s ease-out forwards;
        opacity: 0;
        transform: translateX(-20px);
    }
    
    .animate-bounce-slow {
        animation: bounce 2s infinite;
    }
    
    @keyframes fadeIn {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes slideInLeft {
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
</style>
@endpush