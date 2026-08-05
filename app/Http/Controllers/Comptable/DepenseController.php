<?php
// app/Http/Controllers/Comptable/DepenseController.php

namespace App\Http\Controllers\Comptable;

use App\Http\Controllers\Controller;
use App\Models\Depense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DepenseController extends Controller
{
    /**
     * Affiche la liste des dépenses
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $query = Depense::where('etablissement_id', $etablissementId);
        
        // Filtres
        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('libelle', 'like', "%{$search}%")
                  ->orWhere('beneficiaire', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $depenses = $query->orderBy('date', 'desc')->paginate(20);
        
        // Statistiques
        $stats = [
            'total' => Depense::where('etablissement_id', $etablissementId)->sum('montant'),
            'mois' => Depense::where('etablissement_id', $etablissementId)
                ->whereMonth('date', now()->month)
                ->sum('montant'),
            'nombre' => Depense::where('etablissement_id', $etablissementId)->count(),
            'par_categorie' => Depense::where('etablissement_id', $etablissementId)
                ->selectRaw('categorie, sum(montant) as total')
                ->groupBy('categorie')
                ->pluck('total', 'categorie'),
        ];
        
        // Liste des catégories pour le filtre
        $categories = Depense::where('etablissement_id', $etablissementId)
            ->distinct('categorie')
            ->pluck('categorie');
        
        return view('comptable.depenses.index', compact('depenses', 'stats', 'categories'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        $categories = [
            'fournitures' => 'Fournitures de bureau',
            'equipement' => 'Équipement',
            'maintenance' => 'Maintenance',
            'electricite' => 'Électricité',
            'eau' => 'Eau',
            'internet' => 'Internet/Téléphone',
            'transport' => 'Transport',
            'salaire' => 'Salaires',
            'formation' => 'Formation',
            'autre' => 'Autre'
        ];
        
        $modes = [
            'especes' => 'Espèces',
            'cheque' => 'Chèque',
            'virement' => 'Virement',
            'carte' => 'Carte bancaire'
        ];
        
        return view('comptable.depenses.create', compact('categories', 'modes'));
    }

    /**
     * Enregistre une nouvelle dépense
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;

        $validator = Validator::make($request->all(), [
            'libelle' => 'required|string|max:255',
            'description' => 'nullable|string',
            'montant' => 'required|numeric|min:1',
            'date' => 'required|date',
            'categorie' => 'required|string',
            'mode_paiement' => 'required|string',
            'beneficiaire' => 'nullable|string|max:255',
            'piece_jointe' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'etablissement_id' => $etablissementId,
            'libelle' => $request->libelle,
            'description' => $request->description,
            'montant' => $request->montant,
            'date' => $request->date,
            'categorie' => $request->categorie,
            'mode_paiement' => $request->mode_paiement,
            'beneficiaire' => $request->beneficiaire,
        ];

        // Gestion de la pièce jointe
        if ($request->hasFile('piece_jointe')) {
            $path = $request->file('piece_jointe')->store('depenses', 'public');
            $data['piece_jointe'] = $path;
        }

        Depense::create($data);

        return redirect()->route('comptable.depenses.index')
            ->with('success', 'Dépense enregistrée avec succès.');
    }

    /**
     * Affiche les détails d'une dépense
     */
    public function show($id)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $depense = Depense::where('etablissement_id', $etablissementId)
            ->findOrFail($id);
        
        return view('comptable.depenses.show', compact('depense'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit($id)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $depense = Depense::where('etablissement_id', $etablissementId)
            ->findOrFail($id);
        
        $categories = [
            'fournitures' => 'Fournitures de bureau',
            'equipement' => 'Équipement',
            'maintenance' => 'Maintenance',
            'electricite' => 'Électricité',
            'eau' => 'Eau',
            'internet' => 'Internet/Téléphone',
            'transport' => 'Transport',
            'salaire' => 'Salaires',
            'formation' => 'Formation',
            'autre' => 'Autre'
        ];
        
        $modes = [
            'especes' => 'Espèces',
            'cheque' => 'Chèque',
            'virement' => 'Virement',
            'carte' => 'Carte bancaire'
        ];
        
        return view('comptable.depenses.edit', compact('depense', 'categories', 'modes'));
    }

    /**
     * Met à jour une dépense
     */
    public function update(Request $request, $id)
    {
        $depense = Depense::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'libelle' => 'required|string|max:255',
            'description' => 'nullable|string',
            'montant' => 'required|numeric|min:1',
            'date' => 'required|date',
            'categorie' => 'required|string',
            'mode_paiement' => 'required|string',
            'beneficiaire' => 'nullable|string|max:255',
            'piece_jointe' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'libelle' => $request->libelle,
            'description' => $request->description,
            'montant' => $request->montant,
            'date' => $request->date,
            'categorie' => $request->categorie,
            'mode_paiement' => $request->mode_paiement,
            'beneficiaire' => $request->beneficiaire,
        ];

        // Gestion de la pièce jointe
        if ($request->hasFile('piece_jointe')) {
            // Supprimer l'ancienne pièce jointe
            if ($depense->piece_jointe) {
                Storage::disk('public')->delete($depense->piece_jointe);
            }
            $path = $request->file('piece_jointe')->store('depenses', 'public');
            $data['piece_jointe'] = $path;
        }

        $depense->update($data);

        return redirect()->route('comptable.depenses.index')
            ->with('success', 'Dépense mise à jour avec succès.');
    }

    /**
     * Supprime une dépense
     */
    public function destroy($id)
    {
        $depense = Depense::findOrFail($id);
        
        // Supprimer la pièce jointe
        if ($depense->piece_jointe) {
            Storage::disk('public')->delete($depense->piece_jointe);
        }
        
        $depense->delete();

        return redirect()->route('comptable.depenses.index')
            ->with('success', 'Dépense supprimée avec succès.');
    }

    /**
     * Export des dépenses au format CSV
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $query = Depense::where('etablissement_id', $etablissementId);
        
        // Appliquer les filtres
        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        
        $depenses = $query->orderBy('date', 'desc')->get();
        
        $filename = 'depenses_' . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($handle, ['Date', 'Libellé', 'Catégorie', 'Montant', 'Mode', 'Bénéficiaire']);
        
        foreach ($depenses as $depense) {
            fputcsv($handle, [
                $depense->date->format('d/m/Y'),
                $depense->libelle,
                $depense->categorie,
                number_format($depense->montant, 0, ',', ' ') . ' FCFA',
                $depense->mode_paiement,
                $depense->beneficiaire ?? '',
            ]);
        }
        
        fclose($handle);
        exit;
    }
}