@extends('layouts.app')

@section('title', 'Dossier Patient - ' . $patient->nom_complet)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-user-circle text-primary me-2"></i>
                Dossier Patient - {{ $patient->nom_complet }}
            </h1>
            <p class="text-muted mb-0">{{ $patient->numero_dossier }} • Créé le {{ $patient->created_at->format('d/m/Y') }}</p>
        </div>
        <div>
            <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i>Retour à la liste
            </a>
            <a href="{{ route('reports.patient', $patient) }}" class="btn btn-info me-2">
                <i class="fas fa-file-medical me-1"></i>Rapport
            </a>
            <a href="{{ route('patients.edit', $patient) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i>Modifier
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informations du patient -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-id-card me-2"></i>Informations Personnelles
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-circle bg-primary text-white mx-auto mb-3" 
                             style="width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                            {{ strtoupper(substr($patient->prenom, 0, 1) . substr($patient->nom, 0, 1)) }}
                        </div>
                        <h4 class="mb-1">{{ $patient->nom_complet }}</h4>
                        <span class="badge bg-{{ $patient->sexe == 'F' ? 'pink' : 'blue' }} bg-opacity-10 text-{{ $patient->sexe == 'F' ? 'pink' : 'blue' }}">
                            <i class="fas fa-{{ $patient->sexe == 'F' ? 'female' : 'male' }} me-1"></i>
                            {{ $patient->sexe == 'F' ? 'Femme' : 'Homme' }}
                        </span>
                    </div>

                    <div class="row text-center mb-4">
                        <div class="col-6">
                            <div class="border-end">
                                <h3 class="text-primary mb-0">{{ $patient->age }}</h3>
                                <small class="text-muted">ans</small>
                            </div>
                        </div>
                        <div class="col-6">
                            @php
                                $totalAnalyses = $patient->analyses->count() + $patient->analysesIA->count();
                            @endphp
                            <h3 class="text-info mb-0">{{ $totalAnalyses }}</h3>
                            <small class="text-muted">analyses</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Date de naissance:</small>
                        <p class="mb-0"><strong>{{ $patient->date_naissance->format('d/m/Y') }}</strong></p>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Numéro de dossier:</small>
                        <p class="mb-0"><span class="badge bg-secondary">{{ $patient->numero_dossier }}</span></p>
                    </div>

                    @if($patient->telephone)
                        <div class="mb-3">
                            <small class="text-muted">Téléphone:</small>
                            <p class="mb-0">
                                <i class="fas fa-phone text-success me-1"></i>
                                <strong>{{ $patient->telephone }}</strong>
                            </p>
                        </div>
                    @endif

                    @if($patient->email)
                        <div class="mb-3">
                            <small class="text-muted">Email:</small>
                            <p class="mb-0">
                                <i class="fas fa-envelope text-info me-1"></i>
                                <strong>{{ $patient->email }}</strong>
                            </p>
                        </div>
                    @endif

                    @if($patient->adresse)
                        <div class="mb-3">
                            <small class="text-muted">Adresse:</small>
                            <p class="mb-0">
                                <i class="fas fa-map-marker-alt text-warning me-1"></i>
                                {{ $patient->adresse }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Informations médicales -->
            @if($patient->antecedents_medicaux || $patient->notes)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-stethoscope me-2"></i>Informations Médicales
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($patient->antecedents_medicaux)
                            <div class="mb-3">
                                <small class="text-muted">Antécédents médicaux:</small>
                                <div class="bg-light p-3 rounded mt-1">
                                    <p class="mb-0">{{ $patient->antecedents_medicaux }}</p>
                                </div>
                            </div>
                        @endif

                        @if($patient->notes)
                            <div class="mb-0">
                                <small class="text-muted">Notes:</small>
                                <div class="bg-light p-3 rounded mt-1">
                                    <p class="mb-0">{{ $patient->notes }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Analyses du patient -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    @php
                        $totalAnalyses = $patient->analyses->count() + $patient->analysesIA->count();
                    @endphp
                    <h5 class="mb-0">
                        <i class="fas fa-microscope me-2"></i>Historique des Analyses ({{ $totalAnalyses }})
                    </h5>
                    @if(auth()->check())
                        <a href="{{ route('admin.dashboard') }}?patient_id={{ $patient->id }}" class="btn btn-light btn-sm">
                            <i class="fas fa-plus me-1"></i>Nouvelle Analyse
                        </a>
                    @endif
                </div>
                <div class="card-body p-0">
                    @php
                        // Combiner toutes les analyses et les trier par date
                        $allAnalyses = $patient->analyses->merge($patient->analysesIA)->sortByDesc('created_at');
                    @endphp
                    @if($allAnalyses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Image</th>
                                        <th>Date</th>
                                        <th>Résultat</th>
                                        <th>Confiance</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allAnalyses as $analyse)
                                        <tr>
                                            <td>
                                                @if(isset($analyse->chemin_image))
                                                    <img src="{{ route('secure.image.show', basename($analyse->chemin_image)) }}" 
                                                         class="rounded" 
                                                         style="width: 50px; height: 50px; object-fit: cover;"
                                                         alt="Analyse">
                                                @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $analyse->created_at->format('d/m/Y') }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $analyse->created_at->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                @if(isset($analyse->classe_predite))
                                                    <strong class="text-{{ $analyse->risque_color ?? 'primary' }}">
                                                        {{ $analyse->classe_predite }}
                                                    </strong>
                                                @else
                                                    <span class="badge bg-{{ $analyse->resultat_color ?? 'secondary' }}">
                                                        {{ $analyse->resultat ?? 'Non défini' }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(isset($analyse->probabilite))
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-2">{{ round($analyse->probabilite * 100) }}%</span>
                                                        <div class="progress" style="width: 60px; height: 6px;">
                                                            <div class="progress-bar bg-{{ $analyse->risque_color ?? 'primary' }}" 
                                                                 style="width: {{ $analyse->probabilite * 100 }}%"></div>
                                                        </div>
                                                    </div>
                                                @elseif(isset($analyse->confiance))
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-2">{{ $analyse->confiance }}%</span>
                                                        <div class="progress" style="width: 60px; height: 6px;">
                                                            <div class="progress-bar bg-primary" 
                                                                 style="width: {{ $analyse->confiance }}%"></div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $analyse->statut_color ?? 'secondary' }}">
                                                    {{ $analyse->statut }}
                                                </span>
                                            </td>
                                            <td>
                                                @if(isset($analyse->classe_predite))
                                                    <a href="{{ route('historique.show', $analyse) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="Voir les détails">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @else
                                                    <button class="btn btn-sm btn-outline-secondary" disabled>
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-microscope fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune analyse</h5>
                            <p class="text-muted">Ce patient n'a pas encore d'analyse effectuée</p>
                            @if(auth()->check())
                                <a href="{{ route('admin.dashboard') }}?patient_id={{ $patient->id }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Effectuer une analyse
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection