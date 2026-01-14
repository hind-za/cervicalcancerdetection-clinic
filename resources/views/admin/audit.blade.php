@extends('layouts.app')

@section('title', 'Logs d\'Audit')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-shield-alt text-danger me-2"></i>
                Logs d'Audit & Sécurité
            </h1>
            <p class="text-muted mb-0">Surveillance des accès et modifications des données sensibles</p>
        </div>
        <div>
            <a href="{{ route('audit.export', request()->query()) }}" class="btn btn-outline-primary">
                <i class="fas fa-download me-1"></i>Exporter CSV
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-list fa-2x text-info mb-2"></i>
                    <h3 class="text-info mb-1">{{ number_format($stats['total_logs']) }}</h3>
                    <small class="text-muted">Total logs</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-day fa-2x text-success mb-2"></i>
                    <h3 class="text-success mb-1">{{ $stats['today_logs'] }}</h3>
                    <small class="text-muted">Aujourd'hui</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                    <h3 class="text-danger mb-1">{{ $stats['critical_logs'] }}</h3>
                    <small class="text-muted">Critiques</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x text-warning mb-2"></i>
                    <h3 class="text-warning mb-1">{{ $stats['unique_users'] }}</h3>
                    <small class="text-muted">Utilisateurs actifs</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>Filtres
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('audit.index') }}">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Action</label>
                        <select name="action" class="form-select">
                            <option value="">Toutes</option>
                            <option value="create" {{ request('action') === 'create' ? 'selected' : '' }}>Création</option>
                            <option value="update" {{ request('action') === 'update' ? 'selected' : '' }}>Modification</option>
                            <option value="delete" {{ request('action') === 'delete' ? 'selected' : '' }}>Suppression</option>
                            <option value="view" {{ request('action') === 'view' ? 'selected' : '' }}>Consultation</option>
                            <option value="download" {{ request('action') === 'download' ? 'selected' : '' }}>Téléchargement</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sévérité</label>
                        <select name="severity" class="form-select">
                            <option value="">Toutes</option>
                            <option value="low" {{ request('severity') === 'low' ? 'selected' : '' }}>Faible</option>
                            <option value="medium" {{ request('severity') === 'medium' ? 'selected' : '' }}>Moyenne</option>
                            <option value="high" {{ request('severity') === 'high' ? 'selected' : '' }}>Élevée</option>
                            <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>Critique</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Modèle</label>
                        <select name="model_type" class="form-select">
                            <option value="">Tous</option>
                            <option value="App\Models\Patient" {{ request('model_type') === 'App\Models\Patient' ? 'selected' : '' }}>Patient</option>
                            <option value="App\Models\AnalyseIA" {{ request('model_type') === 'App\Models\AnalyseIA' ? 'selected' : '' }}>Analyse IA</option>
                            <option value="App\Models\User" {{ request('model_type') === 'App\Models\User' ? 'selected' : '' }}>Utilisateur</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date début</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date fin</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Filtrer
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs -->
    <div class="card border-0 shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Logs d'Audit ({{ $logs->total() }})
            </h5>
        </div>
        <div class="card-body p-0">
            @if($logs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Date/Heure</th>
                                <th>Utilisateur</th>
                                <th>Action</th>
                                <th>Modèle</th>
                                <th>Sévérité</th>
                                <th>IP</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                <tr class="{{ $log->severity === 'critical' ? 'table-danger' : ($log->severity === 'high' ? 'table-warning' : '') }}">
                                    <td>
                                        <strong>{{ $log->created_at->format('d/m/Y H:i:s') }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        @if($log->user)
                                            <strong>{{ $log->user->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $log->user->role }}</small>
                                        @else
                                            <span class="text-muted">Système</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $log->action === 'delete' ? 'danger' : ($log->action === 'create' ? 'success' : 'primary') }}">
                                            {{ ucfirst($log->action) }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ class_basename($log->model_type) }}</strong>
                                        @if($log->model_id)
                                            <br>
                                            <small class="text-muted">ID: {{ $log->model_id }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $log->severity === 'critical' ? 'danger' : ($log->severity === 'high' ? 'warning' : ($log->severity === 'medium' ? 'info' : 'secondary')) }}">
                                            {{ ucfirst($log->severity) }}
                                        </span>
                                    </td>
                                    <td>
                                        <code>{{ $log->ip_address }}</code>
                                    </td>
                                    <td>
                                        <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $log->description }}">
                                            {{ $log->description }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('audit.show', $log) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-3 border-top">
                    {{ $logs->withQueryString()->links('pagination::minimal') }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-search fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun log trouvé</h5>
                    <p class="text-muted">Aucun log d'audit ne correspond aux critères de recherche.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection