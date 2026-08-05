{{-- resources/views/enseignant/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Tableau de bord - Enseignant')
@section('page-title', 'Tableau de bord enseignant')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg animate-float">
                <i class="fas fa-chalkboard-teacher text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Bonjour, {{ $enseignant->user->name ?? 'Enseignant' }}</h2>
                <p class="text-sm text-gray-500">
                    {{ $anneeScolaire->libelle ?? 'Année scolaire' }} · 
                    {{ $trimestreActuel->libelle ?? 'Trimestre en cours' }}
                </p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('enseignant.notes') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors">
                <i class="fas fa-book-open mr-2"></i>Gérer les notes
            </a>
            <a href="{{ route('enseignant.presences') }}" class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors">
                <i class="fas fa-check-circle mr-2"></i>Présences
            </a>
        </div>
    </div>

    <!-- Alertes -->
    @if(isset($alertes) && count($alertes) > 0)
    <div class="space-y-2">
        @foreach($alertes as $alerte)
        <div class="p-4 {{ $alerte['type'] == 'danger' ? 'bg-red-50 border-red-200' : 'bg-yellow-50 border-yellow-200' }} border rounded-xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas {{ $alerte['type'] == 'danger' ? 'fa-exclamation-circle text-red-600' : 'fa-exclamation-triangle text-yellow-600' }} mr-3"></i>
                    <span class="text-sm {{ $alerte['type'] == 'danger' ? 'text-red-800' : 'text-yellow-800' }}">{{ $alerte['message'] }}</span>
                </div>
                @if(isset($alerte['lien']) && $alerte['lien'] != '#')
                <a href="{{ $alerte['lien'] }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    Voir les détails →
                </a>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Cartes statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500 hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-gray-500">Mes classes</p>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chalkboard text-blue-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total_classes'] ?? 0 }}</p>
            <p class="text-xs text-gray-500 mt-1">classes à charge</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500 hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-gray-500">Mes élèves</p>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-green-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total_eleves'] ?? 0 }}</p>
            <p class="text-xs text-gray-500 mt-1">élèves au total</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500 hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-gray-500">Mes matières</p>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-book text-purple-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total_matieres'] ?? 0 }}</p>
            <p class="text-xs text-gray-500 mt-1">matières enseignées</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-orange-500 hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-gray-500">Notes saisies</p>
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-pen-fancy text-orange-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total_notes'] ?? 0 }}</p>
            <p class="text-xs text-gray-500 mt-1">notes enregistrées</p>
        </div>
    </div>

    <!-- Emploi du temps du jour et cours à venir -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Emploi du temps du jour -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-500 to-blue-600">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-calendar-day mr-2"></i>
                    Emploi du temps - Aujourd'hui
                    <span class="ml-3 text-sm font-normal text-blue-100">{{ Carbon\Carbon::now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</span>
                </h3>
            </div>
            
            <div class="p-4">
                @if($emploiDuJour->count() > 0)
                    <div class="space-y-3">
                        @foreach($emploiDuJour as $cours)
                        <div class="flex items-center p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                            <div class="w-20 text-center">
                                <span class="text-sm font-bold text-gray-700">
                                    {{ \Carbon\Carbon::parse($cours->heure_debut)->format('H:i') }}
                                </span>
                                <span class="text-xs text-gray-400">→</span>
                                <span class="text-sm font-bold text-gray-700">
                                    {{ \Carbon\Carbon::parse($cours->heure_fin)->format('H:i') }}
                                </span>
                            </div>
                            <div class="flex-1 ml-4">
                                <p class="font-semibold text-gray-800">{{ $cours->matiere->nom ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-500">Classe: {{ $cours->classe->nom ?? 'N/A' }}</p>
                            </div>
                            @if($cours->salle)
                            <div class="px-3 py-1 bg-gray-200 rounded-lg text-sm text-gray-600">
                                <i class="fas fa-door-open mr-1"></i> {{ $cours->salle }}
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-clock text-4xl text-gray-300 mb-2"></i>
                        <p class="text-gray-500">Aucun cours programmé pour aujourd'hui</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Prochains cours -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-green-500 to-green-600">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-hourglass-half mr-2"></i>
                    Prochains cours à venir
                </h3>
            </div>
            
            <div class="p-4">
                @if($prochainsCours->count() > 0)
                    <div class="space-y-3">
                        @foreach($prochainsCours as $cours)
                        <div class="flex items-center p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                            <div class="w-24">
                                <span class="text-sm font-medium text-gray-600">{{ $cours->jour }}</span>
                            </div>
                            <div class="w-20 text-center">
                                <span class="text-sm font-bold text-gray-700">
                                    {{ \Carbon\Carbon::parse($cours->heure_debut)->format('H:i') }}
                                </span>
                            </div>
                            <div class="flex-1 ml-4">
                                <p class="font-semibold text-gray-800">{{ $cours->matiere->nom ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-500">Classe: {{ $cours->classe->nom ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-check-circle text-4xl text-green-300 mb-2"></i>
                        <p class="text-gray-500">Aucun cours à venir cette semaine</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Moyennes par matière -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-simple text-blue-500 mr-2"></i>
                Moyennes par matière
            </h3>
            <canvas id="moyennesChart" height="250"></canvas>
            <div class="mt-4 space-y-2">
                @foreach($moyennesParMatiere as $item)
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">{{ $item['matiere'] }}</span>
                    <div class="flex-1 mx-4">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($item['moyenne'] / 20) * 100 }}%"></div>
                        </div>
                    </div>
                    <span class="font-bold {{ $item['moyenne'] >= 10 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $item['moyenne'] }}/20
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Présences par classe -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-pie text-green-500 mr-2"></i>
                Taux de présence par classe
            </h3>
            <canvas id="presencesChart" height="250"></canvas>
            <div class="mt-4 grid grid-cols-2 gap-2">
                @foreach($tauxPresenceParClasse as $item)
                <div class="p-2 bg-gray-50 rounded-lg">
                    <p class="text-sm font-medium text-gray-700">{{ $item['classe'] }}</p>
                    <div class="flex items-center mt-1">
                        <div class="flex-1">
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $item['taux'] }}%"></div>
                            </div>
                        </div>
                        <span class="ml-2 text-sm font-bold {{ $item['taux'] >= 75 ? 'text-green-600' : 'text-orange-600' }}">
                            {{ $item['taux'] }}%
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Absences du jour -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-red-500 to-red-600">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-user-graduate mr-2"></i>
                Absences et retards du jour
            </h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Classe</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Absences</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Retards</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Taux présence</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($absencesJour as $index => $absence)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $absence['classe'] }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $absence['total_absences'] > 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                {{ $absence['total_absences'] }} absent(s)
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $absence['total_retards'] > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                {{ $absence['total_retards'] }} retard(s)
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $taux = isset($tauxPresenceParClasse[$index]) ? $tauxPresenceParClasse[$index]['taux'] : 0;
                            @endphp
                            <div class="flex items-center justify-center">
                                <div class="w-16 bg-gray-200 rounded-full h-1.5 mr-2">
                                    <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $taux }}%"></div>
                                </div>
                                <span class="text-sm">{{ $taux }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('enseignant.presences') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                                Gérer <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-check-circle text-4xl mb-2 text-green-400"></i>
                            <p>Aucune absence enregistrée aujourd'hui</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Dernières notes saisies -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-purple-500 to-purple-600">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-pen-fancy mr-2"></i>
                Dernières notes saisies
            </h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Élève</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matière</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Note</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Appréciation</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($dernieresNotes as $note)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $note->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-indigo-500 flex items-center justify-center mr-2 text-white text-xs font-semibold">
                                    {{ substr($note->eleve->prenom, 0, 1) }}{{ substr($note->eleve->nom, 0, 1) }}
                                </div>
                                <span class="font-medium text-sm">{{ $note->eleve->prenom }} {{ $note->eleve->nom }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $note->matiere->nom ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-12 px-2 py-1 rounded-full text-sm font-bold {{ $note->note >= 10 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $note->note }}/{{ $note->note_max ?? 20 }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $note->appreciation ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-receipt text-4xl mb-2 text-gray-400"></i>
                            <p>Aucune note enregistrée</p>
                            @if($classes->isNotEmpty() && $enseignant->matieres->isNotEmpty())
                            <a href="{{ route('enseignant.notes.create', ['classe' => $classes->first()->id, 'matiere' => $enseignant->matieres->first()->id]) }}" class="mt-2 inline-block text-indigo-600 hover:text-indigo-800">
                                Commencer à saisir des notes →
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-bolt text-yellow-500 mr-2"></i>
            Actions rapides
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @if($classes->isNotEmpty() && $enseignant->matieres->isNotEmpty())
            <a href="{{ route('enseignant.notes.create', ['classe' => $classes->first()->id, 'matiere' => $enseignant->matieres->first()->id]) }}" 
               class="flex flex-col items-center p-4 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition-colors group">
                <i class="fas fa-plus-circle text-2xl text-indigo-600 mb-2 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700">Saisir notes</span>
            </a>
            @else
            <div class="flex flex-col items-center p-4 bg-gray-50 rounded-xl opacity-50">
                <i class="fas fa-plus-circle text-2xl text-gray-400 mb-2"></i>
                <span class="text-sm font-medium text-gray-500">Saisir notes</span>
                <span class="text-xs text-gray-400">(Aucune classe/matière)</span>
            </div>
            @endif
            
            <a href="{{ route('enseignant.presences') }}" class="flex flex-col items-center p-4 bg-green-50 rounded-xl hover:bg-green-100 transition-colors group">
                <i class="fas fa-check-double text-2xl text-green-600 mb-2 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700">Marquer présences</span>
            </a>
            
            <a href="{{ route('enseignant.emploi_temps.index') }}" class="flex flex-col items-center p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors group">
                <i class="fas fa-table text-2xl text-blue-600 mb-2 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700">Voir emploi du temps</span>
            </a>
            
            <a href="{{ route('enseignant.evaluations') }}" class="flex flex-col items-center p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition-colors group">
                <i class="fas fa-file-alt text-2xl text-purple-600 mb-2 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700">Mes évaluations</span>
            </a>
        </div>
    </div>

    <!-- Informations supplémentaires -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-gradient-to-r from-green-50 to-teal-50 rounded-2xl p-6">
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-line text-white text-xl"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800 mb-1">Performance générale</h4>
                    <p class="text-sm text-gray-600">
                        @php
                            $moyenneGenerale = collect($moyennesParMatiere)->avg('moyenne') ?? 0;
                        @endphp
                        @if($moyenneGenerale >= 14)
                            Excellente performance ! La moyenne générale est de {{ $moyenneGenerale }}/20.
                        @elseif($moyenneGenerale >= 12)
                            Bonne performance. Continuez sur cette lancée.
                        @elseif($moyenneGenerale >= 10)
                            Performance satisfaisante. Des efforts sont encore possibles.
                        @else
                            Des efforts sont nécessaires pour améliorer les résultats.
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-2xl p-6">
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-lightbulb text-white text-xl"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800 mb-1">Objectifs du trimestre</h4>
                    <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                        <li>Saisir toutes les notes avant la fin du trimestre</li>
                        <li>Maintenir un taux de présence ≥ 85%</li>
                        <li>Améliorer la moyenne générale de 2 points</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique des moyennes par matière
    var moyennesCtx = document.getElementById('moyennesChart')?.getContext('2d');
    if (moyennesCtx && moyennesCtx.canvas) {
        var matieres = @json(array_column($moyennesParMatiere, 'matiere'));
        var moyennes = @json(array_column($moyennesParMatiere, 'moyenne'));
        
        if (matieres.length > 0) {
            new Chart(moyennesCtx, {
                type: 'bar',
                data: {
                    labels: matieres,
                    datasets: [{
                        label: 'Moyenne (/20)',
                        data: moyennes,
                        backgroundColor: 'rgba(59, 130, 246, 0.6)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 20,
                            ticks: {
                                stepSize: 5
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.raw + ' / 20';
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // Graphique des présences par classe
    var presencesCtx = document.getElementById('presencesChart')?.getContext('2d');
    if (presencesCtx && presencesCtx.canvas) {
        var classes = @json(array_column($tauxPresenceParClasse, 'classe'));
        var tauxPresences = @json(array_column($tauxPresenceParClasse, 'taux'));
        
        if (classes.length > 0) {
            new Chart(presencesCtx, {
                type: 'doughnut',
                data: {
                    labels: classes,
                    datasets: [{
                        data: tauxPresences,
                        backgroundColor: [
                            'rgb(34, 197, 94)',
                            'rgb(59, 130, 246)',
                            'rgb(168, 85, 247)',
                            'rgb(249, 115, 22)',
                            'rgb(236, 72, 153)'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.raw + '%';
                                }
                            }
                        }
                    }
                }
            });
        }
    }
});
</script>
@endpush

@push('styles')
<style>
    .animate-float {
        animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
    
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }
    
    .group-hover\:scale-110:hover {
        transform: scale(1.1);
    }
</style>
@endpush
@endsection