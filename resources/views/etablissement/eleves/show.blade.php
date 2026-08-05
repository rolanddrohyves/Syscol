{{-- resources/views/etablissement/eleves/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Détails de l\'élève - SYSCOL')
@section('page-title', 'Détails de l\'élève')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-user-graduate text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $eleve->prenom }} {{ $eleve->nom }}</h2>
                <p class="text-sm text-gray-500">{{ $eleve->matricule }} · {{ $eleve->classe->nom }}</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('etablissement.eleves.edit', $eleve->id) }}" 
               class="flex items-center px-4 py-2 bg-yellow-600 text-white rounded-xl hover:bg-yellow-700 transition-all shadow-lg">
                <i class="fas fa-edit mr-2"></i>
                Modifier
            </a>
            <a href="{{ route('etablissement.absences.create', ['eleve_id' => $eleve->id]) }}" 
               class="flex items-center px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all shadow-lg">
                <i class="fas fa-calendar-times mr-2"></i>
                Signaler absence
            </a>
            <a href="{{ route('etablissement.eleves.index') }}" 
               class="flex items-center px-4 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour
            </a>
        </div>
    </div>

    <!-- Grille d'informations -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne gauche : Photo et informations personnelles -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <!-- Photo -->
                <div class="flex flex-col items-center">
                    <div class="w-32 h-32 rounded-2xl bg-gradient-to-br from-green-500 to-emerald-500 flex items-center justify-center shadow-lg mb-4 overflow-hidden">
                        @if($eleve->photo)
                            <img src="{{ Storage::url($eleve->photo) }}" alt="{{ $eleve->prenom }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-5xl font-bold text-white">
                                {{ substr($eleve->prenom, 0, 1) }}{{ substr($eleve->nom, 0, 1) }}
                            </span>
                        @endif
                    </div>
                    
                    <h3 class="text-xl font-bold text-gray-800">{{ $eleve->prenom }} {{ $eleve->nom }}</h3>
                    <p class="text-sm text-gray-500">{{ $eleve->matricule }}</p>
                    
                    <div class="mt-4 flex items-center space-x-2">
                        <span class="px-3 py-1 text-xs rounded-full {{ $eleve->status == 'actif' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($eleve->status) }}
                        </span>
                        <span class="px-3 py-1 text-xs rounded-full {{ $eleve->sexe == 'F' ? 'bg-pink-100 text-pink-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $eleve->sexe == 'F' ? 'Féminin' : 'Masculin' }}
                        </span>
                    </div>
                </div>

                <!-- Informations personnelles -->
                <div class="mt-6 pt-6 border-t border-gray-100">
                    <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-user-circle text-green-600 mr-2"></i>
                        Informations personnelles
                    </h4>
                    
                    <dl class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">Date naissance</dt>
                            <dd class="font-medium text-gray-900">{{ $eleve->date_naissance->format('d/m/Y') }} ({{ $stats['age'] }} ans)</dd>
                        </div>
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">Lieu naissance</dt>
                            <dd class="font-medium text-gray-900">{{ $eleve->lieu_naissance }}</dd>
                        </div>
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">Adresse</dt>
                            <dd class="font-medium text-gray-900">{{ $eleve->adresse }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Colonne droite : Informations parent et statistiques -->
        <div class="lg:col-span-2">
            <!-- Informations parent -->
            <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-users text-green-600 mr-2"></i>
                    Informations parent
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <p class="text-xs text-gray-500 mb-1">Nom du parent</p>
                        <p class="font-medium text-gray-900">{{ $eleve->nom_parent }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <p class="text-xs text-gray-500 mb-1">Téléphone</p>
                        <p class="font-medium text-gray-900">{{ $eleve->telephone_parent }}</p>
                    </div>
                    @if($eleve->email_parent)
                    <div class="p-4 bg-gray-50 rounded-xl md:col-span-2">
                        <p class="text-xs text-gray-500 mb-1">Email</p>
                        <p class="font-medium text-gray-900">{{ $eleve->email_parent }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Notes</p>
                            <p class="text-3xl font-bold text-gray-800">{{ $stats['notes_count'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-star text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-red-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Absences</p>
                            <p class="text-3xl font-bold text-gray-800">{{ $stats['absences_count'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-calendar-times text-red-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Moyenne générale</p>
                            <p class="text-3xl font-bold text-gray-800">{{ $stats['moyenne_generale'] ?? 'N/A' }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-chart-line text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dernières notes -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-history text-green-600 mr-2"></i>
                        Dernières notes
                    </h4>
                    <a href="{{ route('etablissement.notes.create', ['eleve_id' => $eleve->id]) }}" 
                       class="text-sm text-green-600 hover:text-green-800">
                        <i class="fas fa-plus mr-1"></i>
                        Ajouter une note
                    </a>
                </div>

                @if($dernieresNotes->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matière</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Note</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Appréciation</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($dernieresNotes as $note)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $note->date_evaluation->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $note->matiere->nom }}</td>
                                <td class="px-4 py-3">
                                    <span class="font-semibold {{ $note->note_sur20 >= 10 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $note->note }}/{{ $note->note_max }}
                                    </span>
                                    <span class="text-xs text-gray-500 ml-1">({{ $note->note_sur20 }}/20)</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $note->appreciation ?? $note->appreciation_auto }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('etablissement.notes.edit', $note->id) }}" 
                                           class="p-1 bg-yellow-50 text-yellow-600 rounded hover:bg-yellow-100">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        <button onclick="deleteNote({{ $note->id }})" 
                                                class="p-1 bg-red-50 text-red-600 rounded hover:bg-red-100">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-gray-500 text-center py-4">Aucune note enregistrée</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression de note -->
<div id="deleteNoteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Confirmer la suppression</h3>
            <p class="text-gray-600 mb-6">Êtes-vous sûr de vouloir supprimer cette note ?</p>
            <div class="flex justify-center space-x-3">
                <button onclick="closeDeleteModal()" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Annuler
                </button>
                <button id="confirmDeleteNote" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Formulaire caché pour les actions -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    let deleteNoteId = null;

    function deleteNote(id) {
        deleteNoteId = id;
        document.getElementById('deleteNoteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteNoteModal').classList.add('hidden');
        deleteNoteId = null;
    }

    document.getElementById('confirmDeleteNote').addEventListener('click', function() {
        if (deleteNoteId) {
            const form = document.getElementById('deleteForm');
            form.action = `/etablissement/notes/${deleteNoteId}`;
            form.submit();
        }
    });

    // Fermer le modal en cliquant dehors
    document.getElementById('deleteNoteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
</script>
@endpush

@push('styles')
<style>
    .hover\:scale-102:hover {
        transform: scale(1.02);
    }
</style>
@endpush