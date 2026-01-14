<?php

namespace App\Http\Controllers;

use App\Models\AnalyseIA;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DoctorController extends Controller
{
    /**
     * Dashboard du docteur - Analyses en attente de validation
     */
    public function dashboard()
    {
        // Vérifier que l'utilisateur est connecté et docteur
        if (!Auth::check() || Auth::user()->role !== 'doctor') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }

        // Analyses en attente de validation (effectuées par l'admin)
        $analysesEnAttente = AnalyseIA::with(['patient', 'analyste'])
                                    ->where('statut', 'En attente')
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(12);

        // Analyses déjà validées par ce docteur (modifiables)
        $analysesValidees = AnalyseIA::with(['patient', 'analyste'])
                                   ->where('valide_par', Auth::id())
                                   ->whereIn('statut', ['Validé', 'Rejeté', 'À revoir'])
                                   ->orderBy('date_validation', 'desc')
                                   ->limit(6)
                                   ->get();

        // Statistiques pour le docteur
        $stats = [
            'en_attente' => AnalyseIA::where('statut', 'En attente')->count(),
            'validees_aujourd_hui' => AnalyseIA::where('statut', 'Validé')
                                              ->where('valide_par', Auth::id())
                                              ->whereDate('date_validation', today())
                                              ->count(),
            'a_revoir' => AnalyseIA::where('statut', 'À revoir')->count(),
            'total_validees' => AnalyseIA::where('statut', 'Validé')
                                        ->where('valide_par', Auth::id())
                                        ->count(),
            'modifiees' => AnalyseIA::where('valide_par', Auth::id())
                                   ->whereNotNull('date_derniere_modification')
                                   ->count()
        ];

        // Analyses récemment validées par ce docteur
        $recentValidations = AnalyseIA::with(['patient', 'analyste'])
                                     ->where('valide_par', Auth::id())
                                     ->orderBy('date_validation', 'desc')
                                     ->limit(5)
                                     ->get();

        return view('doctor.dashboard', compact('analysesEnAttente', 'analysesValidees', 'stats', 'recentValidations'));
    }

    /**
     * Afficher les détails d'une analyse pour validation
     */
    public function showAnalyse(AnalyseIA $analyse)
    {
        if (!Auth::check() || Auth::user()->role !== 'doctor') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }

        $analyse->load(['patient', 'analyste', 'validateur']);
        
        return view('doctor.analyse-detail', compact('analyse'));
    }

    /**
     * Valider une analyse
     */
    public function validerAnalyse(Request $request, AnalyseIA $analyse)
    {
        if (!Auth::check() || Auth::user()->role !== 'doctor') {
            return response()->json(['success' => false, 'error' => 'Accès non autorisé'], 403);
        }

        $request->validate([
            'decision' => 'required|in:valide,rejete,a_revoir',
            'classe_finale' => 'nullable|string',
            'commentaires_medecin' => 'nullable|string|max:1000',
            'recommandations_finales' => 'nullable|string|max:1000'
        ]);

        // Déterminer le nouveau statut
        $nouveauStatut = match($request->decision) {
            'valide' => 'Validé',
            'rejete' => 'Rejeté',
            'a_revoir' => 'À revoir',
            default => 'En attente'
        };

        // Vérifier si c'est une modification d'une validation existante
        $isModification = $analyse->valide_par && $analyse->date_validation;
        
        // Mettre à jour l'analyse
        $analyse->update([
            'statut' => $nouveauStatut,
            'valide_par' => Auth::id(),
            'date_validation' => now(),
            'commentaires_medecin' => $request->commentaires_medecin,
            'classe_finale_medecin' => $request->classe_finale ?? $analyse->classe_predite,
            'decision_medecin' => $request->decision,
            'recommandations_finales' => $request->recommandations_finales,
            'date_derniere_modification' => $isModification ? now() : null
        ]);

        $message = $isModification 
            ? 'Validation modifiée avec succès' 
            : 'Analyse ' . strtolower($nouveauStatut) . 'e avec succès';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'statut' => $nouveauStatut,
                'is_modification' => $isModification
            ]);
        }

        return redirect()->route('doctor.dashboard')
                        ->with('success', $message);
    }

    /**
     * Historique des validations du docteur
     */
    public function historique(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'doctor') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }

        $query = AnalyseIA::with(['patient', 'analyste'])
                          ->where('valide_par', Auth::id())
                          ->orderBy('date_validation', 'desc');

        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('periode')) {
            switch ($request->periode) {
                case 'today':
                    $query->whereDate('date_validation', today());
                    break;
                case 'week':
                    $query->whereBetween('date_validation', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('date_validation', now()->month);
                    break;
            }
        }

        $validations = $query->paginate(15);

        return view('doctor.historique', compact('validations'));
    }

    /**
     * Analyses nécessitant une révision
     */
    public function aRevoir()
    {
        if (!Auth::check() || Auth::user()->role !== 'doctor') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }

        $analysesARevoir = AnalyseIA::with(['patient', 'analyste'])
                                   ->where('statut', 'À revoir')
                                   ->orderBy('created_at', 'desc')
                                   ->paginate(12);

        return view('doctor.a-revoir', compact('analysesARevoir'));
    }
}