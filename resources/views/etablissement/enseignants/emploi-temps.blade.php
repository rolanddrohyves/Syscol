{{-- resources/views/etablissement/enseignants/emploi-temps.blade.php --}}
@extends('layouts.app')

@section('title', 'Emploi du temps - SYSCOL')
@section('page-title', 'Emploi du temps de ' . $enseignant->name)

@section('content')
<div class="space-y-6">
    <!-- En-tête avec statistiques -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-calendar-alt text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Emploi du temps</h2>
                <p class="text-sm text-gray-500">{{ $enseignant->name }} · {{ $enseignant->enseignant->specialite }}</p>
            </div>
        </div>
        
        <a href="{{ route('etablissement.enseignants.show', $enseignant->id) }}" 
           class="flex items-center px-4 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>
            Retour au profil
        </a>
    </div>

    <!-- ✅ Statistiques rapides -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500">
            <p class="text-sm text-gray-500">Total cours</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total_cours'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500">
            <p class="text-sm text-gray-500">Matières enseignées</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['matieres_enseignees'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500">
            <p class="text-sm text-gray-500">Classes</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['classes'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-amber-500">
            <p class="text-sm text-gray-500">Heures/semaine</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['heures_semaine'] ?? 0 }}h</p>
        </div>
    </div>

    <!-- Emploi du temps par jour -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @php
            $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
            $couleurs = ['blue', 'green', 'purple', 'yellow', 'red', 'indigo'];
        @endphp

        @foreach($jours as $index => $jour)
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden border-t-4 border-{{ $couleurs[$index] }}-500">
                <div class="p-4 bg-gradient-to-r from-{{ $couleurs[$index] }}-50 to-transparent">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-calendar-day text-{{ $couleurs[$index] }}-600 mr-2"></i>
                        {{ $jour }}
                    </h3>
                </div>

                <div class="p-4">
                    @if(isset($emploisTemps[$jour]) && count($emploisTemps[$jour]) > 0)
                        <div class="space-y-3">
                            @foreach($emploisTemps[$jour] as $cours)
                                <div class="p-3 bg-gray-50 rounded-xl hover:shadow-md transition-all border-l-2 border-{{ $couleurs[$index] }}-500">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-semibold text-{{ $couleurs[$index] }}-700 bg-{{ $couleurs[$index] }}-100 px-2 py-1 rounded-full">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ substr($cours->heure_debut, 0, 5) }} - {{ substr($cours->heure_fin, 0, 5) }}
                                        </span>
                                        @if($cours->salle)
                                            <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded-full shadow-sm">
                                                <i class="fas fa-door-open mr-1"></i> {{ $cours->salle }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <p class="font-semibold text-gray-800">{{ $cours->matiere->nom }}</p>
                                    <p class="text-sm text-gray-600 flex items-center mt-1">
                                        <i class="fas fa-users text-gray-400 mr-2 text-xs"></i>
                                        {{ $cours->classe->nom }}
                                    </p>
                                    
                                    <div class="mt-3 flex justify-end space-x-2 border-t border-gray-200 pt-2">
                                        <a href="{{ route('etablissement.emplois_temps.edit', $cours->id) }}" 
                                           class="p-1.5 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition-colors"
                                           title="Modifier">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        <button onclick="deleteCours({{ $cours->id }})" 
                                                class="p-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors"
                                                title="Supprimer">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-calendar-times text-2xl text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 text-sm">Aucun cours</p>
                            <p class="text-xs text-gray-400 mt-1">Journée libre</p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Résumé hebdomadaire -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-chart-pie text-green-600 mr-2"></i>
            Résumé hebdomadaire
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center p-3 bg-green-50 rounded-xl">
                <span class="text-2xl font-bold text-green-600">{{ $stats['total_cours'] ?? 0 }}</span>
                <p class="text-xs text-gray-600">Total cours</p>
            </div>
            <div class="text-center p-3 bg-blue-50 rounded-xl">
                <span class="text-2xl font-bold text-blue-600">{{ $stats['heures_semaine'] ?? 0 }}</span>
                <p class="text-xs text-gray-600">Heures totales</p>
            </div>
            <div class="text-center p-3 bg-purple-50 rounded-xl">
                <span class="text-2xl font-bold text-purple-600">{{ $stats['classes'] ?? 0 }}</span>
                <p class="text-xs text-gray-600">Classes différentes</p>
            </div>
            <div class="text-center p-3 bg-amber-50 rounded-xl">
                <span class="text-2xl font-bold text-amber-600">{{ $stats['matieres_enseignees'] ?? 0 }}</span>
                <p class="text-xs text-gray-600">Matières</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation suppression -->
<div id="deleteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Confirmer la suppression</h3>
            <p class="text-gray-600 mb-6">Supprimer ce cours de l'emploi du temps ?</p>
            <div class="flex justify-center space-x-3">
                <button onclick="closeModal()" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Annuler
                </button>
                <button id="confirmDelete" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    let coursId = null;

    function deleteCours(id) {
        coursId = id;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        coursId = null;
    }

    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (coursId) {
            const form = document.getElementById('deleteForm');
            form.action = `/etablissement/emplois-temps/${coursId}`;
            form.submit();
        }
    });

    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    // Fermer avec Echap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('deleteModal').classList.contains('hidden')) {
            closeModal();
        }
    });
</script>
@endpush

@push('styles')
<style>
    .hover\:shadow-md:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
</style>
@endpush