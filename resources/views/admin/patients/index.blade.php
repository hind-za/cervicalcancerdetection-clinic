@extends('layouts.app')

@section('title', 'Gestion des Patients')

@section('content')
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
            <i class="fas fa-plus me-2"></i>Nouveau Patient
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

<!-- Statistiques rapides -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body text-center">
                <i class="fas fa-users fa-2x mb-2"></i>
                <h4 class="fw-bold">{{ $patients->total() }}</h4>
                <p class="mb-0">Total Patients</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body text-center">
                <i class="fas fa-images fa-2x mb-2"></i>
                <h4 class="fw-bold">{{ $patients->sum(function($p) { return $p->analyses->count(); }) }}</h4>
                <p class="mb-0">Analyses Totales</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning text-white">
            <div class="card-body text-center">
                <i class="fas fa-clock fa-2x mb-2"></i>
                <h4 class="fw-bold">{{ $patients->sum(function($p) { return $p->analyses->where('statut', 'En attente')->count(); }) }}</h4>
                <p class="mb-0">En Attente</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body text-center">
                <i class="fas fa-check fa-2x mb-2"></i>
                <h4 class="fw-bold">{{ $patients->sum(function($p) { return $p->analyses->where('statut', 'Validé')->count(); }) }}</h4>
                <p class="mb-0">Validées</p>
            </div>
        </div>
    </div>
</div>

<!-- Liste des patients -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list text-primary me-2"></i>
                Liste des Patients
            </h5>
            <div class="d-flex gap-2">
                <input type="text" class="form-control form-control-sm" placeholder="Rechercher..." id="searchInput">
                <button class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        @if($patients->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>N° Dossier</th>
                            <th>Patient</th>
                            <th>Âge</th>
                            <th>Contact</th>
                            <th>Analyses</th>
                            <th>Dernière Analyse</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patients as $patient)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">{{ $patient->numero_dossier }}</span>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $patient->nom_complet }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-{{ $patient->sexe == 'F' ? 'female' : 'male' }} me-1"></i>
                                            {{ $patient->sexe == 'F' ? 'Femme' : 'Homme' }}
                                        </small>
                                    </div>
                                </td>
                                <td>{{ $patient->age }} ans</td>
                                <td>
                                    @if($patient->telephone)
                                        <div><i class="fas fa-phone me-1"></i>{{ $patient->telephone }}</div>
                                    @endif
                                    @if($patient->email)
                                        <div><i class="fas fa-envelope me-1"></i>{{ $patient->email }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $patient->analyses->count() }}</span>
                                    @if($patient->analyses->where('statut', 'En attente')->count() > 0)
                                        <span class="badge bg-warning ms-1">{{ $patient->analyses->where('statut', 'En attente')->count() }} en attente</span>
                                    @endif
                                </td>
                                <td>
                                    @if($patient->analyses->count() > 0)
                                        @php $derniere = $patient->analyses->first() @endphp
                                        <div>
                                            <span class="badge bg-{{ $derniere->resultat_color }}">{{ $derniere->resultat }}</span>
                                            <br>
                                            <small class="text-muted">{{ $derniere->created_at->format('d/m/Y') }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted">Aucune</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('patients.show', $patient) }}" class="btn btn-sm btn-outline-primary" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('patients.edit', $patient) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger" title="Supprimer" 
                                                onclick="confirmDelete({{ $patient->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $patients->links('pagination::minimal') }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Aucun patient enregistré</h5>
                <p class="text-muted">Commencez par ajouter votre premier patient</p>
                <a href="{{ route('patients.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Ajouter un Patient
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer ce patient ?</p>
                <p class="text-danger"><strong>Cette action est irréversible et supprimera également toutes les analyses associées.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(patientId) {
    const form = document.getElementById('deleteForm');
    form.action = `/patients/${patientId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Recherche simple
document.getElementById('searchInput').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});
</script>
@endsection