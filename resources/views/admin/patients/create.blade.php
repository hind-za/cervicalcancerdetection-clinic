@extends('layouts.app')

@section('title', 'Nouveau Patient')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">
            <i class="fas fa-user-plus text-primary me-2"></i>
            Nouveau Patient
        </h1>
        <p class="text-muted mb-0">Ajouter un nouveau patient au système</p>
    </div>
    <div>
        <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-user-edit me-2"></i>
                    Informations du Patient
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('patients.store') }}" method="POST">
                    @csrf
                    
                    <!-- Informations personnelles -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-id-card me-2"></i>Informations Personnelles
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                   id="nom" name="nom" value="{{ old('nom') }}" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('prenom') is-invalid @enderror" 
                                   id="prenom" name="prenom" value="{{ old('prenom') }}" required>
                            @error('prenom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="date_naissance" class="form-label">Date de Naissance <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date_naissance') is-invalid @enderror" 
                                   id="date_naissance" name="date_naissance" value="{{ old('date_naissance') }}" required>
                            @error('date_naissance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="sexe" class="form-label">Sexe <span class="text-danger">*</span></label>
                            <select class="form-select @error('sexe') is-invalid @enderror" id="sexe" name="sexe" required>
                                <option value="">Sélectionner...</option>
                                <option value="F" {{ old('sexe') == 'F' ? 'selected' : '' }}>Femme</option>
                                <option value="M" {{ old('sexe') == 'M' ? 'selected' : '' }}>Homme</option>
                            </select>
                            @error('sexe')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Informations de contact -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-address-book me-2"></i>Informations de Contact
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="tel" class="form-control @error('telephone') is-invalid @enderror" 
                                   id="telephone" name="telephone" value="{{ old('telephone') }}" 
                                   placeholder="+212 6XX XXX XXX">
                            @error('telephone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" 
                                   placeholder="patient@example.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="adresse" class="form-label">Adresse</label>
                            <textarea class="form-control @error('adresse') is-invalid @enderror" 
                                      id="adresse" name="adresse" rows="3" 
                                      placeholder="Adresse complète du patient">{{ old('adresse') }}</textarea>
                            @error('adresse')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Informations médicales -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-notes-medical me-2"></i>Informations Médicales
                            </h6>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="antecedents_medicaux" class="form-label">Antécédents Médicaux</label>
                            <textarea class="form-control @error('antecedents_medicaux') is-invalid @enderror" 
                                      id="antecedents_medicaux" name="antecedents_medicaux" rows="4" 
                                      placeholder="Antécédents médicaux, allergies, traitements en cours...">{{ old('antecedents_medicaux') }}</textarea>
                            @error('antecedents_medicaux')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="notes" class="form-label">Notes Additionnelles</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3" 
                                      placeholder="Notes supplémentaires...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Boutons d'action -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Enregistrer le Patient
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Informations d'aide -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Informations
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-info">Numéro de Dossier</h6>
                    <p class="small text-muted">Un numéro unique sera automatiquement généré pour ce patient (format: PAT-YYYY-XXXX)</p>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-info">Champs Obligatoires</h6>
                    <ul class="small text-muted">
                        <li>Nom et Prénom</li>
                        <li>Date de naissance</li>
                        <li>Sexe</li>
                    </ul>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-info">Après Création</h6>
                    <p class="small text-muted">Vous pourrez ajouter des analyses d'images directement depuis la fiche du patient.</p>
                </div>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">
                    <i class="fas fa-shield-alt me-2"></i>Confidentialité
                </h6>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-0">
                    <i class="fas fa-lock me-2 text-success"></i>
                    Toutes les informations sont chiffrées et sécurisées selon les normes RGPD.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
// Calcul automatique de l'âge
document.getElementById('date_naissance').addEventListener('change', function() {
    const birthDate = new Date(this.value);
    const today = new Date();
    const age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    
    if (age >= 0 && age <= 120) {
        // Afficher l'âge quelque part si nécessaire
        console.log('Âge calculé:', age, 'ans');
    }
});
</script>
@endsection