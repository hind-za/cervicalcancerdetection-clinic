@extends('layouts.app')

@section('title', 'Détail de l\'Analyse')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-microscope text-primary me-2"></i>
                        Détail de l'Analyse
                    </h1>
                    <p class="text-muted mb-0">Analyse effectuée le {{ $analyse->created_at->format('d/m/Y à H:i') }}</p>
                </div>
                <div class="text-end">
                    <a href="{{ route('patient.analyses') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i>Retour aux Analyses
                    </a>
                    @if($analyse->statut === 'Validé')
                    <a href="{{ route('patient.analyse.download', $analyse) }}" class="btn btn-success">
                        <i class="fas fa-download me-1"></i>Télécharger PDF
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations Principales -->
        <div class="col-lg-8 mb-4">
            <!-- Résultat Principal -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        Résultat de l'Analyse IA
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $riskClass = match($analyse->niveau_risque) {
                            'Élevé' => 'danger',
                            'Modéré' => 'warning',
                            'Faible' => 'success',
                            default => 'secondary'
                        };
                        $confidence = round($analyse->probabilite * 100, 1);
                    @endphp
                    
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="text-center mb-3">
                                <div class="display-6 fw-bold text-primary mb-2">{{ $analyse->classe_predite }}</div>
                                <div class="progress mb-2" style="height: 20px;">
                                    <div class="progress-bar bg-{{ $riskClass }}" 
                                         style="width: {{ $confidence }}%">
                                        {{ $confidence }}% de confiance
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center">
                                <h6 class="text-muted mb-2">Niveau de Risque</h6>
                                <span class="badge bg-{{ $riskClass }} fs-5 p-3">
                                    @php
                                        $riskIcon = match($analyse->niveau_risque) {
                                            'Élevé' => 'fas fa-exclamation-triangle',
                                            'Modéré' => 'fas fa-exclamation-circle',
                                            'Faible' => 'fas fa-check-circle',
                                            default => 'fas fa-question-circle'
                                        };
                                    @endphp
                                    <i class="{{ $riskIcon }} me-2"></i>
                                    {{ $analyse->niveau_risque }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Interprétation Médicale -->
            @if($analyse->interpretation)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-stethoscope me-2"></i>
                        Interprétation Médicale
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $analyse->interpretation }}</p>
                </div>
            </div>
            @endif

            <!-- Détail des Probabilités -->
            @if($analyse->toutes_probabilites)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Détail des Probabilités
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $probabilites = json_decode($analyse->toutes_probabilites, true) ?? [];
                    @endphp
                    
                    @if(!empty($probabilites))
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Type Cellulaire</th>
                                    <th>Probabilité</th>
                                    <th>Pourcentage</th>
                                    <th>Visualisation</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($probabilites as $classe => $prob)
                                @php
                                    $percentage = round($prob * 100, 1);
                                    $isHighest = $classe === $analyse->classe_predite;
                                @endphp
                                <tr class="{{ $isHighest ? 'table-primary' : '' }}">
                                    <td>
                                        <strong>{{ $classe }}</strong>
                                        @if($isHighest)
                                        <span class="badge bg-primary ms-2">Résultat</span>
                                        @endif
                                    </td>
                                    <td>{{ round($prob, 4) }}</td>
                                    <td><strong>{{ $percentage }}%</strong></td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar {{ $isHighest ? 'bg-primary' : 'bg-secondary' }}" 
                                                 style="width: {{ max(5, $percentage) }}%">
                                                {{ $percentage }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted mb-0">Détails des probabilités non disponibles.</p>
                    @endif
                </div>
            </div>
            @endif

            <!-- Recommandations -->
            @if($analyse->recommandations)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        Recommandations Médicales
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $recommandations = json_decode($analyse->recommandations, true) ?? [];
                    @endphp
                    
                    @if(!empty($recommandations) && is_array($recommandations))
                    <ul class="list-unstyled mb-0">
                        @foreach($recommandations as $recommandation)
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            {{ $recommandation }}
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <p class="mb-0">{{ $analyse->recommandations }}</p>
                    @endif
                </div>
            </div>
            @endif

            <!-- Commentaires du Médecin -->
            @if($analyse->commentaires_medecin)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">
                        <i class="fas fa-user-md me-2"></i>
                        Commentaires du Médecin
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $analyse->commentaires_medecin }}</p>
                    @if($analyse->validateur)
                    <hr>
                    <small class="text-muted">
                        <i class="fas fa-user me-1"></i>
                        Par {{ $analyse->validateur->name }} le {{ $analyse->updated_at->format('d/m/Y à H:i') }}
                    </small>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Informations Complémentaires -->
        <div class="col-lg-4">
            <!-- Statut de l'Analyse -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Statut de l'Analyse
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $statusClass = match($analyse->statut) {
                            'Validé' => 'success',
                            'En attente' => 'warning',
                            'À revoir' => 'danger',
                            default => 'secondary'
                        };
                        $statusIcon = match($analyse->statut) {
                            'Validé' => 'fas fa-check-circle',
                            'En attente' => 'fas fa-clock',
                            'À revoir' => 'fas fa-exclamation-triangle',
                            default => 'fas fa-question-circle'
                        };
                    @endphp
                    
                    <div class="text-center mb-3">
                        <span class="badge bg-{{ $statusClass }} fs-6 p-3">
                            <i class="{{ $statusIcon }} me-2"></i>
                            {{ $analyse->statut }}
                        </span>
                    </div>
                    
                    @if($analyse->statut === 'Validé')
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Analyse validée</strong><br>
                        Cette analyse a été validée par un médecin qualifié.
                    </div>
                    @elseif($analyse->statut === 'En attente')
                    <div class="alert alert-warning">
                        <i class="fas fa-clock me-2"></i>
                        <strong>En attente de validation</strong><br>
                        Cette analyse est en cours de révision par un médecin.
                    </div>
                    @elseif($analyse->statut === 'À revoir')
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Révision nécessaire</strong><br>
                        Cette analyse nécessite une révision médicale approfondie.
                    </div>
                    @endif
                </div>
            </div>

            <!-- Informations Techniques -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">
                        <i class="fas fa-cog text-primary me-2"></i>
                        Informations Techniques
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Date d'analyse</small>
                        <p class="mb-0 fw-bold">{{ $analyse->created_at->format('d/m/Y à H:i:s') }}</p>
                    </div>
                    
                    @if($analyse->user)
                    <div class="mb-3">
                        <small class="text-muted">Analyste</small>
                        <p class="mb-0 fw-bold">{{ $analyse->user->name }}</p>
                        <small class="text-muted">{{ $analyse->user->role ?? 'Analyste' }}</small>
                    </div>
                    @endif
                    
                    @if($analyse->validateur && $analyse->statut === 'Validé')
                    <div class="mb-3">
                        <small class="text-muted">Validé par</small>
                        <p class="mb-0 fw-bold">{{ $analyse->validateur->name }}</p>
                        <small class="text-muted">{{ $analyse->updated_at->format('d/m/Y à H:i') }}</small>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <small class="text-muted">Temps d'analyse</small>
                        <p class="mb-0 fw-bold">{{ $analyse->temps_analyse ?? '2.5' }} secondes</p>
                    </div>
                    
                    <div class="mb-0">
                        <small class="text-muted">Modèle IA</small>
                        <p class="mb-0 fw-bold">CervicalCare AI v2.1</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt text-primary me-2"></i>
                        Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($analyse->statut === 'Validé')
                        <a href="{{ route('patient.analyse.download', $analyse) }}" class="btn btn-success">
                            <i class="fas fa-download me-2"></i>Télécharger PDF
                        </a>
                        @endif
                        
                        <a href="{{ route('patient.analyses') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list me-2"></i>Toutes mes Analyses
                        </a>
                        
                        <a href="{{ route('patient.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-2"></i>Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <!-- Informations Patient -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">
                        <i class="fas fa-user text-primary me-2"></i>
                        Informations Patient
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">Nom complet</small>
                        <p class="mb-0 fw-bold">{{ $patientData->nom_complet }}</p>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">N° Dossier</small>
                        <p class="mb-0 fw-bold">{{ $patientData->numero_dossier ?? 'En attente' }}</p>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted">Âge</small>
                        <p class="mb-0 fw-bold">{{ $patientData->age ?? 'N/A' }} ans</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Avertissement Médical -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-info border-0 shadow-sm">
                <div class="d-flex">
                    <i class="fas fa-info-circle fa-2x text-info me-3"></i>
                    <div>
                        <h6 class="alert-heading">Information Importante</h6>
                        <p class="mb-0">
                            Cette analyse par intelligence artificielle est fournie à titre informatif uniquement. 
                            Elle ne remplace en aucun cas l'avis d'un professionnel de santé qualifié. 
                            Pour toute question médicale, consultez votre médecin traitant.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}

.card {
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-1px);
}

.badge {
    font-size: 0.9em;
}

.table-primary {
    background-color: rgba(13, 110, 253, 0.1);
}
</style>
@endsection