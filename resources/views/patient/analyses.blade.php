@extends('layouts.app')

@section('title', 'Mes Analyses')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-microscope text-primary me-2"></i>
                        Mes Analyses Médicales
                    </h1>
                    <p class="text-muted mb-0">Historique complet de vos analyses cytologiques</p>
                </div>
                <div class="text-end">
                    <a href="{{ route('patient.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour au Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations Patient -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-1">{{ $patientData->nom_complet }}</h5>
                            <p class="text-muted mb-0">
                                <i class="fas fa-id-card me-1"></i>Dossier N° {{ $patientData->numero_dossier ?? 'En attente' }} • 
                                <i class="fas fa-birthday-cake me-1"></i>{{ $patientData->age ?? 'N/A' }} ans • 
                                <i class="fas fa-venus-mars me-1"></i>{{ isset($patientData->sexe) ? ($patientData->sexe === 'F' ? 'Féminin' : 'Masculin') : 'N/A' }}
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-primary fs-6">
                                {{ $analyses->total() }} analyse(s) au total
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et Recherche -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('patient.analyses') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Statut</label>
                            <select name="statut" class="form-select">
                                <option value="">Tous les statuts</option>
                                <option value="Validé" {{ request('statut') === 'Validé' ? 'selected' : '' }}>Validé</option>
                                <option value="En attente" {{ request('statut') === 'En attente' ? 'selected' : '' }}>En attente</option>
                                <option value="À revoir" {{ request('statut') === 'À revoir' ? 'selected' : '' }}>À revoir</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Niveau de risque</label>
                            <select name="risque" class="form-select">
                                <option value="">Tous les niveaux</option>
                                <option value="Faible" {{ request('risque') === 'Faible' ? 'selected' : '' }}>Faible</option>
                                <option value="Modéré" {{ request('risque') === 'Modéré' ? 'selected' : '' }}>Modéré</option>
                                <option value="Élevé" {{ request('risque') === 'Élevé' ? 'selected' : '' }}>Élevé</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date de début</label>
                            <input type="date" name="date_debut" class="form-control" value="{{ request('date_debut') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date de fin</label>
                            <input type="date" name="date_fin" class="form-control" value="{{ request('date_fin') }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Filtrer
                            </button>
                            <a href="{{ route('patient.analyses') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Réinitialiser
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des Analyses -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-list text-primary me-2"></i>
                        Historique des Analyses
                    </h5>
                </div>
                <div class="card-body">
                    @if($analyses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date & Heure</th>
                                        <th>Résultat de l'Analyse</th>
                                        <th>Niveau de Risque</th>
                                        <th>Statut</th>
                                        <th>Analyste</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($analyses as $analyse)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $analyse->created_at->format('d/m/Y') }}</div>
                                            <small class="text-muted">{{ $analyse->created_at->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-primary">{{ $analyse->classe_predite }}</div>
                                            <small class="text-muted">
                                                Confiance: {{ round($analyse->probabilite * 100, 1) }}%
                                            </small>
                                            @if($analyse->interpretation)
                                            <div class="mt-1">
                                                <small class="text-muted">{{ Str::limit($analyse->interpretation, 50) }}</small>
                                            </div>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $riskClass = match($analyse->niveau_risque) {
                                                    'Élevé' => 'danger',
                                                    'Modéré' => 'warning',
                                                    'Faible' => 'success',
                                                    default => 'secondary'
                                                };
                                                $riskIcon = match($analyse->niveau_risque) {
                                                    'Élevé' => 'fas fa-exclamation-triangle',
                                                    'Modéré' => 'fas fa-exclamation-circle',
                                                    'Faible' => 'fas fa-check-circle',
                                                    default => 'fas fa-question-circle'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $riskClass }} d-flex align-items-center">
                                                <i class="{{ $riskIcon }} me-1"></i>
                                                {{ $analyse->niveau_risque }}
                                            </span>
                                        </td>
                                        <td>
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
                                            <span class="badge bg-{{ $statusClass }} d-flex align-items-center">
                                                <i class="{{ $statusIcon }} me-1"></i>
                                                {{ $analyse->statut }}
                                            </span>
                                            @if($analyse->statut === 'Validé' && $analyse->validateur)
                                            <div class="mt-1">
                                                <small class="text-muted">
                                                    Par: {{ $analyse->validateur->name }}
                                                </small>
                                            </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($analyse->user)
                                            <div class="fw-bold">{{ $analyse->user->name }}</div>
                                            <small class="text-muted">{{ $analyse->user->role ?? 'Analyste' }}</small>
                                            @else
                                            <span class="text-muted">Non renseigné</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('patient.analyse.show', $analyse) }}" 
                                                   class="btn btn-outline-primary" 
                                                   title="Voir les détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($analyse->statut === 'Validé')
                                                <a href="{{ route('patient.analyse.download', $analyse) }}" 
                                                   class="btn btn-outline-success" 
                                                   title="Télécharger le rapport PDF">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $analyses->appends(request()->query())->links('pagination::minimal') }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-microscope fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune analyse trouvée</h5>
                            <p class="text-muted">
                                @if(request()->hasAny(['statut', 'risque', 'date_debut', 'date_fin']))
                                    Aucune analyse ne correspond à vos critères de recherche.
                                @else
                                    Vous n'avez pas encore d'analyses enregistrées.
                                @endif
                            </p>
                            @if(request()->hasAny(['statut', 'risque', 'date_debut', 'date_fin']))
                            <a href="{{ route('patient.analyses') }}" class="btn btn-outline-primary">
                                <i class="fas fa-times me-1"></i>Réinitialiser les filtres
                            </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Légende -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body">
                    <h6 class="mb-3">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        Légende des Statuts et Niveaux de Risque
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="small fw-bold">Statuts d'Analyse :</h6>
                            <div class="mb-2">
                                <span class="badge bg-success me-2">Validé</span>
                                <small>Analyse validée par un médecin</small>
                            </div>
                            <div class="mb-2">
                                <span class="badge bg-warning me-2">En attente</span>
                                <small>En attente de validation médicale</small>
                            </div>
                            <div class="mb-2">
                                <span class="badge bg-danger me-2">À revoir</span>
                                <small>Nécessite une révision médicale</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="small fw-bold">Niveaux de Risque :</h6>
                            <div class="mb-2">
                                <span class="badge bg-success me-2">Faible</span>
                                <small>Résultat normal, suivi de routine</small>
                            </div>
                            <div class="mb-2">
                                <span class="badge bg-warning me-2">Modéré</span>
                                <small>Surveillance recommandée</small>
                            </div>
                            <div class="mb-2">
                                <span class="badge bg-danger me-2">Élevé</span>
                                <small>Suivi médical immédiat requis</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,.075);
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
}

.badge {
    font-size: 0.75em;
}

.card {
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-1px);
}
</style>
@endsection