{{-- resources/views/etablissement/classes/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Modifier la classe - SYSCOL')
@section('page-title', 'Modifier la classe : ' . $classe->nom)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <!-- En-tête -->
        <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-edit text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Modifier la classe</h2>
                <p class="text-gray-500">{{ $classe->nom }} · {{ $classe->niveau }}</p>
            </div>
        </div>

        <!-- Formulaire -->
        <form action="{{ route('etablissement.classes.update', $classe->id) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Informations générales -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-info-circle text-yellow-600 mr-2"></i>
                    Informations générales
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nom de la classe -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Nom de la classe <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="nom" 
                               value="{{ old('nom', $classe->nom) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all @error('nom') border-red-500 @enderror"
                               placeholder="Ex: 6ème A, Terminale S"
                               required>
                        @error('nom')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Niveau -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Niveau <span class="text-red-500">*</span>
                        </label>
                        <select name="niveau" 
                                id="niveau"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all @error('niveau') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionnez un niveau</option>
                            <option value="Primaire" {{ old('niveau', $classe->niveau) == 'Primaire' ? 'selected' : '' }}>Primaire</option>
                            <option value="Collège" {{ old('niveau', $classe->niveau) == 'Collège' ? 'selected' : '' }}>Collège</option>
                            <option value="Lycée" {{ old('niveau', $classe->niveau) == 'Lycée' ? 'selected' : '' }}>Lycée</option>
                        </select>
                        @error('niveau')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Série (visible seulement pour Lycée) -->
                    <div class="space-y-2" id="serieField" style="{{ old('niveau', $classe->niveau) == 'Lycée' ? '' : 'display: none;' }}">
                        <label class="block text-sm font-medium text-gray-700">
                            Série
                        </label>
                        <select name="serie" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all @error('serie') border-red-500 @enderror">
                            <option value="">Sélectionnez une série</option>
                            <option value="L" {{ old('serie', $classe->serie) == 'L' ? 'selected' : '' }}>Littéraire (L)</option>
                            <option value="S" {{ old('serie', $classe->serie) == 'S' ? 'selected' : '' }}>Scientifique (S)</option>
                            <option value="SE" {{ old('serie', $classe->serie) == 'SE' ? 'selected' : '' }}>Sciences Expérimentales (SE)</option>
                            <option value="STEG" {{ old('serie', $classe->serie) == 'STEG' ? 'selected' : '' }}>Sciences Technologiques (STEG)</option>
                            <option value="STT" {{ old('serie', $classe->serie) == 'STT' ? 'selected' : '' }}>Sciences Techniques (STT)</option>
                        </select>
                        @error('serie')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Capacité -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Capacité <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="capacite" 
                               value="{{ old('capacite', $classe->capacite) }}"
                               min="10"
                               max="60"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all @error('capacite') border-red-500 @enderror"
                               required>
                        <p class="text-xs text-gray-400">Nombre maximum d'élèves (10-60)</p>
                        @error('capacite')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Année scolaire et professeur principal -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-calendar-alt text-yellow-600 mr-2"></i>
                    Année scolaire et affectation
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Année scolaire -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Année scolaire <span class="text-red-500">*</span>
                        </label>
                        <select name="annee_scolaire_id" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all @error('annee_scolaire_id') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionnez une année</option>
                            @foreach($anneesScolaires as $annee)
                                <option value="{{ $annee->id }}" {{ old('annee_scolaire_id', $classe->annee_scolaire_id) == $annee->id ? 'selected' : '' }}>
                                    {{ $annee->libelle }} {{ $annee->is_current ? '(En cours)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('annee_scolaire_id')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Professeur principal -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Professeur principal
                        </label>
                        <select name="professeur_principal_id" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all @error('professeur_principal_id') border-red-500 @enderror">
                            <option value="">Non assigné</option>
                            @foreach($professeurs as $professeur)
                                <option value="{{ $professeur->id }}" {{ old('professeur_principal_id', $classe->professeur_principal_id) == $professeur->id ? 'selected' : '' }}>
                                    {{ $professeur->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('professeur_principal_id')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Information sur l'effectif actuel -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                    <div>
                        <p class="text-sm text-blue-800 font-medium">Effectif actuel : {{ $classe->eleves_count ?? 0 }} élèves</p>
                        <p class="text-xs text-blue-600 mt-1">
                            La capacité ne peut pas être inférieure à l'effectif actuel.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                <a href="{{ route('etablissement.classes.show', $classe->id) }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-yellow-600 to-orange-600 text-white rounded-xl hover:from-yellow-700 hover:to-orange-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-save mr-2"></i>
                    Mettre à jour
                </button>
            </div>
        </form>

        <!-- Messages flash -->
        @if(session('success'))
            <div class="mt-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mt-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="mt-6 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded-lg">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                {{ session('warning') }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Afficher/masquer le champ série en fonction du niveau
    document.getElementById('niveau').addEventListener('change', function() {
        const serieField = document.getElementById('serieField');
        if (this.value === 'Lycée') {
            serieField.style.display = 'block';
        } else {
            serieField.style.display = 'none';
            document.querySelector('select[name="serie"]').value = '';
        }
    });

    // Validation côté client
    document.querySelector('form').addEventListener('submit', function(e) {
        const nom = document.querySelector('input[name="nom"]').value.trim();
        const capacite = parseInt(document.querySelector('input[name="capacite"]').value);
        const niveau = document.querySelector('select[name="niveau"]').value;
        const annee = document.querySelector('select[name="annee_scolaire_id"]').value;
        const effectifActuel = {{ $classe->eleves_count ?? 0 }};
        
        let errors = [];

        if (!nom) {
            errors.push('Le nom de la classe est obligatoire.');
        }
        
        if (!capacite) {
            errors.push('La capacité est obligatoire.');
        } else if (capacite < 10 || capacite > 60) {
            errors.push('La capacité doit être comprise entre 10 et 60 élèves.');
        } else if (capacite < effectifActuel) {
            errors.push(`La capacité (${capacite}) ne peut pas être inférieure à l'effectif actuel (${effectifActuel}).`);
        }
        
        if (!niveau) {
            errors.push('Le niveau est obligatoire.');
        }
        
        if (!annee) {
            errors.push('L\'année scolaire est obligatoire.');
        }

        if (errors.length > 0) {
            e.preventDefault();
            alert('❌ Erreurs de validation :\n- ' + errors.join('\n- '));
        }
    });

    // Confirmation avant modification de la capacité
    const capaciteInput = document.querySelector('input[name="capacite"]');
    const originalCapacite = capaciteInput.value;
    
    capaciteInput.addEventListener('change', function() {
        const effectifActuel = {{ $classe->eleves_count ?? 0 }};
        if (this.value < effectifActuel) {
            alert(`⚠️ Attention : La capacité (${this.value}) est inférieure à l'effectif actuel (${effectifActuel}).`);
        }
    });

    // Animation d'entrée
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        form.style.opacity = '0';
        form.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            form.style.transition = 'all 0.5s ease';
            form.style.opacity = '1';
            form.style.transform = 'translateY(0)';
        }, 100);
    });
</script>
@endpush

@push('styles')
<style>
    .input-field:focus {
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .fade-in {
        animation: fadeIn 0.5s ease forwards;
    }
</style>
@endpush