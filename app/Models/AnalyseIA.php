<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;
use App\Traits\EncryptionTransition;

class AnalyseIA extends Model
{
    use Auditable, EncryptionTransition;

    protected $table = 'analyses_ia';

    // Champs sensibles à masquer dans les logs
    protected $sensitiveFields = [
        'commentaires_medecin',
        'recommandations_finales'
    ];

    protected $fillable = [
        'patient_id',
        'user_id',
        'nom_image',
        'chemin_image',
        'taille_image',
        'dimensions_image',
        'classe_predite',
        'probabilite',
        'toutes_probabilites',
        'niveau_risque',
        'interpretation',
        'recommandations',
        'commentaires_admin',
        'temps_analyse',
        'statut',
        'commentaires_medecin',
        'classe_finale_medecin',
        'decision_medecin',
        'recommandations_finales',
        'valide_par',
        'date_validation',
        'date_derniere_modification'
    ];

    protected $casts = [
        'toutes_probabilites' => 'array',
        'recommandations' => 'array',
        'probabilite' => 'decimal:4',
        'temps_analyse' => 'decimal:3',
        'date_validation' => 'datetime',
        'date_derniere_modification' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        // Note: Les casts 'encrypted' sont temporairement désactivés
        // pendant la migration pour éviter les erreurs de déchiffrement
    ];

    // Champs qui doivent être chiffrés
    protected $encryptedFields = [
        'commentaires_admin',
        'commentaires_medecin',
        'recommandations_finales',
        'interpretation'
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function analyste(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function validateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function getRisqueColorAttribute(): string
    {
        return match($this->niveau_risque) {
            'Élevé' => 'danger',
            'Modéré' => 'warning',
            'Faible' => 'success',
            default => 'secondary'
        };
    }

    public function getStatutColorAttribute(): string
    {
        return match($this->statut) {
            'Validé' => 'success',
            'En attente' => 'warning',
            'À revoir' => 'danger',
            'Rejeté' => 'dark',
            'Brouillon' => 'secondary',
            default => 'secondary'
        };
    }

    public function getConfidencePercentAttribute(): int
    {
        return round($this->probabilite * 100);
    }

    public function getClasseDescriptionAttribute(): string
    {
        $descriptions = [
            'Dyskeratotic' => 'Cellules dyskeratotiques - Anomalies de kératinisation',
            'Koilocytotic' => 'Cellules koilocytotiques - Possibles signes HPV',
            'Metaplastic' => 'Cellules métaplasiques - Transformation normale',
            'Parabasal' => 'Cellules parabasales - Couche profonde épithélium',
            'Superficial-Intermediate' => 'Cellules superficielles-intermédiaires - Cellules matures'
        ];

        return $descriptions[$this->classe_predite] ?? $this->classe_predite;
    }

    // Accesseurs pour les champs chiffrés
    public function getCommentairesAdminAttribute($value)
    {
        return $this->getEncryptedAttribute('commentaires_admin', $value);
    }

    public function getCommentairesMedecinAttribute($value)
    {
        return $this->getEncryptedAttribute('commentaires_medecin', $value);
    }

    public function getRecommandationsFinalesAttribute($value)
    {
        return $this->getEncryptedAttribute('recommandations_finales', $value);
    }

    public function getInterpretationAttribute($value)
    {
        return $this->getEncryptedAttribute('interpretation', $value);
    }

    // Mutateurs pour les champs chiffrés
    public function setCommentairesAdminAttribute($value)
    {
        $this->setEncryptedAttribute('commentaires_admin', $value);
    }

    public function setCommentairesMedecinAttribute($value)
    {
        $this->setEncryptedAttribute('commentaires_medecin', $value);
    }

    public function setRecommandationsFinalesAttribute($value)
    {
        $this->setEncryptedAttribute('recommandations_finales', $value);
    }

    public function setInterpretationAttribute($value)
    {
        $this->setEncryptedAttribute('interpretation', $value);
    }
}