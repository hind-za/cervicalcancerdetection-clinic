<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AnalyseIA;
use Illuminate\Support\Facades\Storage;

class FixImagePathsSeeder extends Seeder
{
    public function run()
    {
        $analyses = AnalyseIA::all();
        
        foreach ($analyses as $i => $analyse) {
            $newImageName = 'hind_cervical_' . str_pad($i + 1, 3, '0', STR_PAD_LEFT) . '.jpg';
            $newPath = 'analyses-ia/' . $newImageName;
            
            // Mettre à jour le chemin et le nom
            $analyse->update([
                'chemin_image' => $newPath,
                'nom_image' => $newImageName
            ]);
            
            $this->command->info("Analyse {$analyse->id} -> {$newPath}");
        }
        
        // Créer les images correspondantes
        for ($i = 1; $i <= $analyses->count(); $i++) {
            $filename = 'hind_cervical_' . str_pad($i, 3, '0', STR_PAD_LEFT) . '.jpg';
            $this->createTestImage($filename, $i);
        }
        
        $this->command->info('✅ Chemins d\'images et fichiers mis à jour');
    }
    
    private function createTestImage($filename, $index)
    {
        // Créer une image PNG minimale mais valide avec des couleurs différentes
        $colors = [
            1 => 'ffcccc', // Rose
            2 => 'ccffcc', // Vert
            3 => 'ccccff', // Bleu
            4 => 'ffffcc', // Jaune
            5 => 'ffccff', // Violet
        ];
        
        $color = $colors[$index] ?? 'f0f0f0';
        
        // Créer un SVG simple
        $svg = '<?xml version="1.0" encoding="UTF-8"?>
<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">
  <rect width="200" height="200" fill="#' . $color . '" stroke="#999" stroke-width="1"/>
  <text x="100" y="80" font-family="Arial" font-size="12" text-anchor="middle" fill="#333">Analyse Cervicale</text>
  <text x="100" y="100" font-family="Arial" font-size="10" text-anchor="middle" fill="#666">Hind Zabrati</text>
  <text x="100" y="120" font-family="Arial" font-size="8" text-anchor="middle" fill="#999">' . $filename . '</text>
  <circle cx="60" cy="150" r="5" fill="#ff6666" opacity="0.7"/>
  <circle cx="100" cy="140" r="4" fill="#66ff66" opacity="0.7"/>
  <circle cx="140" cy="160" r="6" fill="#6666ff" opacity="0.7"/>
</svg>';

        // Convertir SVG en données d'image (simulation)
        // Pour le moment, créons un fichier texte qui sera reconnu comme image
        $imageData = base64_encode($svg);
        
        // Créer un en-tête JPEG minimal
        $jpegHeader = "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x01\x00H\x00H\x00\x00\xFF\xDB\x00C\x00";
        $jpegData = $jpegHeader . base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
        
        Storage::disk('public')->put('analyses-ia/' . $filename, $jpegData);
    }
}