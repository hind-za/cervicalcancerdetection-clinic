<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function changeLanguage(Request $request, $locale)
    {
        // Vérifier que la langue est supportée
        $supportedLocales = ['fr', 'en', 'ar'];
        
        if (in_array($locale, $supportedLocales)) {
            // Changer la langue de l'application
            App::setLocale($locale);
            
            // Sauvegarder dans la session
            Session::put('locale', $locale);
            
            return response()->json([
                'success' => true,
                'locale' => $locale,
                'message' => 'Language changed successfully'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Language not supported'
        ], 400);
    }
}