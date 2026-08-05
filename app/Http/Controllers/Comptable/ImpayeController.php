<?php
// app/Http/Controllers/Comptable/ImpayeController.php

namespace App\Http\Controllers\Comptable;

use App\Http\Controllers\Controller;
use App\Models\Eleve;
use App\Models\Classe;
use App\Models\FraisScolarite;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpayeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $etablissementId = $user->etablissement_id;

        // Récupérer les classes pour le filtre
        $classes = Classe::where('etablissement_id', $etablissementId)
            ->orderBy('niveau')
            ->orderBy('nom')
            ->get();

        // Récupérer tous les frais par type
        $fraisInscription = FraisScolarite::where('etablissement_id', $etablissementId)
            ->where('type', 'inscription')
            ->first();
        
        $fraisScolarite = FraisScolarite::where('etablissement_id', $etablissementId)
            ->where('type', 'scolarite')
            ->first();

        $montantInscription = $fraisInscription ? $fraisInscription->montant : 0;
        $montantScolarite = $fraisScolarite ? $fraisScolarite->montant : 0;

        // Récupérer tous les élèves
        $query = Eleve::whereHas('classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->with(['classe', 'paiements']);

        // Filtre par classe
        if ($request->filled('classe_id')) {
            $query->where('classe_id', $request->classe_id);
        }

        // Filtre par recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('matricule', 'like', "%{$search}%");
            });
        }

        $eleves = $query->get();
        
        // Calculer les montants pour chaque élève
        $totalGeneral = 0;
        $totalPayeGeneral = 0;
        $totalResteGeneral = 0;
        $nombreImpayes = 0;
        
        $elevesAvecImpayes = [];
        
        foreach ($eleves as $eleve) {
            // Calculer le total des frais pour cet élève
            $totalFrais = $montantInscription + $montantScolarite;
            
            // Calculer le total payé par l'élève
            $totalPaye = $eleve->paiements->sum('montant');
            
            // Calculer ce qui a été payé spécifiquement pour l'inscription et la scolarité
            $payeInscription = $eleve->paiements->where('frais.type', 'inscription')->sum('montant');
            $payeScolarite = $eleve->paiements->where('frais.type', 'scolarite')->sum('montant');
            
            $resteInscription = $montantInscription - $payeInscription;
            $resteScolarite = $montantScolarite - $payeScolarite;
            $reste = $totalFrais - $totalPaye;
            
            $totalGeneral += $totalFrais;
            $totalPayeGeneral += $totalPaye;
            $totalResteGeneral += $reste > 0 ? $reste : 0;
            
            if ($reste > 0) {
                $nombreImpayes++;
                $elevesAvecImpayes[] = (object)[
                    'id' => $eleve->id,
                    'prenom' => $eleve->prenom,
                    'nom' => $eleve->nom,
                    'matricule' => $eleve->matricule,
                    'classe' => $eleve->classe,
                    'email_parent' => $eleve->email_parent,
                    'telephone_parent' => $eleve->telephone_parent,
                    'montant_inscription' => $montantInscription,
                    'montant_scolarite' => $montantScolarite,
                    'montant_total_frais' => $totalFrais,
                    'montant_paye' => $totalPaye,
                    'montant_restant' => $reste,
                    'reste_inscription' => $resteInscription > 0 ? $resteInscription : 0,
                    'reste_scolarite' => $resteScolarite > 0 ? $resteScolarite : 0,
                    'pourcentage_paye' => $totalFrais > 0 ? round(($totalPaye / $totalFrais) * 100, 2) : 0
                ];
            }
        }

        // Pagination manuelle
        $perPage = 20;
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedItems = array_slice($elevesAvecImpayes, $offset, $perPage);
        
        $elevesAvecImpayes = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            count($elevesAvecImpayes),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Statistiques
        $statistiques = [
            'total_impaye' => $totalResteGeneral,
            'nombre_impaye' => $nombreImpayes,
            'total_paye' => $totalPayeGeneral,
            'total_frais' => $totalGeneral,
            'taux_recouvrement' => $totalGeneral > 0 ? round(($totalPayeGeneral / $totalGeneral) * 100, 2) : 0
        ];

        return view('comptable.impayes.index', compact('elevesAvecImpayes', 'classes', 'statistiques'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $etablissementId = $user->etablissement_id;

        $eleve = Eleve::whereHas('classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->with(['classe', 'paiements.frais'])
            ->findOrFail($id);

        // Récupérer les frais
        $fraisInscription = FraisScolarite::where('etablissement_id', $etablissementId)
            ->where('type', 'inscription')
            ->first();
        
        $fraisScolarite = FraisScolarite::where('etablissement_id', $etablissementId)
            ->where('type', 'scolarite')
            ->first();

        $montantInscription = $fraisInscription ? $fraisInscription->montant : 0;
        $montantScolarite = $fraisScolarite ? $fraisScolarite->montant : 0;

        // Calculer les totaux
        $totalPaye = $eleve->paiements->sum('montant');
        $totalFrais = $montantInscription + $montantScolarite;
        $reste = $totalFrais - $totalPaye;

        return view('comptable.impayes.show', compact('eleve', 'totalFrais', 'totalPaye', 'reste', 'montantInscription', 'montantScolarite'));
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        $etablissementId = $user->etablissement_id;

        // Récupérer les frais
        $fraisInscription = FraisScolarite::where('etablissement_id', $etablissementId)
            ->where('type', 'inscription')
            ->first();
        
        $fraisScolarite = FraisScolarite::where('etablissement_id', $etablissementId)
            ->where('type', 'scolarite')
            ->first();

        $montantInscription = $fraisInscription ? $fraisInscription->montant : 0;
        $montantScolarite = $fraisScolarite ? $fraisScolarite->montant : 0;

        $query = Eleve::whereHas('classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->with(['classe', 'paiements']);

        if ($request->filled('classe_id')) {
            $query->where('classe_id', $request->classe_id);
        }

        $eleves = $query->get();

        $filename = 'impayes_' . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($handle, ['Matricule', 'Nom', 'Prénom', 'Classe', 'Frais Inscription', 'Frais Scolarité', 'Total Frais', 'Déjà Payé', 'Reste Inscription', 'Reste Scolarité', 'Reste Total', 'Taux (%)', 'Téléphone Parent', 'Email Parent']);

        foreach ($eleves as $eleve) {
            $totalPaye = $eleve->paiements->sum('montant');
            $totalFrais = $montantInscription + $montantScolarite;
            
            $payeInscription = $eleve->paiements->where('frais.type', 'inscription')->sum('montant');
            $payeScolarite = $eleve->paiements->where('frais.type', 'scolarite')->sum('montant');
            
            $resteInscription = $montantInscription - $payeInscription;
            $resteScolarite = $montantScolarite - $payeScolarite;
            $reste = $totalFrais - $totalPaye;
            $taux = $totalFrais > 0 ? round(($totalPaye / $totalFrais) * 100, 2) : 0;
            
            if ($reste > 0) {
                fputcsv($handle, [
                    $eleve->matricule,
                    $eleve->nom,
                    $eleve->prenom,
                    $eleve->classe->nom ?? '',
                    number_format($montantInscription, 0, ',', ' '),
                    number_format($montantScolarite, 0, ',', ' '),
                    number_format($totalFrais, 0, ',', ' '),
                    number_format($totalPaye, 0, ',', ' '),
                    number_format($resteInscription > 0 ? $resteInscription : 0, 0, ',', ' '),
                    number_format($resteScolarite > 0 ? $resteScolarite : 0, 0, ',', ' '),
                    number_format($reste, 0, ',', ' '),
                    $taux,
                    $eleve->telephone_parent,
                    $eleve->email_parent,
                ]);
            }
        }

        fclose($handle);
        exit;
    }
}