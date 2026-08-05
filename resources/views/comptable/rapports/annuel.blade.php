{{-- resources/views/comptable/rapports/annuel.blade.php --}}
@extends('layouts.app')

@section('title', 'Rapport annuel - SYSCOL')
@section('page-title', 'Rapport annuel')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Filtres -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('comptable.rapports.annuel') }}" class="flex flex-col sm:flex-row gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Année</label>
                <select name="annee" class="px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                    @for($i = date('Y'); $i >= date('Y')-10; $i--)
                        <option value="{{ $i }}" {{ $annee == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Générer
                </button>
                <a href="{{ route('comptable.rapports.export', ['type' => 'annuel', 'annee' => $annee]) }}" 
                   class="px-6 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors ml-2">
                    <i class="fas fa-download mr-2"></i>Exporter CSV
                </a>
            </div>
        </form>
    </div>

    <!-- En-tête du rapport -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <div class="text-center">
            <h2 class="text-2xl font-bold text-gray-800">RAPPORT ANNUEL</h2>
            <p class="text-gray-500">Année {{ $annee }}</p>
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
        </div>

        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-indigo-100 text-sm">SOLDE ANNUEL</p>
                    <p class="text-3xl font-bold">{{ number_format($totalPaiements - $totalDepenses, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-wallet text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique comparatif mensuel -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
            Comparatif mensuel des flux financiers
        </h3>
        <div class="h-96">
            <canvas id="comparatifChart"></canvas>
        </div>
    </div>

    <!-- Tableau récapitulatif mensuel -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-table text-blue-600 mr-2"></i>
                Récapitulatif mensuel
            </h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mois</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Paiements</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Dépenses</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Solde</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Évolution</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @php
                        // Convertir les collections en tableaux associatifs pour un accès facile
                        $paiementsByMois = [];
                        foreach($paiementsParMois as $item) {
                            $paiementsByMois[$item->mois] = $item->total;
                        }
                        
                        $depensesByMois = [];
                        foreach($depensesParMois as $item) {
                            $depensesByMois[$item->mois] = $item->total;
                        }
                        
                        $paiementPrecedent = 0;
                    @endphp
                    
                    @for($mois = 1; $mois <= 12; $mois++)
                        @php
                            $nomMois = \Carbon\Carbon::createFromDate($annee, $mois, 1)->locale('fr')->monthName;
                            $paiementMois = $paiementsByMois[$mois] ?? 0;
                            $depenseMois = $depensesByMois[$mois] ?? 0;
                            $soldeMois = $paiementMois - $depenseMois;
                            $evolution = $mois > 1 ? $paiementMois - $paiementPrecedent : 0;
                            $paiementPrecedent = $paiementMois;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ $nomMois }}</td>
                            <td class="px-4 py-3 text-right text-green-600">{{ number_format($paiementMois, 0, ',', ' ') }} FCFA</td>
                            <td class="px-4 py-3 text-right text-red-600">{{ number_format($depenseMois, 0, ',', ' ') }} FCFA</td>
                            <td class="px-4 py-3 text-right font-bold {{ $soldeMois >= 0 ? 'text-green-700' : 'text-red-700' }}">
                                {{ number_format($soldeMois, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($evolution > 0)
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">
                                        <i class="fas fa-arrow-up mr-1"></i>+{{ number_format($evolution, 0, ',', ' ') }}
                                    </span>
                                @elseif($evolution < 0)
                                    <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded-full">
                                        <i class="fas fa-arrow-down mr-1"></i>{{ number_format($evolution, 0, ',', ' ') }}
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded-full">=</span>
                                @endif
                            </td>
                        </tr>
                    @endfor
                </tbody>
                <tfoot class="bg-gray-100 font-bold">
                    <tr>
                        <td class="px-4 py-3">TOTAL</td>
                        <td class="px-4 py-3 text-right text-green-700">{{ number_format($totalPaiements, 0, ',', ' ') }} FCFA</td>
                        <td class="px-4 py-3 text-right text-red-700">{{ number_format($totalDepenses, 0, ',', ' ') }} FCFA</td>
                        <td class="px-4 py-3 text-right">{{ number_format($totalPaiements - $totalDepenses, 0, ',', ' ') }} FCFA</td>
                        <td class="px-4 py-3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Indicateurs de performance -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h4 class="font-semibold text-gray-800 mb-3"><i class="fas fa-chart-line"></i> Indicateurs clés</h4>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Moyenne mensuelle des paiements</span>
                    <span class="font-bold">{{ number_format($totalPaiements / 12, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Mois le plus performant</span>
                    <span class="font-bold text-green-600">
                        @php
                            $maxMois = null;
                            $maxValeur = 0;
                            foreach($paiementsByMois as $mois => $valeur) {
                                if ($valeur > $maxValeur) {
                                    $maxValeur = $valeur;
                                    $maxMois = $mois;
                                }
                            }
                        @endphp
                        @if($maxMois)
                            {{ \Carbon\Carbon::createFromDate($annee, $maxMois, 1)->locale('fr')->monthName }}
                            ({{ number_format($maxValeur, 0, ',', ' ') }} FCFA)
                        @else
                            -
                        @endif
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Ratio dépenses/paiements</span>
                    <span class="font-bold">{{ $totalPaiements > 0 ? round(($totalDepenses / $totalPaiements) * 100, 2) : 0 }}%</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h4 class="font-semibold text-gray-800 mb-3"><i class="fas fa-lightbulb"></i> Recommandations</h4>
            <ul class="space-y-2 text-sm text-gray-600">
                @php
                    $ratio = $totalPaiements > 0 ? ($totalDepenses / $totalPaiements) * 100 : 0;
                @endphp
                @if($ratio > 80)
                    <li class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-2 mt-0.5"></i>
                        Le ratio dépenses/paiements est élevé ({{ round($ratio, 1) }}%). Envisagez de réduire les dépenses.
                    </li>
                @elseif($ratio > 60)
                    <li class="flex items-start">
                        <i class="fas fa-chart-line text-blue-500 mr-2 mt-0.5"></i>
                        Bon équilibre financier. Continuez à surveiller les dépenses.
                    </li>
                @else
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                        Excellente gestion ! Le ratio dépenses/paiements est optimal.
                    </li>
                @endif
                
                @php
                    $moisFaible = null;
                    $moinsValeur = PHP_INT_MAX;
                    foreach($paiementsByMois as $mois => $valeur) {
                        if ($valeur < $moinsValeur && $valeur > 0) {
                            $moinsValeur = $valeur;
                            $moisFaible = $mois;
                        }
                    }
                @endphp
                @if($moisFaible && $moinsValeur < ($totalPaiements / 12) * 0.5)
                    <li class="flex items-start">
                        <i class="fas fa-lightbulb text-yellow-500 mr-2 mt-0.5"></i>
                        Le mois de {{ \Carbon\Carbon::createFromDate($annee, $moisFaible, 1)->locale('fr')->monthName }}
                        a été moins performant. Analysez les causes.
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var moisLabels = [];
    var paiementsData = [];
    var depensesData = [];
    
    // Récupérer les données depuis PHP
    var paiementsParMois = @json($paiementsParMois);
    var depensesParMois = @json($depensesParMois);
    
    // Créer des tableaux associatifs pour un accès facile
    var paiementsByMois = {};
    var depensesByMois = {};
    
    for (var i = 0; i < paiementsParMois.length; i++) {
        paiementsByMois[paiementsParMois[i].mois] = paiementsParMois[i].total;
    }
    
    for (var i = 0; i < depensesParMois.length; i++) {
        depensesByMois[depensesParMois[i].mois] = depensesParMois[i].total;
    }
    
    // Générer les 12 mois
    for (var mois = 1; mois <= 12; mois++) {
        var date = new Date({{ $annee }}, mois - 1, 1);
        moisLabels.push(date.toLocaleString('fr', { month: 'short' }));
        paiementsData.push(paiementsByMois[mois] || 0);
        depensesData.push(depensesByMois[mois] || 0);
    }
    
    var ctx = document.getElementById('comparatifChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: moisLabels,
            datasets: [
                {
                    label: 'Paiements',
                    data: paiementsData,
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Dépenses',
                    data: depensesData,
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + new Intl.NumberFormat('fr-FR').format(context.raw) + ' FCFA';
                        }
                    }
                }
            },
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