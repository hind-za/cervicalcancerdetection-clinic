@extends('layouts.app')

@section('title', __('app.results'))

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
                        {{ __('app.must_login_results') }}
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
                <i class="fas fa-eye text-info me-2"></i>
                {{ __('app.view_results') }}
            </h1>
            <p class="text-muted mb-0">{{ __('app.analysis_consultation') }}</p>
        </div>
        <div class="d-flex align-items-center">
            <button class="btn btn-outline-primary me-2" onclick="toggleFilters()">
                <i class="fas fa-filter me-1"></i>{{ __('app.filters') }}
            </button>
            <span class="badge bg-{{ Auth::user()->role === 'admin' ? 'primary' : 'success' }} fs-6">
                {{ ucfirst(Auth::user()->role) }}
            </span>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card border-0 shadow-sm mb-4" id="filtersCard" style="display: none;">
        <div class="card-body">
            <form method="GET" action="{{ route('consulter.resultats') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">{{ __('app.result') }}</label>
                        <select class="form-select" name="resultat">
                            <option value="all">{{ __('app.all_results') }}</option>
                            <option value="Normal" {{ request('resultat') == 'Normal' ? 'selected' : '' }}>{{ __('app.normal') }}</option>
                            <option value="À Surveiller" {{ request('resultat') == 'À Surveiller' ? 'selected' : '' }}>{{ __('app.to_monitor') }}</option>
                            <option value="Anomalie Détectée" {{ request('resultat') == 'Anomalie Détectée' ? 'selected' : '' }}>{{ __('app.anomaly_detected') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">{{ __('app.status') }}</label>
                        <select class="form-select" name="statut">
                            <option value="all">{{ __('app.all_statuses') }}</option>
                            <option value="En attente" {{ request('statut') == 'En attente' ? 'selected' : '' }}>{{ __('app.pending') }}</option>
                            <option value="À revoir" {{ request('statut') == 'À revoir' ? 'selected' : '' }}>À revoir</option>
                        </select>
                    </div>
                    <div class="col-md-4">
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
                        <a href="{{ route('consulter.resultats') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Réinitialiser
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-circle fa-2x text-danger mb-2"></i>
                    <h5 class="fw-bold text-danger">{{ $stats['urgent'] }}</h5>
                    <p class="text-muted mb-0">{{ __('app.urgent_cases') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <h5 class="fw-bold text-warning">{{ $stats['en_attente'] }}</h5>
                    <p class="text-muted mb-0">{{ __('app.pending') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-eye fa-2x text-info mb-2"></i>
                    <h5 class="fw-bold text-info">{{ $stats['a_revoir'] }}</h5>
                    <p class="text-muted mb-0">À revoir</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list text-info me-2"></i>
                    {{ __('app.recent_results') }}
                </h5>
                <div class="d-flex align-items-center">
                    @if($analyses->count() > 0)
                        <span class="text-muted me-3">{{ __('app.display') }}: {{ $analyses->firstItem() }}-{{ $analyses->lastItem() }} {{ __('app.of') }} {{ $analyses->total() }}</span>
                    @endif
                    <button class="btn btn-sm btn-outline-primary" onclick="location.reload()">
                        <i class="fas fa-sync-alt"></i> {{ __('app.refresh') }}
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($analyses->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">{{ __('app.patient_id') }}</th>
                                <th class="border-0">{{ __('app.name') }}</th>
                                <th class="border-0">{{ __('app.age') }}</th>
                                <th class="border-0">Image</th>
                                <th class="border-0">{{ __('app.ai_result') }}</th>
                                <th class="border-0">Confiance</th>
                                <th class="border-0">{{ __('app.priority') }}</th>
                                <th class="border-0">{{ __('app.date') }}</th>
                                <th class="border-0">{{ __('app.status') }}</th>
                                <th class="border-0">{{ __('app.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($analyses as $analyse)
                                @php
                                    $priorityClass = match($analyse->resultat) {
                                        'Anomalie Détectée' => 'table-danger',
                                        'À Surveiller' => 'table-warning',
                                        default => ''
                                    };
                                    $priorityBadge = match($analyse->resultat) {
                                        'Anomalie Détectée' => 'danger',
                                        'À Surveiller' => 'warning',
                                        default => 'info'
                                    };
                                    $priorityText = match($analyse->resultat) {
                                        'Anomalie Détectée' => __('app.urgent'),
                                        'À Surveiller' => __('app.medium'),
                                        default => __('app.low')
                                    };
                                @endphp
                                <tr class="{{ $priorityClass }}">
                                    <td><strong>#{{ $analyse->patient->numero_dossier }}</strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-{{ $analyse->resultat_color }} text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                {{ strtoupper(substr($analyse->patient->prenom, 0, 1) . substr($analyse->patient->nom, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $analyse->patient->nom_complet }}</div>
                                                <small class="text-muted">ID: {{ $analyse->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $analyse->patient->age }} ans</td>
                                    <td>
                                        <img src="{{ route('secure.image.show', basename($analyse->chemin_image)) }}" 
                                             class="rounded" style="width: 40px; height: 40px; object-fit: cover;" 
                                             alt="Analyse" data-bs-toggle="tooltip" title="Cliquer pour agrandir">
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
                                    <td><span class="badge bg-{{ $priorityBadge }}">{{ $priorityText }}</span></td>
                                    <td>
                                        <div>{{ $analyse->created_at->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $analyse->created_at->format('H:i') }}</small>
                                    </td>
                                    <td><span class="badge bg-{{ $analyse->statut_color }}">{{ $analyse->statut }}</span></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('patients.show', $analyse->patient) }}" 
                                               class="btn btn-outline-primary" title="{{ __('app.view_details') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(Auth::user()->role === 'doctor' && $analyse->statut !== 'Validé')
                                                <button class="btn btn-outline-success" 
                                                        onclick="openValidationModal({{ $analyse->id }}, '{{ $analyse->patient->nom_complet }}', 'Validé')"
                                                        title="{{ __('app.validate') }}">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="btn btn-outline-warning" 
                                                        onclick="openValidationModal({{ $analyse->id }}, '{{ $analyse->patient->nom_complet }}', 'À revoir')"
                                                        title="{{ __('app.correct') }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            @endif
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
                    <h5 class="text-muted">Aucune analyse en attente</h5>
                    <p class="text-muted">Toutes les analyses ont été validées ou aucune analyse ne correspond aux critères</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de validation (pour les médecins) -->
    @if(Auth::user()->role === 'doctor')
        <div class="modal fade" id="validationModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Validation de l'analyse</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="validationForm" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Patient:</label>
                                <p id="patientName" class="text-muted"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Action:</label>
                                <p id="actionText" class="text-muted"></p>
                            </div>
                            <div class="mb-3">
                                <label for="commentaires" class="form-label">Commentaires (optionnel):</label>
                                <textarea class="form-control" id="commentaires" name="commentaires" rows="3" 
                                          placeholder="Ajoutez vos observations..."></textarea>
                            </div>
                            <input type="hidden" id="statutInput" name="statut">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary" id="confirmBtn">Confirmer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
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
@if(request()->hasAny(['resultat', 'statut', 'search']))
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('filtersCard').style.display = 'block';
    });
@endif

@if(Auth::check() && Auth::user()->role === 'doctor')
function openValidationModal(analyseId, patientName, action) {
    document.getElementById('patientName').textContent = patientName;
    document.getElementById('actionText').textContent = action === 'Validé' ? 'Valider l\'analyse' : 'Marquer à revoir';
    document.getElementById('statutInput').value = action;
    document.getElementById('validationForm').action = `/analyses/${analyseId}/valider`;
    
    const confirmBtn = document.getElementById('confirmBtn');
    confirmBtn.className = action === 'Validé' ? 'btn btn-success' : 'btn btn-warning';
    confirmBtn.textContent = action === 'Validé' ? 'Valider' : 'Marquer à revoir';
    
    new bootstrap.Modal(document.getElementById('validationModal')).show();
}

// Initialiser les tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
@endif
</script>
@endsection