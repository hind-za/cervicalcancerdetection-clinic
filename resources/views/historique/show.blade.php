@extends('layouts.app')

@section('title', 'Détail de l\'Analyse')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-microscope text-primary me-2"></i>
                Détail de l'Analyse IA
            </h1>
            <p class="text-muted mb-0">Analyse effectuée le {{ $analyse->created_at->format('d/m/Y à H:i') }}</p>
        </div>
        <div>
            <a href="{{ route('historique.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour à l'historique
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Image et informations de base -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-image me-2"></i>Image Analysée
                    </h5>
                </div>
                <div class="card-body text-center">
                    <img src="{{ route('secure.image.show', basename($analyse->chemin_image)) }}" 
                         class="img-fluid rounded shadow mb-3" 
                         style="max-height: 400px;"
                         alt="Analyse {{ $analyse->nom_image }}">
                    
                    <div class="row text-start">
                        <div class="col-6">
                            <small class="text-muted">Nom du fichier:</small>
                            <p class="mb-2"><strong>{{ $analyse->nom_image }}</strong></p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Taille:</small>
                            <p class="mb-2"><strong>{{ $analyse->taille_image }}</strong></p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Dimensions:</small>
                            <p class="mb-2"><strong>{{ $analyse->dimensions_image }}</strong></p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Temps d'analyse:</small>
                            <p class="mb-2"><strong>{{ $analyse->temps_analyse }}s</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Résultats de l'analyse -->
        <div class="col-lg-6">
            <!-- Patient -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i>Informations Patient
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Nom complet:</small>
                            <p class="mb-2"><strong>{{ $analyse->patient->nom_complet }}</strong></p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">N° Dossier:</small>
                            <p class="mb-2"><strong>{{ $analyse->patient->numero_dossier }}</strong></p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Date de naissance:</small>
                            <p class="mb-2"><strong>{{ $analyse->patient->date_naissance->format('d/m/Y') }}</strong></p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Âge:</small>
                            <p class="mb-2"><strong>{{ $analyse->patient->age }} ans</strong></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Résultat IA -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-brain me-2"></i>Résultat de l'IA
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Classe prédite -->
                    <div class="text-center mb-4">
                        <h3 class="text-{{ $analyse->risque_color }} mb-2">{{ $analyse->classe_predite }}</h3>
                        <div class="d-flex justify-content-center align-items-center mb-2">
                            <span class="badge bg-{{ $analyse->risque_color }} fs-6 me-2">{{ $analyse->confidence_percent }}%</span>
                            <span class="badge bg-{{ $analyse->risque_color }}">{{ $analyse->niveau_risque }}</span>
                        </div>
                        <div class="progress mx-auto" style="width: 200px; height: 8px;">
                            <div class="progress-bar bg-{{ $analyse->risque_color }}" 
                                 style="width: {{ $analyse->confidence_percent }}%"></div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <small class="text-muted">Description:</small>
                        <p class="mb-0">{{ $analyse->classe_description }}</p>
                    </div>

                    <!-- Interprétation -->
                    <div class="mb-3">
                        <small class="text-muted">Interprétation médicale:</small>
                        <p class="mb-0">{{ $analyse->interpretation }}</p>
                    </div>

                    <!-- Statut -->
                    <div class="text-center">
                        <span class="badge bg-{{ $analyse->statut_color }} fs-6">{{ $analyse->statut }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toutes les probabilités -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">
                <i class="fas fa-chart-bar me-2"></i>Détail des Probabilités par Classe
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($analyse->toutes_probabilites as $classe => $probabilite)
                    <div class="col-md-4 mb-3">
                        <div class="card {{ $classe === $analyse->classe_predite ? 'border-primary' : 'border-light' }}">
                            <div class="card-body text-center">
                                <h6 class="card-title {{ $classe === $analyse->classe_predite ? 'text-primary' : '' }}">
                                    {{ $classe }}
                                    @if($classe === $analyse->classe_predite)
                                        <i class="fas fa-check-circle text-primary ms-1"></i>
                                    @endif
                                </h6>
                                <h4 class="{{ $classe === $analyse->classe_predite ? 'text-primary' : 'text-muted' }}">
                                    {{ round($probabilite * 100, 1) }}%
                                </h4>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar {{ $classe === $analyse->classe_predite ? 'bg-primary' : 'bg-secondary' }}" 
                                         style="width: {{ $probabilite * 100 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recommandations -->
    @if($analyse->recommandations && count($analyse->recommandations) > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-stethoscope me-2"></i>Recommandations Médicales
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    @foreach($analyse->recommandations as $recommandation)
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            {{ $recommandation }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Informations de validation -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">
                <i class="fas fa-info-circle me-2"></i>Informations de Traçabilité
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <small class="text-muted">Analysé par:</small>
                    <p class="mb-2"><strong>{{ $analyse->analyste->name }}</strong></p>
                    
                    <small class="text-muted">Date d'analyse:</small>
                    <p class="mb-2"><strong>{{ $analyse->created_at->format('d/m/Y à H:i:s') }}</strong></p>
                </div>
                <div class="col-md-6">
                    @if($analyse->validateur)
                        <small class="text-muted">Validé par:</small>
                        <p class="mb-2"><strong>{{ $analyse->validateur->name }}</strong></p>
                        
                        <small class="text-muted">Date de validation:</small>
                        <p class="mb-2"><strong>{{ $analyse->date_validation->format('d/m/Y à H:i:s') }}</strong></p>
                    @else
                        <p class="text-muted">En attente de validation</p>
                    @endif
                </div>
            </div>

            @if($analyse->commentaires_medecin)
                <hr>
                <small class="text-muted">Commentaires du médecin:</small>
                <div class="bg-light p-3 rounded mt-2">
                    <p class="mb-0">{{ $analyse->commentaires_medecin }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Actions -->
    @if(auth()->check() && auth()->user()->role === 'admin' && $analyse->statut !== 'Validé')
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-cogs me-2"></i>Actions
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.save-analysis') }}">
                    @csrf
                    <input type="hidden" name="analysis_id" value="{{ $analyse->id }}">
                    
                    <div class="mb-3">
                        <label for="commentaires" class="form-label">Commentaires médicaux</label>
                        <textarea class="form-control" id="commentaires" name="commentaires" rows="3" 
                                  placeholder="Ajoutez vos commentaires...">{{ $analyse->commentaires_medecin }}</textarea>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-1"></i>Valider l'analyse
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection