{{-- resources/views/directeur/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Tableau de bord - Directeur des études')
@section('page-title', 'Tableau de bord')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-chalkboard-teacher text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Tableau de bord</h2>
                <p class="text-sm text-gray-500">Directeur des études · {{ $anneeEnCours->libelle ?? 'Année en cours' }}</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <span class="px-3 py-1 text-sm bg-blue-100 text-blue-800 rounded-full">
                <i class="fas fa-calendar-alt mr-1"></i>
                {{ $trimestreEnCours->libelle ?? 'Trimestre en cours' }}
            </span>
        </div>
    </div>

    <!-- Statistiques générales -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Classes</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_classes'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-door-open text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Enseignants</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_enseignants'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chalkboard-teacher text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Élèves</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_eleves'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-graduate text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-amber-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Notes saisies</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_notes'] }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-star text-amber-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques des notes -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white">
            <p class="text-blue-100 text-sm mb-1">Moyenne générale</p>
            <p class="text-3xl font-bold">{{ number_format($statsNotes['moyenne_generale'], 2) }}/20</p>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-lg p-6 text-white">
            <p class="text-green-100 text-sm mb-1">Meilleure note</p>
            <p class="text-3xl font-bold">{{ number_format($statsNotes['meilleure_note'], 2) }}/20</p>
        </div>
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl shadow-lg p-6 text-white">
            <p class="text-red-100 text-sm mb-1">Moins bonne note</p>
            <p class="text-3xl font-bold">{{ number_format($statsNotes['moins_bonne_note'], 2) }}/20</p>
        </div>
    </div>

    <!-- Graphique et dernières notes -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Répartition des notes -->
        <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Répartition des notes</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>0 - 5</span>
                        <span class="font-medium">{{ $notesParTranche['0-5'] }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full bg-red-500" style="width: {{ $stats['total_notes'] > 0 ? ($notesParTranche['0-5'] / $stats['total_notes'] * 100) : 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>5 - 10</span>
                        <span class="font-medium">{{ $notesParTranche['5-10'] }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full bg-orange-500" style="width: {{ $stats['total_notes'] > 0 ? ($notesParTranche['5-10'] / $stats['total_notes'] * 100) : 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>10 - 15</span>
                        <span class="font-medium">{{ $notesParTranche['10-15'] }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full bg-yellow-500" style="width: {{ $stats['total_notes'] > 0 ? ($notesParTranche['10-15'] / $stats['total_notes'] * 100) : 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>15 - 20</span>
                        <span class="font-medium">{{ $notesParTranche['15-20'] }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full bg-green-500" style="width: {{ $stats['total_notes'] > 0 ? ($notesParTranche['15-20'] / $stats['total_notes'] * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dernières notes saisies -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Dernières notes saisies</h3>
            <div class="space-y-3">
                @forelse($dernieresNotes as $note)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">{{ $note->eleve->prenom }} {{ $note->eleve->nom }}</p>
                            <p class="text-xs text-gray-500">{{ $note->matiere->nom }} · {{ $note->enseignant->name }}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 text-sm rounded-full 
                                @if($note->note >= 16) bg-green-100 text-green-800
                                @elseif($note->note >= 12) bg-blue-100 text-blue-800
                                @elseif($note->note >= 10) bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ number_format($note->note, 2) }}/20
                            </span>
                            <p class="text-xs text-gray-400 mt-1">{{ $note->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">Aucune note récente</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Classes et emploi du temps -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Liste des classes -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Effectifs par classe</h3>
            <div class="space-y-3">
                @foreach($classes as $classe)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">{{ $classe->nom }}</p>
                            <p class="text-xs text-gray-500">{{ $classe->niveau }}</p>
                        </div>
                        <span class="px-3 py-1 text-sm bg-blue-100 text-blue-800 rounded-full">
                            {{ $classe->eleves_count }} élèves
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Emploi du temps du jour -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Emploi du temps du jour</h3>
            @if($emploiDuTemps->count() > 0)
                <div class="space-y-3">
                    @foreach($emploiDuTemps as $cours)
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-semibold text-blue-600">{{ substr($cours->heure_debut, 0, 5) }} - {{ substr($cours->heure_fin, 0, 5) }}</span>
                                @if($cours->salle)
                                    <span class="text-xs text-gray-500">Salle {{ $cours->salle }}</span>
                                @endif
                            </div>
                            <p class="font-medium text-gray-900">{{ $cours->matiere->nom }}</p>
                            <p class="text-xs text-gray-600">{{ $cours->classe->nom }} · {{ $cours->enseignant->name }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">Aucun cours prévu aujourd'hui</p>
            @endif
        </div>
    </div>

    <!-- Alertes et notifications -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Absences ce mois</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $absencesMois }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-times text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Retards ce mois</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $retardsMois }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection