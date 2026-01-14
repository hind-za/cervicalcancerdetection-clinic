@extends('layouts.app')

@section('title', 'Modifier Patient - ' . $patient->nom_complet)

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-user-edit text-primary me-2"></i>
                Modifier Patient
            </h1>
            <p class="text-muted mb-0">{{ $patient->nom_complet }} - {{ $patient->numero_dossier }}</p>
        </div>
        <div>
            <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour au dossier
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-user-circle me-2"></i>Modifier les Informations
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('patients.update', $patient) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Informations personnelles -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-id-card me-1"></i>Informations Personnelles
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
                                <label for="date_naissance" class="form-label">Date de naissance <span class="text-danger">*</span></label>
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
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-address-book me-1"></i>Informations de Contact
                                </h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control @error('telephone') is-invalid @enderror" 
                                       id="telephone" name="telephone" value="{{ old('telephone', $patient->telephone) }}" 
                                       placeholder="Ex: 01 23 45 67 89">
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
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-stethoscope me-1"></i>Informations Médicales
                                </h6>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="antecedents_medicaux" class="form-label">Antécédents médicaux</label>
                                <textarea class="form-control @error('antecedents_medicaux') is-invalid @enderror" 
                                          id="antecedents_medicaux" name="antecedents_medicaux" rows="4" 
                                          placeholder="Antécédents médicaux, allergies, traitements en cours...">{{ old('antecedents_medicaux', $patient->antecedents_medicaux) }}</textarea>
                                @error('antecedents_medicaux')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="3" 
                                          placeholder="Notes supplémentaires...">{{ old('notes', $patient->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Informations système (lecture seule) -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-secondary mb-3">
                                    <i class="fas fa-info-circle me-1"></i>Informations Système
                                </h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Numéro de dossier</label>
                                <input type="text" class="form-control" value="{{ $patient->numero_dossier }}" readonly>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date de création</label>
                                <input type="text" class="form-control" value="{{ $patient->created_at->format('d/m/Y H:i') }}" readonly>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>Annuler
                                    </a>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save me-1"></i>Sauvegarder les Modifications
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Informations sur les analyses -->
            @if($patient->analyses->count() > 0)
                <div class="alert alert-info mt-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Information:</strong> Ce patient a {{ $patient->analyses->count() }} analyse(s) associée(s). 
                    Les modifications n'affecteront pas les analyses existantes.
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Calcul automatique de l'âge
document.getElementById('date_naissance').addEventListener('change', function() {
    const birthDate = new Date(this.value);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    
    if (age >= 0 && age <= 120) {
        console.log('Âge calculé:', age, 'ans');
    }
});
</script>
@endsection