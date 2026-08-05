{{-- resources/views/comptable/frais/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Nouveau frais - SYSCOL')
@section('page-title', 'Ajouter un frais de scolarité')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <div class="flex items-center space-x-4 mb-8 pb-6 border-b">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-tag text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold">Nouveau frais</h2>
                <p class="text-gray-500">Ajouter un frais de scolarité avec échéancier</p>
            </div>
        </div>

        <form action="{{ route('comptable.frais.store') }}" method="POST" class="space-y-6" id="fraisForm">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Année scolaire -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium">Année scolaire *</label>
                    <select name="annee_scolaire_id" id="annee_scolaire_id" class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 @error('annee_scolaire_id') border-red-500 @enderror" required>
                        <option value="">Sélectionner une année scolaire</option>
                        @foreach($anneesScolaires as $annee)
                            <option value="{{ $annee->id }}" {{ old('annee_scolaire_id') == $annee->id ? 'selected' : '' }}>
                                {{ $annee->libelle }} @if($annee->is_current) (En cours) @endif
                            </option>
                        @endforeach
                    </select>
                    @error('annee_scolaire_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Classe -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium">Classe (optionnel)</label>
                    <select name="classe_id" id="classe_id" class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 @error('classe_id') border-red-500 @enderror">
                        <option value="">Toutes les classes</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}" {{ old('classe_id') == $classe->id ? 'selected' : '' }}>
                                {{ $classe->nom }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500">Laissez vide pour appliquer à toutes les classes</p>
                    @error('classe_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium">Type de frais *</label>
                    <select name="type" id="type_frais" class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 @error('type') border-red-500 @enderror" required>
                        @foreach($types as $value => $label)
                            <option value="{{ $value }}" {{ old('type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Libellé -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium">Libellé *</label>
                    <input type="text" name="libelle" id="libelle" value="{{ old('libelle') }}" 
                           class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 @error('libelle') border-red-500 @enderror" 
                           placeholder="Ex: Frais d'inscription, Scolarité, etc." required>
                    @error('libelle')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Périodicité -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium">Périodicité *</label>
                    <select name="periodicite" id="periodicite" class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 @error('periodicite') border-red-500 @enderror" required>
                        @foreach($periodicites as $value => $label)
                            <option value="{{ $value }}" {{ old('periodicite') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('periodicite')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Montant total -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium">Montant total (FCFA) *</label>
                    <input type="number" name="montant" id="montant_total" value="{{ old('montant') }}" 
                           class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 @error('montant') border-red-500 @enderror" 
                           placeholder="Ex: 250000" required>
                    @error('montant')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Obligatoire -->
                <div class="space-y-2 flex items-center">
                    <label class="flex items-center space-x-3">
                        <input type="checkbox" name="obligatoire" value="1" {{ old('obligatoire', true) ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded">
                        <span class="text-sm font-medium">Frais obligatoire</span>
                    </label>
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <label class="block text-sm font-medium">Description</label>
                <textarea name="description" rows="2" class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror" 
                          placeholder="Description détaillée du frais...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Section Échéancier -->
            <div id="echeancierSection" class="hidden pt-4 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
                    Configuration de l'échéancier
                </h3>
                <p class="text-sm text-gray-600 mb-4">Définissez les dates et montants des versements</p>

                <!-- Options de génération automatique -->
                <div class="bg-blue-50 rounded-xl p-4 mb-6">
                    <h4 class="font-medium text-blue-800 mb-3">Génération automatique</h4>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de versements</label>
                            <select id="nbVersements" class="w-full px-3 py-2 border rounded-lg">
                                <option value="1">1 versement (Annuel)</option>
                                <option value="3">3 versements (Trimestriel)</option>
                                <option value="6">6 versements (Semestriel)</option>
                                <option value="9" selected>9 versements (Mensuel)</option>
                                <option value="12">12 versements (Mensuel)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date premier versement</label>
                            <input type="date" id="datePremierVersement" class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jour d'échéance</label>
                            <select id="jourEcheance" class="w-full px-3 py-2 border rounded-lg">
                                <option value="1">1er du mois</option>
                                <option value="5">5 du mois</option>
                                <option value="10">10 du mois</option>
                                <option value="15">15 du mois</option>
                                <option value="20">20 du mois</option>
                                <option value="25">25 du mois</option>
                                <option value="dernier">Dernier jour du mois</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="button" id="btnGenererEcheances" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                <i class="fas fa-cogs mr-2"></i> Générer
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Liste des échéances -->
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="font-medium text-gray-700">Liste des versements</h4>
                        <button type="button" id="ajouterVersement" class="px-3 py-1 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 text-sm">
                            <i class="fas fa-plus mr-1"></i> Ajouter un versement
                        </button>
                    </div>
                    
                    <div id="echeancesList" class="space-y-3">
                        <!-- Les versements seront ajoutés ici dynamiquement -->
                    </div>
                </div>

                <!-- Récapitulatif -->
                <div class="mt-4 p-4 bg-blue-50 rounded-xl">
                    <h4 class="text-sm font-semibold text-blue-800 mb-3">Récapitulatif</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="text-blue-600">Total général:</span>
                            <span class="ml-2 font-bold text-gray-800" id="recapTotal">0 FCFA</span>
                        </div>
                        <div>
                            <span class="text-blue-600">Nombre de versements:</span>
                            <span class="ml-2 font-bold text-gray-800" id="recapNbVersements">0</span>
                        </div>
                        <div>
                            <span class="text-blue-600">Mensualité moyenne:</span>
                            <span class="ml-2 font-bold text-gray-800" id="recapMensuel">0 FCFA</span>
                        </div>
                        <div>
                            <span class="text-blue-600">Statut:</span>
                            <span class="ml-2 font-bold text-gray-800" id="recapStatut">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Message pour les frais uniques -->
            <div id="uniqueMessage" class="hidden p-4 bg-green-50 rounded-xl">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-green-600 mr-3 text-xl"></i>
                    <div>
                        <p class="font-medium text-green-800">Paiement unique</p>
                        <p class="text-sm text-green-600">Ce frais sera payé en une seule fois. Aucun échéancier n'est nécessaire.</p>
                    </div>
                </div>
            </div>

            <!-- Boutons -->
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('comptable.frais.index') }}" class="px-6 py-3 border rounded-xl hover:bg-gray-50 transition-colors">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Créer le frais
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let versementIndex = 0;
    let echeancesData = [];

    // Initialisation
    document.addEventListener('DOMContentLoaded', function() {
        // Date par défaut (septembre de l'année en cours)
        const dateDefaut = new Date();
        dateDefaut.setMonth(8); // Septembre
        dateDefaut.setDate(5);
        document.getElementById('datePremierVersement').value = formatDate(dateDefaut);
        
        // Gestion du type de frais
        const typeSelect = document.getElementById('type_frais');
        const periodiciteSelect = document.getElementById('periodicite');
        const echeancierSection = document.getElementById('echeancierSection');
        const uniqueMessage = document.getElementById('uniqueMessage');
        
        function updateEcheancierVisibility() {
            const type = typeSelect.value;
            const periodicite = periodiciteSelect.value;
            const isScolarite = type === 'scolarite';
            const hasEcheances = periodicite !== 'unique';
            
            if (isScolarite && hasEcheances) {
                echeancierSection.classList.remove('hidden');
                uniqueMessage.classList.add('hidden');
                genererEcheancesAuto();
            } else if (isScolarite && periodicite === 'unique') {
                echeancierSection.classList.add('hidden');
                uniqueMessage.classList.remove('hidden');
                document.getElementById('uniqueMessage').innerHTML = `
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-green-600 mr-3 text-xl"></i>
                        <div>
                            <p class="font-medium text-green-800">Scolarité en paiement unique</p>
                            <p class="text-sm text-green-600">Le montant total sera payé en une seule fois.</p>
                        </div>
                    </div>
                `;
            } else {
                echeancierSection.classList.add('hidden');
                uniqueMessage.classList.remove('hidden');
                const typeLabels = {
                    'inscription': "Frais d'inscription - paiement unique à l'entrée",
                    'cantine': 'Frais de cantine - paiement unique ou à définir',
                    'transport': 'Frais de transport - paiement unique ou à définir',
                    'sortie': 'Frais de sortie - paiement unique',
                    'autre': 'Paiement unique'
                };
                document.getElementById('uniqueMessage').innerHTML = `
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-green-600 mr-3 text-xl"></i>
                        <div>
                            <p class="font-medium text-green-800">Paiement unique</p>
                            <p class="text-sm text-green-600">${typeLabels[type] || 'Paiement unique'}</p>
                        </div>
                    </div>
                `;
            }
        }
        
        typeSelect.addEventListener('change', updateEcheancierVisibility);
        periodiciteSelect.addEventListener('change', updateEcheancierVisibility);
        updateEcheancierVisibility();
        
        // Génération automatique
        document.getElementById('btnGenererEcheances').addEventListener('click', function() {
            genererEcheancesAuto();
        });
        
        document.getElementById('ajouterVersement').addEventListener('click', function() {
            ajouterVersementLigne(versementIndex, 0, new Date());
            versementIndex++;
            updateRecap();
        });
        
        document.getElementById('montant_total').addEventListener('input', function() {
            if (typeSelect.value === 'scolarite') {
                genererEcheancesAuto();
            }
        });
    });
    
    function genererEcheancesAuto() {
        const nbVersements = parseInt(document.getElementById('nbVersements').value);
        const dateDebut = new Date(document.getElementById('datePremierVersement').value);
        const jourEcheance = document.getElementById('jourEcheance').value;
        const montantTotal = parseFloat(document.getElementById('montant_total').value) || 0;
        
        if (!dateDebut || isNaN(dateDebut.getTime())) {
            alert('Veuillez sélectionner une date de premier versement');
            return;
        }
        
        const montantParVersement = montantTotal / nbVersements;
        const container = document.getElementById('echeancesList');
        container.innerHTML = '';
        
        let date = new Date(dateDebut);
        
        for (let i = 0; i < nbVersements; i++) {
            let dateLimite = new Date(date);
            
            if (jourEcheance === 'dernier') {
                dateLimite = new Date(date.getFullYear(), date.getMonth() + 1, 0);
            } else {
                dateLimite.setDate(parseInt(jourEcheance));
            }
            
            let montant = montantParVersement;
            if (i === nbVersements - 1) {
                let totalCalcule = montantParVersement * (nbVersements - 1);
                montant = montantTotal - totalCalcule;
            }
            
            ajouterVersementLigne(i, montant, dateLimite);
            date.setMonth(date.getMonth() + 1);
        }
        
        versementIndex = nbVersements;
        updateRecap();
    }
    
    function ajouterVersementLigne(index, montant, dateLimite) {
        const container = document.getElementById('echeancesList');
        const div = document.createElement('div');
        div.className = 'versement-item grid grid-cols-1 md:grid-cols-4 gap-3 items-end p-3 bg-gray-50 rounded-lg';
        
        const moisNoms = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        const moisNom = dateLimite ? moisNoms[dateLimite.getMonth()] : '';
        const annee = dateLimite ? dateLimite.getFullYear() : '';
        const libelleDefaut = `Versement ${index + 1} - ${moisNom} ${annee}`;
        
        div.innerHTML = `
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Libellé</label>
                <input type="text" name="echeances[${index}][libelle]" class="w-full px-3 py-2 border rounded-lg" value="${libelleDefaut}">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Montant (FCFA)</label>
                <input type="number" name="echeances[${index}][montant]" class="w-full px-3 py-2 border rounded-lg versement-montant" value="${Math.round(montant)}" step="1">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Date limite</label>
                <input type="date" name="echeances[${index}][date_limite]" class="w-full px-3 py-2 border rounded-lg versement-date" value="${dateLimite ? formatDate(dateLimite) : ''}">
            </div>
            <div>
                <button type="button" class="supprimer-versement text-red-500 hover:text-red-700">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            </div>
        `;
        
        container.appendChild(div);
        
        div.querySelector('.supprimer-versement').addEventListener('click', function() {
            div.remove();
            updateRecap();
        });
        
        div.querySelectorAll('input').forEach(input => {
            input.addEventListener('change', updateRecap);
            input.addEventListener('input', updateRecap);
        });
    }
    
    function updateRecap() {
        let total = 0;
        let nbVersements = 0;
        let montants = [];
        
        document.querySelectorAll('.versement-montant').forEach(input => {
            const montant = parseFloat(input.value) || 0;
            total += montant;
            montants.push(montant);
            nbVersements++;
        });
        
        const montantTotal = parseFloat(document.getElementById('montant_total').value) || 0;
        
        document.getElementById('recapTotal').innerHTML = total.toLocaleString('fr-FR') + ' FCFA';
        document.getElementById('recapNbVersements').textContent = nbVersements;
        
        if (nbVersements > 0) {
            const moyenne = total / nbVersements;
            document.getElementById('recapMensuel').innerHTML = Math.round(moyenne).toLocaleString('fr-FR') + ' FCFA';
        } else {
            document.getElementById('recapMensuel').innerHTML = '0 FCFA';
        }
        
        if (Math.abs(total - montantTotal) > 1 && montantTotal > 0 && nbVersements > 0) {
            document.getElementById('recapStatut').innerHTML = '<span class="text-red-600">⚠️ Incohérence: ' + Math.abs(total - montantTotal).toLocaleString('fr-FR') + ' FCFA</span>';
        } else if (nbVersements > 0) {
            document.getElementById('recapStatut').innerHTML = '<span class="text-green-600">✓ Cohérent</span>';
        } else {
            document.getElementById('recapStatut').innerHTML = '<span class="text-gray-500">-</span>';
        }
    }
    
    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }
</script>
@endpush

@push('styles')
<style>
    .versement-item {
        transition: all 0.3s ease;
    }
    .versement-item:hover {
        background-color: #f3f4f6;
    }
</style>
@endpush
@endsection