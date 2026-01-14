@extends('layouts.app')

@section('title', 'Gestion des Patients')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-users text-primary me-2"></i>
                Gestion des Patients
            </h1>
            <p class="text-muted mb-0">Gérer les dossiers patients et leurs analyses</p>
        </div>
        <div>
            <a href="{{ route('patients.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Nouveau Patient
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                    <h5 class="text-primary mb-1">{{ $patients->total() }}</h5>
                    <small class="text-muted">Total patients</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-user-plus fa-2x text-success mb-2"></i>
                    <h5 class="text-success mb-1">{{ $patients->where('created_at', '>=', now()->startOfMonth())->count() }}</h5>
                    <small class="text-muted">Ce mois-ci</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-microscope fa-2x text-info mb-2"></i>
                    <h5 class="text-info mb-1">{{ $patients->sum(function($p) { return $p->analyses->count() + $p->analysesIA->count(); }) }}</h5>
                    <small class="text-muted">Total analyses</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-calendar fa-2x text-warning mb-2"></i>
                    <h5 class="text-warning mb-1">{{ $patients->where('created_at', '>=', now()->startOfWeek())->count() }}</h5>
                    <small class="text-muted">Cette semaine</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('patients.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Recherche</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Nom, prénom, n° dossier..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sexe</label>
                        <select name="sexe" class="form-select">
                            <option value="">Tous</option>
                            <option value="F" {{ request('sexe') == 'F' ? 'selected' : '' }}>Femme</option>
                            <option value="M" {{ request('sexe') == 'M' ? 'selected' : '' }}>Homme</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Âge</label>
                        <select name="age_range" class="form-select">
                            <option value="">Tous âges</option>
                            <option value="18-30" {{ request('age_range') == '18-30' ? 'selected' : '' }}>18-30 ans</option>
                            <option value="31-45" {{ request('age_range') == '31-45' ? 'selected' : '' }}>31-45 ans</option>
                            <option value="46-60" {{ request('age_range') == '46-60' ? 'selected' : '' }}>46-60 ans</option>
                            <option value="60+" {{ request('age_range') == '60+' ? 'selected' : '' }}>60+ ans</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tri</label>
                        <select name="sort" class="form-select">
                            <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Plus récents</option>
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nom A-Z</option>
                            <option value="analyses" {{ request('sort') == 'analyses' ? 'selected' : '' }}>Plus d'analyses</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary d-block w-100">
                            <i class="fas fa-search me-1"></i>Filtrer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des patients -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Patient</th>
                            <th>N° Dossier</th>
                            <th>Âge</th>
                            <th>Contact</th>
                            <th>Analyses</th>
                            <th>Dernière visite</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patients as $patient)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-primary text-white me-3" style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                            {{ strtoupper(substr($patient->prenom, 0, 1) . substr($patient->nom, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong>{{ $patient->nom_complet }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-{{ $patient->sexe == 'F' ? 'female' : 'male' }} me-1"></i>
                                                {{ $patient->sexe == 'F' ? 'Femme' : 'Homme' }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $patient->numero_dossier }}</span>
                                </td>
                                <td>
                                    <strong>{{ $patient->age }} ans</strong>
                                    <br>
                                    <small class="text-muted">{{ $patient->date_naissance->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    @if($patient->telephone)
                                        <div class="mb-1">
                                            <i class="fas fa-phone text-success me-1"></i>
                                            <small>{{ $patient->telephone }}</small>
                                        </div>
                                    @endif
                                    @if($patient->email)
                                        <div>
                                            <i class="fas fa-envelope text-info me-1"></i>
                                            <small>{{ $patient->email }}</small>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @php
                                        $totalAnalyses = $patient->analyses->count() + $patient->analysesIA->count();
                                    @endphp
                                    @if($totalAnalyses > 0)
                                        <span class="badge bg-info fs-6">{{ $totalAnalyses }}</span>
                                        <br>
                                        <small class="text-muted">analyses</small>
                                    @else
                                        <span class="text-muted">Aucune</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $allAnalyses = $patient->analyses->merge($patient->analysesIA)->sortByDesc('created_at');
                                        $lastAnalyse = $allAnalyses->first();
                                    @endphp
                                    @if($lastAnalyse)
                                        <strong>{{ $lastAnalyse->created_at->format('d/m/Y') }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $lastAnalyse->created_at->diffForHumans() }}</small>
                                    @else
                                        <span class="text-muted">Jamais</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('patients.show', $patient) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Voir le dossier">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('reports.patient', $patient) }}" 
                                           class="btn btn-sm btn-outline-info" 
                                           title="Rapport médical">
                                            <i class="fas fa-file-medical"></i>
                                        </a>
                                        <a href="{{ route('patients.edit', $patient) }}" 
                                           class="btn btn-sm btn-outline-warning" 
                                           title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('patients.destroy', $patient) }}" 
                                              class="d-inline" 
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce patient et toutes ses analyses ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Aucun patient trouvé</h5>
                                    <p class="text-muted">Commencez par ajouter votre premier patient</p>
                                    <a href="{{ route('patients.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i>Ajouter un patient
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($patients->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $patients->appends(request()->query())->links('pagination::minimal') }}
        </div>
    @endif
</div>
@endsection