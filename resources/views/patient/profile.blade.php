@extends('layouts.app')

@section('title', 'Mon Profil')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-user-cog text-primary me-2"></i>
                        Mon Profil Patient
                    </h1>
                    <p class="text-muted mb-0">Gérez vos informations personnelles et médicales</p>
                </div>
                <div class="text-end">
                    <a href="{{ route('patient.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour au Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations Personnelles -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i>
                        Informations Personnelles
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Informations Non Modifiables -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Nom</label>
                                <p class="fw-bold fs-5 mb-0">{{ $patientData->nom }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Prénom</label>
                                <p class="fw-bold fs-5 mb-0">{{ $patientData->prenom }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Date de naissance</label>
                                <p class="fw-bold mb-0">{{ $patientData->date_naissance->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Âge</label>
                                <p class="fw-bold mb-0">{{ $patientData->age ?? 'N/A' }} ans</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Sexe</label>
                                <p class="fw-bold mb-0">{{ isset($patientData->sexe) ? ($patientData->sexe === 'F' ? 'Féminin' : 'Masculin') : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">N° de Dossier</label>
                                <p class="fw-bold mb-0">
                                    <span class="badge bg-secondary fs-6">{{ $patientData->numero_dossier ?? 'En attente' }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Formulaire de Modification -->
                    <form method="POST" action="{{ route('patient.profile.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-edit me-2"></i>
                            Informations Modifiables
                        </h6>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telephone" class="form-label">Téléphone</label>
                                    <input type="tel" 
                                           class="form-control @error('telephone') is-invalid @enderror" 
                                           id="telephone" 
                                           name="telephone" 
                                           value="{{ old('telephone', $patientData->telephone) }}"
                                           placeholder="Ex: 01 23 45 67 89">
                                    @error('telephone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           value="{{ $patientData->email }}" 
                                           disabled>
                                    <div class="form-text">L'email ne peut pas être modifié depuis cette interface.</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="adresse" class="form-label">Adresse</label>
                            <textarea class="form-control @error('adresse') is-invalid @enderror" 
                                      id="adresse" 
                                      name="adresse" 
                                      rows="3"
                                      placeholder="Votre adresse complète">{{ old('adresse', $patientData->adresse) }}</textarea>
                            @error('adresse')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="antecedents_medicaux" class="form-label">Antécédents Médicaux</label>
                            <textarea class="form-control @error('antecedents_medicaux') is-invalid @enderror" 
                                      id="antecedents_medicaux" 
                                      name="antecedents_medicaux" 
                                      rows="4"
                                      placeholder="Décrivez vos antécédents médicaux, allergies, traitements en cours, etc.">{{ old('antecedents_medicaux', $patientData->antecedents_medicaux) }}</textarea>
                            @error('antecedents_medicaux')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Ces informations sont importantes pour l'interprétation de vos analyses.
                            </div>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Sauvegarder les Modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Informations Complémentaires -->
        <div class="col-lg-4">
            <!-- Statistiques du Profil -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Statistiques de Mon Dossier
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Analyses totales</span>
                            <span class="badge bg-primary">{{ $patientData->analysesIA->count() }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Analyses validées</span>
                            <span class="badge bg-success">{{ $patientData->analysesIA->where('statut', 'Validé')->count() }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">En attente</span>
                            <span class="badge bg-warning">{{ $patientData->analysesIA->where('statut', 'En attente')->count() }}</span>
                        </div>
                    </div>
                    <div class="mb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Membre depuis</span>
                            <span class="fw-bold">{{ $patientData->created_at->format('m/Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sécurité et Confidentialité -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">
                        <i class="fas fa-shield-alt me-2"></i>
                        Sécurité et Confidentialité
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-lock text-success me-2"></i>
                            <span class="fw-bold">Données Chiffrées</span>
                        </div>
                        <small class="text-muted">
                            Toutes vos informations personnelles sont chiffrées et sécurisées.
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-user-shield text-info me-2"></i>
                            <span class="fw-bold">Accès Contrôlé</span>
                        </div>
                        <small class="text-muted">
                            Seuls vous et vos médecins autorisés peuvent accéder à vos données.
                        </small>
                    </div>
                    
                    <div class="mb-0">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-history text-primary me-2"></i>
                            <span class="fw-bold">Traçabilité</span>
                        </div>
                        <small class="text-muted">
                            Tous les accès à votre dossier sont enregistrés et tracés.
                        </small>
                    </div>
                </div>
            </div>

            <!-- Actions Rapides -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Actions Rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('patient.dashboard') }}" class="btn btn-outline-primary">
                            <i class="fas fa-microscope me-2"></i>Mon Dashboard
                        </a>
                        <a href="{{ route('patient.appointments') }}" class="btn btn-outline-info">
                            <i class="fas fa-calendar me-2"></i>Rendez-vous
                        </a>
                        <a href="{{ route('patient.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-2"></i>Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <!-- Informations de Contact -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-phone text-primary me-2"></i>
                        Besoin d'Aide ?
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Centre Médical Cervical Clinic</strong>
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-phone me-2 text-primary"></i>
                        <a href="tel:+33123456789">01 23 45 67 89</a>
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-envelope me-2 text-primary"></i>
                        <a href="mailto:contact@cervical-clinic.fr">contact@cervical-clinic.fr</a>
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-clock me-2 text-primary"></i>
                        Lun-Ven: 8h-18h
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Avertissement -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-info border-0 shadow-sm">
                <div class="d-flex">
                    <i class="fas fa-info-circle fa-2x text-info me-3"></i>
                    <div>
                        <h6 class="alert-heading">Information Importante</h6>
                        <p class="mb-0">
                            Vos informations médicales sont confidentielles et protégées par le secret médical. 
                            Assurez-vous que les informations fournies sont exactes et à jour pour garantir 
                            la qualité de votre suivi médical.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-1px);
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    transform: translateY(-1px);
}

.badge {
    font-size: 0.8em;
}
</style>
@endsection