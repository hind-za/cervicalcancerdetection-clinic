<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\AnalyseIA;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class HindZabratiSeeder extends Seeder
{
    public function run()
    {
        // Vérifier si la patiente existe déjà
        $patient = Patient::where('numero_dossier', 'PAT-2025-0006')->first();
        
        if (!$patient) {
            // Créer la patiente Hind Zabrati
            $patient = Patient::create([
                'nom' => 'Zabrati',
                'prenom' => 'Hind',
                'date_naissance' => '1990-05-15',
                'sexe' => 'F',
                'telephone' => '0612345678',
                'email' => 'hind.zabrati@email.com',
                'numero_dossier' => 'PAT-2025-0006',
                'antecedents_medicaux' => 'Aucun antécédent particulier',
                'notes' => 'Nouvelle patiente'
            ]);
        }

        // Récupérer l'admin
        $admin = User::where('role', 'admin')->first();

        // Vérifier si l'analyse existe déjà
        $existingAnalyse = AnalyseIA::where('patient_id', $patient->id)->first();
        
        if (!$existingAnalyse) {
            // Créer une analyse pour cette patiente
            $analyse = AnalyseIA::create([
                'patient_id' => $patient->id,
                'user_id' => $admin->id,
                'nom_image' => 'hind_cervical_001.jpg',
                'chemin_image' => 'analyses-ia/hind_cervical_001.jpg',
                'taille_image' => '2.1 MB',
                'dimensions_image' => '1024x768 pixels',
                'classe_predite' => 'Metaplastic',
                'probabilite' => 0.8923,
                'toutes_probabilites' => [
                    'Metaplastic' => 0.8923,
                    'Superficial-Intermediate' => 0.0567,
                    'Parabasal' => 0.0234,
                    'Koilocytotic' => 0.0156,
                    'Dyskeratotic' => 0.0120
                ],
                'niveau_risque' => 'Faible',
                'interpretation' => 'Cellules métaplasiques détectées. Transformation cellulaire normale. Résultat très fiable.',
                'recommandations' => [
                    'Résultat dans les limites normales',
                    'Surveillance de routine',
                    'Prochain contrôle selon protocole'
                ],
                'temps_analyse' => 1.890,
                'statut' => 'En attente',
                'created_at' => now()->subHours(1),
            ]);

            // Créer le fichier image factice
            Storage::disk('public')->makeDirectory('analyses-ia');
            $imageContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
            Storage::disk('public')->put('analyses-ia/hind_cervical_001.jpg', $imageContent);
        }

        $this->command->info('Patiente Hind Zabrati vérifiée/créée avec analyse (ID: ' . $patient->id . ')');
    }
}