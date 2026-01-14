@extends('layouts.app')

@section('title', __('app.history'))

@section('content')
@guest
    <!-- Message pour utilisateurs non connectés -->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-lg">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-lock fa-4x text-muted"></i>
                    </div>
                    <h3 class="text-muted mb-3">{{ __('app.restricted_access') }}</h3>
                    <p class="lead text-muted mb-4">
                        {{ __('app.must_login_history') }}
                    </p>
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>{{ __('app.connect') }}
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-user-plus me-2"></i>{{ __('app.create_account') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <!-- Contenu pour utilisateurs connectés -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-history text-info me-2"></i>
                {{ __('app.analysis_history') }}
            </h1>
            <p class="text-muted mb-0">{{ __('app.chronological_tracking') }}</p>
        </div>
        <div>
            <button class="btn btn-outline-primary me-2" onclick="toggleFilters()">
                <i class="fas fa-filter me-1"></i>{{ __('app.filters') }}
            </button>
            <button class="btn btn-primary">
                <i class="fas fa-file-export me-1"></i>{{ __('app.export') }}
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card border-0 shadow-sm mb-4" id="filtersCard" style="display: none;">
        <div class="card-body">
            <form method="GET" action="{{ route('historique.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">{{ __('app.period') }}</label>
                        <select class="form-select" name="periode">
                            <option value="">Toutes les périodes</option>
                            <option value="last_7_days" {{ request('periode') == 'last_7_days' ? 'selected' : '' }}>{{ __('app.last_7_days') }}</option>
                            <option value="last_month" {{ request('periode') == 'last_month' ? 'selected' : '' }}>{{ __('app.last_month') }}</option>
                            <option value="last_3_months" {{ request('periode') == 'last_3_months' ? 'selected' : '' }}>{{ __('app.last_3_months') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">{{ __('app.status') }}</label>
                        <select class="form-select" name="statut">
                            <option value="all">{{ __('app.all_statuses') }}</option>
                            <option value="En attente" {{ request('statut') == 'En attente' ? 'selected' : '' }}>{{ __('app.pending') }}</option>
                            <option value="Validé" {{ request('statut') == 'Validé' ? 'selected' : '' }}>{{ __('app.validated') }}</option>
                            <option value="À revoir" {{ request('statut') == 'À revoir' ? 'selected' : '' }}>À revoir</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">{{ __('app.result') }}</label>
                        <select class="form-select" name="resultat">
                            <option value="all">{{ __('app.all_results') }}</option>
                            <option value="Normal" {{ request('resultat') == 'Normal' ? 'selected' : '' }}>{{ __('app.normal') }}</option>
                            <option value="À Surveiller" {{ request('resultat') == 'À Surveiller' ? 'selected' : '' }}>{{ __('app.to_monitor') }}</option>
                            <option value="Anomalie Détectée" {{ request('resultat') == 'Anomalie Détectée' ? 'selected' : '' }}>{{ __('app.anomaly_detected') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">{{ __('app.search') }}</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="{{ __('app.search_placeholder') }}">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter me-1"></i>Appliquer les filtres
                        </button>
                        <a href="{{ route('historique.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Réinitialiser
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <i class="fas fa-chart-bar fa-2x text-primary mb-2"></i>
                    <h4 class="fw-bold text-primary">{{ $stats['total'] }}</h4>
                    <p class="text-muted mb-0">{{ __('app.total_analyses') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h4 class="fw-bold text-success">{{ $stats['normal'] }}</h4>
                    <p class="text-muted mb-0">{{ __('app.normal_cases') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                    <h4 class="fw-bold text-warning">{{ $stats['surveiller'] }}</h4>
                    <p class="text-muted mb-0">{{ __('app.to_monitor_cases') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <i class="fas fa-exclamation-circle fa-2x text-danger mb-2"></i>
                    <h4 class="fw-bold text-danger">{{ $stats['anomalie'] }}</h4>
                    <p class="text-muted mb-0">{{ __('app.anomalies') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list text-info me-2"></i>
                    {{ __('app.detailed_history') }}
                </h5>
                <div class="d-flex align-items-center">
                    <span class="text-muted me-3">{{ __('app.display') }}: {{ $analyses->firstItem() }}-{{ $analyses->lastItem() }} {{ __('app.of') }} {{ $analyses->total() }}</span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($analyses->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">ID</th>
                                <th class="border-0">Patient</th>
                                <th class="border-0">{{ __('app.age') }}</th>
                                <th class="border-0">Image</th>
                                <th class="border-0">{{ __('app.ai_result') }}</th>
                                <th class="border-0">Confiance</th>
                                <th class="border-0">{{ __('app.validation') }}</th>
                                <th class="border-0">{{ __('app.physician') }}</th>
                                <th class="border-0">{{ __('app.date') }}</th>
                                <th class="border-0">{{ __('app.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($analyses as $analyse)
                                <tr>
                                    <td><strong>#{{ $analyse->id }}</strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-{{ $analyse->resultat_color }} text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                {{ strtoupper(substr($analyse->patient->prenom, 0, 1) . substr($analyse->patient->nom, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $analyse->patient->nom_complet }}</div>
                                                <small class="text-muted">{{ $analyse->patient->numero_dossier }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $analyse->patient->age }} ans</td>
                                    <td>
                                        <img src="{{ route('secure.image.show', basename($analyse->chemin_image)) }}" 
                                             class="history-image" style="width: 40px; height: 40px; object-fit: cover;" 
                                             alt="Analyse"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="image-placeholder history-image" style="width: 40px; height: 40px; display: none; font-size: 10px;">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $analyse->resultat_color }}">{{ $analyse->resultat }}</span>
                                    </td>
                                    <td>
                                        <div class="progress" style="width: 60px; height: 8px;">
                                            <div class="progress-bar bg-{{ $analyse->resultat_color }}" 
                                                 style="width: {{ $analyse->confiance }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $analyse->confiance }}%</small>
                                    </td>
                                    <td><span class="badge bg-{{ $analyse->statut_color }}">{{ $analyse->statut }}</span></td>
                                    <td>
                                        @if($analyse->validateur)
                                            {{ $analyse->validateur->name }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $analyse->created_at->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $analyse->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('patients.show', $analyse->patient) }}" 
                                               class="btn btn-outline-primary" title="{{ __('app.view_details') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button class="btn btn-outline-success" title="{{ __('app.download_report') }}">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $analyses->appends(request()->query())->links('pagination::minimal') }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune analyse trouvée</h5>
                    <p class="text-muted">Essayez de modifier vos critères de recherche</p>
                </div>
            @endif
        </div>
    </div>
@endguest

<script>
function toggleFilters() {
    const filtersCard = document.getElementById('filtersCard');
    if (filtersCard.style.display === 'none') {
        filtersCard.style.display = 'block';
    } else {
        filtersCard.style.display = 'none';
    }
}

// Afficher les filtres si des paramètres sont présents
@if(request()->hasAny(['periode', 'statut', 'resultat', 'search']))
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('filtersCard').style.display = 'block';
    });
@endif
</script>
@endsection