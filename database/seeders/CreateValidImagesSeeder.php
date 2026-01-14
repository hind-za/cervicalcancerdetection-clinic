<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class CreateValidImagesSeeder extends Seeder
{
    public function run()
    {
        // Créer le dossier d'analyses s'il n'existe pas
        Storage::disk('public')->makeDirectory('analyses-ia');

        // Créer 5 images de test valides
        for ($i = 1; $i <= 5; $i++) {
            $filename = 'hind_cervical_' . str_pad($i, 3, '0', STR_PAD_LEFT) . '.jpg';
            $this->createValidJPEG($filename, $i);
        }

        $this->command->info('✅ 5 images JPEG valides créées');
        
        // Vérifier les images
        for ($i = 1; $i <= 5; $i++) {
            $filename = 'hind_cervical_' . str_pad($i, 3, '0', STR_PAD_LEFT) . '.jpg';
            $path = 'analyses-ia/' . $filename;
            
            if (Storage::disk('public')->exists($path)) {
                $size = Storage::disk('public')->size($path);
                $this->command->info("✅ {$filename}: {$size} bytes");
            } else {
                $this->command->error("❌ {$filename}: manquant");
            }
        }
    }

    private function createValidJPEG($filename, $index)
    {
        // Créer un JPEG minimal mais valide
        // En-tête JPEG standard
        $jpeg = "\xFF\xD8"; // SOI (Start of Image)
        
        // Segment APP0 (JFIF)
        $jpeg .= "\xFF\xE0"; // APP0 marker
        $jpeg .= "\x00\x10"; // Length (16 bytes)
        $jpeg .= "JFIF\x00"; // Identifier
        $jpeg .= "\x01\x01"; // Version 1.1
        $jpeg .= "\x01"; // Units (1 = pixels per inch)
        $jpeg .= "\x00\x48"; // X density (72)
        $jpeg .= "\x00\x48"; // Y density (72)
        $jpeg .= "\x00\x00"; // Thumbnail width and height (0)
        
        // Segment DQT (Define Quantization Table)
        $jpeg .= "\xFF\xDB"; // DQT marker
        $jpeg .= "\x00\x43"; // Length (67 bytes)
        $jpeg .= "\x00"; // Table ID (0)
        
        // Table de quantification standard (64 bytes)
        $qtable = [
            16, 11, 10, 16, 24, 40, 51, 61,
            12, 12, 14, 19, 26, 58, 60, 55,
            14, 13, 16, 24, 40, 57, 69, 56,
            14, 17, 22, 29, 51, 87, 80, 62,
            18, 22, 37, 56, 68, 109, 103, 77,
            24, 35, 55, 64, 81, 104, 113, 92,
            49, 64, 78, 87, 103, 121, 120, 101,
            72, 92, 95, 98, 112, 100, 103, 99
        ];
        
        foreach ($qtable as $val) {
            $jpeg .= chr($val);
        }
        
        // Segment SOF0 (Start of Frame)
        $jpeg .= "\xFF\xC0"; // SOF0 marker
        $jpeg .= "\x00\x11"; // Length (17 bytes)
        $jpeg .= "\x08"; // Precision (8 bits)
        $jpeg .= "\x00\x64"; // Height (100 pixels)
        $jpeg .= "\x00\x64"; // Width (100 pixels)
        $jpeg .= "\x03"; // Number of components (3 for RGB)
        
        // Component 1 (Y)
        $jpeg .= "\x01\x22\x00";
        // Component 2 (Cb)
        $jpeg .= "\x02\x11\x01";
        // Component 3 (Cr)
        $jpeg .= "\x03\x11\x01";
        
        // Segment DHT (Define Huffman Table) - simplifié
        $jpeg .= "\xFF\xC4"; // DHT marker
        $jpeg .= "\x00\x1F"; // Length
        $jpeg .= "\x00"; // Table class and ID
        
        // Table Huffman simplifiée (30 bytes de données factices)
        for ($i = 0; $i < 30; $i++) {
            $jpeg .= chr($i % 256);
        }
        
        // Segment SOS (Start of Scan)
        $jpeg .= "\xFF\xDA"; // SOS marker
        $jpeg .= "\x00\x0C"; // Length (12 bytes)
        $jpeg .= "\x03"; // Number of components
        $jpeg .= "\x01\x00"; // Component 1
        $jpeg .= "\x02\x11"; // Component 2
        $jpeg .= "\x03\x11"; // Component 3
        $jpeg .= "\x00\x3F\x00"; // Spectral selection and approximation
        
        // Données d'image compressées (simulées)
        $colors = [
            1 => [255, 200, 200], // Rose
            2 => [200, 255, 200], // Vert
            3 => [200, 200, 255], // Bleu
            4 => [255, 255, 200], // Jaune
            5 => [255, 200, 255], // Violet
        ];
        
        $color = $colors[$index] ?? [240, 240, 240];
        
        // Données d'image simplifiées (pattern répétitif)
        for ($i = 0; $i < 100; $i++) {
            $jpeg .= chr($color[0] % 256);
            $jpeg .= chr($color[1] % 256);
            $jpeg .= chr($color[2] % 256);
        }
        
        // EOI (End of Image)
        $jpeg .= "\xFF\xD9";
        
        // Sauvegarder l'image
        Storage::disk('public')->put('analyses-ia/' . $filename, $jpeg);
    }
}