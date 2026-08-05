{{-- resources/views/etablissement/notes/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Ajouter une note - SYSCOL')
@section('page-title', 'Ajouter une nouvelle note')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <!-- En-tête -->
        <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-star text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Ajouter une note</h2>
                <p class="text-gray-500">Saisir une note pour un élève</p>
            </div>
        </div>

        <!-- Formulaire -->
        <form action="{{ route('etablissement.notes.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Sélection de l'élève par classe -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-user-graduate text-purple-600 mr-2"></i>
                    Élève
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Classe -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Classe <span class="text-red-500">*</span>
                        </label>
                        <select name="classe_id" 
                                id="classe_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('classe_id') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionner une classe</option>
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}" {{ old('classe_id', $classeId) == $classe->id ? 'selected' : '' }}>
                                    {{ $classe->nom }} ({{ $classe->niveau }})
                                </option>
                            @endforeach
                        </select>
                        @error('classe_id')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Élève -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Élève <span class="text-red-500">*</span>
                        </label>
                        <select name="eleve_id" 
                                id="eleve_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('eleve_id') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionner d'abord une classe</option>
                            @if($classeId && isset($elevesParClasse[$classeId]))
                                @foreach($elevesParClasse[$classeId] as $eleve)
                                    <option value="{{ $eleve->id }}" {{ old('eleve_id', $eleveId) == $eleve->id ? 'selected' : '' }}>
                                        {{ $eleve->prenom }} {{ $eleve->nom }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('eleve_id')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Matière et trimestre -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-book-open text-purple-600 mr-2"></i>
                    Matière et trimestre
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Matière -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Matière <span class="text-red-500">*</span>
                        </label>
                        <select name="matiere_id" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('matiere_id') border-red-500 @enderror"
                                required>
                            <option value="">Choisir une matière</option>
                            @foreach($matieres as $matiere)
                                <option value="{{ $matiere->id }}" {{ old('matiere_id') == $matiere->id ? 'selected' : '' }}>
                                    {{ $matiere->nom }} (Coeff. {{ $matiere->coefficient }})
                                </option>
                            @endforeach
                        </select>
                        @error('matiere_id')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- ✅ TRIMESTRE 1, 2, 3 -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Trimestre <span class="text-red-500">*</span>
                        </label>
                        <select name="trimestre_id" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('trimestre_id') border-red-500 @enderror"
                                required>
                            <option value="">Choisir un trimestre</option>
                            @forelse($trimestres as $trimestre)
                                <option value="{{ $trimestre->id }}" {{ old('trimestre_id') == $trimestre->id ? 'selected' : '' }}>
                                    {{ $trimestre->libelle }} 
                                    @if($trimestre->is_current)
                                        <span class="text-green-600 font-medium">(En cours)</span>
                                    @endif
                                    @if($trimestre->date_debut && $trimestre->date_fin)
                                        <span class="text-gray-400 text-xs">
                                            ({{ $trimestre->date_debut->format('d/m') }} - {{ $trimestre->date_fin->format('d/m') }})
                                        </span>
                                    @endif
                                </option>
                            @empty
                                <option value="" disabled class="text-red-500">Aucun trimestre disponible</option>
                            @endforelse
                        </select>
                        @error('trimestre_id')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        
                        <!-- Indication du trimestre en cours -->
                        @php
                            $trimestreEnCours = $trimestres->firstWhere('is_current', true);
                        @endphp
                        @if($trimestreEnCours)
                            <p class="text-xs text-green-600 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                Trimestre en cours : {{ $trimestreEnCours->libelle }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Note et évaluation -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-pen-alt text-purple-600 mr-2"></i>
                    Note et évaluation
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Note -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Note <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="note" 
                               id="note"
                               value="{{ old('note') }}"
                               step="0.01"
                               min="0"
                               max="20"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('note') border-red-500 @enderror"
                               placeholder="0.00"
                               required>
                        @error('note')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Note maximale -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Note maximale <span class="text-red-500">*</span>
                        </label>
                        <select name="note_max" 
                                id="note_max"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('note_max') border-red-500 @enderror"
                                required>
                            <option value="20" {{ old('note_max', 20) == 20 ? 'selected' : '' }}>/20</option>
                            <option value="10" {{ old('note_max') == 10 ? 'selected' : '' }}>/10</option>
                            <option value="5" {{ old('note_max') == 5 ? 'selected' : '' }}>/5</option>
                        </select>
                        @error('note_max')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date d'évaluation -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Date d'évaluation <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="date_evaluation" 
                               value="{{ old('date_evaluation', now()->format('Y-m-d')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('date_evaluation') border-red-500 @enderror"
                               required>
                        @error('date_evaluation')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Aperçu de la note sur 20 -->
                <div class="p-4 bg-purple-50 rounded-xl">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <span class="text-sm text-purple-800">Note sur 20 équivalente :</span>
                        <span class="text-lg font-bold text-purple-600" id="note_sur_20">0.00/20</span>
                    </div>
                </div>
            </div>

            <!-- Appréciation -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-comment text-purple-600 mr-2"></i>
                    Appréciation
                </h3>

                <div class="space-y-2">
                    <textarea name="appreciation" 
                              rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('appreciation') border-red-500 @enderror"
                              placeholder="Appréciation sur la note (optionnel)">{{ old('appreciation') }}</textarea>
                    @error('appreciation')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('etablissement.notes.index') }}" 
                   class="w-full sm:w-auto px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all text-center">
                    Annuler
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-save mr-2"></i>
                    Enregistrer la note
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Données des élèves par classe passées par le contrôleur
    const elevesParClasse = @json($elevesParClasse ?? []);

    // Gestion dynamique des élèves par classe
    document.getElementById('classe_id').addEventListener('change', function() {
        const classeId = this.value;
        const eleveSelect = document.getElementById('eleve_id');
        
        // Vider le select des élèves
        eleveSelect.innerHTML = '<option value="">Sélectionner un élève</option>';
        
        // Remplir avec les élèves de la classe sélectionnée
        if (classeId && elevesParClasse[classeId]) {
            elevesParClasse[classeId].forEach(eleve => {
                const option = document.createElement('option');
                option.value = eleve.id;
                option.textContent = eleve.prenom + ' ' + eleve.nom;
                eleveSelect.appendChild(option);
            });
        }
    });

    // Déclencher l'événement au chargement si une classe est pré-sélectionnée
    window.addEventListener('DOMContentLoaded', function() {
        const classeSelect = document.getElementById('classe_id');
        if (classeSelect.value) {
            const event = new Event('change');
            classeSelect.dispatchEvent(event);
            
            // Si un élève est pré-sélectionné, le re-sélectionner après le chargement
            const eleveId = '{{ $eleveId ?? '' }}';
            if (eleveId) {
                const eleveSelect = document.getElementById('eleve_id');
                setTimeout(() => {
                    eleveSelect.value = eleveId;
                }, 100);
            }
        }
    });

    // Calcul de la note sur 20 en temps réel
    const noteInput = document.getElementById('note');
    const noteMaxSelect = document.getElementById('note_max');
    const noteSur20Span = document.getElementById('note_sur_20');

    function updateNoteSur20() {
        const note = parseFloat(noteInput.value) || 0;
        const noteMax = parseFloat(noteMaxSelect.value) || 20;
        const noteSur20 = (note * 20) / noteMax;
        noteSur20Span.textContent = noteSur20.toFixed(2) + '/20';
    }

    noteInput.addEventListener('input', updateNoteSur20);
    noteMaxSelect.addEventListener('change', updateNoteSur20);
    updateNoteSur20(); // Initialisation

    // Validation du formulaire
    document.querySelector('form').addEventListener('submit', function(e) {
        const classe = document.getElementById('classe_id').value;
        const eleve = document.getElementById('eleve_id').value;
        const matiere = document.querySelector('select[name="matiere_id"]').value;
        const trimestre = document.querySelector('select[name="trimestre_id"]').value;
        const note = document.getElementById('note').value;
        const date = document.querySelector('input[name="date_evaluation"]').value;
        
        let errors = [];

        if (!classe) errors.push('Veuillez sélectionner une classe');
        if (!eleve) errors.push('Veuillez sélectionner un élève');
        if (!matiere) errors.push('Veuillez sélectionner une matière');
        if (!trimestre) errors.push('Veuillez sélectionner un trimestre');
        if (!date) errors.push('Veuillez sélectionner une date');
        if (!note) errors.push('Veuillez saisir une note');
        else if (note < 0 || note > parseFloat(document.getElementById('note_max').value)) {
            errors.push('La note doit être comprise entre 0 et ' + document.getElementById('note_max').value);
        }

        if (errors.length > 0) {
            e.preventDefault();
            alert('Erreurs :\n- ' + errors.join('\n- '));
        }
    });
</script>
@endpush

@push('styles')
<style>
    .hover\:-translate-y-1:hover {
        transform: translateY(-4px);
    }
</style>
@endpush
@endsection