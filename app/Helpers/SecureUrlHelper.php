<?php

namespace App\Helpers;

class SecureUrlHelper
{
    /**
     * Générer une URL sécurisée pour une image d'analyse
     */
    public static function secureImageUrl(string $imagePath): string
    {
        // Extraire juste le nom du fichier du chemin complet
        $filename = basename($imagePath);
        
        return route('secure.image.show', $filename);
    }

    /**
     * Générer une URL de téléchargement sécurisée pour une image
     */
    public static function secureImageDownloadUrl(string $imagePath): string
    {
        $filename = basename($imagePath);
        
        return route('secure.image.download', $filename);
    }
}

// Fonctions helper globales
if (!function_exists('secure_image_url')) {
    function secure_image_url(string $imagePath): string
    {
        return \App\Helpers\SecureUrlHelper::secureImageUrl($imagePath);
    }
}

if (!function_exists('secure_image_download_url')) {
    function secure_image_download_url(string $imagePath): string
    {
        return \App\Helpers\SecureUrlHelper::secureImageDownloadUrl($imagePath);
    }
}