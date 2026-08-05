{{-- resources/views/etablissement/parametres/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Paramètres - SYSCOL')
@section('page-title', 'Paramètres de l\'établissement')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex items-center space-x-3 mb-6">
        <div class="w-12 h-12 bg-gradient-to-br from-gray-500 to-gray-700 rounded-2xl flex items-center justify-center shadow-lg">
            <i class="fas fa-cogs text-white text-xl"></i>
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Paramètres</h2>
            <p class="text-sm text-gray-500">Configuration de l'établissement</p>
        </div>
    </div>

    <!-- Résumé rapide -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Année en cours</p>
                    <p class="text-xl font-bold text-gray-800">{{ $anneeEnCours->libelle ?? 'Non définie' }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Trimestre en cours</p>
                    <p class="text-xl font-bold text-gray-800">{{ $trimestreEnCours->libelle ?? 'Non défini' }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-layer-group text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Statut</p>
                    <p class="text-xl font-bold text-gray-800">
                        <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i> Actif
                        </span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-shield-alt text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Grille des paramètres -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Informations générales -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    Informations générales
                </h3>
            </div>
            
            <div class="p-6">
                <dl class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm text-gray-600">Nom de l'établissement</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $etablissement->nom }}</dd>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm text-gray-600">Type</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $etablissement->type ?? 'Non défini' }}</dd>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm text-gray-600">Adresse</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $etablissement->adresse ?? 'Non renseignée' }}</dd>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm text-gray-600">Téléphone</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $etablissement->telephone ?? 'Non renseigné' }}</dd>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm text-gray-600">Email</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $etablissement->email ?? 'Non renseigné' }}</dd>
                    </div>
                    <div class="flex justify-between py-2">
                        <dt class="text-sm text-gray-600">Ville / Code postal</dt>
                        <dd class="text-sm font-medium text-gray-900">
                            {{ $etablissement->ville ?? 'Non renseignée' }}
                            @if($etablissement->code_postal)
                                ({{ $etablissement->code_postal }})
                            @endif
                        </dd>
                    </div>
                </dl>
                
                <div class="mt-4 text-right">
                    <a href="#edit-general" class="text-sm text-blue-600 hover:text-blue-800" onclick="openModal('general')">
                        <i class="fas fa-edit mr-1"></i> Modifier
                    </a>
                </div>
            </div>
        </div>

        <!-- Logo et identité visuelle -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-image mr-2"></i>
                    Logo et identité
                </h3>
            </div>
            
            <div class="p-6">
                <div class="flex items-center space-x-6">
                    <div class="w-24 h-24 bg-gray-100 rounded-xl flex items-center justify-center overflow-hidden">
                        @if($etablissement->logo)
                            <img src="{{ Storage::url($etablissement->logo) }}" alt="Logo" class="w-full h-full object-contain">
                        @else
                            <i class="fas fa-school text-4xl text-gray-400"></i>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-2">Logo actuel</p>
                        <form action="{{ route('etablissement.parametres.logo') }}" method="POST" enctype="multipart/form-data" id="logoForm">
                            @csrf
                            <label class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 cursor-pointer inline-block">
                                <i class="fas fa-upload mr-2"></i>
                                Changer le logo
                                <input type="file" name="logo" class="hidden" accept="image/*" onchange="document.getElementById('logoForm').submit()">
                            </label>
                        </form>
                        <p class="text-xs text-gray-400 mt-2">PNG, JPG max 2Mo</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Horaires -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-clock mr-2"></i>
                    Horaires
                </h3>
            </div>
            
            <div class="p-6">
                <dl class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm text-gray-600">Heure d'ouverture</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $etablissement->heure_ouverture ?? 'Non définie' }}</dd>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm text-gray-600">Heure de fermeture</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $etablissement->heure_fermeture ?? 'Non définie' }}</dd>
                    </div>
                    <div class="flex justify-between py-2">
                        <dt class="text-sm text-gray-600">Pause méridienne</dt>
                        <dd class="text-sm font-medium text-gray-900">
                            @if($etablissement->pause_debut && $etablissement->pause_fin)
                                {{ $etablissement->pause_debut }} - {{ $etablissement->pause_fin }}
                            @else
                                Non définie
                            @endif
                        </dd>
                    </div>
                </dl>
                
                <div class="mt-4 text-right">
                    <a href="#edit-horaires" class="text-sm text-green-600 hover:text-green-800" onclick="openModal('horaires')">
                        <i class="fas fa-edit mr-1"></i> Modifier
                    </a>
                </div>
            </div>
        </div>

        <!-- Configuration des notes -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-6 py-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-star mr-2"></i>
                    Configuration des notes
                </h3>
            </div>
            
            <div class="p-6">
                @php
                    $configNotes = $etablissement->config_notes ?? [];
                @endphp
                <dl class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm text-gray-600">Note minimale</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $configNotes['note_minimale'] ?? 0 }}/20</dd>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm text-gray-600">Note maximale</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $configNotes['note_maximale'] ?? 20 }}/20</dd>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm text-gray-600">Note éliminatoire</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $configNotes['note_eliminatoire'] ?? 'Non définie' }}</dd>
                    </div>
                    <div class="flex justify-between py-2">
                        <dt class="text-sm text-gray-600">Moyenne requise</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $configNotes['moyenne_requise'] ?? 10 }}/20</dd>
                    </div>
                </dl>
                
                <div class="mt-4 text-right">
                    <a href="#edit-notes" class="text-sm text-yellow-600 hover:text-yellow-800" onclick="openModal('notes')">
                        <i class="fas fa-edit mr-1"></i> Modifier
                    </a>
                </div>
            </div>
        </div>

        <!-- Configuration des absences -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-calendar-times mr-2"></i>
                    Configuration des absences
                </h3>
            </div>
            
            <div class="p-6">
                @php
                    $configAbsences = $etablissement->config_absences ?? [];
                @endphp
                <dl class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm text-gray-600">Seuil d'alerte</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $configAbsences['seuil_alerte_absence'] ?? 5 }} absences</dd>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm text-gray-600">Notification parents</dt>
                        <dd class="text-sm font-medium text-gray-900">
                            @if($configAbsences['notification_parents'] ?? true)
                                <span class="text-green-600">Activée</span>
                            @else
                                <span class="text-gray-400">Désactivée</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between py-2">
                        <dt class="text-sm text-gray-600">Délai de justification</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $configAbsences['justification_delai'] ?? 7 }} jours</dd>
                    </div>
                </dl>
                
                <div class="mt-4 text-right">
                    <a href="#edit-absences" class="text-sm text-red-600 hover:text-red-800" onclick="openModal('absences')">
                        <i class="fas fa-edit mr-1"></i> Modifier
                    </a>
                </div>
            </div>
        </div>

        <!-- Actions de gestion -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-6 py-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-tools mr-2"></i>
                    Actions de gestion
                </h3>
            </div>
            
            <div class="p-6">
                <div class="space-y-3">
                    <a href="{{ route('etablissement.parametres.rapport') }}" 
                       class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <span class="text-sm text-gray-700">
                            <i class="fas fa-file-pdf text-red-500 mr-2"></i>
                            Générer un rapport
                        </span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                    
                    <form action="{{ route('etablissement.parametres.backup') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <span class="text-sm text-gray-700">
                                <i class="fas fa-database text-green-500 mr-2"></i>
                                Sauvegarder la configuration
                            </span>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </button>
                    </form>
                    
                    <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                        <span class="text-sm text-gray-700">
                            <i class="fas fa-upload text-blue-500 mr-2"></i>
                            Restaurer une sauvegarde
                        </span>
                        <input type="file" name="backup_file" class="hidden" accept=".json">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals d'édition (à implémenter selon les besoins) -->
@include('etablissement.parametres.modals.general')
@include('etablissement.parametres.modals.horaires')
@include('etablissement.parametres.modals.notes')
@include('etablissement.parametres.modals.absences')

@push('scripts')
<script>
    function openModal(type) {
        // Logique d'ouverture des modals
        document.getElementById(`modal-${type}`).classList.remove('hidden');
    }

    function closeModal(type) {
        document.getElementById(`modal-${type}`).classList.add('hidden');
    }

    // Fermer les modals en cliquant dehors
    document.querySelectorAll('[id^="modal-"]').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                const type = this.id.replace('modal-', '');
                closeModal(type);
            }
        });
    });

    // Fermer avec Echap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('[id^="modal-"]:not(.hidden)').forEach(modal => {
                const type = modal.id.replace('modal-', '');
                closeModal(type);
            });
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