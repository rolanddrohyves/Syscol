{{-- resources/views/etablissement/absences/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Signaler une absence - SYSCOL')
@section('page-title', 'Signaler une absence / retard')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <!-- En-tête -->
        <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-calendar-times text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Signaler une absence / retard</h2>
                <p class="text-gray-500">Enregistrer une absence, un retard ou une sortie anticipée</p>
            </div>
        </div>

        <!-- Formulaire -->
        <form action="{{ route('etablissement.absences.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Sélection rapide par classe et élève -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-user-graduate text-red-600 mr-2"></i>
                    Élève concerné
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Classe -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Classe <span class="text-red-500">*</span>
                        </label>
                        <select name="classe_id" 
                                id="classe_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all @error('classe_id') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionner une classe</option>
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}" 
                                        {{ old('classe_id', $classeId) == $classe->id ? 'selected' : '' }}>
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
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all @error('eleve_id') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionner d'abord une classe</option>
                            @if($classeId && $elevesParClasse && isset($elevesParClasse[$classeId]))
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

            <!-- Type et date -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-clock text-red-600 mr-2"></i>
                    Type et horaire
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Type -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Type <span class="text-red-500">*</span>
                        </label>
                        <select name="type" 
                                id="type"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all @error('type') border-red-500 @enderror"
                                required>
                            <option value="absence" {{ old('type') == 'absence' ? 'selected' : '' }}>Absence</option>
                            <option value="retard" {{ old('type') == 'retard' ? 'selected' : '' }}>Retard</option>
                            <option value="sortie_anticipée" {{ old('type') == 'sortie_anticipée' ? 'selected' : '' }}>Sortie anticipée</option>
                        </select>
                        @error('type')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="date" 
                               value="{{ old('date', now()->format('Y-m-d')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all @error('date') border-red-500 @enderror"
                               required>
                        @error('date')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Heure (pour les retards) -->
                    <div class="space-y-2" id="heureField" style="{{ old('type') == 'retard' ? '' : 'display: none;' }}">
                        <label class="block text-sm font-medium text-gray-700">
                            Heure de retard
                        </label>
                        <input type="time" 
                               name="heure" 
                               value="{{ old('heure', now()->format('H:i')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all @error('heure') border-red-500 @enderror">
                        @error('heure')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Motif -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-pen-alt text-red-600 mr-2"></i>
                    Motif (optionnel)
                </h3>

                <div class="space-y-2">
                    <textarea name="motif" 
                              rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all @error('motif') border-red-500 @enderror"
                              placeholder="Raison de l'absence ou du retard...">{{ old('motif') }}</textarea>
                    @error('motif')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Actions rapides (préréglages) -->
            <div class="space-y-4 pt-6 border-t border-gray-100">
                <h3 class="text-sm font-medium text-gray-700">Actions rapides</h3>
                <div class="flex flex-wrap gap-3">
                    <button type="button" onclick="setAbsence()" class="px-4 py-2 bg-red-100 text-red-700 rounded-xl hover:bg-red-200 transition-all">
                        <i class="fas fa-times-circle mr-2"></i>Absence journée
                    </button>
                    <button type="button" onclick="setRetardMatin()" class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-xl hover:bg-yellow-200 transition-all">
                        <i class="fas fa-clock mr-2"></i>Retard (08:30)
                    </button>
                    <button type="button" onclick="setRetardApresMidi()" class="px-4 py-2 bg-orange-100 text-orange-700 rounded-xl hover:bg-orange-200 transition-all">
                        <i class="fas fa-clock mr-2"></i>Retard (14:30)
                    </button>
                    <button type="button" onclick="setSortieAnticipee()" class="px-4 py-2 bg-blue-100 text-blue-700 rounded-xl hover:bg-blue-200 transition-all">
                        <i class="fas fa-running mr-2"></i>Sortie anticipée
                    </button>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                <a href="{{ route('etablissement.absences.index') }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-red-600 to-orange-600 text-white rounded-xl hover:from-red-700 hover:to-orange-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-save mr-2"></i>
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Gestion dynamique des élèves par classe
    const elevesParClasse = @json($elevesParClasse ?? []);

    document.getElementById('classe_id').addEventListener('change', function() {
        const classeId = this.value;
        const eleveSelect = document.getElementById('eleve_id');
        
        // Vider le select
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

    // Afficher/masquer le champ heure selon le type
    document.getElementById('type').addEventListener('change', function() {
        const heureField = document.getElementById('heureField');
        if (this.value === 'retard') {
            heureField.style.display = 'block';
        } else {
            heureField.style.display = 'none';
            document.querySelector('input[name="heure"]').value = '';
        }
    });

    // Actions rapides
    function setAbsence() {
        document.getElementById('type').value = 'absence';
        document.getElementById('heureField').style.display = 'none';
        document.querySelector('input[name="heure"]').value = '';
    }

    function setRetardMatin() {
        document.getElementById('type').value = 'retard';
        document.getElementById('heureField').style.display = 'block';
        document.querySelector('input[name="heure"]').value = '08:30';
    }

    function setRetardApresMidi() {
        document.getElementById('type').value = 'retard';
        document.getElementById('heureField').style.display = 'block';
        document.querySelector('input[name="heure"]').value = '14:30';
    }

    function setSortieAnticipee() {
        document.getElementById('type').value = 'sortie_anticipée';
        document.getElementById('heureField').style.display = 'none';
        document.querySelector('input[name="heure"]').value = '';
    }

    // Validation du formulaire
    document.querySelector('form').addEventListener('submit', function(e) {
        const classe = document.getElementById('classe_id').value;
        const eleve = document.getElementById('eleve_id').value;
        const type = document.getElementById('type').value;
        const date = document.querySelector('input[name="date"]').value;
        
        let errors = [];

        if (!classe) errors.push('Veuillez sélectionner une classe');
        if (!eleve) errors.push('Veuillez sélectionner un élève');
        if (!type) errors.push('Veuillez sélectionner un type');
        if (!date) errors.push('Veuillez sélectionner une date');

        if (errors.length > 0) {
            e.preventDefault();
            alert('❌ Erreurs :\n- ' + errors.join('\n- '));
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