<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\AnalyseIA;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Crypt;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Déchiffrer une valeur manuellement
     */
    private function forceDecrypt($value)
    {
        if (empty($value)) {
            return $value;
        }
        
        try {
            return Crypt::decrypt($value);
        } catch (\Exception $e) {
            // Si le déchiffrement échoue, retourner la valeur telle quelle
            return $value;
        }
    }

    /**
     * Générer un rapport complet pour un patient
     */
    public function generatePatientReport(Patient $patient, Request $request)
    {
        $patient->load([
            'analysesIA' => function($query) {
                $query->with(['analyste', 'validateur'])
                      ->orderBy('created_at', 'desc');
            },
            'analyses' => function($query) {
                $query->with('validateur')
                      ->orderBy('created_at', 'desc');
            }
        ]);

        $format = $request->get('format', 'pdf');
        $type = $request->get('type', 'complet');

        // Déchiffrement manuel forcé des données du patient
        $patientData = (object) [
            'id' => $patient->id,
            'nom' => $this->forceDecrypt($patient->getRawOriginal('nom')),
            'prenom' => $this->forceDecrypt($patient->getRawOriginal('prenom')),
            'numero_dossier' => $patient->numero_dossier,
            'date_naissance' => $patient->date_naissance,
            'age' => $patient->age,
            'sexe' => $patient->sexe,
            'telephone' => $this->forceDecrypt($patient->getRawOriginal('telephone')),
            'email' => $this->forceDecrypt($patient->getRawOriginal('email')),
            'adresse' => $this->forceDecrypt($patient->getRawOriginal('adresse')),
            'antecedents_medicaux' => $this->forceDecrypt($patient->getRawOriginal('antecedents_medicaux')),
            'notes' => $this->forceDecrypt($patient->getRawOriginal('notes')),
            'created_at' => $patient->created_at,
            'updated_at' => $patient->updated_at,
            'analysesIA' => $patient->analysesIA,
            'analyses' => $patient->analyses
        ];
        
        // Calculer le nom complet avec les données déchiffrées
        $patientData->nom_complet = trim($patientData->prenom . ' ' . $patientData->nom);

        $data = [
            'patient' => $patientData,
            'analysesIA' => $patient->analysesIA,
            'analyses' => $patient->analyses,
            'stats' => $this->getPatientStats($patient),
            'generated_at' => now(),
            'generated_by' => auth()->user()
        ];

        if ($format === 'pdf') {
            return $this->generatePDF($data, $type, $patient);
        }

        return view('reports.patient', $data);
    }

    /**
     * Générer un rapport PDF pour une analyse spécifique
     */
    public function generateAnalysisReport(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'analysis_data' => 'required|string'
        ]);

        $patient = Patient::findOrFail($request->patient_id);
        $analysisData = json_decode($request->analysis_data, true);

        // Déchiffrement manuel forcé des données du patient
        $patientData = (object) [
            'id' => $patient->id,
            'nom' => $this->forceDecrypt($patient->getRawOriginal('nom')),
            'prenom' => $this->forceDecrypt($patient->getRawOriginal('prenom')),
            'numero_dossier' => $patient->numero_dossier,
            'date_naissance' => $patient->date_naissance,
            'age' => $patient->age,
            'sexe' => $patient->sexe,
            'telephone' => $this->forceDecrypt($patient->getRawOriginal('telephone')),
            'email' => $this->forceDecrypt($patient->getRawOriginal('email')),
            'adresse' => $this->forceDecrypt($patient->getRawOriginal('adresse')),
            'antecedents_medicaux' => $this->forceDecrypt($patient->getRawOriginal('antecedents_medicaux')),
            'notes' => $this->forceDecrypt($patient->getRawOriginal('notes')),
            'created_at' => $patient->created_at,
            'updated_at' => $patient->updated_at
        ];
        
        // Calculer le nom complet avec les données déchiffrées
        $patientData->nom_complet = trim($patientData->prenom . ' ' . $patientData->nom);

        // Log pour vérifier le déchiffrement
        \Log::info('Données patient pour rapport:', [
            'nom_brut' => $patient->getRawOriginal('nom'),
            'nom_dechiffre' => $patientData->nom,
            'prenom_dechiffre' => $patientData->prenom,
            'nom_complet' => $patientData->nom_complet
        ]);

        // Créer les données pour le rapport
        $data = [
            'patient' => $patientData,
            'analysis' => $analysisData,
            'generated_at' => now(),
            'generated_by' => auth()->user(),
            'is_single_analysis' => true
        ];

        // Log pour déboguer le template utilisé
        \Log::info('Génération PDF avec template analysis.blade.php:', [
            'template' => 'reports.pdf.analysis',
            'patient_nom' => $patientData->nom_complet,
            'analysis_classe' => $analysisData['classe_predite'] ?? 'Non définie',
            'template_exists' => view()->exists('reports.pdf.analysis')
        ]);

        $pdf = Pdf::loadView('reports.pdf.analysis', $data)
                  ->setPaper('a4', 'portrait')
                  ->setOptions([
                      'defaultFont' => 'DejaVu Sans',
                      'isHtml5ParserEnabled' => true,
                      'isRemoteEnabled' => true
                  ]);

        $filename = "analyse_{$patient->numero_dossier}_" . now()->format('Y-m-d_H-i') . ".pdf";
        
        return $pdf->download($filename);
    }

    /**
     * Générer rapport PDF
     */
    private function generatePDF($data, $type, $patient)
    {
        $view = match($type) {
            'medical' => 'reports.pdf.medical',
            'summary' => 'reports.pdf.summary',
            default => 'reports.pdf.complete'
        };

        $pdf = Pdf::loadView($view, $data)
                  ->setPaper('a4', 'portrait')
                  ->setOptions([
                      'defaultFont' => 'DejaVu Sans',
                      'isHtml5ParserEnabled' => true,
                      'isRemoteEnabled' => true
                  ]);

        $filename = "rapport_{$patient->numero_dossier}_" . now()->format('Y-m-d') . ".pdf";
        
        return $pdf->download($filename);
    }

    /**
     * Statistiques du patient
     */
    private function getPatientStats($patient)
    {
        return [
            'total_analyses' => $patient->analysesIA->count() + $patient->analyses->count(),
            'analyses_validees' => $patient->analysesIA->where('statut', 'Validé')->count(),
            'analyses_en_attente' => $patient->analysesIA->where('statut', 'En attente')->count(),
            'derniere_analyse' => $patient->analysesIA->first()?->created_at,
            'risque_eleve' => $patient->analysesIA->where('niveau_risque', 'Élevé')->count(),
            'classes_detectees' => $patient->analysesIA->pluck('classe_predite')->unique()->values()
        ];
    }

    /**
     * Rapport global des patients
     */
    public function globalReport(Request $request)
    {
        $query = Patient::with(['analysesIA', 'analyses']);

        // Filtres
        if ($request->filled('date_debut')) {
            $query->where('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->where('created_at', '<=', $request->date_fin);
        }

        $patients = $query->get();
        
        $data = [
            'patients' => $patients,
            'stats' => $this->getGlobalStats($patients),
            'generated_at' => now(),
            'generated_by' => auth()->user(),
            'filters' => $request->all()
        ];

        if ($request->get('format') === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf.global', $data);
            return $pdf->download('rapport_global_' . now()->format('Y-m-d') . '.pdf');
        }

        return view('reports.global', $data);
    }

    /**
     * Test direct du template analysis.blade.php
     */
    public function testAnalysisTemplate(Request $request)
    {
        $patient = (object) [
            'id' => 1,
            'nom_complet' => 'Test Patient',
            'numero_dossier' => 'TEST001',
            'date_naissance' => now()->subYears(30),
            'age' => 30,
            'sexe' => 'F',
            'telephone' => '0123456789',
            'email' => 'test@example.com',
            'adresse' => 'Test Address',
            'antecedents_medicaux' => 'Aucun antécédent particulier',
            'notes' => 'Patient de test'
        ];
        
        $analysis = [
            'classe_predite' => 'Superficial-Intermediate',
            'probabilite' => 0.85,
            'niveau_risque' => 'Faible',
            'interpretation' => 'Cellules superficielles-intermédiaires détectées. Résultat normal.',
            'toutes_probabilites' => [
                'Superficial-Intermediate' => 0.85,
                'Parabasal' => 0.10,
                'Metaplastic' => 0.03,
                'Koilocytotic' => 0.01,
                'Dyskeratotic' => 0.01
            ],
            'recommandations' => [
                'Suivi médical de routine',
                'Contrôle dans 12 mois',
                'Maintenir une bonne hygiène'
            ]
        ];
        
        $data = [
            'patient' => $patient,
            'analysis' => $analysis,
            'generated_at' => now(),
            'generated_by' => auth()->user() ?? (object)['name' => 'Test User', 'role' => 'Admin'],
            'is_single_analysis' => true
        ];
        
        if ($request->get('pdf') === '1') {
            $pdf = Pdf::loadView('reports.pdf.analysis', $data)
                      ->setPaper('a4', 'portrait')
                      ->setOptions([
                          'defaultFont' => 'DejaVu Sans',
                          'isHtml5ParserEnabled' => true,
                          'isRemoteEnabled' => true
                      ]);
            
            return $pdf->download('test_analysis_' . now()->format('Y-m-d_H-i') . '.pdf');
        }
        
        return view('reports.pdf.analysis', $data);
    }

    /**
     * Statistiques globales
     */
    private function getGlobalStats($patients)
    {
        $totalAnalyses = $patients->sum(function($p) {
            return $p->analysesIA->count() + $p->analyses->count();
        });

        return [
            'total_patients' => $patients->count(),
            'total_analyses' => $totalAnalyses,
            'patients_avec_analyses' => $patients->filter(function($p) {
                return $p->analysesIA->count() > 0 || $p->analyses->count() > 0;
            })->count(),
            'analyses_validees' => $patients->sum(function($p) {
                return $p->analysesIA->where('statut', 'Validé')->count();
            }),
            'risque_eleve' => $patients->sum(function($p) {
                return $p->analysesIA->where('niveau_risque', 'Élevé')->count();
            })
        ];
    }
}