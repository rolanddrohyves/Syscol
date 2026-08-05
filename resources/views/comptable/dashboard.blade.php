{{-- resources/views/comptable/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Tableau de bord - Comptable')
@section('page-title', 'Tableau de bord comptable')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg animate-float">
                <i class="fas fa-coins text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Tableau de bord comptable</h2>
                <p class="text-sm text-gray-500">Gestion financière · {{ $anneeEnCours->libelle ?? date('Y') . ' - ' . (date('Y') + 1) }}</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('comptable.paiements.index') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors">
                <i class="fas fa-list mr-2"></i>Tous les paiements
            </a>
            <a href="{{ route('comptable.impayes.index') }}" class="px-4 py-2 bg-orange-600 text-white rounded-xl hover:bg-orange-700 transition-colors">
                <i class="fas fa-exclamation-triangle mr-2"></i>Impayés
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
                <a href="{{ $alerte['lien'] ?? '#' }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    Voir les détails →
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Cartes statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500 hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-gray-500">Paiements du mois</p>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-green-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-800">
                {{ number_format($stats['total_paiements_mois'] ?? 0, 0, ',', ' ') }} FCFA
            </p>
            <a href="{{ route('comptable.paiements.index') }}" class="mt-3 text-sm text-green-600 hover:text-green-800 flex items-center">
                Voir tous les paiements <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500 hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-gray-500">Paiements année</p>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-blue-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-800">
                {{ number_format($stats['total_paiements_annee'] ?? 0, 0, ',', ' ') }} FCFA
            </p>
            <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5">
                <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ min(100, ($stats['total_paiements_annee'] / max(1, $stats['total_attendu'])) * 100) }}%"></div>
            </div>
            <p class="text-xs text-gray-500 mt-1">Objectif annuel: {{ number_format($stats['total_attendu'] ?? 0, 0, ',', ' ') }} FCFA</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-red-500 hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-gray-500">Dépenses du mois</p>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-red-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-800">
                {{ number_format($stats['total_depenses_mois'] ?? 0, 0, ',', ' ') }} FCFA
            </p>
            <a href="{{ route('comptable.depenses.index') }}" class="mt-3 text-sm text-red-600 hover:text-red-800 flex items-center">
                Voir les dépenses <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500 hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-gray-500">Taux de recouvrement</p>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-purple-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['taux_recouvrement'] ?? 0 }}%</p>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $stats['taux_recouvrement'] ?? 0 }}%"></div>
            </div>
            <p class="text-xs text-gray-500 mt-1">Objectif: 95%</p>
        </div>
    </div>

    <!-- Deuxième ligne de statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">Montant des impayés</h3>
                <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-sm font-medium">
                    {{ number_format($stats['nombre_impayes'] ?? 0, 0, ',', ' ') }} élèves
                </span>
            </div>
            <p class="text-3xl font-bold text-red-600 mb-3">
                {{ number_format($stats['montant_impayes'] ?? 0, 0, ',', ' ') }} FCFA
            </p>
            <div class="w-full bg-gray-200 rounded-full h-2">
                @php
                    $pourcentage = min(100, ($stats['montant_impayes'] / max(1, $stats['total_attendu'])) * 100);
                @endphp
                <div class="bg-red-600 h-2 rounded-full" style="width: {{ $pourcentage }}%"></div>
            </div>
            <p class="text-xs text-gray-500 mt-2">{{ number_format($pourcentage, 1) }}% du total attendu</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">Élèves avec impayés</h3>
                <i class="fas fa-users text-2xl text-orange-600"></i>
            </div>
            <p class="text-3xl font-bold text-orange-600 mb-3">{{ number_format($stats['total_impayes'] ?? 0, 0, ',', ' ') }}</p>
            <a href="{{ route('comptable.impayes.index') }}" class="text-sm text-orange-600 hover:text-orange-800">
                Voir la liste des impayés →
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">Relances en cours</h3>
                <i class="fas fa-bell text-2xl text-blue-600"></i>
            </div>
            <p class="text-3xl font-bold text-blue-600 mb-3">{{ number_format($stats['relances_en_cours'] ?? 0, 0, ',', ' ') }}</p>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Envoyées aujourd'hui:</span>
                    <span class="font-medium">{{ number_format($stats['relances_aujourdhui'] ?? 0, 0, ',', ' ') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">En attente:</span>
                    <span class="font-medium">{{ number_format($stats['relances_attente'] ?? 0, 0, ',', ' ') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-line text-green-600 mr-2"></i>
                Évolution des paiements (6 derniers mois)
            </h3>
            <div class="h-64">
                <canvas id="paiementsChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-pie text-blue-600 mr-2"></i>
                Répartition par type de frais
            </h3>
            <div class="h-64">
                <canvas id="fraisChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Derniers paiements -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">📋 Derniers paiements</h3>
            <a href="{{ route('comptable.paiements.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                Voir tout <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Élève</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Classe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Frais</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($derniersPaiements as $paiement)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-500 to-emerald-500 flex items-center justify-center mr-2 text-white text-xs font-semibold">
                                    {{ substr($paiement->eleve->prenom, 0, 1) }}{{ substr($paiement->eleve->nom, 0, 1) }}
                                </div>
                                <span class="font-medium text-sm">{{ $paiement->eleve->prenom }} {{ $paiement->eleve->nom }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $paiement->eleve->classe->nom ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $paiement->frais->libelle ?? '-' }}</td>
                        <td class="px-6 py-4 text-right font-medium">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                        <td class="px-6 py-4 text-center">
                            @if($paiement->statut == 'paye')
                                <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">✓ Payé</span>
                            @elseif($paiement->statut == 'partiel')
                                <span class="px-2 py-1 text-xs bg-orange-100 text-orange-700 rounded-full">⚠ Partiel</span>
                            @else
                                <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded-full">✗ Impayé</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-receipt text-4xl mb-2 text-gray-400"></i>
                            <p>Aucun paiement enregistré</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Prochains impayés -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">⚠️ Prochaines échéances à relancer</h3>
            <a href="{{ route('comptable.impayes.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                Voir tout <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Échéance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Facture</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Élève</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Classe</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant TTC</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($prochainsImpayes as $facture)
                    @php
                        $joursRestants = $facture->date_echeance ? \Carbon\Carbon::now()->diffInDays($facture->date_echeance, false) : 0;
                        $statutClass = $joursRestants <= 0 ? 'bg-red-100 text-red-700' : ($joursRestants <= 7 ? 'bg-orange-100 text-orange-700' : 'bg-yellow-100 text-yellow-700');
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm {{ $joursRestants <= 0 ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                            {{ $facture->date_echeance ? $facture->date_echeance->format('d/m/Y') : 'Non définie' }}
                            @if($joursRestants > 0 && $joursRestants <= 7)
                                <span class="ml-2 text-xs text-orange-600">(J-{{ $joursRestants }})</span>
                            @elseif($joursRestants <= 0)
                                <span class="ml-2 text-xs text-red-600">(Expirée)</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-mono">{{ $facture->numero ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-500 to-red-500 flex items-center justify-center mr-2 text-white text-xs font-semibold">
                                    {{ substr($facture->eleve->prenom, 0, 1) }}{{ substr($facture->eleve->nom, 0, 1) }}
                                </div>
                                <span class="font-medium text-sm">{{ $facture->eleve->prenom }} {{ $facture->eleve->nom }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $facture->eleve->classe->nom ?? '-' }}</td>
                        <td class="px-6 py-4 text-right font-medium text-red-600">{{ number_format($facture->montant_ttc ?? $facture->montant, 0, ',', ' ') }} FCFA</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 text-xs {{ $statutClass }} rounded-full">
                                @if($facture->statut == 'envoyee')
                                    📧 Relancé
                                @elseif($facture->statut == 'en_attente')
                                    ⏳ En attente
                                @else
                                    {{ ucfirst($facture->statut) }}
                                @endif
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-check-circle text-4xl mb-2 text-green-400"></i>
                            <p>Aucune échéance imminente</p>
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
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <a href="{{ route('comptable.paiements.create') }}" class="flex flex-col items-center p-4 bg-green-50 rounded-xl hover:bg-green-100 transition-colors group">
                <i class="fas fa-plus-circle text-2xl text-green-600 mb-2 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700">Nouveau paiement</span>
            </a>
            
            <a href="{{ route('comptable.impayes.index') }}" class="flex flex-col items-center p-4 bg-orange-50 rounded-xl hover:bg-orange-100 transition-colors group">
                <i class="fas fa-exclamation-triangle text-2xl text-orange-600 mb-2 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700">Gérer impayés</span>
            </a>
            
            <a href="{{ route('comptable.frais.index') }}" class="flex flex-col items-center p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors group">
                <i class="fas fa-tag text-2xl text-blue-600 mb-2 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700">Frais scolaires</span>
            </a>
            
            <a href="{{ route('comptable.factures.index') }}" class="flex flex-col items-center p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition-colors group">
                <i class="fas fa-file-invoice text-2xl text-purple-600 mb-2 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700">Factures</span>
            </a>

            <a href="{{ route('comptable.depenses.create') }}" class="flex flex-col items-center p-4 bg-red-50 rounded-xl hover:bg-red-100 transition-colors group">
                <i class="fas fa-shopping-cart text-2xl text-red-600 mb-2 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700">Nouvelle dépense</span>
            </a>
        </div>
    </div>

    <!-- Informations supplémentaires -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6">
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-simple text-white text-xl"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800 mb-1">Conseil du jour</h4>
                    <p class="text-sm text-gray-600">
                        @if(($stats['taux_recouvrement'] ?? 0) < 70)
                            Le taux de recouvrement est faible. Priorisez les relances pour les impayés les plus anciens.
                        @elseif(($stats['taux_recouvrement'] ?? 0) < 90)
                            Bonne progression ! Continuez les relances pour atteindre l'objectif de 95%.
                        @else
                            Excellent travail ! Le taux de recouvrement est satisfaisant.
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-6">
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-lightbulb text-white text-xl"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800 mb-1">Objectif du mois</h4>
                    <p class="text-sm text-gray-600">
                        Objectif de recouvrement: {{ number_format($stats['total_attendu'] * 0.15, 0, ',', ' ') }} FCFA
                    </p>
                    @php
                        $objectifMois = $stats['total_attendu'] * 0.15;
                        $progress = $objectifMois > 0 ? min(100, ($stats['total_paiements_mois'] / $objectifMois) * 100) : 0;
                    @endphp
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                        <div class="bg-green-600 h-1.5 rounded-full" style="width: {{ $progress }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ number_format($progress, 1) }}% atteint</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique d'évolution des paiements
    var paiementsCtx = document.getElementById('paiementsChart')?.getContext('2d');
    if (paiementsCtx) {
        new Chart(paiementsCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($evolutionPaiements['labels'] ?? ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin']) !!},
                datasets: [{
                    label: 'Paiements (FCFA)',
                    data: {!! json_encode($evolutionPaiements['donnees'] ?? [0, 0, 0, 0, 0, 0]) !!},
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgb(34, 197, 94)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += new Intl.NumberFormat('fr-FR').format(context.raw) + ' FCFA';
                                return label;
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
    }

    // Graphique de répartition des frais
    var fraisCtx = document.getElementById('fraisChart')?.getContext('2d');
    if (fraisCtx) {
        new Chart(fraisCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($repartitionFrais['labels'] ?? ['Scolarité', 'Inscription']) !!},
                datasets: [{
                    data: {!! json_encode($repartitionFrais['donnees'] ?? [0, 0]) !!},
                    backgroundColor: [
                        'rgb(34, 197, 94)',
                        'rgb(59, 130, 246)',
                        'rgb(249, 115, 22)',
                        'rgb(168, 85, 247)',
                        'rgb(107, 114, 128)'
                    ],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.label || '';
                                var value = context.raw;
                                var total = context.dataset.data.reduce(function(a, b) { return a + b; }, 0);
                                var percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return label + ': ' + new Intl.NumberFormat('fr-FR').format(value) + ' FCFA (' + percentage + '%)';
                            }
                        }
                    }
                },
                cutout: '60%'
            }
        });
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
    
    .transition-colors {
        transition-property: background-color, border-color, color, fill, stroke;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }
</style>
@endpush
@endsection