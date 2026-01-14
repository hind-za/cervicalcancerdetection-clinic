<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AnalyseIA;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class FixImagesSeeder extends Seeder
{
    public function run()
    {
        // Créer le dossier d'analyses s'il n'existe pas
        Storage::disk('public')->makeDirectory('analyses-ia');

        // Créer des images de test simples mais valides
        $this->createSimpleImage('hind_cervical_001.jpg');
        $this->createSimpleImage('hind_cervical_002.jpg');
        $this->createSimpleImage('hind_cervical_003.jpg');
        $this->createSimpleImage('hind_cervical_004.jpg');
        $this->createSimpleImage('hind_cervical_005.jpg');

        $this->command->info('✅ Images de test créées avec succès!');
        
        // Vérifier que les analyses existent et ont les bons chemins
        $analyses = AnalyseIA::all();
        foreach ($analyses as $analyse) {
            $imagePath = 'analyses-ia/' . basename($analyse->chemin_image);
            if (Storage::disk('public')->exists($imagePath)) {
                $size = Storage::disk('public')->size($imagePath);
                $this->command->info("✅ Image trouvée: {$imagePath} ({$size} bytes)");
            } else {
                $this->command->error("❌ Image manquante: {$imagePath}");
            }
        }
    }

    private function createSimpleImage($filename)
    {
        // Créer une image SVG simple qui sera convertie
        $svg = '<?xml version="1.0" encoding="UTF-8"?>
<svg width="300" height="300" xmlns="http://www.w3.org/2000/svg">
  <rect width="300" height="300" fill="#f0f0f0" stroke="#ccc" stroke-width="2"/>
  <text x="150" y="120" font-family="Arial" font-size="16" text-anchor="middle" fill="#333">Analyse Cervicale</text>
  <text x="150" y="150" font-family="Arial" font-size="14" text-anchor="middle" fill="#666">' . $filename . '</text>
  <text x="150" y="180" font-family="Arial" font-size="12" text-anchor="middle" fill="#999">Image de Test</text>
  <circle cx="80" cy="220" r="8" fill="#ffcccc"/>
  <circle cx="120" cy="200" r="6" fill="#ccffcc"/>
  <circle cx="160" cy="230" r="7" fill="#ccccff"/>
  <circle cx="200" cy="210" r="5" fill="#ffffcc"/>
  <circle cx="240" cy="240" r="9" fill="#ffccff"/>
</svg>';

        // Sauvegarder le SVG temporairement
        $tempSvgPath = sys_get_temp_dir() . '/' . $filename . '.svg';
        file_put_contents($tempSvgPath, $svg);

        // Pour le moment, créons une image PNG simple avec une signature binaire valide
        $pngData = $this->createMinimalPNG();
        
        // Sauvegarder dans le storage
        Storage::disk('public')->put('analyses-ia/' . $filename, $pngData);
        
        // Nettoyer
        if (file_exists($tempSvgPath)) {
            unlink($tempSvgPath);
        }
    }

    private function createMinimalPNG()
    {
        // Créer un PNG minimal valide (1x1 pixel transparent)
        // Signature PNG + IHDR + IDAT + IEND
        $png = pack('H*', 
            '89504e470d0a1a0a' . // PNG signature
            '0000000d' . // IHDR length
            '49484452' . // IHDR
            '00000001' . // width: 1
            '00000001' . // height: 1
            '08' . // bit depth: 8
            '06' . // color type: RGBA
            '00' . // compression: deflate
            '00' . // filter: none
            '00' . // interlace: none
            'ae426082' . // IHDR CRC
            '0000000b' . // IDAT length
            '49444154' . // IDAT
            '789c6300010000050001' . // compressed data
            '0d0a2db4' . // IDAT CRC
            '00000000' . // IEND length
            '49454e44' . // IEND
            'ae426082'   // IEND CRC
        );
        
        return $png;
    }
}