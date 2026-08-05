{{-- resources/views/comptable/impayes/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Détails des impayés - SYSCOL')
@section('page-title', 'Détails des impayés')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <!-- En-tête avec informations élève -->
        <div class="bg-gradient-to-r from-red-600 to-orange-600 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-user-graduate text-white text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">{{ $eleve->prenom }} {{ $eleve->nom }}</h1>
                        <p class="text-red-100">Matricule: {{ $eleve->matricule }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-red-100">Classe</p>
                    <p class="text-xl font-bold text-white">{{ $eleve->classe->nom ?? 'Non définie' }}</p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="px-8 py-4 bg-gray-50 border-b border-gray-200 flex justify-end space-x-3">
            <a href="{{ route('comptable.paiements.create', ['eleve_id' => $eleve->id]) }}" 
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-money-bill-wave mr-2"></i>Enregistrer un paiement
            </a>
            <button onclick="envoyerRelance({{ $eleve->id }}, '{{ $eleve->email_parent }}')" 
                    class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                <i class="fas fa-bell mr-2"></i>Envoyer une relance
            </button>
            <a href="{{ route('comptable.impayes.index') }}" 
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>

        <!-- Corps -->
        <div class="p-8">
            <!-- Récapitulatif financier -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-chart-line text-red-600 mr-2"></i>
                    Situation financière
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 rounded-xl p-4 text-center">
                        <p class="text-sm text-blue-600">Total des frais</p>
                        <p class="text-2xl font-bold text-blue-700">{{ number_format($totalFrais, 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div class="bg-green-50 rounded-xl p-4 text-center">
                        <p class="text-sm text-green-600">Déjà payé</p>
                        <p class="text-2xl font-bold text-green-700">{{ number_format($totalPaye, 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div class="bg-orange-50 rounded-xl p-4 text-center">
                        <p class="text-sm text-orange-600">Reste à payer</p>
                        <p class="text-2xl font-bold text-orange-700">{{ number_format($reste, 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div class="bg-purple-50 rounded-xl p-4 text-center">
                        <p class="text-sm text-purple-600">Taux de paiement</p>
                        <p class="text-2xl font-bold text-purple-700">{{ $totalFrais > 0 ? round(($totalPaye / $totalFrais) * 100, 2) : 0 }}%</p>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                            <div class="bg-purple-600 h-1.5 rounded-full" style="width: {{ $totalFrais > 0 ? round(($totalPaye / $totalFrais) * 100, 2) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Détail des frais -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-tags text-red-600 mr-2"></i>
                    Détail des frais
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Type de frais</th>
                                <th class="px-4 py-3 text-right text-sm font-medium text-gray-500">Montant total</th>
                                <th class="px-4 py-3 text-right text-sm font-medium text-gray-500">Déjà payé</th>
                                <th class="px-4 py-3 text-right text-sm font-medium text-gray-500">Reste à payer</th>
                                <th class="px-4 py-3 text-center text-sm font-medium text-gray-500">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-pen-alt text-blue-500 mr-2"></i>
                                        <span class="font-medium">Frais d'inscription</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right">{{ number_format($montantInscription, 0, ',', ' ') }} FCFA</td>
                                <td class="px-4 py-3 text-right text-green-600">{{ number_format($payeInscription ?? 0, 0, ',', ' ') }} FCFA</td>
                                <td class="px-4 py-3 text-right font-bold {{ ($resteInscription ?? 0) > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ number_format($resteInscription ?? 0, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if(($resteInscription ?? 0) <= 0)
                                        <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">✓ Payé</span>
                                    @elseif(($payeInscription ?? 0) > 0)
                                        <span class="px-2 py-1 text-xs bg-orange-100 text-orange-700 rounded-full">⚠ Partiel</span>
                                    @else
                                        <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded-full">✗ Impayé</span>
                                    @endif
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-book text-indigo-500 mr-2"></i>
                                        <span class="font-medium">Frais de scolarité</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right">{{ number_format($montantScolarite, 0, ',', ' ') }} FCFA</td>
                                <td class="px-4 py-3 text-right text-green-600">{{ number_format($payeScolarite ?? 0, 0, ',', ' ') }} FCFA</td>
                                <td class="px-4 py-3 text-right font-bold {{ ($resteScolarite ?? 0) > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ number_format($resteScolarite ?? 0, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if(($resteScolarite ?? 0) <= 0)
                                        <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">✓ Payé</span>
                                    @elseif(($payeScolarite ?? 0) > 0)
                                        <span class="px-2 py-1 text-xs bg-orange-100 text-orange-700 rounded-full">⚠ Partiel</span>
                                    @else
                                        <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded-full">✗ Impayé</span>
                                    @endif
                                </td>
                            </tr>
                            <tr class="bg-gray-50 font-semibold">
                                <td class="px-4 py-3">TOTAL</td>
                                <td class="px-4 py-3 text-right">{{ number_format($totalFrais, 0, ',', ' ') }} FCFA</td>
                                <td class="px-4 py-3 text-right text-green-700">{{ number_format($totalPaye, 0, ',', ' ') }} FCFA</td>
                                <td class="px-4 py-3 text-right text-red-700">{{ number_format($reste, 0, ',', ' ') }} FCFA</td>
                                <td class="px-4 py-3"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Historique des paiements -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-history text-red-600 mr-2"></i>
                    Historique des paiements
                </h3>
                
                @if($eleve->paiements->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Date</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Référence</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Frais</th>
                                    <th class="px-4 py-3 text-right text-sm font-medium text-gray-500">Montant</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Mode</th>
                                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-500">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($eleve->paiements->sortByDesc('date_paiement') as $paiement)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm">{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 text-sm font-mono">{{ $paiement->reference }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $paiement->frais->libelle ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-right font-medium">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                                    <td class="px-4 py-3 text-sm">
                                        @switch($paiement->mode_paiement)
                                            @case('especes') Espèces @break
                                            @case('cheque') Chèque @break
                                            @case('virement') Virement @break
                                            @case('carte') Carte bancaire @break
                                            @case('mobile_money') Mobile Money @break
                                            @default {{ $paiement->mode_paiement }}
                                        @endswitch
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($paiement->statut == 'paye')
                                            <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">Payé</span>
                                        @elseif($paiement->statut == 'partiel')
                                            <span class="px-2 py-1 text-xs bg-orange-100 text-orange-700 rounded-full">Partiel</span>
                                        @else
                                            <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded-full">En attente</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-100">
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-right font-semibold">TOTAL PAYÉ</td>
                                    <td class="px-4 py-3 text-right font-bold text-green-700">{{ number_format($eleve->paiements->sum('montant'), 0, ',', ' ') }} FCFA</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="bg-gray-50 rounded-xl p-6 text-center">
                        <i class="fas fa-receipt text-4xl text-gray-400 mb-2"></i>
                        <p class="text-gray-500">Aucun paiement enregistré pour cet élève</p>
                    </div>
                @endif
            </div>

            <!-- Informations de contact -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-address-card text-red-600 mr-2"></i>
                    Informations de contact
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-sm text-gray-500">Parent / Tuteur</p>
                        <p class="font-medium">{{ $eleve->nom_parent }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-sm text-gray-500">Téléphone</p>
                        <p class="font-medium">{{ $eleve->telephone_parent }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="font-medium">{{ $eleve->email_parent ?? 'Non renseigné' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-sm text-gray-500">Adresse</p>
                        <p class="font-medium">{{ $eleve->adresse ?? 'Non renseignée' }}</p>
                    </div>
                </div>
            </div>

            <!-- Alertes -->
            @if($reste > 0)
                <div class="p-4 bg-orange-50 border-l-4 border-orange-500 rounded-r-xl">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-orange-500 text-xl mr-3"></i>
                        <div>
                            <p class="font-medium text-orange-800">Attention : Un montant de {{ number_format($reste, 0, ',', ' ') }} FCFA reste à payer.</p>
                            <p class="text-sm text-orange-700">Veuillez contacter les parents pour régulariser la situation.</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="p-4 bg-green-50 border-l-4 border-green-500 rounded-r-xl">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                        <div>
                            <p class="font-medium text-green-800">Tous les frais sont payés !</p>
                            <p class="text-sm text-green-700">Cet élève n'a aucun impayé.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
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
                        <option value="email"><i class="fas fa-envelope mr-2"></i> Email</option>
                        <option value="sms"><i class="fas fa-sms mr-2"></i> SMS</option>
                        <option value="courrier"><i class="fas fa-mail-bulk mr-2"></i> Courrier</option>
                        <option value="appel"><i class="fas fa-phone-alt mr-2"></i> Appel téléphonique</option>
                    </select>
                </div>
                
                <div id="contactField">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact</label>
                    <input type="text" name="contact" id="contact" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Email ou numéro de téléphone" value="{{ $eleve->email_parent ?? '' }}">
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
    function envoyerRelance(eleveId, email) {
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