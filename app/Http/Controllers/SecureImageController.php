<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Models\AnalyseIA;
use App\Services\ImageEncryptionService;

class SecureImageController extends Controller
{
    /**
     * Servir une image d'analyse de manière sécurisée
     */
    public function show(Request $request, $path)
    {
        // Le middleware SecureImageAccess a déjà vérifié les permissions
        
        // Nettoyer le chemin - enlever le préfixe analyses-ia/ s'il existe
        $cleanPath = str_replace('analyses-ia/', '', $path);
        $fullPath = 'analyses-ia/' . $cleanPath;
        
        // Priorité 1: Essayer les images migrées dans le stockage public
        if (Storage::disk('public')->exists($fullPath)) {
            try {
                $file = Storage::disk('public')->get($fullPath);
                $mimeType = Storage::disk('public')->mimeType($fullPath);
                
                return Response::make($file, 200, [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'inline; filename="' . basename($cleanPath) . '"',
                    'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0',
                    'X-Content-Type-Options' => 'nosniff',
                    'X-Frame-Options' => 'DENY'
                ]);
            } catch (\Exception $e) {
                \Log::error('Public image access failed', [
                    'path' => $fullPath,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Priorité 2: Essayer de récupérer l'image chiffrée (pour compatibilité)
        if (ImageEncryptionService::isEncrypted($fullPath)) {
            try {
                $fileContent = ImageEncryptionService::decryptAndRetrieve($fullPath);
                
                if ($fileContent) {
                    // Déterminer le type MIME
                    $mimeType = $this->getMimeTypeFromContent($fileContent);
                    
                    return Response::make($fileContent, 200, [
                        'Content-Type' => $mimeType,
                        'Content-Disposition' => 'inline; filename="' . basename($cleanPath) . '"',
                        'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
                        'Pragma' => 'no-cache',
                        'Expires' => '0',
                        'X-Content-Type-Options' => 'nosniff',
                        'X-Frame-Options' => 'DENY'
                    ]);
                }
            } catch (\Exception $e) {
                \Log::warning('Image decryption failed, using placeholder', [
                    'path' => $fullPath,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Priorité 3: Vérifier d'autres emplacements possibles
        $alternativePaths = [
            'analyses/' . $cleanPath,
            $cleanPath
        ];
        
        foreach ($alternativePaths as $altPath) {
            if (Storage::disk('public')->exists($altPath)) {
                try {
                    $file = Storage::disk('public')->get($altPath);
                    $mimeType = Storage::disk('public')->mimeType($altPath);
                    
                    return Response::make($file, 200, [
                        'Content-Type' => $mimeType,
                        'Content-Disposition' => 'inline; filename="' . basename($cleanPath) . '"',
                        'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
                        'Pragma' => 'no-cache',
                        'Expires' => '0',
                        'X-Content-Type-Options' => 'nosniff',
                        'X-Frame-Options' => 'DENY'
                    ]);
                } catch (\Exception $e) {
                    continue;
                }
            }
        }
        
        // Dernière tentative : image placeholder
        \Log::info('Serving placeholder image', ['requested_path' => $path]);
        return $this->getPlaceholderImage();
    }

    /**
     * Télécharger une image d'analyse (avec watermark)
     */
    public function download(Request $request, $path)
    {
        // Le middleware SecureImageAccess a déjà vérifié les permissions
        
        // Nettoyer le chemin - enlever le préfixe analyses-ia/ s'il existe
        $cleanPath = str_replace('analyses-ia/', '', $path);
        $fullPath = 'analyses-ia/' . $cleanPath;
        
        $fileContent = null;
        $analyse = AnalyseIA::where('chemin_image', $fullPath)->first();
        
        if (!$analyse) {
            abort(404, 'Analyse non trouvée');
        }
        
        // Priorité 1: Essayer les images migrées dans le stockage public
        if (Storage::disk('public')->exists($fullPath)) {
            $fileContent = Storage::disk('public')->get($fullPath);
        }
        // Priorité 2: Essayer l'image chiffrée
        elseif (ImageEncryptionService::isEncrypted($fullPath)) {
            $fileContent = ImageEncryptionService::decryptAndRetrieve($fullPath);
        }
        // Priorité 3: Vérifier d'autres emplacements
        else {
            $alternativePaths = [
                'analyses/' . $cleanPath,
                $cleanPath
            ];
            
            foreach ($alternativePaths as $altPath) {
                if (Storage::disk('public')->exists($altPath)) {
                    $fileContent = Storage::disk('public')->get($altPath);
                    break;
                }
            }
        }
        
        if (!$fileContent) {
            abort(404, 'Image non trouvée');
        }
        
        // Log du téléchargement
        \Log::info('Image download', [
            'user_id' => auth()->id(),
            'analyse_id' => $analyse->id,
            'patient_id' => $analyse->patient_id,
            'image_path' => $path,
            'ip' => $request->ip()
        ]);

        $fileName = 'analyse_' . $analyse->patient->numero_dossier . '_' . $analyse->id . '_' . basename($cleanPath);
        
        return Response::make($fileContent, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Content-Length' => strlen($fileContent),
            'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Déterminer le type MIME à partir du contenu de l'image
     */
    private function getMimeTypeFromContent(string $content): string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($content);
        
        // Types MIME supportés pour les images médicales
        $allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/jpg'
        ];
        
        return in_array($mimeType, $allowedTypes) ? $mimeType : 'image/jpeg';
    }

    /**
     * Retourner une image placeholder en cas d'erreur
     */
    private function getPlaceholderImage()
    {
        // Créer une image placeholder simple avec du contenu SVG
        $svg = '<?xml version="1.0" encoding="UTF-8"?>
<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">
    <rect width="200" height="200" fill="#f8f9fa" stroke="#dee2e6" stroke-width="2"/>
    <circle cx="100" cy="80" r="25" fill="#6c757d"/>
    <rect x="75" y="120" width="50" height="30" rx="5" fill="#6c757d"/>
    <text x="100" y="170" text-anchor="middle" font-family="Arial, sans-serif" font-size="12" fill="#6c757d">
        Image indisponible
    </text>
</svg>';
        
        return Response::make($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'inline; filename="placeholder.svg"',
            'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }
}
