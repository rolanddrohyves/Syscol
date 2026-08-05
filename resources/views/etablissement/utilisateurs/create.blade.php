{{-- resources/views/etablissement/utilisateurs/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Créer un utilisateur - SYSCOL')
@section('page-title', 'Créer un nouvel utilisateur')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <!-- En-tête -->
        <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-indigo-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-user-plus text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Nouvel utilisateur</h2>
                <p class="text-gray-500">Ajouter un membre au personnel de l'établissement</p>
            </div>
        </div>

        <!-- Formulaire -->
        <form action="{{ route('etablissement.utilisateurs.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Nom complet -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                    Nom complet <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       value="{{ old('name') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('name') border-red-500 @enderror"
                       placeholder="Ex: Jean Kouassi"
                       required>
                @error('name')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                    Adresse email <span class="text-red-500">*</span>
                </label>
                <input type="email" 
                       name="email" 
                       value="{{ old('email') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('email') border-red-500 @enderror"
                       placeholder="exemple@email.com"
                       required>
                @error('email')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Téléphone -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                    Téléphone
                </label>
                <input type="tel" 
                       name="telephone" 
                       value="{{ old('telephone') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('telephone') border-red-500 @enderror"
                       placeholder="Ex: 0708091011">
                @error('telephone')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Rôle -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                    Rôle <span class="text-red-500">*</span>
                </label>
                <select name="role_id" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('role_id') border-red-500 @enderror"
                        required>
                    <option value="">Sélectionner un rôle</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" 
                                {{ old('role_id') == $role->id ? 'selected' : '' }}
                                class="{{ $role->name == 'admin_etablissement' ? 'font-semibold' : '' }}">
                            {{ $role->display_name ?? $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Mot de passe -->
            <div class="space-y-4 p-4 bg-purple-50 rounded-xl">
                <h4 class="text-sm font-semibold text-purple-800 flex items-center">
                    <i class="fas fa-key mr-2"></i>
                    Mot de passe
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Mot de passe <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               name="password" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('password') border-red-500 @enderror"
                               placeholder="••••••••"
                               required>
                        @error('password')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Confirmer <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               name="password_confirmation" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                               placeholder="••••••••"
                               required>
                    </div>
                </div>
                <p class="text-xs text-purple-600">
                    <i class="fas fa-info-circle mr-1"></i>
                    Le mot de passe doit contenir au moins 8 caractères
                </p>
            </div>

            <!-- Aperçu des droits selon le rôle -->
            <div class="space-y-3 p-4 bg-gray-50 rounded-xl">
                <h4 class="text-sm font-semibold text-gray-700 flex items-center">
                    <i class="fas fa-shield-alt mr-2 text-purple-600"></i>
                    Aperçu des permissions
                </h4>
                
                <div id="permissionsPreview" class="text-sm text-gray-600">
                    <p class="italic">Sélectionnez un rôle pour voir les permissions</p>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center justify-end space-x-4 pt-4 border-t">
                <a href="{{ route('etablissement.utilisateurs.index') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-xl hover:bg-gray-50 transition-all">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl hover:from-purple-700 hover:to-indigo-700 transition-all shadow-lg">
                    <i class="fas fa-save mr-2"></i>
                    Créer l'utilisateur
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Données des permissions par rôle
    const permissions = {
        'admin_etablissement': [
            'Gestion complète de l\'établissement',
            'Gestion des utilisateurs',
            'Gestion des classes',
            'Gestion des enseignants',
            'Gestion des élèves',
            'Gestion des notes',
            'Gestion des absences',
            'Gestion des emplois du temps'
        ],
        'directeur_etudes': [
            'Gestion des emplois du temps',
            'Gestion des examens',
            'Gestion des bulletins',
            'Gestion des programmes',
            'Consultation des notes',
            'Gestion des classes'
        ],
        'cpe': [
            'Gestion des absences',
            'Gestion des retards',
            'Gestion des disciplines',
            'Gestion des sanctions',
            'Gestion des présences',
            'Consultation des élèves'
        ],
        'comptable': [
            'Gestion des frais de scolarité',
            'Gestion des paiements',
            'Gestion des factures',
            'Gestion des dépenses',
            'Rapports financiers',
            'Gestion des bourses'
        ],
        'enseignant': [
            'Gestion des notes',
            'Gestion des présences',
            'Consultation de l\'emploi du temps',
            'Gestion des évaluations',
            'Rapports de classe'
        ]
    };

    // Mise à jour de l'aperçu des permissions
    document.querySelector('select[name="role_id"]').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const roleName = selectedOption.text.toLowerCase().includes('admin') ? 'admin_etablissement' :
                        selectedOption.text.toLowerCase().includes('directeur') ? 'directeur_etudes' :
                        selectedOption.text.toLowerCase().includes('cpe') ? 'cpe' :
                        selectedOption.text.toLowerCase().includes('comptable') ? 'comptable' :
                        selectedOption.text.toLowerCase().includes('enseignant') ? 'enseignant' : null;
        
        const previewDiv = document.getElementById('permissionsPreview');
        
        if (roleName && permissions[roleName]) {
            let html = '<ul class="space-y-1">';
            permissions[roleName].forEach(perm => {
                html += `<li class="flex items-center text-sm text-gray-700">
                    <i class="fas fa-check-circle text-green-500 mr-2 text-xs"></i>
                    ${perm}
                </li>`;
            });
            html += '</ul>';
            previewDiv.innerHTML = html;
        } else {
            previewDiv.innerHTML = '<p class="italic text-gray-500">Sélectionnez un rôle pour voir les permissions</p>';
        }
    });

    // Validation du téléphone (uniquement chiffres)
    document.querySelector('input[name="telephone"]')?.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Validation du formulaire
    document.querySelector('form').addEventListener('submit', function(e) {
        const name = document.querySelector('input[name="name"]').value;
        const email = document.querySelector('input[name="email"]').value;
        const role = document.querySelector('select[name="role_id"]').value;
        const password = document.querySelector('input[name="password"]').value;
        const passwordConfirm = document.querySelector('input[name="password_confirmation"]').value;
        
        let errors = [];

        if (!name) errors.push('Le nom est requis');
        if (!email) errors.push('L\'email est requis');
        else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            errors.push('L\'email n\'est pas valide');
        }
        if (!role) errors.push('Le rôle est requis');
        if (!password) errors.push('Le mot de passe est requis');
        else if (password.length < 8) {
            errors.push('Le mot de passe doit contenir au moins 8 caractères');
        }
        if (password !== passwordConfirm) {
            errors.push('Les mots de passe ne correspondent pas');
        }

        if (errors.length > 0) {
            e.preventDefault();
            alert('Erreurs :\n- ' + errors.join('\n- '));
        }
    });

    // Avertissement pour le rôle admin
    document.querySelector('select[name="role_id"]').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.text.includes('admin') || selectedOption.text.includes('Admin')) {
            if (!confirm('Attention : Le rôle administrateur donne tous les droits sur l\'établissement. Confirmer ?')) {
                this.value = '';
                document.getElementById('permissionsPreview').innerHTML = '<p class="italic text-gray-500">Sélectionnez un rôle pour voir les permissions</p>';
            }
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