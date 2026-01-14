<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\AnalyseIA;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class PatientDashboardController extends Controller
{
    /**
     * Dashboard principal du patient
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Récupérer le patient associé à cet utilisateur (si existe)
        $patient = Patient::where('email', $user->email)->first();
        
        // Si aucun dossier médical n'existe, créer des données par défaut
        if (!$patient) {
            $patientData = (object) [
                'id' => null,
                'nom' => $user->name ?? 'Patient',
                'prenom' => '',
                'nom_complet' => $user->name ?? 'Patient',
                'email' => $user->email,
                'age' => 'N/A',
                'sexe' => 'N/A',
                'numero_dossier' => 'En attente',
                'telephone' => null,
                'analysesIA' => collect([]) // Collection vide
            ];
            
            $recentAnalyses = collect([]);
        } else {
            // Charger les analyses du patient existant
            $patient->load([
                'analysesIA' => function($query) {
                    $query->with(['user', 'validateur' => function($q) {
                        $q->select('id', 'name', 'role');
                    }])
                          ->orderBy('created_at', 'desc');
                }
            ]);
            
            $patientData = $this->decryptPatientData($patient);
            $recentAnalyses = $patient->analysesIA->take(5);
        }
        
        // Statistiques du patient
        $stats = [
            'total_analyses' => $patientData->analysesIA->count(),
            'analyses_validees' => $patientData->analysesIA->where('statut', 'Validé')->count(),
            'analyses_en_attente' => $patientData->analysesIA->where('statut', 'En attente')->count(),
            'derniere_analyse' => $patientData->analysesIA->first()?->created_at,
            'risque_eleve' => $patientData->analysesIA->where('niveau_risque', 'Élevé')->count(),
            'prochaine_visite' => $patient ? $this->calculateNextVisit($patient) : null
        ];
        
        return view('patient.dashboard', compact('patientData', 'recentAnalyses', 'stats'));
    }
    
    /**
     * Historique complet des analyses
     */
    public function analyses()
    {
        $user = Auth::user();
        $patient = $this->getPatientForUser($user);
        
        if (!$patient) {
            return view('patient.analyses', [
                'patientData' => (object) [
                    'nom_complet' => $user->name, 
                    'email' => $user->email,
                    'numero_dossier' => 'En attente',
                    'age' => 'N/A',
                    'sexe' => 'N/A'
                ],
                'analyses' => collect([])
            ]);
        }
        
        $analyses = $patient->analysesIA()
                           ->with(['user', 'validateur' => function($q) {
                               $q->select('id', 'name', 'role');
                           }])
                           ->orderBy('created_at', 'desc')
                           ->paginate(10);
        
        $patientData = $this->decryptPatientData($patient);
        
        return view('patient.analyses', compact('patientData', 'analyses'));
    }
    
    /**
     * Détail d'une analyse spécifique
     */
    public function showAnalyse(AnalyseIA $analyse)
    {
        $user = Auth::user();
        $patient = $this->getPatientForUser($user);
        
        // Vérifier que l'analyse appartient bien au patient connecté
        if (!$patient || $analyse->patient_id !== $patient->id) {
            abort(403, 'Accès non autorisé à cette analyse.');
        }
        
        $analyse->load(['user', 'validateur' => function($q) {
            $q->select('id', 'name', 'role');
        }]);
        $patientData = $this->decryptPatientData($patient);
        
        return view('patient.analyse-detail', compact('patientData', 'analyse'));
    }
    
    /**
     * Profil du patient
     */
    public function profile()
    {
        $user = Auth::user();
        $patient = $this->getPatientForUser($user);
        
        if (!$patient) {
            return redirect()->route('patient.profile.create');
        }
        
        $patientData = $this->decryptPatientData($patient);
        
        return view('patient.profile', compact('patientData'));
    }
    
    /**
     * Créer un profil patient
     */
    public function createProfile()
    {
        return view('patient.profile-create');
    }
    
    /**
     * Sauvegarder le profil patient
     */
    public function storeProfile(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'date_naissance' => 'required|date|before:today',
            'sexe' => 'required|in:M,F',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:500',
            'antecedents_medicaux' => 'nullable|string|max:1000'
        ]);
        
        $user = Auth::user();
        
        // Vérifier qu'un patient n'existe pas déjà
        $existingPatient = Patient::where('email', $user->email)->first();
        if ($existingPatient) {
            return redirect()->route('patient.dashboard')
                           ->with('info', 'Votre profil patient existe déjà.');
        }
        
        // Calculer l'âge
        $dateNaissance = \Carbon\Carbon::parse($request->date_naissance);
        $age = $dateNaissance->age;
        
        // Créer le patient
        $patient = Patient::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'numero_dossier' => 'PAT' . str_pad(Patient::count() + 1, 6, '0', STR_PAD_LEFT),
            'date_naissance' => $request->date_naissance,
            'age' => $age,
            'sexe' => $request->sexe,
            'telephone' => $request->telephone,
            'email' => $user->email,
            'adresse' => $request->adresse,
            'antecedents_medicaux' => $request->antecedents_medicaux
        ]);
        
        return redirect()->route('patient.dashboard')
                       ->with('success', 'Votre profil patient a été créé avec succès!');
    }
    
    /**
     * Télécharger un rapport d'analyse
     */
    public function downloadReport(AnalyseIA $analyse)
    {
        $user = Auth::user();
        $patient = $this->getPatientForUser($user);
        
        // Vérifier que l'analyse appartient bien au patient connecté
        if (!$patient || $analyse->patient_id !== $patient->id) {
            abort(403, 'Accès non autorisé à cette analyse.');
        }
        
        // Seules les analyses validées peuvent être téléchargées
        if ($analyse->statut !== 'Validé') {
            return redirect()->back()
                           ->with('error', 'Seules les analyses validées peuvent être téléchargées.');
        }
        
        // Générer le PDF directement
        $reportController = new \App\Http\Controllers\ReportController();
        
        // Préparer les données d'analyse
        $analysisData = [
            'classe_predite' => $analyse->classe_predite,
            'probabilite' => $analyse->probabilite,
            'niveau_risque' => $analyse->niveau_risque,
            'interpretation' => $analyse->interpretation,
            'recommandations' => $analyse->recommandations ? json_decode($analyse->recommandations, true) : [],
            'toutes_probabilites' => $analyse->toutes_probabilites ? json_decode($analyse->toutes_probabilites, true) : [],
            'temps_analyse' => $analyse->temps_analyse ?? '2.5',
            'created_at' => $analyse->created_at,
            'statut' => $analyse->statut,
            'commentaires_medecin' => $analyse->commentaires_medecin,
            'validateur' => $analyse->validateur ? $analyse->validateur->name : null,
            'date_validation' => $analyse->updated_at
        ];
        
        // Créer une requête simulée pour le contrôleur de rapport
        $request = new \Illuminate\Http\Request();
        $request->merge([
            'patient_id' => $patient->id,
            'analysis_data' => json_encode($analysisData)
        ]);
        
        return $reportController->generateAnalysisReport($request);
    }
    
    /**
     * Rendez-vous et notifications
     */
    public function appointments()
    {
        $user = Auth::user();
        $patient = $this->getPatientForUser($user);
        
        $patientData = $patient ? $this->decryptPatientData($patient) : (object) [
            'nom_complet' => $user->name,
            'email' => $user->email,
            'numero_dossier' => 'En attente',
            'age' => 'N/A',
            'sexe' => 'N/A'
        ];
        
        // Simuler des rendez-vous (à implémenter selon vos besoins)
        $appointments = collect([]);
        
        // Notifications basées sur les analyses
        $notifications = $patient ? $this->getPatientNotifications($patient) : collect([]);
        
        return view('patient.appointments', compact('patientData', 'appointments', 'notifications'));
    }
    
    /**
     * Méthodes utilitaires privées
     */
    private function getPatientForUser($user)
    {
        return Patient::where('email', $user->email)->first();
    }
    
    private function decryptPatientData($patient)
    {
        return (object) [
            'id' => $patient->id,
            'nom' => $this->forceDecrypt($patient->getRawOriginal('nom')),
            'prenom' => $this->forceDecrypt($patient->getRawOriginal('prenom')),
            'nom_complet' => trim($this->forceDecrypt($patient->getRawOriginal('prenom')) . ' ' . $this->forceDecrypt($patient->getRawOriginal('nom'))),
            'numero_dossier' => $patient->numero_dossier,
            'date_naissance' => $patient->date_naissance,
            'age' => $patient->age,
            'sexe' => $patient->sexe,
            'telephone' => $this->forceDecrypt($patient->getRawOriginal('telephone')),
            'email' => $this->forceDecrypt($patient->getRawOriginal('email')),
            'adresse' => $this->forceDecrypt($patient->getRawOriginal('adresse')),
            'antecedents_medicaux' => $this->forceDecrypt($patient->getRawOriginal('antecedents_medicaux')),
            'created_at' => $patient->created_at,
            'updated_at' => $patient->updated_at,
            'analysesIA' => $patient->analysesIA ?? collect()
        ];
    }
    
    private function forceDecrypt($value)
    {
        if (empty($value)) {
            return $value;
        }
        
        try {
            return Crypt::decrypt($value);
        } catch (\Exception $e) {
            return $value;
        }
    }
    
    private function calculateNextVisit($patient)
    {
        $lastAnalysis = $patient->analysesIA->first();
        
        if (!$lastAnalysis) {
            return null;
        }
        
        // Calculer la prochaine visite selon le niveau de risque
        $interval = match($lastAnalysis->niveau_risque) {
            'Élevé' => 3, // 3 mois
            'Modéré' => 6, // 6 mois
            'Faible' => 12, // 12 mois
            default => 12
        };
        
        return $lastAnalysis->created_at->addMonths($interval);
    }
    
    private function getPatientNotifications($patient)
    {
        $notifications = collect();
        
        // Vérifier les analyses en attente
        $pendingAnalyses = $patient->analysesIA->where('statut', 'En attente')->count();
        if ($pendingAnalyses > 0) {
            $notifications->push([
                'type' => 'info',
                'title' => 'Analyses en attente',
                'message' => "Vous avez {$pendingAnalyses} analyse(s) en attente de validation médicale.",
                'date' => now()
            ]);
        }
        
        // Vérifier les analyses à risque élevé
        $highRiskAnalyses = $patient->analysesIA->where('niveau_risque', 'Élevé')->where('statut', 'Validé')->count();
        if ($highRiskAnalyses > 0) {
            $notifications->push([
                'type' => 'warning',
                'title' => 'Suivi médical recommandé',
                'message' => 'Vous avez des analyses avec un niveau de risque élevé. Un suivi médical est recommandé.',
                'date' => now()
            ]);
        }
        
        return $notifications;
    }
}