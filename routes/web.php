<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AnalyseController;
use App\Http\Controllers\HistoriqueController;
use App\Http\Controllers\CervicalCancerController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// Route pour changer de langue
Route::post('/language/{locale}', [LanguageController::class, 'changeLanguage'])->name('language.change');

// Page d'accueil
Route::get('/', function () {
    return view('home');
})->name('home');

// Routes d'authentification
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes pour mot de passe oublié
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');

Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');

Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Pages principales
Route::get('/historique', [HistoriqueController::class, 'index'])->name('historique.index')->middleware('auth');
Route::get('/historique/{analyse}', [HistoriqueController::class, 'show'])->name('historique.show')->middleware('auth');

// Routes pour les utilisateurs connectés
Route::middleware('auth')->group(function () {
    Route::delete('/historique/{analyse}', [HistoriqueController::class, 'destroy'])->name('historique.destroy');
});

// Routes sécurisées pour les images
Route::middleware(['auth', 'secure.image'])->group(function () {
    Route::get('/secure/images/{path}', [App\Http\Controllers\SecureImageController::class, 'show'])
          ->where('path', '.*')
          ->name('secure.image.show');
    Route::get('/secure/images/{path}/download', [App\Http\Controllers\SecureImageController::class, 'download'])
          ->where('path', '.*')
          ->name('secure.image.download');
});

Route::get('/consulter-resultats', [AnalyseController::class, 'consulterResultats'])->name('consulter.resultats');

// Routes pour la détection du cancer cervical
Route::get('/cervical-cancer', [CervicalCancerController::class, 'index'])->name('cervical-cancer.index');
Route::post('/cervical-cancer/analyze', [CervicalCancerController::class, 'analyze'])->name('cervical-cancer.analyze');
Route::get('/cervical-cancer/api-status', [CervicalCancerController::class, 'apiStatus'])->name('cervical-cancer.api-status');

// Route de debug temporaire
Route::get('/debug-client', function () {
    return response()->file(base_path('debug-client.html'));
})->name('debug-client');

