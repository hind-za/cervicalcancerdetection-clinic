<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\AnalyseIA;
use App\Models\AnalyseImage;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CleanAndKeepHindSeeder extends Seeder
{
    public function run()
    {
        // Récupérer Hind Zabrati avant de tout supprimer
        $hindZabrati = Patient::where('nom', 'Zabrati')
                             ->where('prenom', 'Hind')
                             ->first();

        if (!$hindZabrati) {
            $this->command->error('Hind Zabrati non trouvée dans la base de données!');
            return;
        }

        // Sauvegarder les données de Hind Zabrati
        $hindData = $hindZabrati->toArray();
        $hindAnalysesIA = AnalyseIA::where('patient_id', $hindZabrati->id)->get()->toArray();
        $hindAnalysesImage = AnalyseImage::where('patient_id', $hindZabrati->id)->get()->toArray();

        $this->command->info('Sauvegarde des données de Hind Zabrati...');
        $this->command->info('- Patient: ' . $hindZabrati->nom_complet);
        $this->command->info('- Analyses IA: ' . count($hindAnalysesIA));
        $this->command->info('- Analyses Image: ' . count($hindAnalysesImage));

        // Désactiver les contraintes de clés étrangères
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Supprimer toutes les analyses
        AnalyseIA::truncate();
        AnalyseImage::truncate();
        
        // Supprimer tous les patients
        Patient::truncate();

        // Réactiver les contraintes de clés étrangères
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Suppression de tous les patients et analyses terminée.');

        // Recréer Hind Zabrati avec un nouvel ID
        $newHind = Patient::create([
            'nom' => $hindData['nom'],
            'prenom' => $hindData['prenom'],
            'date_naissance' => $hindData['date_naissance'],
            'sexe' => $hindData['sexe'],
            'telephone' => $hindData['telephone'],
            'email' => $hindData['email'],
            'adresse' => $hindData['adresse'],
            'numero_dossier' => 'PAT-2025-0001', // Nouveau numéro de dossier
            'antecedents_medicaux' => $hindData['antecedents_medicaux'],
            'notes' => $hindData['notes'],
            'created_at' => $hindData['created_at'],
            'updated_at' => $hindData['updated_at']
        ]);

        $this->command->info('Hind Zabrati recréée avec ID: ' . $newHind->id);

        // Récupérer l'utilisateur admin
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $this->command->error('Aucun utilisateur admin trouvé!');
            return;
        }

        // Recréer les analyses IA de Hind
        foreach ($hindAnalysesIA as $analyseData) {
            unset($analyseData['id']); // Supprimer l'ancien ID
            $analyseData['patient_id'] = $newHind->id; // Nouveau patient ID
            $analyseData['user_id'] = $admin->id; // S'assurer que l'admin existe
            
            AnalyseIA::create($analyseData);
        }

        // Recréer les analyses Image de Hind (s'il y en a)
        foreach ($hindAnalysesImage as $analyseData) {
            unset($analyseData['id']); // Supprimer l'ancien ID
            $analyseData['patient_id'] = $newHind->id; // Nouveau patient ID
            
            AnalyseImage::create($analyseData);
        }

        $this->command->info('Analyses de Hind Zabrati restaurées:');
        $this->command->info('- Analyses IA: ' . count($hindAnalysesIA));
        $this->command->info('- Analyses Image: ' . count($hindAnalysesImage));

        // Nettoyer les fichiers d'images inutiles (garder seulement ceux de Hind)
        $this->cleanImageFiles($newHind);

        $this->command->info('✅ Nettoyage terminé! Seule Hind Zabrati reste dans la base de données.');
        $this->command->info('📊 Résumé:');
        $this->command->info('   - Patients: 1 (Hind Zabrati)');
        $this->command->info('   - Analyses totales: ' . (count($hindAnalysesIA) + count($hindAnalysesImage)));
        $this->command->info('   - Nouveau numéro de dossier: PAT-2025-0001');
    }

    private function cleanImageFiles(Patient $patient)
    {
        // Récupérer tous les chemins d'images de Hind
        $hindImagePaths = [];
        
        $analysesIA = AnalyseIA::where('patient_id', $patient->id)->get();
        foreach ($analysesIA as $analyse) {
            if ($analyse->chemin_image) {
                $hindImagePaths[] = $analyse->chemin_image;
            }
        }

        $analysesImage = AnalyseImage::where('patient_id', $patient->id)->get();
        foreach ($analysesImage as $analyse) {
            if ($analyse->chemin_image) {
                $hindImagePaths[] = $analyse->chemin_image;
            }
        }

        // Nettoyer le dossier analyses-ia (garder seulement les images de Hind)
        if (Storage::disk('public')->exists('analyses-ia')) {
            $allFiles = Storage::disk('public')->files('analyses-ia');
            $deletedCount = 0;
            
            foreach ($allFiles as $file) {
                if (!in_array($file, $hindImagePaths)) {
                    Storage::disk('public')->delete($file);
                    $deletedCount++;
                }
            }
            
            $this->command->info("🗑️  Fichiers images supprimés: $deletedCount");
        }
    }
}