<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\AnalyseImage;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AnalyseController extends Controller
{
    public function historique(Request $request)
    {
        $query = AnalyseImage::with(['patient', 'validateur'])
                            ->orderBy('created_at', 'desc');

        // Filtres
        if ($request->filled('periode')) {
            switch ($request->periode) {
                case 'last_7_days':
                    $query->where('created_at', '>=', Carbon::now()->subDays(7));
                    break;
                case 'last_month':
                    $query->where('created_at', '>=', Carbon::now()->subMonth());
                    break;
                case 'last_3_months':
                    $query->where('created_at', '>=', Carbon::now()->subMonths(3));
                    break;
            }
        }

        if ($request->filled('statut') && $request->statut !== 'all') {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('resultat') && $request->resultat !== 'all') {
            $query->where('resultat', $request->resultat);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('numero_dossier', 'like', "%{$search}%");
            });
        }

        $analyses = $query->paginate(15);

        // Statistiques
        $stats = [
            'total' => AnalyseImage::count(),
            'normal' => AnalyseImage::where('resultat', 'Normal')->count(),
            'surveiller' => AnalyseImage::where('resultat', 'À Surveiller')->count(),
            'anomalie' => AnalyseImage::where('resultat', 'Anomalie Détectée')->count(),
        ];

        return view('historique', compact('analyses', 'stats'));
    }

    public function consulterResultats(Request $request)
    {
        $query = AnalyseImage::with(['patient', 'validateur'])
                            ->where('statut', '!=', 'Validé')
                            ->orderBy('created_at', 'desc');

        // Filtres pour les résultats à consulter
        if ($request->filled('resultat') && $request->resultat !== 'all') {
            $query->where('resultat', $request->resultat);
        }

        if ($request->filled('statut') && $request->statut !== 'all') {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('numero_dossier', 'like', "%{$search}%");
            });
        }

        $analyses = $query->paginate(12);

        // Statistiques pour les résultats à consulter
        $stats = [
            'en_attente' => AnalyseImage::where('statut', 'En attente')->count(),
            'a_revoir' => AnalyseImage::where('statut', 'À revoir')->count(),
            'urgent' => AnalyseImage::where('resultat', 'Anomalie Détectée')
                                   ->where('statut', 'En attente')->count(),
        ];

        return view('consulter-resultats', compact('analyses', 'stats'));
    }

    public function doctorDashboard()
    {
        // Statistiques pour le dashboard médecin
        $stats = [
            'cases_to_validate' => AnalyseImage::where('statut', 'En attente')->count(),
            'validated_cases' => AnalyseImage::where('statut', 'Validé')->count(),
            'urgent_cases' => AnalyseImage::where('resultat', 'Anomalie Détectée')
                                         ->where('statut', 'En attente')->count(),
            'total_patients' => Patient::count(),
        ];

        // Analyses en attente de validation (pour le tableau)
        $pendingAnalyses = AnalyseImage::with(['patient'])
                                      ->where('statut', '!=', 'Validé')
                                      ->orderBy('created_at', 'desc')
                                      ->limit(5)
                                      ->get();

        // Alertes médicales
        $alerts = [
            'urgent' => AnalyseImage::with('patient')
                                   ->where('resultat', 'Anomalie Détectée')
                                   ->where('statut', 'En attente')
                                   ->orderBy('created_at', 'desc')
                                   ->first(),
            'pending_count' => AnalyseImage::where('statut', 'En attente')->count(),
            'follow_up' => AnalyseImage::with('patient')
                                      ->where('resultat', 'À Surveiller')
                                      ->where('statut', 'Validé')
                                      ->orderBy('created_at', 'desc')
                                      ->first(),
        ];

        return view('doctor.dashboard', compact('stats', 'pendingAnalyses', 'alerts'));
    }

    public function validerAnalyse(Request $request, AnalyseImage $analyse)
    {
        $request->validate([
            'statut' => 'required|in:Validé,À revoir',
            'commentaires' => 'nullable|string',
        ]);

        $analyse->update([
            'statut' => $request->statut,
            'valide_par' => auth()->id(),
            'date_validation' => now(),
            'commentaires' => $request->commentaires,
        ]);

        return back()->with('success', 'Analyse ' . strtolower($request->statut) . 'e avec succès !');
    }
}
