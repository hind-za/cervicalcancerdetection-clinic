@extends('layouts.app')

@section('title', __('app.register'))

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-success text-white text-center py-4">
                <h3 class="mb-0">
                    <i class="fas fa-user-plus me-2"></i>
                    {{ __('app.create_account') }}
                </h3>
                <p class="mb-0 mt-2 opacity-75">{{ __('app.join_medical_team') }}</p>
            </div>
            
            <div class="card-body p-5">
                <form method="POST" action="{{ route('register.post') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="name" class="form-label fw-semibold">
                            <i class="fas fa-user me-2 text-success"></i>{{ __('app.full_name') }}
                        </label>
                        <input type="text" 
                               class="form-control form-control-lg @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}" 
                               required 
                               autocomplete="name"
                               placeholder="{{ __('app.full_name') }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="email" class="form-label fw-semibold">
                            <i class="fas fa-envelope me-2 text-success"></i>{{ __('app.email_address') }}
                        </label>
                        <input type="email" 
                               class="form-control form-control-lg @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autocomplete="email"
                               placeholder="votre@email.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="role" class="form-label fw-semibold">
                            <i class="fas fa-user-tag me-2 text-success"></i>{{ __('app.role') }}
                        </label>
                        <select class="form-select form-select-lg @error('role') is-invalid @enderror" 
                                id="role" 
                                name="role" 
                                required>
                            <option value="">{{ __('app.select_role') }}</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                                {{ __('app.administrator') }}
                            </option>
                            <option value="doctor" {{ old('role') == 'doctor' ? 'selected' : '' }}>
                                {{ __('app.doctor') }}
                            </option>
                            <option value="patient" {{ old('role') == 'patient' ? 'selected' : '' }}>
                                {{ __('app.patient') }}
                            </option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="speciality" class="form-label fw-semibold">
                            <i class="fas fa-graduation-cap me-2 text-success"></i>{{ __('app.speciality') }}
                        </label>
                        <input type="text" 
                               class="form-control form-control-lg @error('speciality') is-invalid @enderror" 
                               id="speciality" 
                               name="speciality" 
                               value="{{ old('speciality') }}" 
                               autocomplete="organization-title"
                               placeholder="Ex: Gynécologie, Oncologie...">
                        @error('speciality')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="password" class="form-label fw-semibold">
                                <i class="fas fa-lock me-2 text-success"></i>{{ __('app.password') }}
                            </label>
                            <input type="password" 
                                   class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   autocomplete="new-password"
                                   placeholder="••••••••">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <label for="password_confirmation" class="form-label fw-semibold">
                                <i class="fas fa-lock me-2 text-success"></i>{{ __('app.confirm_password') }}
                            </label>
                            <input type="password" 
                                   class="form-control form-control-lg" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required 
                                   autocomplete="new-password"
                                   placeholder="••••••••">
                        </div>
                    </div>

                    <div class="d-grid mb-4">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-user-plus me-2"></i>{{ __('app.create_account') }}
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="card-footer bg-light text-center py-3">
                <p class="mb-0 text-muted">
                    {{ __('app.already_account') }}
                    <a href="{{ route('login') }}" class="text-success text-decoration-none fw-semibold">
                        {{ __('app.connect') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection