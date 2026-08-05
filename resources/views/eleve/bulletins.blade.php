{{-- resources/views/eleve/bulletins.blade.php --}}
@extends('layouts.app')

@section('title', 'Mes bulletins - Élève')
@section('page-title', 'Mes bulletins')

@section('content')
<div class="max-w-7xl mx-auto">
    @if(session('error') || isset($errors) && $errors->any())
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
        <p class="font-bold">⚠️ Erreur</p>
        <p>{{ session('error') ?? $errors->first() }}</p>
    </div>
    @endif
    
    @if(isset($error))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
        <p class="font-bold">⚠️ Erreur</p>
        <p>{{ $error }}</p>
    </div>
    @endif
    
    @if(isset($eleve))
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600">
            <h3 class="text-lg font-semibold text-white">
                <i class="fas fa-file-alt mr-2"></i>
                Mes bulletins - {{ $eleve->prenom }} {{ $eleve->nom }}
            </h3>
        </div>
        
        <div class="p-6">
            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="bg-blue-50 rounded-xl p-4 text-center">
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['total_bulletins'] ?? 0 }}</p>
                    <p class="text-sm text-gray-600">Bulletins disponibles</p>
                </div>
                <div class="bg-green-50 rounded-xl p-4 text-center">
                    <p class="text-2xl font-bold text-green-600">{{ number_format($stats['moyenne_generale'] ?? 0, 2) }}/20</p>
                    <p class="text-sm text-gray-600">Moyenne générale</p>
                </div>
            </div>
            
            <!-- Liste des bulletins -->
            @if(isset($bulletins) && $bulletins->count() > 0)
            <div class="space-y-4">
                @foreach($bulletins as $bulletin)
                <div class="border rounded-xl p-4 hover:shadow-lg transition-shadow">
                    <div class="flex justify-between items-center">
                        <div>
                            <h4 class="font-semibold text-gray-800">
                                {{ $bulletin->trimestre->nom ?? 'Trimestre' }} 
                                ({{ $bulletin->anneeScolaire->libelle ?? 'Année scolaire' }})
                            </h4>
                            <p class="text-sm text-gray-500">
                                Publié le {{ $bulletin->created_at->format('d/m/Y') }}
                            </p>
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $bulletin->moyenne_generale >= 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    Moyenne: {{ number_format($bulletin->moyenne_generale, 2) }}/20
                                </span>
                                @if($bulletin->rang)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 ml-2">
                                    Rang: {{ $bulletin->rang }}{{ $bulletin->rang == 1 ? 'er' : 'ème' }}
                                </span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('eleve.bulletin.show', $bulletin->trimestre_id) }}" 
                               class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                <i class="fas fa-eye mr-2"></i>Voir
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <i class="fas fa-file-alt text-5xl text-gray-300 mb-3 block"></i>
                <p class="text-gray-500">Aucun bulletin disponible pour le moment.</p>
                <p class="text-gray-400 text-sm mt-2">Les bulletins apparaîtront ici une fois publiés.</p>
            </div>
            @endif
        </div>
    </div>
    @else
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="p-12 text-center">
            <i class="fas fa-user-graduate text-5xl text-gray-300 mb-3 block"></i>
            <p class="text-gray-500">Profil élève non trouvé.</p>
            <p class="text-gray-400 text-sm mt-2">Veuillez contacter l'administrateur pour lier votre compte à un élève.</p>
        </div>
    </div>
    @endif
</div>
@endsection