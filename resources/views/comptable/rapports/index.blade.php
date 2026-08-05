{{-- resources/views/comptable/rapports/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Rapports financiers - SYSCOL')
@section('page-title', 'Rapports financiers')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Filtres rapides -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-filter text-red-600 mr-2"></i>
            Générer un rapport
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <form action="{{ route('comptable.rapports.journalier') }}" method="GET" class="flex space-x-2">
                <input type="date" name="date" value="{{ date('Y-m-d') }}" 
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors">
                    <i class="fas fa-calendar-day mr-2"></i>Journalier
                </button>
            </form>
            
            <form action="{{ route('comptable.rapports.mensuel') }}" method="GET" class="flex space-x-2">
                <select name="mois" class="px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>
                            {{ Carbon\Carbon::createFromDate(null, $i, 1)->locale('fr')->monthName }}
                        </option>
                    @endfor
                </select>
                <select name="annee" class="px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                    @for($i = date('Y'); $i >= date('Y')-5; $i--)
                        <option value="{{ $i }}" {{ date('Y') == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors">
                    <i class="fas fa-calendar-alt mr-2"></i>Mensuel
                </button>
            </form>
            
            <form action="{{ route('comptable.rapports.annuel') }}" method="GET" class="flex space-x-2">
                <select name="annee" class="flex-1 px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                    @for($i = date('Y'); $i >= date('Y')-10; $i--)
                        <option value="{{ $i }}" {{ date('Y') == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors">
                    <i class="fas fa-chart-line mr-2"></i>Annuel
                </button>
            </form>
        </div>
    </div>

    <!-- Cartes de synthèse -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total des paiements</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($totalPaiements, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-arrow-down text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total des dépenses</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($totalDepenses, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-arrow-up text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Solde</p>
                    <p class="text-2xl font-bold {{ $solde >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($solde, 0, ',', ' ') }} FCFA
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-wallet text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Impayés</p>
                    <p class="text-2xl font-bold text-orange-600">{{ number_format($totalImpayes, 0, ',', ' ') }} FCFA</p>
                    <p class="text-xs text-gray-500">{{ $nombreImpayes }} élève(s)</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Paiements par mois -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-bar text-red-600 mr-2"></i>
                Évolution des paiements
            </h3>
            <canvas id="paiementsChart" height="250"></canvas>
            <div class="mt-4 space-y-2">
                @foreach($paiementsParMois->take(6) as $item)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">{{ $item->mois }}/{{ $item->annee }}</span>
                    <span class="font-medium">{{ number_format($item->total, 0, ',', ' ') }} FCFA</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Paiements par mode -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-pie text-red-600 mr-2"></i>
                Répartition par mode de paiement
            </h3>
            <canvas id="modePaiementChart" height="250"></canvas>
            <div class="mt-4 grid grid-cols-2 gap-2">
                @foreach($paiementsParMode as $mode)
                <div class="flex justify-between text-sm p-2 bg-gray-50 rounded-lg">
                    <span class="text-gray-600">
                        @switch($mode->mode_paiement)
                            @case('especes') 💵 Espèces @break
                            @case('cheque') 📝 Chèque @break
                            @case('virement') 💻 Virement @break
                            @case('carte') 💳 Carte @break
                            @case('mobile_money') 📱 Mobile Money @break
                            @default {{ $mode->mode_paiement }}
                        @endswitch
                    </span>
                    <span class="font-medium">{{ number_format($mode->total, 0, ',', ' ') }} FCFA</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Paiements par classe -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-school text-red-600 mr-2"></i>
                Paiements par classe
            </h3>
            <div class="space-y-3">
                @foreach($paiementsParClasse as $classe => $data)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">{{ $classe }}</span>
                        <span class="font-medium">{{ number_format($data['total'], 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        @php
                            $maxTotal = $paiementsParClasse->max('total');
                            $widthPercentage = $maxTotal > 0 ? ($data['total'] / $maxTotal) * 100 : 0;
                        @endphp
                        <div class="bg-red-600 h-2 rounded-full" style="width: {{ $widthPercentage }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ $data['nombre'] }} paiement(s)</p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Top 10 des payeurs -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-trophy text-red-600 mr-2"></i>
                Top 10 des meilleurs payeurs
            </h3>
            <div class="space-y-3">
                @foreach($topEleves as $index => $eleve)
                <div class="flex items-center justify-between p-3 {{ $index < 3 ? 'bg-yellow-50' : 'bg-gray-50' }} rounded-xl">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-full {{ $index == 0 ? 'bg-yellow-500' : ($index == 1 ? 'bg-gray-400' : ($index == 2 ? 'bg-orange-600' : 'bg-gray-300')) }} flex items-center justify-center text-white font-bold">
                            {{ $index + 1 }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $eleve->eleve->prenom }} {{ $eleve->eleve->nom }}</p>
                            <p class="text-xs text-gray-500">{{ $eleve->eleve->classe->nom ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <p class="font-bold text-green-600">{{ number_format($eleve->total, 0, ',', ' ') }} FCFA</p>
                </div>
                @endforeach
                @if($topEleves->isEmpty())
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-info-circle text-4xl mb-2"></i>
                    <p>Aucun paiement enregistré</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Actions d'export -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-download text-red-600 mr-2"></i>
            Exporter les rapports
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <form action="{{ route('comptable.rapports.export') }}" method="GET" class="flex">
                <input type="hidden" name="type" value="journalier">
                <input type="date" name="date" value="{{ date('Y-m-d') }}" 
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-l-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-r-xl hover:bg-green-700 transition-colors">
                    <i class="fas fa-file-csv mr-2"></i>CSV Journalier
                </button>
            </form>
            
            <form action="{{ route('comptable.rapports.export') }}" method="GET" class="flex">
                <input type="hidden" name="type" value="mensuel">
                <select name="mois" class="px-3 py-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}">{{ Carbon\Carbon::createFromDate(null, $i, 1)->locale('fr')->monthName }}</option>
                    @endfor
                </select>
                <select name="annee" class="px-3 py-2 border-t border-b border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500">
                    @for($i = date('Y'); $i >= date('Y')-5; $i--)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-r-xl hover:bg-green-700 transition-colors">
                    <i class="fas fa-file-csv mr-2"></i>CSV Mensuel
                </button>
            </form>
            
            <form action="{{ route('comptable.rapports.export') }}" method="GET" class="flex">
                <input type="hidden" name="type" value="annuel">
                <select name="annee" class="flex-1 px-4 py-2 border border-gray-300 rounded-l-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                    @for($i = date('Y'); $i >= date('Y')-10; $i--)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-r-xl hover:bg-green-700 transition-colors">
                    <i class="fas fa-file-csv mr-2"></i>CSV Annuel
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Graphique des paiements par mois
    const paiementsCtx = document.getElementById('paiementsChart').getContext('2d');
    const paiementsData = @json($paiementsParMois);
    
    const moisLabels = paiementsData.reverse().map(item => {
        const date = new Date(item.annee, item.mois - 1);
        return date.toLocaleString('fr', { month: 'short' }) + ' ' + item.annee;
    });
    
    const paiementsMontants = paiementsData.map(item => item.total);
    
    new Chart(paiementsCtx, {
        type: 'line',
        data: {
            labels: moisLabels,
            datasets: [{
                label: 'Montant des paiements (FCFA)',
                data: paiementsMontants,
                borderColor: 'rgb(220, 38, 38)',
                backgroundColor: 'rgba(220, 38, 38, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
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
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
                        }
                    }
                }
            }
        }
    });

    // Graphique des modes de paiement
    const modeCtx = document.getElementById('modePaiementChart').getContext('2d');
    const modeData = @json($paiementsParMode);
    
    const modeLabels = modeData.map(item => {
        switch(item.mode_paiement) {
            case 'especes': return 'Espèces';
            case 'cheque': return 'Chèque';
            case 'virement': return 'Virement';
            case 'carte': return 'Carte';
            case 'mobile_money': return 'Mobile Money';
            default: return item.mode_paiement;
        }
    });
    
    const modeMontants = modeData.map(item => item.total);
    
    new Chart(modeCtx, {
        type: 'doughnut',
        data: {
            labels: modeLabels,
            datasets: [{
                data: modeMontants,
                backgroundColor: [
                    'rgb(34, 197, 94)',
                    'rgb(59, 130, 246)',
                    'rgb(249, 115, 22)',
                    'rgb(168, 85, 247)',
                    'rgb(236, 72, 153)'
                ],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${new Intl.NumberFormat('fr-FR').format(value)} FCFA (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
</script>
@endpush

@push('styles')
<style>
    .transition-colors {
        transition-property: background-color, border-color, color, fill, stroke;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }
</style>
@endpush
@endsection