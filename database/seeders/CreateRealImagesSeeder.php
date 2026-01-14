<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AnalyseIA;
use Illuminate\Support\Facades\Storage;

class CreateRealImagesSeeder extends Seeder
{
    public function run()
    {
        // Créer le dossier d'analyses s'il n'existe pas
        Storage::disk('public')->makeDirectory('analyses-ia');

        // Créer une vraie image PNG de test (100x100 pixels, couleur unie)
        $this->createTestImage('hind_cervical_001.jpg', 255, 200, 200); // Rose clair
        $this->createTestImage('hind_cervical_002.jpg', 200, 255, 200); // Vert clair
        $this->createTestImage('hind_cervical_003.jpg', 200, 200, 255); // Bleu clair
        $this->createTestImage('hind_cervical_004.jpg', 255, 255, 200); // Jaune clair
        $this->createTestImage('hind_cervical_005.jpg', 255, 220, 255); // Violet clair

        $this->command->info('✅ Images de test créées avec succès!');
        
        // Vérifier que les analyses existent et ont les bons chemins
        $analyses = AnalyseIA::all();
        foreach ($analyses as $analyse) {
            $imagePath = 'analyses-ia/' . basename($analyse->chemin_image);
            if (Storage::disk('public')->exists($imagePath)) {
                $this->command->info("✅ Image trouvée: {$imagePath}");
            } else {
                $this->command->error("❌ Image manquante: {$imagePath}");
            }
        }
    }

    private function createTestImage($filename, $r = 200, $g = 200, $b = 200)
    {
        // Créer une image de 300x300 pixels
        $width = 300;
        $height = 300;
        
        // Créer l'image
        $image = imagecreate($width, $height);
        
        // Définir les couleurs
        $backgroundColor = imagecolorallocate($image, $r, $g, $b);
        $textColor = imagecolorallocate($image, 0, 0, 0); // Noir
        $borderColor = imagecolorallocate($image, 100, 100, 100); // Gris
        
        // Remplir l'arrière-plan
        imagefill($image, 0, 0, $backgroundColor);
        
        // Ajouter une bordure
        imagerectangle($image, 0, 0, $width-1, $height-1, $borderColor);
        imagerectangle($image, 5, 5, $width-6, $height-6, $borderColor);
        
        // Ajouter du texte
        $text1 = "Analyse Cervicale";
        $text2 = basename($filename, '.jpg');
        $text3 = "Image de Test";
        
        // Centrer le texte
        imagestring($image, 3, ($width - strlen($text1) * 10) / 2, $height / 2 - 30, $text1, $textColor);
        imagestring($image, 2, ($width - strlen($text2) * 8) / 2, $height / 2, $text2, $textColor);
        imagestring($image, 2, ($width - strlen($text3) * 8) / 2, $height / 2 + 20, $text3, $textColor);
        
        // Ajouter quelques formes pour simuler des cellules
        for ($i = 0; $i < 20; $i++) {
            $x = rand(20, $width - 20);
            $y = rand(20, $height - 20);
            $size = rand(5, 15);
            $cellColor = imagecolorallocate($image, 
                max(0, $r - rand(0, 50)), 
                max(0, $g - rand(0, 50)), 
                max(0, $b - rand(0, 50))
            );
            imagefilledellipse($image, $x, $y, $size, $size, $cellColor);
        }
        
        // Sauvegarder l'image
        $tempPath = sys_get_temp_dir() . '/' . $filename;
        imagejpeg($image, $tempPath, 85);
        
        // Copier vers le storage
        $content = file_get_contents($tempPath);
        Storage::disk('public')->put('analyses-ia/' . $filename, $content);
        
        // Nettoyer
        imagedestroy($image);
        unlink($tempPath);
    }
}