{{-- resources/views/etablissement/classes/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Détails de la classe - SYSCOL')
@section('page-title', 'Détails de la classe : ' . $classe->nom)

@section('content')
<div class="space-y-6">
    <!-- En-tête avec actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-door-open text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $classe->nom }}</h2>
                <p class="text-sm text-gray-500">{{ $classe->niveau }} · {{ $classe->serie ?? 'Sans série' }}</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('etablissement.classes.edit', $classe->id) }}" 
               class="flex items-center px-4 py-2 bg-yellow-600 text-white rounded-xl hover:bg-yellow-700 transition-all shadow-lg">
                <i class="fas fa-edit mr-2"></i>
                Modifier
            </a>
            <a href="{{ route('etablissement.classes.index') }}" 
               class="flex items-center px-4 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour
            </a>
        </div>
    </div>

    <!-- Grille d'informations -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne gauche : Informations générales -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Informations
                </h3>
                
                <dl class="space-y-4">
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <dt class="text-sm text-gray-500">Nom</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $classe->nom }}</dd>
                    </div>
                    
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <dt class="text-sm text-gray-500">Niveau</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $classe->niveau }}</dd>
                    </div>
                    
                    @if($classe->serie)
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <dt class="text-sm text-gray-500">Série</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $classe->serie }}</dd>
                    </div>
                    @endif
                    
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <dt class="text-sm text-gray-500">Capacité</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $classe->capacite }} élèves</dd>
                    </div>
                    
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <dt class="text-sm text-gray-500">Année scolaire</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $classe->anneeScolaire->libelle }}</dd>
                    </div>
                    
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <dt class="text-sm text-gray-500">Professeur principal</dt>
                        <dd class="text-sm font-medium text-gray-900">
                            @if($classe->professeurPrincipal)
                                <a href="{{ route('etablissement.enseignants.show', $classe->professeurPrincipal->id) }}" 
                                   class="text-blue-600 hover:text-blue-800 hover:underline">
                                    {{ $classe->professeurPrincipal->name }}
                                </a>
                            @else
                                <span class="text-gray-400">Non assigné</span>
                            @endif
                        </dd>
                    </div>
                    
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <dt class="text-sm text-gray-500">Date de création</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $classe->created_at->format('d/m/Y') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Colonne droite : Statistiques -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                    Statistiques
                </h3>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 rounded-xl p-4 text-center">
                        <p class="text-3xl font-bold text-blue-600">{{ $stats['total_eleves'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">Total élèves</p>
                    </div>
                    
                    <div class="bg-green-50 rounded-xl p-4 text-center">
                        <p class="text-3xl font-bold text-green-600">{{ $stats['places_disponibles'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">Places disponibles</p>
                    </div>
                    
                    <div class="bg-purple-50 rounded-xl p-4 text-center">
                        <p class="text-3xl font-bold text-purple-600">{{ $stats['filles'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">Filles</p>
                    </div>
                    
                    <div class="bg-amber-50 rounded-xl p-4 text-center">
                        <p class="text-3xl font-bold text-amber-600">{{ $stats['garcons'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">Garçons</p>
                    </div>
                </div>
                
                <!-- Barre de progression du taux d'occupation -->
                <div class="mt-6 p-4 bg-gray-50 rounded-xl">
                    <div class="flex justify-between text-sm text-gray-600 mb-2">
                        <span>Taux d'occupation</span>
                        <span class="font-semibold">{{ $stats['taux_occupation'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-blue-600 h-3 rounded-full" style="width: {{ $stats['taux_occupation'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des élèves -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-users text-blue-600 mr-2"></i>
                Élèves de la classe
                <span class="ml-2 text-sm font-normal text-gray-500">({{ $stats['total_eleves'] }} inscrits)</span>
            </h3>
            <a href="{{ route('etablissement.eleves.create', ['classe_id' => $classe->id]) }}" 
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all text-sm">
                <i class="fas fa-plus mr-1"></i>
                Ajouter un élève
            </a>
        </div>

        @if($eleves->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matricule</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom & Prénom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sexe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date naissance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Parent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($eleves as $eleve)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $eleve->matricule }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                    <span class="text-blue-600 text-sm font-semibold">{{ substr($eleve->prenom, 0, 1) }}{{ substr($eleve->nom, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $eleve->prenom }} {{ $eleve->nom }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <span class="px-2 py-1 text-xs rounded-full {{ $eleve->sexe == 'F' ? 'bg-pink-100 text-pink-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $eleve->sexe == 'F' ? 'Féminin' : 'Masculin' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $eleve->date_naissance->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $eleve->nom_parent }}<br>
                            <span class="text-xs text-gray-500">{{ $eleve->telephone_parent }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $eleve->status == 'actif' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($eleve->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('etablissement.eleves.show', $eleve->id) }}" 
                                   class="p-1.5 bg-blue-50 text-blue-600 rounded hover:bg-blue-100"
                                   title="Voir détails">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('etablissement.eleves.edit', $eleve->id) }}" 
                                   class="p-1.5 bg-yellow-50 text-yellow-600 rounded hover:bg-yellow-100"
                                   title="Modifier">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                <a href="{{ route('etablissement.absences.create', ['eleve_id' => $eleve->id]) }}" 
                                   class="p-1.5 bg-red-50 text-red-600 rounded hover:bg-red-100"
                                   title="Signaler absence">
                                    <i class="fas fa-calendar-times text-sm"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($eleves->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $eleves->links() }}
        </div>
        @endif

        @else
        <div class="text-center py-12">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-users text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun élève</h3>
            <p class="text-gray-500 mb-4">Cette classe n'a pas encore d'élèves inscrits</p>
            <a href="{{ route('etablissement.eleves.create', ['classe_id' => $classe->id]) }}" 
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-plus mr-2"></i>
                Ajouter un élève
            </a>
        </div>
        @endif
    </div>

    <!-- Emploi du temps de la classe -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
                Emploi du temps
            </h3>
            <a href="{{ route('etablissement.emplois_temps.create', ['classe_id' => $classe->id]) }}" 
               class="text-sm text-blue-600 hover:text-blue-800">
                <i class="fas fa-plus mr-1"></i>
                Ajouter un cours
            </a>
        </div>

        @if($classe->emploisTemps && $classe->emploisTemps->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($classe->emploisTemps->take(6) as $emploi)
                <div class="p-3 bg-gray-50 rounded-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-gray-800">{{ $emploi->matiere->nom }}</p>
                            <p class="text-sm text-gray-600">{{ $emploi->enseignant->name }}</p>
                        </div>
                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                            {{ $emploi->jour }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        {{ substr($emploi->heure_debut, 0, 5) }} - {{ substr($emploi->heure_fin, 0, 5) }}
                        @if($emploi->salle) · Salle {{ $emploi->salle }} @endif
                    </p>
                </div>
                @endforeach
            </div>
            
            @if($classe->emploisTemps->count() > 6)
            <div class="mt-4 text-center">
                <a href="{{ route('etablissement.emplois_temps.classe', $classe->id) }}" 
                   class="text-sm text-blue-600 hover:text-blue-800">
                    Voir tout l'emploi du temps →
                </a>
            </div>
            @endif
        @else
        <p class="text-gray-500 text-center py-4">Aucun cours planifié pour cette classe</p>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .hover\:scale-102:hover {
        transform: scale(1.02);
    }
</style>
@endpush