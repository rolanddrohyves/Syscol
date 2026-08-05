{{-- resources/views/comptable/impayes/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des impayés - SYSCOL')
@section('page-title', 'Gestion des impayés')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-exclamation-triangle text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Élèves avec impayés</h2>
                <p class="text-sm text-gray-500">Liste des élèves ayant un montant restant à payer</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('comptable.impayes.export') }}?{{ http_build_query(request()->all()) }}" 
               class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all">
                <i class="fas fa-download mr-2"></i> Export CSV
            </a>
            <a href="{{ route('comptable.impayes.statistiques') }}" 
               class="flex items-center px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-all">
                <i class="fas fa-chart-pie mr-2"></i> Statistiques
            </a>
        </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total impayés</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($statistiques['total_impaye'] ?? 0, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
            </div>
            <small class="text-gray-500">{{ $statistiques['nombre_impaye'] ?? 0 }} élèves concernés</small>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Déjà payé</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($statistiques['total_paye'] ?? 0, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
            <small class="text-gray-500">Total des paiements effectués</small>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total des frais</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($statistiques['total_frais'] ?? 0, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-coins text-yellow-600 text-xl"></i>
                </div>
            </div>
            <small class="text-gray-500">Montant total attendu</small>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Taux de recouvrement</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $statistiques['taux_recouvrement'] ?? 0 }}%</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $statistiques['taux_recouvrement'] ?? 0 }}%"></div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <form method="GET" action="{{ route('comptable.impayes.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Classe</label>
                <select name="classe_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">Toutes les classes</option>
                    @foreach($classes as $classe)
                        <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                            {{ $classe->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500"
                       placeholder="Nom, prénom ou matricule">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Trier par</label>
                <select name="order_by" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="reste_desc" {{ request('order_by') == 'reste_desc' ? 'selected' : '' }}>Reste à payer (décroissant)</option>
                    <option value="reste_asc" {{ request('order_by') == 'reste_asc' ? 'selected' : '' }}>Reste à payer (croissant)</option>
                    <option value="nom" {{ request('order_by') == 'nom' ? 'selected' : '' }}>Nom (A-Z)</option>
                </select>
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700">
                    <i class="fas fa-filter mr-2"></i>Filtrer
                </button>
                <a href="{{ route('comptable.impayes.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200">
                    <i class="fas fa-times"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- Liste des élèves avec impayés -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Élève</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Classe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matricule</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Frais Inscription</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Frais Scolarité</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Frais</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Payé</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Reste à payer</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Progression</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($elevesAvecImpayes as $eleve)
                        @php
                            $inscription = $eleve->montant_inscription ?? 0;
                            $scolarite = $eleve->montant_scolarite ?? 0;
                            $totalFrais = $eleve->montant_total_frais;
                            $paye = $eleve->montant_paye;
                            $reste = $eleve->montant_restant;
                            $pourcentage = $eleve->pourcentage_paye;
                            $estUrgent = $reste > 50000;
                        @endphp
                        <tr class="hover:bg-gray-50 {{ $estUrgent ? 'bg-red-50' : '' }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center mr-3 text-white text-sm font-semibold">
                                        {{ substr($eleve->prenom, 0, 1) }}{{ substr($eleve->nom, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $eleve->prenom }} {{ $eleve->nom }}</p>
                                        <p class="text-xs text-gray-500">{{ $eleve->email_parent ?? 'Pas d\'email' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $eleve->classe->nom ?? 'Non définie' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $eleve->matricule }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium text-blue-600">
                                {{ number_format($inscription, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium text-indigo-600">
                                {{ number_format($scolarite, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                {{ number_format($totalFrais, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4 text-right text-sm text-green-600">
                                {{ number_format($paye, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-red-600">
                                {{ number_format($reste, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $pourcentage }}%"></div>
                                    </div>
                                    <span class="ml-2 text-xs text-gray-600 min-w-[45px]">{{ $pourcentage }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('comptable.impayes.show', $eleve->id) }}" 
                                       class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors"
                                       title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('comptable.paiements.create', ['eleve_id' => $eleve->id]) }}" 
                                       class="p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors"
                                       title="Enregistrer un paiement">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </a>
                                    <button onclick="envoyerRelance({{ $eleve->id }}, '{{ $eleve->email_parent }}')" 
                                            class="p-2 bg-orange-50 text-orange-600 rounded-lg hover:bg-orange-100 transition-colors"
                                            title="Envoyer une relance">
                                        <i class="fas fa-bell"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-check-circle text-4xl text-green-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun impayé</h3>
                                    <p class="text-gray-500">Tous les élèves ont leurs frais à jour</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($elevesAvecImpayes->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $elevesAvecImpayes->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal d'envoi de relance -->
<div id="relanceModal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800">Envoyer une relance</h3>
            <button onclick="closeRelanceModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="relanceForm" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type de relance</label>
                    <select name="type" id="relanceType" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500" required>
                        <option value="email">📧 Email</option>
                        <option value="sms">📱 SMS</option>
                        <option value="courrier">📮 Courrier</option>
                        <option value="appel">📞 Appel téléphonique</option>
                    </select>
                </div>
                
                <div id="contactField">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact</label>
                    <input type="text" name="contact" id="contact" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Email ou numéro de téléphone">
                </div>
                
                <div id="messageField">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                    <textarea name="message" id="message" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Saisissez votre message de relance..."></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes (optionnel)</label>
                    <textarea name="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Informations complémentaires..."></textarea>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeRelanceModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Annuler
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <i class="fas fa-paper-plane mr-2"></i>Envoyer la relance
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let currentEleveId = null;

    function envoyerRelance(eleveId, email) {
        currentEleveId = eleveId;
        const modal = document.getElementById('relanceModal');
        const form = document.getElementById('relanceForm');
        const contactInput = document.getElementById('contact');
        
        modal.classList.remove('hidden');
        form.action = `/comptable/impayes/relance/${eleveId}`;
        
        if (email) {
            contactInput.value = email;
        }
    }

    function closeRelanceModal() {
        document.getElementById('relanceModal').classList.add('hidden');
        currentEleveId = null;
        document.getElementById('relanceForm').reset();
    }

    // Gestion de l'affichage des champs selon le type de relance
    const relanceType = document.getElementById('relanceType');
    if (relanceType) {
        relanceType.addEventListener('change', function() {
            const type = this.value;
            const contactField = document.getElementById('contactField');
            const messageField = document.getElementById('messageField');
            const contactInput = document.getElementById('contact');
            const messageTextarea = document.getElementById('message');
            
            if (type === 'email' || type === 'sms') {
                contactField.style.display = 'block';
                messageField.style.display = 'block';
                contactInput.required = true;
                messageTextarea.required = true;
            } else {
                contactField.style.display = 'none';
                messageField.style.display = 'none';
                contactInput.required = false;
                messageTextarea.required = false;
            }
        });
    }

    // Fermer le modal en cliquant dehors
    const modal = document.getElementById('relanceModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeRelanceModal();
            }
        });
    }

    // Fermer avec la touche Echap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            closeRelanceModal();
        }
    });

    // Initialisation
    document.addEventListener('DOMContentLoaded', function() {
        if (relanceType) {
            relanceType.dispatchEvent(new Event('change'));
        }
    });
</script>
@endpush

@push('styles')
<style>
    .transition-colors {
        transition-property: background-color, border-color, color, fill, stroke;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }
</style>
@endpush
@endsection