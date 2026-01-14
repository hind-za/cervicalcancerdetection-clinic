<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AnalyseIA;
use Illuminate\Support\Facades\Storage;

class FinalImageTestSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🔍 Vérification finale des images...');
        
        $analyses = AnalyseIA::all();
        
        foreach ($analyses as $analyse) {
            $imagePath = $analyse->chemin_image;
            $fullUrl = Storage::url($imagePath);
            
            if (Storage::disk('public')->exists($imagePath)) {
                $size = Storage::disk('public')->size($imagePath);
                $this->command->info("✅ Analyse {$analyse->id}: {$imagePath} ({$size} bytes)");
                $this->command->info("   URL: {$fullUrl}");
            } else {
                $this->command->error("❌ Analyse {$analyse->id}: {$imagePath} - MANQUANT");
            }
        }
        
        $this->command->info('');
        $this->command->info('📋 Résumé:');
        $this->command->info("   - Patient: Hind Zabrati");
        $this->command->info("   - Analyses: {$analyses->count()}");
        $this->command->info("   - Toutes en attente de validation docteur");
        $this->command->info('');
        $this->command->info('🔗 URLs de test:');
        $this->command->info('   - Admin: http://127.0.0.1:8000/admin/dashboard');
        $this->command->info('   - Docteur: http://127.0.0.1:8000/doctor/dashboard');
        $this->command->info('   - Patients: http://127.0.0.1:8000/patients');
        $this->command->info('');
        $this->command->info('👥 Comptes de test:');
        $this->command->info('   - Admin: admin@test.com / password');
        $this->command->info('   - Docteur: hind@gmail.com / password (ou doctor@test.com)');
    }
}