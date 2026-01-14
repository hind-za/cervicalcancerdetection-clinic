@extends('layouts.app')

@section('title', __('app.login'))

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-primary text-white text-center py-4">
                <h3 class="mb-0">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    {{ __('app.login') }}
                </h3>
                <p class="mb-0 mt-2 opacity-75">{{ __('app.access_professional') }}</p>
            </div>
            
            <div class="card-body p-5">
                @if (session('status') === 'password-reset')
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ __('app.password_reset_success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="email" class="form-label fw-semibold">
                            <i class="fas fa-envelope me-2 text-primary"></i>{{ __('app.email_address') }}
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
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold">
                            <i class="fas fa-lock me-2 text-primary"></i>{{ __('app.password') }}
                        </label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password"
                                   placeholder="••••••••">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                {{ __('app.remember_me') }}
                            </label>
                        </div>
                    </div>

                    <div class="d-grid mb-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>{{ __('app.connect') }}
                        </button>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('password.request') }}" class="text-decoration-none">
                            <i class="fas fa-key me-1"></i>{{ __('app.forgot_password') }}
                        </a>
                    </div>
                </form>
            </div>
            
            <div class="card-footer bg-light text-center py-3">
                <p class="mb-0 text-muted">
                    {{ __('app.no_account') }}
                    <a href="{{ route('register') }}" class="text-primary text-decoration-none fw-semibold">
                        {{ __('app.create_account') }}
                    </a>
                </p>
            </div>
        </div>
        
        <!-- Role Selection Info -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <i class="fas fa-user-shield fa-2x text-primary mb-2"></i>
                        <h6 class="fw-bold">{{ __('app.administrator') }}</h6>
                        <small class="text-muted">{{ __('app.complete_management') }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <i class="fas fa-stethoscope fa-2x text-success mb-2"></i>
                        <h6 class="fw-bold">{{ __('app.doctor') }}</h6>
                        <small class="text-muted">{{ __('app.diagnosis_validation') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('togglePassword').addEventListener('click', function() {
    const password = document.getElementById('password');
    const icon = this.querySelector('i');
    
    if (password.type === 'password') {
        password.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        password.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});
</script>
@endsection