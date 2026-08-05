{{-- resources/views/cpe/sanctions/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Modifier une sanction - CPE')
@section('page-title', 'Modifier la sanction disciplinaire')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <!-- En-tête -->
        <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-indigo-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-gavel text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Modifier la sanction</h2>
                <p class="text-gray-500">{{ $sanction->eleve->prenom }} {{ $sanction->eleve->nom }} · {{ $sanction->date->format('d/m/Y') }}</p>
            </div>
        </div>

        <!-- Formulaire -->
        <form action="{{ route('cpe.sanctions.update', $sanction->id) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Sélection par classe et élève -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-user-graduate text-purple-600 mr-2"></i>
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
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('classe_id') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionner une classe</option>
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}" 
                                        {{ old('classe_id', $sanction->eleve->classe_id) == $classe->id ? 'selected' : '' }}>
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
                            <option value="">Sélectionner un élève</option>
                            @foreach($elevesParClasse[$sanction->eleve->classe_id] ?? [] as $eleve)
                                <option value="{{ $eleve->id }}" 
                                        {{ old('eleve_id', $sanction->eleve_id) == $eleve->id ? 'selected' : '' }}>
                                    {{ $eleve->prenom }} {{ $eleve->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('eleve_id')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Type de sanction et date -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-exclamation-triangle text-purple-600 mr-2"></i>
                    Type de sanction et date
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Type -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Type de sanction <span class="text-red-500">*</span>
                        </label>
                        <select name="type" 
                                id="type"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('type') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionner un type</option>
                            @foreach($types as $value => $label)
                                <option value="{{ $value }}" {{ old('type', $sanction->type) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date de la sanction -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Date de la sanction <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="date" 
                               value="{{ old('date', $sanction->date->format('Y-m-d')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('date') border-red-500 @enderror"
                               required>
                        @error('date')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Détails de la sanction -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-pen-alt text-purple-600 mr-2"></i>
                    Détails de la sanction
                </h3>

                <!-- Motif -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Motif de la sanction <span class="text-red-500">*</span>
                    </label>
                    <textarea name="motif" 
                              rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('motif') border-red-500 @enderror"
                              placeholder="Raison de la sanction...">{{ old('motif', $sanction->motif) }}</textarea>
                    @error('motif')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description complémentaire -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Description complémentaire
                    </label>
                    <textarea name="description" 
                              rows="2"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('description') border-red-500 @enderror"
                              placeholder="Informations complémentaires...">{{ old('description', $sanction->description) }}</textarea>
                    @error('description')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Durée et statut -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Durée (en heures)
                        </label>
                        <input type="number" 
                               name="duree" 
                               id="duree"
                               value="{{ old('duree', $sanction->duree) }}"
                               min="1"
                               max="100"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('duree') border-red-500 @enderror"
                               placeholder="Ex: 2">
                        @error('duree')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Statut -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Statut
                        </label>
                        <select name="statut" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('statut') border-red-500 @enderror">
                            @foreach($statuts as $value => $label)
                                <option value="{{ $value }}" {{ old('statut', $sanction->statut) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('statut')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Période d'application -->
            <div class="space-y-6 pt-6 border-t border-gray-100" id="periodeSection" style="{{ in_array($sanction->type, ['exclusion_temporaire', 'exclusion_definitive']) ? '' : 'display: none;' }}">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-calendar-alt text-purple-600 mr-2"></i>
                    Période d'application
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Date de début -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Date de début
                        </label>
                        <input type="date" 
                               name="date_debut" 
                               value="{{ old('date_debut', $sanction->date_debut ? $sanction->date_debut->format('Y-m-d') : '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('date_debut') border-red-500 @enderror">
                        @error('date_debut')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date de fin -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Date de fin
                        </label>
                        <input type="date" 
                               name="date_fin" 
                               value="{{ old('date_fin', $sanction->date_fin ? $sanction->date_fin->format('Y-m-d') : '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('date_fin') border-red-500 @enderror">
                        @error('date_fin')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('cpe.sanctions.index') }}" 
                   class="w-full sm:w-auto px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all text-center">
                    Annuler
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl hover:from-purple-700 hover:to-indigo-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-save mr-2"></i>
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Données des élèves par classe
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

    // Afficher/masquer la section période selon le type de sanction
    document.getElementById('type').addEventListener('change', function() {
        const periodeSection = document.getElementById('periodeSection');
        if (this.value === 'exclusion_temporaire' || this.value === 'exclusion_definitive') {
            periodeSection.style.display = 'block';
        } else {
            periodeSection.style.display = 'none';
        }
    });

    // Validation de la durée max pour les retenues
    document.getElementById('duree').addEventListener('input', function() {
        const type = document.getElementById('type').value;
        if (type === 'retenue' && this.value > 4) {
            this.setCustomValidity('La durée d\'une retenue ne peut pas dépasser 4 heures');
        } else {
            this.setCustomValidity('');
        }
    });

    // Validation du formulaire
    document.querySelector('form').addEventListener('submit', function(e) {
        const classe = document.getElementById('classe_id').value;
        const eleve = document.getElementById('eleve_id').value;
        const type = document.getElementById('type').value;
        const date = document.querySelector('input[name="date"]').value;
        const motif = document.querySelector('textarea[name="motif"]').value;
        
        let errors = [];

        if (!classe) errors.push('Veuillez sélectionner une classe');
        if (!eleve) errors.push('Veuillez sélectionner un élève');
        if (!type) errors.push('Veuillez sélectionner un type de sanction');
        if (!date) errors.push('Veuillez sélectionner une date');
        if (!motif) errors.push('Veuillez saisir un motif');

        if (type === 'exclusion_temporaire' || type === 'exclusion_definitive') {
            const dateDebut = document.querySelector('input[name="date_debut"]').value;
            const dateFin = document.querySelector('input[name="date_fin"]').value;
            
            if (type === 'exclusion_temporaire') {
                if (!dateDebut) errors.push('Veuillez saisir une date de début');
                if (!dateFin) errors.push('Veuillez saisir une date de fin');
                if (dateDebut && dateFin && new Date(dateFin) <= new Date(dateDebut)) {
                    errors.push('La date de fin doit être après la date de début');
                }
            }
        }

        if (type === 'retenue') {
            const duree = document.getElementById('duree').value;
            if (duree && duree > 4) {
                errors.push('La durée d\'une retenue ne peut pas dépasser 4 heures');
            }
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