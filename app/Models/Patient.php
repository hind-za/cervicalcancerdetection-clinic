<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Auditable;
use App\Traits\EncryptionTransition;

class Patient extends Model
{
    use Auditable, EncryptionTransition;

    // Champs sensibles à masquer dans les logs
    protected $sensitiveFields = [
        'telephone',
        'email',
        'adresse',
        'antecedents_medicaux',
        'notes'
    ];
    protected $fillable = [
        'nom',
        'prenom',
        'date_naissance',
        'sexe',
        'telephone',
        'email',
        'adresse',
        'numero_dossier',
        'antecedents_medicaux',
        'notes'
    ];

    protected $casts = [
        'date_naissance' => 'date',
        // Note: Les casts 'encrypted' sont temporairement désactivés
        // pendant la migration pour éviter les erreurs de déchiffrement
    ];

    // Champs qui doivent être chiffrés
    protected $encryptedFields = [
        'nom',
        'prenom', 
        'telephone',
        'email',
        'adresse',
        'antecedents_medicaux',
        'notes'
    ];

    public function analyses(): HasMany
    {
        return $this->hasMany(AnalyseImage::class);
    }

    public function analysesIA(): HasMany
    {
        return $this->hasMany(AnalyseIA::class);
    }

    public function getAllAnalysesAttribute()
    {
        // Retourner toutes les analyses (IA + Image) combinées
        return $this->analysesIA->merge($this->analyses);
    }

    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function getAgeAttribute(): int
    {
        return $this->date_naissance->age;
    }

    // Accesseurs pour les champs chiffrés
    public function getNomAttribute($value)
    {
        return $this->getEncryptedAttribute('nom', $value);
    }

    public function getPrenomAttribute($value)
    {
        return $this->getEncryptedAttribute('prenom', $value);
    }

    public function getTelephoneAttribute($value)
    {
        return $this->getEncryptedAttribute('telephone', $value);
    }

    public function getEmailAttribute($value)
    {
        return $this->getEncryptedAttribute('email', $value);
    }

    public function getAdresseAttribute($value)
    {
        return $this->getEncryptedAttribute('adresse', $value);
    }

    public function getAntecedentsMedicauxAttribute($value)
    {
        return $this->getEncryptedAttribute('antecedents_medicaux', $value);
    }

    public function getNotesAttribute($value)
    {
        return $this->getEncryptedAttribute('notes', $value);
    }

    // Mutateurs pour les champs chiffrés
    public function setNomAttribute($value)
    {
        $this->setEncryptedAttribute('nom', $value);
    }

    public function setPrenomAttribute($value)
    {
        $this->setEncryptedAttribute('prenom', $value);
    }

    public function setTelephoneAttribute($value)
    {
        $this->setEncryptedAttribute('telephone', $value);
    }

    public function setEmailAttribute($value)
    {
        $this->setEncryptedAttribute('email', $value);
    }

    public function setAdresseAttribute($value)
    {
        $this->setEncryptedAttribute('adresse', $value);
    }

    public function setAntecedentsMedicauxAttribute($value)
    {
        $this->setEncryptedAttribute('antecedents_medicaux', $value);
    }

    public function setNotesAttribute($value)
    {
        $this->setEncryptedAttribute('notes', $value);
    }
}
