{{-- resources/views/admin/statistiques/pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Statistiques SYSCOL</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #4f46e5;
        }
        .header h1 {
            color: #4f46e5;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #666;
            margin: 5px 0 0;
            font-size: 11px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section h2 {
            color: #4f46e5;
            font-size: 18px;
            border-left: 4px solid #4f46e5;
            padding-left: 10px;
            margin-bottom: 15px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-card {
            border: 1px solid #e5e7eb;
            padding: 15px;
            border-radius: 8px;
            background-color: #f9fafb;
        }
        .stat-card h3 {
            margin: 0 0 8px 0;
            color: #4b5563;
            font-size: 14px;
            font-weight: normal;
        }
        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #4f46e5;
            margin-bottom: 5px;
        }
        .stat-detail {
            color: #6b7280;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th {
            background-color: #4f46e5;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 12px;
        }
        td {
            padding: 8px 10px;
            border: 1px solid #e5e7eb;
            font-size: 11px;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #9ca3af;
            font-size: 10px;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SYSCOL - Statistiques globales</h1>
        <p>Généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    <!-- Vue d'ensemble - Données réelles -->
    <div class="section">
        <h2>📊 Vue d'ensemble</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Établissements</h3>
                <div class="stat-value">{{ $stats['general']['etablissements']['total'] }}</div>
                <div class="stat-detail">
                    {{ $stats['general']['etablissements']['actifs'] }} actifs
                </div>
            </div>
            <div class="stat-card">
                <h3>Utilisateurs</h3>
                <div class="stat-value">{{ $stats['general']['utilisateurs']['total'] }}</div>
                <div class="stat-detail">
                    {{ $stats['general']['utilisateurs']['actifs'] }} actifs
                </div>
            </div>
            <div class="stat-card">
                <h3>Élèves</h3>
                <div class="stat-value">{{ $stats['general']['eleves']['total'] }}</div>
                <div class="stat-detail">
                    {{ $stats['general']['eleves']['actifs'] }} actifs
                </div>
            </div>
            <div class="stat-card">
                <h3>Classes</h3>
                <div class="stat-value">{{ $stats['general']['classes']['total'] }}</div>
                <div class="stat-detail">
                    {{ $stats['general']['classes']['avec_prof_principal'] }} avec professeur principal
                </div>
            </div>
            <div class="stat-card">
                <h3>Enseignants</h3>
                <div class="stat-value">{{ $stats['general']['enseignants']['total'] }}</div>
            </div>
            <div class="stat-card">
                <h3>Matières</h3>
                <div class="stat-value">{{ $stats['general']['matieres']['total'] }}</div>
            </div>
        </div>
    </div>

    <!-- Répartition par type d'établissement - Données réelles -->
    @if(isset($stats['repartition']['etablissements']) && $stats['repartition']['etablissements']->count() > 0)
    <div class="section">
        <h2>🏫 Types d'établissements</h2>
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Nombre</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['repartition']['etablissements'] as $type)
                <tr>
                    <td>{{ $type->type }}</td>
                    <td>{{ $type->total }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Répartition par niveau scolaire - Données réelles -->
    @if(isset($stats['repartition']['eleves']) && count($stats['repartition']['eleves']) > 0)
    <div class="section">
        <h2>📚 Répartition par niveau</h2>
        <table>
            <thead>
                <tr>
                    <th>Niveau</th>
                    <th>Classes</th>
                    <th>Élèves</th>
                    <th>Capacité</th>
                    <th>Taux d'occupation</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['repartition']['eleves'] as $niveau)
                <tr>
                    <td>{{ $niveau['niveau'] }}</td>
                    <td>{{ $niveau['classes'] }}</td>
                    <td>{{ $niveau['eleves'] }}</td>
                    <td>{{ $niveau['capacite'] }}</td>
                    <td>{{ $niveau['taux_occupation'] }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Répartition par rôle - Données réelles -->
    @if(isset($stats['repartition']['utilisateurs']) && count($stats['repartition']['utilisateurs']) > 0)
    <div class="section">
        <h2>👥 Répartition par rôle</h2>
        <table>
            <thead>
                <tr>
                    <th>Rôle</th>
                    <th>Nombre d'utilisateurs</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['repartition']['utilisateurs'] as $role)
                <tr>
                    <td>{{ $role['role'] }}</td>
                    <td>{{ $role['total'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Top établissements - Données réelles -->
    @if(isset($stats['top']['etablissements']) && count($stats['top']['etablissements']) > 0)
    <div class="section">
        <h2>🏆 Top 5 des établissements (plus d'élèves)</h2>
        <table>
            <thead>
                <tr>
                    <th>Établissement</th>
                    <th>Nombre d'élèves</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['top']['etablissements'] as $etab)
                <tr>
                    <td>{{ $etab['nom'] }}</td>
                    <td><strong>{{ $etab['total'] }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Top classes - Données réelles -->
    @if(isset($stats['top']['classes']) && count($stats['top']['classes']) > 0)
    <div class="section">
        <h2>🏆 Top 5 des classes (plus chargées)</h2>
        <table>
            <thead>
                <tr>
                    <th>Classe</th>
                    <th>Effectif</th>
                    <th>Capacité</th>
                    <th>Taux</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['top']['classes'] as $classe)
                @php
                    $taux = $classe['capacite'] > 0 ? round(($classe['total'] / $classe['capacite']) * 100, 1) : 0;
                @endphp
                <tr>
                    <td>{{ $classe['nom'] }}</td>
                    <td><strong>{{ $classe['total'] }}</strong></td>
                    <td>{{ $classe['capacite'] }}</td>
                    <td>{{ $taux }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Activité récente - Données réelles -->
    @if(isset($stats['activite']))
    <div class="section">
        <h2>📱 Activité récente</h2>
        <table>
            <thead>
                <tr>
                    <th>Indicateur</th>
                    <th>Valeur</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Connexions (7 derniers jours)</td>
                    <td>{{ $stats['activite']['connexions_7j'] }}</td>
                </tr>
                <tr>
                    <td>Utilisateurs actifs aujourd'hui</td>
                    <td>{{ $stats['activite']['actifs_aujourdhui'] }}</td>
                </tr>
                <tr>
                    <td>Taux d'activité</td>
                    <td>{{ $stats['activite']['taux_activite'] }}%</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    <!-- Indicateurs de performance - Données réelles -->
    <div class="section">
        <h2>📈 Indicateurs de performance</h2>
        <table>
            <thead>
                <tr>
                    <th>Indicateur</th>
                    <th>Valeur</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Taux de remplissage global</td>
                    <td><strong>{{ $stats['performance']['taux_remplissage'] }}%</strong></td>
                </tr>
                <tr>
                    <td>Ratio élèves/enseignant</td>
                    <td><strong>{{ $stats['performance']['ratio_eleves_enseignant'] }}</strong></td>
                </tr>
                <tr>
                    <td>Moyenne élèves par classe</td>
                    <td><strong>{{ $stats['performance']['moyenne_eleves_par_classe'] }}</strong></td>
                </tr>
                <tr>
                    <td>Moyenne classes par établissement</td>
                    <td><strong>{{ $stats['performance']['moyenne_classes_par_etablissement'] }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Évolution mensuelle - Données réelles (optionnel) -->
    @if(isset($stats['evolution']))
    <div class="section">
        <h2>📅 Évolution des inscriptions (12 derniers mois)</h2>
        <table>
            <thead>
                <tr>
                    <th>Mois</th>
                    <th>Élèves</th>
                    <th>Utilisateurs</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['evolution']['mois'] as $index => $mois)
                <tr>
                    <td>{{ $mois }}</td>
                    <td>{{ $stats['evolution']['eleves'][$index] }}</td>
                    <td>{{ $stats['evolution']['utilisateurs'][$index] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Pied de page -->
    <div class="footer">
        SYSCOL - Système de Gestion Scolaire © {{ date('Y') }} - Document généré automatiquement le {{ now()->format('d/m/Y à H:i:s') }}
    </div>
</body>
</html>