{{-- resources/views/parent/bulletins/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Bulletins de mes enfants')
@section('page-title', 'Bulletins scolaires')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Filtre par enfant -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('parent.bulletins.index') }}" class="flex flex-col sm:flex-row gap-4">
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

    @if(empty($bulletins))
    <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
        <i class="fas fa-file-alt text-5xl text-gray-300 mb-3"></i>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">Aucun bulletin disponible</h3>
        <p class="text-gray-500">Aucun bulletin n'a été généré pour vos enfants.</p>
    </div>
    @else
        @foreach($bulletins as $data)
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

            <!-- Liste des bulletins par trimestre -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($data['bulletins'] as $bulletin)
                    <div class="border rounded-xl p-4 hover:shadow-md transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-2">
                                @if($bulletin['trimestre']->numero == 1)
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <i class="fas fa-chart-line text-blue-600"></i>
                                    </div>
                                @elseif($bulletin['trimestre']->numero == 2)
                                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                        <i class="fas fa-chart-line text-green-600"></i>
                                    </div>
                                @else
                                    <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                                        <i class="fas fa-chart-line text-purple-600"></i>
                                    </div>
                                @endif
                                <div>
                                    <h4 class="font-semibold text-gray-800">{{ $bulletin['trimestre']->libelle }}</h4>
                                    <p class="text-xs text-gray-500">{{ $bulletin['trimestre']->date_debut ? \Carbon\Carbon::parse($bulletin['trimestre']->date_debut)->format('d/m/Y') : '' }} - {{ $bulletin['trimestre']->date_fin ? \Carbon\Carbon::parse($bulletin['trimestre']->date_fin)->format('d/m/Y') : '' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold {{ $bulletin['moyenne'] >= 10 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $bulletin['moyenne'] }}/20
                                </p>
                                <p class="text-xs text-gray-500">Moyenne</p>
                            </div>
                        </div>

                        <!-- Barre de progression -->
                        <div class="mb-3">
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-gray-500">Performance</span>
                                <span class="font-medium {{ $bulletin['moyenne'] >= 10 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $bulletin['moyenne'] }}/20
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ ($bulletin['moyenne'] / 20) * 100 }}%"></div>
                            </div>
                        </div>

                        <!-- Rang -->
                        <div class="flex justify-between text-sm mb-3">
                            <span class="text-gray-500">Rang</span>
                            <span class="font-medium">{{ $bulletin['rang'] ?? '-' }}/{{ $bulletin['total_eleves'] ?? '-' }}</span>
                        </div>

                        <!-- Appréciation -->
                        @if(isset($bulletin['appreciation']) && $bulletin['appreciation'])
                        <div class="bg-gray-50 rounded-lg p-2 mb-3">
                            <p class="text-xs text-gray-600 italic">"{{ $bulletin['appreciation'] }}"</p>
                        </div>
                        @endif

                        <!-- Bouton télécharger -->
                        <a href="{{ route('parent.bulletins.pdf', $bulletin['id']) }}" 
                           class="block text-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors mt-2">
                            <i class="fas fa-download mr-2"></i>Télécharger PDF
                        </a>
                    </div>
                    @endforeach
                </div>

                <!-- Détail des notes par matière (accordéon) -->
                <div class="mt-6">
                    <button type="button" onclick="toggleDetails({{ $data['enfant']->id }})" 
                            class="w-full flex items-center justify-between px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        <span class="font-medium text-gray-700">
                            <i class="fas fa-chart-simple mr-2"></i>Détail des notes par matière
                        </span>
                        <i id="icon-{{ $data['enfant']->id }}" class="fas fa-chevron-down transition-transform"></i>
                    </button>
                    
                    <div id="details-{{ $data['enfant']->id }}" class="hidden mt-4">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Matière</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Trimestre 1</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Trimestre 2</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Trimestre 3</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Moyenne</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($data['matieres'] as $matiere)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 font-medium">{{ $matiere['nom'] }}</td>
                                        <td class="px-4 py-2 text-center">
                                            <span class="inline-flex items-center justify-center w-12 px-2 py-1 rounded-full text-sm font-bold {{ $matiere['t1'] >= 10 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ $matiere['t1'] }}/20
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <span class="inline-flex items-center justify-center w-12 px-2 py-1 rounded-full text-sm font-bold {{ $matiere['t2'] >= 10 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ $matiere['t2'] }}/20
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <span class="inline-flex items-center justify-center w-12 px-2 py-1 rounded-full text-sm font-bold {{ $matiere['t3'] >= 10 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ $matiere['t3'] }}/20
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-center font-bold {{ $matiere['moyenne'] >= 10 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $matiere['moyenne'] }}/20
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
        @endforeach
    @endif
</div>

<script>
function toggleDetails(id) {
    const details = document.getElementById('details-' + id);
    const icon = document.getElementById('icon-' + id);
    
    if (details.classList.contains('hidden')) {
        details.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        details.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
}
</script>

<style>
    .rotate-180 {
        transform: rotate(180deg);
    }
    .transition-transform {
        transition: transform 0.3s ease;
    }
</style>
@endsection