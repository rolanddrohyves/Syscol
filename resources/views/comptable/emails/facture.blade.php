{{-- resources/views/emails/facture.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture {{ $facture->numero }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background: #8b5cf6;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
            <p>Facture {{ $facture->numero }}</p>
        </div>
        
        <div class="content">
            <p>Bonjour,</p>
            
            <p>Veuillez trouver ci-joint la facture {{ $facture->numero }} pour {{ $facture->eleve->prenom }} {{ $facture->eleve->nom }} (classe {{ $facture->eleve->classe->nom }}).</p>
            
            <p><strong>Récapitulatif :</strong></p>
            <ul>
                <li>Date d'émission : {{ $facture->date_emission->format('d/m/Y') }}</li>
                <li>Date d'échéance : {{ $facture->date_echeance->format('d/m/Y') }}</li>
                <li>Montant TTC : {{ number_format($facture->montant_ttc, 0, ',', ' ') }} FCFA</li>
            </ul>
            
            <p>Vous pouvez télécharger la facture en pièce jointe ou vous connecter à votre espace parent pour plus d'informations.</p>
            
            <a href="{{ route('login') }}" class="button">Se connecter à mon espace</a>
            
            <p>Cordialement,<br>L'équipe {{ config('app.name') }}</p>
        </div>
        
        <div class="footer">
            <p>Ce message est automatique, merci de ne pas y répondre.</p>
            <p>{{ config('app.name') }} - Gestion Scolaire</p>
        </div>
    </div>
</body>
</html>