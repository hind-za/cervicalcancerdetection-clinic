<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\AnalyseIA;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class HistoriqueController extends Controller
{
    public function index(Request $request)
    {
        // Vérifier que l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à l\'historique');
        }

        // Log de l'accès à l'historique
        AuditLog::logAction(
            'view',
            'HistoriqueIndex',
            null,
            null,
            ['filters' => $request->all()],
            'medium',
            'Consultation de l\'historique des analyses'
        );

        $query = AnalyseIA::with(['patient', 'analyste', 'validateur'])
                          ->orderBy('created_at', 'desc');

        // Filtres selon le rôle
        $user = Auth::user();
        if ($user->role === 'doctor') {
            // Docteur ne voit que les analyses qui lui sont assignées ou qu'il a validées
            $query->where(function($q) use ($user) {
                $q->where('statut', 'En attente')
                  ->orWhere('valide_par', $user->id);
            });
        } elseif ($user->role === 'admin') {
            // Admin voit toutes les analyses
        } else {
            // Autres rôles : accès refusé
            abort(403, 'Accès non autorisé');
        }

        // Filtres de recherche
        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->filled('classe')) {
            $query->where('classe_predite', $request->classe);
        }

        if ($request->filled('statut')) {
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
        $patients = Patient::orderBy('nom')->orderBy('prenom')->get();
        
        // Classes disponibles
        $classes = [
            'Dyskeratotic',
            'Koilocytotic', 
            'Metaplastic',
            'Parabasal',
            'Superficial-Intermediate'
        ];

        // Statistiques selon le rôle
        if ($user->role === 'admin') {
            $stats = [
                'total' => AnalyseIA::count(),
                'en_attente' => AnalyseIA::where('statut', 'En attente')->count(),
                'valide' => AnalyseIA::where('statut', 'Validé')->count(),
                'a_revoir' => AnalyseIA::where('statut', 'À revoir')->count(),
            ];
        } else {
            $stats = [
                'total' => AnalyseIA::where(function($q) use ($user) {
                    $q->where('statut', 'En attente')->orWhere('valide_par', $user->id);
                })->count(),
                'en_attente' => AnalyseIA::where('statut', 'En attente')->count(),
                'valide' => AnalyseIA::where('statut', 'Validé')->where('valide_par', $user->id)->count(),
                'a_revoir' => AnalyseIA::where('statut', 'À revoir')->where('valide_par', $user->id)->count(),
            ];
        }

        return view('historique.index', compact('analyses', 'patients', 'classes', 'stats'));
    }

    public function show(AnalyseIA $analyse)
    {
        // Vérifier que l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour voir cette analyse');
        }

        $user = Auth::user();
        
        // Vérifier les permissions d'accès
        if ($user->role === 'doctor') {
            // Docteur peut voir seulement les analyses en attente ou qu'il a validées
            if ($analyse->statut !== 'En attente' && $analyse->valide_par !== $user->id) {
                abort(403, 'Vous ne pouvez voir que les analyses qui vous sont assignées');
            }
        } elseif ($user->role === 'admin') {
            // Admin peut voir toutes les analyses
        } else {
            abort(403, 'Accès non autorisé');
        }

        // Log de l'accès à l'analyse
        AuditLog::logAction(
            'view',
            'AnalyseIA',
            $analyse->id,
            null,
            null,
            'medium',
            'Consultation de l\'analyse ID: ' . $analyse->id . ' pour le patient ' . $analyse->patient->nom_complet
        );

        $analyse->load(['patient', 'analyste', 'validateur']);
        return view('historique.show', compact('analyse'));
    }

    public function destroy(AnalyseIA $analyse)
    {
        // Vérifier que l'utilisateur est admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Seuls les administrateurs peuvent supprimer des analyses');
        }

        // Log de la suppression
        AuditLog::logAction(
            'delete',
            'AnalyseIA',
            $analyse->id,
            $analyse->toArray(),
            null,
            'critical',
            'Suppression de l\'analyse ID: ' . $analyse->id . ' pour le patient ' . $analyse->patient->nom_complet
        );

        // Supprimer l'image du stockage
        if (Storage::disk('public')->exists($analyse->chemin_image)) {
            Storage::disk('public')->delete($analyse->chemin_image);
        }
        
        $analyse->delete();
        
        return redirect()->route('historique.index')
                        ->with('success', 'Analyse supprimée avec succès');
    }
}