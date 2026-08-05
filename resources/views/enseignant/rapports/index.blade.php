{{-- resources/views/enseignant/rapports/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Rapports - Enseignant')
@section('page-title', 'Rapports et statistiques')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Filtres -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-filter text-indigo-500 mr-2"></i>
            Filtres
        </h3>
        <form method="GET" action="{{ route('enseignant.rapports.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Période</label>
                <select name="periode" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="mois" {{ $periode == 'mois' ? 'selected' : '' }}>Ce mois</option>
                    <option value="semaine" {{ $periode == 'semaine' ? 'selected' : '' }}>Cette semaine</option>
                    <option value="trimestre" {{ $periode == 'trimestre' ? 'selected' : '' }}>Ce trimestre</option>
                    <option value="annee" {{ $periode == 'annee' ? 'selected' : '' }}>Cette année</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Classe</label>
                <select name="classe_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Toutes les classes</option>
                    @foreach($classes as $classe)
                        <option value="{{ $classe->id }}" {{ $classeId == $classe->id ? 'selected' : '' }}>
                            {{ $classe->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Matière</label>
                <select name="matiere_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Toutes les matières</option>
                    @foreach($matieres as $matiere)
                        <option value="{{ $matiere->id }}" {{ $matiereId == $matiere->id ? 'selected' : '' }}>
                            {{ $matiere->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date début</label>
                <input type="date" name="date_debut" value="{{ request('date_debut', $dateDebut->format('Y-m-d')) }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date fin</label>
                <input type="date" name="date_fin" value="{{ request('date_fin', $dateFin->format('Y-m-d')) }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            
            <div class="md:col-span-5 flex justify-end">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Générer le rapport
                </button>
                <a href="{{ route('enseignant.rapports.export', request()->all()) }}" 
                   class="ml-2 px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-download mr-2"></i>Exporter CSV
                </a>
            </div>
        </form>
    </div>

    <!-- Cartes statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500">
            <p class="text-sm text-gray-500">Total notes</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total_notes'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500">
            <p class="text-sm text-gray-500">Moyenne générale</p>
            <p class="text-2xl font-bold {{ $stats['moyenne_generale'] >= 10 ? 'text-green-600' : 'text-red-600' }}">
                {{ $stats['moyenne_generale'] }}/20
            </p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-orange-500">
            <p class="text-sm text-gray-500">Meilleure note</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['meilleure_note'] }}/20</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-red-500">
            <p class="text-sm text-gray-500">Plus faible note</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['plus_faible_note'] }}/20</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-purple-500">
            <p class="text-sm text-gray-500">Taux de réussite</p>
            <p class="text-2xl font-bold text-purple-600">{{ $stats['taux_reussite'] }}%</p>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Distribution des notes -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-pie text-indigo-500 mr-2"></i>
                Distribution des notes
            </h3>
            <canvas id="distributionChart" height="250"></canvas>
            <div class="mt-4 grid grid-cols-2 gap-2 text-sm">
                <div class="flex justify-between">
                    <span><span class="w-3 h-3 bg-green-500 inline-block rounded-full mr-1"></span> Excellent</span>
                    <span class="font-bold">{{ $distribution['excellent'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span><span class="w-3 h-3 bg-blue-500 inline-block rounded-full mr-1"></span> Très bien</span>
                    <span class="font-bold">{{ $distribution['tres_bien'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span><span class="w-3 h-3 bg-purple-500 inline-block rounded-full mr-1"></span> Bien</span>
                    <span class="font-bold">{{ $distribution['bien'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span><span class="w-3 h-3 bg-yellow-500 inline-block rounded-full mr-1"></span> Passable</span>
                    <span class="font-bold">{{ $distribution['passable'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span><span class="w-3 h-3 bg-orange-500 inline-block rounded-full mr-1"></span> Insuffisant</span>
                    <span class="font-bold">{{ $distribution['insuffisant'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span><span class="w-3 h-3 bg-red-500 inline-block rounded-full mr-1"></span> Faible</span>
                    <span class="font-bold">{{ $distribution['faible'] }}</span>
                </div>
            </div>
        </div>

        <!-- Évolution hebdomadaire -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-line text-green-500 mr-2"></i>
                Évolution des moyennes (6 semaines)
            </h3>
            <canvas id="evolutionChart" height="250"></canvas>
        </div>
    </div>

    <!-- Performance par classe -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-chalkboard text-indigo-500 mr-2"></i>
                Performance par classe
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Classe</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Moyenne</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Taux réussite</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Notes saisies</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total élèves</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($performanceClasses as $perf)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $perf['classe'] }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 rounded-full text-sm font-bold {{ $perf['moyenne'] >= 10 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $perf['moyenne'] }}/20
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center">
                                <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $perf['taux_reussite'] ?? 0 }}%"></div>
                                </div>
                                <span class="text-sm">{{ $perf['taux_reussite'] ?? 0 }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">{{ $perf['notes_saisies'] ?? 0 }}/{{ $perf['total_eleves'] * 5 ?? 0 }}</td>
                        <td class="px-6 py-4 text-center">{{ $perf['total_eleves'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            Aucune donnée disponible
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Dernières notes saisies -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-history text-purple-500 mr-2"></i>
                Dernières notes saisies
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Élève</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Classe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matière</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Note</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Appréciation</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($dernieresNotes ?? [] as $note)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $note->date_evaluation ? \Carbon\Carbon::parse($note->date_evaluation)->format('d/m/Y') : '-' }}</td>
                        <td class="px-6 py-4 font-medium">{{ $note->eleve->prenom ?? '' }} {{ $note->eleve->nom ?? '' }}</td>
                        <td class="px-6 py-4 text-sm">{{ $note->eleve->classe->nom ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">{{ $note->matiere->nom ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 rounded-full text-sm font-bold {{ $note->note >= 10 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $note->note }}/20
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $note->appreciation ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            Aucune note enregistrée
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique de distribution des notes
    const distributionCtx = document.getElementById('distributionChart')?.getContext('2d');
    if (distributionCtx) {
        new Chart(distributionCtx, {
            type: 'bar',
            data: {
                labels: ['Excellent', 'Très bien', 'Bien', 'Passable', 'Insuffisant', 'Faible'],
                datasets: [{
                    label: 'Nombre d\'élèves',
                    data: [
                        {{ $distribution['excellent'] }},
                        {{ $distribution['tres_bien'] }},
                        {{ $distribution['bien'] }},
                        {{ $distribution['passable'] }},
                        {{ $distribution['insuffisant'] }},
                        {{ $distribution['faible'] }}
                    ],
                    backgroundColor: [
                        'rgb(34, 197, 94)',
                        'rgb(59, 130, 246)',
                        'rgb(168, 85, 247)',
                        'rgb(234, 179, 8)',
                        'rgb(249, 115, 22)',
                        'rgb(239, 68, 68)'
                    ],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: (ctx) => `${ctx.raw} élève(s)` } }
                }
            }
        });
    }

    // Graphique d'évolution
    const evolutionCtx = document.getElementById('evolutionChart')?.getContext('2d');
    if (evolutionCtx) {
        new Chart(evolutionCtx, {
            type: 'line',
            data: {
                labels: @json(array_column($evolutionHebdo, 'semaine')),
                datasets: [{
                    label: 'Moyenne /20',
                    data: @json(array_column($evolutionHebdo, 'moyenne')),
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: { min: 0, max: 20, ticks: { stepSize: 5 } }
                }
            }
        });
    }
});
</script>
@endpush
@endsection