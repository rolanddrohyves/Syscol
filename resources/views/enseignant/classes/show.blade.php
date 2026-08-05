{{-- resources/views/enseignant/classes/show.blade.php --}}
@extends('layouts.app')

@section('title', $classe->nom . ' - Détails')
@section('page-title', $classe->nom)

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- En-tête -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">{{ $classe->nom }}</h2>
                    <p class="text-indigo-100">
                        @if($classe->niveau) Niveau: {{ $classe->niveau }} | @endif
                        {{ $stats['total_eleves'] }} élève(s)
                    </p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('enseignant.classes.eleves', $classe->id) }}" 
                       class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-users mr-2"></i>Liste des élèves
                    </a>
                    <a href="{{ route('enseignant.classes.index') }}" 
                       class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 rounded-xl p-4 text-center">
                    <p class="text-sm text-blue-600">Total élèves</p>
                    <p class="text-2xl font-bold text-blue-700">{{ $stats['total_eleves'] }}</p>
                    <p class="text-xs text-gray-500">Garçons: {{ $stats['garcons'] }} | Filles: {{ $stats['filles'] }}</p>
                </div>
                <div class="bg-green-50 rounded-xl p-4 text-center">
                    <p class="text-sm text-green-600">Moyenne générale</p>
                    <p class="text-2xl font-bold text-green-700">{{ $stats['moyenne_generale'] }}/20</p>
                </div>
                <div class="bg-orange-50 rounded-xl p-4 text-center">
                    <p class="text-sm text-orange-600">Taux de présence</p>
                    <p class="text-2xl font-bold text-orange-700">{{ $stats['taux_presence'] }}%</p>
                </div>
                <div class="bg-purple-50 rounded-xl p-4 text-center">
                    <p class="text-sm text-purple-600">Matières enseignées</p>
                    <p class="text-2xl font-bold text-purple-700">{{ $classe->matieres->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Emploi du temps de la classe -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-indigo-600">
                <h3 class="text-lg font-semibold text-white">
                    <i class="fas fa-calendar-alt mr-2"></i> Emploi du temps
                </h3>
            </div>
            <div class="p-4">
                @if(count($emploiParJour) > 0)
                    @foreach($emploiParJour as $jour => $cours)
                        @if(count($cours) > 0)
                            <div class="mb-4">
                                <h4 class="font-semibold text-gray-700 mb-2 border-b pb-1">{{ $jour }}</h4>
                                <div class="space-y-2">
                                    @foreach($cours as $c)
                                    <div class="flex items-center text-sm">
                                        <span class="w-20 text-gray-600">{{ \Carbon\Carbon::parse($c->heure_debut)->format('H:i') }} - {{ \Carbon\Carbon::parse($c->heure_fin)->format('H:i') }}</span>
                                        <span class="font-medium text-gray-800">{{ $c->matiere->nom ?? 'N/A' }}</span>
                                        @if($c->salle)
                                            <span class="ml-2 text-xs text-gray-500">(Salle: {{ $c->salle }})</span>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <p class="text-center text-gray-500 py-4">Aucun cours programmé</p>
                @endif
            </div>
        </div>

        <!-- Dernières notes saisies -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-green-600">
                <h3 class="text-lg font-semibold text-white">
                    <i class="fas fa-star mr-2"></i> Dernières notes saisies
                </h3>
            </div>
            <div class="p-4">
                @if($dernieresNotes->count() > 0)
                    <div class="space-y-3">
                        @foreach($dernieresNotes as $note)
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center text-white text-xs font-semibold">
                                    {{ substr($note->eleve->prenom, 0, 1) }}{{ substr($note->eleve->nom, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-medium text-sm">{{ $note->eleve->prenom }} {{ $note->eleve->nom }}</p>
                                    <p class="text-xs text-gray-500">{{ $note->matiere->nom }} - {{ $note->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            <span class="font-bold {{ $note->note >= 10 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $note->note }}/20
                            </span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-gray-500 py-4">Aucune note saisie</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="mt-6 bg-white rounded-2xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Actions rapides</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('enseignant.notes.create', ['classe' => $classe->id, 'matiere' => $classe->matieres->first()->id ?? 0]) }}" 
               class="flex flex-col items-center p-4 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition-colors">
                <i class="fas fa-plus-circle text-2xl text-indigo-600 mb-2"></i>
                <span class="text-sm font-medium">Saisir notes</span>
            </a>
            <a href="{{ route('enseignant.presences') }}?classe_id={{ $classe->id }}" 
               class="flex flex-col items-center p-4 bg-green-50 rounded-xl hover:bg-green-100 transition-colors">
                <i class="fas fa-check-circle text-2xl text-green-600 mb-2"></i>
                <span class="text-sm font-medium">Faire l'appel</span>
            </a>
            <!-- CORRECTION ICI -->
            <a href="{{ route('enseignant.emploi_temps.index') }}?classe_id={{ $classe->id }}" 
               class="flex flex-col items-center p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors">
                <i class="fas fa-calendar-alt text-2xl text-blue-600 mb-2"></i>
                <span class="text-sm font-medium">Emploi du temps</span>
            </a>
            <a href="{{ route('enseignant.classes.eleves', $classe->id) }}" 
               class="flex flex-col items-center p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition-colors">
                <i class="fas fa-download text-2xl text-purple-600 mb-2"></i>
                <span class="text-sm font-medium">Exporter liste</span>
            </a>
        </div>
    </div>
</div>
@endsection