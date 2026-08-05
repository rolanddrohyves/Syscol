{{-- resources/views/comptable/rapports/journalier.blade.php --}}
@extends('layouts.app')

@section('title', 'Rapport journalier - SYSCOL')
@section('page-title', 'Rapport journalier')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Filtres -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('comptable.rapports.journalier') }}" class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                <input type="date" name="date" value="{{ $date }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Générer
                </button>
                <a href="{{ route('comptable.rapports.export', ['type' => 'journalier', 'date' => $date]) }}" 
                   class="px-6 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors ml-2">
                    <i class="fas fa-download mr-2"></i>Exporter CSV
                </a>
            </div>
        </form>
    </div>

    <!-- En-tête du rapport -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <div class="text-center">
            <h2 class="text-2xl font-bold text-gray-800">RAPPORT JOURNALIER</h2>
            <p class="text-gray-500">Du {{ \Carbon\Carbon::parse($date)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Paiements du jour -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-green-50 border-b border-green-200">
                <h3 class="text-lg font-semibold text-green-800 flex items-center">
                    <i class="fas fa-arrow-down text-green-600 mr-2"></i>
                    Paiements du jour
                    <span class="ml-3 px-2 py-1 bg-green-200 text-green-800 rounded-full text-xs">
                        {{ $paiements->count() }} transaction(s)
                    </span>
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Heure</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Élève</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Frais</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Mode</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($paiements as $paiement)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $paiement->date_paiement->format('H:i') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-500 to-emerald-500 flex items-center justify-center mr-2 text-white text-xs font-semibold">
                                        {{ substr($paiement->eleve->prenom, 0, 1) }}{{ substr($paiement->eleve->nom, 0, 1) }}
                                    </div>
                                    <span class="text-sm">{{ $paiement->eleve->prenom }} {{ $paiement->eleve->nom }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $paiement->frais->libelle ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-medium text-green-600">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full">
                                    @switch($paiement->mode_paiement)
                                        @case('especes') 💵 Espèces @break
                                        @case('cheque') 📝 Chèque @break
                                        @case('virement') 💻 Virement @break
                                        @case('carte') 💳 Carte @break
                                        @case('mobile_money') 📱 Mobile Money @break
                                        @default {{ $paiement->mode_paiement }}
                                    @endswitch
                                </span>
                            </td>
                        </tr>
                        @empty
                        </tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-receipt text-4xl mb-2 text-gray-400"></i>
                                <p>Aucun paiement enregistré ce jour</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-100 font-bold">
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-right">TOTAL PAIEMENTS</td>
                            <td class="px-4 py-3 text-right text-green-700">{{ number_format($totalPaiements, 0, ',', ' ') }} FCFA</td>
                            <td class="px-4 py-3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Dépenses du jour -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-red-50 border-b border-red-200">
                <h3 class="text-lg font-semibold text-red-800 flex items-center">
                    <i class="fas fa-arrow-up text-red-600 mr-2"></i>
                    Dépenses du jour
                    <span class="ml-3 px-2 py-1 bg-red-200 text-red-800 rounded-full text-xs">
                        {{ $depenses->count() }} dépense(s)
                    </span>
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Heure</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Libellé</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant</th>
                        </td>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($depenses as $depense)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $depense->date->format('H:i') }}</td>
                            <td class="px-4 py-3 text-sm">{{ $depense->libelle }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded-full">{{ $depense->categorie ?? 'Non catégorisé' }}</span>
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-red-600">{{ number_format($depense->montant, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-shopping-cart text-4xl mb-2 text-gray-400"></i>
                                <p>Aucune dépense enregistrée ce jour</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-100 font-bold">
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-right">TOTAL DÉPENSES</td>
                            <td class="px-4 py-3 text-right text-red-700">{{ number_format($totalDepenses, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Solde du jour -->
    <div class="mt-6">
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-indigo-100 text-sm">SOLDE DU JOUR</p>
                    <p class="text-3xl font-bold text-white">{{ number_format($totalPaiements - $totalDepenses, 0, ',', ' ') }} FCFA</p>
                    <p class="text-indigo-100 text-sm mt-1">Paiements - Dépenses</p>
                </div>
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-wallet text-white text-3xl"></i>
                </div>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-4">
                <div class="bg-white/10 rounded-xl p-3">
                    <p class="text-indigo-100 text-xs">Total encaissé</p>
                    <p class="text-white font-bold">{{ number_format($totalPaiements, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="bg-white/10 rounded-xl p-3">
                    <p class="text-indigo-100 text-xs">Total décaissé</p>
                    <p class="text-white font-bold">{{ number_format($totalDepenses, 0, ',', ' ') }} FCFA</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection