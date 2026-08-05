{{-- resources/views/etablissement/annes-scolaires/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Créer une année scolaire - SYSCOL')
@section('page-title', 'Créer une nouvelle année scolaire')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <!-- En-tête -->
        <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-calendar-plus text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Nouvelle année scolaire</h2>
                <p class="text-gray-500">Ajouter une nouvelle année scolaire à l'établissement</p>
            </div>
        </div>

        <!-- Formulaire -->
        <form action="{{ route('etablissement.annes_scolaires.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Libellé -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                    Libellé de l'année <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="libelle" 
                       value="{{ old('libelle') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('libelle') border-red-500 @enderror"
                       placeholder="Ex: 2024-2025, Année scolaire 2024"
                       required>
                @error('libelle')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Dates -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Date de début <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="date_debut" 
                           id="date_debut"
                           value="{{ old('date_debut') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('date_debut') border-red-500 @enderror"
                           required>
                    @error('date_debut')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Date de fin <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="date_fin" 
                           id="date_fin"
                           value="{{ old('date_fin') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('date_fin') border-red-500 @enderror"
                           required>
                    @error('date_fin')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Durée calculée automatiquement -->
            <div class="p-4 bg-indigo-50 rounded-xl">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-indigo-800">Durée totale :</span>
                    <span class="text-lg font-bold text-indigo-600" id="duree">-</span>
                </div>
            </div>

            <!-- Année en cours -->
            <div class="flex items-center space-x-3 p-4 bg-gray-50 rounded-xl">
                <input type="checkbox" 
                       name="is_current" 
                       id="is_current"
                       value="1"
                       {{ old('is_current') ? 'checked' : '' }}
                       class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                <label for="is_current" class="text-sm font-medium text-gray-700">
                    Marquer comme année scolaire en cours
                </label>
            </div>

            <!-- Avertissement si année en cours -->
            <div id="currentWarning" class="hidden p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mr-3"></i>
                    <div>
                        <h4 class="text-sm font-semibold text-yellow-800">Attention</h4>
                        <p class="text-xs text-yellow-700">
                            En marquant cette année comme "en cours", les autres années perdront ce statut.
                            Les trimestres de cette année seront créés automatiquement.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Aperçu des trimestres -->
            <div class="space-y-4 p-4 bg-gray-50 rounded-xl">
                <h4 class="text-sm font-semibold text-gray-700 flex items-center">
                    <i class="fas fa-layer-group text-indigo-600 mr-2"></i>
                    Aperçu des trimestres (créés automatiquement)
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3" id="trimestresPreview">
                    <!-- Les trimestres seront générés dynamiquement par JavaScript -->
                </div>
                
                <p class="text-xs text-gray-500 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Les 3 trimestres seront automatiquement créés selon les dates de l'année.
                </p>
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center justify-end space-x-4 pt-4 border-t">
                <a href="{{ route('etablissement.annes_scolaires.index') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-xl hover:bg-gray-50 transition-all">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg">
                    <i class="fas fa-save mr-2"></i>
                    Créer l'année scolaire
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Éléments du DOM
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');
    const dureeSpan = document.getElementById('duree');
    const isCurrentCheckbox = document.getElementById('is_current');
    const currentWarning = document.getElementById('currentWarning');
    const trimestresPreview = document.getElementById('trimestresPreview');

    // Fonction pour formater une date en jj/mm/aaaa
    function formatDate(date) {
        const d = new Date(date);
        const jour = d.getDate().toString().padStart(2, '0');
        const mois = (d.getMonth() + 1).toString().padStart(2, '0');
        const annee = d.getFullYear();
        return `${jour}/${mois}/${annee}`;
    }

    // Fonction pour calculer la durée
    function calculerDuree() {
        if (dateDebut.value && dateFin.value) {
            const debut = new Date(dateDebut.value);
            const fin = new Date(dateFin.value);
            
            if (fin <= debut) {
                dureeSpan.textContent = 'Date de fin doit être après date de début';
                trimestresPreview.innerHTML = '';
                return;
            }
            
            const diffTime = Math.abs(fin - debut);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            const mois = Math.floor(diffDays / 30);
            const jours = diffDays % 30;
            let dureeTexte = '';
            if (mois > 0) dureeTexte += mois + ' mois ';
            if (jours > 0) dureeTexte += jours + ' jours';
            dureeSpan.textContent = dureeTexte || diffDays + ' jours';
            
            // Générer l'aperçu des trimestres
            genererTrimestres(debut, fin, diffDays);
        } else {
            dureeSpan.textContent = '-';
            trimestresPreview.innerHTML = '';
        }
    }

    // Fonction pour générer l'aperçu des trimestres
    function genererTrimestres(debut, fin, dureeTotale) {
        const dureeTrimestre = Math.floor(dureeTotale / 3);
        
        const trimestres = [
            {
                numero: 1,
                libelle: 'Trimestre 1',
                date_debut: new Date(debut),
                date_fin: new Date(debut.getTime() + (dureeTrimestre * 24 * 60 * 60 * 1000))
            },
            {
                numero: 2,
                libelle: 'Trimestre 2',
                date_debut: new Date(debut.getTime() + ((dureeTrimestre + 1) * 24 * 60 * 60 * 1000)),
                date_fin: new Date(debut.getTime() + (dureeTrimestre * 2 * 24 * 60 * 60 * 1000))
            },
            {
                numero: 3,
                libelle: 'Trimestre 3',
                date_debut: new Date(debut.getTime() + ((dureeTrimestre * 2 + 1) * 24 * 60 * 60 * 1000)),
                date_fin: new Date(fin)
            }
        ];

        let html = '';
        trimestres.forEach((t, index) => {
            const debutStr = formatDate(t.date_debut);
            const finStr = formatDate(t.date_fin);
            const duree = Math.round((t.date_fin - t.date_debut) / (1000 * 60 * 60 * 24));
            
            html += `
                <div class="p-3 bg-white rounded-lg border border-gray-200">
                    <p class="font-medium text-gray-800 text-sm">${t.libelle}</p>
                    <p class="text-xs text-gray-500 mt-1">${debutStr} - ${finStr}</p>
                    <p class="text-xs text-gray-400 mt-1">${duree} jours</p>
                </div>
            `;
        });
        
        trimestresPreview.innerHTML = html;
    }

    // Afficher/masquer l'avertissement pour l'année en cours
    function toggleWarning() {
        if (isCurrentCheckbox.checked) {
            currentWarning.classList.remove('hidden');
        } else {
            currentWarning.classList.add('hidden');
        }
    }

    // Écouteurs d'événements
    dateDebut.addEventListener('change', calculerDuree);
    dateFin.addEventListener('change', calculerDuree);
    isCurrentCheckbox.addEventListener('change', toggleWarning);

    // Initialisation
    if (dateDebut.value && dateFin.value) {
        calculerDuree();
    }

    // Validation du formulaire
    document.querySelector('form').addEventListener('submit', function(e) {
        const libelle = document.querySelector('input[name="libelle"]').value;
        const debut = dateDebut.value;
        const fin = dateFin.value;
        
        let errors = [];

        if (!libelle) errors.push('Le libellé est requis');
        if (!debut) errors.push('La date de début est requise');
        if (!fin) errors.push('La date de fin est requise');

        if (debut && fin && new Date(fin) <= new Date(debut)) {
            errors.push('La date de fin doit être après la date de début');
        }

        if (errors.length > 0) {
            e.preventDefault();
            alert('❌ Erreurs :\n- ' + errors.join('\n- '));
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