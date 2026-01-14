<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;

class TestPatientsSeeder extends Seeder
{
    public function run()
    {
        // Supprimer les analyses d'abord
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Patient::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Créer des patients de test
        $patients = [
            [
                'nom' => 'Dupont',
                'prenom' => 'Marie',
                'date_naissance' => '1985-03-15',
                'sexe' => 'F',
                'telephone' => '0123456789',
                'email' => 'marie.dupont@email.com',
                'numero_dossier' => 'PAT-2025-0001',
                'antecedents_medicaux' => 'Aucun antécédent particulier',
                'notes' => 'Patiente suivie régulièrement'
            ],
            [
                'nom' => 'Martin',
                'prenom' => 'Sophie',
                'date_naissance' => '1978-08-22',
                'sexe' => 'F',
                'telephone' => '0987654321',
                'email' => 'sophie.martin@email.com',
                'numero_dossier' => 'PAT-2025-0002',
                'antecedents_medicaux' => 'Antécédents familiaux de cancer',
                'notes' => 'Surveillance renforcée recommandée'
            ],
            [
                'nom' => 'Dubois',
                'prenom' => 'Claire',
                'date_naissance' => '1972-12-03',
                'sexe' => 'F',
                'telephone' => '0147258369',
                'email' => 'claire.dubois@email.com',
                'numero_dossier' => 'PAT-2025-0003',
                'antecedents_medicaux' => 'Hypertension artérielle',
                'notes' => 'Traitement en cours'
            ],
            [
                'nom' => 'Leroy',
                'prenom' => 'Anne',
                'date_naissance' => '1990-06-10',
                'sexe' => 'F',
                'telephone' => '0156789012',
                'email' => 'anne.leroy@email.com',
                'numero_dossier' => 'PAT-2025-0004',
                'antecedents_medicaux' => 'Aucun',
                'notes' => 'Première consultation'
            ],
            [
                'nom' => 'Moreau',
                'prenom' => 'Isabelle',
                'date_naissance' => '1965-11-28',
                'sexe' => 'F',
                'telephone' => '0198765432',
                'email' => 'isabelle.moreau@email.com',
                'numero_dossier' => 'PAT-2025-0005',
                'antecedents_medicaux' => 'Diabète type 2',
                'notes' => 'Contrôle glycémique stable'
            ]
        ];

        foreach ($patients as $patientData) {
            Patient::create($patientData);
        }

        $this->command->info('Patients de test créés avec succès!');
    }
}