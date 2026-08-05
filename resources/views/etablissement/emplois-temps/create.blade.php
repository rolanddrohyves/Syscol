{{-- resources/views/etablissement/emplois-temps/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Créer un emploi du temps - SYSCOL')
@section('page-title', 'Créer un nouvel emploi du temps')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <!-- En-tête -->
        <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-calendar-plus text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Nouvel emploi du temps</h2>
                <p class="text-gray-500">Ajouter un cours à l'emploi du temps</p>
            </div>
        </div>

        <!-- Formulaire -->
        <form action="{{ route('etablissement.emplois_temps.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Informations générales -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-info-circle text-indigo-600 mr-2"></i>
                    Informations générales
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Classe -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Classe <span class="text-red-500">*</span>
                        </label>
                        <select name="classe_id" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('classe_id') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionner une classe</option>
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}" {{ old('classe_id') == $classe->id ? 'selected' : '' }}>
                                    {{ $classe->nom }} ({{ $classe->niveau }})
                                </option>
                            @endforeach
                        </select>
                        @error('classe_id')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Matière -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Matière <span class="text-red-500">*</span>
                        </label>
                        <select name="matiere_id" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('matiere_id') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionner une matière</option>
                            @foreach($matieres as $matiere)
                                <option value="{{ $matiere->id }}" {{ old('matiere_id') == $matiere->id ? 'selected' : '' }}>
                                    {{ $matiere->nom }} (Coeff. {{ $matiere->coefficient }})
                                </option>
                            @endforeach
                        </select>
                        @error('matiere_id')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Enseignant -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Enseignant <span class="text-red-500">*</span>
                        </label>
                        <select name="enseignant_id" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('enseignant_id') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionner un enseignant</option>
                            @foreach($enseignants as $enseignant)
                                <option value="{{ $enseignant->id }}" {{ old('enseignant_id') == $enseignant->id ? 'selected' : '' }}>
                                    {{ $enseignant->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('enseignant_id')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Salle -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Salle
                        </label>
                        <input type="text" 
                               name="salle" 
                               value="{{ old('salle') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('salle') border-red-500 @enderror"
                               placeholder="Ex: Salle 101, Laboratoire...">
                        @error('salle')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Jour et horaire -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-clock text-indigo-600 mr-2"></i>
                    Jour et horaire
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Jour -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Jour <span class="text-red-500">*</span>
                        </label>
                        <select name="jour" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('jour') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionner un jour</option>
                            @foreach($jours as $jour)
                                <option value="{{ $jour }}" {{ old('jour') == $jour ? 'selected' : '' }}>
                                    {{ $jour }}
                                </option>
                            @endforeach
                        </select>
                        @error('jour')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Heure de début -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Heure de début <span class="text-red-500">*</span>
                        </label>
                        <select name="heure_debut" 
                                id="heure_debut"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('heure_debut') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionner</option>
                            @foreach($heures as $heure)
                                <option value="{{ $heure }}" {{ old('heure_debut') == $heure ? 'selected' : '' }}>
                                    {{ $heure }}
                                </option>
                            @endforeach
                        </select>
                        @error('heure_debut')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Heure de fin -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Heure de fin <span class="text-red-500">*</span>
                        </label>
                        <select name="heure_fin" 
                                id="heure_fin"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('heure_fin') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionner</option>
                            @foreach($heures as $heure)
                                <option value="{{ $heure }}" {{ old('heure_fin') == $heure ? 'selected' : '' }}>
                                    {{ $heure }}
                                </option>
                            @endforeach
                        </select>
                        @error('heure_fin')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Aperçu de la durée -->
                <div class="p-4 bg-indigo-50 rounded-xl">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-indigo-800">Durée du cours :</span>
                        <span class="text-lg font-bold text-indigo-600" id="duree">-</span>
                    </div>
                </div>
            </div>

            <!-- Vérification de conflit (affichage dynamique) -->
            <div id="conflitWarning" class="hidden p-4 bg-red-50 border border-red-200 rounded-xl">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-600 mr-3"></i>
                    <div>
                        <h4 class="text-sm font-semibold text-red-800">Conflit détecté</h4>
                        <p class="text-xs text-red-600" id="conflitMessage">Un cours existe déjà sur ce créneau</p>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                <a href="{{ route('etablissement.emplois_temps.index') }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-save mr-2"></i>
                    Créer l'emploi du temps
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Calcul de la durée
    const heureDebut = document.getElementById('heure_debut');
    const heureFin = document.getElementById('heure_fin');
    const dureeSpan = document.getElementById('duree');

    function calculerDuree() {
        if (heureDebut.value && heureFin.value) {
            const [h1, m1] = heureDebut.value.split(':').map(Number);
            const [h2, m2] = heureFin.value.split(':').map(Number);
            
            let minutes1 = h1 * 60 + m1;
            let minutes2 = h2 * 60 + m2;
            
            if (minutes2 > minutes1) {
                const dureeMinutes = minutes2 - minutes1;
                const heures = Math.floor(dureeMinutes / 60);
                const minutes = dureeMinutes % 60;
                
                let dureeTexte = '';
                if (heures > 0) dureeTexte += heures + 'h';
                if (minutes > 0) dureeTexte += minutes + 'min';
                dureeSpan.textContent = dureeTexte || '0min';
            } else {
                dureeSpan.textContent = 'Horaire invalide';
            }
        } else {
            dureeSpan.textContent = '-';
        }
    }

    heureDebut.addEventListener('change', calculerDuree);
    heureFin.addEventListener('change', calculerDuree);

    // Validation du formulaire
    document.querySelector('form').addEventListener('submit', function(e) {
        const classe = document.querySelector('select[name="classe_id"]').value;
        const matiere = document.querySelector('select[name="matiere_id"]').value;
        const enseignant = document.querySelector('select[name="enseignant_id"]').value;
        const jour = document.querySelector('select[name="jour"]').value;
        const debut = heureDebut.value;
        const fin = heureFin.value;
        
        let errors = [];

        if (!classe) errors.push('Veuillez sélectionner une classe');
        if (!matiere) errors.push('Veuillez sélectionner une matière');
        if (!enseignant) errors.push('Veuillez sélectionner un enseignant');
        if (!jour) errors.push('Veuillez sélectionner un jour');
        if (!debut) errors.push('Veuillez sélectionner une heure de début');
        if (!fin) errors.push('Veuillez sélectionner une heure de fin');

        if (debut && fin && debut >= fin) {
            errors.push('L\'heure de fin doit être après l\'heure de début');
        }

        if (errors.length > 0) {
            e.preventDefault();
            alert('❌ Erreurs :\n- ' + errors.join('\n- '));
        }
    });

    // Simulation de vérification de conflit (optionnel, nécessite une route AJAX)
    async function verifierConflit() {
        const classeId = document.querySelector('select[name="classe_id"]').value;
        const jour = document.querySelector('select[name="jour"]').value;
        const debut = heureDebut.value;
        const fin = heureFin.value;

        if (classeId && jour && debut && fin) {
            try {
                const response = await fetch(`/etablissement/emplois-temps/verifier-conflit?classe_id=${classeId}&jour=${jour}&debut=${debut}&fin=${fin}`);
                const data = await response.json();
                
                const warningDiv = document.getElementById('conflitWarning');
                if (data.conflit) {
                    warningDiv.classList.remove('hidden');
                    document.getElementById('conflitMessage').textContent = data.message;
                } else {
                    warningDiv.classList.add('hidden');
                }
            } catch (error) {
                console.error('Erreur lors de la vérification', error);
            }
        }
    }

    // Déclencher la vérification quand les champs changent
    ['classe_id', 'jour', 'heure_debut', 'heure_fin'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', verifierConflit);
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