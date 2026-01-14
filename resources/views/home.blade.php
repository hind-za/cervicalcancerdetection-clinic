@extends('layouts.app')

@section('title', __('app.home'))

@section('content')

<!-- Hero Section -->
<div class="hero-section text-white py-5 mb-5 rounded-3" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4 fade-in-up">
                    <i class="fas fa-microscope me-3"></i>
                    {{ __('app.cervical_care') }}
                </h1>
                <p class="lead mb-4 fade-in-up" style="animation-delay: 0.2s;">
                    {{ __('app.intelligent_platform') }} {{ __('app.advanced_analysis') }}
                </p>
                <div class="d-flex gap-3 justify-content-start fade-in-up" style="animation-delay: 0.4s;">
                    <a href="{{ route('login') }}" class="btn btn-light btn-lg px-4">
                        <i class="fas fa-sign-in-alt me-2"></i>{{ __('app.login') }}
                    </a>
                    <a href="#features" class="btn btn-outline-light btn-lg px-4">
                        <i class="fas fa-info-circle me-2"></i>{{ __('app.learn_more') }}
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="medical-illustration">
                    <div class="icon-wrapper" style="width: 200px; height: 200px; margin: 0 auto; background: rgba(255,255,255,0.1); border-radius: 50%;">
                        <i class="fas fa-user-md" style="font-size: 80px; color: rgba(255,255,255,0.8);"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Section -->
<div class="row mb-5 g-4">
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="icon-wrapper icon-success mx-auto">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-number">95%</div>
            <div class="stat-label">{{ __('app.detection_precision') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="icon-wrapper icon-primary mx-auto">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-number">2 min</div>
            <div class="stat-label">{{ __('app.analysis_time') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="icon-wrapper icon-warning mx-auto">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="stat-number">100%</div>
            <div class="stat-label">{{ __('app.data_security') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="icon-wrapper icon-danger mx-auto">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-number">500+</div>
            <div class="stat-label">{{ __('app.patients_treated') }}</div>
        </div>
    </div>
</div>

<!-- Features Section -->
<section id="features" class="mb-5">
    <div class="text-center mb-5">
        <h2 class="display-5 fw-bold text-primary">{{ __('app.main_features') }}</h2>
        <p class="lead text-muted">{{ __('app.complete_solution') }}</p>
    </div>
    
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-body p-4">
                    <div class="icon-wrapper icon-primary mb-3">
                        <i class="fas fa-upload"></i>
                    </div>
                    <h5 class="card-title fw-bold">{{ __('app.upload_images') }}</h5>
                    <p class="card-text text-muted">
                        {{ __('app.upload_images_desc') }}
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-body p-4">
                    <div class="icon-wrapper icon-success mb-3">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h5 class="card-title fw-bold">{{ __('app.ai_analysis') }}</h5>
                    <p class="card-text text-muted">
                        {{ __('app.ai_analysis_desc') }}
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-body p-4">
                    <div class="icon-wrapper icon-info mb-3">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <h5 class="card-title fw-bold">{{ __('app.medical_validation') }}</h5>
                    <p class="card-text text-muted">
                        {{ __('app.medical_validation_desc') }}
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-body p-4">
                    <div class="icon-wrapper icon-warning mb-3">
                        <i class="fas fa-database"></i>
                    </div>
                    <h5 class="card-title fw-bold">{{ __('app.data_management') }}</h5>
                    <p class="card-text text-muted">
                        {{ __('app.data_management_desc') }}
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-body p-4">
                    <div class="icon-wrapper icon-danger mb-3">
                        <i class="fas fa-file-export"></i>
                    </div>
                    <h5 class="card-title fw-bold">{{ __('app.report_export') }}</h5>
                    <p class="card-text text-muted">
                        {{ __('app.report_export_desc') }}
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-body p-4">
                    <div class="icon-wrapper icon-primary mb-3">
                        <i class="fas fa-history"></i>
                    </div>
                    <h5 class="card-title fw-bold">{{ __('app.complete_history') }}</h5>
                    <p class="card-text text-muted">
                        {{ __('app.complete_history_desc') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- User Roles Section -->
<section class="py-5 mb-5" style="background-color: var(--neutral-50); border-radius: var(--radius-xl);">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold text-primary">{{ __('app.user_roles') }}</h2>
            <p class="lead text-muted">{{ __('app.personalized_access') }}</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card h-100 text-center">
                    <div class="card-body p-4">
                        <div class="icon-wrapper icon-primary mx-auto mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-user-shield fa-2x"></i>
                        </div>
                        <h4 class="fw-bold text-primary mb-3">{{ __('app.administrator') }}</h4>
                        <ul class="list-unstyled text-start">
                            @foreach(__('app.admin_tasks') as $task)
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $task }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card h-100 text-center">
                    <div class="card-body p-4">
                        <div class="icon-wrapper icon-success mx-auto mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-stethoscope fa-2x"></i>
                        </div>
                        <h4 class="fw-bold text-success mb-3">{{ __('app.doctor') }}</h4>
                        <ul class="list-unstyled text-start">
                            @foreach(__('app.doctor_tasks') as $task)
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $task }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card h-100 text-center">
                    <div class="card-body p-4">
                        <div class="icon-wrapper icon-info mx-auto mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-user fa-2x"></i>
                        </div>
                        <h4 class="fw-bold text-info mb-3">Patient</h4>
                        <ul class="list-unstyled text-start">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ __('app.consult_results') }}</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ __('app.download_report') }}</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ __('app.my_profile') }}</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Prise de rendez-vous</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Cervical Cancer Section -->
<section class="py-5 mb-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold" style="color: var(--danger-color);">{{ __('app.about_cervical_cancer') }}</h2>
            <p class="lead text-muted">{{ __('app.cervical_cancer_desc') }}</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 text-center">
                    <div class="card-body p-4">
                        <div class="icon-wrapper icon-danger mx-auto">
                            <i class="fas fa-search"></i>
                        </div>
                        <h5 class="fw-bold mb-3" style="color: var(--danger-color);">{{ __('app.early_detection') }}</h5>
                        <p class="text-muted small">{{ __('app.early_detection_desc') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 text-center">
                    <div class="card-body p-4">
                        <div class="icon-wrapper icon-primary mx-auto">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h5 class="fw-bold text-primary mb-3">{{ __('app.ai_assistance') }}</h5>
                        <p class="text-muted small">{{ __('app.ai_assistance_desc') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 text-center">
                    <div class="card-body p-4">
                        <div class="icon-wrapper icon-success mx-auto">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <h5 class="fw-bold text-success mb-3">{{ __('app.medical_follow_up') }}</h5>
                        <p class="text-muted small">{{ __('app.medical_follow_up_desc') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 text-center">
                    <div class="card-body p-4">
                        <div class="icon-wrapper icon-warning mx-auto">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h5 class="fw-bold text-warning mb-3">95%</h5>
                        <p class="text-muted small">{{ __('app.detection_precision') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="text-center py-5 mb-5" style="background: linear-gradient(135deg, var(--primary-light) 0%, var(--info-light) 100%); border-radius: var(--radius-xl);">
    <div class="container">
        <h3 class="fw-bold text-primary mb-3">{{ __('app.create_account') }}?</h3>
        <p class="lead text-muted mb-4">{{ __('app.join_medical_team') }}</p>
        <div class="d-flex gap-3 justify-content-center">
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-4">
                <i class="fas fa-user-plus me-2"></i>{{ __('app.register') }}
            </a>
            <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg px-4">
                <i class="fas fa-sign-in-alt me-2"></i>{{ __('app.login') }}
            </a>
        </div>
    </div>
</section>

@endsection