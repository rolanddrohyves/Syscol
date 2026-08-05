{{-- resources/views/etablissement/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Établissement - SYSCOL')
@section('page-title', 'Tableau de bord')

@section('content')
<!-- ACTIONS RAPIDES - Toujours visibles -->
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-bolt text-yellow-500 mr-2"></i>
        Actions rapides
    </h3>
    
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
        <a href="{{ route('etablissement.notes.create') }}" 
           class="flex flex-col items-center p-4 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl text-white hover:shadow-lg transform hover:scale-105 transition-all group">
            <i class="fas fa-star text-2xl mb-2 group-hover:rotate-12 transition-transform"></i>
            <span class="text-sm font-medium">Saisir note</span>
            <span class="text-xs opacity-75 mt-1">Ctrl+N</span>
        </a>
        
        <a href="{{ route('etablissement.absences.create') }}" 
           class="flex flex-col items-center p-4 bg-gradient-to-br from-red-500 to-orange-500 rounded-xl text-white hover:shadow-lg transform hover:scale-105 transition-all group">
            <i class="fas fa-calendar-times text-2xl mb-2 group-hover:rotate-12 transition-transform"></i>
            <span class="text-sm font-medium">Absence</span>
            <span class="text-xs opacity-75 mt-1">Ctrl+A</span>
        </a>
        
        <a href="{{ route('etablissement.eleves.create') }}" 
           class="flex flex-col items-center p-4 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl text-white hover:shadow-lg transform hover:scale-105 transition-all group">
            <i class="fas fa-user-plus text-2xl mb-2 group-hover:rotate-12 transition-transform"></i>
            <span class="text-sm font-medium">Nouvel élève</span>
            <span class="text-xs opacity-75 mt-1">Ctrl+E</span>
        </a>
        
        <a href="{{ route('etablissement.classes.create') }}" 
           class="flex flex-col items-center p-4 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl text-white hover:shadow-lg transform hover:scale-105 transition-all group">
            <i class="fas fa-door-open text-2xl mb-2 group-hover:rotate-12 transition-transform"></i>
            <span class="text-sm font-medium">Nouvelle classe</span>
            <span class="text-xs opacity-75 mt-1">Ctrl+C</span>
        </a>
        
        <a href="{{ route('etablissement.emplois_temps.create') }}" 
           class="flex flex-col items-center p-4 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl text-white hover:shadow-lg transform hover:scale-105 transition-all">
            <i class="fas fa-calendar-alt text-2xl mb-2"></i>
            <span class="text-sm font-medium">Emploi du temps</span>
        </a>
        
        <a href="{{ route('etablissement.notes.index') }}" 
           class="flex flex-col items-center p-4 bg-gradient-to-br from-gray-500 to-gray-700 rounded-xl text-white hover:shadow-lg transform hover:scale-105 transition-all">
            <i class="fas fa-list text-2xl mb-2"></i>
            <span class="text-sm font-medium">Toutes les notes</span>
        </a>
    </div>
</div>

<!-- Le reste de votre dashboard -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Classes -->
    <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500 hover:shadow-lg transition-all hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Classes</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['classes']['total'] ?? 0 }}</p>
            </div>
            <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center">
                <i class="fas fa-door-open text-2xl text-blue-600"></i>
            </div>
        </div>
        <a href="{{ route('etablissement.classes.index') }}" class="text-sm text-blue-600 hover:text-blue-800 mt-2 inline-block">Voir toutes →</a>
    </div>

    <!-- Élèves -->
    <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500 hover:shadow-lg transition-all hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Élèves</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['eleves']['total'] ?? 0 }}</p>
            </div>
            <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center">
                <i class="fas fa-user-graduate text-2xl text-green-600"></i>
            </div>
        </div>
        <a href="{{ route('etablissement.eleves.index') }}" class="text-sm text-green-600 hover:text-green-800 mt-2 inline-block">Voir tous →</a>
    </div>

    <!-- Enseignants -->
    <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500 hover:shadow-lg transition-all hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Enseignants</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['enseignants']['total'] ?? 0 }}</p>
            </div>
            <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center">
                <i class="fas fa-chalkboard-teacher text-2xl text-purple-600"></i>
            </div>
        </div>
        <a href="{{ route('etablissement.enseignants.index') }}" class="text-sm text-purple-600 hover:text-purple-800 mt-2 inline-block">Voir tous →</a>
    </div>

    <!-- Notes -->
    <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-amber-500 hover:shadow-lg transition-all hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Notes</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['notes_count'] ?? 0 }}</p>
            </div>
            <div class="w-14 h-14 bg-amber-100 rounded-2xl flex items-center justify-center">
                <i class="fas fa-star text-2xl text-amber-600"></i>
            </div>
        </div>
        <a href="{{ route('etablissement.notes.index') }}" class="text-sm text-amber-600 hover:text-amber-800 mt-2 inline-block">Voir toutes →</a>
    </div>
</div>

<!-- Graphiques et activités récentes -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
    <!-- Graphique (placeholder) -->
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Activité récente</h3>
        <div class="h-64 bg-gray-50 rounded-xl flex items-center justify-center">
            <p class="text-gray-400">Graphique d'activité</p>
        </div>
    </div>

    <!-- Dernières notes saisies -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-history text-indigo-600 mr-2"></i>
            Dernières notes
        </h3>
        <div class="space-y-3">
            @forelse($dernieresNotes ?? [] as $note)
                <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium">{{ $note->eleve->prenom }} {{ $note->eleve->nom }}</p>
                        <p class="text-xs text-gray-500">{{ $note->matiere->nom }}</p>
                    </div>
                    <span class="font-bold {{ $note->note >= 10 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $note->note }}/{{ $note->note_max }}
                    </span>
                </div>
            @empty
                <p class="text-gray-500 text-center py-4">Aucune note récente</p>
            @endforelse
            <a href="{{ route('etablissement.notes.create') }}" class="block text-center text-sm text-indigo-600 hover:text-indigo-800 mt-2">
                + Ajouter une note
            </a>
        </div>
    </div>
</div>
@endsection