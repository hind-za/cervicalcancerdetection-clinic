<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AnalyseIA;

class CleanAnalysesSeeder extends Seeder
{
    public function run()
    {
        // Garder seulement les 5 premières analyses
        $analyses = AnalyseIA::orderBy('id')->get();
        $toKeep = $analyses->take(5);
        $toDelete = $analyses->skip(5);

        foreach ($toDelete as $analyse) {
            $analyse->delete();
        }

        $this->command->info('Gardé: ' . $toKeep->count() . ' analyses');
        $this->command->info('Supprimé: ' . $toDelete->count() . ' analyses');
        
        // Vérifier les images
        foreach ($toKeep as $analyse) {
            $this->command->info("Analyse {$analyse->id}: {$analyse->chemin_image}");
        }
    }
}