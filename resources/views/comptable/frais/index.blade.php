{{-- resources/views/comptable/frais/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Frais de scolarité - SYSCOL')
@section('page-title', 'Frais de scolarité')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-tag text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Frais de scolarité</h2>
                <p class="text-sm text-gray-500">Gestion des frais par année scolaire</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('comptable.frais.create') }}" 
               class="flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg">
                <i class="fas fa-plus mr-2"></i>
                Nouveau frais
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Année scolaire</label>
                <select name="annee_scolaire_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    <option value="">Toutes</option>
                    @foreach($anneesScolaires as $annee)
                        <option value="{{ $annee->id }}" {{ request('annee_scolaire_id') == $annee->id ? 'selected' : '' }}>
                            {{ $annee->libelle }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous</option>
                    @foreach($types as $value => $label)
                        <option value="{{ $value }}" {{ request('type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Obligatoire</label>
                <select name="obligatoire" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous</option>
                    <option value="1" {{ request('obligatoire') == '1' ? 'selected' : '' }}>Obligatoire</option>
                    <option value="0" {{ request('obligatoire') == '0' ? 'selected' : '' }}>Optionnel</option>
                </select>
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i>Filtrer
                </button>
                <a href="{{ route('comptable.frais.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Liste -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Libellé</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Classe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Année</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Périodicité</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Obligatoire</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($frais as $f)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium">{{ $f->libelle }}</td>
                        <td class="px-6 py-4">
                            @if($f->classe_id)
                                <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                                    {{ $f->classe->nom ?? 'Classe' }}
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full">
                                    Toutes classes
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $f->anneeScolaire->libelle ?? 'N/A' }}</td>
                        <td class="px-6 py-4">{{ $types[$f->type] ?? $f->type }}</td>
                        <td class="px-6 py-4">{{ ucfirst($f->periodicite) }}</td>
                        <td class="px-6 py-4 font-medium">{{ number_format($f->montant, 0, ',', ' ') }} FCFA</td>
                        <td class="px-6 py-4">
                            @if($f->obligatoire)
                                <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Oui</span>
                            @else
                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">Non</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('comptable.frais.show', $f->id) }}" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('comptable.frais.edit', $f->id) }}" class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deleteFrais({{ $f->id }})" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-inbox text-4xl text-gray-400 mb-3"></i>
                                <p>Aucun frais de scolarité trouvé</p>
                                <a href="{{ route('comptable.frais.create') }}" class="mt-3 text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-plus mr-1"></i>Ajouter un frais
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($frais->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $frais->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal de suppression -->
<div id="deleteModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Confirmer la suppression</h3>
            <p class="text-gray-600 mb-6">Êtes-vous sûr de vouloir supprimer ce frais ? Cette action est irréversible.</p>
            <div class="flex justify-center space-x-3">
                <button onclick="closeDeleteModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Annuler
                </button>
                <button id="confirmDelete" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
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

@push('scripts')
<script>
    let deleteId = null;

    function deleteFrais(id) {
        deleteId = id;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('deleteModal').classList.remove('flex');
        deleteId = null;
    }

    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (deleteId) {
            const form = document.getElementById('deleteForm');
            form.action = `/comptable/frais/${deleteId}`;
            form.submit();
        }
    });

    // Fermer le modal en cliquant en dehors
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });

    // Fermer avec la touche Echap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.getElementById('deleteModal').classList.contains('flex')) {
            closeDeleteModal();
        }
    });
</script>
@endpush

@push('styles')
<style>
    .modal-transition {
        transition: all 0.3s ease;
    }
</style>
@endpush
@endsection