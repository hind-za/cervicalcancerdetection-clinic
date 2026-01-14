<?php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

trait EncryptionTransition
{
    /**
     * Déchiffrer un attribut avec gestion de la transition
     */
    protected function getEncryptedAttribute($key, $value)
    {
        if (is_null($value)) {
            return null;
        }

        try {
            // Essayer de déchiffrer (données déjà chiffrées)
            return Crypt::decrypt($value);
        } catch (DecryptException $e) {
            // Si le déchiffrement échoue, c'est probablement du texte en clair
            // Retourner la valeur telle quelle
            return $value;
        }
    }

    /**
     * Vérifier si une valeur est déjà chiffrée
     */
    protected function isEncrypted($value): bool
    {
        return self::isValueEncrypted($value);
    }

    /**
     * Chiffrer un attribut avec gestion de la transition
     */
    protected function setEncryptedAttribute($key, $value)
    {
        if (is_null($value)) {
            $this->attributes[$key] = null;
            return;
        }

        // Vérifier si la valeur est déjà chiffrée
        try {
            Crypt::decrypt($value);
            // Si le déchiffrement réussit, c'est déjà chiffré
            $this->attributes[$key] = $value;
        } catch (DecryptException $e) {
            // Si le déchiffrement échoue, chiffrer la valeur
            $this->attributes[$key] = Crypt::encrypt($value);
        }
    }

    /**
     * Vérifier si une valeur est déjà chiffrée (méthode statique)
     */
    public static function isValueEncrypted($value): bool
    {
        if (is_null($value) || empty($value)) {
            return false;
        }

        try {
            Crypt::decrypt($value);
            return true;
        } catch (DecryptException $e) {
            return false;
        }
    }
}