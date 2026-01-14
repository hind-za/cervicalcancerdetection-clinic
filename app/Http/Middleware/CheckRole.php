<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        if ($user->role !== $role) {
            // Rediriger vers le dashboard approprié selon le rôle au lieu d'abort(403)
            return match($user->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'doctor' => redirect()->route('doctor.dashboard'),
                'patient' => redirect()->route('patient.dashboard'),
                default => abort(403, 'Accès non autorisé')
            };
        }

        return $next($request);
    }
}