@extends('layouts.app')

@section('title', __('app.forgot_password_title'))

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-warning text-white text-center py-4">
                <h3 class="mb-0">
                    <i class="fas fa-key me-2"></i>
                    {{ __('app.forgot_password_title') }}
                </h3>
                <p class="mb-0 mt-2 opacity-75">{{ __('app.enter_email_reset') }}</p>
            </div>
            
            <div class="card-body p-5">
                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ __('app.reset_link_sent') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    
                    @if (session('test_mode'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="fas fa-flask me-2"></i>
                            <strong>Mode Test Activé</strong><br>
                            {{ session('message') }}<br><br>
                            <a href="{{ session('reset_url') }}" class="btn btn-primary btn-sm" target="_blank">
                                <i class="fas fa-external-link-alt me-1"></i>Cliquer ici pour réinitialiser le mot de passe
                            </a>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="email" class="form-label fw-semibold">
                            <i class="fas fa-envelope me-2 text-warning"></i>{{ __('app.email_address') }}
                        </label>
                        <input type="email" 
                               class="form-control form-control-lg @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autocomplete="email" 
                               autofocus
                               placeholder="votre@email.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            {{ __('app.enter_email_reset') }}
                        </div>
                    </div>

                    <div class="d-grid mb-4">
                        <button type="submit" class="btn btn-warning btn-lg text-white">
                            <i class="fas fa-paper-plane me-2"></i>{{ __('app.send_reset_link') }}
                        </button>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('login') }}" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i>{{ __('app.back_to_login') }}
                        </a>
                    </div>
                </form>
            </div>
            
            <div class="card-footer bg-light text-center py-3">
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            Sécurisé
                        </small>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            Lien valide 60 min
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Info Card -->
        <div class="card mt-4 border-info">
            <div class="card-body text-center">
                <i class="fas fa-info-circle fa-2x text-info mb-3"></i>
                <h6 class="fw-bold">{{ __('app.forgot_password_title') }}</h6>
                <p class="text-muted small mb-0">
                    Un email avec un lien de réinitialisation sera envoyé à votre adresse. 
                    Vérifiez également votre dossier spam.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection