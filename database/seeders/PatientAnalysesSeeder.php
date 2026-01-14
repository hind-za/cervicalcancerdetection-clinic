<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\AnalyseIA;
use App\Models\User;

class PatientAnalysesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les patients par leurs numéros de dossier (non chiffrés)
        $marie = Patient::where('numero_dossier', 'PAT000001')->first();
        $sophie = Patient::where('numero_dossier', 'PAT000002')->first();
        
        // Récupérer un utilisateur admin pour les analyses
        $admin = User::where('role', 'admin')->first();
        
        echo "Marie trouvée: " . ($marie ? 'Oui (ID: ' . $marie->id . ')' : 'Non') . "\n";
        echo "Sophie trouvée: " . ($sophie ? 'Oui (ID: ' . $sophie->id . ')' : 'Non') . "\n";
        echo "Admin trouvé: " . ($admin ? 'Oui (ID: ' . $admin->id . ')' : 'Non') . "\n";
        
        if (!$marie || !$sophie || !$admin) {
            echo "Patients ou admin non trouvés.\n";
            echo "Patients dans la DB: " . Patient::count() . "\n";
            echo "Admins dans la DB: " . User::where('role', 'admin')->count() . "\n";
            return;
        }

        // Analyses pour Marie
        AnalyseIA::create([
            'patient_id' => $marie->id,
            'user_id' => $admin->id,
            'nom_image' => 'marie_analyse_1.jpg',
            'chemin_image' => 'analyses/marie_analyse_1.jpg',
            'taille_image' => '2.1 MB',
            'dimensions_image' => '1024x768',
            'classe_predite' => 'Superficial-Intermediate',
            'probabilite' => 0.92,
            'niveau_risque' => 'Faible',
            'statut' => 'Validé',
            'valide_par' => $admin->id,
            'date_validation' => now()->subDays(14),
            'interpretation' => 'Cellules superficielles-intermédiaires normales. Résultat rassurant.',
            'recommandations' => json_encode([
                'Suivi gynécologique de routine dans 12 mois',
                'Maintenir une bonne hygiène intime',
                'Pas de traitement spécifique nécessaire'
            ]),
            'toutes_probabilites' => json_encode([
                'Superficial-Intermediate' => 0.92,
                'Parabasal' => 0.05,
                'Metaplastic' => 0.02,
                'Koilocytotic' => 0.01,
                'Dyskeratotic' => 0.00
            ]),
            'temps_analyse' => '2.1',
            'commentaires_medecin' => 'Analyse normale, pas d\'inquiétude particulière.',
            'created_at' => now()->subDays(15),
            'updated_at' => now()->subDays(14)
        ]);

        AnalyseIA::create([
            'patient_id' => $marie->id,
            'user_id' => $admin->id,
            'nom_image' => 'marie_analyse_2.jpg',
            'chemin_image' => 'analyses/marie_analyse_2.jpg',
            'taille_image' => '1.8 MB',
            'dimensions_image' => '1024x768',
            'classe_predite' => 'Parabasal',
            'probabilite' => 0.78,
            'niveau_risque' => 'Modéré',
            'statut' => 'En attente',
            'interpretation' => 'Présence de cellules parabasales. Surveillance recommandée.',
            'recommandations' => json_encode([
                'Contrôle dans 6 mois',
                'Surveillance médicale renforcée',
                'Éviter les facteurs de risque'
            ]),
            'toutes_probabilites' => json_encode([
                'Parabasal' => 0.78,
                'Superficial-Intermediate' => 0.15,
                'Metaplastic' => 0.05,
                'Koilocytotic' => 0.02,
                'Dyskeratotic' => 0.00
            ]),
            'temps_analyse' => '3.2',
            'created_at' => now()->subDays(3)
        ]);

        // Analyses pour Sophie
        AnalyseIA::create([
            'patient_id' => $sophie->id,
            'user_id' => $admin->id,
            'nom_image' => 'sophie_analyse_1.jpg',
            'chemin_image' => 'analyses/sophie_analyse_1.jpg',
            'taille_image' => '2.3 MB',
            'dimensions_image' => '1024x768',
            'classe_predite' => 'Koilocytotic',
            'probabilite' => 0.85,
            'niveau_risque' => 'Élevé',
            'statut' => 'Validé',
            'valide_par' => $admin->id,
            'date_validation' => now()->subDays(6),
            'interpretation' => 'Cellules koilocytotiques détectées. Infection HPV probable.',
            'recommandations' => json_encode([
                'Consultation médicale urgente',
                'Test HPV complémentaire',
                'Colposcopie recommandée',
                'Suivi médical rapproché'
            ]),
            'toutes_probabilites' => json_encode([
                'Koilocytotic' => 0.85,
                'Dyskeratotic' => 0.08,
                'Metaplastic' => 0.04,
                'Parabasal' => 0.02,
                'Superficial-Intermediate' => 0.01
            ]),
            'temps_analyse' => '2.8',
            'commentaires_medecin' => 'Résultat préoccupant nécessitant un suivi immédiat. Patient informée.',
            'created_at' => now()->subDays(7),
            'updated_at' => now()->subDays(6)
        ]);

        AnalyseIA::create([
            'patient_id' => $sophie->id,
            'user_id' => $admin->id,
            'nom_image' => 'sophie_analyse_2.jpg',
            'chemin_image' => 'analyses/sophie_analyse_2.jpg',
            'taille_image' => '1.9 MB',
            'dimensions_image' => '1024x768',
            'classe_predite' => 'Metaplastic',
            'probabilite' => 0.73,
            'niveau_risque' => 'Modéré',
            'statut' => 'À revoir',
            'interpretation' => 'Métaplasie cervicale détectée. Nécessite une évaluation approfondie.',
            'recommandations' => json_encode([
                'Révision par un spécialiste',
                'Biopsie si nécessaire',
                'Contrôle dans 3 mois'
            ]),
            'toutes_probabilites' => json_encode([
                'Metaplastic' => 0.73,
                'Parabasal' => 0.15,
                'Superficial-Intermediate' => 0.08,
                'Koilocytotic' => 0.03,
                'Dyskeratotic' => 0.01
            ]),
            'temps_analyse' => '4.1',
            'created_at' => now()->subDays(1)
        ]);

        echo "Analyses de test créées avec succès!\n";
        echo "- Marie: 2 analyses (1 validée, 1 en attente)\n";
        echo "- Sophie: 2 analyses (1 validée, 1 à revoir)\n";
    }
}
