{{-- resources/views/etablissement/matieres/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Modifier une matière - SYSCOL')
@section('page-title', 'Modifier la matière')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <!-- En-tête -->
        <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-book-open text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Modifier la matière</h2>
                <p class="text-gray-500">{{ $matiere->nom }} ({{ $matiere->code }})</p>
            </div>
        </div>

        <!-- Formulaire -->
        <form action="{{ route('etablissement.matieres.update', $matiere->id) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

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
                               value="{{ old('nom', $matiere->nom) }}"
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
                               value="{{ old('code', $matiere->code) }}"
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
                               value="{{ old('coefficient', $matiere->coefficient) }}"
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

                    <!-- Niveau -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Niveau <span class="text-red-500">*</span>
                        </label>
                        <select name="niveau" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('niveau') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionnez un niveau</option>
                            <option value="Primaire" {{ old('niveau', $matiere->niveau) == 'Primaire' ? 'selected' : '' }}>Primaire</option>
                            <option value="Collège" {{ old('niveau', $matiere->niveau) == 'Collège' ? 'selected' : '' }}>Collège</option>
                            <option value="Lycée" {{ old('niveau', $matiere->niveau) == 'Lycée' ? 'selected' : '' }}>Lycée</option>
                            <option value="Tous" {{ old('niveau', $matiere->niveau) == 'Tous' ? 'selected' : '' }}>Tous les niveaux</option>
                        </select>
                        @error('niveau')
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
                              placeholder="Description de la matière, objectifs pédagogiques...">{{ old('description', $matiere->description) }}</textarea>
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
                    
                    <!-- ✅ Grille responsive comme dans create -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($classes as $classe)
                            <label class="flex items-center p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition-all">
                                <input type="checkbox" 
                                       name="classe_ids[]" 
                                       value="{{ $classe->id }}"
                                       {{ in_array($classe->id, old('classe_ids', $matiere->classes->pluck('id')->toArray())) ? 'checked' : '' }}
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

            <!-- Enseignants concernés -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-chalkboard-teacher text-blue-600 mr-2"></i>
                    Enseignants
                </h3>

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Sélectionnez les enseignants de cette matière
                    </label>
                    
                    <!-- ✅ Grille responsive comme dans create -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($enseignants as $id => $nom)
                            <label class="flex items-center p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition-all">
                                <input type="checkbox" 
                                       name="enseignant_ids[]" 
                                       value="{{ $id }}"
                                       {{ in_array($id, old('enseignant_ids', $matiere->enseignants->pluck('id')->toArray())) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 mr-3">
                                <div>
                                    <span class="font-medium text-gray-700">{{ $nom }}</span>
                                </div>
                            </label>
                        @empty
                            <div class="col-span-3 text-center py-8 text-gray-500 bg-gray-50 rounded-xl">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Aucun enseignant disponible
                            </div>
                        @endforelse
                    </div>
                    
                    @error('enseignant_ids')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Aperçu du coefficient -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                    Aperçu du coefficient
                </h3>

                <!-- ✅ Même style que create -->
                <div class="bg-blue-50 rounded-xl p-6">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-center sm:text-left">
                            <p class="text-sm text-gray-600">Coefficient actuel</p>
                            <p class="text-3xl font-bold text-blue-600" id="coeffPreview">{{ $matiere->coefficient }}</p>
                        </div>
                        <div class="text-center sm:text-right">
                            <p class="text-sm text-gray-600">Impact sur la moyenne</p>
                            <p class="text-sm text-gray-600" id="impactPreview">
                                @if($matiere->coefficient == 1)
                                    Note sur 20 (coefficient normal)
                                @elseif($matiere->coefficient > 1)
                                    Note x {{ number_format($matiere->coefficient, 1) }} (pondération forte)
                                @else
                                    Note x {{ number_format($matiere->coefficient, 1) }} (pondération faible)
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('etablissement.matieres.index') }}" 
                   class="w-full sm:w-auto px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all text-center">
                    Annuler
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-xl hover:from-blue-700 hover:to-cyan-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-save mr-2"></i>
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

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

    // Mise en majuscules du code
    document.querySelector('input[name="code"]').addEventListener('input', function(e) {
        this.value = this.value.toUpperCase();
    });

    // Validation côté client
    document.querySelector('form').addEventListener('submit', function(e) {
        const nom = document.querySelector('input[name="nom"]').value;
        const code = document.querySelector('input[name="code"]').value;
        const coefficient = document.querySelector('input[name="coefficient"]').value;
        const niveau = document.querySelector('select[name="niveau"]').value;

        if (!nom || !code || !coefficient || !niveau) {
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
@endsection