<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\AnalyseImage;
use App\Services\ImageEncryptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::with(['analyses', 'analysesIA']);

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('numero_dossier', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('sexe')) {
            $query->where('sexe', $request->sexe);
        }

        if ($request->filled('age_range')) {
            $ageRange = $request->age_range;
            $today = now();
            
            switch ($ageRange) {
                case '18-30':
                    $query->whereBetween('date_naissance', [
                        $today->copy()->subYears(30)->format('Y-m-d'),
                        $today->copy()->subYears(18)->format('Y-m-d')
                    ]);
                    break;
                case '31-45':
                    $query->whereBetween('date_naissance', [
                        $today->copy()->subYears(45)->format('Y-m-d'),
                        $today->copy()->subYears(31)->format('Y-m-d')
                    ]);
                    break;
                case '46-60':
                    $query->whereBetween('date_naissance', [
                        $today->copy()->subYears(60)->format('Y-m-d'),
                        $today->copy()->subYears(46)->format('Y-m-d')
                    ]);
                    break;
                case '60+':
                    $query->where('date_naissance', '<', $today->copy()->subYears(60)->format('Y-m-d'));
                    break;
            }
        }

        // Tri
        switch ($request->get('sort', 'recent')) {
            case 'name':
                $query->orderBy('nom')->orderBy('prenom');
                break;
            case 'analyses':
                $query->withCount(['analyses', 'analysesIA'])->orderByRaw('(analyses_count + analyses_i_a_count) desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $patients = $query->paginate(15);
        
        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'date_naissance' => 'required|date|before:today',
            'sexe' => 'required|in:F,M',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'adresse' => 'nullable|string',
            'antecedents_medicaux' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Générer un numéro de dossier unique
        $numeroDossier = 'PAT-' . date('Y') . '-' . str_pad(Patient::count() + 1, 4, '0', STR_PAD_LEFT);

        $patient = Patient::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'date_naissance' => $request->date_naissance,
            'sexe' => $request->sexe,
            'telephone' => $request->telephone,
            'email' => $request->email,
            'adresse' => $request->adresse,
            'numero_dossier' => $numeroDossier,
            'antecedents_medicaux' => $request->antecedents_medicaux,
            'notes' => $request->notes,
        ]);

        return redirect()->route('patients.show', $patient)
                        ->with('success', 'Patient ajouté avec succès !');
    }

    public function show(Patient $patient)
    {
        $patient->load(['analyses' => function($query) {
            $query->orderBy('created_at', 'desc');
        }, 'analysesIA' => function($query) {
            $query->orderBy('created_at', 'desc');
        }]);
        
        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'date_naissance' => 'required|date|before:today',
            'sexe' => 'required|in:F,M',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'adresse' => 'nullable|string',
            'antecedents_medicaux' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $patient->update($request->all());

        return redirect()->route('patients.show', $patient)
                        ->with('success', 'Patient mis à jour avec succès !');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();
        return redirect()->route('patients.index')
                        ->with('success', 'Patient supprimé avec succès !');
    }

    public function addAnalyse(Request $request, Patient $patient)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,tiff|max:10240',
            'resultat' => 'required|in:Normal,Anomalie Détectée,À Surveiller',
            'confiance' => 'required|numeric|min:0|max:100',
            'commentaires' => 'nullable|string',
        ]);

        // Chiffrer et sauvegarder l'image
        $image = $request->file('image');
        $imageContent = file_get_contents($image->path());
        $imageName = time() . '_' . $image->getClientOriginalName();
        $imagePath = 'analyses/' . $imageName;
        
        // Chiffrer et stocker l'image
        if (!ImageEncryptionService::encryptAndStore($imageContent, $imagePath)) {
            return back()->with('error', 'Erreur lors du chiffrement de l\'image');
        }

        // Créer l'analyse
        $analyse = AnalyseImage::create([
            'patient_id' => $patient->id,
            'nom_image' => $imageName,
            'chemin_image' => $imagePath,
            'resultat' => $request->resultat,
            'confiance' => $request->confiance,
            'commentaires' => $request->commentaires,
            'details' => [
                'qualite_image' => 'Excellente',
                'zones_analysees' => ['Col de l\'utérus', 'Cellules épithéliales'],
                'algorithme' => 'CervicalCare AI v2.1'
            ],
            'temps_analyse' => rand(15, 45) / 10, // Simulation temps d'analyse
        ]);

        return redirect()->route('patients.show', $patient)
                        ->with('success', 'Analyse ajoutée avec succès !');
    }
}
