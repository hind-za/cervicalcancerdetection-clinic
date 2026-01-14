<?php

namespace App\Http\Controllers;

use App\Services\CervicalCancerDetectionService;
use App\Services\ImageEncryptionService;
use App\Models\Patient;
use App\Models\AnalyseIA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    private CervicalCancerDetectionService $detectionService;

    public function __construct(CervicalCancerDetectionService $detectionService)
    {
        $this->detectionService = $detectionService;
    }

    /**
     * Afficher le dashboard admin
     */
    public function dashboard(Request $request)
    {
        // Vérifier que l'utilisateur est connecté et admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }

        // Récupérer les patients pour la sélection
        $patients = Patient::orderBy('nom')->orderBy('prenom')->get();
        
        // Patient pré-sélectionné si passé en paramètre
        $selectedPatient = null;
        if ($request->has('patient_id')) {
            $selectedPatient = Patient::find($request->patient_id);
        }
        
        // Statistiques récentes
        $stats = [
            'analyses_today' => AnalyseIA::whereDate('created_at', today())->count(),
            'analyses_week' => AnalyseIA::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'patients_total' => Patient::count(),
            'analyses_pending' => AnalyseIA::where('statut', 'En attente')->count()
        ];

        return view('admin.dashboard', compact('patients', 'stats', 'selectedPatient'));
    }

    /**
     * Analyser une image dans le dashboard admin
     */
    public function analyzeImage(Request $request)
    {
        // Vérifier que l'utilisateur est connecté et admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'error' => 'Accès non autorisé'], 403);
        }

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,tiff|max:10240', // 10MB max
            'patient_id' => 'required|exists:patients,id',
            'save_analysis' => 'nullable' // Accepter n'importe quelle valeur
        ], [
            'image.required' => 'Veuillez sélectionner une image.',
            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'L\'image doit être au format JPEG, PNG, JPG ou TIFF.',
            'image.max' => 'L\'image ne doit pas dépasser 10MB.',
            'patient_id.required' => 'Veuillez sélectionner un patient.',
            'patient_id.exists' => 'Patient non trouvé.'
        ]);

        try {
            $image = $request->file('image');
            $patient = Patient::findOrFail($request->patient_id);
            $startTime = microtime(true);
            
            // Analyser l'image avec l'IA
            $result = $this->detectionService->analyzeImage($image);
            $analysisTime = microtime(true) - $startTime;
            
            if ($result['success']) {
                // Chiffrer et sauvegarder l'image analysée
                $imageContent = file_get_contents($image->path());
                $imagePath = 'analyses-ia/' . uniqid() . '_' . $image->getClientOriginalName();
                
                // Chiffrer et stocker l'image
                if (!ImageEncryptionService::encryptAndStore($imageContent, $imagePath)) {
                    throw new \Exception('Erreur lors du chiffrement de l\'image');
                }
                
                // Préparer les données pour l'affichage
                $analysisData = [
                    'success' => true,
                    'image_path' => route('secure.image.show', $imagePath), // URL sécurisée
                    'image_name' => $image->getClientOriginalName(),
                    'patient' => [
                        'id' => $patient->id,
                        'nom_complet' => $patient->nom_complet,
                        'numero_dossier' => $patient->numero_dossier
                    ],
                    'classe_predite' => $result['classe_predite'],
                    'probabilite' => $result['probabilite'],
                    'toutes_probabilites' => $result['toutes_probabilites'],
                    'risque' => $result['risque'],
                    'interpretation' => $result['interpretation'],
                    'timestamp' => now()->format('d/m/Y H:i:s'),
                    'analyzed_by' => Auth::user()->name,
                    'file_size' => $this->formatBytes($image->getSize()),
                    'dimensions' => $this->getImageDimensions($image),
                    'recommendations' => $this->getRecommendations($result['classe_predite'], $result['probabilite']),
                    'analysis_time' => round($analysisTime, 3)
                ];

                // Toujours sauvegarder l'analyse en base de données
                $analyseIA = AnalyseIA::create([
                    'patient_id' => $patient->id,
                    'user_id' => Auth::id(),
                    'nom_image' => $image->getClientOriginalName(),
                    'chemin_image' => $imagePath,
                    'taille_image' => $this->formatBytes($image->getSize()),
                    'dimensions_image' => $this->getImageDimensions($image),
                    'classe_predite' => $result['classe_predite'],
                    'probabilite' => $result['probabilite'],
                    'toutes_probabilites' => $result['toutes_probabilites'],
                    'niveau_risque' => $result['risque'],
                    'interpretation' => $result['interpretation'],
                    'recommandations' => $this->getRecommendations($result['classe_predite'], $result['probabilite']),
                    'temps_analyse' => $analysisTime,
                    'statut' => 'En attente'
                ]);

                $analysisData['analysis_id'] = $analyseIA->id;
                $analysisData['saved'] = true;
                
                return response()->json($analysisData);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => $result['error']
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Erreur analyse image', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de l\'analyse: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ajouter des commentaires administratifs à une analyse
     */
    public function saveAnalysis(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'error' => 'Accès non autorisé'], 403);
        }

        $request->validate([
            'analysis_id' => 'required|exists:analyses_ia,id',
            'commentaires' => 'nullable|string|max:1000'
        ]);

        try {
            $analyse = AnalyseIA::findOrFail($request->analysis_id);
            
            // L'admin peut seulement ajouter des commentaires, pas valider
            // La validation doit être faite par un docteur
            if ($request->has('commentaires')) {
                $analyse->update([
                    'commentaires_admin' => $request->commentaires,
                    // Le statut reste 'En attente' pour que le docteur puisse valider
                    'statut' => 'En attente'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Commentaires administratifs ajoutés avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir l'historique des analyses d'un patient
     */
    public function getPatientAnalyses(Request $request, $patientId)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'error' => 'Accès non autorisé'], 403);
        }

        try {
            $patient = Patient::findOrFail($patientId);
            $analyses = AnalyseIA::where('patient_id', $patientId)
                ->with(['analyste', 'validateur'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'patient' => $patient,
                'analyses' => $analyses->map(function ($analyse) {
                    return [
                        'id' => $analyse->id,
                        'date' => $analyse->created_at->format('d/m/Y H:i'),
                        'classe_predite' => $analyse->classe_predite,
                        'probabilite' => $analyse->confidence_percent,
                        'niveau_risque' => $analyse->niveau_risque,
                        'statut' => $analyse->statut,
                        'analyste' => $analyse->analyste->name,
                        'image_url' => Storage::url($analyse->chemin_image)
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifier le statut de l'API IA
     */
    public function checkApiStatus()
    {
        // Vérifier que l'utilisateur est connecté et admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'error' => 'Accès non autorisé'], 403);
        }

        $isAvailable = $this->detectionService->isApiAvailable();
        
        return response()->json([
            'api_available' => $isAvailable,
            'message' => $isAvailable ? 'API IA disponible' : 'API IA non disponible',
            'timestamp' => now()->format('H:i:s')
        ]);
    }

    /**
     * Obtenir les recommandations médicales
     */
    private function getRecommendations(string $classe, float $probabilite): array
    {
        $recommendations = [
            'Dyskeratotic' => [
                'Surveillance rapprochée recommandée',
                'Consultation spécialisée conseillée',
                'Répéter l\'examen dans 6 mois'
            ],
            'Koilocytotic' => [
                'Test HPV recommandé',
                'Surveillance gynécologique renforcée',
                'Évaluation complémentaire nécessaire'
            ],
            'Metaplastic' => [
                'Résultat dans les limites normales',
                'Surveillance de routine',
                'Prochain contrôle selon protocole'
            ],
            'Parabasal' => [
                'Cellules de régénération normale',
                'Pas d\'action immédiate requise',
                'Surveillance standard'
            ],
            'Superficial-Intermediate' => [
                'Cellules matures normales',
                'Résultat satisfaisant',
                'Surveillance de routine'
            ]
        ];

        $baseRecommendations = $recommendations[$classe] ?? ['Consultation médicale recommandée'];
        
        // Ajouter des recommandations selon la confiance
        if ($probabilite < 0.7) {
            $baseRecommendations[] = 'Confirmation par un second examen recommandée';
        }
        
        return $baseRecommendations;
    }

    /**
     * Formater la taille du fichier
     */
    private function formatBytes(int $size): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }

    /**
     * Obtenir les dimensions de l'image
     */
    private function getImageDimensions($image): string
    {
        try {
            $imageInfo = getimagesize($image->path());
            return $imageInfo[0] . ' x ' . $imageInfo[1] . ' pixels';
        } catch (\Exception $e) {
            return 'Dimensions non disponibles';
        }
    }
}