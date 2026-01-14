<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\AnalyseIA;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class HindAnalysesSeeder extends Seeder
{
    public function run()
    {
        // Récupérer Hind Zabrati
        $hind = Patient::where('nom', 'Zabrati')->where('prenom', 'Hind')->first();
        
        if (!$hind) {
            $this->command->error('Hind Zabrati non trouvée!');
            return;
        }

        // Récupérer l'admin
        $admin = User::where('role', 'admin')->first();

        // Créer le dossier d'analyses s'il n'existe pas
        Storage::disk('public')->makeDirectory('analyses-ia');

        // Analyses supplémentaires pour Hind Zabrati
        $analyses = [
            [
                'nom_image' => 'hind_cervical_002.jpg',
                'chemin_image' => 'analyses-ia/hind_cervical_002.jpg',
                'taille_image' => '1.9 MB',
                'dimensions_image' => '800x600 pixels',
                'classe_predite' => 'Superficial-Intermediate',
                'probabilite' => 0.9234,
                'toutes_probabilites' => [
                    'Superficial-Intermediate' => 0.9234,
                    'Metaplastic' => 0.0456,
                    'Parabasal' => 0.0189,
                    'Koilocytotic' => 0.0089,
                    'Dyskeratotic' => 0.0032
                ],
                'niveau_risque' => 'Faible',
                'interpretation' => 'Cellules superficielles-intermédiaires détectées. Cellules matures normales. Résultat très fiable.',
                'recommandations' => [
                    'Cellules matures normales',
                    'Résultat satisfaisant',
                    'Surveillance de routine'
                ],
                'temps_analyse' => 1.650,
                'statut' => 'Validé',
                'valide_par' => $admin->id,
                'date_validation' => now()->subDays(2),
                'commentaires_medecin' => 'Résultat normal. Cellules matures en bon état.',
                'created_at' => now()->subDays(3),
            ],
            [
                'nom_image' => 'hind_cervical_003.jpg',
                'chemin_image' => 'analyses-ia/hind_cervical_003.jpg',
                'taille_image' => '2.8 MB',
                'dimensions_image' => '1200x900 pixels',
                'classe_predite' => 'Koilocytotic',
                'probabilite' => 0.7845,
                'toutes_probabilites' => [
                    'Koilocytotic' => 0.7845,
                    'Dyskeratotic' => 0.1234,
                    'Metaplastic' => 0.0567,
                    'Superficial-Intermediate' => 0.0234,
                    'Parabasal' => 0.0120
                ],
                'niveau_risque' => 'Modéré',
                'interpretation' => 'Cellules koilocytotiques détectées. Possibles signes d\'infection HPV. Résultat fiable.',
                'recommandations' => [
                    'Test HPV recommandé',
                    'Surveillance gynécologique renforcée',
                    'Évaluation complémentaire nécessaire'
                ],
                'temps_analyse' => 2.120,
                'statut' => 'À revoir',
                'commentaires_medecin' => 'Présence de koilocytes. Recommande test HPV et suivi rapproché.',
                'created_at' => now()->subWeeks(2),
            ],
            [
                'nom_image' => 'hind_cervical_004.jpg',
                'chemin_image' => 'analyses-ia/hind_cervical_004.jpg',
                'taille_image' => '2.3 MB',
                'dimensions_image' => '1024x768 pixels',
                'classe_predite' => 'Parabasal',
                'probabilite' => 0.8567,
                'toutes_probabilites' => [
                    'Parabasal' => 0.8567,
                    'Metaplastic' => 0.0823,
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
                'temps_analyse' => 1.890,
                'statut' => 'Validé',
                'valide_par' => $admin->id,
                'date_validation' => now()->subWeeks(3),
                'commentaires_medecin' => 'Cellules parabasales normales. Processus de régénération physiologique.',
                'created_at' => now()->subMonth(),
            ]
        ];

        foreach ($analyses as $analyseData) {
            // Ajouter les données communes
            $analyseData['patient_id'] = $hind->id;
            $analyseData['user_id'] = $admin->id;
            
            AnalyseIA::create($analyseData);

            // Créer le fichier image factice
            $imageContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
            Storage::disk('public')->put($analyseData['chemin_image'], $imageContent);
        }

        $this->command->info('✅ Analyses supplémentaires créées pour Hind Zabrati:');
        $this->command->info('   - 3 nouvelles analyses ajoutées');
        $this->command->info('   - Total analyses: ' . AnalyseIA::where('patient_id', $hind->id)->count());
        $this->command->info('   - Statuts variés: Validé, À revoir, En attente');
        $this->command->info('   - Classes variées: Superficial-Intermediate, Koilocytotic, Parabasal');
    }
}