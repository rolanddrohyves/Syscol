{{-- resources/views/admin/statistiques/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Statistiques globales - SYSCOL')
@section('page-title', 'Statistiques globales')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center shadow-lg animate-float">
                <i class="fas fa-chart-line text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Statistiques globales</h2>
                <p class="text-sm text-gray-500">Aperçu général de l'activité du système</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <button onclick="exportStats('pdf')" 
                    class="flex items-center px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all">
                <i class="fas fa-file-pdf mr-2"></i>
                Export PDF
            </button>
            <button onclick="exportStats('excel')" 
                    class="flex items-center px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all">
                <i class="fas fa-file-excel mr-2"></i>
                Export Excel
            </button>
        </div>
    </div>

    <!-- Statistiques générales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Établissements -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-indigo-500 hover:shadow-lg transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Établissements</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['general']['etablissements']['total'] }}</p>
                </div>
                <div class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-school text-2xl text-indigo-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600 font-medium">{{ $stats['general']['etablissements']['actifs'] }} actifs</span>
                <span class="mx-2 text-gray-300">|</span>
                <span class="text-blue-600">{{ $stats['general']['etablissements']['nouveaux_mois'] }} nouveaux (mois)</span>
            </div>
        </div>

        <!-- Utilisateurs -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500 hover:shadow-lg transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Utilisateurs</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['general']['utilisateurs']['total'] }}</p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-users text-2xl text-green-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600 font-medium">{{ $stats['general']['utilisateurs']['actifs'] }} actifs</span>
                <span class="mx-2 text-gray-300">|</span>
                <span class="text-blue-600">{{ $stats['general']['utilisateurs']['nouveaux_mois'] }} nouveaux</span>
            </div>
        </div>

        <!-- Élèves -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500 hover:shadow-lg transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Élèves</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['general']['eleves']['total'] }}</p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-user-graduate text-2xl text-blue-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600 font-medium">{{ $stats['general']['eleves']['actifs'] }} actifs</span>
                <span class="mx-2 text-gray-300">|</span>
                <span class="text-blue-600">{{ $stats['general']['eleves']['nouveaux_mois'] }} nouveaux</span>
            </div>
        </div>

        <!-- Classes -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500 hover:shadow-lg transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Classes</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['general']['classes']['total'] }}</p>
                </div>
                <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-door-open text-2xl text-purple-600"></i>
                </div>
            </div>
            <div class="mt-4 text-sm">
                <span class="text-green-600">{{ $stats['general']['classes']['avec_prof_principal'] }} avec PP</span>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Évolution des inscriptions -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                Évolution des inscriptions
            </h3>
            <div class="h-80" id="evolutionChart"></div>
        </div>

        <!-- Répartition par rôle -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-pie text-green-600 mr-2"></i>
                Répartition par rôle
            </h3>
            <div class="h-80" id="rolesChart"></div>
        </div>
    </div>

    <!-- Tableaux de répartition -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Répartition par type d'établissement -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-school text-indigo-600 mr-2"></i>
                Types d'établissements
            </h3>
            <div class="space-y-4">
                @foreach($stats['repartition']['etablissements'] as $type)
                <div>
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>{{ $type->type }}</span>
                        <span class="font-medium">{{ $type->total }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        @php
                            $pourcentage = ($type->total / $stats['general']['etablissements']['total']) * 100;
                        @endphp
                        <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $pourcentage }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Occupation des classes par niveau -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-door-open text-purple-600 mr-2"></i>
                Occupation par niveau
            </h3>
            <div class="space-y-4">
                @foreach($stats['repartition']['eleves'] as $niveau)
                <div>
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>{{ $niveau['niveau'] }}</span>
                        <span>{{ $niveau['eleves'] }}/{{ $niveau['capacite'] }} élèves ({{ $niveau['classes'] }} classes)</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $niveau['taux_occupation'] }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Taux d'occupation: {{ $niveau['taux_occupation'] }}%</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Top statistiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top établissements -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-trophy text-yellow-500 mr-2"></i>
                Établissements avec le plus d'élèves
            </h3>
            <div class="space-y-3">
                @foreach($stats['top']['etablissements'] as $index => $etab)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <div class="flex items-center">
                        <span class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 font-bold mr-3">
                            {{ $index + 1 }}
                        </span>
                        <span class="font-medium text-gray-800">{{ $etab['nom'] }}</span>
                    </div>
                    <span class="text-lg font-bold text-gray-900">{{ $etab['total'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Top classes -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-trophy text-yellow-500 mr-2"></i>
                Classes les plus chargées
            </h3>
            <div class="space-y-3">
                @foreach($stats['top']['classes'] as $index => $classe)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <div class="flex items-center">
                        <span class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 font-bold mr-3">
                            {{ $index + 1 }}
                        </span>
                        <div>
                            <span class="font-medium text-gray-800">{{ $classe['nom'] }}</span>
                            <p class="text-xs text-gray-500">Capacité: {{ $classe['capacite'] }}</p>
                        </div>
                    </div>
                    <span class="text-lg font-bold text-gray-900">{{ $classe['total'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Indicateurs de performance -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white">
            <p class="text-blue-100 text-sm">Taux de remplissage</p>
            <p class="text-3xl font-bold mt-2">{{ $stats['performance']['taux_remplissage'] }}%</p>
            <div class="mt-4 bg-blue-400/30 rounded-full h-2">
                <div class="bg-white h-2 rounded-full" style="width: {{ $stats['performance']['taux_remplissage'] }}%"></div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-lg p-6 text-white">
            <p class="text-green-100 text-sm">Ratio élèves/enseignant</p>
            <p class="text-3xl font-bold mt-2">{{ $stats['performance']['ratio_eleves_enseignant'] }}</p>
            <p class="text-green-100 text-sm mt-2">élèves par enseignant</p>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
            <p class="text-purple-100 text-sm">Moyenne élèves/classe</p>
            <p class="text-3xl font-bold mt-2">{{ $stats['performance']['moyenne_eleves_par_classe'] }}</p>
            <p class="text-purple-100 text-sm mt-2">élèves par classe</p>
        </div>

        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl shadow-lg p-6 text-white">
            <p class="text-indigo-100 text-sm">Moyenne classes/établissement</p>
            <p class="text-3xl font-bold mt-2">{{ $stats['performance']['moyenne_classes_par_etablissement'] }}</p>
            <p class="text-indigo-100 text-sm mt-2">classes par établissement</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Graphique d'évolution
    const evolutionCtx = document.getElementById('evolutionChart').getContext('2d');
    new Chart(evolutionCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($stats['evolution']['mois']) !!},
            datasets: [{
                label: 'Élèves',
                data: {!! json_encode($stats['evolution']['eleves']) !!},
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Utilisateurs',
                data: {!! json_encode($stats['evolution']['utilisateurs']) !!},
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });

    // Graphique des rôles
    const rolesCtx = document.getElementById('rolesChart').getContext('2d');
    new Chart(rolesCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($stats['repartition']['utilisateurs']->pluck('role')) !!},
            datasets: [{
                data: {!! json_encode($stats['repartition']['utilisateurs']->pluck('total')) !!},
                backgroundColor: [
                    '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                }
            }
        }
    });

    // Export
    function exportStats(format) {
        window.location.href = "{{ route('admin.statistiques.export') }}?format=" + format;
    }
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
</style>
@endpush