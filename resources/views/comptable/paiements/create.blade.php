{{-- resources/views/comptable/paiements/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Ajouter un paiement - SYSCOL')
@section('page-title', 'Ajouter un paiement')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <!-- En-tête -->
        <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-money-bill-wave text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Nouveau paiement</h2>
                <p class="text-gray-500">Enregistrer un paiement de frais de scolarité</p>
            </div>
        </div>

        <!-- Formulaire -->
        <form action="{{ route('comptable.paiements.store') }}" method="POST" class="space-y-8" id="paiementForm">
            @csrf

            <!-- Sélection par classe et élève -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-user-graduate text-green-600 mr-2"></i>
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
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all @error('classe_id') border-red-500 @enderror"
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
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all @error('eleve_id') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionner d'abord une classe</option>
                            @if($classeId && isset($elevesParClasse[$classeId]))
                                @foreach($elevesParClasse[$classeId] as $eleve)
                                    <option value="{{ $eleve->id }}" 
                                            data-matricule="{{ $eleve->matricule }}"
                                            {{ old('eleve_id', $eleveId) == $eleve->id ? 'selected' : '' }}>
                                        {{ $eleve->prenom }} {{ $eleve->nom }} ({{ $eleve->matricule }})
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('eleve_id')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Informations élève sélectionné -->
                <div id="infoEleve" class="hidden p-4 bg-green-50 rounded-xl">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-500 to-emerald-500 flex items-center justify-center mr-3 text-white font-bold">
                            <span id="eleveInitiales"></span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800" id="eleveNom"></p>
                            <p class="text-sm text-gray-600" id="eleveMatricule"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Frais et montant -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-tag text-green-600 mr-2"></i>
                    Frais et montant
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Frais -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Type de frais <span class="text-red-500">*</span>
                        </label>
                        <select name="frais_id" 
                                id="frais_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all @error('frais_id') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionner d'abord une classe</option>
                        </select>
                        @error('frais_id')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Montant -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Montant (FCFA) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="montant" 
                               id="montant"
                               value="{{ old('montant') }}"
                               min="1"
                               step="1"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all @error('montant') border-red-500 @enderror"
                               placeholder="Ex: 50000"
                               required>
                        <p class="text-xs text-gray-500">Saisissez le montant exact (exemple: 25000, 50000, 75500)</p>
                        @error('montant')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Rappel du montant du frais -->
                <div id="montantFrais" class="text-sm text-green-600 hidden">
                    <i class="fas fa-info-circle mr-1"></i>
                    Montant recommandé: <span id="montantRecommended"></span> FCFA
                </div>
            </div>

            <!-- Date et mode de paiement -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-calendar-alt text-green-600 mr-2"></i>
                    Date et mode de paiement
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Date -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Date de paiement <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="date_paiement" 
                               id="date_paiement"
                               value="{{ old('date_paiement', now()->format('Y-m-d')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all @error('date_paiement') border-red-500 @enderror"
                               required>
                        @error('date_paiement')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mode -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Mode de paiement <span class="text-red-500">*</span>
                        </label>
                        <select name="mode_paiement" 
                                id="mode_paiement"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all @error('mode_paiement') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionner un mode</option>
                            @foreach($modes as $value => $label)
                                <option value="{{ $value }}" {{ old('mode_paiement') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('mode_paiement')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Référence et statut -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-info-circle text-green-600 mr-2"></i>
                    Informations complémentaires
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Référence -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Référence (optionnel)
                        </label>
                        <input type="text" 
                               name="reference" 
                               id="reference"
                               value="{{ old('reference') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all @error('reference') border-red-500 @enderror"
                               placeholder="Ex: PAI-2025-001">
                        @error('reference')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Statut -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Statut <span class="text-red-500">*</span>
                        </label>
                        <select name="statut" 
                                id="statut"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all @error('statut') border-red-500 @enderror"
                                required>
                            <option value="paye" {{ old('statut', 'paye') == 'paye' ? 'selected' : '' }}>Payé</option>
                            <option value="en_attente" {{ old('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="annule" {{ old('statut') == 'annule' ? 'selected' : '' }}>Annulé</option>
                        </select>
                        @error('statut')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Commentaire -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Commentaire (optionnel)
                    </label>
                    <textarea name="commentaire" 
                              id="commentaire"
                              rows="2"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all @error('commentaire') border-red-500 @enderror"
                              placeholder="Informations complémentaires...">{{ old('commentaire') }}</textarea>
                    @error('commentaire')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Récapitulatif -->
            <div class="p-6 bg-green-50 rounded-xl">
                <h4 class="text-sm font-semibold text-green-800 mb-3 flex items-center">
                    <i class="fas fa-receipt mr-2"></i>
                    Récapitulatif du paiement
                </h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-green-600">Élève:</span>
                        <span class="ml-2 font-medium text-gray-800" id="recapEleve">-</span>
                    </div>
                    <div>
                        <span class="text-green-600">Frais:</span>
                        <span class="ml-2 font-medium text-gray-800" id="recapFrais">-</span>
                    </div>
                    <div>
                        <span class="text-green-600">Montant:</span>
                        <span class="ml-2 font-medium text-gray-800" id="recapMontant">-</span>
                    </div>
                    <div>
                        <span class="text-green-600">Date:</span>
                        <span class="ml-2 font-medium text-gray-800" id="recapDate">{{ now()->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('comptable.paiements.index') }}" 
                   class="w-full sm:w-auto px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all text-center">
                    Annuler
                </a>
                <button type="submit" 
                        id="submitBtn"
                        class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-save mr-2"></i>
                    Enregistrer le paiement
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Données des élèves par classe
    const elevesParClasse = @json($elevesParClasse ?? []);
    
    // Données des frais par classe
    const fraisParClasse = @json($fraisParClasse ?? []);
    
    // Gestion du changement de classe
    document.getElementById('classe_id').addEventListener('change', function() {
        const classeId = this.value;
        const eleveSelect = document.getElementById('eleve_id');
        const fraisSelect = document.getElementById('frais_id');
        const infoEleve = document.getElementById('infoEleve');
        
        // Vider et réinitialiser les selects
        eleveSelect.innerHTML = '<option value="">Sélectionner un élève</option>';
        fraisSelect.innerHTML = '<option value="">Sélectionner un type de frais</option>';
        infoEleve.classList.add('hidden');
        document.getElementById('montantFrais').classList.add('hidden');
        
        // Remplir les élèves
        if (classeId && elevesParClasse[classeId] && elevesParClasse[classeId].length > 0) {
            elevesParClasse[classeId].forEach(eleve => {
                const option = document.createElement('option');
                option.value = eleve.id;
                option.dataset.matricule = eleve.matricule;
                option.textContent = eleve.prenom + ' ' + eleve.nom + ' (' + eleve.matricule + ')';
                eleveSelect.appendChild(option);
            });
        } else if (classeId) {
            eleveSelect.innerHTML = '<option value="">Aucun élève dans cette classe</option>';
        }
        
        // Remplir les frais
        if (classeId) {
            let hasFrais = false;
            
            // Frais généraux (toutes classes)
            if (fraisParClasse['toutes'] && fraisParClasse['toutes'].length > 0) {
                fraisParClasse['toutes'].forEach(frais => {
                    ajouterOptionFrais(fraisSelect, frais);
                    hasFrais = true;
                });
            }
            
            // Frais spécifiques à la classe
            if (fraisParClasse[classeId] && fraisParClasse[classeId].length > 0) {
                fraisParClasse[classeId].forEach(frais => {
                    ajouterOptionFrais(fraisSelect, frais);
                    hasFrais = true;
                });
            }
            
            if (!hasFrais) {
                fraisSelect.innerHTML = '<option value="">Aucun frais disponible pour cette classe</option>';
            }
        } else {
            fraisSelect.innerHTML = '<option value="">Sélectionner d\'abord une classe</option>';
        }
        
        updateRecap();
    });
    
    // Ajouter une option de frais
    function ajouterOptionFrais(select, frais) {
        const option = document.createElement('option');
        option.value = frais.id;
        option.dataset.montant = frais.montant;
        let texte = frais.libelle + ' - ' + formatNombre(frais.montant) + ' FCFA';
        if (frais.obligatoire) texte += ' (Obligatoire)';
        option.textContent = texte;
        select.appendChild(option);
    }
    
    // Formatage des nombres
    function formatNombre(nombre) {
        return nombre.toLocaleString('fr-FR');
    }

    // Affichage des infos élève
    document.getElementById('eleve_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const infoEleve = document.getElementById('infoEleve');
        
        if (this.value && selectedOption && selectedOption.value) {
            const nom = selectedOption.text.split(' (')[0];
            const matricule = selectedOption.dataset.matricule;
            const initiales = nom.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
            
            document.getElementById('eleveInitiales').textContent = initiales;
            document.getElementById('eleveNom').textContent = nom;
            document.getElementById('eleveMatricule').textContent = 'Matricule: ' + (matricule || 'N/A');
            
            infoEleve.classList.remove('hidden');
        } else {
            infoEleve.classList.add('hidden');
        }
        
        updateRecap();
    });

    // Remplissage automatique du montant
    document.getElementById('frais_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const montantInput = document.getElementById('montant');
        const montantFrais = document.getElementById('montantFrais');
        const montantRecommended = document.getElementById('montantRecommended');
        
        if (this.value && selectedOption && selectedOption.dataset.montant) {
            const montant = parseFloat(selectedOption.dataset.montant);
            if (!isNaN(montant) && montant > 0) {
                montantInput.value = montant;
                montantRecommended.textContent = formatNombre(montant);
                montantFrais.classList.remove('hidden');
            }
        } else {
            montantFrais.classList.add('hidden');
            if (montantInput.value === '') {
                montantInput.value = '';
            }
        }
        
        updateRecap();
    });

    // Mise à jour du récapitulatif
    function updateRecap() {
        const eleveSelect = document.getElementById('eleve_id');
        const fraisSelect = document.getElementById('frais_id');
        const montantInput = document.getElementById('montant');
        const dateInput = document.getElementById('date_paiement');
        
        if (eleveSelect && eleveSelect.value && eleveSelect.selectedIndex >= 0) {
            const eleveText = eleveSelect.options[eleveSelect.selectedIndex]?.text.split(' (')[0] || '-';
            document.getElementById('recapEleve').textContent = eleveText;
        } else {
            document.getElementById('recapEleve').textContent = '-';
        }
        
        if (fraisSelect && fraisSelect.value && fraisSelect.selectedIndex >= 0) {
            const fraisText = fraisSelect.options[fraisSelect.selectedIndex]?.text.split(' - ')[0] || '-';
            document.getElementById('recapFrais').textContent = fraisText;
        } else {
            document.getElementById('recapFrais').textContent = '-';
        }
        
        if (montantInput && montantInput.value && parseFloat(montantInput.value) > 0) {
            document.getElementById('recapMontant').textContent = formatNombre(parseFloat(montantInput.value)) + ' FCFA';
        } else {
            document.getElementById('recapMontant').textContent = '-';
        }
        
        if (dateInput && dateInput.value) {
            const date = new Date(dateInput.value);
            document.getElementById('recapDate').textContent = date.toLocaleDateString('fr-FR');
        }
    }

    // Écouter les changements
    document.getElementById('montant')?.addEventListener('input', updateRecap);
    document.getElementById('date_paiement')?.addEventListener('change', updateRecap);
    document.getElementById('mode_paiement')?.addEventListener('change', updateRecap);
    document.getElementById('statut')?.addEventListener('change', updateRecap);

    // Initialisation au chargement
    document.addEventListener('DOMContentLoaded', function() {
        const classeSelect = document.getElementById('classe_id');
        if (classeSelect && classeSelect.value) {
            classeSelect.dispatchEvent(new Event('change'));
            
            const eleveId = '{{ $eleveId ?? '' }}';
            if (eleveId) {
                const eleveSelect = document.getElementById('eleve_id');
                setTimeout(() => {
                    if (eleveSelect) {
                        eleveSelect.value = eleveId;
                        eleveSelect.dispatchEvent(new Event('change'));
                    }
                }, 150);
            }
        }
        
        updateRecap();
    });

    // Validation du formulaire
    const form = document.getElementById('paiementForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const classe = document.getElementById('classe_id')?.value;
            const eleve = document.getElementById('eleve_id')?.value;
            const frais = document.getElementById('frais_id')?.value;
            const montant = document.getElementById('montant')?.value;
            const date = document.getElementById('date_paiement')?.value;
            
            const errors = [];
            if (!classe) errors.push('Veuillez sélectionner une classe');
            if (!eleve) errors.push('Veuillez sélectionner un élève');
            if (!frais) errors.push('Veuillez sélectionner un type de frais');
            if (!montant) errors.push('Veuillez saisir un montant');
            else if (parseFloat(montant) <= 0) errors.push('Le montant doit être supérieur à 0');
            if (!date) errors.push('Veuillez sélectionner une date');

            if (errors.length > 0) {
                e.preventDefault();
                alert('❌ Erreurs :\n- ' + errors.join('\n- '));
            }
        });
    }
</script>
@endpush

@push('styles')
<style>
    .hover\:-translate-y-1:hover {
        transform: translateY(-4px);
    }
    input[type="number"] {
        -moz-appearance: textfield;
    }
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    select:disabled, input:disabled {
        background-color: #f3f4f6;
        cursor: not-allowed;
    }
</style>
@endpush
@endsection