{{-- resources/views/comptable/factures/pdf.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture {{ $facture->numero }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #8b5cf6;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #8b5cf6;
            margin-bottom: 5px;
        }
        .facture-title {
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
            color: #8b5cf6;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .info-box {
            border: 1px solid #e5e7eb;
            padding: 15px;
            border-radius: 8px;
        }
        .info-label {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 5px;
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
            padding: 10px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            color: #374151;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .total {
            font-weight: bold;
            background: #f9fafb;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .status-payee {
            background: #d1fae5;
            color: #065f46;
        }
        .status-impayee {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">{{ config('app.name') }}</div>
        <p>{{ $etablissement->nom ?? 'Système de Gestion Scolaire' }}</p>
        <p>{{ $etablissement->adresse ?? '' }} - {{ $etablissement->ville ?? '' }}</p>
        <p>Tél: {{ $etablissement->telephone ?? '' }} | Email: {{ $etablissement->email ?? '' }}</p>
        <p class="facture-title">FACTURE N° {{ $facture->numero }}</p>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <div class="info-label">Date d'émission</div>
            <div class="info-value">{{ $facture->date_emission->format('d/m/Y') }}</div>
            
            <div class="info-label" style="margin-top: 10px;">Date d'échéance</div>
            <div class="info-value">{{ $facture->date_echeance->format('d/m/Y') }}</div>
        </div>
        
        <div class="info-box">
            <div class="info-label">Statut</div>
            <div class="info-value">
                <span class="status-badge {{ $facture->statut == 'payee' ? 'status-payee' : 'status-impayee' }}">
                    {{ $facture->statut == 'payee' ? 'PAYÉE' : 'À PAYER' }}
                </span>
            </div>
            
            <div class="info-label" style="margin-top: 10px;">Mode de paiement</div>
            <div class="info-value">{{ $facture->mode_paiement ?? 'À définir' }}</div>
        </div>
    </div>

    <div class="info-box" style="margin-bottom: 20px;">
        <div class="info-label">Client</div>
        <div class="info-value" style="font-size: 16px;">{{ $facture->eleve->prenom }} {{ $facture->eleve->nom }}</div>
        <div style="display: flex; gap: 20px; margin-top: 10px;">
            <div>
                <div class="info-label">Classe</div>
                <div class="info-value">{{ $facture->eleve->classe->nom }}</div>
            </div>
            <div>
                <div class="info-label">Matricule</div>
                <div class="info-value">{{ $facture->eleve->matricule }}</div>
            </div>
        </div>
        @if($facture->eleve->email_parent)
        <div style="margin-top: 10px;">
            <div class="info-label">Email</div>
            <div class="info-value">{{ $facture->eleve->email_parent }}</div>
        </div>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th style="text-align: right">Montant HT</th>
                <th style="text-align: right">TVA (18%)</th>
                <th style="text-align: right">Montant TTC</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>{{ $facture->description ?? 'Frais de scolarité' }}</strong>
                    <br><small>Référence: {{ $facture->numero }}</small>
                </td>
                <td style="text-align: right">{{ number_format($facture->montant_ht, 0, ',', ' ') }} FCFA</td>
                <td style="text-align: right">{{ number_format($facture->montant_ttc - $facture->montant_ht, 0, ',', ' ') }} FCFA</td>
                <td style="text-align: right">{{ number_format($facture->montant_ttc, 0, ',', ' ') }} FCFA</td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="total">
                <td colspan="3" style="text-align: right"><strong>TOTAL TTC</strong></td>
                <td style="text-align: right"><strong>{{ number_format($facture->montant_ttc, 0, ',', ' ') }} FCFA</strong></td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 30px;">
        <p><strong>Arrêté la présente facture à la somme de :</strong></p>
        <p style="font-size: 14px; font-style: italic;">{{ ucfirst(\App\Helpers\NumberToWords::convert($facture->montant_ttc)) }} Francs CFA</p>
    </div>

    <div style="margin-top: 30px; font-size: 11px; color: #666;">
        <p>Conditions de règlement : paiement à réception de facture</p>
        <p>Pénalités de retard : 3 fois le taux d'intérêt légal</p>
        <p>Escompte pour règlement anticipé : néant</p>
        <p>TVA non applicable, article 257 du CGI</p>
    </div>

    <div class="footer">
        <p>Document généré le {{ now()->format('d/m/Y à H:i') }} - {{ config('app.name') }}</p>
        <p>Pour toute réclamation, contactez le service comptable</p>
    </div>
</body>
</html>