{{-- resources/views/enseignant/classes/eleves.blade.php --}}
@extends('layouts.app')

@section('title', 'Élèves de ' . $classe->nom)
@section('page-title', 'Élèves de ' . $classe->nom)

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- En-tête -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $classe->nom }}</h2>
                <p class="text-gray-500">{{ $eleves->count() }} élève(s)</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('enseignant.classes.export', $classe->id) }}" 
                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-download mr-2"></i>Exporter CSV
                </a>
                <a href="{{ route('enseignant.classes.show', $classe->id) }}" 
                   class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Retour
                </a>
            </div>
        </div>
    </div>

    <!-- Liste des élèves -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N°</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matricule</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom & Prénom</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Moyenne</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Appréciation</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($eleves as $index => $eleve)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-sm font-mono text-gray-600">{{ $eleve->matricule ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center mr-2 text-white text-xs font-semibold">
                                    {{ substr($eleve->prenom, 0, 1) }}{{ substr($eleve->nom, 0, 1) }}
                                </div>
                                <span class="font-medium">{{ $eleve->prenom }} {{ $eleve->nom }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $moyennesEleve = $notesParEleve[$eleve->id]['moyennes'] ?? [];
                                $moyenneGenerale = collect($moyennesEleve)->avg('moyenne') ?: 0;
                            @endphp
                            <span class="inline-flex items-center justify-center w-16 px-2 py-1 rounded-full text-sm font-bold {{ $moyenneGenerale >= 10 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ number_format($moyenneGenerale, 2) }}/20
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @php
                                $appreciation = '';
                                if ($moyenneGenerale >= 16) $appreciation = 'Excellent';
                                elseif ($moyenneGenerale >= 14) $appreciation = 'Très bien';
                                elseif ($moyenneGenerale >= 12) $appreciation = 'Bien';
                                elseif ($moyenneGenerale >= 10) $appreciation = 'Passable';
                                elseif ($moyenneGenerale >= 8) $appreciation = 'Insuffisant';
                                else $appreciation = 'Faible';
                            @endphp
                            {{ $appreciation }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('enseignant.notes', ['classe_id' => $classe->id, 'matiere_id' => $matieres->first()->id ?? 0, 'eleve_id' => $eleve->id]) }}" 
                               class="text-indigo-600 hover:text-indigo-800" title="Voir les notes">
                                <i class="fas fa-star"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-users text-4xl mb-2 text-gray-400"></i>
                            <p>Aucun élève dans cette classe</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Détail des moyennes par matière (accordéon) -->
    @if($eleves->count() > 0 && $matieres->count() > 0)
    <div class="mt-6 bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-chart-line text-indigo-500 mr-2"></i>
                Détail des moyennes par matière
            </h3>
        </div>
        <div class="p-4">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Élève</th>
                            @foreach($matieres as $matiere)
                            <th class="px-4 py-2 text-center text-sm font-medium text-gray-600">{{ $matiere->nom }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($eleves as $eleve)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 font-medium">{{ $eleve->prenom }} {{ $eleve->nom }}</td>
                            @foreach($matieres as $matiere)
                            @php
                                $moyenne = $notesParEleve[$eleve->id]['moyennes'][$matiere->id]['moyenne'] ?? 0;
                            @endphp
                            <td class="px-4 py-2 text-center">
                                <span class="{{ $moyenne >= 10 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $moyenne > 0 ? number_format($moyenne, 2) : '-' }}
                                </span>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection