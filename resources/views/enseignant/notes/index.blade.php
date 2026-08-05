{{-- resources/views/enseignant/notes/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des notes - Enseignant')
@section('page-title', 'Gestion des notes')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Filtres -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('enseignant.notes') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Classe</label>
                <select name="classe_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Toutes les classes</option>
                    @foreach($classes as $classe)
                        <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                            {{ $classe->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Matière</label>
                <select name="matiere_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Toutes les matières</option>
                    @foreach($matieres as $matiere)
                        <option value="{{ $matiere->id }}" {{ request('matiere_id') == $matiere->id ? 'selected' : '' }}>
                            {{ $matiere->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Voir les notes
                </button>
            </div>
        </form>
    </div>

    @if(request('classe_id') && request('matiere_id'))
        @php
            $classe = \App\Models\Classe::find(request('classe_id'));
            $matiere = \App\Models\Matiere::find(request('matiere_id'));
        @endphp
        
        <!-- En-tête -->
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-white">{{ $classe->nom ?? 'Classe' }}</h2>
                    <p class="text-indigo-100">Matière: {{ $matiere->nom ?? 'Matière' }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('enseignant.notes.create', ['classe' => request('classe_id'), 'matiere' => request('matiere_id')]) }}" 
                       class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors">
                        <i class="fas fa-plus-circle mr-2"></i>Saisir/Modifier les notes
                    </a>
                    <a href="{{ route('enseignant.notes.export', ['classe_id' => request('classe_id'), 'matiere_id' => request('matiere_id')]) }}" 
                       class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors">
                        <i class="fas fa-download mr-2"></i>Exporter CSV
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        @if($notes->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-2xl shadow-sm p-4 text-center">
                <p class="text-sm text-gray-500">Nombre d'élèves</p>
                <p class="text-2xl font-bold text-gray-800">{{ $notes->count() }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm p-4 text-center">
                <p class="text-sm text-gray-500">Moyenne de la classe</p>
                <p class="text-2xl font-bold {{ $notes->avg('note') >= 10 ? 'text-green-600' : 'text-red-600' }}">
                    {{ round($notes->avg('note'), 2) }}/20
                </p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm p-4 text-center">
                <p class="text-sm text-gray-500">Note maximale</p>
                <p class="text-2xl font-bold text-green-600">{{ $notes->max('note') ?? 0 }}/20</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm p-4 text-center">
                <p class="text-sm text-gray-500">Note minimale</p>
                <p class="text-2xl font-bold text-red-600">{{ $notes->min('note') ?? 0 }}/20</p>
            </div>
        </div>

        <!-- Graphique de répartition -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-pie text-indigo-500 mr-2"></i>
                Répartition des notes
            </h3>
            <div class="h-64">
                <canvas id="notesChart"></canvas>
            </div>
        </div>
        @endif

        <!-- Tableau des notes -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-table text-indigo-500 mr-2"></i>
                    Liste des notes des élèves
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N°</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matricule</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom & Prénom</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Note /20</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Appréciation</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date d'évaluation</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($notes as $index => $note)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 text-sm font-mono text-gray-600">{{ $note->eleve->matricule ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center mr-2 text-white text-xs font-semibold">
                                        {{ substr($note->eleve->prenom ?? '', 0, 1) }}{{ substr($note->eleve->nom ?? '', 0, 1) }}
                                    </div>
                                    <span class="font-medium text-sm">{{ $note->eleve->prenom ?? '' }} {{ $note->eleve->nom ?? '' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-16 px-2 py-1 rounded-full text-sm font-bold {{ $note->note >= 10 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $note->note }}/20
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $note->appreciation ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $note->date_evaluation ? \Carbon\Carbon::parse($note->date_evaluation)->format('d/m/Y') : '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('enseignant.notes.edit', $note->id) }}" 
                                   class="text-indigo-600 hover:text-indigo-800 mr-3" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" 
                                        onclick="confirmerSuppression({{ $note->id }})" 
                                        class="text-red-600 hover:text-red-800" title="Supprimer">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <td>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-receipt text-4xl mb-2 text-gray-400"></i>
                                <p>Aucune note enregistrée pour cette classe et cette matière</p>
                                <a href="{{ route('enseignant.notes.create', ['classe' => request('classe_id'), 'matiere' => request('matiere_id')]) }}" 
                                   class="mt-2 inline-block text-indigo-600 hover:text-indigo-800">
                                    Commencer à saisir des notes →
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- Message de sélection -->
        <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-chalkboard text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">Sélectionnez une classe et une matière</h3>
            <p class="text-gray-500">Choisissez une classe et une matière pour voir et gérer les notes des élèves.</p>
        </div>
    @endif
</div>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function confirmerSuppression(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette note ? Cette action est irréversible.')) {
        const form = document.getElementById('deleteForm');
        form.action = '/enseignant/notes/' + id;
        form.submit();
    }
}

@if(request('classe_id') && request('matiere_id') && isset($notes) && $notes->count() > 0)
// Graphique de répartition des notes
const notesCtx = document.getElementById('notesChart')?.getContext('2d');
if (notesCtx) {
    const notes = @json($notes->pluck('note')->toArray());
    
    const repartition = {
        'Excellent (16-20)': 0,
        'Très bien (14-15.99)': 0,
        'Bien (12-13.99)': 0,
        'Passable (10-11.99)': 0,
        'Insuffisant (8-9.99)': 0,
        'Faible (<8)': 0
    };
    
    notes.forEach(note => {
        if (note >= 16) repartition['Excellent (16-20)']++;
        else if (note >= 14) repartition['Très bien (14-15.99)']++;
        else if (note >= 12) repartition['Bien (12-13.99)']++;
        else if (note >= 10) repartition['Passable (10-11.99)']++;
        else if (note >= 8) repartition['Insuffisant (8-9.99)']++;
        else repartition['Faible (<8)']++;
    });
    
    new Chart(notesCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(repartition),
            datasets: [{
                data: Object.values(repartition),
                backgroundColor: [
                    'rgb(34, 197, 94)',
                    'rgb(59, 130, 246)',
                    'rgb(168, 85, 247)',
                    'rgb(249, 115, 22)',
                    'rgb(245, 158, 11)',
                    'rgb(239, 68, 68)'
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
                            const label = context.label || '';
                            const value = context.raw;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return `${label}: ${value} élève(s) (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}
@endif
</script>
@endpush
@endsection