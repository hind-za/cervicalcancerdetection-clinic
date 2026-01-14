@extends('layouts.app')

@section('title', 'Mon Espace Patient')

@section('content')
<div class="container-fluid">
    <!-- En-tête simplifié -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-user-circle text-primary me-2"></i>
                Bonjour, {{ $patientData->prenom }} {{ $patientData->nom }}
            </h1>
            <p class="text-muted mb-0">Tableau de bord de vos analyses cytologiques</p>
        </div>
        <div class="text-end">
            <span class="badge bg-info fs-6">Dossier: {{ $patientData->numero_dossier ?? 'En attente' }}</span>
        </div>
    </div>

    <!-- Statistiques principales -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-microscope fa-2x text-primary mb-2"></i>
                    <h4 class="text-primary mb-1">{{ $stats['total_analyses'] }}</h4>
                    <small class="text-muted">Analyses effectuées</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h4 class="text-success mb-1">{{ $stats['analyses_validees'] }}</h4>
                    <small class="text-muted">Résultats validés</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <h4 class="text-warning mb-1">{{ $stats['analyses_en_attente'] }}</h4>
                    <small class="text-muted">En attente</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                    <h4 class="text-danger mb-1">{{ $stats['risque_eleve'] }}</h4>
                    <small class="text-muted">Risque élevé</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Analyses récentes -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-history text-primary me-2"></i>
                            Mes Dernières Analyses
                        </h5>
                        <a href="{{ route('patient.analyses') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-list me-1"></i>Voir tout l'historique
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($recentAnalyses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0">Date</th>
                                        <th class="border-0">Image</th>
                                        <th class="border-0">Résultat IA</th>
                                        <th class="border-0">Confiance</th>
                                        <th class="border-0">Statut</th>
                                        <th class="border-0">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentAnalyses as $analyse)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $analyse->created_at->format('d/m/Y') }}</div>
                                                <small class="text-muted">{{ $analyse->created_at->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                <img src="{{ route('secure.image.show', basename($analyse->chemin_image)) }}" 
                                                     class="history-image" style="width: 35px; height: 35px; object-fit: cover;" 
                                                     alt="Analyse"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="image-placeholder history-image" style="width: 35px; height: 35px; display: none; font-size: 8px;">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $analyse->niveau_risque === 'Élevé' ? 'danger' : ($analyse->niveau_risque === 'Modéré' ? 'warning' : 'success') }}">
                                                    {{ $analyse->classe_predite }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress me-2" style="width: 50px; height: 6px;">
                                                        <div class="progress-bar bg-{{ $analyse->niveau_risque === 'Élevé' ? 'danger' : ($analyse->niveau_risque === 'Modéré' ? 'warning' : 'success') }}" 
                                                             style="width: {{ round($analyse->probabilite * 100) }}%"></div>
                                                    </div>
                                                    <small class="text-muted">{{ round($analyse->probabilite * 100) }}%</small>
                                                </div>
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
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('patient.analyse.show', $analyse) }}" 
                                                       class="btn btn-outline-primary" title="Voir détails">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($analyse->statut === 'Validé')
                                                        <a href="{{ route('patient.analyse.download', $analyse) }}" 
                                                           class="btn btn-outline-success" title="Télécharger rapport">
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
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-microscope fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune analyse disponible</h5>
                            <p class="text-muted">Vos analyses cytologiques apparaîtront ici une fois effectuées</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Panneau latéral -->
        <div class="col-lg-4">
            <!-- Dernière analyse en détail -->
            @if($recentAnalyses->count() > 0)
                @php $lastAnalyse = $recentAnalyses->first(); @endphp
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">
                            <i class="fas fa-star text-warning me-2"></i>
                            Dernière Analyse
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <img src="{{ route('secure.image.show', basename($lastAnalyse->chemin_image)) }}" 
                                 class="rounded" style="width: 80px; height: 80px; object-fit: cover;" 
                                 alt="Dernière analyse"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="image-placeholder rounded" style="width: 80px; height: 80px; display: none; margin: 0 auto;">
                                <i class="fas fa-image"></i>
                            </div>
                        </div>
                        
                        <div class="text-center mb-3">
                            <h6 class="mb-1">{{ $lastAnalyse->created_at->format('d/m/Y à H:i') }}</h6>
                            <span class="badge bg-{{ $lastAnalyse->niveau_risque === 'Élevé' ? 'danger' : ($lastAnalyse->niveau_risque === 'Modéré' ? 'warning' : 'success') }} fs-6">
                                {{ $lastAnalyse->classe_predite }}
                            </span>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small>Confiance IA</small>
                                <small>{{ round($lastAnalyse->probabilite * 100) }}%</small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-{{ $lastAnalyse->niveau_risque === 'Élevé' ? 'danger' : ($lastAnalyse->niveau_risque === 'Modéré' ? 'warning' : 'success') }}" 
                                     style="width: {{ round($lastAnalyse->probabilite * 100) }}%"></div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="{{ route('patient.analyse.show', $lastAnalyse) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>Voir en détail
                            </a>
                            @if($lastAnalyse->statut === 'Validé')
                                <a href="{{ route('patient.analyse.download', $lastAnalyse) }}" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-download me-1"></i>Télécharger rapport
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

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
                        <a href="{{ route('patient.analyses') }}" class="btn btn-primary">
                            <i class="fas fa-list me-2"></i>
                            Historique complet
                        </a>
                        
                        <a href="{{ route('patient.appointments') }}" class="btn btn-outline-info">
                            <i class="fas fa-calendar me-2"></i>
                            Mes rendez-vous
                        </a>
                        
                        <button class="btn btn-outline-secondary" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>
                            Imprimer résumé
                        </button>
                        
                        <div class="dropdown">
                            <button class="btn btn-outline-success dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-download me-2"></i>Téléchargements
                            </button>
                            <ul class="dropdown-menu w-100">
                                @foreach($recentAnalyses->where('statut', 'Validé')->take(3) as $analyse)
                                    <li>
                                        <a class="dropdown-item" href="{{ route('patient.analyse.download', $analyse) }}">
                                            <i class="fas fa-file-pdf me-2"></i>
                                            Rapport {{ $analyse->created_at->format('d/m/Y') }}
                                        </a>
                                    </li>
                                @endforeach
                                @if($recentAnalyses->where('statut', 'Validé')->count() == 0)
                                    <li><span class="dropdown-item text-muted">Aucun rapport disponible</span></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations patient -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-user text-primary me-2"></i>
                        Mes Informations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="mb-2">
                                <strong>{{ $patientData->age ?? 'N/A' }}</strong>
                                <div><small class="text-muted">Âge</small></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-2">
                                <strong>{{ isset($patientData->sexe) ? ($patientData->sexe === 'F' ? 'F' : 'M') : 'N/A' }}</strong>
                                <div><small class="text-muted">Sexe</small></div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <small class="text-muted">Email:</small><br>
                        <strong>{{ $patientData->email }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Évolution des analyses (si plusieurs analyses) -->
    @if($stats['total_analyses'] > 1)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line text-primary me-2"></i>
                            Évolution de mes Analyses
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($recentAnalyses->take(6) as $analyse)
                                <div class="col-md-2 col-4">
                                    <div class="text-center mb-3">
                                        <div class="mb-2">
                                            <span class="badge bg-{{ $analyse->niveau_risque === 'Élevé' ? 'danger' : ($analyse->niveau_risque === 'Modéré' ? 'warning' : 'success') }} fs-6">
                                                {{ $analyse->classe_predite }}
                                            </span>
                                        </div>
                                        <small class="text-muted">
                                            {{ $analyse->created_at->format('d/m') }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection