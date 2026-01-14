@extends('layouts.app')

@section('title', 'Patient - ' . $patient->nom_complet)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">
            <i class="fas fa-user text-primary me-2"></i>
            {{ $patient->nom_complet }}
        </h1>
        <p class="text-muted mb-0">Dossier N° {{ $patient->numero_dossier }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('patients.edit', $patient) }}" class="btn btn-outline-warning">
            <i class="fas fa-edit me-2"></i>Modifier
        </a>
        <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <!-- Informations du patient -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-id-card me-2"></i>Informations Patient
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 80px; height: 80px;">
                        <i class="fas fa-user fa-2x text-primary"></i>
                    </div>
                    <h5 class="mt-2 mb-0">{{ $patient->nom_complet }}</h5>
                    <span class="badge bg-{{ $patient->sexe == 'F' ? 'pink' : 'blue' }}">
                        {{ $patient->sexe == 'F' ? 'Femme' : 'Homme' }}
                    </span>
                </div>
                
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="text-center p-2 bg-light rounded">
                            <div class="fw-bold text-primary">{{ $patient->age }}</div>
                            <small class="text-muted">ans</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-2 bg-light rounded">
                            <div class="fw-bold text-success">{{ $patient->analyses->count() }}</div>
                            <small class="text-muted">analyses</small>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="mb-3">
                    <h6 class="text-primary mb-2">Contact</h6>
                    @if($patient->telephone)
                        <div class="mb-1">
                            <i class="fas fa-phone text-muted me-2"></i>
                            <a href="tel:{{ $patient->telephone }}">{{ $patient->telephone }}</a>
                        </div>
                    @endif
                    @if($patient->email)
                        <div class="mb-1">
                            <i class="fas fa-envelope text-muted me-2"></i>
                            <a href="mailto:{{ $patient->email }}">{{ $patient->email }}</a>
                        </div>
                    @endif
                    @if($patient->adresse)
                        <div class="mb-1">
                            <i class="fas fa-map-marker-alt text-muted me-2"></i>
                            {{ $patient->adresse }}
                        </div>
                    @endif
                </div>
                
                @if($patient->antecedents_medicaux)
                    <div class="mb-3">
                        <h6 class="text-primary mb-2">Antécédents Médicaux</h6>
                        <p class="small text-muted">{{ $patient->antecedents_medicaux }}</p>
                    </div>
                @endif
                
                @if($patient->notes)
                    <div class="mb-3">
                        <h6 class="text-primary mb-2">Notes</h6>
                        <p class="small text-muted">{{ $patient->notes }}</p>
                    </div>
                @endif
                
                <div class="text-muted small">
                    <i class="fas fa-calendar me-1"></i>
                    Créé le {{ $patient->created_at->format('d/m/Y à H:i') }}
                </div>
            </div>
        </div>
    </div>
    
    <!-- Analyses et ajout d'analyse -->
    <div class="col-lg-8">
        <!-- Formulaire d'ajout d'analyse -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-plus-circle me-2"></i>Nouvelle Analyse
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('patients.add-analyse', $patient) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="image" class="form-label">Image d'Analyse <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*" required>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Formats: JPG, PNG, TIFF - Max: 10MB</div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="resultat" class="form-label">Résultat <span class="text-danger">*</span></label>
                            <select class="form-select @error('resultat') is-invalid @enderror" id="resultat" name="resultat" required>
                                <option value="">Sélectionner...</option>
                                <option value="Normal">Normal</option>
                                <option value="Anomalie Détectée">Anomalie Détectée</option>
                                <option value="À Surveiller">À Surveiller</option>
                            </select>
                            @error('resultat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="confiance" class="form-label">Confiance (%) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('confiance') is-invalid @enderror" 
                                   id="confiance" name="confiance" min="0" max="100" step="0.1" required>
                            @error('confiance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="commentaires" class="form-label">Commentaires</label>
                            <textarea class="form-control @error('commentaires') is-invalid @enderror" 
                                      id="commentaires" name="commentaires" rows="2" 
                                      placeholder="Commentaires sur l'analyse..."></textarea>
                            @error('commentaires')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Ajouter l'Analyse
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Liste des analyses -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">
                    <i class="fas fa-images text-primary me-2"></i>
                    Analyses ({{ $patient->analyses->count() }})
                </h5>
            </div>
            <div class="card-body">
                @if($patient->analyses->count() > 0)
                    <div class="row">
                        @foreach($patient->analyses as $analyse)
                            <div class="col-md-6 mb-4">
                                <div class="card border h-100">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ $analyse->created_at->format('d/m/Y H:i') }}
                                        </small>
                                        <span class="badge bg-{{ $analyse->statut_color }}">{{ $analyse->statut }}</span>
                                    </div>
                                    <div class="card-body p-3">
                                        <!-- Image -->
                                        <div class="text-center mb-3">
                                            <img src="{{ route('secure.image.show', basename($analyse->chemin_image)) }}" 
                                                 class="img-fluid rounded" style="max-height: 150px;" 
                                                 alt="Analyse {{ $analyse->id }}">
                                        </div>
                                        
                                        <!-- Résultat -->
                                        <div class="text-center mb-3">
                                            <span class="badge bg-{{ $analyse->resultat_color }} fs-6 p-2">
                                                {{ $analyse->resultat }}
                                            </span>
                                            <div class="mt-2">
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar bg-{{ $analyse->resultat_color }}" 
                                                         style="width: {{ $analyse->confiance }}%"></div>
                                                </div>
                                                <small class="text-muted">{{ $analyse->confiance }}% de confiance</small>
                                            </div>
                                        </div>
                                        
                                        <!-- Détails -->
                                        @if($analyse->details)
                                            <div class="mb-2">
                                                <small class="text-muted">
                                                    <strong>Détails:</strong><br>
                                                    @php
                                                        $details = $analyse->details;
                                                        // S'assurer que details est un tableau
                                                        if (is_string($details)) {
                                                            $details = json_decode($details, true) ?: [];
                                                        }
                                                        if (!is_array($details)) {
                                                            $details = [];
                                                        }
                                                    @endphp
                                                    
                                                    @if(count($details) > 0)
                                                        @foreach($details as $key => $value)
                                                            • {{ ucfirst(str_replace('_', ' ', $key)) }}: 
                                                            @if(is_array($value))
                                                                {{ implode(', ', $value) }}
                                                            @else
                                                                {{ $value }}
                                                            @endif
                                                            <br>
                                                        @endforeach
                                                    @else
                                                        <em>Aucun détail disponible</em>
                                                    @endif
                                                </small>
                                            </div>
                                        @endif
                                        
                                        @if($analyse->commentaires)
                                            <div class="mb-2">
                                                <small class="text-muted">
                                                    <strong>Commentaires:</strong><br>
                                                    {{ $analyse->commentaires }}
                                                </small>
                                            </div>
                                        @endif
                                        
                                        @if($analyse->temps_analyse)
                                            <div class="text-center">
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    Analysé en {{ $analyse->temps_analyse }}s
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-footer bg-light text-center">
                                        <button class="btn btn-sm btn-outline-primary me-1" title="Télécharger">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-info me-1" title="Rapport PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </button>
                                        @if($analyse->statut == 'En attente')
                                            <button class="btn btn-sm btn-outline-success" title="Valider">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-images fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">Aucune analyse pour ce patient</h6>
                        <p class="text-muted">Utilisez le formulaire ci-dessus pour ajouter la première analyse</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
// Prévisualisation de l'image
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Créer ou mettre à jour la prévisualisation
            let preview = document.getElementById('imagePreview');
            if (!preview) {
                preview = document.createElement('img');
                preview.id = 'imagePreview';
                preview.className = 'img-fluid rounded mt-2';
                preview.style.maxHeight = '200px';
                document.getElementById('image').parentNode.appendChild(preview);
            }
            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

// Suggestion automatique de confiance basée sur le résultat
document.getElementById('resultat').addEventListener('change', function() {
    const confianceInput = document.getElementById('confiance');
    const suggestions = {
        'Normal': 95,
        'À Surveiller': 78,
        'Anomalie Détectée': 87
    };
    
    if (suggestions[this.value]) {
        confianceInput.value = suggestions[this.value];
    }
});
</script>
@endsection