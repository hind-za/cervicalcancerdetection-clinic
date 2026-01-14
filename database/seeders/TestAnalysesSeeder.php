<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\AnalyseImage;
use Illuminate\Support\Facades\Storage;

class TestAnalysesSeeder extends Seeder
{
    public function run()
    {
        // Créer quelques patients de test
        $patients = [
            [
                'nom' => 'Dupont',
                'prenom' => 'Marie',
                'date_naissance' => '1978-05-15',
                'sexe' => 'F',
                'telephone' => '0123456789',
                'email' => 'marie.dupont@email.com',
                'adresse' => '123 Rue de la Santé, Paris',
                'numero_dossier' => 'PAT-2025-0002',
                'antecedents_medicaux' => 'Aucun antécédent particulier',
            ],
            [
                'nom' => 'Martin',
                'prenom' => 'Sophie',
                'date_naissance' => '1985-08-22',
                'sexe' => 'F',
                'telephone' => '0987654321',
                'email' => 'sophie.martin@email.com',
                'adresse' => '456 Avenue de la République, Lyon',
                'numero_dossier' => 'PAT-2025-0003',
                'antecedents_medicaux' => 'Antécédents familiaux de cancer',
            ],
            [
                'nom' => 'Dubois',
                'prenom' => 'Claire',
                'date_naissance' => '1972-12-03',
                'sexe' => 'F',
                'telephone' => '0147258369',
                'email' => 'claire.dubois@email.com',
                'adresse' => '789 Boulevard Saint-Michel, Marseille',
                'numero_dossier' => 'PAT-2025-0004',
                'antecedents_medicaux' => 'Hypertension artérielle',
            ],
        ];

        foreach ($patients as $patientData) {
            Patient::create($patientData);
        }

        // Récupérer tous les patients
        $allPatients = Patient::all();

        // Créer des analyses de test
        $analyses = [
            [
                'patient_id' => $allPatients[0]->id,
                'nom_image' => 'cervical_scan_001.jpg',
                'chemin_image' => 'analyses/cervical_scan_001.jpg',
                'resultat' => 'Anomalie Détectée',
                'confiance' => 92.5,
                'details' => json_encode([
                    'zones_detectees' => ['Zone cervicale supérieure'],
                    'type_anomalie' => 'Lésion suspecte',
                    'recommandations' => 'Biopsie recommandée'
                ]),
                'temps_analyse' => 2.340,
                'statut' => 'En attente',
                'created_at' => now(),
            ],
            [
                'patient_id' => $allPatients[1]->id,
                'nom_image' => 'cervical_scan_002.jpg',
                'chemin_image' => 'analyses/cervical_scan_002.jpg',
                'resultat' => 'À Surveiller',
                'confiance' => 78.3,
                'details' => json_encode([
                    'zones_detectees' => ['Zone cervicale inférieure'],
                    'type_anomalie' => 'Inflammation légère',
                    'recommandations' => 'Contrôle dans 6 mois'
                ]),
                'temps_analyse' => 1.890,
                'statut' => 'En attente',
                'created_at' => now()->subHours(2),
            ],
            [
                'patient_id' => $allPatients[2]->id,
                'nom_image' => 'cervical_scan_003.jpg',
                'chemin_image' => 'analyses/cervical_scan_003.jpg',
                'resultat' => 'Normal',
                'confiance' => 95.7,
                'details' => json_encode([
                    'zones_detectees' => [],
                    'type_anomalie' => 'Aucune',
                    'recommandations' => 'Contrôle de routine dans 1 an'
                ]),
                'temps_analyse' => 1.560,
                'statut' => 'Validé',
                'valide_par' => 1, // Assumant qu'il y a un utilisateur avec ID 1
                'date_validation' => now()->subHour(),
                'created_at' => now()->subDay(),
            ],
            [
                'patient_id' => $allPatients[0]->id,
                'nom_image' => 'cervical_scan_004.jpg',
                'chemin_image' => 'analyses/cervical_scan_004.jpg',
                'resultat' => 'Anomalie Détectée',
                'confiance' => 88.9,
                'details' => json_encode([
                    'zones_detectees' => ['Zone cervicale centrale'],
                    'type_anomalie' => 'Dysplasie modérée',
                    'recommandations' => 'Consultation spécialisée urgente'
                ]),
                'temps_analyse' => 2.120,
                'statut' => 'À revoir',
                'created_at' => now()->subHours(6),
            ],
            [
                'patient_id' => $allPatients[1]->id,
                'nom_image' => 'cervical_scan_005.jpg',
                'chemin_image' => 'analyses/cervical_scan_005.jpg',
                'resultat' => 'Normal',
                'confiance' => 91.2,
                'details' => json_encode([
                    'zones_detectees' => [],
                    'type_anomalie' => 'Aucune',
                    'recommandations' => 'Résultat normal'
                ]),
                'temps_analyse' => 1.780,
                'statut' => 'En attente',
                'created_at' => now()->subHours(4),
            ],
        ];

        foreach ($analyses as $analyseData) {
            AnalyseImage::create($analyseData);
        }

        // Créer des fichiers d'image factices dans le storage
        Storage::disk('public')->makeDirectory('analyses');
        
        $imageNames = [
            'cervical_scan_001.jpg',
            'cervical_scan_002.jpg', 
            'cervical_scan_003.jpg',
            'cervical_scan_004.jpg',
            'cervical_scan_005.jpg'
        ];

        foreach ($imageNames as $imageName) {
            // Créer un fichier image factice (1x1 pixel transparent)
            $imageContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
            Storage::disk('public')->put('analyses/' . $imageName, $imageContent);
        }

        $this->command->info('Test analyses created successfully!');
    }
}