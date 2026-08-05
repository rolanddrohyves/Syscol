@extends('layouts.app')

@section('title', 'Dashboard Administrateur')
@section('page-title', 'Tableau de bord')

@section('content')
<div class="space-y-6">
    <!-- Statistiques principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-indigo-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Établissements</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['etablissements'] }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.etablissements') }}" class="text-sm text-indigo-600 hover:text-indigo-800 mt-2 inline-block">Voir détails →</a>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Utilisateurs</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['utilisateurs'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.users') }}" class="text-sm text-green-600 hover:text-green-800 mt-2 inline-block">Voir détails →</a>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Élèves</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['eleves'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
            <span class="text-sm text-blue-600 mt-2 inline-block">Total inscriptions</span>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Enseignants</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['enseignants'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
            <span class="text-sm text-purple-600 mt-2 inline-block">Corps enseignant</span>
        </div>
    </div>

    <!-- Alertes -->
    @if(count($alertes) > 0)
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Alertes</h3>
        <div class="space-y-3">
            @foreach($alertes as $alerte)
            <div class="flex items-center p-3 bg-{{ $alerte['type'] }}-50 border border-{{ $alerte['type'] }}-200 rounded-lg">
                <div class="w-2 h-2 bg-{{ $alerte['type'] }}-500 rounded-full mr-3"></div>
                <p class="text-sm text-gray-700">{{ $alerte['message'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Deux colonnes -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Derniers établissements -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Derniers établissements</h3>
            <div class="space-y-4">
                @forelse($derniersEtablissements as $etablissement)
                <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                    <div>
                        <p class="font-medium text-gray-800">{{ $etablissement->nom }}</p>
                        <p class="text-sm text-gray-500">{{ $etablissement->type }} • {{ $etablissement->ville }}</p>
                    </div>
                    <span class="text-xs text-gray-400">{{ $etablissement->created_at->diffForHumans() }}</span>
                </div>
                @empty
                <p class="text-gray-500 text-sm">Aucun établissement</p>
                @endforelse
            </div>
        </div>

        <!-- Derniers utilisateurs -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Derniers utilisateurs</h3>
            <div class="space-y-4">
                @forelse($derniersUtilisateurs as $user)
                <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                            <span class="text-indigo-600 text-sm font-semibold">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->role->display_name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</span>
                </div>
                @empty
                <p class="text-gray-500 text-sm">Aucun utilisateur</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Activités récentes -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Activités récentes</h3>
        <div class="space-y-4">
            @foreach($activites as $activite)
            <div class="flex items-center text-sm">
                <div class="w-2 h-2 rounded-full bg-{{ $activite['color'] }}-500 mr-3"></div>
                <span class="text-gray-600">{{ $activite['message'] }}</span>
                <span class="ml-auto text-xs text-gray-400">{{ $activite['time'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection