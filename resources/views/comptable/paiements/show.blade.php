{{-- resources/views/comptable/paiements/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Détails du paiement - SYSCOL')
@section('page-title', 'Détails du paiement')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <!-- En-tête avec statut -->
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-receipt text-white text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Reçu de paiement</h1>
                        <p class="text-green-100">Référence: {{ $paiement->reference ?? 'N/A' }}</p>
                    </div>
                </div>
                <div>
                    <span class="px-4 py-2 text-sm rounded-full 
                        @if($paiement->statut == 'paye') bg-green-500 text-white
                        @elseif($paiement->statut == 'en_attente') bg-yellow-500 text-white
                        @else bg-red-500 text-white
                        @endif">
                        @if($paiement->statut == 'paye') Payé
                        @elseif($paiement->statut == 'en_attente') En attente
                        @else ❌ Annulé
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="px-8 py-4 bg-gray-50 border-b border-gray-200 flex justify-end space-x-3">
            <a href="{{ route('comptable.paiements.edit', $paiement->id) }}" 
               class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                <i class="fas fa-edit mr-2"></i>Modifier
            </a>
            <a href="{{ route('comptable.paiements.recu', $paiement->id) }}" target="_blank"
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-print mr-2"></i>Imprimer le reçu
            </a>
            <a href="{{ route('comptable.paiements.index') }}" 
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>

        <!-- Corps du reçu -->
        <div class="p-8">
            <!-- En-tête du reçu -->
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-800">{{ config('app.name', 'SYSCOL') }}</h2>
                <p class="text-gray-500">Reçu de paiement officiel</p>
            </div>

            <!-- Informations paiement -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gray-50 p-4 rounded-xl">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Date du paiement</h3>
                    <p class="text-lg font-semibold text-gray-800">{{ $paiement->date_paiement->format('d/m/Y') }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-xl">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Mode de paiement</h3>
                    <p class="text-lg font-semibold text-gray-800">
                        @switch($paiement->mode_paiement)
                            @case('especes') Espèces @break
                            @case('cheque') Chèque @break
                            @case('virement') Virement bancaire @break
                            @case('carte') Carte bancaire @break
                            @case('mobile_money') Mobile Money @break
                            @default {{ ucfirst($paiement->mode_paiement) }}
                        @endswitch
                    </p>
                </div>
                <div class="bg-gray-50 p-4 rounded-xl">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Statut</h3>
                    <p class="text-lg font-semibold">
                        @if($paiement->statut == 'paye')
                            <span class="text-green-600">✓ Payé</span>
                        @elseif($paiement->statut == 'partiel')
                            <span class="text-orange-600">Paiement partiel</span>
                        @elseif($paiement->statut == 'en_attente')
                            <span class="text-yellow-600">En attente</span>
                        @else
                            <span class="text-red-600">✗ Annulé</span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- Informations élève -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-user-graduate text-green-600 mr-2"></i>
                    Informations élève
                </h3>
                <div class="bg-green-50 rounded-xl p-6">
                    <div class="flex items-center">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-green-500 to-emerald-500 flex items-center justify-center mr-4 text-white font-bold text-2xl">
                            {{ substr($paiement->eleve->prenom, 0, 1) }}{{ substr($paiement->eleve->nom, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <p class="text-xl font-bold text-gray-800">{{ $paiement->eleve->prenom }} {{ $paiement->eleve->nom }}</p>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                                <div>
                                    <p class="text-sm text-gray-500">Classe</p>
                                    <p class="font-medium">{{ $paiement->eleve->classe->nom ?? 'Non définie' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Matricule</p>
                                    <p class="font-medium">{{ $paiement->eleve->matricule }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Année scolaire</p>
                                    <p class="font-medium">{{ $paiement->frais->anneeScolaire->libelle ?? date('Y') . '-' . (date('Y')+1) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Situation financière globale -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                    Situation financière
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 rounded-xl p-4 text-center">
                        <p class="text-sm text-blue-600">Total des frais</p>
                        <p class="text-2xl font-bold text-blue-700">
                            {{ number_format(isset($situation['total_general']) ? $situation['total_general'] : ($paiement->eleve->montant_total_frais ?? $paiement->montant), 0, ',', ' ') }} FCFA
                        </p>
                    </div>
                    <div class="bg-green-50 rounded-xl p-4 text-center">
                        <p class="text-sm text-green-600">Déjà payé</p>
                        <p class="text-2xl font-bold text-green-700">
                            {{ number_format(isset($situation['total_paye']) ? $situation['total_paye'] : ($paiement->eleve->montant_paye ?? $paiement->montant), 0, ',', ' ') }} FCFA
                        </p>
                    </div>
                    <div class="bg-orange-50 rounded-xl p-4 text-center">
                        <p class="text-sm text-orange-600">Reste à payer</p>
                        <p class="text-2xl font-bold text-orange-700">
                            {{ number_format(isset($situation['total_reste']) ? $situation['total_reste'] : ($paiement->eleve->montant_restant ?? 0), 0, ',', ' ') }} FCFA
                        </p>
                    </div>
                    <div class="bg-purple-50 rounded-xl p-4 text-center">
                        <p class="text-sm text-purple-600">Taux de paiement</p>
                        <p class="text-2xl font-bold text-purple-700">
                            {{ isset($situation['pourcentage_paye']) ? $situation['pourcentage_paye'] : 0 }}%
                        </p>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                            <div class="bg-purple-600 h-1.5 rounded-full" style="width: {{ isset($situation['pourcentage_paye']) ? $situation['pourcentage_paye'] : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Détail des frais par catégorie -->
            @if(isset($situation['par_frais']) && !empty($situation['par_frais']))
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-tags text-blue-600 mr-2"></i>
                    Détail des frais par catégorie
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Type de frais</th>
                                <th class="px-4 py-3 text-right text-sm font-medium text-gray-500">Total</th>
                                <th class="px-4 py-3 text-right text-sm font-medium text-gray-500">Payé</th>
                                <th class="px-4 py-3 text-right text-sm font-medium text-gray-500">Reste</th>
                                <th class="px-4 py-3 text-center text-sm font-medium text-gray-500">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($situation['par_frais'] as $type => $data)
                                @php
                                    $typeLabels = [
                                        'inscription' => 'Frais d\'inscription',
                                        'scolarite' => 'Scolarité',
                                        'cantine' => 'Cantine',
                                        'transport' => 'Transport',
                                        'sortie' => 'Sortie pédagogique',
                                        'autre' => 'Autres frais'
                                    ];
                                    $label = $typeLabels[$type] ?? ucfirst($type);
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium">{{ $label }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($data['total'], 0, ',', ' ') }} FCFA</td>
                                    <td class="px-4 py-3 text-right text-green-600">{{ number_format($data['paye'], 0, ',', ' ') }} FCFA</td>
                                    <td class="px-4 py-3 text-right text-orange-600">{{ number_format($data['reste'], 0, ',', ' ') }} FCFA</td>
                                    <td class="px-4 py-3 text-center">
                                        @if($data['reste'] <= 0)
                                            <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">✓ Payé</span>
                                        @elseif($data['paye'] > 0)
                                            <span class="px-2 py-1 text-xs bg-orange-100 text-orange-700 rounded-full">⚠ Partiel</span>
                                        @else
                                            <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded-full">✗ Impayé</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100 font-semibold">
                            <tr>
                                <td class="px-4 py-3">TOTAUX</td>
                                <td class="px-4 py-3 text-right">{{ number_format($situation['total_general'] ?? 0, 0, ',', ' ') }} FCFA</td>
                                <td class="px-4 py-3 text-right text-green-700">{{ number_format($situation['total_paye'] ?? 0, 0, ',', ' ') }} FCFA</td>
                                <td class="px-4 py-3 text-right text-orange-700">{{ number_format($situation['total_reste'] ?? 0, 0, ',', ' ') }} FCFA</td>
                                <td class="px-4 py-3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @endif

            <!-- Échéancier détaillé -->
            @if(isset($situation['toutes_echeances']) && $situation['toutes_echeances']->count() > 0)
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
                    Échéancier des paiements
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Période</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Libellé</th>
                                <th class="px-4 py-3 text-right text-sm font-medium text-gray-500">Montant</th>
                                <th class="px-4 py-3 text-right text-sm font-medium text-gray-500">Payé</th>
                                <th class="px-4 py-3 text-right text-sm font-medium text-gray-500">Reste</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Date limite</th>
                                <th class="px-4 py-3 text-center text-sm font-medium text-gray-500">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($situation['toutes_echeances'] as $echeance)
                                @php
                                    $estPaye = $echeance->montant_paye >= $echeance->montant;
                                    $estRetard = !$estPaye && $echeance->date_limite < now();
                                    $progress = $echeance->montant > 0 ? round(($echeance->montant_paye / $echeance->montant) * 100, 1) : 0;
                                @endphp
                                <tr class="hover:bg-gray-50 {{ $estRetard ? 'bg-red-50' : ($estPaye ? 'bg-green-50' : '') }}">
                                    <td class="px-4 py-3">{{ $echeance->periode }}</td>
                                    <td class="px-4 py-3">{{ $echeance->libelle }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($echeance->montant, 0, ',', ' ') }} FCFA</td>
                                    <td class="px-4 py-3 text-right text-green-600">{{ number_format($echeance->montant_paye, 0, ',', ' ') }} FCFA</td>
                                    <td class="px-4 py-3 text-right {{ $echeance->montant_restant > 0 ? 'text-orange-600 font-bold' : 'text-green-600' }}">
                                        {{ number_format($echeance->montant_restant, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $echeance->date_limite->format('d/m/Y') }}
                                        @if($estRetard)
                                            <span class="ml-2 text-xs text-red-500">(En retard)</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($estPaye)
                                            <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full"><i class="fas fa-check"></i> Payé</span>
                                        @elseif($estRetard)
                                            <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded-full"><i class="fas fa-exclamation-triangle"></i> En retard</span>
                                        @else
                                            <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-700 rounded-full"><i class="fas fa-clock"></i> En attente</span>
                                        @endif
                                    </td>
                                </tr>
                                @if(!$estPaye && $progress > 0 && $progress < 100)
                                <tr class="bg-gray-50">
                                    <td colspan="7" class="px-4 py-2">
                                        <div class="flex items-center">
                                            <span class="text-xs text-gray-500 mr-2">Progression:</span>
                                            <div class="flex-1 bg-gray-200 rounded-full h-1.5">
                                                <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $progress }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-500 ml-2">{{ $progress }}%</span>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100 font-semibold">
                            <tr>
                                <td class="px-4 py-3" colspan="2">TOTAUX</td>
                                <td class="px-4 py-3 text-right">{{ number_format($situation['total_general'] ?? 0, 0, ',', ' ') }} FCFA</td>
                                <td class="px-4 py-3 text-right text-green-700">{{ number_format($situation['total_paye'] ?? 0, 0, ',', ' ') }} FCFA</td>
                                <td class="px-4 py-3 text-right text-orange-700">{{ number_format($situation['total_reste'] ?? 0, 0, ',', ' ') }} FCFA</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @endif

            <!-- Détail du paiement effectué -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-money-bill-wave text-green-600 mr-2"></i>
                    Détail du paiement effectué
                </h3>
                <div class="bg-gray-50 rounded-xl p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500">Libellé</p>
                            <p class="font-medium">{{ $paiement->frais->libelle }}</p>
                            @if($paiement->frais->description)
                                <p class="text-sm text-gray-500 mt-2">Description</p>
                                <p class="text-sm">{{ $paiement->frais->description }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Montant versé</p>
                            <p class="text-3xl font-bold text-green-600">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</p>
                            @if($paiement->commentaire)
                                <p class="text-sm text-gray-500 mt-2">Commentaire</p>
                                <p class="text-sm italic">{{ $paiement->commentaire }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prochaines échéances -->
            @if(isset($situation['prochaines_echeances']) && $situation['prochaines_echeances']->count() > 0)
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-bell text-yellow-600 mr-2"></i>
                    Prochaines échéances
                </h3>
                <div class="bg-yellow-50 rounded-xl p-4">
                    <div class="space-y-3">
                        @foreach($situation['prochaines_echeances'] as $prochaine)
                            <div class="flex justify-between items-center border-b border-yellow-200 pb-2 last:border-0">
                                <div>
                                    <p class="font-medium">{{ $prochaine->libelle }} - {{ $prochaine->periode }}</p>
                                    <p class="text-sm text-gray-600">Date limite: {{ $prochaine->date_limite->format('d/m/Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-orange-600">{{ number_format($prochaine->montant_restant, 0, ',', ' ') }} FCFA</p>
                                    <p class="text-xs text-gray-500">Dans {{ now()->diffInDays($prochaine->date_limite) }} jours</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Alertes -->
            @if(($situation['total_reste'] ?? 0) > 0)
                <div class="mb-8 p-4 bg-orange-50 border-l-4 border-orange-500 rounded-r-xl">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-orange-500 text-xl mr-3"></i>
                        <div>
                            <p class="font-medium text-orange-800">Attention : Un montant de {{ number_format($situation['total_reste'] ?? 0, 0, ',', ' ') }} FCFA reste à payer.</p>
                            @if(isset($situation['echeances_retard']) && $situation['echeances_retard']->count() > 0)
                                <p class="text-sm text-orange-700">Dont {{ $situation['echeances_retard']->count() }} échéance(s) en retard.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Pied de page -->
            <div class="border-t border-gray-200 pt-6 mt-4">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500">Enregistré le {{ $paiement->created_at->format('d/m/Y à H:i') }}</p>
                        <p class="text-xs text-gray-400">Dernière modification: {{ $paiement->updated_at->diffForHumans() }}</p>
                    </div>
                    <div class="text-right">
                        <div class="flex space-x-8">
                            <div>
                                <div class="w-32 border-t border-gray-400 mt-2"></div>
                                <p class="text-xs text-gray-500 mt-1">Signature du parent</p>
                            </div>
                            <div>
                                <div class="w-32 border-t border-gray-400 mt-2"></div>
                                <p class="text-xs text-gray-500 mt-1">Cachet de l'établissement</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection