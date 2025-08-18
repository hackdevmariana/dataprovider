<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo UserChallenge para la participación en retos.
 * 
 * Gestiona el estado y progreso de los usuarios en challenges,
 * incluyendo ranking, puntos y recompensas obtenidas.
 */
class UserChallenge extends Model
{
    protected $fillable = [
        'user_id',
        'challenge_id',
        'status',
        'joined_at',
        'completed_at',
        'progress',
        'current_value',
        'ranking_position',
        'points_earned',
        'reward_earned',
        'achievements_unlocked',
        'notes',
        'is_team_leader',
        'team_id',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'completed_at' => 'datetime',
        'progress' => 'array',
        'current_value' => 'decimal:4',
        'ranking_position' => 'integer',
        'points_earned' => 'integer',
        'reward_earned' => 'decimal:2',
        'achievements_unlocked' => 'array',
        'is_team_leader' => 'boolean',
        'team_id' => 'integer',
    ];

    /**
     * Usuario participante.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Reto en el que participa.
     */
    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeRanked($query)
    {
        return $query->whereNotNull('ranking_position')
                    ->orderBy('ranking_position');
    }

    /**
     * Marcar como completado.
     */
    public function markAsCompleted(int $pointsEarned = 0, float $rewardEarned = 0): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'points_earned' => $pointsEarned,
            'reward_earned' => $rewardEarned,
        ]);
    }

    /**
     * Actualizar progreso.
     */
    public function updateProgress(float $newValue, array $progressData = []): void
    {
        $this->update([
            'current_value' => $newValue,
            'progress' => array_merge($this->progress ?? [], $progressData),
        ]);
    }

    /**
     * Actualizar posición en ranking.
     */
    public function updateRanking(int $position): void
    {
        $this->update(['ranking_position' => $position]);
    }

    /**
     * Añadir logro desbloqueado.
     */
    public function addUnlockedAchievement(int $achievementId): void
    {
        $current = $this->achievements_unlocked ?? [];
        if (!in_array($achievementId, $current)) {
            $current[] = $achievementId;
            $this->update(['achievements_unlocked' => $current]);
        }
    }

    /**
     * Verificar si está completado.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Verificar si está activo.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Obtener el porcentaje de progreso basado en el objetivo del reto.
     */
    public function getProgressPercentageAttribute(): float
    {
        if (!$this->challenge || !$this->challenge->goals) {
            return 0.0;
        }

        $goals = $this->challenge->goals;
        $mainGoal = array_values($goals)[0] ?? 0;
        
        if ($mainGoal <= 0) return 0.0;

        return min(100, max(0, ($this->current_value / $mainGoal) * 100));
    }

    /**
     * Obtener el nombre legible del estado.
     */
    public function getStatusNameAttribute(): string
    {
        $statuses = [
            'registered' => 'Registrado',
            'active' => 'Activo',
            'completed' => 'Completado',
            'failed' => 'Fallido',
            'abandoned' => 'Abandonado',
        ];

        return $statuses[$this->status] ?? $this->status;
    }
}