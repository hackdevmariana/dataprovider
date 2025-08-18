<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo UserAchievement para la relación usuario-logros.
 * 
 * Gestiona el progreso y estado de los logros para cada usuario,
 * incluyendo progreso parcial, niveles y metadatos.
 */
class UserAchievement extends Model
{
    protected $fillable = [
        'user_id',
        'achievement_id',
        'progress',
        'level',
        'is_completed',
        'completed_at',
        'metadata',
        'value_achieved',
        'points_earned',
        'is_notified',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'metadata' => 'array',
        'value_achieved' => 'decimal:4',
        'points_earned' => 'integer',
        'progress' => 'integer',
        'level' => 'integer',
        'is_notified' => 'boolean',
    ];

    /**
     * Usuario que obtuvo el logro.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Logro obtenido.
     */
    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class);
    }

    /**
     * Scopes
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopeInProgress($query)
    {
        return $query->where('is_completed', false);
    }

    public function scopeNotNotified($query)
    {
        return $query->where('is_notified', false);
    }

    /**
     * Marcar como completado.
     */
    public function markAsCompleted(int $pointsEarned = null): void
    {
        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
            'points_earned' => $pointsEarned ?? $this->achievement->points,
        ]);
    }

    /**
     * Actualizar progreso.
     */
    public function updateProgress(int $newProgress, float $valueAchieved = null): void
    {
        $this->update([
            'progress' => $newProgress,
            'value_achieved' => $valueAchieved,
        ]);

        // Auto-completar si se alcanza el 100%
        if ($newProgress >= 100 && !$this->is_completed) {
            $this->markAsCompleted();
        }
    }

    /**
     * Marcar como notificado.
     */
    public function markAsNotified(): void
    {
        $this->update(['is_notified' => true]);
    }

    /**
     * Obtener el porcentaje de progreso.
     */
    public function getProgressPercentageAttribute(): float
    {
        return min(100, max(0, $this->progress));
    }
}