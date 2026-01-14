<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Récupérer la langue depuis la session ou utiliser la langue par défaut
        $locale = Session::get('locale', config('app.locale', 'fr'));
        
        // Vérifier que la langue est supportée
        $supportedLocales = ['fr', 'en', 'ar'];
        if (in_array($locale, $supportedLocales)) {
            App::setLocale($locale);
        }
        
        return $next($request);
    }
}