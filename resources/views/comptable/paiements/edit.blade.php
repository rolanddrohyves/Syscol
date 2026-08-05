{{-- resources/views/comptable/paiements/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Modifier le paiement - SYSCOL')
@section('page-title', 'Modifier le paiement')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <!-- En-tête -->
        <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-edit text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Modifier le paiement</h2>
                <p class="text-gray-500">Référence: {{ $paiement->reference ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Formulaire -->
        <form action="{{ route('comptable.paiements.update', $paiement->id) }}" method="POST" class="space-y-8" id="paiementForm">
            @csrf
            @method('PUT')

            <!-- Sélection de l'élève -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-user-graduate text-yellow-600 mr-2"></i>
                    Informations élève
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
                                <option value="{{ $classe->id }}" {{ old('classe_id', $paiement->eleve->classe_id) == $classe->id ? 'selected' : '' }}>
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
                            <option value="">Sélectionner un élève</option>
                            @foreach($elevesParClasse[$paiement->eleve->classe_id] ?? [] as $eleve)
                                <option value="{{ $eleve->id }}" {{ old('eleve_id', $paiement->eleve_id) == $eleve->id ? 'selected' : '' }}>
                                    {{ $eleve->prenom }} {{ $eleve->nom }} ({{ $eleve->matricule }})
                                </option>
                            @endforeach
                        </select>
                        @error('eleve_id')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Informations élève -->
                <div id="infoEleve" class="p-4 bg-yellow-50 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-yellow-500 to-orange-500 flex items-center justify-center mr-3 text-white font-bold">
                                {{ substr($paiement->eleve->prenom, 0, 1) }}{{ substr($paiement->eleve->nom, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $paiement->eleve->prenom }} {{ $paiement->eleve->nom }}</p>
                                <p class="text-sm text-gray-600">Matricule: {{ $paiement->eleve->matricule }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Total des frais</p>
                            <p class="font-bold text-yellow-600" id="totalFrais">Chargement...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Situation financière -->
            <div id="situationFinanciere" class="hidden space-y-4 p-4 bg-blue-50 rounded-xl">
                <h4 class="font-semibold text-blue-800">📊 Situation financière</h4>
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-xs text-blue-600">Total des frais</p>
                        <p class="font-bold text-blue-800" id="situationTotal">0 FCFA</p>
                    </div>
                    <div>
                        <p class="text-xs text-green-600">Déjà payé</p>
                        <p class="font-bold text-green-600" id="situationPaye">0 FCFA</p>
                    </div>
                    <div>
                        <p class="text-xs text-orange-600">Reste à payer</p>
                        <p class="font-bold text-orange-600" id="situationReste">0 FCFA</p>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="progressBar" class="bg-green-500 h-2 rounded-full" style="width: 0%"></div>
                </div>
            </div>

            <!-- Détails du paiement -->
            <div class="space-y-6 pt-4 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-info-circle text-yellow-600 mr-2"></i>
                    Détails du paiement
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Frais -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Type de frais <span class="text-red-500">*</span>
                        </label>
                        <select name="frais_id" 
                                id="frais_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all @error('frais_id') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionner un type</option>
                            @foreach($fraisParClasse['toutes'] ?? [] as $f)
                                <option value="{{ $f->id }}" 
                                        data-montant="{{ $f->montant }}"
                                        {{ old('frais_id', $paiement->frais_id) == $f->id ? 'selected' : '' }}>
                                    {{ $f->libelle }} - {{ number_format($f->montant, 0, ',', ' ') }} FCFA
                                </option>
                            @endforeach
                            @if(isset($fraisParClasse[$paiement->eleve->classe_id]))
                                @foreach($fraisParClasse[$paiement->eleve->classe_id] as $f)
                                    <option value="{{ $f->id }}" 
                                            data-montant="{{ $f->montant }}"
                                            {{ old('frais_id', $paiement->frais_id) == $f->id ? 'selected' : '' }}>
                                        {{ $f->libelle }} - {{ number_format($f->montant, 0, ',', ' ') }} FCFA
                                    </option>
                                @endforeach
                            @endif
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
                               value="{{ old('montant', $paiement->montant) }}"
                               min="1"
                               step="1"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all @error('montant') border-red-500 @enderror"
                               required>
                        <p class="text-xs text-gray-500" id="montantHint"></p>
                        @error('montant')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Date et mode -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Date de paiement <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="date_paiement" 
                           id="date_paiement"
                           value="{{ old('date_paiement', $paiement->date_paiement->format('Y-m-d')) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all @error('date_paiement') border-red-500 @enderror"
                           required>
                    @error('date_paiement')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Mode de paiement <span class="text-red-500">*</span>
                    </label>
                    <select name="mode_paiement" 
                            id="mode_paiement"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all @error('mode_paiement') border-red-500 @enderror"
                            required>
                        <option value="">Sélectionner un mode</option>
                        @foreach($modes as $value => $label)
                            <option value="{{ $value }}" {{ old('mode_paiement', $paiement->mode_paiement) == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('mode_paiement')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Référence et statut -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Référence
                    </label>
                    <input type="text" 
                           name="reference" 
                           id="reference"
                           value="{{ old('reference', $paiement->reference) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all @error('reference') border-red-500 @enderror"
                           placeholder="Laissez vide pour génération automatique">
                    @error('reference')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Statut <span class="text-red-500">*</span>
                    </label>
                    <select name="statut" 
                            id="statut"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all @error('statut') border-red-500 @enderror"
                            required>
                        <option value="paye" {{ old('statut', $paiement->statut) == 'paye' ? 'selected' : '' }}>✅ Payé</option>
                        <option value="en_attente" {{ old('statut', $paiement->statut) == 'en_attente' ? 'selected' : '' }}>⏳ En attente</option>
                        <option value="partiel" {{ old('statut', $paiement->statut) == 'partiel' ? 'selected' : '' }}>⚠ Partiel</option>
                        <option value="annule" {{ old('statut', $paiement->statut) == 'annule' ? 'selected' : '' }}>❌ Annulé</option>
                    </select>
                    @error('statut')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Commentaire -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                    Commentaire
                </label>
                <textarea name="commentaire" 
                          id="commentaire"
                          rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all @error('commentaire') border-red-500 @enderror"
                          placeholder="Informations complémentaires...">{{ old('commentaire', $paiement->commentaire) }}</textarea>
                @error('commentaire')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Récapitulatif -->
            <div class="p-6 bg-yellow-50 rounded-xl">
                <h4 class="text-sm font-semibold text-yellow-800 mb-3 flex items-center">
                    <i class="fas fa-receipt mr-2"></i>
                    Récapitulatif de la modification
                </h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-yellow-600">Élève:</span>
                        <span class="ml-2 font-medium text-gray-800" id="recapEleve">{{ $paiement->eleve->prenom }} {{ $paiement->eleve->nom }}</span>
                    </div>
                    <div>
                        <span class="text-yellow-600">Montant:</span>
                        <span class="ml-2 font-medium text-gray-800" id="recapMontant">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div>
                        <span class="text-yellow-600">Mode:</span>
                        <span class="ml-2 font-medium text-gray-800" id="recapMode">
                            @switch($paiement->mode_paiement)
                                @case('especes') Espèces @break
                                @case('cheque') Chèque @break
                                @case('virement') Virement @break
                                @case('carte') Carte bancaire @break
                                @case('mobile_money') Mobile Money @break
                                @default {{ ucfirst($paiement->mode_paiement) }}
                            @endswitch
                        </span>
                    </div>
                    <div>
                        <span class="text-yellow-600">Statut:</span>
                        <span class="ml-2 font-medium text-gray-800" id="recapStatut">
                            @if($paiement->statut == 'paye') Payé
                            @elseif($paiement->statut == 'partiel') Partiel
                            @elseif($paiement->statut == 'en_attente') En attente
                            @else Annulé
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Boutons -->
            <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('comptable.paiements.show', $paiement->id) }}" 
                   class="w-full sm:w-auto px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all text-center">
                    Annuler
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-yellow-600 to-orange-600 text-white rounded-xl hover:from-yellow-700 hover:to-orange-700 transition-all shadow-lg hover:shadow-xl">
                    <i class="fas fa-save mr-2"></i>
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Données des élèves par classe
    const elevesParClasse = @json($elevesParClasse ?? []);
    let resteAPayer = 0;

    // Gestion du changement de classe
    document.getElementById('classe_id').addEventListener('change', function() {
        const classeId = this.value;
        const eleveSelect = document.getElementById('eleve_id');
        const infoEleve = document.getElementById('infoEleve');
        
        eleveSelect.innerHTML = '<option value="">Sélectionner un élève</option>';
        infoEleve.classList.add('hidden');
        document.getElementById('situationFinanciere').classList.add('hidden');
        
        if (classeId && elevesParClasse[classeId]) {
            elevesParClasse[classeId].forEach(eleve => {
                const option = document.createElement('option');
                option.value = eleve.id;
                option.dataset.matricule = eleve.matricule;
                option.textContent = eleve.prenom + ' ' + eleve.nom + ' (' + eleve.matricule + ')';
                if (eleve.id == {{ $paiement->eleve_id }}) {
                    option.selected = true;
                }
                eleveSelect.appendChild(option);
            });
        }
        
        updateRecap();
    });

    // Gestion du changement d'élève
    document.getElementById('eleve_id').addEventListener('change', function() {
        const eleveId = this.value;
        const infoEleve = document.getElementById('infoEleve');
        
        if (eleveId) {
            chargerInfosEleve(eleveId);
            infoEleve.classList.remove('hidden');
        } else {
            infoEleve.classList.add('hidden');
            document.getElementById('situationFinanciere').classList.add('hidden');
        }
        
        updateRecap();
    });

    // Charger les informations de l'élève
    function chargerInfosEleve(eleveId) {
        fetch(`/api/eleves/${eleveId}/infos`)
            .then(response => response.json())
            .then(data => {
                const initiales = (data.prenom.charAt(0) + data.nom.charAt(0)).toUpperCase();
                const infoDiv = document.querySelector('#infoEleve');
                const initialesDiv = infoDiv.querySelector('.bg-gradient-to-br');
                if (initialesDiv) initialesDiv.textContent = initiales;
                
                const nomP = infoDiv.querySelector('.font-medium');
                if (nomP) nomP.textContent = data.prenom + ' ' + data.nom;
                
                const matriculeP = infoDiv.querySelector('.text-sm.text-gray-600');
                if (matriculeP) matriculeP.textContent = 'Matricule: ' + (data.matricule || 'N/A');
                
                document.getElementById('totalFrais').textContent = formatMontant(data.total_frais || 0);
                
                document.getElementById('situationTotal').textContent = formatMontant(data.total_frais || 0);
                document.getElementById('situationPaye').textContent = formatMontant(data.total_paye || 0);
                document.getElementById('situationReste').textContent = formatMontant(data.total_reste || 0);
                
                const pourcentage = data.total_frais > 0 ? (data.total_paye / data.total_frais) * 100 : 0;
                document.getElementById('progressBar').style.width = pourcentage + '%';
                document.getElementById('situationFinanciere').classList.remove('hidden');
                
                resteAPayer = data.total_reste || 0;
                const montantHint = document.getElementById('montantHint');
                if (montantHint) montantHint.textContent = `Reste à payer: ${formatMontant(resteAPayer)}`;
                document.getElementById('montant').max = resteAPayer;
            })
            .catch(error => console.error('Erreur:', error));
    }

    // Validation du montant
    document.getElementById('montant').addEventListener('input', function() {
        const montant = parseFloat(this.value);
        if (montant > resteAPayer && resteAPayer > 0) {
            this.value = resteAPayer;
            alert('Le montant ne peut pas dépasser le reste à payer (' + formatMontant(resteAPayer) + ')');
        }
        updateRecap();
    });

    // Mise à jour du récapitulatif
    function updateRecap() {
        const eleveSelect = document.getElementById('eleve_id');
        const montantInput = document.getElementById('montant');
        const modeSelect = document.getElementById('mode_paiement');
        const statutSelect = document.getElementById('statut');
        
        if (eleveSelect && eleveSelect.value) {
            const eleveText = eleveSelect.options[eleveSelect.selectedIndex]?.text.split(' (')[0] || '-';
            document.getElementById('recapEleve').textContent = eleveText;
        }
        
        if (montantInput && montantInput.value) {
            document.getElementById('recapMontant').textContent = formatMontant(parseFloat(montantInput.value));
        }
        
        if (modeSelect && modeSelect.value) {
            const modeText = modeSelect.options[modeSelect.selectedIndex]?.text || '-';
            document.getElementById('recapMode').textContent = modeText;
        }
        
        if (statutSelect && statutSelect.value) {
            const statutText = statutSelect.options[statutSelect.selectedIndex]?.text || '-';
            document.getElementById('recapStatut').textContent = statutText;
        }
    }

    function formatMontant(montant) {
        return parseFloat(montant).toLocaleString('fr-FR') + ' FCFA';
    }

    // Écouter les changements
    const modeSelect = document.getElementById('mode_paiement');
    if (modeSelect) modeSelect.addEventListener('change', updateRecap);
    
    const statutSelect = document.getElementById('statut');
    if (statutSelect) statutSelect.addEventListener('change', updateRecap);
    
    const fraisSelect = document.getElementById('frais_id');
    if (fraisSelect) fraisSelect.addEventListener('change', updateRecap);

    // Initialisation
    document.addEventListener('DOMContentLoaded', function() {
        updateRecap();
        const eleveId = document.getElementById('eleve_id')?.value;
        if (eleveId) {
            chargerInfosEleve(eleveId);
        }
    });
</script>
@endpush

@push('styles')
<style>
    .hover\:shadow-xl:hover {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    select:disabled, input:disabled {
        background-color: #f3f4f6;
        cursor: not-allowed;
    }
</style>
@endpush
@endsection