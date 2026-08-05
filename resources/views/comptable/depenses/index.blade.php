{{-- resources/views/comptable/depenses/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Dépenses - SYSCOL')
@section('page-title', 'Gestion des dépenses')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-shopping-cart text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Dépenses</h2>
                <p class="text-sm text-gray-500">Suivi des dépenses de l'établissement</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('comptable.depenses.create') }}" 
               class="flex items-center px-4 py-2 bg-gradient-to-r from-red-600 to-orange-600 text-white rounded-xl hover:from-red-700 hover:to-orange-700 transition-all shadow-lg">
                <i class="fas fa-plus mr-2"></i>
                Nouvelle dépense
            </a>
            <a href="{{ route('comptable.depenses.export') }}?{{ http_build_query(request()->all()) }}" 
               class="flex items-center px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all">
                <i class="fas fa-download mr-2"></i>
                Export CSV
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-red-500">
            <p class="text-sm text-gray-500">Total dépenses</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total'], 0, ',', ' ') }} FCFA</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-orange-500">
            <p class="text-sm text-gray-500">Ce mois</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['mois'], 0, ',', ' ') }} FCFA</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500">
            <p class="text-sm text-gray-500">Nombre</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['nombre'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500">
            <p class="text-sm text-gray-500">Moyenne</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['nombre'] > 0 ? number_format($stats['total'] / $stats['nombre'], 0, ',', ' ') : 0 }} FCFA</p>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Libellé, bénéficiaire..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
                <select name="categorie" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500">
                    <option value="">Toutes</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('categorie') == $cat ? 'selected' : '' }}>
                            {{ ucfirst($cat) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date début</label>
                <input type="date" name="date_debut" value="{{ request('date_debut') }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date fin</label>
                <input type="date" name="date_fin" value="{{ request('date_fin') }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500">
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700">
                    <i class="fas fa-filter mr-2"></i>Filtrer
                </button>
                <a href="{{ route('comptable.depenses.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Liste -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Libellé</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bénéficiaire</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($depenses as $depense)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">{{ $depense->date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 font-medium">{{ $depense->libelle }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                            {{ ucfirst($depense->categorie) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 font-medium text-red-600">{{ number_format($depense->montant, 0, ',', ' ') }} FCFA</td>
                    <td class="px-6 py-4">{{ ucfirst($depense->mode_paiement) }}</td>
                    <td class="px-6 py-4">{{ $depense->beneficiaire ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('comptable.depenses.show', $depense->id) }}" 
                               class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100" title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('comptable.depenses.edit', $depense->id) }}" 
                               class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($depense->piece_jointe)
                                <a href="{{ Storage::url($depense->piece_jointe) }}" target="_blank"
                                   class="p-2 bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100" title="Pièce jointe">
                                    <i class="fas fa-paperclip"></i>
                                </a>
                            @endif
                            <button onclick="deleteDepense({{ $depense->id }})" 
                                    class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        Aucune dépense trouvée
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($depenses->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $depenses->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <!-- Graphique par catégorie -->
    @if($stats['par_categorie']->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Répartition par catégorie</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($stats['par_categorie'] as $categorie => $montant)
                @php
                    $pourcentage = $stats['total'] > 0 ? round(($montant / $stats['total']) * 100, 1) : 0;
                    $colors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-500', 'bg-blue-500', 'bg-purple-500', 'bg-pink-500'];
                    $color = $colors[$loop->index % count($colors)];
                @endphp
                <div class="text-center p-3 bg-gray-50 rounded-xl">
                    <p class="text-sm font-medium text-gray-700">{{ ucfirst($categorie) }}</p>
                    <p class="text-xl font-bold text-gray-800">{{ number_format($montant, 0, ',', ' ') }} FCFA</p>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        <div class="h-2 rounded-full {{ $color }}" style="width: {{ $pourcentage }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ $pourcentage }}%</p>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Modal de suppression -->
<div id="deleteModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md">
        <h3 class="text-xl font-bold mb-4">Confirmer la suppression</h3>
        <p class="text-gray-600 mb-6">Cette action est irréversible.</p>
        <div class="flex justify-end space-x-3">
            <button onclick="closeDeleteModal()" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Annuler</button>
            <button id="confirmDelete" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Supprimer</button>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">@csrf @method('DELETE')</form>

@push('scripts')
<script>
    let deleteId = null;
    
    function deleteDepense(id) {
        deleteId = id;
        document.getElementById('deleteModal').classList.remove('hidden');
    }
    
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        deleteId = null;
    }
    
    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (deleteId) {
            document.getElementById('deleteForm').action = `/comptable/depenses/${deleteId}`;
            document.getElementById('deleteForm').submit();
        }
    });
</script>
@endpush
@endsection