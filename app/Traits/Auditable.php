<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    protected static function bootAuditable()
    {
        static::created(function ($model) {
            AuditLog::logAction(
                'create',
                get_class($model),
                $model->id,
                null,
                $model->getAttributes(),
                'medium',
                "Création de " . class_basename($model) . " ID: {$model->id}"
            );
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            $original = $model->getOriginal();
            
            // Filtrer les champs sensibles pour l'audit
            $sensitiveFields = $model->getSensitiveFields ?? [];
            $oldValues = array_intersect_key($original, $changes);
            
            // Masquer les champs sensibles dans les logs
            foreach ($sensitiveFields as $field) {
                if (isset($oldValues[$field])) {
                    $oldValues[$field] = '[MASKED]';
                }
                if (isset($changes[$field])) {
                    $changes[$field] = '[MASKED]';
                }
            }

            AuditLog::logAction(
                'update',
                get_class($model),
                $model->id,
                $oldValues,
                $changes,
                'high',
                "Modification de " . class_basename($model) . " ID: {$model->id}"
            );
        });

        static::deleted(function ($model) {
            AuditLog::logAction(
                'delete',
                get_class($model),
                $model->id,
                $model->getAttributes(),
                null,
                'critical',
                "Suppression de " . class_basename($model) . " ID: {$model->id}"
            );
        });
    }

    /**
     * Log d'une action personnalisée
     */
    public function logCustomAction(string $action, string $description, string $severity = 'medium')
    {
        AuditLog::logAction(
            $action,
            get_class($this),
            $this->id,
            null,
            null,
            $severity,
            $description
        );
    }
}