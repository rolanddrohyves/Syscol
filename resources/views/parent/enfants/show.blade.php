{{-- resources/views/parent/enfants/show.blade.php --}}
@extends('layouts.app')

@section('title', $enfant->prenom . ' ' . $enfant->nom)
@section('page-title', $enfant->prenom . ' ' . $enfant->nom)

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- En-tête -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <div class="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center">
                        @if($enfant->photo)
                            <img src="{{ Storage::url($enfant->photo) }}" alt="{{ $enfant->prenom }}" class="w-20 h-20 rounded-full object-cover">
                        @else
                            <i class="fas fa-user-graduate text-white text-4xl"></i>
                        @endif
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">{{ $enfant->prenom }} {{ $enfant->nom }}</h2>
                        <p class="text-indigo-100">Matricule: {{ $enfant->matricule ?? 'N/A' }} | Classe: {{ $enfant->classe->nom ?? 'Non définie' }}</p>
                    </div>
                </div>
                <a href="{{ route('parent.enfants.index') }}" class="px-4 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30">
                    <i class="fas fa-arrow-left mr-2"></i> Retour
                </a>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informations personnelles -->
                <div class="border rounded-xl p-4">
                    <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-user-circle text-indigo-500 mr-2"></i>
                        Informations personnelles
                    </h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Nom complet</span>
                            <span class="font-medium">{{ $enfant->prenom }} {{ $enfant->nom }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Date de naissance</span>
                            <span class="font-medium">{{ $enfant->date_naissance ? \Carbon\Carbon::parse($enfant->date_naissance)->format('d/m/Y') : '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Lieu de naissance</span>
                            <span class="font-medium">{{ $enfant->lieu_naissance ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Sexe</span>
                            <span class="font-medium">{{ $enfant->sexe == 'M' ? 'Masculin' : 'Féminin' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Date d'inscription</span>
                            <span class="font-medium">{{ $enfant->date_inscription ? \Carbon\Carbon::parse($enfant->date_inscription)->format('d/m/Y') : '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Informations financières -->
                <div class="border rounded-xl p-4">
                    <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-money-bill-wave text-green-500 mr-2"></i>
                        Situation financière
                    </h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Total frais</span>
                            <span class="font-medium">{{ number_format($enfant->montant_total_frais ?? 0, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Déjà payé</span>
                            <span class="font-medium text-green-600">{{ number_format($enfant->montant_paye ?? 0, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Reste à payer</span>
                            <span class="font-medium text-red-600">{{ number_format($enfant->montant_restant ?? 0, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="mt-2">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $enfant->montant_total_frais > 0 ? round(($enfant->montant_paye / $enfant->montant_total_frais) * 100, 2) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <a href="{{ route('parent.notes.enfant', $enfant->id) }}" 
           class="flex flex-col items-center p-4 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition-colors">
            <i class="fas fa-star text-2xl text-indigo-600 mb-2"></i>
            <span class="text-sm font-medium">Notes</span>
        </a>
        <a href="{{ route('parent.emploi_temps.enfant', $enfant->id) }}" 
           class="flex flex-col items-center p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors">
            <i class="fas fa-calendar-alt text-2xl text-blue-600 mb-2"></i>
            <span class="text-sm font-medium">Emploi du temps</span>
        </a>
        <a href="{{ route('parent.absences.enfant', $enfant->id) }}" 
           class="flex flex-col items-center p-4 bg-orange-50 rounded-xl hover:bg-orange-100 transition-colors">
            <i class="fas fa-calendar-times text-2xl text-orange-600 mb-2"></i>
            <span class="text-sm font-medium">Absences</span>
        </a>
        <a href="{{ route('parent.paiements.enfant', $enfant->id) }}" 
           class="flex flex-col items-center p-4 bg-green-50 rounded-xl hover:bg-green-100 transition-colors">
            <i class="fas fa-receipt text-2xl text-green-600 mb-2"></i>
            <span class="text-sm font-medium">Paiements</span>
        </a>
    </div>

    <!-- Évolution des notes -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-chart-line text-blue-500 mr-2"></i>
            Évolution des notes par trimestre
        </h3>
        <canvas id="notesEvolutionChart" height="200"></canvas>
    </div>

    <!-- Dernières absences -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-calendar-times text-red-500 mr-2"></i>
                Dernières absences
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motif</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($enfant->absences->sortByDesc('date')->take(5) as $absence)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm">{{ $absence->date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-sm">{{ $absence->motif ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 text-xs rounded-full {{ $absence->justifiee ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $absence->justifiee ? 'Justifiée' : 'Non justifiée' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                            Aucune absence enregistrée
                        <tr>
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
    const ctx = document.getElementById('notesEvolutionChart')?.getContext('2d');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Trimestre 1', 'Trimestre 2', 'Trimestre 3'],
                datasets: [{
                    label: 'Moyenne générale',
                    data: [{{ $enfant->notes()->where('trimestre_id', 1)->avg('note') ?? 0 }},
                           {{ $enfant->notes()->where('trimestre_id', 2)->avg('note') ?? 0 }},
                           {{ $enfant->notes()->where('trimestre_id', 3)->avg('note') ?? 0 }}],
                    borderColor: 'rgb(99, 102, 241)',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: { y: { min: 0, max: 20, ticks: { stepSize: 5 } } }
            }
        });
    }
});
</script>
@endpush
@endsection