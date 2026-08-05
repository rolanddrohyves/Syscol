{{-- resources/views/cpe/retards/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Signaler un retard - CPE')
@section('page-title', 'Signaler un retard')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <!-- En-tête -->
        <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-clock text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Signaler un retard</h2>
                <p class="text-gray-500">Enregistrer un retard pour un élève</p>
            </div>
        </div>

        <!-- Formulaire -->
        <form action="{{ route('cpe.retards.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Sélection par classe et élève -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-user-graduate text-yellow-600 mr-2"></i>
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
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all @error('classe_id') border-red-500 @enderror"
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
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all @error('eleve_id') border-red-500 @enderror"
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

            <!-- Date et heure du retard -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-calendar-clock text-yellow-600 mr-2"></i>
                    Date et heure
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Date -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Date du retard <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="date" 
                               id="date"
                               value="{{ old('date', now()->format('Y-m-d')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all @error('date') border-red-500 @enderror"
                               required>
                        @error('date')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Heure -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Heure du retard <span class="text-red-500">*</span>
                        </label>
                        <input type="time" 
                               name="heure" 
                               id="heure"
                               value="{{ old('heure', '08:30') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all @error('heure') border-red-500 @enderror"
                               required>
                        @error('heure')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Actions rapides pour les heures courantes -->
                <div class="flex flex-wrap gap-3 mt-2">
                    <button type="button" onclick="setHeure('08:30')" class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 text-sm">
                        08:30 (Matin)
                    </button>
                    <button type="button" onclick="setHeure('10:00')" class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 text-sm">
                        10:00 (Récréation)
                    </button>
                    <button type="button" onclick="setHeure('14:30')" class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 text-sm">
                        14:30 (Après-midi)
                    </button>
                    <button type="button" onclick="setHeure('16:00')" class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 text-sm">
                        16:00 (Fin journée)
                    </button>
                </div>
            </div>

            <!-- Motif -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-pen-alt text-yellow-600 mr-2"></i>
                    Motif (optionnel)
                </h3>

                <div class="space-y-2">
                    <textarea name="motif" 
                              rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all @error('motif') border-red-500 @enderror"
                              placeholder="Raison du retard...">{{ old('motif') }}</textarea>
                    @error('motif')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Suggestions de motifs -->
                <div class="flex flex-wrap gap-2 mt-2">
                    <span class="text-xs text-gray-500 mr-2">Suggestions:</span>
                    <button type="button" onclick="ajouterMotif('Transport')" class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded hover:bg-gray-200">
                        Transport
                    </button>
                    <button type="button" onclick="ajouterMotif('Médecin')" class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded hover:bg-gray-200">
                        Médecin
                    </button>
                    <button type="button" onclick="ajouterMotif('Famille')" class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded hover:bg-gray-200">
                        Famille
                    </button>
                    <button type="button" onclick="ajouterMotif('Réveil')" class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded hover:bg-gray-200">
                        Réveil tardif
                    </button>
                </div>
            </div>

            <!-- Informations complémentaires -->
            <div class="space-y-4 p-4 bg-yellow-50 rounded-xl">
                <h4 class="text-sm font-semibold text-yellow-800 flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    Information
                </h4>
                <p class="text-xs text-yellow-700">
                    Le retard sera enregistré et visible dans le tableau de bord. 
                    Vous pourrez le modifier ou le justifier ultérieurement si nécessaire.
                </p>
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('cpe.retards.index') }}" 
                   class="w-full sm:w-auto px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all text-center">
                    Annuler
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-yellow-600 to-orange-600 text-white rounded-xl hover:from-yellow-700 hover:to-orange-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-save mr-2"></i>
                    Enregistrer le retard
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

    // Fonction pour définir l'heure rapidement
    function setHeure(heure) {
        document.getElementById('heure').value = heure;
    }

    // Fonction pour ajouter un motif
    function ajouterMotif(motif) {
        const textarea = document.querySelector('textarea[name="motif"]');
        const currentValue = textarea.value;
        if (currentValue) {
            textarea.value = currentValue + ', ' + motif.toLowerCase();
        } else {
            textarea.value = motif;
        }
    }

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

    // Validation du formulaire
    document.querySelector('form').addEventListener('submit', function(e) {
        const classe = document.getElementById('classe_id').value;
        const eleve = document.getElementById('eleve_id').value;
        const date = document.querySelector('input[name="date"]').value;
        const heure = document.querySelector('input[name="heure"]').value;
        
        let errors = [];

        if (!classe) errors.push('Veuillez sélectionner une classe');
        if (!eleve) errors.push('Veuillez sélectionner un élève');
        if (!date) errors.push('Veuillez sélectionner une date');
        if (!heure) errors.push('Veuillez saisir une heure');

        if (errors.length > 0) {
            e.preventDefault();
            alert('Erreurs :\n- ' + errors.join('\n- '));
        }
    });

    // Empêcher la sélection de dates futures (optionnel)
    const dateInput = document.getElementById('date');
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