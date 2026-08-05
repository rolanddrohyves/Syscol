{{-- resources/views/parent/notes/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Notes de mes enfants')
@section('page-title', 'Notes de mes enfants')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Filtre par enfant -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('parent.notes.index') }}" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Sélectionner un enfant</label>
                <select name="enfant_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="this.form.submit()">
                    <option value="">Tous les enfants</option>
                    @foreach($enfants as $e)
                        <option value="{{ $e->id }}" {{ request('enfant_id') == $e->id ? 'selected' : '' }}>
                            {{ $e->prenom }} {{ $e->nom }} - {{ $e->classe->nom ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-search mr-2"></i>Filtrer
                </button>
            </div>
        </form>
    </div>

    @if(empty($notesParEnfant))
    <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
        <i class="fas fa-star text-5xl text-gray-300 mb-3"></i>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">Aucune note disponible</h3>
        <p class="text-gray-500">Aucune note n'a été enregistrée pour vos enfants.</p>
    </div>
    @else
        @foreach($notesParEnfant as $data)
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
            <!-- En-tête -->
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-white">{{ $data['enfant']->prenom }} {{ $data['enfant']->nom }}</h3>
                        <p class="text-indigo-100 text-sm">Classe: {{ $data['enfant']->classe->nom ?? 'N/A' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-white text-sm">Moyenne générale</p>
                        <p class="text-2xl font-bold text-white">{{ $data['moyenne_generale'] }}/20</p>
                    </div>
                </div>
            </div>

            <!-- Tableau des notes -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matière</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Note</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Moyenne classe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Appréciation</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($data['notes'] as $note)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium">{{ $note->matiere->nom ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-16 px-2 py-1 rounded-full text-sm font-bold {{ $note->note >= 10 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $note->note }}/20
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-600">
                                {{ $data['moyennes_classe'][$note->matiere_id] ?? '-' }}/20
                            </td>
                            <td class="px-6 py-4 text-sm">{{ $note->appreciation ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm">{{ $note->date_evaluation ? \Carbon\Carbon::parse($note->date_evaluation)->format('d/m/Y') : '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                Aucune note enregistrée
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    @endif
</div>
@endsection