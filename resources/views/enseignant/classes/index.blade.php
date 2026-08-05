{{-- resources/views/enseignant/classes/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Mes classes - Enseignant')
@section('page-title', 'Mes classes')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- En-tête -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Mes classes</h2>
                <p class="text-gray-500">Classes où j'enseigne ou dont je suis professeur principal</p>
            </div>
            <div class="bg-indigo-100 rounded-full px-4 py-2">
                <span class="text-indigo-700 font-semibold">{{ $classes->count() }} classe(s)</span>
            </div>
        </div>
    </div>

    <!-- Liste des classes -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($classes as $classe)
        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
            <!-- En-tête de la classe -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-white">{{ $classe->nom }}</h3>
                        <p class="text-indigo-100 text-sm">
                            @if($classe->niveau)
                                Niveau: {{ $classe->niveau }}
                            @else
                                {{ $classe->eleves->count() }} élève(s)
                            @endif
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-door-open text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Corps de la carte -->
            <div class="p-6">
                <!-- Statistiques rapides -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-gray-800">{{ $classe->eleves->count() }}</p>
                        <p class="text-xs text-gray-500">Élèves</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-gray-800">
                            {{ $statistiques[$classe->id]['total_matieres'] ?? $classe->matieres->count() }}
                        </p>
                        <p class="text-xs text-gray-500">Matières</p>
                    </div>
                </div>

                <!-- Barre de progression du taux de saisie des notes -->
                <div class="mb-4">
                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                        <span>Taux de saisie des notes</span>
                        <span>{{ $statistiques[$classe->id]['taux_saisie'] ?? 0 }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $statistiques[$classe->id]['taux_saisie'] ?? 0 }}%"></div>
                    </div>
                </div>

                <!-- Informations supplémentaires -->
                <div class="space-y-2 text-sm mb-4">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Cours par semaine</span>
                        <span class="font-medium">{{ $statistiques[$classe->id]['total_cours_hebdo'] ?? 0 }}h</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Notes saisies</span>
                        <span class="font-medium">{{ $statistiques[$classe->id]['notes_saisies'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Absences ce mois</span>
                        <span class="font-medium text-red-600">{{ $statistiques[$classe->id]['absences_mois'] ?? 0 }}</span>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="flex space-x-2 mt-4 pt-3 border-t border-gray-100">
                    <a href="{{ route('enseignant.classes.show', $classe->id) }}" 
                       class="flex-1 text-center px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-eye mr-1"></i> Détails
                    </a>
                    <a href="{{ route('enseignant.classes.eleves', $classe->id) }}" 
                       class="flex-1 text-center px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-users mr-1"></i> Élèves
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-chalkboard text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Aucune classe trouvée</h3>
                <p class="text-gray-500">Vous n'êtes pas encore assigné à des classes.</p>
                <p class="text-gray-400 text-sm mt-2">Contactez l'administrateur pour être affecté à des classes.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection