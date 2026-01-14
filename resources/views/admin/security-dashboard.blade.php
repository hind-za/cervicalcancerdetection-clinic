@extends('layouts.app')

@section('title', 'Tableau de Bord Sécurité')

@section('content')
<div class="container-fluid">
    @if(isset($error))
    <div class="alert alert-warning">
        <strong>Attention:</strong> {{ $error }}
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">🔐 Tableau de Bord Sécurité</h1>
                <div class="btn-group">
                    <button class="btn btn-outline-primary" onclick="refreshStats(1)">1h</button>
                    <button class="btn btn-outline-primary" onclick="refreshStats(6)">6h</button>
                    <button class="btn btn-primary" onclick="refreshStats(24)">24h</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes -->
    @if(count($alerts) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">🚨 Alertes de Sécurité</h5>
                </div>
                <div class="card-body">
                    @foreach($alerts as $alert)
                    <div class="alert alert-{{ $alert['type'] }} mb-2">
                        <strong>{{ $alert['title'] }}</strong><br>
                        {{ $alert['message'] }}
                        <small class="text-muted d-block">{{ $alert['timestamp']->format('d/m/Y H:i:s') }}</small>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="display-4 text-primary" id="stat-total-logs">{{ $stats['total_logs'] }}</div>
                    <h6 class="card-title">Total Logs</h6>
                    <small class="text-muted">Dernières {{ $stats['period_hours'] }}h</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="display-4 text-danger" id="stat-decryption-errors">{{ $stats['decryption_errors'] }}</div>
                    <h6 class="card-title">Erreurs Déchiffrement</h6>
                    <small class="text-muted">Seuil: 5/h</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="display-4 text-warning" id="stat-unauthorized">{{ $stats['unauthorized_attempts'] }}</div>
                    <h6 class="card-title">Accès Non Autorisé</h6>
                    <small class="text-muted">Seuil: 3/h</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="display-4 text-info" id="stat-sensitive-access">{{ $stats['sensitive_data_accesses'] }}</div>
                    <h6 class="card-title">Accès Données Sensibles</h6>
                    <small class="text-muted">Seuil: 50/h</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <div class="display-4 text-secondary" id="stat-image-errors">{{ $stats['image_access_errors'] }}</div>
                    <h6 class="card-title">Erreurs Images</h6>
                    <small class="text-muted">Seuil: 10/h</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <div class="display-4 text-success" id="stat-unique-users">{{ $stats['unique_users'] }}</div>
                    <h6 class="card-title">Utilisateurs Actifs</h6>
                    <small class="text-muted">Uniques</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <div class="display-4 text-dark" id="stat-unique-ips">{{ $stats['unique_ips'] }}</div>
                    <h6 class="card-title">Adresses IP</h6>
                    <small class="text-muted">Uniques</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Logs récents -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">📋 Logs de Sécurité Récents</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Timestamp</th>
                                    <th>Type</th>
                                    <th>Message</th>
                                </tr>
                            </thead>
                            <tbody id="recent-logs">
                                @foreach($recentLogs as $log)
                                <tr>
                                    <td>{{ $log['timestamp']->format('d/m/Y H:i:s') }}</td>
                                    <td>
                                        @if(strpos($log['content'], 'DecryptException') !== false)
                                            <span class="badge bg-danger">Déchiffrement</span>
                                        @elseif(strpos($log['content'], 'Unauthorized') !== false)
                                            <span class="badge bg-warning">Non Autorisé</span>
                                        @elseif(strpos($log['content'], 'Sensitive data access') !== false)
                                            <span class="badge bg-info">Données Sensibles</span>
                                        @elseif(strpos($log['content'], 'Image access') !== false)
                                            <span class="badge bg-secondary">Image</span>
                                        @else
                                            <span class="badge bg-light text-dark">Autre</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ Str::limit(strip_tags($log['content']), 100) }}</small>
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
</div>

<script>
function refreshStats(hours) {
    // Mettre à jour les boutons
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-outline-primary');
    });
    event.target.classList.remove('btn-outline-primary');
    event.target.classList.add('btn-primary');

    // Charger les nouvelles statistiques
    fetch(`/admin/security/stats?hours=${hours}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('stat-total-logs').textContent = data.total_logs;
            document.getElementById('stat-decryption-errors').textContent = data.decryption_errors;
            document.getElementById('stat-unauthorized').textContent = data.unauthorized_attempts;
            document.getElementById('stat-sensitive-access').textContent = data.sensitive_data_accesses;
            document.getElementById('stat-image-errors').textContent = data.image_access_errors;
            document.getElementById('stat-unique-users').textContent = data.unique_users;
            document.getElementById('stat-unique-ips').textContent = data.unique_ips;
        })
        .catch(error => {
            console.error('Erreur lors du chargement des statistiques:', error);
        });
}

// Auto-refresh toutes les 30 secondes
setInterval(() => {
    const activeBtn = document.querySelector('.btn-group .btn-primary');
    if (activeBtn) {
        const hours = activeBtn.textContent.replace('h', '');
        refreshStats(parseInt(hours));
    }
}, 30000);
</script>
@endsection