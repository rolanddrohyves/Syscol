{{-- resources/views/parent/absences/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Absences de mes enfants')
@section('page-title', 'Absences et retards')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Filtre par enfant -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('parent.absences.index') }}" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Filtrer par enfant</label>
                <select name="enfant_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="this.form.submit()">
                    <option value="">Tous les enfants</option>
                    @foreach($enfants as $e)
                        <option value="{{ $e->id }}" {{ request('enfant_id') == $e->id ? 'selected' : '' }}>
                            {{ $e->prenom }} {{ $e->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl p-4 text-white">
            <p class="text-red-100 text-sm">Total absences</p>
            <p class="text-3xl font-bold">{{ $statistiques['total_absences'] }}</p>
        </div>
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-4 text-white">
            <p class="text-orange-100 text-sm">Total retards</p>
            <p class="text-3xl font-bold">{{ $statistiques['total_retards'] }}</p>
        </div>
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 text-white">
            <p class="text-green-100 text-sm">Taux de présence</p>
            <p class="text-3xl font-bold">{{ $statistiques['taux_presence'] }}%</p>
        </div>
    </div>

    <!-- Liste des absences -->
    @foreach($absencesParEnfant as $data)
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                {{ $data['enfant']->prenom }} {{ $data['enfant']->nom }}
                <span class="text-sm font-normal text-gray-500 ml-2">{{ $data['total_absences'] }} absence(s)</span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motif</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($data['absences'] as $absence)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm">{{ $absence->date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 text-xs rounded-full {{ $absence->est_retard ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-700' }}">
                                {{ $absence->est_retard ? 'Retard' : 'Absence' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $absence->motif ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 text-xs rounded-full {{ $absence->justifiee ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $absence->justifiee ? 'Justifiée' : 'Non justifiée' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if(!$absence->justifiee)
                            <button type="button" onclick="ouvrirJustification({{ $absence->id }})" 
                                    class="text-indigo-600 hover:text-indigo-800 text-sm">
                                <i class="fas fa-pen mr-1"></i> Justifier
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            Aucune absence enregistrée
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
</div>

<!-- Modal justification -->
<div id="justificationModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Justifier l'absence</h3>
        <form id="justificationForm" method="POST">
            @csrf
            <textarea name="motif" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                      placeholder="Motif de l'absence..."></textarea>
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="fermerJustification()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Annuler
                </button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Envoyer la justification
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function ouvrirJustification(id) {
    const modal = document.getElementById('justificationModal');
    const form = document.getElementById('justificationForm');
    form.action = '/parent/absences/' + id + '/justify';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function fermerJustification() {
    const modal = document.getElementById('justificationModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endsection