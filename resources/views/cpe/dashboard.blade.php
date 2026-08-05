{{-- resources/views/cpe/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Tableau de bord - CPE')
@section('page-title', 'Tableau de bord')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-user-shield text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Tableau de bord CPE</h2>
                <p class="text-sm text-gray-500">Conseiller Principal d'Éducation · {{ $anneeEnCours->libelle ?? 'Année en cours' }}</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <span class="px-3 py-1 text-sm bg-orange-100 text-orange-800 rounded-full">
                <i class="fas fa-calendar-alt mr-1"></i>
                {{ $trimestreEnCours->libelle ?? 'Trimestre en cours' }}
            </span>
            <a href="{{ route('cpe.absences.create') }}" 
               class="flex items-center px-4 py-2 bg-gradient-to-r from-orange-600 to-red-600 text-white rounded-xl hover:from-orange-700 hover:to-red-700 transition-all shadow-lg">
                <i class="fas fa-plus mr-2"></i>
                Signaler
            </a>
        </div>
    </div>

    <!-- Statistiques générales -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Élèves</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_eleves'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-graduate text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Classes</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_classes'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-door-open text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Sanctions</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_sanctions'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-gavel text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-amber-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Présences</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['presences_aujourdhui'] ?? 0 }}%</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-amber-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques des absences, retards et sanctions -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Absences aujourd'hui -->
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <p class="text-red-100 text-sm">Absences aujourd'hui</p>
                <i class="fas fa-calendar-times text-2xl text-red-200"></i>
            </div>
            <p class="text-3xl font-bold">{{ $statsAbsences['aujourdhui'] }}</p>
        </div>

        <!-- Retards aujourd'hui -->
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <p class="text-yellow-100 text-sm">Retards aujourd'hui</p>
                <i class="fas fa-clock text-2xl text-yellow-200"></i>
            </div>
            <p class="text-3xl font-bold">{{ $statsRetards['aujourdhui'] }}</p>
        </div>

        <!-- Sanctions en cours -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <p class="text-purple-100 text-sm">Sanctions en cours</p>
                <i class="fas fa-gavel text-2xl text-purple-200"></i>
            </div>
            <p class="text-3xl font-bold">{{ $stats['sanctions_en_cours'] ?? 0 }}</p>
        </div>

        <!-- Absences non justifiées -->
        <div class="bg-gradient-to-br from-gray-500 to-gray-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <p class="text-gray-100 text-sm">Non justifiées</p>
                <i class="fas fa-question-circle text-2xl text-gray-200"></i>
            </div>
            <p class="text-3xl font-bold">{{ $statsAbsences['non_justifiees'] }}</p>
        </div>
    </div>

    <!-- Graphiques et statistiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Graphique des absences (7 derniers jours) -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Évolution des absences</h3>
            <div class="space-y-3">
                @foreach($absences7Jours as $jour)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">{{ $jour['date'] }}</span>
                            <span class="font-medium text-gray-800">{{ $jour['total'] }} absence(s)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            @php
                                $maxAbsences = max(array_column($absences7Jours, 'total')) ?: 1;
                                $pourcentage = ($jour['total'] / $maxAbsences) * 100;
                            @endphp
                            <div class="h-2.5 rounded-full bg-red-500" style="width: {{ $pourcentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Graphique des retards (7 derniers jours) -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Évolution des retards</h3>
            <div class="space-y-3">
                @foreach($retards7Jours ?? [] as $jour)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">{{ $jour['date'] }}</span>
                            <span class="font-medium text-gray-800">{{ $jour['total'] }} retard(s)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            @php
                                $maxRetards = max(array_column($retards7Jours ?? [], 'total')) ?: 1;
                                $pourcentage = ($jour['total'] / $maxRetards) * 100;
                            @endphp
                            <div class="h-2.5 rounded-full bg-yellow-500" style="width: {{ $pourcentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Dernières activités -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Dernières absences -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Dernières absences</h3>
                <a href="{{ route('cpe.absences.index') }}" class="text-sm text-orange-600 hover:text-orange-800">
                    Voir tout <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            
            <div class="space-y-3 max-h-80 overflow-y-auto">
                @forelse($dernieresAbsences as $absence)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center text-white text-xs font-bold">
                                {{ substr($absence->eleve->prenom, 0, 1) }}{{ substr($absence->eleve->nom, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $absence->eleve->prenom }} {{ $absence->eleve->nom }}</p>
                                <p class="text-xs text-gray-500">{{ $absence->eleve->classe->nom }} · {{ $absence->date->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <div>
                            @if($absence->justifiee)
                                <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Justifiée</span>
                            @else
                                <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Non justifiée</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">Aucune absence récente</p>
                @endforelse
            </div>
        </div>

        <!-- Derniers retards -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Derniers retards</h3>
                <a href="{{ route('cpe.retards.index') }}" class="text-sm text-orange-600 hover:text-orange-800">
                    Voir tout <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            
            <div class="space-y-3 max-h-80 overflow-y-auto">
                @forelse($derniersRetards as $retard)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-yellow-500 to-orange-500 flex items-center justify-center text-white text-xs font-bold">
                                {{ substr($retard->eleve->prenom, 0, 1) }}{{ substr($retard->eleve->nom, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $retard->eleve->prenom }} {{ $retard->eleve->nom }}</p>
                                <p class="text-xs text-gray-500">{{ $retard->eleve->classe->nom }} · {{ $retard->date->format('d/m/Y') }} à {{ substr($retard->heure, 0, 5) }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">Aucun retard récent</p>
                @endforelse
            </div>
        </div>

        <!-- Dernières sanctions -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Dernières sanctions</h3>
                <a href="{{ route('cpe.sanctions.index') }}" class="text-sm text-orange-600 hover:text-orange-800">
                    Voir tout <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            
            <div class="space-y-3 max-h-80 overflow-y-auto">
                @forelse($dernieresSanctions ?? [] as $sanction)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-indigo-500 flex items-center justify-center text-white text-xs font-bold">
                                {{ substr($sanction->eleve->prenom, 0, 1) }}{{ substr($sanction->eleve->nom, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $sanction->eleve->prenom }} {{ $sanction->eleve->nom }}</p>
                                <p class="text-xs text-gray-500">{{ $sanction->type }} · {{ $sanction->date->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <div>
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($sanction->statut == 'en_cours') bg-yellow-100 text-yellow-800
                                @elseif($sanction->statut == 'executee') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $sanction->statut }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">Aucune sanction récente</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Élèves les plus problématiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Élèves les plus absents -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Élèves les plus absents</h3>
            
            @if(($elevesPlusAbsents ?? collect())->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Élève</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Classe</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Absences</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($elevesPlusAbsents as $eleve)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center mr-2 text-white text-xs font-bold">
                                                {{ substr($eleve->prenom, 0, 1) }}{{ substr($eleve->nom, 0, 1) }}
                                            </div>
                                            <span class="text-sm font-medium">{{ $eleve->prenom }} {{ $eleve->nom }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 text-sm">{{ $eleve->classe->nom }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                            {{ $eleve->absences_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <a href="{{ route('cpe.absences.create', ['eleve_id' => $eleve->id]) }}" 
                                           class="text-xs text-orange-600 hover:text-orange-800">
                                            <i class="fas fa-plus-circle"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">Aucun élève avec des absences</p>
            @endif
        </div>

        <!-- Élèves avec le plus de retards -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Élèves avec le plus de retards</h3>
            
            @if(($elevesPlusRetards ?? collect())->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Élève</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Classe</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Retards</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($elevesPlusRetards as $eleve)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-yellow-500 to-orange-500 flex items-center justify-center mr-2 text-white text-xs font-bold">
                                                {{ substr($eleve->prenom, 0, 1) }}{{ substr($eleve->nom, 0, 1) }}
                                            </div>
                                            <span class="text-sm font-medium">{{ $eleve->prenom }} {{ $eleve->nom }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 text-sm">{{ $eleve->classe->nom }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                            {{ $eleve->retards_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <a href="{{ route('cpe.retards.create', ['eleve_id' => $eleve->id]) }}" 
                                           class="text-xs text-orange-600 hover:text-orange-800">
                                            <i class="fas fa-plus-circle"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">Aucun élève avec des retards</p>
            @endif
        </div>
    </div>

    <!-- Statistiques par classe -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistiques par classe</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($absencesParClasse as $classe)
                <div class="p-4 bg-gray-50 rounded-xl">
                    <h4 class="font-semibold text-gray-800 mb-2">{{ $classe->nom }}</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Effectif:</span>
                            <span class="font-medium">{{ $classe->eleves_count }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Absences (mois):</span>
                            <span class="font-medium text-red-600">{{ $classe->absences_mois ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Retards (mois):</span>
                            <span class="font-medium text-yellow-600">{{ $classe->retards_mois ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Sanctions:</span>
                            <span class="font-medium text-purple-600">{{ $classe->sanctions_count ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Actions rapides</h3>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <a href="{{ route('cpe.absences.create') }}" 
               class="flex flex-col items-center p-3 bg-red-50 rounded-xl hover:bg-red-100 transition-colors">
                <i class="fas fa-calendar-times text-2xl text-red-600 mb-2"></i>
                <span class="text-xs font-medium text-gray-700">Absence</span>
            </a>
            
            <a href="{{ route('cpe.retards.create') }}" 
               class="flex flex-col items-center p-3 bg-yellow-50 rounded-xl hover:bg-yellow-100 transition-colors">
                <i class="fas fa-clock text-2xl text-yellow-600 mb-2"></i>
                <span class="text-xs font-medium text-gray-700">Retard</span>
            </a>
            
            <a href="{{ route('cpe.sanctions.create') }}" 
               class="flex flex-col items-center p-3 bg-purple-50 rounded-xl hover:bg-purple-100 transition-colors">
                <i class="fas fa-gavel text-2xl text-purple-600 mb-2"></i>
                <span class="text-xs font-medium text-gray-700">Sanction</span>
            </a>
            
            <a href="{{ route('cpe.presences.index') }}" 
               class="flex flex-col items-center p-3 bg-green-50 rounded-xl hover:bg-green-100 transition-colors">
                <i class="fas fa-check-circle text-2xl text-green-600 mb-2"></i>
                <span class="text-xs font-medium text-gray-700">Présences</span>
            </a>
            
            <a href="{{ route('cpe.absences.export') }}" 
               class="flex flex-col items-center p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                <i class="fas fa-download text-2xl text-gray-600 mb-2"></i>
                <span class="text-xs font-medium text-gray-700">Export</span>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Actualisation automatique toutes les 5 minutes (optionnel)
    setTimeout(function() {
        location.reload();
    }, 300000); // 5 minutes
</script>
@endpush

@push('styles')
<style>
    .hover\:-translate-y-1:hover {
        transform: translateY(-4px);
    }
</style>
@endpush