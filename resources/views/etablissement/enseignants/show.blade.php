{{-- resources/views/etablissement/enseignants/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Détails enseignant - SYSCOL')
@section('page-title', 'Détails de l\'enseignant')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-chalkboard-teacher text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $enseignant->name }}</h2>
                <p class="text-sm text-gray-500">{{ $enseignant->enseignant->matricule }} · {{ $enseignant->enseignant->specialite }}</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('etablissement.enseignants.edit', $enseignant->id) }}" 
               class="flex items-center px-4 py-2 bg-yellow-600 text-white rounded-xl hover:bg-yellow-700 transition-all shadow-lg">
                <i class="fas fa-edit mr-2"></i>
                Modifier
            </a>
            <a href="{{ route('etablissement.enseignants.emploi-temps', $enseignant->id) }}" 
               class="flex items-center px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all shadow-lg">
                <i class="fas fa-calendar-alt mr-2"></i>
                Emploi du temps
            </a>
            <a href="{{ route('etablissement.enseignants.index') }}" 
               class="flex items-center px-4 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour
            </a>
        </div>
    </div>

    <!-- Grille d'informations -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne gauche : Informations personnelles -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <div class="flex flex-col items-center">
                    <div class="w-24 h-24 rounded-full bg-gradient-to-br from-purple-500 to-indigo-500 flex items-center justify-center text-white text-4xl font-bold shadow-lg mb-4">
                        {{ substr($enseignant->name, 0, 1) }}
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">{{ $enseignant->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $enseignant->email }}</p>
                    
                    <div class="mt-4">
                        <span class="px-3 py-1 text-xs rounded-full {{ $enseignant->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $enseignant->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-100">
                    <h4 class="text-sm font-semibold text-gray-800 mb-3">Informations personnelles</h4>
                    
                    <dl class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">Téléphone</dt>
                            <dd class="font-medium text-gray-900">{{ $enseignant->telephone ?? 'Non renseigné' }}</dd>
                        </div>
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">Adresse</dt>
                            <dd class="font-medium text-gray-900">{{ $enseignant->enseignant->adresse ?? 'Non renseignée' }}</dd>
                        </div>
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">Date embauche</dt>
                            <dd class="font-medium text-gray-900">{{ $enseignant->enseignant->date_embauche?->format('d/m/Y') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Colonne droite : Statistiques et affectations -->
        <div class="lg:col-span-2">
            <!-- Statistiques -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500">
                    <p class="text-sm text-gray-500">Classes (PP)</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['classes_principales'] }}</p>
                </div>
                
                <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500">
                    <p class="text-sm text-gray-500">Heures cours</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['heures_cours'] }}h</p>
                </div>
                
                <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500">
                    <p class="text-sm text-gray-500">Matières</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['matieres_enseignees'] }}</p>
                </div>
                
                <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-amber-500">
                    <p class="text-sm text-gray-500">Élèves</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['nb_eleves'] }}</p>
                </div>
            </div>

            <!-- Classes (Professeur principal) -->
            <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Classes (Professeur principal)</h4>
                
                @if($enseignant->classes->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($enseignant->classes as $classe)
                            <div class="p-4 bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $classe->nom }}</p>
                                        <p class="text-xs text-gray-500">{{ $classe->niveau }} · {{ $classe->eleves()->count() }} élèves</p>
                                    </div>
                                    <a href="{{ route('etablissement.classes.show', $classe->id) }}" 
                                       class="text-purple-600 hover:text-purple-800">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">Aucune classe en tant que professeur principal</p>
                @endif
            </div>

            <!-- Emploi du temps (aperçu) -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-lg font-semibold text-gray-800">Emploi du temps (aujourd'hui)</h4>
                    <a href="{{ route('etablissement.enseignants.emploi-temps', $enseignant->id) }}" 
                       class="text-sm text-purple-600 hover:text-purple-800">
                        Voir tout →
                    </a>
                </div>

                @php
                    $coursAujourdhui = $enseignant->emploisTemps
                        ->where('jour', now()->locale('fr')->dayName)
                        ->sortBy('heure_debut');
                @endphp

                @if($coursAujourdhui->count() > 0)
                    <div class="space-y-3">
                        @foreach($coursAujourdhui as $cours)
                            <div class="p-3 bg-gray-50 rounded-xl flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-800">{{ $cours->matiere->nom }}</p>
                                    <p class="text-sm text-gray-500">{{ $cours->classe->nom }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-800">
                                        {{ substr($cours->heure_debut, 0, 5) }} - {{ substr($cours->heure_fin, 0, 5) }}
                                    </p>
                                    @if($cours->salle)
                                        <p class="text-xs text-gray-500">Salle {{ $cours->salle }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">Aucun cours aujourd'hui</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection