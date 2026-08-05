{{-- resources/views/etablissement/matieres/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Créer une matière - SYSCOL')
@section('page-title', 'Créer une nouvelle matière')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <!-- En-tête -->
        <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-book-open text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Nouvelle matière</h2>
                <p class="text-gray-500">Ajoutez une nouvelle matière au programme scolaire</p>
            </div>
        </div>

        <!-- Formulaire -->
        <form action="{{ route('etablissement.matieres.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Informations générales -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Informations générales
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nom de la matière -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Nom de la matière <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="nom" 
                               value="{{ old('nom') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('nom') border-red-500 @enderror"
                               placeholder="Ex: Mathématiques, Français, Physique..."
                               required>
                        @error('nom')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Code de la matière -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Code <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="code" 
                               value="{{ old('code') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('code') border-red-500 @enderror"
                               placeholder="Ex: MATH, FR, HG..."
                               required>
                        @error('code')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Coefficient -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Coefficient <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="coefficient" 
                               value="{{ old('coefficient', 1) }}"
                               min="0.5"
                               max="10"
                               step="0.5"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('coefficient') border-red-500 @enderror"
                               required>
                        <p class="text-xs text-gray-400">Valeur entre 0.5 et 10 (par pas de 0.5)</p>
                        @error('coefficient')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-align-left text-blue-600 mr-2"></i>
                    Description
                </h3>

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Description (optionnelle)
                    </label>
                    <textarea name="description" 
                              rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('description') border-red-500 @enderror"
                              placeholder="Description de la matière, objectifs pédagogiques...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Classes concernées -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-door-open text-blue-600 mr-2"></i>
                    Classes concernées
                </h3>

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Sélectionnez les classes où cette matière sera enseignée
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($classes as $classe)
                            <label class="flex items-center p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition-all">
                                <input type="checkbox" 
                                       name="classe_ids[]" 
                                       value="{{ $classe->id }}"
                                       {{ in_array($classe->id, old('classe_ids', [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 mr-3">
                                <div>
                                    <span class="font-medium text-gray-700">{{ $classe->nom }}</span>
                                    <p class="text-xs text-gray-500">{{ $classe->niveau }}</p>
                                </div>
                            </label>
                        @empty
                            <div class="col-span-3 text-center py-8 text-gray-500 bg-gray-50 rounded-xl">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Aucune classe disponible
                            </div>
                        @endforelse
                    </div>
                    @error('classe_ids')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Laissez vide si cette matière est enseignée dans toutes les classes
                    </p>
                </div>
            </div>

            <!-- Aperçu du coefficient -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                    Aperçu du coefficient
                </h3>

                <div class="bg-blue-50 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Coefficient actuel</p>
                            <p class="text-3xl font-bold text-blue-600" id="coeffPreview">1.0</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Impact sur la moyenne</p>
                            <p class="text-sm text-gray-600" id="impactPreview">
                                Note sur 20 x coefficient
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                <a href="{{ route('etablissement.matieres.index') }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-xl hover:from-blue-700 hover:to-cyan-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-save mr-2"></i>
                    Créer la matière
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Aperçu du coefficient en temps réel
    const coeffInput = document.querySelector('input[name="coefficient"]');
    const coeffPreview = document.getElementById('coeffPreview');
    const impactPreview = document.getElementById('impactPreview');

    function updateCoeffPreview() {
        const value = parseFloat(coeffInput.value) || 1;
        coeffPreview.textContent = value.toFixed(1);
        
        if (value === 1) {
            impactPreview.textContent = 'Note sur 20 (coefficient normal)';
        } else if (value > 1) {
            impactPreview.textContent = `Note x ${value.toFixed(1)} (pondération forte)`;
        } else {
            impactPreview.textContent = `Note x ${value.toFixed(1)} (pondération faible)`;
        }
    }

    coeffInput.addEventListener('input', updateCoeffPreview);
    updateCoeffPreview();

    // Mise en majuscules du code (comme dans l'exemple de validation)
    document.querySelector('input[name="code"]').addEventListener('input', function(e) {
        this.value = this.value.toUpperCase();
    });

    // Validation côté client (copié du modèle classe)
    document.querySelector('form').addEventListener('submit', function(e) {
        const nom = document.querySelector('input[name="nom"]').value;
        const code = document.querySelector('input[name="code"]').value;
        const coefficient = document.querySelector('input[name="coefficient"]').value;

        if (!nom || !code || !coefficient) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires.');
            return;
        }

        if (coefficient < 0.5 || coefficient > 10) {
            e.preventDefault();
            alert('Le coefficient doit être compris entre 0.5 et 10.');
            return;
        }
    });
</script>
@endpush