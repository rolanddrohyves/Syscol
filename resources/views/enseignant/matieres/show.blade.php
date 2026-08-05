@extends('layouts.app')

@section('title', $matiere->nom . ' - Détails')
@section('page-title', $matiere->nom)

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">{{ $matiere->nom }}</h2>
                    <p class="text-indigo-100">Coefficient: {{ $matiere->coefficient ?? 1 }}</p>
                </div>
                <a href="{{ route('enseignant.matieres') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Retour
                </a>
            </div>
        </div>
        
        <div class="p-6">
            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 rounded-xl p-4 text-center">
                    <p class="text-sm text-blue-600">Total notes</p>
                    <p class="text-2xl font-bold text-blue-700">{{ $stats['total_notes'] }}</p>
                </div>
                <div class="bg-green-50 rounded-xl p-4 text-center">
                    <p class="text-sm text-green-600">Moyenne générale</p>
                    <p class="text-2xl font-bold text-green-700">{{ $stats['moyenne_generale'] }}/20</p>
                </div>
                <div class="bg-orange-50 rounded-xl p-4 text-center">
                    <p class="text-sm text-orange-600">Meilleure note</p>
                    <p class="text-2xl font-bold text-orange-700">{{ $stats['meilleure_note'] }}/20</p>
                </div>
                <div class="bg-red-50 rounded-xl p-4 text-center">
                    <p class="text-sm text-red-600">Plus faible note</p>
                    <p class="text-2xl font-bold text-red-700">{{ $stats['plus_faible_note'] }}/20</p>
                </div>
            </div>
            
            <!-- Classes enseignées -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3"><i class="fas fa-chalkboard-teacher mr-2"></i> Classes enseignées</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($classes as $classe)
                    <span class="px-3 py-2 bg-indigo-100 text-indigo-700 rounded-lg">
                        {{ $classe->nom }} ({{ $classe->eleves->count() }} élèves)
                    </span>
                    @endforeach
                </div>
            </div>
            
            <!-- Dernières notes -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-3"><i class="fas fa-file-alt mr-2"></i> Dernières notes saisies</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left">Date</th>
                                <th class="px-4 py-2 text-left">Élève</th>
                                <th class="px-4 py-2 text-left">Classe</th>
                                <th class="px-4 py-2 text-center">Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dernieresNotes as $note)
                            <tr>
                                <td class="px-4 py-2">{{ $note->created_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-2">{{ $note->eleve->prenom }} {{ $note->eleve->nom }}</td>
                                <td class="px-4 py-2">{{ $note->classe->nom ?? '-' }}</td>
                                <td class="px-4 py-2 text-center">
                                    <span class="px-2 py-1 rounded-full {{ $note->note >= 10 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $note->note }}/20
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection