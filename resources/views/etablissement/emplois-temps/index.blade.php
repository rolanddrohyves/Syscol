{{-- resources/views/etablissement/emplois-temps/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Emplois du temps - SYSCOL')
@section('page-title', 'Gestion des emplois du temps')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-2xl flex items-center justify-center shadow-lg animate-float">
                <i class="fas fa-calendar-alt text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Emplois du temps</h2>
                <p class="text-sm text-gray-500">Gestion des emplois du temps des classes et enseignants</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('etablissement.emplois_temps.create') }}" 
               class="flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg">
                <i class="fas fa-plus mr-2"></i>
                Nouvel emploi du temps
            </a>
            <a href="{{ route('etablissement.emplois_temps.export') }}" 
               class="flex items-center px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all">
                <i class="fas fa-download mr-2"></i>
                Export
            </a>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-indigo-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total cours</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-week text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Classes</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['classes'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-door-open text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Enseignants</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['enseignants'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chalkboard-teacher text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-amber-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Cours aujourd'hui</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['aujourdhui'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-day text-amber-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <form method="GET" action="{{ route('etablissement.emplois_temps.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Classe</label>
                <select name="classe_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Toutes les classes</option>
                    @foreach($classes as $classe)
                        <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                            {{ $classe->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Enseignant</label>
                <select name="enseignant_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tous les enseignants</option>
                    @foreach($enseignants as $enseignant)
                        <option value="{{ $enseignant->id }}" {{ request('enseignant_id') == $enseignant->id ? 'selected' : '' }}>
                            {{ $enseignant->user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jour</label>
                <select name="jour" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tous les jours</option>
                    <option value="Lundi" {{ request('jour') == 'Lundi' ? 'selected' : '' }}>Lundi</option>
                    <option value="Mardi" {{ request('jour') == 'Mardi' ? 'selected' : '' }}>Mardi</option>
                    <option value="Mercredi" {{ request('jour') == 'Mercredi' ? 'selected' : '' }}>Mercredi</option>
                    <option value="Jeudi" {{ request('jour') == 'Jeudi' ? 'selected' : '' }}>Jeudi</option>
                    <option value="Vendredi" {{ request('jour') == 'Vendredi' ? 'selected' : '' }}>Vendredi</option>
                    <option value="Samedi" {{ request('jour') == 'Samedi' ? 'selected' : '' }}>Samedi</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Période</label>
                <select name="periode" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Toutes</option>
                    <option value="matin" {{ request('periode') == 'matin' ? 'selected' : '' }}>Matin</option>
                    <option value="apres-midi" {{ request('periode') == 'apres-midi' ? 'selected' : '' }}>Après-midi</option>
                </select>
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700">
                    <i class="fas fa-filter mr-2"></i>
                    Filtrer
                </button>
                <a href="{{ route('etablissement.emplois_temps.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Liste des emplois du temps -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jour</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horaire</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Classe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matière</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Enseignant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Salle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($emploisTemps as $emploi)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 text-xs rounded-full bg-indigo-100 text-indigo-800">
                                {{ $emploi->jour }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">
                            {{ substr($emploi->heure_debut, 0, 5) }} - {{ substr($emploi->heure_fin, 0, 5) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $emploi->classe->nom }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $emploi->matiere->nom }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $emploi->enseignant->user->name ?? 'Non assigné' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $emploi->salle ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('etablissement.emplois_temps.show', $emploi->id) }}" 
                                   class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100"
                                   title="Voir détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('etablissement.emplois_temps.edit', $emploi->id) }}" 
                                   class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100"
                                   title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deleteEmploi({{ $emploi->id }})" 
                                        class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100"
                                        title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-calendar-alt text-4xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun emploi du temps</h3>
                                <p class="text-gray-500 mb-4">Commencez par créer un emploi du temps</p>
                                <a href="{{ route('etablissement.emplois_temps.create') }}" 
                                   class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                    <i class="fas fa-plus mr-2"></i>
                                    Créer un emploi du temps
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($emploisTemps->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $emploisTemps->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <!-- Vue compacte par classe (optionnel) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($classes as $classe)
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-door-open text-indigo-600 mr-2"></i>
                    {{ $classe->nom }}
                </h3>
                <a href="{{ route('etablissement.emplois_temps.classe', $classe->id) }}" 
                   class="text-sm text-indigo-600 hover:text-indigo-800">
                    Voir tout
                </a>
            </div>
            
            @php
                $coursClasse = $emploisTemps->where('classe_id', $classe->id)->take(3);
            @endphp
            
            @if($coursClasse->count() > 0)
                <div class="space-y-2">
                    @foreach($coursClasse as $cours)
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                        <div>
                            <span class="text-xs font-medium text-gray-500">{{ $cours->jour }}</span>
                            <p class="text-sm font-medium text-gray-800">{{ $cours->matiere->nom }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-600">{{ $cours->heure_debut }} - {{ $cours->heure_fin }}</p>
                            <p class="text-xs text-gray-500">{{ $cours->enseignant->user->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 text-center py-4">Aucun cours planifié</p>
            @endif
        </div>
        @endforeach
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div id="deleteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Confirmer la suppression</h3>
            <p class="text-gray-600 mb-6">Êtes-vous sûr de vouloir supprimer ce cours ?</p>
            <div class="flex justify-center space-x-3">
                <button onclick="closeDeleteModal()" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Annuler
                </button>
                <button id="confirmDelete" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Formulaire caché pour la suppression -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    let deleteId = null;

    function deleteEmploi(id) {
        deleteId = id;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        deleteId = null;
    }

    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (deleteId) {
            const form = document.getElementById('deleteForm');
            form.action = `/etablissement/emplois-temps/${deleteId}`;
            form.submit();
        }
    });

    // Fermer le modal en cliquant dehors
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });

    // Fermer avec la touche Echap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('deleteModal').classList.contains('hidden')) {
            closeDeleteModal();
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