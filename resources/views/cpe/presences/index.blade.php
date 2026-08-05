{{-- resources/views/cpe/presences/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des présences - CPE')
@section('page-title', 'Gestion des présences')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg animate-float">
                <i class="fas fa-check-circle text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Gestion des présences</h2>
                <p class="text-sm text-gray-500">Suivi des présences par classe</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('cpe.presences.export') }}?date={{ $date }}&classe_id={{ request('classe_id') }}" 
               class="flex items-center px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all">
                <i class="fas fa-download mr-2"></i>
                Export CSV
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <form method="GET" action="{{ route('cpe.presences.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                <input type="date" 
                       name="date" 
                       value="{{ $date }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Classe</label>
                <select name="classe_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Toutes les classes</option>
                    @foreach($toutesLesClasses as $classe)
                        <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                            {{ $classe->nom }} ({{ $classe->niveau }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700">
                    <i class="fas fa-filter mr-2"></i>
                    Filtrer
                </button>
                <a href="{{ route('cpe.presences.index', ['date' => $date]) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Statistiques globales du jour -->
    @php
        $totalPresent = 0;
        $totalAbsent = 0;
        $totalRetard = 0;
        $totalElevesFiltre = 0;
        
        foreach($presencesParClasse as $classeData) {
            $totalPresent += $classeData['present'];
            $totalAbsent += $classeData['absent'];
            $totalElevesFiltre += $classeData['total'];
            
            foreach($classeData['presences'] as $presence) {
                if (!$presence['present'] && ($presence['type'] ?? '') == 'retard') {
                    $totalRetard++;
                }
            }
        }
        $tauxPresence = $totalElevesFiltre > 0 ? round(($totalPresent / $totalElevesFiltre) * 100, 1) : 0;
    @endphp

    <!-- Résumé des filtres -->
    @if(request('classe_id'))
        <div class="bg-blue-50 rounded-xl p-4 border border-blue-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-filter text-blue-600 mr-2"></i>
                    <span class="text-sm text-blue-800">
                        Affichage filtré : 
                        @foreach($toutesLesClasses as $classe)
                            @if($classe->id == request('classe_id'))
                                <strong>{{ $classe->nom }} ({{ $classe->niveau }})</strong>
                            @endif
                        @endforeach
                    </span>
                </div>
                <span class="text-sm text-blue-600">
                    {{ $totalElevesFiltre }} élève(s)
                </span>
            </div>
        </div>
    @endif

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Présents</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalPresent }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-check text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Absents</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalAbsent }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-times text-red-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Retards</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalRetard }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Taux de présence</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $tauxPresence }}%</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Listes des classes filtrées -->
    <div class="space-y-6">
        @forelse($presencesParClasse as $classeId => $classeData)
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <!-- En-tête de classe -->
                <div class="bg-gradient-to-r from-green-500 to-emerald-500 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-door-open mr-2"></i>
                            {{ $classeData['classe']->nom }}
                            <span class="ml-3 text-sm bg-white/20 px-3 py-1 rounded-full">
                                {{ $classeData['present'] }}/{{ $classeData['total'] }} présents
                            </span>
                        </h3>
                        <div class="flex items-center space-x-2">
                            <span class="text-white text-sm">
                                <i class="fas fa-check-circle mr-1"></i>
                                {{ round(($classeData['present'] / max($classeData['total'], 1)) * 100, 1) }}%
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Barre de progression -->
                <div class="px-6 pt-4">
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        @php
                            $pourcentagePresent = $classeData['total'] > 0 
                                ? ($classeData['present'] / $classeData['total']) * 100 
                                : 0;
                        @endphp
                        <div class="h-2.5 rounded-full bg-green-500" style="width: {{ $pourcentagePresent }}%"></div>
                    </div>
                </div>

                <!-- Liste des élèves -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Élève</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Justifié</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($classeData['presences'] as $presence)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-500 to-emerald-500 flex items-center justify-center mr-3 text-white text-sm font-semibold">
                                                {{ substr($presence['eleve']->prenom, 0, 1) }}{{ substr($presence['eleve']->nom, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $presence['eleve']->prenom }} {{ $presence['eleve']->nom }}</p>
                                                <p class="text-xs text-gray-500">{{ $presence['eleve']->matricule }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($presence['present'])
                                            <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i> Présent
                                            </span>
                                        @else
                                            <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                                <i class="fas fa-times-circle mr-1"></i> Absent
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        @if(!$presence['present'])
                                            @if(isset($presence['type']) && $presence['type'] == 'retard')
                                                <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                                    Retard
                                                </span>
                                            @elseif(isset($presence['type']) && $presence['type'] == 'sortie_anticipée')
                                                <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                                    Sortie anticipée
                                                </span>
                                            @else
                                                <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                                    Absence
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if(!$presence['present'] && isset($presence['absence']) && $presence['absence'])
                                            @if($presence['absence']->justifiee)
                                                <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                                    <i class="fas fa-check-circle mr-1"></i> Oui
                                                </span>
                                            @else
                                                <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                                    <i class="fas fa-times-circle mr-1"></i> Non
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-2">
                                            @if($presence['present'])
                                                <button onclick="marquerAbsence({{ $presence['eleve']->id }}, '{{ $date }}')" 
                                                        class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100"
                                                        title="Marquer absent">
                                                    <i class="fas fa-user-times"></i>
                                                </button>
                                            @else
                                                <button onclick="marquerPresence({{ $presence['eleve']->id }}, '{{ $date }}')" 
                                                        class="p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100"
                                                        title="Marquer présent">
                                                    <i class="fas fa-user-check"></i>
                                                </button>
                                            @endif
                                            
                                            @if(!$presence['present'] && isset($presence['absence']) && $presence['absence'] && !$presence['absence']->justifiee)
                                                <button onclick="justifierAbsence({{ $presence['absence']->id }})" 
                                                        class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100"
                                                        title="Justifier">
                                                    <i class="fas fa-file-signature"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pied de classe -->
                <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center space-x-4">
                            <span class="text-gray-600">
                                <i class="fas fa-user-check text-green-600 mr-1"></i>
                                Présents: {{ $classeData['present'] }}
                            </span>
                            <span class="text-gray-600">
                                <i class="fas fa-user-times text-red-600 mr-1"></i>
                                Absents: {{ $classeData['absent'] }}
                            </span>
                            @php
                                $retardsClasse = 0;
                                foreach($classeData['presences'] as $presence) {
                                    if (!$presence['present'] && isset($presence['type']) && $presence['type'] == 'retard') {
                                        $retardsClasse++;
                                    }
                                }
                            @endphp
                            @if($retardsClasse > 0)
                                <span class="text-gray-600">
                                    <i class="fas fa-clock text-yellow-600 mr-1"></i>
                                    Retards: {{ $retardsClasse }}
                                </span>
                            @endif
                        </div>
                        <a href="{{ route('cpe.absences.create', ['classe_id' => $classeData['classe']->id, 'date' => $date]) }}" 
                           class="text-green-600 hover:text-green-800 text-sm">
                            <i class="fas fa-plus-circle mr-1"></i>
                            Signaler une absence
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
                <div class="flex flex-col items-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-calendar-times text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune donnée</h3>
                    <p class="text-gray-500">Aucune classe ou élève trouvé pour cette date</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal de justification -->
<div id="justifyModal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-file-signature text-2xl text-yellow-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Justifier l'absence</h3>
            <textarea id="justification" rows="3" 
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500"
                      placeholder="Motif de justification..."></textarea>
            <div class="flex justify-center space-x-3 mt-4">
                <button onclick="closeJustifyModal()" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Annuler
                </button>
                <button onclick="submitJustification()" class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                    Justifier
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Formulaire caché pour les actions -->
<form id="actionForm" method="POST" style="display: none;">
    @csrf
</form>

@push('scripts')
<script>
    let currentAbsenceId = null;

    // Marquer comme présent
    function marquerPresence(eleveId, date) {
        if (confirm('Marquer cet élève comme présent ?')) {
            const form = document.getElementById('actionForm');
            form.action = "{{ route('cpe.presences.marquer-presence') }}";
            
            const input1 = document.createElement('input');
            input1.type = 'hidden';
            input1.name = 'eleve_id';
            input1.value = eleveId;
            
            const input2 = document.createElement('input');
            input2.type = 'hidden';
            input2.name = 'date';
            input2.value = date;
            
            form.appendChild(input1);
            form.appendChild(input2);
            form.submit();
        }
    }

    // Marquer comme absent
    function marquerAbsence(eleveId, date) {
        if (confirm('Marquer cet élève comme absent ?')) {
            const form = document.getElementById('actionForm');
            form.action = "{{ route('cpe.presences.marquer-absence') }}";
            
            const input1 = document.createElement('input');
            input1.type = 'hidden';
            input1.name = 'eleve_id';
            input1.value = eleveId;
            
            const input2 = document.createElement('input');
            input2.type = 'hidden';
            input2.name = 'date';
            input2.value = date;
            
            form.appendChild(input1);
            form.appendChild(input2);
            form.submit();
        }
    }

    // Justifier une absence
    function justifierAbsence(absenceId) {
        currentAbsenceId = absenceId;
        document.getElementById('justifyModal').classList.remove('hidden');
    }

    function closeJustifyModal() {
        document.getElementById('justifyModal').classList.add('hidden');
        document.getElementById('justification').value = '';
        currentAbsenceId = null;
    }

    function submitJustification() {
        if (currentAbsenceId) {
            const justification = document.getElementById('justification').value;
            if (!justification) {
                alert('Veuillez saisir un motif de justification');
                return;
            }
            
            const form = document.getElementById('actionForm');
            form.action = `/cpe/absences/${currentAbsenceId}/justify`;
            
            const justif = document.createElement('input');
            justif.type = 'hidden';
            justif.name = 'justification';
            justif.value = justification;
            
            form.appendChild(justif);
            form.submit();
        }
    }

    // Fermer le modal en cliquant dehors
    document.getElementById('justifyModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeJustifyModal();
        }
    });

    // Fermer avec la touche Echap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('justifyModal').classList.contains('hidden')) {
            closeJustifyModal();
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
</style>
@endpush
@endsection