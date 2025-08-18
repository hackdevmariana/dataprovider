<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Modelo Achievement para sistema de gamificación de KiroLux.
 * 
 * Gestiona logros, medallas y objetivos que los usuarios pueden
 * desbloquear mediante acciones relacionadas con energía renovable,
 * ahorro energético y participación en cooperativas.
 * 
 * @property int $id
 * @property string $name Nombre del logro
 * @property string $slug Slug único
 * @property string $description Descripción del logro
 * @property string|null $icon Icono del logro
 * @property string $badge_color Color del badge en HEX
 * @property string $category Categoría del logro
 * @property string $type Tipo de logro (single, progressive, recurring)
 * @property string $difficulty Dificultad (bronze, silver, gold, platinum, legendary)
 * @property array|null $conditions Condiciones para obtener el logro
 * @property int $points Puntos que otorga
 * @property int|null $required_value Valor requerido
 * @property string|null $required_unit Unidad del valor requerido
 * @property bool $is_active Si está activo
 * @property bool $is_hidden Si es un logro secreto
 * @property int $sort_order Orden de visualización
 */
class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'badge_color',
        'category',
        'type',
        'difficulty',
        'conditions',
        'points',
        'required_value',
        'required_unit',
        'is_active',
        'is_hidden',
        'sort_order',
    ];

    protected $casts = [
        'conditions' => 'array',
        'is_active' => 'boolean',
        'is_hidden' => 'boolean',
        'points' => 'integer',
        'required_value' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Usuarios que han obtenido este logro.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_achievements')
                    ->withPivot([
                        'progress',
                        'level',
                        'is_completed',
                        'completed_at',
                        'metadata',
                        'value_achieved',
                        'points_earned',
                        'is_notified'
                    ])
                    ->withTimestamps();
    }

    /**
     * Registros de logros de usuarios.
     */
    public function userAchievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }

    /**
     * Usuarios que han completado este logro.
     */
    public function completedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_achievements')
                    ->wherePivot('is_completed', true)
                    ->withPivot([
                        'completed_at',
                        'level',
                        'points_earned',
                        'value_achieved'
                    ])
                    ->withTimestamps();
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_hidden', false);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByDifficulty($query, string $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Verificar si un usuario ha completado este logro.
     */
    public function isCompletedByUser(User $user, int $level = 1): bool
    {
        return $this->userAchievements()
                   ->where('user_id', $user->id)
                   ->where('level', $level)
                   ->where('is_completed', true)
                   ->exists();
    }

    /**
     * Obtener el progreso de un usuario en este logro.
     */
    public function getUserProgress(User $user, int $level = 1): ?UserAchievement
    {
        return $this->userAchievements()
                   ->where('user_id', $user->id)
                   ->where('level', $level)
                   ->first();
    }

    /**
     * Obtener el color del badge como array RGB.
     */
    public function getBadgeColorRgb(): array
    {
        $hex = ltrim($this->badge_color, '#');
        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * Obtener el nombre legible de la dificultad.
     */
    public function getDifficultyNameAttribute(): string
    {
        $difficulties = [
            'bronze' => 'Bronce',
            'silver' => 'Plata',
            'gold' => 'Oro',
            'platinum' => 'Platino',
            'legendary' => 'Legendario',
        ];

        return $difficulties[$this->difficulty] ?? $this->difficulty;
    }

    /**
     * Obtener el nombre legible de la categoría.
     */
    public function getCategoryNameAttribute(): string
    {
        $categories = [
            'energy_saving' => 'Ahorro Energético',
            'solar_production' => 'Producción Solar',
            'cooperation' => 'Cooperativismo',
            'sustainability' => 'Sostenibilidad',
            'engagement' => 'Participación',
            'milestone' => 'Hitos',
            'streak' => 'Rachas',
            'community' => 'Comunidad',
        ];

        return $categories[$this->category] ?? $this->category;
    }

    /**
     * Obtener estadísticas del logro.
     */
    public function getStatsAttribute(): array
    {
        return [
            'total_earned' => $this->userAchievements()->where('is_completed', true)->count(),
            'total_in_progress' => $this->userAchievements()->where('is_completed', false)->count(),
            'completion_rate' => $this->getCompletionRate(),
            'average_time_to_complete' => $this->getAverageTimeToComplete(),
        ];
    }

    /**
     * Calcular la tasa de finalización del logro.
     */
    public function getCompletionRate(): float
    {
        $total = $this->userAchievements()->count();
        if ($total === 0) return 0.0;

        $completed = $this->userAchievements()->where('is_completed', true)->count();
        return round(($completed / $total) * 100, 2);
    }

    /**
     * Calcular el tiempo promedio para completar el logro.
     */
    public function getAverageTimeToComplete(): ?float
    {
        $completed = $this->userAchievements()
                         ->where('is_completed', true)
                         ->whereNotNull('completed_at')
                         ->get();

        if ($completed->isEmpty()) return null;

        $totalHours = $completed->sum(function ($userAchievement) {
            return $userAchievement->created_at->diffInHours($userAchievement->completed_at);
        });

        return round($totalHours / $completed->count(), 2);
    }
}