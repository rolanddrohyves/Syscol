{{-- resources/views/etablissement/parametres/rapport-pdf.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport {{ $etablissement->nom }}</title>
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
            border-bottom: 2px solid #4f46e5;
        }
        .logo {
            max-width: 100px;
            max-height: 100px;
            margin-bottom: 10px;
        }
        h1 {
            font-size: 24px;
            color: #1e293b;
            margin: 5px 0;
        }
        h2 {
            font-size: 18px;
            color: #4f46e5;
            margin: 20px 0 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e2e8f0;
        }
        h3 {
            font-size: 16px;
            color: #334155;
            margin: 15px 0 10px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #f8fafc;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            border-left: 4px solid #4f46e5;
        }
        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #0f172a;
        }
        .stat-label {
            font-size: 12px;
            color: #64748b;
            margin-top: 5px;
        }
        .repartition-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .class-stats {
            background: #f8fafc;
            border-radius: 8px;
            padding: 15px;
        }
        .class-item {
            margin-bottom: 10px;
        }
        .class-name {
            font-weight: bold;
            color: #0f172a;
        }
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            margin: 5px 0;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: #4f46e5;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 20px;
        }
        th {
            background: #f1f5f9;
            padding: 10px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            color: #475569;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
        }
        .sexe-stats {
            display: flex;
            justify-content: space-around;
            text-align: center;
            margin: 20px 0;
        }
        .sexe-item {
            padding: 10px;
        }
        .sexe-value {
            font-size: 24px;
            font-weight: bold;
        }
        .sexe-label {
            font-size: 11px;
            color: #64748b;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
        }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        .badge-green { background: #dcfce7; color: #166534; }
        .info-ets {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #64748b;
            margin-bottom: 20px;
        }
        .page-break {
            page-break-after: always;
        }
        .text-blue { color: #2563eb; }
        .text-pink { color: #db2777; }
        .text-green { color: #10b981; }
        .text-purple { color: #8b5cf6; }
        .bg-blue-light { background: #dbeafe; }
        .bg-pink-light { background: #fce7f3; }
        .bg-green-light { background: #d1fae5; }
        .bg-purple-light { background: #ede9fe; }
    </style>
</head>
<body>
    <!-- En-tête du rapport -->
    <div class="header">
        @if($etablissement->logo)
            <img src="{{ public_path('storage/' . $etablissement->logo) }}" class="logo" alt="Logo">
        @endif
        <h1>{{ $etablissement->nom }}</h1>
        <p style="color: #4f46e5; font-size: 14px;">{{ $etablissement->type ?? 'Établissement scolaire' }}</p>
        <div class="info-ets">
            <span>{{ $etablissement->adresse ?? '' }} - {{ $etablissement->ville ?? '' }}</span>
            <span>Tél: {{ $etablissement->telephone ?? '' }}</span>
            <span>Email: {{ $etablissement->email ?? '' }}</span>
        </div>
        <p style="color: #64748b;">Rapport généré le {{ now()->format('d/m/Y') }} à {{ now()->format('H:i') }}</p>
    </div>

    <!-- Informations sur l'année scolaire -->
    <div style="background: #f8fafc; padding: 10px; border-radius: 8px; margin-bottom: 20px;">
        <p style="margin: 0;"><strong>Année scolaire :</strong> {{ $stats['annee_en_cours']->libelle ?? 'Non définie' }}</p>
    </div>

    <!-- Statistiques générales -->
    <h2>Statistiques générales</h2>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_classes'] }}</div>
            <div class="stat-label">Classes</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_enseignants'] }}</div>
            <div class="stat-label">Enseignants</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_eleves'] }}</div>
            <div class="stat-label">Élèves</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">
                @if($stats['total_enseignants'] > 0)
                    {{ round($stats['total_eleves'] / $stats['total_enseignants'], 1) }}
                @else
                    0
                @endif
            </div>
            <div class="stat-label">Élèves/enseignant</div>
        </div>
    </div>

    <!-- Répartition par sexe et par classe -->
    <div class="repartition-section">
        <!-- Répartition par sexe -->
        <div class="class-stats">
            <h3 style="margin-top: 0;">Répartition par sexe</h3>
            <div class="sexe-stats">
                <div class="sexe-item">
                    <div class="sexe-value text-blue">{{ $stats['garcons'] }}</div>
                    <div class="sexe-label">Garçons</div>
                    <div style="font-size: 11px;">{{ $stats['pourcentage_garcons'] }}%</div>
                </div>
                <div class="sexe-item">
                    <div class="sexe-value text-pink">{{ $stats['filles'] }}</div>
                    <div class="sexe-label">Filles</div>
                    <div style="font-size: 11px;">{{ $stats['pourcentage_filles'] }}%</div>
                </div>
            </div>
            <div style="margin-top: 15px;">
                <div style="margin-bottom: 8px;">
                    <span style="font-size: 11px;">Garçons</span>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $stats['pourcentage_garcons'] }}%; background: #2563eb;"></div>
                    </div>
                </div>
                <div>
                    <span style="font-size: 11px;">Filles</span>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $stats['pourcentage_filles'] }}%; background: #db2777;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Répartition par niveau -->
        <div class="class-stats">
            <h3 style="margin-top: 0;">Répartition par niveau</h3>
            @php
                $classesParNiveau = $etablissement->classes->groupBy('niveau');
            @endphp
            @foreach($classesParNiveau as $niveau => $classes)
                @php
                    $totalNiveau = $classes->sum(function($c) { return $c->eleves->count(); });
                    $pourcentageNiveau = $stats['total_eleves'] > 0 ? round(($totalNiveau / $stats['total_eleves']) * 100, 1) : 0;
                    $couleurs = ['#2563eb', '#10b981', '#8b5cf6', '#f59e0b', '#ef4444'];
                    $couleur = $couleurs[$loop->index % count($couleurs)];
                @endphp
                <div style="margin-bottom: 10px;">
                    <div style="display: flex; justify-content: space-between; font-size: 11px;">
                        <span style="font-weight: bold;">{{ $niveau }}</span>
                        <span>{{ $totalNiveau }} élèves ({{ $pourcentageNiveau }}%)</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $pourcentageNiveau }}%; background: {{ $couleur }};"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Détail par classe -->
    <h2>Détail par classe</h2>
    <table>
        <thead>
            <tr>
                <th>Classe</th>
                <th>Niveau</th>
                <th>Effectif</th>
                <th>Garçons</th>
                <th>Filles</th>
                <th>Professeur principal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stats['par_classe'] as $classe)
                <tr>
                    <td><strong>{{ $classe['nom'] }}</strong></td>
                    <td>{{ $classe['niveau'] }}</td>
                    <td>{{ $classe['effectif'] }}</td>
                    <td>{{ $classe['garcons'] }}</td>
                    <td>{{ $classe['filles'] }}</td>
                    <td>{{ $classe['professeur'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Saut de page pour la suite -->
    <div class="page-break"></div>

    <!-- Deuxième page : Liste des enseignants -->
    <h2>Liste des enseignants</h2>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Spécialité</th>
                <th>Classes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($etablissement->enseignants as $enseignant)
                <tr>
                    <td>{{ $enseignant->user->name ?? 'N/A' }}</td>
                    <td>{{ $enseignant->user->email ?? 'N/A' }}</td>
                    <td>{{ $enseignant->user->telephone ?? 'N/A' }}</td>
                    <td>{{ $enseignant->specialite ?? 'N/A' }}</td>
                    <td>
                        @if($enseignant->classes->count() > 0)
                            {{ $enseignant->classes->pluck('nom')->implode(', ') }}
                        @else
                            <span style="color: #94a3b8;">Aucune</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #94a3b8;">Aucun enseignant</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Liste des 10 meilleurs élèves (si disponibles) -->
    @if(isset($meilleursEleves) && count($meilleursEleves) > 0)
        <h2>Top 10 des élèves</h2>
        <table>
            <thead>
                <tr>
                    <th>Rang</th>
                    <th>Nom</th>
                    <th>Classe</th>
                    <th>Moyenne</th>
                </tr>
            </thead>
            <tbody>
                @foreach($meilleursEleves as $index => $eleve)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $eleve['nom'] }}</td>
                        <td>{{ $eleve['classe'] }}</td>
                        <td><strong>{{ $eleve['moyenne'] }}/20</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Pied de page -->
    <div class="footer">
        <p>Rapport généré par SYSCOL · {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>{{ $etablissement->adresse }} · Tél: {{ $etablissement->telephone ?? 'Non renseigné' }} · Email: {{ $etablissement->email ?? 'Non renseigné' }}</p>
        <p>Document officiel - {{ config('app.name') }} v{{ config('app.version', '1.0') }}</p>
    </div>
</body>
</html>