{{-- resources/views/etablissement/parametres/rapport.blade.php --}}
@extends('layouts.app')

@section('title', 'Rapport établissement - SYSCOL')
@section('page-title', 'Rapport de l\'établissement')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec actions d'export -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-file-alt text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Rapport de l'établissement</h2>
                <p class="text-sm text-gray-500">{{ $etablissement->nom }} · Généré le {{ now()->format('d/m/Y à H:i') }}</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <!-- ✅ Bouton Export PDF -->
            <a href="{{ route('etablissement.parametres.rapport-pdf') }}" 
               class="flex items-center px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all">
                <i class="fas fa-file-pdf mr-2"></i>
                Exporter PDF
            </a>
            
            <button onclick="window.print()" 
                    class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all">
                <i class="fas fa-print mr-2"></i>
                Imprimer
            </button>
            
            <a href="{{ route('etablissement.parametres.index') }}" 
               class="flex items-center px-4 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour
            </a>
        </div>
    </div>

    <!-- En-tête du rapport avec logo -->
    <div class="bg-white rounded-2xl shadow-sm p-8 border-l-4 border-blue-500">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="flex items-center space-x-4">
                @if($etablissement->logo)
                    <img src="{{ Storage::url($etablissement->logo) }}" alt="Logo" class="w-20 h-20 object-contain">
                @else
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-school text-white text-3xl"></i>
                    </div>
                @endif
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">{{ $etablissement->nom }}</h1>
                    <p class="text-gray-500">{{ $etablissement->type ?? 'Établissement scolaire' }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Rapport annuel</p>
                <p class="text-lg font-semibold text-blue-600">{{ $stats['annee_en_cours']->libelle ?? 'Année en cours' }}</p>
            </div>
        </div>
    </div>

    <!-- Statistiques générales -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Classes</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['total_classes'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-door-open text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Enseignants</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['total_enseignants'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chalkboard-teacher text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Élèves</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['total_eleves'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-graduate text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-amber-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Ratio</p>
                    <p class="text-3xl font-bold text-gray-800">
                        @if($stats['total_enseignants'] > 0)
                            {{ round($stats['total_eleves'] / $stats['total_enseignants'], 1) }}
                        @else
                            0
                        @endif
                    </p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-balance-scale text-amber-600 text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Élèves par enseignant</p>
        </div>
    </div>

    <!-- Graphiques et répartitions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Répartition par classe -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-pie text-blue-600 mr-2"></i>
                Répartition par classe
            </h3>
            
            <div class="space-y-4">
                @foreach($etablissement->classes as $classe)
                    @php
                        $effectif = $classe->eleves->count();
                        $pourcentage = $stats['total_eleves'] > 0 ? round(($effectif / $stats['total_eleves']) * 100, 1) : 0;
                        $couleurs = ['blue', 'green', 'purple', 'yellow', 'red', 'indigo'];
                        $couleur = $couleurs[$loop->index % count($couleurs)];
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-medium text-gray-700">{{ $classe->nom }}</span>
                            <span class="text-gray-600">{{ $effectif }} élèves ({{ $pourcentage }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="h-2.5 rounded-full bg-{{ $couleur }}-500" style="width: {{ $pourcentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Répartition par sexe -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-venus-mars text-purple-600 mr-2"></i>
                Répartition par sexe
            </h3>
            
            @php
                $garcons = $etablissement->eleves->where('sexe', 'M')->count();
                $filles = $etablissement->eleves->where('sexe', 'F')->count();
                $total = $garcons + $filles;
                $pourcentageGarcons = $total > 0 ? round(($garcons / $total) * 100, 1) : 0;
                $pourcentageFilles = $total > 0 ? round(($filles / $total) * 100, 1) : 0;
            @endphp
            
            <div class="flex items-center justify-center space-x-8 mb-6">
                <div class="text-center">
                    <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-mars text-blue-600 text-3xl"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">{{ $garcons }}</p>
                    <p class="text-sm text-gray-500">Garçons</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 rounded-full bg-pink-100 flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-venus text-pink-600 text-3xl"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">{{ $filles }}</p>
                    <p class="text-sm text-gray-500">Filles</p>
                </div>
            </div>
            
            <div class="space-y-2">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-blue-600">Garçons</span>
                        <span class="text-gray-600">{{ $pourcentageGarcons }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="h-2.5 rounded-full bg-blue-500" style="width: {{ $pourcentageGarcons }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-pink-600">Filles</span>
                        <span class="text-gray-600">{{ $pourcentageFilles }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="h-2.5 rounded-full bg-pink-500" style="width: {{ $pourcentageFilles }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Détails par classe -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-list-alt text-indigo-600 mr-2"></i>
            Détail par classe
        </h3>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Classe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Niveau</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Effectif</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Garçons</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Filles</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Professeur principal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($etablissement->classes as $classe)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $classe->nom }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $classe->niveau }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $classe->eleves->count() }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $classe->eleves->where('sexe', 'M')->count() }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $classe->eleves->where('sexe', 'F')->count() }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @if($classe->professeurPrincipal)
                                    {{ $classe->professeurPrincipal->name }}
                                @else
                                    <span class="text-gray-400">Non assigné</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pied de page du rapport -->
    <div class="bg-gray-50 rounded-2xl p-6 text-sm text-gray-500 flex justify-between items-center">
        <div>
            <i class="fas fa-calendar-alt mr-2"></i>
            Rapport généré le {{ now()->format('d/m/Y à H:i:s') }}
        </div>
        <div>
            <i class="fas fa-user mr-2"></i>
            Généré par {{ auth()->user()->name }}
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        body {
            background: white;
        }
        .no-print {
            display: none !important;
        }
        .bg-white {
            background: white !important;
            box-shadow: none !important;
            border: 1px solid #e5e7eb !important;
        }
        .shadow-sm {
            box-shadow: none !important;
        }
        .btn-print {
            display: none !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Fonction d'export PDF (appelle la route dédiée)
    function exportPDF() {
        window.location.href = "{{ route('etablissement.parametres.rapport-pdf') }}";
    }
</script>
@endpush
@endsection