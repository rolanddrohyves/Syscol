<?php
// app/Http/Controllers/Comptable/FactureController.php

namespace App\Http\Controllers\Comptable;

use App\Http\Controllers\Controller;
use App\Models\Facture;
use App\Models\Eleve;
use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class FactureController extends Controller
{
    /**
     * Affiche la liste des factures
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $query = Facture::where('etablissement_id', $etablissementId)
            ->with(['eleve', 'eleve.classe']);
        
        // Filtres
        if ($request->filled('classe_id')) {
            $query->whereHas('eleve', function($q) use ($request) {
                $q->where('classe_id', $request->classe_id);
            });
        }
        
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date_emission', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('date_emission', '<=', $request->date_fin);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                  ->orWhereHas('eleve', function($q2) use ($search) {
                      $q2->where('prenom', 'like', "%{$search}%")
                         ->orWhere('nom', 'like', "%{$search}%");
                  });
            });
        }
        
        $factures = $query->orderBy('date_emission', 'desc')->paginate(20);
        
        $classes = Classe::where('etablissement_id', $etablissementId)->get();
        
        // Statistiques
        $stats = [
            'total' => Facture::where('etablissement_id', $etablissementId)->sum('montant_ttc'),
            'impayees' => Facture::where('etablissement_id', $etablissementId)
                ->whereIn('statut', ['emise', 'envoyee'])
                ->sum('montant_ttc'),
            'payees' => Facture::where('etablissement_id', $etablissementId)
                ->where('statut', 'payee')
                ->sum('montant_ttc'),
            'nombre' => Facture::where('etablissement_id', $etablissementId)->count(),
        ];
        
        return view('comptable.factures.index', compact('factures', 'classes', 'stats'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $eleveId = $request->get('eleve_id');
        $classeId = $request->get('classe_id');
        
        $classes = Classe::where('etablissement_id', $etablissementId)
            ->orderBy('niveau')
            ->orderBy('nom')
            ->get();
        
        $elevesParClasse = [];
        foreach ($classes as $classe) {
            $elevesParClasse[$classe->id] = Eleve::where('classe_id', $classe->id)
                ->where('status', 'actif')
                ->orderBy('nom')
                ->orderBy('prenom')
                ->get(['id', 'prenom', 'nom']);
        }
        
        // Générer un numéro de facture unique
        $lastId = Facture::max('id') ?? 0;
        $numero = 'FACT-' . date('Y') . '-' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);
        
        return view('comptable.factures.create', compact('classes', 'elevesParClasse', 'eleveId', 'classeId', 'numero'));
    }

    /**
     * Enregistre une nouvelle facture
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;

        $validator = Validator::make($request->all(), [
            'eleve_id' => 'required|exists:eleves,id',
            'classe_id' => 'required|exists:classes,id',
            'numero' => 'required|string|unique:factures,numero',
            'date_emission' => 'required|date',
            'date_echeance' => 'required|date|after_or_equal:date_emission',
            'montant_ht' => 'required|numeric|min:1',
            'montant_ttc' => 'required|numeric|min:1',
            'description' => 'nullable|string',
            'statut' => 'required|in:emise,envoyee,payee,impayee',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier que l'élève appartient à la classe
        $eleve = Eleve::find($request->eleve_id);
        if ($eleve->classe_id != $request->classe_id) {
            return redirect()->back()
                ->with('error', "L'élève n'appartient pas à la classe sélectionnée.")
                ->withInput();
        }

        Facture::create([
            'etablissement_id' => $etablissementId,
            'eleve_id' => $request->eleve_id,
            'numero' => $request->numero,
            'date_emission' => $request->date_emission,
            'date_echeance' => $request->date_echeance,
            'montant_ht' => $request->montant_ht,
            'montant_ttc' => $request->montant_ttc,
            'description' => $request->description,
            'statut' => $request->statut,
        ]);

        return redirect()->route('comptable.factures.index')
            ->with('success', 'Facture créée avec succès.');
    }

    /**
     * Affiche les détails d'une facture
     */
    public function show($id)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $facture = Facture::where('etablissement_id', $etablissementId)
            ->with(['eleve', 'eleve.classe'])
            ->findOrFail($id);
        
        return view('comptable.factures.show', compact('facture'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit($id)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $facture = Facture::where('etablissement_id', $etablissementId)
            ->findOrFail($id);
        
        $classes = Classe::where('etablissement_id', $etablissementId)
            ->orderBy('niveau')
            ->orderBy('nom')
            ->get();
        
        $elevesParClasse = [];
        foreach ($classes as $classe) {
            $elevesParClasse[$classe->id] = Eleve::where('classe_id', $classe->id)
                ->where('status', 'actif')
                ->orderBy('nom')
                ->orderBy('prenom')
                ->get(['id', 'prenom', 'nom']);
        }
        
        return view('comptable.factures.edit', compact('facture', 'classes', 'elevesParClasse'));
    }

    /**
     * Met à jour une facture
     */
    public function update(Request $request, $id)
    {
        $facture = Facture::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'eleve_id' => 'required|exists:eleves,id',
            'classe_id' => 'required|exists:classes,id',
            'numero' => 'required|string|unique:factures,numero,' . $id,
            'date_emission' => 'required|date',
            'date_echeance' => 'required|date|after_or_equal:date_emission',
            'montant_ht' => 'required|numeric|min:1',
            'montant_ttc' => 'required|numeric|min:1',
            'description' => 'nullable|string',
            'statut' => 'required|in:emise,envoyee,payee,impayee',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier l'élève
        $eleve = Eleve::find($request->eleve_id);
        if ($eleve->classe_id != $request->classe_id) {
            return redirect()->back()
                ->with('error', "L'élève n'appartient pas à la classe sélectionnée.")
                ->withInput();
        }

        $facture->update($request->all());

        return redirect()->route('comptable.factures.index')
            ->with('success', 'Facture mise à jour avec succès.');
    }

    /**
     * Supprime une facture
     */
    public function destroy($id)
    {
        $facture = Facture::findOrFail($id);
        $facture->delete();

        return redirect()->route('comptable.factures.index')
            ->with('success', 'Facture supprimée avec succès.');
    }

    /**
     * Marque une facture comme payée
     */
    public function marquerPayee($id)
    {
        $facture = Facture::findOrFail($id);
        $facture->update(['statut' => 'payee']);

        return redirect()->back()->with('success', 'Facture marquée comme payée.');
    }

    /**
     * Envoie la facture par email
     */
    public function envoyerEmail($id)
    {
        $facture = Facture::with('eleve')->findOrFail($id);
        
        // Vérifier que l'élève a un email parent
        if (!$facture->eleve->email_parent) {
            return redirect()->back()
                ->with('error', "L'élève n'a pas d'adresse email parent renseignée.");
        }
        
        try {
            // Générer le PDF
            $pdf = Pdf::loadView('comptable.factures.pdf', compact('facture'));
            
            // Envoyer l'email
            Mail::send('emails.facture', ['facture' => $facture], function($message) use ($facture, $pdf) {
                $message->to($facture->eleve->email_parent)
                        ->subject('Facture ' . $facture->numero)
                        ->attachData($pdf->output(), 'facture-' . $facture->numero . '.pdf');
            });
            
            $facture->update(['statut' => 'envoyee']);
            
            return redirect()->back()->with('success', 'Facture envoyée par email avec succès.');
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'envoi de l\'email: ' . $e->getMessage());
        }
    }

    /**
     * Export PDF d'une facture
     */
    public function pdf($id)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $facture = Facture::where('etablissement_id', $etablissementId)
            ->with(['eleve', 'eleve.classe'])
            ->findOrFail($id);
        
        $etablissement = \App\Models\Etablissement::find($etablissementId);
        
        $pdf = Pdf::loadView('comptable.factures.pdf', compact('facture', 'etablissement'));
        
        return $pdf->download('facture-' . $facture->numero . '.pdf');
    }
}