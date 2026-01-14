<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport d'Analyse - {{ $patient->nom_complet }}</title>
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 12px; 
            line-height: 1.4; 
            margin: 20px;
        }
        .header { 
            text-align: center; 
            border-bottom: 2px solid #333; 
            padding-bottom: 15px; 
            margin-bottom: 25px; 
        }
        .patient-info { 
            background: #f8f9fa; 
            padding: 15px; 
            margin-bottom: 20px; 
            border-radius: 5px; 
        }
        .section { 
            margin-bottom: 25px; 
        }
        .section h3 { 
            color: #333; 
            border-bottom: 1px solid #ddd; 
            padding-bottom: 5px; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 15px; 
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 8px; 
            text-align: left; 
        }
        th { 
            background-color: #f2f2f2; 
            font-weight: bold; 
        }
        .badge { 
            padding: 3px 8px; 
            border-radius: 3px; 
            color: white; 
            font-size: 10px; 
        }
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-danger { background-color: #dc3545; }
        .result-box { 
            background: #e8f4fd; 
            padding: 15px; 
            border-radius: 5px; 
            margin: 15px 0; 
            border-left: 4px solid #2196f3;
        }
        .footer { 
            margin-top: 30px; 
            padding-top: 15px; 
            border-top: 1px solid #ddd; 
            font-size: 10px; 
            color: #666; 
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <h1>CENTRE MÉDICAL CERVICAL CLINIC</h1>
        <h2>RAPPORT D'ANALYSE DÉTAILLÉ</h2>
        <h3>{{ $patient->nom_complet }}</h3>
        <p>Analyse effectuée le {{ $generated_at->format('d/m/Y à H:i') }}</p>
    </div>

    <!-- Informations Patient -->
    <div class="patient-info">
        <h3>INFORMATIONS PATIENT</h3>
        <table>
            <tr>
                <td><strong>Nom complet:</strong></td>
                <td>{{ $patient->nom_complet }}</td>
                <td><strong>N° Dossier:</strong></td>
                <td>{{ $patient->numero_dossier }}</td>
            </tr>
            <tr>
                <td><strong>Date de naissance:</strong></td>
                <td>{{ $patient->date_naissance->format('d/m/Y') }}</td>
                <td><strong>Âge:</strong></td>
                <td>{{ $patient->age }} ans</td>
            </tr>
            <tr>
                <td><strong>Sexe:</strong></td>
                <td>{{ $patient->sexe === 'F' ? 'Féminin' : 'Masculin' }}</td>
                <td><strong>Contact:</strong></td>
                <td>{{ $patient->telephone ?? $patient->email ?? 'Non renseigné' }}</td>
            </tr>
        </table>
    </div>

    <!-- Résultats de l'Analyse -->
    <div class="section">
        <h3>RÉSULTATS DE L'ANALYSE IA</h3>
        
        @php
            $confidence = ($analysis['probabilite'] ?? 0.5) * 100;
            $riskBadge = match($analysis['niveau_risque'] ?? 'Modéré') {
                'Élevé' => 'badge-danger',
                'Modéré' => 'badge-warning',
                'Faible' => 'badge-success',
                default => 'badge-warning'
            };
        @endphp
        
        <div class="result-box">
            <h4>RÉSULTAT PRINCIPAL</h4>
            <table>
                <tr>
                    <td><strong>Classe détectée:</strong></td>
                    <td><strong>{{ $analysis['classe_predite'] ?? 'Non définie' }}</strong></td>
                </tr>
                <tr>
                    <td><strong>Niveau de confiance:</strong></td>
                    <td><strong>{{ round($confidence, 1) }}%</strong></td>
                </tr>
                <tr>
                    <td><strong>Niveau de risque:</strong></td>
                    <td><span class="badge {{ $riskBadge }}">{{ $analysis['niveau_risque'] ?? 'Modéré' }}</span></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Interprétation Médicale -->
    @if(isset($analysis['interpretation']))
    <div class="section">
        <h3>INTERPRÉTATION MÉDICALE</h3>
        <p><strong>Analyse:</strong> {{ $analysis['interpretation'] }}</p>
    </div>
    @endif

    <!-- Détails des Probabilités -->
    @if(isset($analysis['toutes_probabilites']) && is_array($analysis['toutes_probabilites']))
    <div class="section">
        <h3>DÉTAIL DES PROBABILITÉS</h3>
        <table>
            <thead>
                <tr>
                    <th>Type Cellulaire</th>
                    <th>Probabilité</th>
                    <th>Pourcentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($analysis['toutes_probabilites'] as $classe => $prob)
                @php
                    $percentage = round($prob * 100, 1);
                @endphp
                <tr>
                    <td><strong>{{ $classe }}</strong></td>
                    <td>{{ round($prob, 4) }}</td>
                    <td><strong>{{ $percentage }}%</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Recommandations -->
    @if(isset($analysis['recommandations']))
    <div class="section">
        <h3>RECOMMANDATIONS</h3>
        @if(is_array($analysis['recommandations']))
            <ul>
                @foreach($analysis['recommandations'] as $recommandation)
                <li>{{ $recommandation }}</li>
                @endforeach
            </ul>
        @else
            <p>{{ $analysis['recommandations'] }}</p>
        @endif
    </div>
    @endif

    <!-- Antécédents du Patient -->
    @if($patient->antecedents_medicaux)
    <div class="section">
        <h3>ANTÉCÉDENTS MÉDICAUX</h3>
        <p><strong>Historique médical:</strong> {{ $patient->antecedents_medicaux }}</p>
    </div>
    @endif

    <!-- Métadonnées Techniques -->
    <div class="section">
        <h3>MÉTADONNÉES TECHNIQUES</h3>
        <table>
            <tr>
                <td><strong>Date d'analyse:</strong></td>
                <td>{{ $generated_at->format('d/m/Y à H:i:s') }}</td>
                <td><strong>Analyste:</strong></td>
                <td>{{ $generated_by->name }}</td>
            </tr>
            <tr>
                <td><strong>Temps d'analyse:</strong></td>
                <td>{{ $analysis['temps_analyse'] ?? '2.5' }} secondes</td>
                <td><strong>Version du modèle:</strong></td>
                <td>CervicalCare AI v2.1</td>
            </tr>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>
            <strong>Rapport généré automatiquement le {{ $generated_at->format('d/m/Y à H:i') }}</strong><br>
            Par {{ $generated_by->name }} - {{ $generated_by->role ?? 'Analyste' }}<br>
            Centre Médical Cervical Clinic - Système de Détection Automatisée du Cancer Cervical
        </p>
        <p>
            <strong>DOCUMENT MÉDICAL CONFIDENTIEL</strong><br>
            Ce document contient des informations médicales confidentielles.
        </p>
    </div>
</body>
</html>