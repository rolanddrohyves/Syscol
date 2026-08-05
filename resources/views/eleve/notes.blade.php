{{-- resources/views/eleve/notes.blade.php --}}
@extends('layouts.app')

@section('title', 'Mes notes - Élève')
@section('page-title', 'Mes notes')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Moyenne générale</p>
                    <p class="text-2xl font-bold">{{ $stats['moyenne_generale'] ?? 0 }}/20</p>
                </div>
                <i class="fas fa-chart-line text-2xl text-blue-200"></i>
            </div>
        </div>
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Total notes</p>
                    <p class="text-2xl font-bold">{{ $stats['total_notes'] ?? 0 }}</p>
                </div>
                <i class="fas fa-star text-2xl text-green-200"></i>
            </div>
        </div>
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm">Meilleure note</p>
                    <p class="text-2xl font-bold">{{ $stats['meilleure_note'] ?? 0 }}/20</p>
                </div>
                <i class="fas fa-trophy text-2xl text-orange-200"></i>
            </div>
        </div>
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Matières</p>
                    <p class="text-2xl font-bold">{{ count($moyennesParMatiere) ?? 0 }}</p>
                </div>
                <i class="fas fa-book text-2xl text-purple-200"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Moyennes par matière -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden sticky top-6">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-chart-simple mr-2"></i>Moyennes par matière
                    </h3>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($moyennesParMatiere ?? [] as $matiere)
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-medium text-gray-800">{{ $matiere['matiere'] }}</p>
                                <p class="text-xs text-gray-500">{{ $matiere['notes'] }} note(s)</p>
                            </div>
                            <div class="text-right">
                                <span class="text-xl font-bold {{ $matiere['moyenne'] >= 10 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $matiere['moyenne'] }}/20
                                </span>
                            </div>
                        </div>
                        <!-- Barre de progression -->
                        <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-2 rounded-full transition-all duration-500"
                                 style="width: {{ min(100, ($matiere['moyenne'] / 20) * 100) }}%"></div>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-gray-500">
                        Aucune note enregistrée
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Liste de toutes les notes -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-list mr-2"></i>Toutes mes notes
                    </h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Appréciation</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enseignant</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($notes ?? [] as $note)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-medium text-gray-900">{{ $note->matiere->nom ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $note->note >= 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $note->note }}/20
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-600">
                                        @if($note->note >= 16)
                                            Excellent
                                        @elseif($note->note >= 14)
                                            Très bien
                                        @elseif($note->note >= 12)
                                            Bien
                                        @elseif($note->note >= 10)
                                            Assez bien
                                        @elseif($note->note >= 8)
                                            Passable
                                        @else
                                            Insuffisant
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $note->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $note->enseignant->name ?? 'N/A' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-star text-4xl text-gray-300 mb-3 block"></i>
                                    Aucune note enregistrée pour le moment
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Conseils -->
    @if(($stats['moyenne_generale'] ?? 0) < 10)
    <div class="mt-6 bg-red-50 rounded-xl p-4 border border-red-200">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-500 text-lg"></i>
            </div>
            <div class="ml-3">
                <h4 class="text-sm font-medium text-red-800">Attention</h4>
                <p class="mt-1 text-sm text-red-700">
                    Votre moyenne générale est inférieure à 10/20. Nous vous conseillons de redoubler d'efforts et de consulter vos professeurs.
                </p>
            </div>
        </div>
    </div>
    @elseif(($stats['moyenne_generale'] ?? 0) >= 14)
    <div class="mt-6 bg-green-50 rounded-xl p-4 border border-green-200">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-smile-wink text-green-500 text-lg"></i>
            </div>
            <div class="ml-3">
                <h4 class="text-sm font-medium text-green-800">Félicitations !</h4>
                <p class="mt-1 text-sm text-green-700">
                    Excellente moyenne générale ! Continuez sur cette lancée.
                </p>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 300ms;
    }
</style>
@endsection