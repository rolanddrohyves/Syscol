{{-- resources/views/comptable/rapports/mensuel.blade.php --}}
@extends('layouts.app')

@section('title', 'Rapport mensuel - SYSCOL')
@section('page-title', 'Rapport mensuel')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Filtres -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('comptable.rapports.mensuel') }}" class="flex flex-col sm:flex-row gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mois</label>
                <select name="mois" class="px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $mois == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate(null, $i, 1)->locale('fr')->monthName }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Année</label>
                <select name="annee" class="px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                    @for($i = date('Y'); $i >= date('Y')-5; $i--)
                        <option value="{{ $i }}" {{ $annee == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Générer
                </button>
                <a href="{{ route('comptable.rapports.export', ['type' => 'mensuel', 'mois' => $mois, 'annee' => $annee]) }}" 
                   class="px-6 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors ml-2">
                    <i class="fas fa-download mr-2"></i>Exporter CSV
                </a>
            </div>
        </form>
    </div>

    <!-- En-tête du rapport -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <div class="text-center">
            <h2 class="text-2xl font-bold text-gray-800">RAPPORT MENSUEL</h2>
            <p class="text-gray-500">{{ ucfirst($nomMois) }} {{ $annee }}</p>
        </div>
    </div>

    <!-- Cartes récapitulatives -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">TOTAL PAIEMENTS</p>
                    <p class="text-3xl font-bold">{{ number_format($totalPaiements, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-arrow-down text-white text-xl"></i>
                </div>
            </div>
            <p class="text-green-100 text-xs mt-2">{{ $paiements->count() }} transaction(s)</p>
        </div>

        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm">TOTAL DÉPENSES</p>
                    <p class="text-3xl font-bold">{{ number_format($totalDepenses, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-arrow-up text-white text-xl"></i>
                </div>
            </div>
            <p class="text-red-100 text-xs mt-2">{{ $depenses->count() }} dépense(s)</p>
        </div>

        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-indigo-100 text-sm">SOLDE DU MOIS</p>
                    <p class="text-3xl font-bold">{{ number_format($totalPaiements - $totalDepenses, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-wallet text-white text-xl"></i>
                </div>
            </div>
            <p class="text-indigo-100 text-xs mt-2">Paiements - Dépenses</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Détail des paiements -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-green-50 border-b border-green-200">
                <h3 class="text-lg font-semibold text-green-800 flex items-center">
                    <i class="fas fa-list text-green-600 mr-2"></i>
                    Détail des paiements
                </h3>
            </div>
            
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Élève</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Frais</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($paiements as $paiement)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-sm">{{ $paiement->eleve->prenom }} {{ $paiement->eleve->nom }}</td>
                            <td class="px-4 py-3 text-sm">{{ $paiement->frais->libelle ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-medium text-green-600">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">Aucun paiement</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Détail des dépenses -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-red-50 border-b border-red-200">
                <h3 class="text-lg font-semibold text-red-800 flex items-center">
                    <i class="fas fa-list text-red-600 mr-2"></i>
                    Détail des dépenses
                </h3>
            </div>
            
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Libellé</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($depenses as $depense)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">{{ $depense->date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-sm">{{ $depense->libelle }}</td>
                            <td class="px-4 py-3 text-sm">{{ $depense->categorie ?? 'Non catégorisé' }}</td>
                            <td class="px-4 py-3 text-right font-medium text-red-600">{{ number_format($depense->montant, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">Aucune dépense</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Graphique d'évolution journalière -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-chart-line text-blue-600 mr-2"></i>
            Évolution journalière des paiements
        </h3>
        <div class="h-80">
            <canvas id="evolutionChart"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var paiementsParJour = {};
    @foreach($paiements as $paiement)
        var date = '{{ $paiement->date_paiement->format('d/m') }}';
        if (!paiementsParJour[date]) paiementsParJour[date] = 0;
        paiementsParJour[date] += {{ $paiement->montant }};
    @endforeach
    
    var labels = Object.keys(paiementsParJour);
    var data = Object.values(paiementsParJour);
    
    var ctx = document.getElementById('evolutionChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Paiements (FCFA)',
                data: data,
                backgroundColor: 'rgba(34, 197, 94, 0.6)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection