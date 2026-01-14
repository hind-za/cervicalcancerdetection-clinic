@extends('layouts.app')

@section('title', 'Historique des Analyses')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-history text-primary me-2"></i>
                Historique des Analyses IA
            </h1>
            <p class="text-muted mb-0">Toutes les analyses effectuées avec l'Intelligence Artificielle</p>
        </div>
        <div class="text-end">
            <span class="badge bg-info fs-6">{{ $stats['total'] }} analyses</span>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-chart-bar fa-2x text-primary mb-2"></i>
                    <h5 class="text-primary mb-1">{{ $stats['total'] }}</h5>
                    <small class="text-muted">Total analyses</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <h5 class="text-warning mb-1">{{ $stats['en_attente'] }}</h5>
                    <small class="text-muted">En attente</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h5 class="text-success mb-1">{{ $stats['valide'] }}</h5>
                    <small class="text-muted">Validées</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                    <h5 class="text-danger mb-1">{{ $stats['a_revoir'] }}</h5>
                    <small class="text-muted">À revoir</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('historique.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Patient</label>
                        <select name="patient_id" class="form-select">
                            <option value="">Tous les patients</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->nom_complet }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Classe détectée</label>
                        <select name="classe" class="form-select">
                            <option value="">Toutes les classes</option>
                            @foreach($classes as $classe)
                                <option value="{{ $classe }}" {{ request('classe') == $classe ? 'selected' : '' }}>
                                    {{ $classe }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Statut</label>
                        <select name="statut" class="form-select">
                            <option value="">Tous les statuts</option>
                            <option value="En attente" {{ request('statut') == 'En attente' ? 'selected' : '' }}>En attente</option>
                            <option value="Validé" {{ request('statut') == 'Validé' ? 'selected' : '' }}>Validé</option>
                            <option value="À revoir" {{ request('statut') == 'À revoir' ? 'selected' : '' }}>À revoir</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Recherche</label>
                        <input type="text" name="search" class="form-control" placeholder="Nom, prénom, n° dossier..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary d-block">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des analyses -->
    <div class="row">
        @forelse($analyses as $analyse)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <!-- Image -->
                    <div class="position-relative">
                        <img src="{{ route('secure.image.show', basename($analyse->chemin_image)) }}" 
                             class="card-img-top" 
                             style="height: 200px; object-fit: cover;"
                             alt="Analyse {{ $analyse->nom_image }}"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="image-placeholder card-img-top" style="height: 200px; display: none;">
                            <div class="text-center">
                                <i class="fas fa-image fa-2x mb-2"></i>
                                <div>Image indisponible</div>
                            </div>
                        </div>
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge bg-{{ $analyse->statut_color }}">{{ $analyse->statut }}</span>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Patient -->
                        <h6 class="card-title mb-2">
                            <i class="fas fa-user text-primary me-1"></i>
                            {{ $analyse->patient->nom_complet }}
                        </h6>
                        <p class="text-muted small mb-2">
                            <i class="fas fa-id-card me-1"></i>
                            {{ $analyse->patient->numero_dossier }}
                        </p>

                        <!-- Résultat -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="text-{{ $analyse->risque_color }}">{{ $analyse->classe_predite }}</strong>
                                <span class="badge bg-{{ $analyse->risque_color }}">{{ $analyse->confidence_percent }}%</span>
                            </div>
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar bg-{{ $analyse->risque_color }}" 
                                     style="width: {{ $analyse->confidence_percent }}%"></div>
                            </div>
                        </div>

                        <!-- Niveau de risque -->
                        <p class="mb-2">
                            <small class="text-muted">Niveau de risque:</small>
                            <span class="badge bg-{{ $analyse->risque_color }} ms-1">{{ $analyse->niveau_risque }}</span>
                        </p>

                        <!-- Date et analyste -->
                        <div class="text-muted small">
                            <div class="d-flex justify-content-between">
                                <span>
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $analyse->created_at->format('d/m/Y H:i') }}
                                </span>
                                <span>
                                    <i class="fas fa-user-md me-1"></i>
                                    {{ $analyse->analyste->name }}
                                </span>
                            </div>
                        </div>

                        @if($analyse->commentaires_medecin)
                            <div class="mt-2">
                                <small class="text-muted">Commentaire:</small>
                                <p class="small text-dark mb-0">{{ Str::limit($analyse->commentaires_medecin, 100) }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="card-footer bg-transparent border-0">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('historique.show', $analyse) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye me-1"></i>Détails
                            </a>
                            @if(auth()->check() && auth()->user()->role === 'admin')
                                <form method="POST" action="{{ route('historique.destroy', $analyse) }}" 
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette analyse ?')" 
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash me-1"></i>Supprimer
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-search fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune analyse trouvée</h5>
                    <p class="text-muted">Aucune analyse ne correspond à vos critères de recherche</p>
                    <a href="{{ route('historique.index') }}" class="btn btn-primary">
                        <i class="fas fa-refresh me-1"></i>Réinitialiser les filtres
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($analyses->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $analyses->appends(request()->query())->links('pagination::minimal') }}
        </div>
    @endif
</div>
@endsection