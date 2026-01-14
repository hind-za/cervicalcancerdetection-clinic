@extends('layouts.app')

@section('title', 'Centre de Téléchargements')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- En-tête -->
            <div class="text-center mb-5">
                <div class="mb-4">
                    <i class="fas fa-download fa-4x text-primary"></i>
                </div>
                <h1 class="h2 mb-3">Centre de Téléchargements</h1>
                <p class="text-muted">
                    Accédez à tous vos rapports d'analyses et documents médicaux
                </p>
            </div>

            <!-- Statistiques rapides -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <i class="fas fa-file-pdf fa-2x text-danger mb-2"></i>
                            <h4 class="text-danger mb-1">{{ $stats['rapports_disponibles'] ?? 0 }}</h4>
                            <small class="text-muted">Rapports disponibles</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <h4 class="text-success mb-1">{{ $stats['analyses_validees'] ?? 0 }}</h4>
                            <small class="text-muted">Analyses validées</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <i class="fas fa-calendar fa-2x text-info mb-2"></i>
                            <h4 class="text-info mb-1">{{ $stats['derniere_analyse'] ?? 'N/A' }}</h4>
                            <small class="text-muted">Dernière analyse</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <i class="fas fa-download fa-2x text-warning mb-2"></i>
                            <h4 class="text-warning mb-1">{{ $stats['telechargements'] ?? 0 }}</h4>
                            <small class="text-muted">Téléchargements</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Rapports d'analyses -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-file-medical text-primary me-2"></i>
                                    Mes Rapports d'Analyses
                                </h5>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="downloadAll()">
                                        <i class="fas fa-download me-1"></i>Tout télécharger
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            @if(isset($analyses) && $analyses->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="border-0">Date</th>
                                                <th class="border-0">Type</th>
                                                <th class="border-0">Résultat</th>
                                                <th class="border-0">Statut</th>
                                                <th class="border-0">Taille</th>
                                                <th class="border-0">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($analyses as $analyse)
                                                <tr>
                                                    <td>
                                                        <div class="fw-semibold">{{ $analyse->created_at->format('d/m/Y') }}</div>
                                                        <small class="text-muted">{{ $analyse->created_at->format('H:i') }}</small>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-microscope text-primary me-2"></i>
                                                            <div>
                                                                <div class="fw-semibold">Analyse Cytologique</div>
                                                                <small class="text-muted">ID: #{{ $analyse->id }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $analyse->niveau_risque === 'Élevé' ? 'danger' : ($analyse->niveau_risque === 'Modéré' ? 'warning' : 'success') }}">
                                                            {{ $analyse->classe_predite }}
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
                                                        @endphp
                                                        <span class="badge bg-{{ $statusClass }}">
                                                            {{ $analyse->statut }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">
                                                            <i class="fas fa-file-pdf me-1"></i>~2.5 MB
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="{{ route('patient.analyse.show', $analyse) }}" 
                                                               class="btn btn-outline-primary" title="Voir détails">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            @if($analyse->statut === 'Validé')
                                                                <a href="{{ route('patient.analyse.download', $analyse) }}" 
                                                                   class="btn btn-outline-success" title="Télécharger PDF">
                                                                    <i class="fas fa-download"></i>
                                                                </a>
                                                                <button class="btn btn-outline-info" 
                                                                        onclick="previewReport({{ $analyse->id }})" 
                                                                        title="Aperçu">
                                                                    <i class="fas fa-search"></i>
                                                                </button>
                                                            @else
                                                                <button class="btn btn-outline-secondary" disabled title="En attente de validation">
                                                                    <i class="fas fa-clock"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-file-medical fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">Aucun rapport disponible</h5>
                                    <p class="text-muted">Vos rapports d'analyses apparaîtront ici une fois validés</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Panneau latéral -->
                <div class="col-lg-4">
                    <!-- Actions rapides -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0">
                                <i class="fas fa-bolt text-primary me-2"></i>
                                Actions Rapides
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary" onclick="downloadAll()">
                                    <i class="fas fa-download me-2"></i>
                                    Télécharger tous les rapports
                                </button>
                                
                                <a href="{{ route('patient.dashboard') }}" class="btn btn-outline-info">
                                    <i class="fas fa-tachometer-alt me-2"></i>
                                    Mon Dashboard
                                </a>
                                
                                <button class="btn btn-outline-success" onclick="exportData()">
                                    <i class="fas fa-file-export me-2"></i>
                                    Exporter mes données
                                </button>
                                
                                <button class="btn btn-outline-secondary" onclick="window.print()">
                                    <i class="fas fa-print me-2"></i>
                                    Imprimer cette page
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Informations utiles -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle text-info me-2"></i>
                                Informations Utiles
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6 class="text-primary">
                                    <i class="fas fa-file-pdf me-2"></i>Format des rapports
                                </h6>
                                <p class="small text-muted mb-0">
                                    Tous les rapports sont au format PDF et incluent vos images d'analyses, 
                                    résultats détaillés et recommandations médicales.
                                </p>
                            </div>
                            
                            <div class="mb-3">
                                <h6 class="text-success">
                                    <i class="fas fa-shield-alt me-2"></i>Sécurité
                                </h6>
                                <p class="small text-muted mb-0">
                                    Vos documents sont sécurisés et accessibles uniquement avec votre compte patient.
                                </p>
                            </div>
                            
                            <div class="mb-0">
                                <h6 class="text-warning">
                                    <i class="fas fa-clock me-2"></i>Disponibilité
                                </h6>
                                <p class="small text-muted mb-0">
                                    Les rapports sont disponibles immédiatement après validation par votre médecin.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Ressources supplémentaires -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0">
                                <i class="fas fa-book text-success me-2"></i>
                                Ressources
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="#" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-question-circle me-2"></i>
                                    Guide de lecture des résultats
                                </a>
                                
                                <a href="#" class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-phone me-2"></i>
                                    Contacter mon médecin
                                </a>
                                
                                <a href="{{ route('patient.appointments') }}" class="btn btn-outline-warning btn-sm">
                                    <i class="fas fa-calendar me-2"></i>
                                    Prendre rendez-vous
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'aperçu -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-search me-2"></i>Aperçu du Rapport
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="previewContent" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" id="downloadFromPreview">
                    <i class="fas fa-download me-1"></i>Télécharger
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function downloadAll() {
    // Simuler le téléchargement de tous les rapports
    const validatedAnalyses = document.querySelectorAll('tbody tr');
    let downloadCount = 0;
    
    validatedAnalyses.forEach((row, index) => {
        const downloadBtn = row.querySelector('.btn-outline-success');
        if (downloadBtn && !downloadBtn.disabled) {
            setTimeout(() => {
                downloadBtn.click();
                downloadCount++;
            }, index * 500); // Délai entre chaque téléchargement
        }
    });
    
    if (downloadCount > 0) {
        showNotification('Téléchargement de ' + downloadCount + ' rapport(s) en cours...', 'info');
    } else {
        showNotification('Aucun rapport disponible pour le téléchargement', 'warning');
    }
}

function previewReport(analyseId) {
    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    const previewContent = document.getElementById('previewContent');
    
    // Afficher le spinner
    previewContent.innerHTML = `
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Chargement...</span>
        </div>
        <p class="mt-2">Génération de l'aperçu...</p>
    `;
    
    modal.show();
    
    // Simuler le chargement de l'aperçu
    setTimeout(() => {
        previewContent.innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Aperçu du Rapport #${analyseId}</strong>
            </div>
            <div class="text-start">
                <h6>Contenu du rapport :</h6>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success me-2"></i>Informations patient</li>
                    <li><i class="fas fa-check text-success me-2"></i>Image d'analyse</li>
                    <li><i class="fas fa-check text-success me-2"></i>Résultats IA détaillés</li>
                    <li><i class="fas fa-check text-success me-2"></i>Recommandations médicales</li>
                    <li><i class="fas fa-check text-success me-2"></i>Signature du médecin</li>
                </ul>
            </div>
        `;
    }, 1500);
}

function exportData() {
    showNotification('Préparation de l\'export de vos données...', 'info');
    
    // Simuler l'export
    setTimeout(() => {
        showNotification('Export terminé ! Vérifiez vos téléchargements.', 'success');
    }, 2000);
}

function showNotification(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 
                      type === 'warning' ? 'alert-warning' : 
                      type === 'error' ? 'alert-danger' : 'alert-info';
    
    const notification = document.createElement('div');
    notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}
</script>

<style>
.card {
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,.075);
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
}

.badge {
    font-size: 0.75em;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}
</style>
@endsection