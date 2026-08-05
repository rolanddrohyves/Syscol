{{-- resources/views/parent/communications/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Communications - Parent')
@section('page-title', 'Messagerie')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Messages envoyés</p>
                    <p class="text-2xl font-bold">{{ $stats['total_envoyes'] ?? 0 }}</p>
                </div>
                <i class="fas fa-paper-plane text-2xl text-blue-200"></i>
            </div>
        </div>
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Messages reçus</p>
                    <p class="text-2xl font-bold">{{ $stats['total_recus'] ?? 0 }}</p>
                </div>
                <i class="fas fa-inbox text-2xl text-green-200"></i>
            </div>
        </div>
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm">Non lus</p>
                    <p class="text-2xl font-bold">{{ $stats['non_lus'] ?? 0 }}</p>
                </div>
                <i class="fas fa-envelope text-2xl text-orange-200"></i>
            </div>
        </div>
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Taux de réponse</p>
                    <p class="text-2xl font-bold">{{ ($stats['total_envoyes'] ?? 0) > 0 ? round((($stats['total_recus'] ?? 0) / ($stats['total_envoyes'] ?? 1)) * 100) : 0 }}%</p>
                </div>
                <i class="fas fa-chart-line text-2xl text-purple-200"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Formulaire d'envoi -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden sticky top-6">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-pen-alt mr-2"></i>
                            Nouveau message
                        </h3>
                        <a href="{{ route('parent.communications.create') }}" 
                           class="text-white hover:text-indigo-200 transition-colors" 
                           title="Agrandir">
                            <i class="fas fa-expand text-sm"></i>
                        </a>
                    </div>
                </div>
                
                <form action="{{ route('parent.communications.send') }}" method="POST" class="p-6">
                    @csrf
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Destinataire *</label>
                            <select name="destinataire" id="destinataireSelect" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Sélectionner --</option>
                                <option value="administration">📋 Administration</option>
                                <option value="directeur_etudes">🎓 Directeur des études</option>
                                <option value="cpe">👮 CPE</option>
                                <option value="professeur_principal">👨‍🏫 Professeur principal</option>
                                <option value="enseignant">👨‍🏫 Enseignant</option>
                                <option value="autre">📧 Autre destinataire</option>
                            </select>
                        </div>
                        
                        <div id="enseignantContainer" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sélectionner un enseignant</label>
                            <select name="enseignant_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Choisir un enseignant --</option>
                                @foreach($destinataires['enseignants'] ?? [] as $enseignant)
                                    <option value="{{ $enseignant->id }}">{{ $enseignant->name }} - {{ $enseignant->email }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div id="autreContainer" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email du destinataire</label>
                            <input type="email" name="destinataire_nom" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                   placeholder="exemple@email.com">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Enfant concerné</label>
                            <select name="eleve_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Optionnel --</option>
                                @foreach($enfants as $enfant)
                                    <option value="{{ $enfant->id }}">{{ $enfant->prenom }} {{ $enfant->nom }} - {{ $enfant->classe->nom ?? 'N/A' }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sujet *</label>
                            <input type="text" name="sujet" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                   placeholder="Objet du message">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                            <textarea name="message" rows="4" required
                                      class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                      placeholder="Votre message..."></textarea>
                        </div>
                        
                        <button type="submit" 
                                class="w-full px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-200">
                            <i class="fas fa-paper-plane mr-2"></i>Envoyer le message
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste des messages -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <button onclick="showTab('recus')" id="tabRecusBtn" 
                                class="tab-btn active px-6 py-4 text-sm font-medium text-indigo-600 border-b-2 border-indigo-600">
                            <i class="fas fa-inbox mr-2"></i>Reçus
                            @if(($stats['non_lus'] ?? 0) > 0)
                                <span class="ml-2 px-2 py-0.5 text-xs bg-red-500 text-white rounded-full">{{ $stats['non_lus'] }}</span>
                            @endif
                        </button>
                        <button onclick="showTab('envoyes')" id="tabEnvoyesBtn" 
                                class="tab-btn px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent">
                            <i class="fas fa-paper-plane mr-2"></i>Envoyés
                            @if(($stats['total_envoyes'] ?? 0) > 0)
                                <span class="ml-2 px-2 py-0.5 text-xs bg-indigo-500 text-white rounded-full">{{ $stats['total_envoyes'] }}</span>
                            @endif
                        </button>
                    </nav>
                </div>

                <!-- Messages reçus -->
                <div id="recusContent" class="divide-y divide-gray-200">
                    @forelse($messagesRecus ?? [] as $message)
                    <div class="p-4 hover:bg-gray-50 transition-colors {{ !$message->lu ? 'bg-blue-50' : '' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-1">
                                    @if(!$message->lu)
                                        <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                    @endif
                                    <span class="font-semibold text-gray-800">
                                        De: {{ $message->sender->name ?? 'Administration' }}
                                    </span>
                                    <span class="text-xs text-gray-400">
                                        {{ $message->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <h4 class="font-medium text-gray-800 mb-1">{{ $message->sujet }}</h4>
                                <p class="text-sm text-gray-600 line-clamp-2">{{ Str::limit($message->message, 100) }}</p>
                                @if($message->eleve)
                                    <p class="text-xs text-gray-400 mt-1">
                                        <i class="fas fa-user-graduate mr-1"></i>Élève: {{ $message->eleve->prenom }} {{ $message->eleve->nom }}
                                    </p>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2 ml-4">
                                <a href="{{ route('parent.communications.show', $message->id) }}" 
                                   class="px-3 py-1 text-indigo-600 hover:bg-indigo-50 rounded-lg text-sm">
                                    Lire
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-12 text-center">
                        <i class="fas fa-inbox text-5xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">Aucun message reçu</p>
                        <p class="text-gray-400 text-sm mt-2">Vous n'avez pas encore reçu de messages</p>
                    </div>
                    @endforelse
                </div>

                <!-- Messages envoyés -->
                <div id="envoyesContent" class="divide-y divide-gray-200 hidden">
                    @forelse($messagesEnvoyes ?? [] as $message)
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-1">
                                    <span class="font-semibold text-gray-800">
                                        À: {{ $message->receiver->name ?? 'Administration' }}
                                    </span>
                                    <span class="text-xs text-gray-400">
                                        {{ $message->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <h4 class="font-medium text-gray-800 mb-1">{{ $message->sujet }}</h4>
                                <p class="text-sm text-gray-600 line-clamp-2">{{ Str::limit($message->message, 100) }}</p>
                                @if($message->eleve)
                                    <p class="text-xs text-gray-400 mt-1">
                                        <i class="fas fa-user-graduate mr-1"></i>Élève: {{ $message->eleve->prenom }} {{ $message->eleve->nom }}
                                    </p>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2 ml-4">
                                <a href="{{ route('parent.communications.show', $message->id) }}" 
                                   class="px-3 py-1 text-indigo-600 hover:bg-indigo-50 rounded-lg text-sm">
                                    Voir
                                </a>
                                <form action="{{ route('parent.communications.destroy', $message->id) }}" 
                                      method="POST" 
                                      class="inline-block"
                                      onsubmit="return confirm('Supprimer ce message ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 text-red-600 hover:bg-red-50 rounded-lg text-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-12 text-center">
                        <i class="fas fa-paper-plane text-5xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">Aucun message envoyé</p>
                        <p class="text-gray-400 text-sm mt-2">Envoyez votre premier message en utilisant le formulaire</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showTab(tab) {
    const recusContent = document.getElementById('recusContent');
    const envoyesContent = document.getElementById('envoyesContent');
    const tabRecusBtn = document.getElementById('tabRecusBtn');
    const tabEnvoyesBtn = document.getElementById('tabEnvoyesBtn');
    
    if (tab === 'recus') {
        recusContent.classList.remove('hidden');
        envoyesContent.classList.add('hidden');
        tabRecusBtn.classList.add('active', 'text-indigo-600', 'border-indigo-600');
        tabRecusBtn.classList.remove('text-gray-500', 'border-transparent');
        tabEnvoyesBtn.classList.remove('active', 'text-indigo-600', 'border-indigo-600');
        tabEnvoyesBtn.classList.add('text-gray-500', 'border-transparent');
    } else {
        recusContent.classList.add('hidden');
        envoyesContent.classList.remove('hidden');
        tabEnvoyesBtn.classList.add('active', 'text-indigo-600', 'border-indigo-600');
        tabEnvoyesBtn.classList.remove('text-gray-500', 'border-transparent');
        tabRecusBtn.classList.remove('active', 'text-indigo-600', 'border-indigo-600');
        tabRecusBtn.classList.add('text-gray-500', 'border-transparent');
    }
}

// Gestion de l'affichage des destinataires
const destinataireSelect = document.getElementById('destinataireSelect');
if (destinataireSelect) {
    destinataireSelect.addEventListener('change', function() {
        const enseignantContainer = document.getElementById('enseignantContainer');
        const autreContainer = document.getElementById('autreContainer');
        
        if (enseignantContainer) enseignantContainer.classList.add('hidden');
        if (autreContainer) autreContainer.classList.add('hidden');
        
        if (this.value === 'enseignant' && enseignantContainer) {
            enseignantContainer.classList.remove('hidden');
        } else if (this.value === 'autre' && autreContainer) {
            autreContainer.classList.remove('hidden');
        }
    });
}
</script>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .tab-btn.active {
        border-bottom-width: 2px;
    }
</style>
@endsection