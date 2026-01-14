<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Cervical Clinic') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link href="{{ asset('css/cervical-care.css') }}" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="fas fa-microscope me-2"></i>CervicalCare AI
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                            <i class="fas fa-home me-1"></i>{{ __('app.home') }}
                        </a>
                    </li>
                    @auth
                        @if(Auth::user()->role === 'patient')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('patient/dashboard*') ? 'active' : '' }}" href="{{ route('patient.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('patient/appointments*') ? 'active' : '' }}" href="{{ route('patient.appointments') }}">
                                    <i class="fas fa-calendar me-1"></i>{{ __('app.appointments') ?? 'Rendez-vous' }}
                                </a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('patients*') ? 'active' : '' }}" href="{{ route('patients.index') }}">
                                    <i class="fas fa-users me-1"></i>{{ __('app.patients') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('historique*') ? 'active' : '' }}" href="{{ route('historique.index') }}">
                                    <i class="fas fa-history me-1"></i>{{ __('app.history') }}
                                </a>
                            </li>
                        @endif
                        @if(Auth::user()->role === 'admin')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('admin/security*') ? 'active' : '' }}" href="{{ route('admin.security.dashboard') }}">
                                    <i class="fas fa-shield-alt me-1"></i>{{ __('app.security') ?? 'Sécurité' }}
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>
                
                <ul class="navbar-nav ms-auto">
                    <!-- Sélecteur de langues -->
                    <li class="nav-item dropdown me-3">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-globe me-1"></i>
                            <span id="current-lang">FR</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="#" onclick="changeLanguage('fr')">
                                    <img src="https://flagcdn.com/16x12/fr.png" alt="Français" class="me-2">
                                    Français
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="changeLanguage('en')">
                                    <img src="https://flagcdn.com/16x12/us.png" alt="English" class="me-2">
                                    English
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="changeLanguage('ar')">
                                    <img src="https://flagcdn.com/16x12/sa.png" alt="العربية" class="me-2">
                                    العربية
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
                                <span class="badge bg-light text-primary ms-1">{{ ucfirst(Auth::user()->role) }}</span>
                            </a>
                            <ul class="dropdown-menu">
                                @if(Auth::user()->role === 'admin')
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard Admin
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.security.dashboard') }}">
                                        <i class="fas fa-shield-alt me-2"></i>Surveillance Sécurité
                                    </a></li>
                                @elseif(Auth::user()->role === 'doctor')
                                    <li><a class="dropdown-item" href="{{ route('doctor.dashboard') }}">
                                        <i class="fas fa-stethoscope me-2"></i>Dashboard Médecin
                                    </a></li>
                                @elseif(Auth::user()->role === 'patient')
                                    <li><a class="dropdown-item" href="{{ route('patient.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>Mon Dashboard
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('patient.profile') }}">
                                        <i class="fas fa-user-cog me-2"></i>Mon Profil
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('patient.analyses') }}">
                                        <i class="fas fa-microscope me-2"></i>Mes Analyses
                                    </a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i>{{ __('app.login') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="fas fa-user-plus me-1"></i>{{ __('app.register') }}
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="container-fluid py-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="footer bg-dark text-white">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-microscope me-2"></i>{{ __('app.cervical_care') }}
                    </h5>
                    <p class="text-light opacity-75">
                        {{ __('app.intelligent_platform_desc') }}
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-light">
                            <i class="fab fa-linkedin-in fa-lg"></i>
                        </a>
                        <a href="#" class="text-light">
                            <i class="fab fa-twitter fa-lg"></i>
                        </a>
                        <a href="#" class="text-light">
                            <i class="fas fa-envelope fa-lg"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">{{ __('app.navigation') }}</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="{{ url('/') }}" class="text-light opacity-75 text-decoration-none">
                                <i class="fas fa-home me-1"></i>{{ __('app.home') }}
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">{{ __('app.account') }}</h6>
                    <ul class="list-unstyled">
                        @auth
                            <li class="mb-2">
                                <a href="#" class="text-light opacity-75 text-decoration-none">
                                    <i class="fas fa-user me-1"></i>{{ __('app.my_profile') }}
                                </a>
                            </li>
                        @else
                            <li class="mb-2">
                                <a href="{{ route('login') }}" class="text-light opacity-75 text-decoration-none">
                                    <i class="fas fa-sign-in-alt me-1"></i>{{ __('app.login') }}
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="{{ route('register') }}" class="text-light opacity-75 text-decoration-none">
                                    <i class="fas fa-user-plus me-1"></i>{{ __('app.register') }}
                                </a>
                            </li>
                        @endauth
                    </ul>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <h6 class="fw-bold mb-3">{{ __('app.contact_support') }}</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-envelope me-2"></i>
                            <a href="mailto:support@cervicalcare.ai" class="text-light opacity-75 text-decoration-none">
                                support@cervicalcare.ai
                            </a>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-phone me-2"></i>
                            <span class="text-light opacity-75">+33 1 23 45 67 89</span>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            <span class="text-light opacity-75">Paris, France</span>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-clock me-2"></i>
                            <span class="text-light opacity-75">{{ __('app.medical_support_24_7') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4 opacity-25">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-light opacity-75">
                        © {{ date('Y') }} {{ __('app.cervical_care') }}. {{ __('app.all_rights_reserved') }}.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="d-flex justify-content-md-end gap-3">
                        <a href="#" class="text-light opacity-75 text-decoration-none small">
                            {{ __('app.privacy_policy') }}
                        </a>
                        <a href="#" class="text-light opacity-75 text-decoration-none small">
                            {{ __('app.terms_of_use') }}
                        </a>
                        <a href="#" class="text-light opacity-75 text-decoration-none small">
                            {{ __('app.legal_notices') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Chatbot Widget (only for authenticated users) -->
    @auth
        @include('components.chatbot')
    @endauth

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Language Switcher Script -->
    <script>
        function changeLanguage(lang) {
            fetch(`/language/${lang}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const currentLangElement = document.getElementById('current-lang');
                    const langCodes = { fr: 'FR', en: 'EN', ar: 'AR' };
                    currentLangElement.textContent = langCodes[lang];
                    
                    if (lang === 'ar') {
                        document.documentElement.setAttribute('dir', 'rtl');
                        document.documentElement.setAttribute('lang', 'ar');
                    } else {
                        document.documentElement.setAttribute('dir', 'ltr');
                        document.documentElement.setAttribute('lang', lang);
                    }
                    
                    showLanguageChangeNotification(lang);
                    setTimeout(() => window.location.reload(), 1000);
                }
            })
            .catch(error => console.error('Erreur lors du changement de langue:', error));
        }

        function showLanguageChangeNotification(lang) {
            const messages = {
                fr: 'Langue changée en Français',
                en: 'Language changed to English',
                ar: 'تم تغيير اللغة إلى العربية'
            };
            
            const notification = document.createElement('div');
            notification.className = 'alert alert-success alert-dismissible fade show position-fixed';
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                <i class="fas fa-globe me-2"></i>${messages[lang]}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(notification);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const currentLocale = '{{ app()->getLocale() }}';
            const langCodes = { fr: 'FR', en: 'EN', ar: 'AR' };
            const currentLangElement = document.getElementById('current-lang');
            
            if (currentLangElement && langCodes[currentLocale]) {
                currentLangElement.textContent = langCodes[currentLocale];
            }
            
            if (currentLocale === 'ar') {
                document.documentElement.setAttribute('dir', 'rtl');
            }
        });
    </script>
</body>
</html>