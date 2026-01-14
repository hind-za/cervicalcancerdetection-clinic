<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ProtectPatientData
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
            // Ne pas rediriger si on est déjà sur la page de login
            if ($request->routeIs('login') || $request->routeIs('register')) {
                return $next($request);
            }
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Log de l'accès aux données sensibles
        if ($this->isSensitiveRoute($request)) {
            \Log::info('Sensitive data access', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'route' => $request->route()->getName(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()
            ]);
        }

        // Vérifier les permissions selon le rôle et l'action
        if ($request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('PATCH')) {
            return $this->handleModificationRequest($request, $next, $user);
        }

        return $next($request);
    }

    /**
     * Gérer les requêtes de modification
     */
    private function handleModificationRequest(Request $request, Closure $next, $user)
    {
        $route = $request->route()->getName();
        
        // Règles de modification selon le rôle
        switch ($user->role) {
            case 'admin':
                // Admin peut créer des analyses mais pas modifier les patients directement
                if (str_contains($route, 'patients.update') || str_contains($route, 'patients.destroy')) {
                    \Log::warning('Unauthorized patient modification attempt', [
                        'user_id' => $user->id,
                        'route' => $route,
                        'ip' => $request->ip()
                    ]);
                    abort(403, 'Modification des données patient non autorisée pour les administrateurs');
                }
                break;
                
            case 'doctor':
                // Docteur peut valider mais pas modifier les analyses originales
                if (str_contains($route, 'admin.')) {
                    \Log::warning('Unauthorized admin action attempt', [
                        'user_id' => $user->id,
                        'route' => $route,
                        'ip' => $request->ip()
                    ]);
                    abort(403, 'Actions administrateur non autorisées pour les docteurs');
                }
                break;
        }

        return $next($request);
    }

    /**
     * Vérifier si la route accède à des données sensibles
     */
    private function isSensitiveRoute(Request $request): bool
    {
        $sensitiveRoutes = [
            'patients.',
            'doctor.analyse',
            'admin.analyze',
            'historique.'
        ];

        $routeName = $request->route()->getName() ?? '';
        
        foreach ($sensitiveRoutes as $sensitive) {
            if (str_contains($routeName, $sensitive)) {
                return true;
            }
        }

        return false;
    }
}
