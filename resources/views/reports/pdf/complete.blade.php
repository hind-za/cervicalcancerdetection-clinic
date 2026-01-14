<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport Complet - {{ $patient->nom_complet }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; line-height: 1.4; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .patient-info { background: #f8f9fa; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .section { margin-bottom: 25px; }
        .section h3 { color: #333; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .badge { padding: 3px 8px; border-radius: 3px; color: white; font-size: 10px; }
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-danger { background-color: #dc3545; }
        .badge-secondary { background-color: #6c757d; }
        .stats { display: flex; justify-content: space-around; margin: 20px 0; }
        .stat-box { text-align: center; padding: 10px; border: 1px solid #ddd; }
        .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <h1>RAPPORT MÉDICAL COMPLET</h1>
        <h2>{{ $patient->nom_complet }}</h2>
        <p>Dossier N° {{ $patient->numero_dossier }}</p>
    </div>

    <!-- Informations Patient -->
    <div class="patient-info">
        <h3>INFORMATIONS PATIENT</h3>
        <table>
            <tr>
                <td><strong>Nom complet:</strong></td>
                <td>{{ $patient->nom_complet }}</td>
                <td><strong>Date de naissance:</strong></td>
                <td>{{ $patient->date_naissance->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td><strong>Âge:</strong></td>
                <td>{{ $patient->age }} ans</td>
                <td><strong>Sexe:</strong></td>
                <td>{{ $patient->sexe === 'F' ? 'Féminin' : 'Masculin' }}</td>
            </tr>
            @if($patient->telephone || $patient->email)
            <tr>
                <td><strong>Téléphone:</strong></td>
                <td>{{ $patient->telephone ?? 'Non renseigné' }}</td>
                <td><strong>Email:</strong></td>
                <td>{{ $patient->email ?? 'Non renseigné' }}</td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Statistiques -->
    <div class="section">
        <h3>STATISTIQUES GÉNÉRALES</h3>
        <div class="stats">
            <div class="stat-box">
                <strong>{{ $stats['total_analyses'] }}</strong><br>
                Total Analyses
            </div>
            <div class="stat-box">
                <strong>{{ $stats['analyses_validees'] }}</strong><br>
                Analyses Validées
            </div>
            <div class="stat-box">
                <strong>{{ $stats['analyses_en_attente'] }}</strong><br>
                En Attente
            </div>
            <div class="stat-box">
                <strong>{{ $stats['risque_eleve'] }}</strong><br>
                Risque Élevé
            </div>
        </div>
    </div>

    <!-- Antécédents -->
    @if($patient->antecedents_medicaux)
    <div class="section">
        <h3>ANTÉCÉDENTS MÉDICAUX</h3>
        <p>{{ $patient->antecedents_medicaux }}</p>
    </div>
    @endif

    <!-- Analyses IA -->
    @if($analysesIA->count() > 0)
    <div class="section">
        <h3>ANALYSES IA ({{ $analysesIA->count() }} analyses)</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Classe Prédite</th>
                    <th>Probabilité</th>
                    <th>Risque</th>
                    <th>Statut</th>
                    <th>Validé par</th>
                </tr>
            </thead>
            <tbody>
                @foreach($analysesIA as $analyse)
                <tr>
                    <td>{{ $analyse->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $analyse->classe_predite }}</td>
                    <td>{{ $analyse->confidence_percent }}%</td>
                    <td>{{ $analyse->niveau_risque }}</td>
                    <td>{{ $analyse->statut }}</td>
                    <td>{{ $analyse->validateur?->name ?? 'Non validé' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Détails des analyses récentes -->
    @foreach($analysesIA->take(3) as $analyse)
    <div class="section">
        <h3>DÉTAIL ANALYSE - {{ $analyse->created_at->format('d/m/Y') }}</h3>
        <table>
            <tr>
                <td><strong>Classe prédite:</strong></td>
                <td>{{ $analyse->classe_predite }}</td>
            </tr>
            <tr>
                <td><strong>Probabilité:</strong></td>
                <td>{{ $analyse->confidence_percent }}%</td>
            </tr>
            <tr>
                <td><strong>Niveau de risque:</strong></td>
                <td>{{ $analyse->niveau_risque }}</td>
            </tr>
            <tr>
                <td><strong>Interprétation:</strong></td>
                <td>{{ $analyse->interpretation }}</td>
            </tr>
            @if($analyse->commentaires_medecin)
            <tr>
                <td><strong>Commentaires médecin:</strong></td>
                <td>{{ $analyse->commentaires_medecin }}</td>
            </tr>
            @endif
            @if($analyse->recommandations_finales)
            <tr>
                <td><strong>Recommandations:</strong></td>
                <td>{{ $analyse->recommandations_finales }}</td>
            </tr>
            @endif
        </table>
    </div>
    @endforeach
    @endif

    <!-- Notes -->
    @if($patient->notes)
    <div class="section">
        <h3>NOTES</h3>
        <p>{{ $patient->notes }}</p>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>
            Rapport généré le {{ $generated_at->format('d/m/Y à H:i') }} 
            par {{ $generated_by->name }}
        </p>
        <p>Document confidentiel - Usage médical uniquement</p>
    </div>
</body>
</html>