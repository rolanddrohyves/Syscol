{{-- resources/views/cpe/disciplines/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Ajouter un incident - CPE')
@section('page-title', 'Ajouter un incident disciplinaire')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <!-- En-tête -->
        <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-exclamation-triangle text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Ajouter un incident</h2>
                <p class="text-gray-500">Enregistrer un incident disciplinaire</p>
            </div>
        </div>

        <!-- Formulaire -->
        <form action="{{ route('cpe.disciplines.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Sélection par classe et élève -->
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
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all @error('eleve_id') border-red-500 @enderror"
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

            <!-- Type et gravité -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-tag text-red-600 mr-2"></i>
                    Type et gravité
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Type d'incident -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Type d'incident <span class="text-red-500">*</span>
                        </label>
                        <select name="type" 
                                id="type"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all @error('type') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionner un type</option>
                            <option value="incident" {{ old('type') == 'incident' ? 'selected' : '' }}>Incident</option>
                            <option value="avertissement" {{ old('type') == 'avertissement' ? 'selected' : '' }}>Avertissement</option>
                            <option value="retenue" {{ old('type') == 'retenue' ? 'selected' : '' }}>Retenue</option>
                            <option value="exclusion" {{ old('type') == 'exclusion' ? 'selected' : '' }}>Exclusion</option>
                        </select>
                        @error('type')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gravité -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Gravité <span class="text-red-500">*</span>
                        </label>
                        <select name="gravite" 
                                id="gravite"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all @error('gravite') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionner la gravité</option>
                            <option value="faible" {{ old('gravite') == 'faible' ? 'selected' : '' }}>Faible</option>
                            <option value="moyenne" {{ old('gravite') == 'moyenne' ? 'selected' : '' }}>Moyenne</option>
                            <option value="elevee" {{ old('gravite') == 'elevee' ? 'selected' : '' }}>Élevée</option>
                            <option value="critique" {{ old('gravite') == 'critique' ? 'selected' : '' }}>Critique</option>
                        </select>
                        @error('gravite')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Date et heure -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-clock text-red-600 mr-2"></i>
                    Date et heure
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Date -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Date de l'incident <span class="text-red-500">*</span>
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

                    <!-- Heure (optionnelle) -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Heure (optionnelle)
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

            <!-- Description -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-pen-alt text-red-600 mr-2"></i>
                    Description de l'incident
                </h3>

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" 
                              rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all @error('description') border-red-500 @enderror"
                              placeholder="Décrivez l'incident en détail..."
                              required>{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Suggestions de descriptions rapides -->
            <div class="space-y-4 p-4 bg-red-50 rounded-xl">
                <h4 class="text-sm font-semibold text-red-800 flex items-center">
                    <i class="fas fa-lightbulb mr-2"></i>
                    Descriptions rapides
                </h4>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="ajouterDescription('Retard répété après plusieurs avertissements')" 
                            class="px-3 py-1 bg-white text-sm text-red-700 rounded-lg hover:bg-red-100 border border-red-200">
                        Retard répété
                    </button>
                    <button type="button" onclick="ajouterDescription('Comportement perturbateur en cours')" 
                            class="px-3 py-1 bg-white text-sm text-red-700 rounded-lg hover:bg-red-100 border border-red-200">
                        Perturbation en cours
                    </button>
                    <button type="button" onclick="ajouterDescription('Non-respect du règlement intérieur')" 
                            class="px-3 py-1 bg-white text-sm text-red-700 rounded-lg hover:bg-red-100 border border-red-200">
                        Non-respect règlement
                    </button>
                    <button type="button" onclick="ajouterDescription('Absence non justifiée prolongée')" 
                            class="px-3 py-1 bg-white text-sm text-red-700 rounded-lg hover:bg-red-100 border border-red-200">
                        Absence prolongée
                    </button>
                    <button type="button" onclick="ajouterDescription('Incident avec un autre élève')" 
                            class="px-3 py-1 bg-white text-sm text-red-700 rounded-lg hover:bg-red-100 border border-red-200">
                        Conflit entre élèves
                    </button>
                </div>
            </div>

            <!-- Informations complémentaires -->
            <div class="space-y-4 p-4 bg-amber-50 rounded-xl">
                <h4 class="text-sm font-semibold text-amber-800 flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    Information
                </h4>
                <p class="text-xs text-amber-700">
                    L'incident sera enregistré et visible dans le tableau de bord. 
                    Vous pourrez le modifier ultérieurement si nécessaire.
                </p>
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('cpe.disciplines.index') }}" 
                   class="w-full sm:w-auto px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all text-center">
                    Annuler
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-red-600 to-orange-600 text-white rounded-xl hover:from-red-700 hover:to-orange-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-save mr-2"></i>
                    Enregistrer l'incident
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

    // Fonction pour ajouter une description rapide
    function ajouterDescription(texte) {
        const textarea = document.querySelector('textarea[name="description"]');
        const currentValue = textarea.value;
        if (currentValue) {
            textarea.value = currentValue + '\n\n' + texte;
        } else {
            textarea.value = texte;
        }
    }

    // Validation du formulaire
    document.querySelector('form').addEventListener('submit', function(e) {
        const classe = document.getElementById('classe_id').value;
        const eleve = document.getElementById('eleve_id').value;
        const type = document.getElementById('type').value;
        const gravite = document.getElementById('gravite').value;
        const date = document.querySelector('input[name="date"]').value;
        const description = document.querySelector('textarea[name="description"]').value;
        
        let errors = [];

        if (!classe) errors.push('Veuillez sélectionner une classe');
        if (!eleve) errors.push('Veuillez sélectionner un élève');
        if (!type) errors.push('Veuillez sélectionner un type d\'incident');
        if (!gravite) errors.push('Veuillez sélectionner la gravité');
        if (!date) errors.push('Veuillez sélectionner une date');
        if (!description) errors.push('Veuillez saisir une description');
        else if (description.length < 10) {
            errors.push('La description doit contenir au moins 10 caractères');
        }

        if (errors.length > 0) {
            e.preventDefault();
            alert('❌ Erreurs :\n- ' + errors.join('\n- '));
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

    // Empêcher la sélection de dates futures
    const dateInput = document.querySelector('input[name="date"]');
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('max', today);
    }
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