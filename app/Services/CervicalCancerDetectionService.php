<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\ImageEncryptionService;

class CervicalCancerDetectionService
{
    private string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.flask_api.url', 'http://localhost:5000');
    }

    /**
     * Vérifier si l'API Flask est disponible
     */
    public function isApiAvailable(): bool
    {
        try {
            $response = Http::timeout(5)->get($this->apiUrl);
            
            if (!$response->successful()) {
                Log::warning('API Flask non accessible', [
                    'url' => $this->apiUrl,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return false;
            }
            
            // Vérifier que la réponse est du JSON valide
            $data = $response->json();
            if (!is_array($data)) {
                Log::warning('API Flask retourne une réponse non-JSON', [
                    'url' => $this->apiUrl,
                    'body' => $response->body()
                ]);
                return false;
            }
            
            // Vérifier que l'API retourne le bon format
            if (!isset($data['status']) || $data['status'] !== 'OK') {
                Log::warning('API Flask retourne un statut invalide', [
                    'url' => $this->apiUrl,
                    'data' => $data
                ]);
                return false;
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification de l\'API Flask', [
                'url' => $this->apiUrl,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Analyser une image pour détecter le cancer cervical
     */
    public function analyzeImage(UploadedFile $image): array
    {
        try {
            // Valider le fichier image
            $this->validateImage($image);

            Log::info('Début analyse image', [
                'filename' => $image->getClientOriginalName(),
                'size' => $image->getSize(),
                'api_url' => $this->apiUrl
            ]);

            // Envoyer l'image à l'API Flask
            $response = Http::timeout(30)
                ->attach('image', file_get_contents($image->path()), $image->getClientOriginalName())
                ->post($this->apiUrl . '/predict');

            Log::info('Réponse API Flask', [
                'status' => $response->status(),
                'body_preview' => substr($response->body(), 0, 500)
            ]);

            if (!$response->successful()) {
                $errorBody = $response->body();
                Log::error('Erreur API Flask', [
                    'status' => $response->status(),
                    'body' => $errorBody
                ]);
                
                // Vérifier si c'est une page d'erreur HTML
                if (strpos($errorBody, '<!DOCTYPE') === 0 || strpos($errorBody, '<html') !== false) {
                    throw new \Exception('L\'API Flask retourne une page d\'erreur HTML. Vérifiez que l\'API fonctionne correctement.');
                }
                
                throw new \Exception('Erreur API Flask (HTTP ' . $response->status() . '): ' . $errorBody);
            }

            // Décoder la réponse JSON
            $result = $response->json();
            
            if (!is_array($result)) {
                Log::error('Réponse API non-JSON', [
                    'body' => $response->body()
                ]);
                throw new \Exception('L\'API Flask retourne une réponse non-JSON. Réponse: ' . substr($response->body(), 0, 200));
            }

            // Vérifier si c'est une réponse d'erreur
            if (isset($result['error'])) {
                Log::error('Erreur analyse IA', ['error' => $result['error']]);
                throw new \Exception($result['error']);
            }

            // Vérifier si c'est une réponse de succès
            if (isset($result['success']) && !$result['success']) {
                $error = $result['error'] ?? 'Erreur inconnue';
                Log::error('Erreur analyse IA', ['error' => $error]);
                throw new \Exception($error);
            }

            // Vérifier les champs requis
            if (!isset($result['classe_predite']) || !isset($result['probabilite'])) {
                Log::error('Format de réponse API invalide', [
                    'result' => $result
                ]);
                throw new \Exception('Format de réponse API invalide - champs manquants');
            }

            Log::info('Analyse IA réussie', [
                'classe' => $result['classe_predite'],
                'probabilite' => $result['probabilite']
            ]);

            return [
                'success' => true,
                'classe_predite' => $result['classe_predite'],
                'probabilite' => $result['probabilite'],
                'toutes_probabilites' => $result['toutes_probabilites'] ?? [],
                'risque' => $result['risque'] ?? $this->calculateRisk($result['probabilite']),
                'interpretation' => $this->getInterpretation($result['classe_predite'], $result['probabilite'])
            ];

        } catch (\Exception $e) {
            Log::error('Erreur analyse image', [
                'error' => $e->getMessage(),
                'file' => $image->getClientOriginalName() ?? 'unknown',
                'api_url' => $this->apiUrl
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Valider le fichier image
     */
    private function validateImage(UploadedFile $image): void
    {
        // Vérifier la taille (max 10MB)
        if ($image->getSize() > 10 * 1024 * 1024) {
            throw new \Exception('L\'image est trop volumineuse (max 10MB)');
        }

        // Vérifier le type MIME
        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($image->getMimeType(), $allowedMimes)) {
            throw new \Exception('Format d\'image non supporté. Utilisez JPG ou PNG.');
        }
    }

    /**
     * Calculer le niveau de risque basé sur la probabilité
     */
    private function calculateRisk(float $probabilite): string
    {
        if ($probabilite >= 0.8) {
            return 'Élevé';
        } elseif ($probabilite >= 0.6) {
            return 'Modéré';
        } else {
            return 'Faible';
        }
    }

    /**
     * Obtenir l'interprétation médicale
     */
    private function getInterpretation(string $classe, float $probabilite): string
    {
        $interpretations = [
            'Dyskeratotic' => 'Cellules dyskeratotiques détectées. Anomalies de la kératinisation cellulaire.',
            'Koilocytotic' => 'Cellules koilocytotiques détectées. Possibles signes d\'infection HPV.',
            'Metaplastic' => 'Cellules métaplasiques détectées. Transformation cellulaire normale.',
            'Parabasal' => 'Cellules parabasales détectées. Cellules de la couche profonde de l\'épithélium.',
            'Superficial-Intermediate' => 'Cellules superficielles-intermédiaires détectées. Cellules matures normales.'
        ];

        $baseInterpretation = $interpretations[$classe] ?? 'Classification non reconnue.';
        
        $confidence = '';
        if ($probabilite >= 0.9) {
            $confidence = ' Résultat très fiable.';
        } elseif ($probabilite >= 0.7) {
            $confidence = ' Résultat fiable.';
        } else {
            $confidence = ' Résultat à confirmer par un expert.';
        }

        return $baseInterpretation . $confidence;
    }
}