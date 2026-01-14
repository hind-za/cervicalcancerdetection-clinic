@extends('layouts.app')

@section('title', 'Rapport Patient - ' . $patient->nom_complet)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-file-medical"></i>
                        Rapport Médical - {{ $patient->nom_complet }}
                    </h4>
                    <div class="btn-group">
                        <a href="{{ route('reports.patient.pdf', ['patient' => $patient->id, 'type' => 'complet']) }}" 
                           class="btn btn-primary">
                            <i class="fas fa-file-pdf"></i> PDF Complet
                        </a>
                        <a href="{{ route('reports.patient.pdf', ['patient' => $patient->id, 'type' => 'medical']) }}" 
                           class="btn btn-success">
                            <i class="fas fa-stethoscope"></i> PDF Médical
                        </a>
                        <a href="{{ route('reports.patient.pdf', ['patient' => $patient->id, 'type' => 'summary']) }}" 
                           class="btn btn-info">
                            <i class="fas fa-chart-line"></i> PDF Résumé
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Informations Patient -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5><i class="fas fa-user"></i> Informations Patient</h5>
                            <table class="table table-sm">
                                <tr><td><strong>Nom complet:</strong></td><td>{{ $patient->nom_complet }}</td></tr>
                                <tr><td><strong>N° Dossier:</strong></td><td>{{ $patient->numero_dossier }}</td></tr>
                                <tr><td><strong>Date naissance:</strong></td><td>{{ $patient->date_naissance->format('d/m/Y') }}</td></tr>
                                <tr><td><strong>Âge:</strong></td><td>{{ $patient->age }} ans</td></tr>
                                <tr><td><strong>Sexe:</strong></td><td>{{ $patient->sexe === 'F' ? 'Féminin' : 'Masculin' }}</td></tr>
                                @if($patient->telephone)
                                <tr><td><strong>Téléphone:</strong></td><td>{{ $patient->telephone }}</td></tr>
                                @endif
                                @if($patient->email)
                                <tr><td><strong>Email:</strong></td><td>{{ $patient->email }}</td></tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-chart-bar"></i> Statistiques</h5>
                            <div class="row">
                                <div class="col-6">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h3>{{ $stats['total_analyses'] }}</h3>
                                            <small>Total Analyses</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h3>{{ $stats['analyses_validees'] }}</h3>
                                            <small>Validées</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 mt-2">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body text-center">
                                            <h3>{{ $stats['analyses_en_attente'] }}</h3>
                                            <small>En Attente</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 mt-2">
                                    <div class="card bg-danger text-white">
                                        <div class="card-body text-center">
                                            <h3>{{ $stats['risque_eleve'] }}</h3>
                                            <small>Risque Élevé</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Antécédents -->
                    @if($patient->antecedents_medicaux)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5><i class="fas fa-history"></i> Antécédents Médicaux</h5>
                            <div class="alert alert-info">
                                {{ $patient->antecedents_medicaux }}
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Analyses IA -->
                    @if($analysesIA->count() > 0)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5><i class="fas fa-brain"></i> Analyses IA ({{ $analysesIA->count() }})</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Classe Prédite</th>
                                            <th>Probabilité</th>
                                            <th>Risque</th>
                                            <th>Statut</th>
                                            <th>Validé par</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($analysesIA as $analyse)
                                        <tr>
                                            <td>{{ $analyse->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <span class="badge badge-secondary">{{ $analyse->classe_predite }}</span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar" style="width: {{ $analyse->confidence_percent }}%">
                                                        {{ $analyse->confidence_percent }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $analyse->risque_color }}">
                                                    {{ $analyse->niveau_risque }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $analyse->statut_color }}">
                                                    {{ $analyse->statut }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $analyse->validateur?->name ?? 'Non validé' }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Notes -->
                    @if($patient->notes)
                    <div class="row">
                        <div class="col-12">
                            <h5><i class="fas fa-sticky-note"></i> Notes</h5>
                            <div class="alert alert-secondary">
                                {{ $patient->notes }}
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Footer -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <hr>
                            <small class="text-muted">
                                Rapport généré le {{ $generated_at->format('d/m/Y à H:i') }} 
                                par {{ $generated_by->name }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection