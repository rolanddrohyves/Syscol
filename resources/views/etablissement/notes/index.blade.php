{{-- resources/views/etablissement/notes/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des notes - SYSCOL')
@section('page-title', 'Gestion des notes')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec boutons -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center shadow-lg animate-float">
                <i class="fas fa-star text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Notes</h2>
                <p class="text-sm text-gray-500">Gestion des notes des élèves</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <!-- ✅ Bouton Ajouter un trimestre -->
            <a href="{{ route('etablissement.trimestres.create') }}" 
               class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all shadow-lg">
                <i class="fas fa-calendar-plus mr-2"></i>
                Ajouter un trimestre
            </a>
            
            <a href="{{ route('etablissement.notes.create') }}" 
               class="flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all shadow-lg">
                <i class="fas fa-plus mr-2"></i>
                Ajouter une note
            </a>
            
            <a href="{{ route('etablissement.notes.export') }}?format=csv" 
               class="flex items-center px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all">
                <i class="fas fa-download mr-2"></i>
                Export CSV
            </a>
        </div>
    </div>

    <!-- Statistiques rapides (optionnel) -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total notes</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-star text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Matières</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['matieres'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-book-open text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Élèves notés</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['eleves'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-graduate text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-amber-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Trimestres</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['trimestres'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-amber-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <form method="GET" action="{{ route('etablissement.notes.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Classe</label>
                <select name="classe_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">Toutes les classes</option>
                    @foreach($classes as $classe)
                        <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                            {{ $classe->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Matière</label>
                <select name="matiere_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">Toutes les matières</option>
                    @foreach($matieres as $matiere)
                        <option value="{{ $matiere->id }}" {{ request('matiere_id') == $matiere->id ? 'selected' : '' }}>
                            {{ $matiere->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Trimestre</label>
                <select name="trimestre_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">Tous les trimestres</option>
                    @foreach($trimestres as $trimestre)
                        <option value="{{ $trimestre->id }}" {{ request('trimestre_id') == $trimestre->id ? 'selected' : '' }}>
                            {{ $trimestre->libelle }}
                            @if($trimestre->is_current)
                                (En cours)
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                <input type="date" name="date" value="{{ request('date') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700">
                    <i class="fas fa-filter mr-2"></i>
                    Filtrer
                </button>
                <a href="{{ route('etablissement.notes.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Liste des notes -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Élève</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Classe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matière</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Note</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trimestre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($notes as $note)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">
                            {{ $note->date_evaluation->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center mr-3 text-white text-sm font-semibold">
                                    {{ substr($note->eleve->prenom, 0, 1) }}{{ substr($note->eleve->nom, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $note->eleve->prenom }} {{ $note->eleve->nom }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $note->eleve->classe->nom }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $note->matiere->nom }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs rounded-full 
                                @if($note->note >= 16) bg-green-100 text-green-800
                                @elseif($note->note >= 12) bg-blue-100 text-blue-800
                                @elseif($note->note >= 10) bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ number_format($note->note, 2) }}/20
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $note->trimestre->libelle ?? 'N/A' }}
                            @if($note->trimestre && $note->trimestre->is_current)
                                <span class="text-xs text-green-600 ml-1">(En cours)</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('etablissement.notes.show', $note->id) }}" 
                                   class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100"
                                   title="Voir détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('etablissement.notes.edit', $note->id) }}" 
                                   class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100"
                                   title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deleteNote({{ $note->id }})" 
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
                                    <i class="fas fa-star text-4xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune note</h3>
                                <p class="text-gray-500 mb-4">Aucune note enregistrée pour le moment</p>
                                <div class="flex space-x-3">
                                    <a href="{{ route('etablissement.notes.create') }}" 
                                       class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                                        <i class="fas fa-plus mr-2"></i>
                                        Ajouter une note
                                    </a>
                                    <a href="{{ route('etablissement.trimestres.create') }}" 
                                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                        <i class="fas fa-calendar-plus mr-2"></i>
                                        Ajouter un trimestre
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($notes->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $notes->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Formulaire caché pour la suppression -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    function deleteNote(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette note ?')) {
            const form = document.getElementById('deleteForm');
            form.action = `/etablissement/notes/${id}`;
            form.submit();
        }
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
@endsection