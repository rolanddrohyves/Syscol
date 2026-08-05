{{-- resources/views/comptable/rapports/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Rapports financiers - SYSCOL')
@section('page-title', 'Rapports financiers')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-chart-line text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Rapports financiers</h2>
                <p class="text-sm text-gray-500">Synthèse des paiements et dépenses</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <button onclick="window.print()" class="px-4 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-colors">
                <i class="fas fa-print mr-2"></i> Imprimer
            </button>
        </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total des paiements</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($totalPaiements ?? 0, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total des dépenses</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($totalDepenses ?? 0, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-red-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Solde</p>
                    <p class="text-2xl font-bold {{ ($solde ?? 0) >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                        {{ number_format(abs($solde ?? 0), 0, ',', ' ') }} FCFA
                        @if(($solde ?? 0) >= 0)
                            <span class="text-sm text-green-600">(Crédit)</span>
                        @else
                            <span class="text-sm text-red-600">(Débit)</span>
                        @endif
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Impays</p>
                    <p class="text-2xl font-bold text-orange-600">{{ number_format($totalImpayes ?? 0, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-orange-600 text-xl"></i>
                </div>
            </div>
            <small class="text-gray-500">{{ $nombreImpayes ?? 0 }} élèves concernés</small>
        </div>
    </div>

    <!-- Évolution des paiements par mois -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">📈 Évolution des paiements</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Période</th>
                            <th class="px-4 py-3 text-right text-sm font-medium text-gray-500">Montant</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($paiementsParMois ?? [] as $item)
                            <tr>
                                <td class="px-4 py-3">{{ $item->annee }} - Mois {{ $item->mois }}</td>
                                <td class="px-4 py-3 text-right font-medium">{{ number_format($item->total, 0, ',', ' ') }} FCFA</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-8 text-center text-gray-500">Aucune donnée disponible</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-100">
                        <tr>
                            <td class="px-4 py-3 font-semibold">TOTAL</td>
                            <td class="px-4 py-3 text-right font-bold">{{ number_format($paiementsParMois->sum('total') ?? 0, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Répartition par mode de paiement -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">💳 Répartition par mode de paiement</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($paiementsParMode ?? [] as $item)
                    @php
                        $pourcentage = ($totalPaiements ?? 1) > 0 ? round(($item->total / $totalPaiements) * 100, 1) : 0;
                        $couleurs = [
                            'especes' => 'green',
                            'cheque' => 'blue',
                            'virement' => 'purple',
                            'carte' => 'indigo',
                            'mobile_money' => 'orange'
                        ];
                        $couleur = $couleurs[$item->mode_paiement] ?? 'gray';
                    @endphp
                    <div class="bg-{{ $couleur }}-50 rounded-xl p-4">
                        <p class="text-sm text-{{ $couleur }}-600 mb-2">
                            @switch($item->mode_paiement)
                                @case('especes') 💵 Espèces @break
                                @case('cheque') 📝 Chèque @break
                                @case('virement') 🏦 Virement @break
                                @case('carte') 💳 Carte bancaire @break
                                @case('mobile_money') 📱 Mobile Money @break
                                @default {{ $item->mode_paiement }}
                            @endswitch
                        </p>
                        <p class="text-2xl font-bold text-{{ $couleur }}-700">{{ number_format($item->total, 0, ',', ' ') }} FCFA</p>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                            <div class="bg-{{ $couleur }}-500 h-1.5 rounded-full" style="width: {{ $pourcentage }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $pourcentage }}% du total</p>
                        <p class="text-xs text-gray-400">{{ $item->nombre }} transaction(s)</p>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-8 text-gray-500">Aucune donnée disponible</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Top 10 des élèves payeurs -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">🏆 Top 10 des élèves payeurs</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Élève</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Classe</th>
                        <th class="px-4 py-3 text-right text-sm font-medium text-gray-500">Montant total payé</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($topEleves ?? [] as $item)
                        <tr>
                            <td class="px-4 py-3">{{ $item->eleve->prenom }} {{ $item->eleve->nom }}</td>
                            <td class="px-4 py-3">{{ $item->eleve->classe->nom ?? 'Non définie' }}</td>
                            <td class="px-4 py-3 text-right font-medium text-green-600">{{ number_format($item->total, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-500">Aucune donnée disponible</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paiements par classe -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">🏫 Paiements par classe</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Classe</th>
                        <th class="px-4 py-3 text-right text-sm font-medium text-gray-500">Montant total</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-500">Nombre de paiements</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($paiementsParClasse ?? [] as $classe => $data)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $classe }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($data['total'], 0, ',', ' ') }} FCFA</td>
                            <td class="px-4 py-3 text-center">{{ $data['nombre'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-500">Aucune donnée disponible</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection