<?php
// app/Http/Controllers/Admin/EtablissementController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Etablissement;
use App\Models\Eleve;
use App\Models\Classe;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class EtablissementController extends Controller
{
    /**
     * Affiche la liste des établissements
     */
    public function index()
    {
        $etablissements = Etablissement::withCount(['classes', 'users'])
            ->orderBy('nom')
            ->paginate(15);
        
        $stats = [
            'total' => Etablissement::count(),
            'actifs' => Etablissement::where('is_active', true)->count(),
            'inactifs' => Etablissement::where('is_active', false)->count(),
            'types' => Etablissement::selectRaw('type, count(*) as total')
                ->groupBy('type')
                ->get()
        ];

        return view('admin.etablissements.index', compact('etablissements', 'stats'));
    }

    /**
     * Affiche le formulaire de création d'un établissement
     */
    public function create()
    {
        return view('admin.etablissements.create');
    }

    /**
     * Enregistre un nouvel établissement
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'type' => 'required|in:Primaire,Collège,Lycée,Primaire/Secondaire',
            'adresse' => 'required|string|max:500',
            'telephone' => 'required|string|max:20',
            'email' => 'required|email|unique:etablissements,email',
            'code_etablissement' => 'required|string|unique:etablissements,code_etablissement|max:50',
            'academie' => 'required|string|max:100',
            'inspectorat' => 'nullable|string|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except('logo');
        
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $data['logo'] = $path;
        }

        $etablissement = Etablissement::create($data);

        return redirect()->route('admin.etablissements')
            ->with('success', 'Établissement créé avec succès.');
    }

    /**
     * Affiche les détails d'un établissement
     */
    public function show($id)
    {
        $etablissement = Etablissement::with(['classes' => function($q) {
                $q->withCount('eleves');
            }, 'users' => function($q) {
                $q->with('role');
            }])
            ->findOrFail($id);
        
        $stats = [
            'total_classes' => $etablissement->classes->count(),
            'total_eleves' => $etablissement->classes->sum('eleves_count'),
            'total_enseignants' => $etablissement->users()
                ->whereHas('role', fn($q) => $q->where('name', 'enseignant'))
                ->count(),
            'total_admins' => $etablissement->users()
                ->whereHas('role', fn($q) => $q->where('name', 'admin_etablissement'))
                ->count(),
        ];

        return view('admin.etablissements.show', compact('etablissement', 'stats'));
    }

    /**
     * Affiche le formulaire d'édition d'un établissement
     */
    public function edit($id)
    {
        $etablissement = Etablissement::findOrFail($id);
        return view('admin.etablissements.edit', compact('etablissement'));
    }

    /**
     * Met à jour un établissement
     */
    public function update(Request $request, $id)
    {
        $etablissement = Etablissement::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'type' => 'required|in:Primaire,Collège,Lycée,Primaire/Secondaire',
            'adresse' => 'required|string|max:500',
            'telephone' => 'required|string|max:20',
            'email' => 'required|email|unique:etablissements,email,' . $id,
            'code_etablissement' => 'required|string|unique:etablissements,code_etablissement,' . $id . '|max:50',
            'academie' => 'required|string|max:100',
            'inspectorat' => 'nullable|string|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except('logo');
        
        if ($request->hasFile('logo')) {
            if ($etablissement->logo) {
                Storage::disk('public')->delete($etablissement->logo);
            }
            $path = $request->file('logo')->store('logos', 'public');
            $data['logo'] = $path;
        }

        $etablissement->update($data);

        return redirect()->route('admin.etablissements')
            ->with('success', 'Établissement mis à jour avec succès.');
    }

    /**
     * Supprime un établissement
     */
    public function destroy($id)
    {
        $etablissement = Etablissement::findOrFail($id);
        
        if ($etablissement->classes()->count() > 0) {
            return redirect()->route('admin.etablissements')
                ->with('error', 'Impossible de supprimer cet établissement car il contient des classes.');
        }
        
        if ($etablissement->users()->count() > 0) {
            return redirect()->route('admin.etablissements')
                ->with('error', 'Impossible de supprimer cet établissement car il a des utilisateurs associés.');
        }

        if ($etablissement->logo) {
            Storage::disk('public')->delete($etablissement->logo);
        }

        $etablissement->delete();

        return redirect()->route('admin.etablissements')
            ->with('success', 'Établissement supprimé avec succès.');
    }

    /**
     * Active ou désactive un établissement
     */
    public function toggleStatus($id)
    {
        $etablissement = Etablissement::findOrFail($id);
        $etablissement->is_active = !$etablissement->is_active;
        $etablissement->save();

        $status = $etablissement->is_active ? 'activé' : 'désactivé';
        
        return redirect()->route('admin.etablissements')
            ->with('success', "Établissement {$status} avec succès.");
    }

    /**
     * Exporte la liste des établissements
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $etablissements = Etablissement::all();
        
        switch($format) {
            case 'csv':
                return $this->exportCsv($etablissements);
            case 'pdf':
                return redirect()->back()->with('info', 'Export PDF en cours de développement');
            case 'excel':
                return redirect()->back()->with('info', 'Export Excel en cours de développement');
            default:
                return redirect()->back()->with('error', 'Format non supporté');
        }
    }

    /**
     * Export CSV
     */
    private function exportCsv($etablissements)
    {
        $filename = "etablissements_" . date('Y-m-d') . ".csv";
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        fputcsv($handle, ['ID', 'Nom', 'Type', 'Email', 'Téléphone', 'Académie', 'Statut']);
        
        foreach ($etablissements as $etab) {
            fputcsv($handle, [
                $etab->id,
                $etab->nom,
                $etab->type,
                $etab->email,
                $etab->telephone,
                $etab->academie,
                $etab->is_active ? 'Actif' : 'Inactif'
            ]);
        }
        
        fclose($handle);
        exit;
    }

    /**
     * Recherche d'établissements
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $etablissements = Etablissement::where('nom', 'like', "%{$query}%")
            ->orWhere('code_etablissement', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('academie', 'like', "%{$query}%")
            ->withCount('classes')
            ->limit(10)
            ->get();
        
        return response()->json($etablissements);
    }

    /**
     * Statistiques détaillées
     */
    public function statistiques()
    {
        $stats = [
            'par_type' => Etablissement::selectRaw('type, count(*) as total')
                ->groupBy('type')
                ->get(),
            'par_academie' => Etablissement::selectRaw('academie, count(*) as total')
                ->groupBy('academie')
                ->get(),
            'evolution' => Etablissement::selectRaw('YEAR(created_at) as annee, MONTH(created_at) as mois, count(*) as total')
                ->groupBy('annee', 'mois')
                ->orderBy('annee')
                ->orderBy('mois')
                ->get(),
            'total_eleves' => Eleve::count(),
            'total_classes' => Classe::count(),
            'total_utilisateurs' => User::count(),
        ];

        return view('admin.etablissements.statistiques', compact('stats'));
    }

    /**
     * Données pour les graphiques
     */
    public function chartData()
    {
        $data = [
            'types' => Etablissement::selectRaw('type, count(*) as total')
                ->groupBy('type')
                ->get(),
            'evolution' => Etablissement::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as mois, count(*) as total')
                ->groupBy('mois')
                ->orderBy('mois')
                ->limit(12)
                ->get(),
        ];

        return response()->json($data);
    }
}