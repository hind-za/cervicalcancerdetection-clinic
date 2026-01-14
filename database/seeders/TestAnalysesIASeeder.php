<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\AnalyseIA;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class TestAnalysesIASeeder extends Seeder
{
    public function run()
    {
        // Récupérer les patients existants
        $patients = Patient::all();
        
        if ($patients->count() === 0) {
            $this->command->error('Aucun patient trouvé. Exécutez d\'abord TestPatientsSeeder.');
            return;
        }

        // Récupérer ou créer un utilisateur admin
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin Test',
                'email' => 'admin@test.com',
                'password' => bcrypt('password'),
                'role' => 'admin'
            ]);
        }

        // Créer le dossier d'analyses s'il n'existe pas
        Storage::disk('public')->makeDirectory('analyses-ia');

        // Créer des analyses de test
        $analyses = [
            [
                'patient_id' => $patients[0]->id,
                'user_id' => $admin->id,
                'nom_image' => 'cervical_001.jpg',
                'chemin_image' => 'analyses-ia/cervical_001.jpg',
                'taille_image' => '2.3 MB',
                'dimensions_image' => '1024x768 pixels',
                'classe_predite' => 'Dyskeratotic',
                'probabilite' => 0.8945,
                'toutes_probabilites' => [
                    'Dyskeratotic' => 0.8945,
                    'Koilocytotic' => 0.0623,
                    'Metaplastic' => 0.0234,
                    'Parabasal' => 0.0123,
                    'Superficial-Intermediate' => 0.0075
                ],
                'niveau_risque' => 'Élevé',
                'interpretation' => 'Cellules dyskeratotiques détectées. Anomalies de la kératinisation cellulaire. Résultat très fiable.',
                'recommandations' => [
                    'Surveillance rapprochée recommandée',
                    'Consultation spécialisée conseillée',
                    'Répéter l\'examen dans 6 mois'
                ],
                'temps_analyse' => 2.340,
                'statut' => 'En attente',
                'created_at' => now()->subHours(2),
            ],
            [
                'patient_id' => $patients[1]->id,
                'user_id' => $admin->id,
                'nom_image' => 'cervical_002.jpg',
                'chemin_image' => 'analyses-ia/cervical_002.jpg',
                'taille_image' => '1.8 MB',
                'dimensions_image' => '800x600 pixels',
                'classe_predite' => 'Koilocytotic',
                'probabilite' => 0.7823,
                'toutes_probabilites' => [
                    'Koilocytotic' => 0.7823,
                    'Dyskeratotic' => 0.1234,
                    'Metaplastic' => 0.0567,
                    'Parabasal' => 0.0234,
                    'Superficial-Intermediate' => 0.0142
                ],
                'niveau_risque' => 'Modéré',
                'interpretation' => 'Cellules koilocytotiques détectées. Possibles signes d\'infection HPV. Résultat fiable.',
                'recommandations' => [
                    'Test HPV recommandé',
                    'Surveillance gynécologique renforcée',
                    'Évaluation complémentaire nécessaire'
                ],
                'temps_analyse' => 1.890,
                'statut' => 'Validé',
                'valide_par' => $admin->id,
                'date_validation' => now()->subHour(),
                'commentaires_medecin' => 'Résultat cohérent avec l\'examen clinique. Surveillance recommandée.',
                'created_at' => now()->subHours(4),
            ],
            [
                'patient_id' => $patients[2]->id,
                'user_id' => $admin->id,
                'nom_image' => 'cervical_003.jpg',
                'chemin_image' => 'analyses-ia/cervical_003.jpg',
                'taille_image' => '3.1 MB',
                'dimensions_image' => '1200x900 pixels',
                'classe_predite' => 'Metaplastic',
                'probabilite' => 0.9156,
                'toutes_probabilites' => [
                    'Metaplastic' => 0.9156,
                    'Superficial-Intermediate' => 0.0456,
                    'Parabasal' => 0.0234,
                    'Koilocytotic' => 0.0098,
                    'Dyskeratotic' => 0.0056
                ],
                'niveau_risque' => 'Faible',
                'interpretation' => 'Cellules métaplasiques détectées. Transformation cellulaire normale. Résultat très fiable.',
                'recommandations' => [
                    'Résultat dans les limites normales',
                    'Surveillance de routine',
                    'Prochain contrôle selon protocole'
                ],
                'temps_analyse' => 1.560,
                'statut' => 'Validé',
                'valide_par' => $admin->id,
                'date_validation' => now()->subMinutes(30),
                'commentaires_medecin' => 'Résultat normal. Pas d\'anomalie détectée.',
                'created_at' => now()->subHours(6),
            ],
            [
                'patient_id' => $patients[0]->id,
                'user_id' => $admin->id,
                'nom_image' => 'cervical_004.jpg',
                'chemin_image' => 'analyses-ia/cervical_004.jpg',
                'taille_image' => '2.7 MB',
                'dimensions_image' => '1024x768 pixels',
                'classe_predite' => 'Superficial-Intermediate',
                'probabilite' => 0.8734,
                'toutes_probabilites' => [
                    'Superficial-Intermediate' => 0.8734,
                    'Metaplastic' => 0.0823,
                    'Parabasal' => 0.0234,
                    'Koilocytotic' => 0.0134,
                    'Dyskeratotic' => 0.0075
                ],
                'niveau_risque' => 'Faible',
                'interpretation' => 'Cellules superficielles-intermédiaires détectées. Cellules matures normales. Résultat fiable.',
                'recommandations' => [
                    'Cellules matures normales',
                    'Résultat satisfaisant',
                    'Surveillance de routine'
                ],
                'temps_analyse' => 1.780,
                'statut' => 'En attente',
                'created_at' => now()->subHours(8),
            ],
            [
                'patient_id' => $patients[3]->id,
                'user_id' => $admin->id,
                'nom_image' => 'cervical_005.jpg',
                'chemin_image' => 'analyses-ia/cervical_005.jpg',
                'taille_image' => '1.9 MB',
                'dimensions_image' => '900x675 pixels',
                'classe_predite' => 'Parabasal',
                'probabilite' => 0.8456,
                'toutes_probabilites' => [
                    'Parabasal' => 0.8456,
                    'Metaplastic' => 0.0934,
                    'Superficial-Intermediate' => 0.0345,
                    'Koilocytotic' => 0.0156,
                    'Dyskeratotic' => 0.0109
                ],
                'niveau_risque' => 'Faible',
                'interpretation' => 'Cellules parabasales détectées. Cellules de la couche profonde de l\'épithélium. Résultat fiable.',
                'recommandations' => [
                    'Cellules de régénération normale',
                    'Pas d\'action immédiate requise',
                    'Surveillance standard'
                ],
                'temps_analyse' => 2.120,
                'statut' => 'À revoir',
                'created_at' => now()->subDay(),
            ]
        ];

        foreach ($analyses as $analyseData) {
            AnalyseIA::create($analyseData);
        }

        // Créer des fichiers d'image factices
        $imageNames = [
            'cervical_001.jpg',
            'cervical_002.jpg', 
            'cervical_003.jpg',
            'cervical_004.jpg',
            'cervical_005.jpg'
        ];

        foreach ($imageNames as $imageName) {
            // Créer un fichier image factice (1x1 pixel transparent)
            $imageContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
            Storage::disk('public')->put('analyses-ia/' . $imageName, $imageContent);
        }

        $this->command->info('Analyses IA de test créées avec succès!');
    }
}