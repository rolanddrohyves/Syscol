{{-- resources/views/comptable/paiements/recu.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu de paiement - {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
            background: #f0f2f5;
        }
        .recu-container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border-radius: 16px;
            overflow: hidden;
        }
        .header {
            text-align: center;
            padding: 30px 20px;
            background: linear-gradient(135deg, #1a5f7a, #0d3b4f);
            color: white;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .logo i {
            font-size: 32px;
            margin-right: 10px;
        }
        .recu-title {
            font-size: 20px;
            font-weight: bold;
            margin: 15px 0 5px;
            color: #ffd700;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
            padding: 0 20px;
        }
        .info-box {
            border: 1px solid #e5e7eb;
            padding: 15px;
            border-radius: 12px;
            background: #f9fafb;
        }
        .info-label {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-value {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background: #f3f4f6;
            padding: 12px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        .total {
            font-weight: bold;
            background: #f9fafb;
        }
        .footer {
            margin-top: 40px;
            padding: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            background: #f8f9fa;
        }
        .signature {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
        }
        .signature-line {
            width: 200px;
            border-top: 1px solid #000;
            margin-top: 30px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .status-paye {
            background: #d1fae5;
            color: #065f46;
        }
        .status-partiel {
            background: #fed7aa;
            color: #9a3412;
        }
        .status-attente {
            background: #fef3c7;
            color: #92400e;
        }
        .status-retard {
            background: #fee2e2;
            color: #991b1b;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin: 25px 20px 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #10b981;
            color: #1a5f7a;
        }
        .section-title i {
            margin-right: 8px;
            color: #10b981;
        }
        .echeance-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 20px;
            border-bottom: 1px dashed #e5e7eb;
        }
        .echeance-item:hover {
            background: #f9fafb;
        }
        .total-general {
            background: #f3f4f6;
            font-weight: bold;
        }
        .text-success { color: #065f46; }
        .text-warning { color: #92400e; }
        .text-danger { color: #991b1b; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .mt-2 { margin-top: 10px; }
        .alert {
            padding: 12px 20px;
            border-radius: 8px;
            margin: 15px 20px;
        }
        .alert-warning {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            color: #92400e;
        }
        .alert-danger {
            background: #fee2e2;
            border-left: 4px solid #ef4444;
            color: #991b1b;
        }
        .alert-success {
            background: #d1fae5;
            border-left: 4px solid #10b981;
            color: #065f46;
        }
        .progress-bar {
            background: #e5e7eb;
            border-radius: 10px;
            height: 8px;
            overflow: hidden;
        }
        .progress-fill {
            background: #10b981;
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s ease;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 0; background: white; }
            .recu-container { box-shadow: none; margin: 0; border-radius: 0; }
            .header { background: #1a5f7a; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 500;
        }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fed7aa; color: #9a3412; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="recu-container">
        <!-- En-tête -->
        <div class="header">
            <div class="logo">
                <i class="fas fa-graduation-cap"></i>
                {{ config('app.name', 'SYSCOL') }}
            </div>
            <p>Système de Gestion Scolaire</p>
            <p class="recu-title">
                <i class="fas fa-receipt"></i> REÇU DE PAIEMENT
            </p>
            <p><i class="fas fa-hashtag"></i> {{ $paiement->reference ?? $paiement->id }}</p>
        </div>

        <!-- Informations du paiement -->
        <div class="info-grid">
            <div class="info-box">
                <div class="info-label"><i class="fas fa-calendar-alt"></i> Date du paiement</div>
                <div class="info-value">{{ $paiement->date_paiement->format('d/m/Y') }}</div>
                <div class="info-label" style="margin-top: 10px;"><i class="fas fa-credit-card"></i> Mode de paiement</div>
                <div class="info-value">
                    @switch($paiement->mode_paiement)
                        @case('especes') <i class="fas fa-money-bill-wave"></i> Espèces @break
                        @case('cheque') <i class="fas fa-money-check"></i> Chèque @break
                        @case('virement') <i class="fas fa-university"></i> Virement bancaire @break
                        @case('carte') <i class="fas fa-credit-card"></i> Carte bancaire @break
                        @case('mobile_money') <i class="fas fa-mobile-alt"></i> Mobile Money @break
                        @default {{ ucfirst($paiement->mode_paiement) }}
                    @endswitch
                </div>
            </div>
            <div class="info-box">
                <div class="info-label"><i class="fas fa-info-circle"></i> Statut</div>
                <div class="info-value">
                    <span class="status-badge 
                        @if($paiement->statut == 'paye') status-paye
                        @elseif($paiement->statut == 'partiel') status-partiel
                        @elseif($paiement->statut == 'en_attente') status-attente
                        @else status-retard
                        @endif">
                        @if($paiement->statut == 'paye') <i class="fas fa-check-circle"></i> PAYÉ
                        @elseif($paiement->statut == 'partiel') <i class="fas fa-exclamation-triangle"></i> PAIEMENT PARTIEL
                        @elseif($paiement->statut == 'en_attente') <i class="fas fa-clock"></i> EN ATTENTE
                        @else <i class="fas fa-times-circle"></i> ANNULÉ
                        @endif
                    </span>
                </div>
                <div class="info-label" style="margin-top: 10px;"><i class="fas fa-barcode"></i> Référence</div>
                <div class="info-value">{{ $paiement->reference ?? 'N/A' }}</div>
            </div>
        </div>

        <!-- Informations de l'élève -->
        <div class="info-box" style="margin: 0 20px 20px 20px;">
            <div class="info-label"><i class="fas fa-user-graduate"></i> Élève</div>
            <div class="info-value" style="font-size: 16px;">{{ $paiement->eleve->prenom }} {{ $paiement->eleve->nom }}</div>
            <div style="display: flex; gap: 30px; margin-top: 10px; flex-wrap: wrap;">
                <div>
                    <div class="info-label"><i class="fas fa-door-open"></i> Classe</div>
                    <div class="info-value">{{ $paiement->eleve->classe->nom ?? 'Non définie' }}</div>
                </div>
                <div>
                    <div class="info-label"><i class="fas fa-id-card"></i> Matricule</div>
                    <div class="info-value">{{ $paiement->eleve->matricule }}</div>
                </div>
                <div>
                    <div class="info-label"><i class="fas fa-calendar-alt"></i> Année scolaire</div>
                    <div class="info-value">{{ $paiement->frais->anneeScolaire->libelle ?? date('Y') . '-' . (date('Y')+1) }}</div>
                </div>
            </div>
        </div>

        <!-- Récapitulatif financier -->
        @php
            use App\Services\EcheanceService;
            $echeanceService = new EcheanceService();
            $situation = $echeanceService->getSituationFinanciere($paiement->eleve);
        @endphp

        <div class="section-title">
            <i class="fas fa-chart-line"></i> RÉCAPITULATIF FINANCIER
        </div>
        <div class="info-grid" style="margin-bottom: 20px;">
            <div class="info-box">
                <div class="info-label"><i class="fas fa-coins"></i> Total des frais</div>
                <div class="info-value" style="font-size: 18px;">{{ number_format($situation['total_general'], 0, ',', ' ') }} FCFA</div>
            </div>
            <div class="info-box">
                <div class="info-label"><i class="fas fa-check-circle"></i> Déjà payé</div>
                <div class="info-value text-success" style="font-size: 18px;">{{ number_format($situation['total_paye'], 0, ',', ' ') }} FCFA</div>
            </div>
            <div class="info-box">
                <div class="info-label"><i class="fas fa-clock"></i> Reste à payer</div>
                <div class="info-value text-warning" style="font-size: 18px;">{{ number_format($situation['total_reste'], 0, ',', ' ') }} FCFA</div>
            </div>
            <div class="info-box">
                <div class="info-label"><i class="fas fa-percent"></i> Taux de paiement</div>
                <div class="info-value">{{ $situation['pourcentage_paye'] }}%</div>
                <div class="progress-bar mt-2">
                    <div class="progress-fill" style="width: {{ $situation['pourcentage_paye'] }}%"></div>
                </div>
            </div>
        </div>

        <!-- Détail des frais par catégorie -->
        @if(!empty($situation['par_frais']))
        <div class="section-title">
            <i class="fas fa-tags"></i> DÉTAIL DES FRAIS PAR CATÉGORIE
        </div>
        <table style="margin: 0 20px; width: calc(100% - 40px);">
            <thead>
                <tr>
                    <th><i class="fas fa-tag"></i> Type de frais</th>
                    <th class="text-right"><i class="fas fa-coins"></i> Total</th>
                    <th class="text-right"><i class="fas fa-check"></i> Payé</th>
                    <th class="text-right"><i class="fas fa-clock"></i> Reste</th>
                    <th class="text-center"><i class="fas fa-chart-simple"></i> Statut</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $typeLabels = [
                        'inscription' => '<i class="fas fa-pen-alt"></i> Frais d\'inscription',
                        'scolarite' => '<i class="fas fa-book"></i> Scolarité',
                        'cantine' => '<i class="fas fa-utensils"></i> Cantine',
                        'transport' => '<i class="fas fa-bus"></i> Transport',
                        'sortie' => '<i class="fas fa-hiking"></i> Sortie pédagogique',
                        'autre' => '<i class="fas fa-box"></i> Autres frais'
                    ];
                @endphp
                @foreach($situation['par_frais'] as $type => $data)
                <tr>
                    <td>{!! $typeLabels[$type] ?? ucfirst($type) !!}</td>
                    <td class="text-right">{{ number_format($data['total'], 0, ',', ' ') }} FCFA</td>
                    <td class="text-right text-success">{{ number_format($data['paye'], 0, ',', ' ') }} FCFA</td>
                    <td class="text-right {{ $data['reste'] > 0 ? 'text-warning' : 'text-success' }}">{{ number_format($data['reste'], 0, ',', ' ') }} FCFA</td>
                    <td class="text-center">
                        @if($data['reste'] <= 0)
                            <span class="badge badge-success"><i class="fas fa-check-circle"></i> Payé</span>
                        @elseif($data['paye'] > 0)
                            <span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> Partiel</span>
                        @else
                            <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Impayé</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="total">
                <tr>
                    <td><strong>TOTAUX</strong></td>
                    <td class="text-right"><strong>{{ number_format($situation['total_general'], 0, ',', ' ') }} FCFA</strong></td>
                    <td class="text-right"><strong>{{ number_format($situation['total_paye'], 0, ',', ' ') }} FCFA</strong></td>
                    <td class="text-right"><strong>{{ number_format($situation['total_reste'], 0, ',', ' ') }} FCFA</strong></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        @endif

        <!-- Détail du paiement effectué -->
        <div class="section-title">
            <i class="fas fa-money-bill-wave"></i> DÉTAIL DU PAIEMENT EFFECTUÉ
        </div>
        <table style="margin: 0 20px; width: calc(100% - 40px);">
            <thead>
                <tr>
                    <th><i class="fas fa-align-left"></i> Description</th>
                    <th><i class="fas fa-calendar"></i> Période concernée</th>
                    <th class="text-right"><i class="fas fa-coins"></i> Montant</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $paiement->frais->libelle }}</strong>
                        @if($paiement->frais->description)
                            <br><small>{{ $paiement->frais->description }}</small>
                        @endif
                    </td>
                    <td>{{ $paiement->periode_concernee ?? 'Paiement unique' }}</td>
                    <td class="text-right"><strong style="font-size: 16px;">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</strong></td>
                </tr>
                @if($paiement->commentaire)
                <tr>
                    <td colspan="3"><em><i class="fas fa-comment"></i> Note: {{ $paiement->commentaire }}</em></td>
                </tr>
                @endif
            </tbody>
            <tfoot class="total">
                <tr>
                    <td colspan="2" class="text-right"><strong>MONTANT VERSÉ</strong></td>
                    <td class="text-right"><strong style="font-size: 16px;">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</strong></td>
                </tr>
            </tfoot>
        </table>

        <!-- Échéancier complet -->
        @if($situation['toutes_echeances'] && $situation['toutes_echeances']->count() > 0)
        <div class="section-title">
            <i class="fas fa-calendar-alt"></i> ÉCHÉANCIER DES PAIEMENTS
        </div>
        <div style="overflow-x: auto; margin: 0 20px;">
            <table style="min-width: 700px;">
                <thead>
                    <tr>
                        <th><i class="fas fa-calendar-week"></i> Période</th>
                        <th><i class="fas fa-tag"></i> Libellé</th>
                        <th class="text-right"><i class="fas fa-coins"></i> Montant</th>
                        <th class="text-right"><i class="fas fa-check"></i> Payé</th>
                        <th class="text-right"><i class="fas fa-clock"></i> Reste</th>
                        <th><i class="fas fa-hourglass-end"></i> Date limite</th>
                        <th class="text-center"><i class="fas fa-chart-simple"></i> Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalEcheances = 0;
                        $totalPayeEcheances = 0;
                    @endphp
                    @foreach($situation['toutes_echeances'] as $echeance)
                        @php
                            $estPaye = $echeance->montant_paye >= $echeance->montant;
                            $estRetard = !$estPaye && $echeance->date_limite < now();
                            $resteEcheance = $echeance->montant_restant;
                            $totalEcheances += $echeance->montant;
                            $totalPayeEcheances += $echeance->montant_paye;
                        @endphp
                        <tr style="background: {{ $estPaye ? '#d1fae5' : ($estRetard ? '#fee2e2' : 'white') }}">
                            <td>{{ $echeance->periode }}</td>
                            <td>{{ $echeance->libelle }}</td>
                            <td class="text-right">{{ number_format($echeance->montant, 0, ',', ' ') }} FCFA</td>
                            <td class="text-right text-success">{{ number_format($echeance->montant_paye, 0, ',', ' ') }} FCFA</td>
                            <td class="text-right {{ $resteEcheance > 0 ? 'text-warning' : 'text-success' }}">{{ number_format($resteEcheance, 0, ',', ' ') }} FCFA</td>
                            <td>{{ $echeance->date_limite->format('d/m/Y') }}</td>
                            <td class="text-center">
                                @if($estPaye)
                                    <span class="badge badge-success"><i class="fas fa-check-circle"></i> Payé</span>
                                @elseif($estRetard)
                                    <span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> En retard</span>
                                @else
                                    <span class="badge badge-warning"><i class="fas fa-clock"></i> En attente</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="total">
                    <tr>
                        <td colspan="2"><strong>TOTAUX</strong></td>
                        <td class="text-right"><strong>{{ number_format($totalEcheances, 0, ',', ' ') }} FCFA</strong></td>
                        <td class="text-right"><strong>{{ number_format($totalPayeEcheances, 0, ',', ' ') }} FCFA</strong></td>
                        <td class="text-right"><strong>{{ number_format($totalEcheances - $totalPayeEcheances, 0, ',', ' ') }} FCFA</strong></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif

        <!-- Prochaines échéances -->
        @if($situation['prochaines_echeances'] && $situation['prochaines_echeances']->count() > 0)
        <div class="section-title">
            <i class="fas fa-bell"></i> PROCHAINES ÉCHÉANCES
        </div>
        <div style="margin: 0 20px 20px;">
            @foreach($situation['prochaines_echeances'] as $prochaine)
                <div class="echeance-item">
                    <div>
                        <strong>{{ $prochaine->libelle }}</strong> - {{ $prochaine->periode }}
                        <br><small><i class="fas fa-calendar-alt"></i> Date limite: {{ $prochaine->date_limite->format('d/m/Y') }}</small>
                    </div>
                    <div class="text-right">
                        <span class="text-warning" style="font-size: 14px;">{{ number_format($prochaine->montant_restant, 0, ',', ' ') }} FCFA</span>
                        <br><small><i class="fas fa-hourglass-half"></i> à payer dans {{ now()->diffInDays($prochaine->date_limite) }} jour(s)</small>
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        <!-- Alertes -->
        @if($situation['total_reste'] > 0)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> <strong>Attention :</strong> Un montant de <strong>{{ number_format($situation['total_reste'], 0, ',', ' ') }} FCFA</strong> reste à payer.
                @if($situation['echeances_retard'] && $situation['echeances_retard']->count() > 0)
                    <br><i class="fas fa-clock"></i> Dont <strong>{{ $situation['echeances_retard']->count() }} échéance(s)</strong> en retard.
                @endif
            </div>
        @else
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <strong>Félicitations !</strong> Tous les frais sont entièrement payés.
            </div>
        @endif

        @if($situation['echeances_retard'] && $situation['echeances_retard']->count() > 0)
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <strong>Échéances en retard :</strong>
                <ul style="margin-top: 8px; margin-left: 20px;">
                    @foreach($situation['echeances_retard'] as $retard)
                        <li>{{ $retard->libelle }} - {{ $retard->periode }} : {{ number_format($retard->montant_restant, 0, ',', ' ') }} FCFA (Date limite: {{ $retard->date_limite->format('d/m/Y') }})</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Signatures -->
        <div class="signature">
            <div>
                <div class="signature-line"></div>
                <p style="margin-top: 5px;"><i class="fas fa-pen"></i> Signature du parent / tuteur</p>
            </div>
            <div>
                <div class="signature-line"></div>
                <p style="margin-top: 5px;"><i class="fas fa-stamp"></i> Cachet de l'établissement</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><i class="fas fa-print"></i> Reçu généré le {{ now()->format('d/m/Y à H:i') }} - Document officiel à conserver</p>
            <p><i class="fas fa-copyright"></i> {{ config('app.name', 'SYSCOL') }} - Tous droits réservés</p>
        </div>

        <!-- Bouton impression -->
        <div class="no-print" style="text-align: center; margin: 20px; padding-bottom: 20px;">
            <button onclick="window.print()" style="padding: 12px 30px; background: #1a5f7a; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 14px;">
                <i class="fas fa-print"></i> Imprimer le reçu
            </button>
        </div>
    </div>
</body>
</html>