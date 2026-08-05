{{-- resources/views/etablissement/eleves/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des élèves - SYSCOL')
@section('page-title', 'Gestion des élèves')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg animate-float">
                <i class="fas fa-user-graduate text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Élèves</h2>
                <p class="text-sm text-gray-500">Gestion de la scolarité des élèves</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('etablissement.eleves.create') }}" 
               class="flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg">
                <i class="fas fa-user-plus mr-2"></i>
                Nouvel élève
            </a>
            <a href="{{ route('etablissement.eleves.export') }}" 
               class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all">
                <i class="fas fa-download mr-2"></i>
                Export
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total élèves</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Par classe</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['classes_count'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-door-open text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-amber-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Garçons</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['garcons'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-male text-amber-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-pink-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Filles</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['filles'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-female text-pink-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <form method="GET" action="{{ route('etablissement.eleves.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Rechercher un élève (nom, prénom, matricule, parent)..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            
            <div>
                <select name="classe" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Toutes les classes</option>
                    @foreach($classes as $classe)
                        <option value="{{ $classe->id }}" {{ request('classe') == $classe->id ? 'selected' : '' }}>
                            {{ $classe->nom }} ({{ $classe->eleves_count ?? 0 }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-center space-x-2">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700">
                    <i class="fas fa-filter mr-2"></i>
                    Filtrer
                </button>
                <a href="{{ route('etablissement.eleves.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Liste des élèves -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Élève</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matricule</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Classe</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Parent</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($eleves as $eleve)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-500 to-emerald-500 flex items-center justify-center text-white font-bold mr-3">
                                {{ substr($eleve->prenom, 0, 1) }}{{ substr($eleve->nom, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $eleve->prenom }} {{ $eleve->nom }}</p>
                                <p class="text-xs text-gray-500">{{ $eleve->date_naissance?->format('d/m/Y') }} ({{ $eleve->age }} ans)</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 font-mono">
                        {{ $eleve->matricule }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                            {{ $eleve->classe->nom ?? 'Non affecté' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($eleve->nom_parent)
                            <div class="space-y-1">
                                <p class="text-sm font-medium text-gray-900">
                                    <i class="fas fa-user-tie text-xs text-gray-400 mr-1"></i>
                                    {{ $eleve->nom_parent }}
                                </p>
                                <p class="text-xs text-gray-600">
                                    <i class="fas fa-phone-alt text-xs text-gray-400 mr-1"></i>
                                    {{ $eleve->telephone_parent }}
                                </p>
                                @if($eleve->email_parent)
                                    <p class="text-xs text-gray-400 truncate max-w-[150px]" title="{{ $eleve->email_parent }}">
                                        <i class="fas fa-envelope text-xs text-gray-400 mr-1"></i>
                                        {{ $eleve->email_parent }}
                                    </p>
                                @endif
                            </div>
                        @else
                            <span class="text-xs text-gray-400 italic flex items-center">
                                <i class="fas fa-user-slash mr-1"></i>
                                Non renseigné
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($eleve->telephone || $eleve->email)
                            @if($eleve->telephone)
                                <p class="text-sm text-gray-600">
                                    <i class="fas fa-phone-alt text-xs text-gray-400 mr-1"></i>
                                    {{ $eleve->telephone }}
                                </p>
                            @endif
                            @if($eleve->email)
                                <p class="text-xs text-gray-400 truncate max-w-[150px]" title="{{ $eleve->email }}">
                                    <i class="fas fa-envelope text-xs text-gray-400 mr-1"></i>
                                    {{ $eleve->email }}
                                </p>
                            @endif
                        @else
                            <span class="text-xs text-gray-400">Non renseigné</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusColors = [
                                'actif' => 'bg-green-100 text-green-800',
                                'exclu' => 'bg-red-100 text-red-800',
                                'transferé' => 'bg-yellow-100 text-yellow-800',
                                'redoublant' => 'bg-purple-100 text-purple-800',
                            ];
                            $statusColor = $statusColors[$eleve->status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-3 py-1 text-xs rounded-full {{ $statusColor }}">
                            {{ ucfirst($eleve->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('etablissement.eleves.show', $eleve->id) }}" 
                               class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors"
                               title="Voir détails">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('etablissement.eleves.edit', $eleve->id) }}" 
                               class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition-colors"
                               title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('etablissement.eleves.notes', $eleve->id) }}" 
                               class="p-2 bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100 transition-colors"
                               title="Notes">
                                <i class="fas fa-star"></i>
                            </a>
                            <button onclick="deleteEleve({{ $eleve->id }})" 
                                    class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors"
                                    title="Supprimer"
                                    {{ $eleve->notes()->count() > 0 ? 'disabled' : '' }}>
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
                                <i class="fas fa-user-graduate text-4xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun élève</h3>
                            <p class="text-gray-500 mb-4">Commencez par ajouter un élève</p>
                            <a href="{{ route('etablissement.eleves.create') }}" 
                               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                Ajouter un élève
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($eleves->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $eleves->appends(request()->query())->links() }}
        </div>
        @endif
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
            <p class="text-gray-600 mb-6">Êtes-vous sûr de vouloir supprimer cet élève ?</p>
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

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    let deleteId = null;

    function deleteEleve(id) {
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
            form.action = `/etablissement/eleves/${deleteId}`;
            form.submit();
        }
    });

    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('deleteModal').classList.contains('hidden')) {
            closeDeleteModal();
        }
    });
</script>
@endpush