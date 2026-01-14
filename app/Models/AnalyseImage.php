<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyseImage extends Model
{
    protected $fillable = [
        'patient_id',
        'nom_image',
        'chemin_image',
        'resultat',
        'confiance',
        'details',
        'temps_analyse',
        'statut',
        'valide_par',
        'date_validation',
        'commentaires'
    ];

    protected $casts = [
        'details' => 'array',
        'confiance' => 'decimal:2',
        'temps_analyse' => 'decimal:3',
        'date_validation' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function validateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function getResultatColorAttribute(): string
    {
        return match($this->resultat) {
            'Normal' => 'success',
            'Anomalie Détectée' => 'danger',
            'À Surveiller' => 'warning',
            default => 'secondary'
        };
    }

    public function getStatutColorAttribute(): string
    {
        return match($this->statut) {
            'Validé' => 'success',
            'En attente' => 'warning',
            'À revoir' => 'danger',
            default => 'secondary'
        };
    }
}
