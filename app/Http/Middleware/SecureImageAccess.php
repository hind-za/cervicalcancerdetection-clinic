<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\AnalyseIA;
use App\Services\ImageEncryptionService;
use Symfony\Component\HttpFoundation\Response;

class SecureImageAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier que l'utilisateur est connecté
        if (!Auth::check()) {
            abort(403, 'Accès non autorisé');
        }

        // Extraire le chemin de l'image depuis l'URL
        $imagePath = $request->route('path');
        
        // Nettoyer le chemin - enlever le préfixe analyses-ia/ s'il existe
        $cleanPath = str_replace('analyses-ia/', '', $imagePath);
        $fullPath = 'analyses-ia/' . $cleanPath;
        
        // Vérifier que l'image appartient à une analyse existante
        $analyse = AnalyseIA::where('chemin_image', $fullPath)
                           ->orWhere('chemin_image', $cleanPath)
                           ->first();
        
        if (!$analyse) {
            abort(404, 'Image non trouvée');
        }

        // Vérifier les permissions selon le rôle
        $user = Auth::user();
        
        switch ($user->role) {
            case 'admin':
                // Admin peut voir toutes les images
                break;
                
            case 'doctor':
                // Docteur peut voir toutes les images pour validation
                break;
                
            case 'patient':
                // Patient peut voir ses propres analyses
                if ($analyse->patient && $analyse->patient->email === $user->email) {
                    // Autorisé - le patient peut voir ses propres analyses
                } else {
                    abort(403, 'Vous ne pouvez voir que vos propres analyses');
                }
                break;
                
            default:
                abort(403, 'Rôle non autorisé');
        }

        // Log de l'accès pour audit
        \Log::info('Image access', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'image_path' => $imagePath,
            'analyse_id' => $analyse->id,
            'patient_id' => $analyse->patient_id,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return $next($request);
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
}
