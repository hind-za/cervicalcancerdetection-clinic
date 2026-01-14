<?php

namespace App\Http\Controllers;

use App\Services\CervicalCancerDetectionService;
use App\Services\ImageEncryptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CervicalCancerController extends Controller
{
    private CervicalCancerDetectionService $detectionService;

    public function __construct(CervicalCancerDetectionService $detectionService)
    {
        $this->detectionService = $detectionService;
    }

    /**
     * Afficher la page d'analyse
     */
    public function index()
    {
        return view('cervical-cancer.index');
    }

    /**
     * Analyser une image
     */
    public function analyze(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:10240', // 10MB max
        ], [
            'image.required' => 'Veuillez sélectionner une image.',
            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'L\'image doit être au format JPEG, PNG ou JPG.',
            'image.max' => 'L\'image ne doit pas dépasser 10MB.'
        ]);

        try {
            $image = $request->file('image');
            
            // Sauvegarder l'image temporairement
            $imagePath = $image->store('temp', 'public');
            
            // Analyser l'image
            $result = $this->detectionService->analyzeImage($image);
            
            if ($result['success']) {
                // Chiffrer et sauvegarder l'image analysée
                $imageContent = file_get_contents($image->path());
                $finalPath = 'analyses/' . uniqid() . '_' . $image->getClientOriginalName();
                
                // Chiffrer et stocker l'image
                if (ImageEncryptionService::encryptAndStore($imageContent, $finalPath)) {
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'classe_predite' => $result['classe_predite'],
                            'probabilite' => $result['probabilite'],
                            'toutes_probabilites' => $result['toutes_probabilites'],
                            'risque' => $result['risque'],
                            'interpretation' => $result['interpretation'],
                            'image_path' => $finalPath, // Chemin chiffré
                            'timestamp' => now()->format('d/m/Y H:i:s')
                        ]
                    ]);
                } else {
                    throw new \Exception('Erreur lors du chiffrement de l\'image');
                }
            } else {
                return response()->json([
                    'success' => false,
                    'error' => $result['error']
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de l\'analyse: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifier le statut de l'API
     */
    public function apiStatus()
    {
        $isAvailable = $this->detectionService->isApiAvailable();
        
        return response()->json([
            'api_available' => $isAvailable,
            'message' => $isAvailable ? 'API disponible' : 'API non disponible'
        ]);
    }
}