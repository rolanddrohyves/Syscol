{{-- resources/views/eleve/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Tableau de bord - Élève')
@section('page-title', 'Mon tableau de bord')

@section('content')
<div class="max-w-7xl mx-auto">
    @if(isset($error))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ $error }}
        </div>
    @else
        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm">Moyenne générale</p>
                        <p class="text-2xl font-bold">{{ $stats['moyenne_generale'] ?? 0 }}/20</p>
                    </div>
                    <i class="fas fa-star text-2xl text-blue-200"></i>
                </div>
            </div>
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm">Notes enregistrées</p>
                        <p class="text-2xl font-bold">{{ $stats['total_notes'] ?? 0 }}</p>
                    </div>
                    <i class="fas fa-chart-line text-2xl text-green-200"></i>
                </div>
            </div>
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm">Absences non justifiées</p>
                        <p class="text-2xl font-bold">{{ $stats['absences_non_justifiees'] ?? 0 }}</p>
                    </div>
                    <i class="fas fa-calendar-times text-2xl text-orange-200"></i>
                </div>
            </div>
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm">Cours aujourd'hui</p>
                        <p class="text-2xl font-bold">{{ $stats['cours_aujourdhui'] ?? 0 }}</p>
                    </div>
                    <i class="fas fa-calendar-day text-2xl text-purple-200"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Dernières notes -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-star mr-2"></i>Dernières notes
                    </h3>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($notes ?? [] as $note)
                    <div class="p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-medium text-gray-800">{{ $note->matiere->nom ?? 'Matière' }}</p>
                                <p class="text-sm text-gray-500">{{ $note->created_at->format('d/m/Y') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-bold {{ $note->note >= 10 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $note->note }}/20
                                </span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-gray-500">
                        Aucune note enregistrée
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Emploi du temps du jour -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-calendar-alt mr-2"></i>Cours du {{ now()->locale('fr')->dayName }}
                    </h3>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($emploiDuTemps ?? [] as $cours)
                    <div class="p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-medium text-gray-800">{{ $cours->matiere->nom ?? 'Cours' }}</p>
                                <p class="text-sm text-gray-500">{{ $cours->enseignant->name ?? 'Professeur' }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($cours->heure_debut)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($cours->heure_fin)->format('H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-gray-500">
                        Pas de cours aujourd'hui
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>
@endsection