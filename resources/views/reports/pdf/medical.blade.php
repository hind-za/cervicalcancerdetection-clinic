<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport Médical - {{ $patient->nom_complet }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; line-height: 1.4; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .section { margin-bottom: 25px; }
        .section h3 { color: #333; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .risk-high { background-color: #ffebee; }
        .risk-medium { background-color: #fff3e0; }
        .risk-low { background-color: #e8f5e8; }
        .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <h1>RAPPORT MÉDICAL</h1>
        <h2>{{ $patient->nom_complet }}</h2>
        <p>Dossier N° {{ $patient->numero_dossier }} - {{ $patient->age }} ans</p>
    </div>

    <!-- Résumé médical -->
    <div class="section">
        <h3>RÉSUMÉ MÉDICAL</h3>
        <table>
            <tr>
                <td><strong>Total analyses:</strong></td>
                <td>{{ $stats['total_analyses'] }}</td>
            </tr>
            <tr>
                <td><strong>Analyses validées:</strong></td>
                <td>{{ $stats['analyses_validees'] }}</td>
            </tr>
            <tr>
                <td><strong>Analyses à risque élevé:</strong></td>
                <td>{{ $stats['risque_eleve'] }}</td>
            </tr>
            <tr>
                <td><strong>Dernière analyse:</strong></td>
                <td>{{ $stats['derniere_analyse']?->format('d/m/Y') ?? 'Aucune' }}</td>
            </tr>
        </table>
    </div>

    <!-- Antécédents -->
    @if($patient->antecedents_medicaux)
    <div class="section">
        <h3>ANTÉCÉDENTS MÉDICAUX</h3>
        <p>{{ $patient->antecedents_medicaux }}</p>
    </div>
    @endif

    <!-- Analyses à risque élevé -->
    @php
        $analysesRisqueEleve = $analysesIA->where('niveau_risque', 'Élevé');
    @endphp
    @if($analysesRisqueEleve->count() > 0)
    <div class="section">
        <h3>⚠️ ANALYSES À RISQUE ÉLEVÉ ({{ $analysesRisqueEleve->count() }})</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Classe</th>
                    <th>Probabilité</th>
                    <th>Statut</th>
                    <th>Recommandations</th>
                </tr>
            </thead>
            <tbody>
                @foreach($analysesRisqueEleve as $analyse)
                <tr class="risk-high">
                    <td>{{ $analyse->created_at->format('d/m/Y') }}</td>
                    <td>{{ $analyse->classe_predite }}</td>
                    <td>{{ $analyse->confidence_percent }}%</td>
                    <td>{{ $analyse->statut }}</td>
                    <td>{{ $analyse->recommandations_finales ?? 'Suivi médical recommandé' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Évolution récente -->
    <div class="section">
        <h3>ÉVOLUTION RÉCENTE (5 dernières analyses)</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Classe Prédite</th>
                    <th>Risque</th>
                    <th>Statut</th>
                    <th>Commentaires Médecin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($analysesIA->take(5) as $analyse)
                <tr class="{{ $analyse->niveau_risque === 'Élevé' ? 'risk-high' : ($analyse->niveau_risque === 'Modéré' ? 'risk-medium' : 'risk-low') }}">
                    <td>{{ $analyse->created_at->format('d/m/Y') }}</td>
                    <td>{{ $analyse->classe_predite }}</td>
                    <td>{{ $analyse->niveau_risque }}</td>
                    <td>{{ $analyse->statut }}</td>
                    <td>{{ $analyse->commentaires_medecin ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Recommandations générales -->
    <div class="section">
        <h3>RECOMMANDATIONS GÉNÉRALES</h3>
        @if($stats['risque_eleve'] > 0)
        <p><strong>⚠️ ATTENTION:</strong> {{ $stats['risque_eleve'] }} analyse(s) présentent un risque élevé. Suivi médical rapproché recommandé.</p>
        @endif
        
        @if($stats['analyses_en_attente'] > 0)
        <p><strong>📋 À FAIRE:</strong> {{ $stats['analyses_en_attente'] }} analyse(s) en attente de validation médicale.</p>
        @endif

        @if($stats['classes_detectees']->count() > 1)
        <p><strong>🔍 OBSERVATION:</strong> Plusieurs types cellulaires détectés : {{ $stats['classes_detectees']->implode(', ') }}.</p>
        @endif

        <p><strong>📅 SUIVI:</strong> Contrôle recommandé selon protocole médical en vigueur.</p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>
            Rapport médical généré le {{ $generated_at->format('d/m/Y à H:i') }} 
            par Dr. {{ $generated_by->name }}
        </p>
        <p><strong>Document médical confidentiel - Ne pas diffuser</strong></p>
    </div>
</body>
</html>