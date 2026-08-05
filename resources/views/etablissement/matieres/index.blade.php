@extends('layouts.app')

@section('title', 'Gestion des matières - SYSCOL')
@section('page-title', 'Gestion des matières')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Matières</h2>
        <p class="text-sm text-gray-500">Gestion du programme scolaire</p>
    </div>
    <a href="{{ route('etablissement.matieres.create') }}" 
       class="px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg hover:from-blue-700 hover:to-cyan-700">
        <i class="fas fa-plus mr-2"></i>
        Nouvelle matière
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Coefficient</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($matieres as $matiere)
            <tr>
                <td class="px-6 py-4">{{ $matiere->nom }}</td>
                <td class="px-6 py-4 font-mono">{{ $matiere->code }}</td>
                <td class="px-6 py-4">{{ $matiere->coefficient }}</td>
                <td class="px-6 py-4">
                    <a href="{{ route('etablissement.matieres.edit', $matiere->id) }}" class="text-blue-600 hover:text-blue-800 mr-3">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button onclick="deleteMatiere({{ $matiere->id }})" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                    Aucune matière trouvée
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($matieres->hasPages())
    <div class="px-6 py-4 border-t">
        {{ $matieres->links() }}
    </div>
    @endif
</div>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function deleteMatiere(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette matière ?')) {
        const form = document.getElementById('deleteForm');
        form.action = `/etablissement/matieres/${id}`;
        form.submit();
    }
}
</script>
@endpush
@endsection