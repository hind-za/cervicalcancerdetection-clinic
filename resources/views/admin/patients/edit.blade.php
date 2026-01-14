@extends('layouts.app')

@section('title', 'Modifier Patient - ' . $patient->nom_complet)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">
            <i class="fas fa-user-edit text-warning me-2"></i>
            Modifier Patient
        </h1>
        <p class="text-muted mb-0">{{ $patient->nom_complet }} - Dossier N° {{ $patient->numero_dossier }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-primary">
            <i class="fas fa-eye me-2"></i>Voir
        </a>
        <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0">
                    <i class="fas fa-user-edit me-2"></i>
                    Modifier les Informations
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('patients.update', $patient) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Informations personnelles -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-warning border-bottom pb-2 mb-3">
                                <i class="fas fa-id-card me-2"></i>Informations Personnelles
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                   id="nom" name="nom" value="{{ old('nom', $patient->nom) }}" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('prenom') is-invalid @enderror" 
                                   id="prenom" name="prenom" value="{{ old('prenom', $patient->prenom) }}" required>
                            @error('prenom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="date_naissance" class="form-label">Date de Naissance <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date_naissance') is-invalid @enderror" 
                                   id="date_naissance" name="date_naissance" 
                                   value="{{ old('date_naissance', $patient->date_naissance->format('Y-m-d')) }}" required>
                            @error('date_naissance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="sexe" class="form-label">Sexe <span class="text-danger">*</span></label>
                            <select class="form-select @error('sexe') is-invalid @enderror" id="sexe" name="sexe" required>
                                <option value="">Sélectionner...</option>
                                <option value="F" {{ old('sexe', $patient->sexe) == 'F' ? 'selected' : '' }}>Femme</option>
                                <option value="M" {{ old('sexe', $patient->sexe) == 'M' ? 'selected' : '' }}>Homme</option>
                            </select>
                            @error('sexe')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Informations de contact -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-warning border-bottom pb-2 mb-3">
                                <i class="fas fa-address-book me-2"></i>Informations de Contact
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="tel" class="form-control @error('telephone') is-invalid @enderror" 
                                   id="telephone" name="telephone" value="{{ old('telephone', $patient->telephone) }}" 
                                   placeholder="+212 6XX XXX XXX">
                            @error('telephone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $patient->email) }}" 
                                   placeholder="patient@example.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="adresse" class="form-label">Adresse</label>
                            <textarea class="form-control @error('adresse') is-invalid @enderror" 
                                      id="adresse" name="adresse" rows="3" 
                                      placeholder="Adresse complète du patient">{{ old('adresse', $patient->adresse) }}</textarea>
                            @error('adresse')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Informations médicales -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-warning border-bottom pb-2 mb-3">
                                <i class="fas fa-notes-medical me-2"></i>Informations Médicales
                            </h6>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="antecedents_medicaux" class="form-label">Antécédents Médicaux</label>
                            <textarea class="form-control @error('antecedents_medicaux') is-invalid @enderror" 
                                      id="antecedents_medicaux" name="antecedents_medicaux" rows="4" 
                                      placeholder="Antécédents médicaux, allergies, traitements en cours...">{{ old('antecedents_medicaux', $patient->antecedents_medicaux) }}</textarea>
                            @error('antecedents_medicaux')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="notes" class="form-label">Notes Additionnelles</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3" 
                                      placeholder="Notes supplémentaires...">{{ old('notes', $patient->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Boutons d'action -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-warning text-white">
                            <i class="fas fa-save me-2"></i>Mettre à Jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Informations du patient -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Informations Actuelles
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-user fa-lg text-primary"></i>
                    </div>
                    <h6 class="mt-2 mb-0">{{ $patient->nom_complet }}</h6>
                    <small class="text-muted">{{ $patient->numero_dossier }}</small>
                </div>
                
                <div class="mb-3">
                    <strong>Âge:</strong> {{ $patient->age }} ans<br>
                    <strong>Sexe:</strong> {{ $patient->sexe == 'F' ? 'Femme' : 'Homme' }}<br>
                    <strong>Analyses:</strong> {{ $patient->analyses->count() }}
                </div>
                
                <div class="mb-3">
                    <small class="text-muted">
                        <i class="fas fa-calendar me-1"></i>
                        Créé le {{ $patient->created_at->format('d/m/Y') }}
                    </small>
                </div>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-warning text-white">
                <h6 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>Attention
                </h6>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-2">
                    <i class="fas fa-info-circle me-2 text-warning"></i>
                    Le numéro de dossier ne peut pas être modifié.
                </p>
                <p class="small text-muted mb-0">
                    <i class="fas fa-shield-alt me-2 text-success"></i>
                    Toutes les modifications sont tracées et sécurisées.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection