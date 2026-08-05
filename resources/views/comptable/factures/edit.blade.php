{{-- resources/views/comptable/factures/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Modifier une facture - SYSCOL')
@section('page-title', 'Modifier la facture')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <!-- En-tête -->
        <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-file-invoice text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Modifier la facture</h2>
                <p class="text-gray-500">{{ $facture->numero }}</p>
            </div>
        </div>

        <form action="{{ route('comptable.factures.update', $facture->id) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Sélection élève -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-user-graduate text-purple-600 mr-2"></i>
                    Élève concerné
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Classe <span class="text-red-500">*</span>
                        </label>
                        <select name="classe_id" id="classe_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 @error('classe_id') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionner une classe</option>
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}" 
                                    {{ ($facture->eleve->classe_id == $classe->id) ? 'selected' : '' }}>
                                    {{ $classe->nom }} ({{ $classe->niveau }})
                                </option>
                            @endforeach
                        </select>
                        @error('classe_id')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Élève <span class="text-red-500">*</span>
                        </label>
                        <select name="eleve_id" id="eleve_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 @error('eleve_id') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionner un élève</option>
                            @if($facture->eleve->classe_id && isset($elevesParClasse[$facture->eleve->classe_id]))
                                @foreach($elevesParClasse[$facture->eleve->classe_id] as $eleve)
                                    <option value="{{ $eleve->id }}" 
                                        {{ $facture->eleve_id == $eleve->id ? 'selected' : '' }}>
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

            <!-- Informations facture -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-info-circle text-purple-600 mr-2"></i>
                    Informations facture
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Numéro de facture <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="numero" value="{{ old('numero', $facture->numero) }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 bg-gray-50"
                               readonly>
                        @error('numero')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Date d'émission <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_emission" value="{{ old('date_emission', $facture->date_emission->format('Y-m-d')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500"
                               required>
                        @error('date_emission')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Date d'échéance <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_echeance" value="{{ old('date_echeance', $facture->date_echeance->format('Y-m-d')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500"
                               required>
                        @error('date_echeance')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Statut <span class="text-red-500">*</span>
                        </label>
                        <select name="statut" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500" required>
                            <option value="emise" {{ $facture->statut == 'emise' ? 'selected' : '' }}>Émise</option>
                            <option value="envoyee" {{ $facture->statut == 'envoyee' ? 'selected' : '' }}>Envoyée</option>
                            <option value="payee" {{ $facture->statut == 'payee' ? 'selected' : '' }}>Payée</option>
                            <option value="impayee" {{ $facture->statut == 'impayee' ? 'selected' : '' }}>Impayée</option>
                        </select>
                        @error('statut')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Montants -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-coins text-purple-600 mr-2"></i>
                    Montants
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Montant HT (FCFA) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="montant_ht" id="montant_ht" value="{{ old('montant_ht', $facture->montant_ht) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500"
                               required>
                        @error('montant_ht')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Montant TTC (FCFA) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="montant_ttc" id="montant_ttc" value="{{ old('montant_ttc', $facture->montant_ttc) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500"
                               required>
                        @error('montant_ttc')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- TVA automatique -->
                <div class="p-4 bg-purple-50 rounded-xl">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-purple-800">TVA (18%) :</span>
                        <span class="text-lg font-bold text-purple-600" id="tva">
                            {{ number_format($facture->montant_ttc - $facture->montant_ht, 0, ',', ' ') }} FCFA
                        </span>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-2 pt-6 border-t border-gray-100">
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" rows="3" 
                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500"
                          placeholder="Objet de la facture...">{{ old('description', $facture->description) }}</textarea>
                @error('description')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Boutons -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('comptable.factures.index') }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:from-purple-700 hover:to-pink-700 shadow-lg">
                    <i class="fas fa-save mr-2"></i>
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Gestion élèves par classe
    const elevesParClasse = @json($elevesParClasse ?? []);
    
    document.getElementById('classe_id').addEventListener('change', function() {
        const classeId = this.value;
        const eleveSelect = document.getElementById('eleve_id');
        
        eleveSelect.innerHTML = '<option value="">Sélectionner un élève</option>';
        
        if (classeId && elevesParClasse[classeId]) {
            elevesParClasse[classeId].forEach(eleve => {
                const option = document.createElement('option');
                option.value = eleve.id;
                option.textContent = eleve.prenom + ' ' + eleve.nom;
                eleveSelect.appendChild(option);
            });
            
            // Re-sélectionner l'élève actuel
            const currentEleveId = '{{ $facture->eleve_id }}';
            if (currentEleveId) {
                eleveSelect.value = currentEleveId;
            }
        }
    });

    // Calcul TVA
    function calculerTVA() {
        const ht = parseFloat(document.getElementById('montant_ht').value) || 0;
        const ttc = parseFloat(document.getElementById('montant_ttc').value) || 0;
        
        if (ht > 0) {
            const tva = ht * 0.18;
            document.getElementById('tva').textContent = tva.toLocaleString('fr-FR') + ' FCFA';
        } else if (ttc > 0) {
            const ht2 = ttc / 1.18;
            const tva2 = ttc - ht2;
            document.getElementById('tva').textContent = tva2.toLocaleString('fr-FR') + ' FCFA';
        }
    }

    document.getElementById('montant_ht').addEventListener('input', calculerTVA);
    document.getElementById('montant_ttc').addEventListener('input', calculerTVA);

    // Validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const classe = document.getElementById('classe_id').value;
        const eleve = document.getElementById('eleve_id').value;
        const ht = document.getElementById('montant_ht').value;
        const ttc = document.getElementById('montant_ttc').value;
        
        let errors = [];
        if (!classe) errors.push('Sélectionnez une classe');
        if (!eleve) errors.push('Sélectionnez un élève');
        if (!ht || ht <= 0) errors.push('Montant HT invalide');
        if (!ttc || ttc <= 0) errors.push('Montant TTC invalide');
        
        if (errors.length > 0) {
            e.preventDefault();
            alert('Erreurs :\n- ' + errors.join('\n- '));
        }
    });
</script>
@endpush
@endsection