<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageEncryptionService
{
    /**
     * Chiffrer et stocker une image
     */
    public static function encryptAndStore($imageContent, string $path): bool
    {
        try {
            // Chiffrer le contenu de l'image
            $encryptedContent = Crypt::encrypt($imageContent);
            
            // Stocker l'image chiffrée
            $stored = Storage::disk('local')->put($path . '.encrypted', $encryptedContent);
            
            if ($stored) {
                Log::info('Image encrypted and stored', [
                    'path' => $path,
                    'size' => strlen($encryptedContent)
                ]);
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('Image encryption failed', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Déchiffrer et récupérer une image
     */
    public static function decryptAndRetrieve(string $path): ?string
    {
        try {
            $encryptedPath = $path . '.encrypted';
            
            if (!Storage::disk('local')->exists($encryptedPath)) {
                Log::warning('Encrypted image not found', ['path' => $encryptedPath]);
                return null;
            }
            
            // Récupérer le contenu chiffré
            $encryptedContent = Storage::disk('local')->get($encryptedPath);
            
            // Déchiffrer
            $decryptedContent = Crypt::decrypt($encryptedContent);
            
            Log::info('Image decrypted successfully', ['path' => $path]);
            
            return $decryptedContent;
        } catch (\Exception $e) {
            Log::error('Image decryption failed', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Migrer une image existante vers le format chiffré
     */
    public static function migrateToEncrypted(string $originalPath): bool
    {
        try {
            if (!Storage::disk('local')->exists($originalPath)) {
                Log::warning('Original image not found for migration', ['path' => $originalPath]);
                return false;
            }
            
            // Lire l'image originale
            $originalContent = Storage::disk('local')->get($originalPath);
            
            // Chiffrer et stocker
            $encrypted = self::encryptAndStore($originalContent, $originalPath);
            
            if ($encrypted) {
                // Supprimer l'original après chiffrement réussi
                Storage::disk('local')->delete($originalPath);
                Log::info('Image migrated to encrypted format', ['path' => $originalPath]);
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('Image migration failed', [
                'path' => $originalPath,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Vérifier si une image est chiffrée
     */
    public static function isEncrypted(string $path): bool
    {
        return Storage::disk('local')->exists($path . '.encrypted');
    }
}