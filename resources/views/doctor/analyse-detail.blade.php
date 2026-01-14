@extends('layouts.app')

@section('title', 'Validation d\'Analyse')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-clipboard-check text-success me-2"></i>
                @if($analyse->valide_par && $analyse->date_validation)
                    Modification de Validation
                @else
                    Validation d'Analyse IA
                @endif
            </h1>
            <p class="text-muted mb-0">Patient: {{ $analyse->patient->nom_complet }} - {{ $analyse->patient->numero_dossier }}</p>
            @if($analyse->date_derniere_modification)
                <small class="text-info">
                    <i class="fas fa-edit me-1"></i>
                    Dernière modification: {{ $analyse->date_derniere_modification->format('d/m/Y H:i') }}
                </small>
            @endif
        </div>
        <div>
            <a href="{{ route('doctor.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour au Dashboard
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Image et informations de base -->
        <div class="col-lg-6">
            <!-- Image analysée -->
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

            <!-- Informations patient -->
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
                    @if($analyse->patient->antecedents_medicaux)
                        <hr>
                        <small class="text-muted">Antécédents médicaux:</small>
                        <div class="bg-light p-2 rounded mt-1">
                            <small>{{ $analyse->patient->antecedents_medicaux }}</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Résultats IA et validation -->
        <div class="col-lg-6">
            <!-- Résultat de l'IA -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-robot me-2"></i>Résultat de l'Intelligence Artificielle
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

                    <!-- Interprétation IA -->
                    <div class="mb-3">
                        <small class="text-muted">Interprétation de l'IA:</small>
                        <p class="mb-0">{{ $analyse->interpretation }}</p>
                    </div>

                    <!-- Recommandations IA -->
                    @if($analyse->recommandations && count($analyse->recommandations) > 0)
                        <div class="mb-3">
                            <small class="text-muted">Recommandations de l'IA:</small>
                            <ul class="list-unstyled mb-0 mt-1">
                                @foreach($analyse->recommandations as $recommandation)
                                    <li class="mb-1">
                                        <i class="fas fa-check-circle text-success me-1"></i>
                                        <small>{{ $recommandation }}</small>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Informations d'analyse -->
                    <div class="bg-light p-3 rounded">
                        <div class="row text-center">
                            <div class="col-6">
                                <small class="text-muted">Analysé par</small>
                                <div class="fw-bold">{{ $analyse->analyste->name }}</div>
                                <small class="text-muted">{{ $analyse->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Statut actuel</small>
                                <div>
                                    <span class="badge bg-{{ $analyse->statut_color }}">{{ $analyse->statut }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulaire de validation -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-stethoscope me-2"></i>Validation Médicale
                    </h5>
                </div>
                <div class="card-body">
                    <form id="validation-form" method="POST" action="{{ route('doctor.analyse.valider', $analyse) }}">
                        @csrf
                        
                        <!-- Décision -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Votre décision <span class="text-danger">*</span></label>
                            @if($analyse->valide_par && $analyse->date_validation)
                                <div class="alert alert-info mb-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>Modification:</strong> Vous pouvez changer votre validation précédente.
                                </div>
                            @endif
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="decision" id="valide" value="valide" 
                                               {{ $analyse->decision_medecin === 'valide' ? 'checked' : '' }} required>
                                        <label class="form-check-label text-success fw-bold" for="valide">
                                            <i class="fas fa-check-circle me-1"></i>Valider
                                        </label>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="decision" id="a_revoir" value="a_revoir" 
                                               {{ $analyse->decision_medecin === 'a_revoir' ? 'checked' : '' }} required>
                                        <label class="form-check-label text-warning fw-bold" for="a_revoir">
                                            <i class="fas fa-exclamation-triangle me-1"></i>À revoir
                                        </label>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="decision" id="rejete" value="rejete" 
                                               {{ $analyse->decision_medecin === 'rejete' ? 'checked' : '' }} required>
                                        <label class="form-check-label text-danger fw-bold" for="rejete">
                                            <i class="fas fa-times-circle me-1"></i>Rejeter
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Classe finale (si différente) -->
                        <div class="mb-3">
                            <label for="classe_finale" class="form-label">Classe finale (si différente de l'IA)</label>
                            <select class="form-select" id="classe_finale" name="classe_finale">
                                <option value="">Garder la classe IA: {{ $analyse->classe_predite }}</option>
                                <option value="Dyskeratotic" {{ ($analyse->classe_finale_medecin ?? $analyse->classe_predite) == 'Dyskeratotic' ? 'selected' : '' }}>Dyskeratotic</option>
                                <option value="Koilocytotic" {{ ($analyse->classe_finale_medecin ?? $analyse->classe_predite) == 'Koilocytotic' ? 'selected' : '' }}>Koilocytotic</option>
                                <option value="Metaplastic" {{ ($analyse->classe_finale_medecin ?? $analyse->classe_predite) == 'Metaplastic' ? 'selected' : '' }}>Metaplastic</option>
                                <option value="Parabasal" {{ ($analyse->classe_finale_medecin ?? $analyse->classe_predite) == 'Parabasal' ? 'selected' : '' }}>Parabasal</option>
                                <option value="Superficial-Intermediate" {{ ($analyse->classe_finale_medecin ?? $analyse->classe_predite) == 'Superficial-Intermediate' ? 'selected' : '' }}>Superficial-Intermediate</option>
                            </select>
                            <div class="form-text">Sélectionnez une classe différente si vous n'êtes pas d'accord avec l'IA</div>
                        </div>

                        <!-- Commentaires médicaux -->
                        <div class="mb-3">
                            <label for="commentaires_medecin" class="form-label">Commentaires médicaux</label>
                            <textarea class="form-control" id="commentaires_medecin" name="commentaires_medecin" rows="4" 
                                      placeholder="Vos observations, justifications de la décision...">{{ $analyse->commentaires_medecin }}</textarea>
                        </div>

                        <!-- Recommandations finales -->
                        <div class="mb-4">
                            <label for="recommandations_finales" class="form-label">Recommandations finales</label>
                            <textarea class="form-control" id="recommandations_finales" name="recommandations_finales" rows="3" 
                                      placeholder="Recommandations pour le suivi du patient...">{{ $analyse->recommandations_finales }}</textarea>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                @if($analyse->valide_par && $analyse->date_validation)
                                    <i class="fas fa-edit me-2"></i>Modifier ma Validation
                                @else
                                    <i class="fas fa-check me-2"></i>Enregistrer ma Validation
                                @endif
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Détail des probabilités -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Détail des Probabilités par Classe
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($analyse->toutes_probabilites as $classe => $probabilite)
                            <div class="col-md-4 col-lg-2 mb-3">
                                <div class="card {{ $classe === $analyse->classe_predite ? 'border-primary' : 'border-light' }}">
                                    <div class="card-body text-center p-3">
                                        <h6 class="card-title small {{ $classe === $analyse->classe_predite ? 'text-primary' : '' }}">
                                            {{ $classe }}
                                            @if($classe === $analyse->classe_predite)
                                                <i class="fas fa-check-circle text-primary ms-1"></i>
                                            @endif
                                        </h6>
                                        <h5 class="{{ $classe === $analyse->classe_predite ? 'text-primary' : 'text-muted' }}">
                                            {{ round($probabilite * 100, 1) }}%
                                        </h5>
                                        <div class="progress" style="height: 4px;">
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
        </div>
    </div>
</div>

<script>
document.getElementById('validation-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const decision = formData.get('decision');
    
    if (!decision) {
        alert('Veuillez sélectionner une décision');
        return;
    }
    
    if (confirm(`Êtes-vous sûr de vouloir ${decision === 'valide' ? 'valider' : decision === 'rejete' ? 'rejeter' : 'marquer à revoir'} cette analyse ?`)) {
        this.submit();
    }
});
</script>
@endsection