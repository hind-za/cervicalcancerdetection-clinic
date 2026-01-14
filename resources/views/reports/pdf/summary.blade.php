<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Résumé - {{ $patient->nom_complet }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 14px; line-height: 1.5; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 15px; margin-bottom: 25px; }
        .summary-box { background: #f8f9fa; padding: 20px; margin-bottom: 20px; border-left: 4px solid #007bff; }
        .stats-grid { display: table; width: 100%; margin: 20px 0; }
        .stat-item { display: table-cell; text-align: center; padding: 15px; border: 1px solid #ddd; }
        .stat-number { font-size: 24px; font-weight: bold; color: #007bff; }
        .risk-indicator { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .risk-high { background: #ffebee; border-left: 4px solid #f44336; }
        .risk-medium { background: #fff3e0; border-left: 4px solid #ff9800; }
        .risk-low { background: #e8f5e8; border-left: 4px solid #4caf50; }
        .footer { margin-top: 40px; padding-top: 15px; border-top: 1px solid #ddd; font-size: 12px; color: #666; text-align: center; }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <h1>RÉSUMÉ PATIENT</h1>
        <h2>{{ $patient->nom_complet }}</h2>
        <p>{{ $patient->numero_dossier }} • {{ $patient->age }} ans • {{ $patient->sexe === 'F' ? 'Féminin' : 'Masculin' }}</p>
    </div>

    <!-- Résumé principal -->
    <div class="summary-box">
        <h3>📊 RÉSUMÉ EXÉCUTIF</h3>
        <p>
            <strong>Patient:</strong> {{ $patient->nom_complet }}, {{ $patient->age }} ans<br>
            <strong>Période d'analyse:</strong> {{ $analysesIA->last()?->created_at->format('d/m/Y') ?? 'N/A' }} 
            au {{ $analysesIA->first()?->created_at->format('d/m/Y') ?? 'N/A' }}<br>
            <strong>Total analyses:</strong> {{ $stats['total_analyses'] }} 
            ({{ $stats['analyses_validees'] }} validées)<br>
            <strong>État général:</strong> 
            @if($stats['risque_eleve'] > 0)
                ⚠️ Surveillance requise ({{ $stats['risque_eleve'] }} analyse(s) à risque élevé)
            @elseif($stats['analyses_en_attente'] > 0)
                📋 En cours d'évaluation ({{ $stats['analyses_en_attente'] }} en attente)
            @else
                ✅ Situation stable
            @endif
        </p>
    </div>

    <!-- Statistiques visuelles -->
    <div class="stats-grid">
        <div class="stat-item">
            <div class="stat-number">{{ $stats['total_analyses'] }}</div>
            <div>Total Analyses</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $stats['analyses_validees'] }}</div>
            <div>Validées</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $stats['analyses_en_attente'] }}</div>
            <div>En Attente</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $stats['risque_eleve'] }}</div>
            <div>Risque Élevé</div>
        </div>
    </div>

    <!-- Indicateur de risque -->
    @if($stats['risque_eleve'] > 0)
    <div class="risk-indicator risk-high">
        <strong>⚠️ ATTENTION MÉDICALE REQUISE</strong><br>
        {{ $stats['risque_eleve'] }} analyse(s) présentent un risque élevé nécessitant un suivi médical immédiat.
    </div>
    @elseif($stats['total_analyses'] > 0 && $stats['analyses_validees'] == $stats['total_analyses'])
    <div class="risk-indicator risk-low">
        <strong>✅ SITUATION STABLE</strong><br>
        Toutes les analyses ont été validées. Suivi de routine recommandé.
    </div>
    @else
    <div class="risk-indicator risk-medium">
        <strong>📋 ÉVALUATION EN COURS</strong><br>
        {{ $stats['analyses_en_attente'] }} analyse(s) en attente de validation médicale.
    </div>
    @endif

    <!-- Classes détectées -->
    @if($stats['classes_detectees']->count() > 0)
    <div style="margin: 20px 0;">
        <h3>🔬 TYPES CELLULAIRES DÉTECTÉS</h3>
        <p>{{ $stats['classes_detectees']->implode(' • ') }}</p>
    </div>
    @endif

    <!-- Dernière analyse -->
    @if($analysesIA->first())
    @php $derniereAnalyse = $analysesIA->first(); @endphp
    <div style="margin: 20px 0;">
        <h3>📅 DERNIÈRE ANALYSE</h3>
        <p>
            <strong>Date:</strong> {{ $derniereAnalyse->created_at->format('d/m/Y à H:i') }}<br>
            <strong>Résultat:</strong> {{ $derniereAnalyse->classe_predite }} 
            ({{ $derniereAnalyse->confidence_percent }}% de confiance)<br>
            <strong>Risque:</strong> {{ $derniereAnalyse->niveau_risque }}<br>
            <strong>Statut:</strong> {{ $derniereAnalyse->statut }}
            @if($derniereAnalyse->validateur)
                - Validé par {{ $derniereAnalyse->validateur->name }}
            @endif
        </p>
    </div>
    @endif

    <!-- Actions recommandées -->
    <div style="margin: 20px 0;">
        <h3>📋 ACTIONS RECOMMANDÉES</h3>
        <ul>
            @if($stats['analyses_en_attente'] > 0)
            <li>Valider les {{ $stats['analyses_en_attente'] }} analyse(s) en attente</li>
            @endif
            @if($stats['risque_eleve'] > 0)
            <li><strong>Programmer un suivi médical rapproché</strong></li>
            <li>Informer le patient des résultats</li>
            @endif
            @if($stats['total_analyses'] > 0)
            <li>Planifier le prochain contrôle selon protocole</li>
            @endif
            @if($stats['total_analyses'] == 0)
            <li>Programmer une première analyse</li>
            @endif
        </ul>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>
            Résumé généré le {{ $generated_at->format('d/m/Y à H:i') }} 
            par {{ $generated_by->name }}
        </p>
        <p>Document confidentiel - Usage médical exclusif</p>
    </div>
</body>
</html>