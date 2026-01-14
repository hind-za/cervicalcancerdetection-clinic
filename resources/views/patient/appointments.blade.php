@extends('layouts.app')

@section('title', 'Mes Rendez-vous')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-calendar-alt text-primary me-2"></i>
                        Mes Rendez-vous et Notifications
                    </h1>
                    <p class="text-muted mb-0">Gérez vos rendez-vous et consultez vos notifications médicales</p>
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
        <!-- Notifications -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-bell me-2"></i>
                        Notifications Médicales
                        @if($notifications->count() > 0)
                        <span class="badge bg-danger ms-2">{{ $notifications->count() }}</span>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    @if($notifications->count() > 0)
                        @foreach($notifications as $notification)
                        <div class="alert alert-{{ $notification['type'] }} border-0 shadow-sm mb-3">
                            <div class="d-flex align-items-start">
                                @php
                                    $icon = match($notification['type']) {
                                        'warning' => 'fas fa-exclamation-triangle',
                                        'info' => 'fas fa-info-circle',
                                        'success' => 'fas fa-check-circle',
                                        'danger' => 'fas fa-exclamation-circle',
                                        default => 'fas fa-bell'
                                    };
                                @endphp
                                <i class="{{ $icon }} fa-2x me-3 mt-1"></i>
                                <div class="flex-grow-1">
                                    <h6 class="alert-heading mb-2">{{ $notification['title'] }}</h6>
                                    <p class="mb-2">{{ $notification['message'] }}</p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $notification['date']->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune notification</h5>
                            <p class="text-muted">Vous n'avez actuellement aucune notification médicale.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Prochains Rendez-vous -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-check me-2"></i>
                        Prochains Rendez-vous
                    </h5>
                </div>
                <div class="card-body">
                    @if($appointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date & Heure</th>
                                        <th>Type</th>
                                        <th>Médecin</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appointments as $appointment)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $appointment->date->format('d/m/Y') }}</div>
                                            <small class="text-muted">{{ $appointment->date->format('H:i') }}</small>
                                        </td>
                                        <td>{{ $appointment->type }}</td>
                                        <td>{{ $appointment->medecin }}</td>
                                        <td>
                                            <span class="badge bg-{{ $appointment->status_class }}">
                                                {{ $appointment->statut }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-outline-danger" title="Annuler">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun rendez-vous programmé</h5>
                            <p class="text-muted">Vous n'avez actuellement aucun rendez-vous programmé.</p>
                            <button class="btn btn-primary" onclick="requestAppointment()">
                                <i class="fas fa-plus me-2"></i>Demander un Rendez-vous
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Informations et Actions -->
        <div class="col-lg-4">
            <!-- Informations Patient -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-user me-2"></i>
                        Mes Informations
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Nom complet</small>
                        <p class="mb-0 fw-bold">{{ $patientData->nom_complet }}</p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">N° Dossier</small>
                        <p class="mb-0 fw-bold">{{ $patientData->numero_dossier ?? 'En attente' }}</p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Téléphone</small>
                        <p class="mb-0 fw-bold">{{ $patientData->telephone ?? 'Non renseigné' }}</p>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted">Email</small>
                        <p class="mb-0 fw-bold">{{ $patientData->email }}</p>
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
                        <button class="btn btn-primary" onclick="requestAppointment()">
                            <i class="fas fa-calendar-plus me-2"></i>Demander un RDV
                        </button>
                        <button class="btn btn-outline-info" onclick="contactClinic()">
                            <i class="fas fa-phone me-2"></i>Contacter la Clinique
                        </button>
                        <a href="{{ route('patient.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-tachometer-alt me-2"></i>Mon Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <!-- Conseils de Santé -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb text-warning me-2"></i>
                        Conseils de Santé
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-shield-alt text-success me-2"></i>
                            <span class="fw-bold small">Prévention</span>
                        </div>
                        <small class="text-muted">
                            Effectuez des contrôles réguliers selon les recommandations de votre médecin.
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-calendar-check text-info me-2"></i>
                            <span class="fw-bold small">Suivi</span>
                        </div>
                        <small class="text-muted">
                            Respectez les dates de rendez-vous et les recommandations médicales.
                        </small>
                    </div>
                    
                    <div class="mb-0">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-phone text-primary me-2"></i>
                            <span class="fw-bold small">Contact</span>
                        </div>
                        <small class="text-muted">
                            N'hésitez pas à nous contacter en cas de questions ou d'inquiétudes.
                        </small>
                    </div>
                </div>
            </div>

            <!-- Informations de Contact -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-hospital me-2"></i>
                        Centre Médical
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Cervical Clinic</strong>
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                        123 Avenue de la Santé<br>
                        75001 Paris, France
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
                        <strong>Horaires :</strong><br>
                        <small>Lun-Ven: 8h-18h<br>Sam: 9h-12h</small>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Demande de Rendez-vous -->
<div class="modal fade" id="appointmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-plus me-2"></i>
                    Demander un Rendez-vous
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="appointment-form">
                    <div class="mb-3">
                        <label for="appointment-type" class="form-label">Type de consultation</label>
                        <select class="form-select" id="appointment-type" required>
                            <option value="">Sélectionnez le type</option>
                            <option value="consultation">Consultation de routine</option>
                            <option value="controle">Contrôle post-analyse</option>
                            <option value="urgence">Consultation urgente</option>
                            <option value="suivi">Suivi médical</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="preferred-date" class="form-label">Date souhaitée</label>
                        <input type="date" class="form-control" id="preferred-date" 
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="preferred-time" class="form-label">Heure souhaitée</label>
                        <select class="form-select" id="preferred-time" required>
                            <option value="">Sélectionnez l'heure</option>
                            <option value="08:00">08:00</option>
                            <option value="09:00">09:00</option>
                            <option value="10:00">10:00</option>
                            <option value="11:00">11:00</option>
                            <option value="14:00">14:00</option>
                            <option value="15:00">15:00</option>
                            <option value="16:00">16:00</option>
                            <option value="17:00">17:00</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="appointment-reason" class="form-label">Motif (optionnel)</label>
                        <textarea class="form-control" id="appointment-reason" rows="3"
                                  placeholder="Décrivez brièvement le motif de votre consultation"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="submitAppointmentRequest()">
                    <i class="fas fa-paper-plane me-2"></i>Envoyer la Demande
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function requestAppointment() {
    const modal = new bootstrap.Modal(document.getElementById('appointmentModal'));
    modal.show();
}

function submitAppointmentRequest() {
    const form = document.getElementById('appointment-form');
    const formData = new FormData(form);
    
    // Simuler l'envoi de la demande
    alert('Votre demande de rendez-vous a été envoyée avec succès! Nous vous contacterons dans les plus brefs délais.');
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('appointmentModal'));
    modal.hide();
    
    // Réinitialiser le formulaire
    form.reset();
}

function contactClinic() {
    const phone = '+33123456789';
    if (confirm('Souhaitez-vous appeler la clinique au 01 23 45 67 89 ?')) {
        window.location.href = `tel:${phone}`;
    }
}
</script>

<style>
.card {
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-1px);
}

.alert {
    border-radius: 10px;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,.075);
}

.modal-content {
    border-radius: 15px;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}
</style>
@endsection