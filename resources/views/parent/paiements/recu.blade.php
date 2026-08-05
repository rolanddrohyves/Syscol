{{-- resources/views/parent/paiements/recu.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reçu de paiement - {{ $paiement->reference }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .recu {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #4f46e5;
            margin: 0;
        }
        .info {
            margin-bottom: 20px;
        }
        .info table {
            width: 100%;
        }
        .info td {
            padding: 5px;
        }
        .details {
            margin-bottom: 20px;
        }
        .details table {
            width: 100%;
            border-collapse: collapse;
        }
        .details th, .details td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .details th {
            background-color: #f3f4f6;
        }
        .total {
            text-align: right;
            font-size: 14px;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #6b7280;
        }
        .signature {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="recu">
        <div class="header">
            <h1>REÇU DE PAIEMENT</h1>
            <p>{{ $paiement->etablissement->nom ?? 'Établissement Scolaire' }}</p>
        </div>

        <div class="info">
            <table>
                <tr>
                    <td width="50%"><strong>Référence:</strong> {{ $paiement->reference }}</td>
                    <td width="50%"><strong>Date:</strong> {{ $paiement->date_paiement->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td><strong>Élève:</strong> {{ $paiement->eleve->prenom }} {{ $paiement->eleve->nom }}</td>
                    <td><strong>Classe:</strong> {{ $paiement->eleve->classe->nom ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Parent:</strong> {{ $paiement->eleve->nom_parent ?? 'N/A' }}</td>
                    <td><strong>Téléphone:</strong> {{ $paiement->eleve->telephone_parent ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <div class="details">
            <table>
                <thead>
                    <tr>
                        <th>Désignation</th>
                        <th>Montant</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $paiement->frais->libelle ?? 'Frais de scolarité' }}</td>
                        <td>{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="total">
            <strong>Total payé: {{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</strong>
        </div>

        <div class="signature">
            <div>
                <p>Cachet de l'établissement</p>
                <div style="margin-top: 30px;">__________________</div>
            </div>
            <div>
                <p>Signature du parent</p>
                <div style="margin-top: 30px;">__________________</div>
            </div>
        </div>

        <div class="footer">
            <p>Ce document fait office de reçu et vaut justification de paiement.</p>
            <p>Merci de votre confiance.</p>
        </div>
    </div>

    <div style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #4f46e5; color: white; border: none; border-radius: 5px; cursor: pointer;">
            <i class="fas fa-print"></i> Imprimer
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6b7280; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            Fermer
        </button>
    </div>
</body>
</html>