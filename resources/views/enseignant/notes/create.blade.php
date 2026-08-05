{{-- resources/views/enseignant/notes/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Saisie des notes - ' . $classe->nom . ' - ' . $matiere->nom)
@section('page-title', 'Saisie des notes - ' . $classe->nom . ' - ' . $matiere->nom)

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-green-500 to-green-600">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-pen mr-2"></i>
                        Saisie des notes
                    </h3>
                    <p class="text-green-100 text-sm mt-1">
                        Classe: {{ $classe->nom }} | Matière: {{ $matiere->nom }}
                    </p>
                </div>
                <div class="flex space-x-2">
                    <button type="button" onclick="toutPresenter()" 
                            class="px-3 py-1 bg-white text-green-600 rounded-lg text-sm hover:bg-gray-100">
                        <i class="fas fa-check-circle mr-1"></i> Tout présenter
                    </button>
                    <button type="button" onclick="toutAbsenter()" 
                            class="px-3 py-1 bg-white text-red-600 rounded-lg text-sm hover:bg-gray-100">
                        <i class="fas fa-times-circle mr-1"></i> Tout absenter
                    </button>
                </div>
            </div>
        </div>
        
        <form method="POST" action="{{ route('enseignant.notes.store') }}" class="p-6">
            @csrf
            <input type="hidden" name="classe_id" value="{{ $classe->id }}">
            <input type="hidden" name="matiere_id" value="{{ $matiere->id }}">
            
            <div class="mb-4 flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    Total élèves: <span class="font-bold">{{ $classe->eleves->count() }}</span>
                </div>
                <div class="text-sm text-gray-500">
                    Notes saisies: <span id="notesSaisies" class="font-bold text-green-600">0</span>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">N°</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matricule</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Élève</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Note /20</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Appréciation</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($classe->eleves as $index => $eleve)
                        @php
                            $noteExistante = $notesExistantes[$eleve->id] ?? null;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 text-sm font-mono text-gray-600">{{ $eleve->matricule ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-500 flex items-center justify-center mr-2 text-white text-xs font-semibold">
                                        {{ substr($eleve->prenom, 0, 1) }}{{ substr($eleve->nom, 0, 1) }}
                                    </div>
                                    <span class="font-medium text-gray-800">{{ $eleve->prenom }} {{ $eleve->nom }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <input type="hidden" name="notes[{{ $index }}][eleve_id]" value="{{ $eleve->id }}">
                                <input type="number" 
                                       name="notes[{{ $index }}][note]" 
                                       value="{{ $noteExistante->note ?? '' }}" 
                                       step="0.25" 
                                       min="0" 
                                       max="20"
                                       class="note-input w-24 px-3 py-2 text-center border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                       placeholder="-"
                                       onchange="updateCompteur()">
                            </td>
                            <td class="px-4 py-3">
                                <input type="text" 
                                       name="notes[{{ $index }}][appreciation]" 
                                       value="{{ $noteExistante->appreciation ?? '' }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                       placeholder="Appréciation...">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                <a href="{{ route('enseignant.notes', ['classe_id' => $classe->id, 'matiere_id' => $matiere->id]) }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Annuler
                </a>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Enregistrer les notes
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function updateCompteur() {
        const inputs = document.querySelectorAll('.note-input');
        let compteur = 0;
        inputs.forEach(input => {
            if (input.value.trim() !== '') {
                compteur++;
            }
        });
        document.getElementById('notesSaisies').innerText = compteur;
    }
    
    function toutPresenter() {
        const inputs = document.querySelectorAll('.note-input');
        inputs.forEach(input => {
            input.value = '10';
        });
        updateCompteur();
    }
    
    function toutAbsenter() {
        const inputs = document.querySelectorAll('.note-input');
        inputs.forEach(input => {
            input.value = '0';
        });
        updateCompteur();
    }
    
    // Initialiser le compteur au chargement
    document.addEventListener('DOMContentLoaded', function() {
        updateCompteur();
    });
</script>
@endpush

@push('styles')
<style>
    .note-input:focus {
        border-color: #22c55e;
        ring-color: #22c55e;
    }
</style>
@endpush
@endsection