// Dashboard routes (protégées)
Route::middleware(['auth', 'protect.data'])->group(function () {
    // Routes admin
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/admin/analyze-image', [AdminController::class, 'analyzeImage'])->name('admin.analyze-image');
    Route::post('/admin/save-analysis', [AdminController::class, 'saveAnalysis'])->name('admin.save-analysis');
    Route::get('/admin/patient/{patient}/analyses', [AdminController::class, 'getPatientAnalyses'])->name('admin.patient-analyses');
    Route::get('/admin/api-status', [AdminController::class, 'checkApiStatus'])->name('admin.api-status');
    
    // Routes d'audit (admin seulement)
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/audit', [App\Http\Controllers\AuditController::class, 'index'])->name('audit.index');
        Route::get('/admin/audit/{auditLog}', [App\Http\Controllers\AuditController::class, 'show'])->name('audit.show');
        Route::get('/admin/audit/export', [App\Http\Controllers\AuditController::class, 'export'])->name('audit.export');
        
        // Routes de surveillance de sécurité
        Route::get('/admin/security', [App\Http\Controllers\SecurityDashboardController::class, 'index'])->name('admin.security.dashboard');
        Route::get('/admin/security/stats', [App\Http\Controllers\SecurityDashboardController::class, 'getStats'])->name('admin.security.stats');
        
        // Route de debug simple
        Route::get('/admin/security-debug', function() {
            return response()->json([
                'user' => auth()->user(),
                'is_admin' => auth()->check() && auth()->user()->role === 'admin',
                'log_file_exists' => file_exists(storage_path('logs/laravel.log')),
                'log_file_size' => file_exists(storage_path('logs/laravel.log')) ? filesize(storage_path('logs/laravel.log')) : 0
            ]);
        });
    });
    
    // Route de debug temporaire
    Route::post('/debug/analyze', [App\Http\Controllers\DebugController::class, 'testAnalyze'])->name('debug.analyze');

    Route::get('/doctor/dashboard', [App\Http\Controllers\DoctorController::class, 'dashboard'])->name('doctor.dashboard');
    Route::get('/doctor/analyse/{analyse}', [App\Http\Controllers\DoctorController::class, 'showAnalyse'])->name('doctor.analyse.show');
    Route::post('/doctor/analyse/{analyse}/valider', [App\Http\Controllers\DoctorController::class, 'validerAnalyse'])->name('doctor.analyse.valider');
    Route::get('/doctor/historique', [App\Http\Controllers\DoctorController::class, 'historique'])->name('doctor.historique');
    Route::get('/doctor/a-revoir', [App\Http\Controllers\DoctorController::class, 'aRevoir'])->name('doctor.a-revoir');
    
    // Routes pour les patients
    Route::middleware('role:patient')->group(function () {
        Route::get('/patient/dashboard', [App\Http\Controllers\PatientDashboardController::class, 'dashboard'])->name('patient.dashboard');
        Route::get('/patient/analyses', [App\Http\Controllers\PatientDashboardController::class, 'analyses'])->name('patient.analyses');
        Route::get('/patient/analyse/{analyse}', [App\Http\Controllers\PatientDashboardController::class, 'showAnalyse'])->name('patient.analyse.show');
        Route::get('/patient/analyse/{analyse}/download', [App\Http\Controllers\PatientDashboardController::class, 'downloadReport'])->name('patient.analyse.download');
        Route::get('/patient/profile', [App\Http\Controllers\PatientDashboardController::class, 'profile'])->name('patient.profile');
        Route::put('/patient/profile', [App\Http\Controllers\PatientDashboardController::class, 'updateProfile'])->name('patient.profile.update');
        Route::get('/patient/profile/create', [App\Http\Controllers\PatientDashboardController::class, 'createProfile'])->name('patient.profile.create');
        Route::post('/patient/profile', [App\Http\Controllers\PatientDashboardController::class, 'storeProfile'])->name('patient.profile.store');
        Route::get('/patient/appointments', [App\Http\Controllers\PatientDashboardController::class, 'appointments'])->name('patient.appointments');
    });
    
    // Routes pour la gestion des patients
    Route::resource('patients', PatientController::class);
    Route::post('/patients/{patient}/add-analyse', [PatientController::class, 'addAnalyse'])->name('patients.add-analyse');
    
    // Routes pour les rapports
    Route::get('/reports/patient/{patient}', [App\Http\Controllers\ReportController::class, 'generatePatientReport'])->name('reports.patient');
    Route::get('/reports/patient/{patient}/pdf', [App\Http\Controllers\ReportController::class, 'generatePatientReport'])->name('reports.patient.pdf');
    Route::post('/reports/analysis', [App\Http\Controllers\ReportController::class, 'generateAnalysisReport'])->name('reports.analysis');
    Route::get('/reports/test-direct/{patient}', [App\Http\Controllers\ReportController::class, 'testDirectReport'])->name('reports.test.direct');
    Route::get('/reports/test-template', [App\Http\Controllers\ReportController::class, 'testAnalysisTemplate'])->name('reports.test.template');
    Route::get('/reports/global', [App\Http\Controllers\ReportController::class, 'globalReport'])->name('reports.global');
    
    // Routes pour le chatbot
    Route::post('/chatbot/chat', [App\Http\Controllers\ChatbotController::class, 'chat'])->name('chatbot.chat');
    Route::get('/chatbot/suggestions', [App\Http\Controllers\ChatbotController::class, 'getSuggestions'])->name('chatbot.suggestions');
    
    // Routes pour la validation des analyses (médecins)
    Route::middleware('role:doctor')->group(function () {
        Route::post('/analyses/{analyse}/valider', [AnalyseController::class, 'validerAnalyse'])->name('analyses.valider');
    });
});