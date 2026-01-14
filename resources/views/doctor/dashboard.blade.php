@extends('layouts.app')

@section('title', 'Dashboard Docteur')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-stethoscope text-success me-2"></i>
                Dashboard Docteur
            </h1>
            <p class="text-muted mb-0">Validation des analyses effectuées par l'administrateur</p>
        </div>
        <div class="text-end">
            <span class="badge bg-success fs-6">Dr. {{ Auth::user()->name }}</span>
            <div class="mt-1">
                <small class="text-muted">{{ now()->format('d/m/Y H:i') }}</small>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <h3 class="text-warning mb-1">{{ $stats['en_attente'] }}</h3>
                    <small class="text-muted">En attente de validation</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h3 class="text-success mb-1">{{ $stats['validees_aujourd_hui'] }}</h3>
                    <small class="text-muted">Validées aujourd'hui</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                    <h3 class="text-danger mb-1">{{ $stats['a_revoir'] }}</h3>
                    <small class="text-muted">À revoir</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-edit fa-2x text-info mb-2"></i>
                    <h3 class="text-info mb-1">{{ $stats['modifiees'] }}</h3>
                    <small class="text-muted">Modifiées</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-award fa-2x text-info mb-2"></i>
                    <h3 class="text-info mb-1">{{ $stats['total_validees'] }}</h3>
                    <small class="text-muted">Total validées</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-tasks me-2"></i>Actions Rapides
                        </h5>
                        <div>
                            <a href="{{ route('doctor.historique') }}" class="btn btn-outline-info me-2">
                                <i class="fas fa-history me-1"></i>Mon Historique
                            </a>
                            <a href="{{ route('doctor.a-revoir') }}" class="btn btn-outline-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i>À Revoir ({{ $stats['a_revoir'] }})
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analyses en attente de validation -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-clipboard-check me-2"></i>
                        Analyses en Attente de Validation ({{ $stats['en_attente'] }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($analysesEnAttente->count() > 0)
                        <div class="row g-0">
                            @foreach($analysesEnAttente as $analyse)
                                <div class="col-lg-6 col-xl-4">
                                    <div class="card border-0 border-end border-bottom">
                                        <div class="card-body">
                                            <!-- En-tête avec patient -->
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div>
                                                    <h6 class="mb-1">
                                                        <i class="fas fa-user text-primary me-1"></i>
                                                        {{ $analyse->patient->nom_complet }}
                                                    </h6>
                                                    <small class="text-muted">{{ $analyse->patient->numero_dossier }}</small>
                                                </div>
                                                <span class="badge bg-warning">En attente</span>
                                            </div>

                                            <!-- Image d'analyse -->
                                            <div class="text-center mb-3">
                                                <img src="{{ route('secure.image.show', $analyse->chemin_image) }}" 
                                                     class="img-fluid rounded shadow-sm" 
                                                     style="max-height: 120px; object-fit: cover;"
                                                     alt="Analyse {{ $analyse->nom_image }}">
                                                <div class="mt-2">
                                                    <small class="text-muted">{{ $analyse->nom_image }}</small>
                                                </div>
                                            </div>

                                            <!-- Résultat IA -->
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <strong class="text-{{ $analyse->risque_color }}">{{ $analyse->classe_predite }}</strong>
                                                    <span class="badge bg-{{ $analyse->risque_color }}">{{ $analyse->confidence_percent }}%</span>
                                                </div>
                                                <div class="progress" style="height: 4px;">
                                                    <div class="progress-bar bg-{{ $analyse->risque_color }}" 
                                                         style="width: {{ $analyse->confidence_percent }}%"></div>
                                                </div>
                                                <small class="text-muted">Risque: {{ $analyse->niveau_risque }}</small>
                                            </div>

                                            <!-- Informations d'analyse -->
                                            <div class="mb-3">
                                                <div class="row text-center">
                                                    <div class="col-6">
                                                        <small class="text-muted">Analysé le</small>
                                                        <div class="fw-bold">{{ $analyse->created_at->format('d/m/Y') }}</div>
                                                        <small class="text-muted">{{ $analyse->created_at->format('H:i') }}</small>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted">Par</small>
                                                        <div class="fw-bold">{{ $analyse->analyste->name }}</div>
                                                        <small class="text-muted">Admin</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Actions -->
                                            <div class="d-grid gap-2">
                                                <a href="{{ route('doctor.analyse.show', $analyse) }}" 
                                                   class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye me-1"></i>Examiner et Valider
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($analysesEnAttente->hasPages())
                            <div class="p-3 border-top">
                                {{ $analysesEnAttente->links('pagination::minimal') }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                            <h5 class="text-success">Aucune analyse en attente</h5>
                            <p class="text-muted">Toutes les analyses ont été validées !</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Analyses validées (modifiables) -->
    @if($analysesValidees->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2"></i>
                            Mes Validations (Modifiables)
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="row g-0">
                            @foreach($analysesValidees as $analyse)
                                <div class="col-lg-6 col-xl-4">
                                    <div class="card border-0 border-end border-bottom">
                                        <div class="card-body">
                                            <!-- En-tête avec patient -->
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div>
                                                    <h6 class="mb-1">
                                                        <i class="fas fa-user text-primary me-1"></i>
                                                        {{ $analyse->patient->nom_complet }}
                                                    </h6>
                                                    <small class="text-muted">{{ $analyse->patient->numero_dossier }}</small>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-{{ $analyse->statut_color }}">{{ $analyse->statut }}</span>
                                                    @if($analyse->date_derniere_modification)
                                                        <br><small class="text-info">Modifiée</small>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Image d'analyse -->
                                            <div class="text-center mb-3">
                                                <img src="{{ route('secure.image.show', $analyse->chemin_image) }}" 
                                                     class="img-fluid rounded shadow-sm" 
                                                     style="max-height: 120px; object-fit: cover;"
                                                     alt="Analyse {{ $analyse->nom_image }}">
                                                <div class="mt-2">
                                                    <small class="text-muted">{{ $analyse->nom_image }}</small>
                                                </div>
                                            </div>

                                            <!-- Résultat IA vs Validation -->
                                            <div class="mb-3">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <small class="text-muted">IA:</small>
                                                        <div class="fw-bold text-{{ $analyse->risque_color }}">{{ $analyse->classe_predite }}</div>
                                                        <small class="text-muted">{{ $analyse->confidence_percent }}%</small>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted">Ma validation:</small>
                                                        <div class="fw-bold text-{{ $analyse->statut_color }}">
                                                            {{ $analyse->classe_finale_medecin ?? $analyse->classe_predite }}
                                                        </div>
                                                        <small class="text-{{ $analyse->statut_color }}">{{ $analyse->decision_medecin }}</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Dates -->
                                            <div class="mb-3">
                                                <div class="row text-center">
                                                    <div class="col-6">
                                                        <small class="text-muted">Validé le</small>
                                                        <div class="fw-bold">{{ $analyse->date_validation->format('d/m/Y') }}</div>
                                                        <small class="text-muted">{{ $analyse->date_validation->format('H:i') }}</small>
                                                    </div>
                                                    <div class="col-6">
                                                        @if($analyse->date_derniere_modification)
                                                            <small class="text-muted">Modifié le</small>
                                                            <div class="fw-bold text-info">{{ $analyse->date_derniere_modification->format('d/m/Y') }}</div>
                                                            <small class="text-info">{{ $analyse->date_derniere_modification->format('H:i') }}</small>
                                                        @else
                                                            <small class="text-muted">Pas de modification</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Actions -->
                                            <div class="d-grid gap-2">
                                                <a href="{{ route('doctor.analyse.show', $analyse) }}" 
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-edit me-1"></i>Modifier ma Validation
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Validations récentes -->
    @if($recentValidations->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i>
                            Mes Validations Récentes
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Patient</th>
                                        <th>Classe IA</th>
                                        <th>Ma Décision</th>
                                        <th>Date Validation</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentValidations as $validation)
                                        <tr>
                                            <td>
                                                <strong>{{ $validation->patient->nom_complet }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $validation->patient->numero_dossier }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $validation->risque_color }}">
                                                    {{ $validation->classe_predite }}
                                                </span>
                                                <br>
                                                <small class="text-muted">{{ $validation->confidence_percent }}%</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $validation->statut_color }}">
                                                    {{ $validation->statut }}
                                                </span>
                                                @if($validation->classe_finale_medecin && $validation->classe_finale_medecin !== $validation->classe_predite)
                                                    <br>
                                                    <small class="text-info">Corrigé: {{ $validation->classe_finale_medecin }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $validation->date_validation->format('d/m/Y H:i') }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $validation->date_validation->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                <a href="{{ route('doctor.analyse.show', $validation) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
