<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

/**
 * Modelo Challenge para retos energéticos de KiroLux.
 * 
 * Gestiona challenges individuales, comunitarios y cooperativos
 * relacionados con ahorro energético, producción solar y
 * sostenibilidad ambiental.
 * 
 * @property int $id
 * @property string $name Nombre del reto
 * @property string $slug Slug único
 * @property string $description Descripción del reto
 * @property string|null $instructions Instrucciones detalladas
 * @property string|null $icon Icono del reto
 * @property string $banner_color Color del banner
 * @property string $type Tipo (individual, community, cooperative)
 * @property string $category Categoría del reto
 * @property string $difficulty Dificultad
 * @property \Carbon\Carbon $start_date Fecha de inicio
 * @property \Carbon\Carbon $end_date Fecha de fin
 * @property array|null $goals Objetivos del reto
 * @property array|null $rewards Recompensas
 * @property int|null $max_participants Máximo participantes
 * @property int $min_participants Mínimo participantes
 * @property float $entry_fee Cuota de entrada
 * @property float $prize_pool Premio acumulado
 * @property bool $is_active Si está activo
 * @property bool $is_featured Si está destacado
 * @property bool $auto_join Auto-inscripción
 * @property int $sort_order Orden
 */
class Challenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'instructions',
        'icon',
        'banner_color',
        'type',
        'category',
        'difficulty',
        'start_date',
        'end_date',
        'goals',
        'rewards',
        'max_participants',
        'min_participants',
        'entry_fee',
        'prize_pool',
        'is_active',
        'is_featured',
        'auto_join',
        'sort_order',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'goals' => 'array',
        'rewards' => 'array',
        'max_participants' => 'integer',
        'min_participants' => 'integer',
        'entry_fee' => 'decimal:2',
        'prize_pool' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'auto_join' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Usuarios participantes en el reto.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_challenges')
                    ->withPivot([
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
                        'team_id'
                    ])
                    ->withTimestamps();
    }

    /**
     * Registros de participación de usuarios.
     */
    public function userChallenges(): HasMany
    {
        return $this->hasMany(UserChallenge::class);
    }

    /**
     * Participantes activos.
     */
    public function activeParticipants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_challenges')
                    ->wherePivot('status', 'active')
                    ->withPivot(['current_value', 'ranking_position'])
                    ->withTimestamps();
    }

    /**
     * Participantes que completaron el reto.
     */
    public function completedParticipants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_challenges')
                    ->wherePivot('status', 'completed')
                    ->withPivot(['completed_at', 'points_earned', 'reward_earned'])
                    ->withTimestamps();
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOngoing($query)
    {
        $now = Carbon::now();
        return $query->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', Carbon::now());
    }

    public function scopeFinished($query)
    {
        return $query->where('end_date', '<', Carbon::now());
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
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
        return $query->orderBy('sort_order')->orderBy('start_date', 'desc');
    }

    /**
     * Verificar si el reto está en curso.
     */
    public function isOngoing(): bool
    {
        $now = Carbon::now();
        return $this->start_date <= $now && $this->end_date >= $now;
    }

    /**
     * Verificar si el reto ha terminado.
     */
    public function isFinished(): bool
    {
        return $this->end_date < Carbon::now();
    }

    /**
     * Verificar si el reto aún no ha comenzado.
     */
    public function isUpcoming(): bool
    {
        return $this->start_date > Carbon::now();
    }

    /**
     * Verificar si un usuario puede unirse al reto.
     */
    public function canUserJoin(User $user): bool
    {
        if (!$this->is_active || $this->isFinished()) {
            return false;
        }

        if ($this->userChallenges()->where('user_id', $user->id)->exists()) {
            return false;
        }

        if ($this->max_participants && $this->getCurrentParticipantsCount() >= $this->max_participants) {
            return false;
        }

        return true;
    }

    /**
     * Obtener el número actual de participantes.
     */
    public function getCurrentParticipantsCount(): int
    {
        return $this->userChallenges()->count();
    }

    /**
     * Obtener el porcentaje de ocupación.
     */
    public function getOccupancyPercentage(): float
    {
        if (!$this->max_participants) return 0.0;

        return round(($this->getCurrentParticipantsCount() / $this->max_participants) * 100, 2);
    }

    /**
     * Obtener los días restantes.
     */
    public function getDaysRemaining(): int
    {
        if ($this->isFinished()) return 0;
        
        return max(0, Carbon::now()->diffInDays($this->end_date, false));
    }

    /**
     * Obtener el ranking de participantes.
     */
    public function getRanking(int $limit = 10): \Illuminate\Support\Collection
    {
        return $this->userChallenges()
                   ->with('user')
                   ->whereNotNull('ranking_position')
                   ->orderBy('ranking_position')
                   ->limit($limit)
                   ->get();
    }

    /**
     * Obtener estadísticas del reto.
     */
    public function getStatsAttribute(): array
    {
        return [
            'total_participants' => $this->getCurrentParticipantsCount(),
            'active_participants' => $this->userChallenges()->where('status', 'active')->count(),
            'completed_participants' => $this->userChallenges()->where('status', 'completed')->count(),
            'completion_rate' => $this->getCompletionRate(),
            'average_progress' => $this->getAverageProgress(),
            'total_prize_pool' => $this->prize_pool,
            'days_remaining' => $this->getDaysRemaining(),
        ];
    }

    /**
     * Calcular la tasa de finalización.
     */
    public function getCompletionRate(): float
    {
        $total = $this->getCurrentParticipantsCount();
        if ($total === 0) return 0.0;

        $completed = $this->userChallenges()->where('status', 'completed')->count();
        return round(($completed / $total) * 100, 2);
    }

    /**
     * Calcular el progreso promedio.
     */
    public function getAverageProgress(): float
    {
        $participants = $this->userChallenges()->get();
        if ($participants->isEmpty()) return 0.0;

        $totalProgress = $participants->sum('current_value');
        return round($totalProgress / $participants->count(), 2);
    }

    /**
     * Obtener el nombre legible del tipo.
     */
    public function getTypeNameAttribute(): string
    {
        $types = [
            'individual' => 'Individual',
            'community' => 'Comunitario',
            'cooperative' => 'Cooperativo',
        ];

        return $types[$this->type] ?? $this->type;
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
            'education' => 'Educativo',
        ];

        return $categories[$this->category] ?? $this->category;
    }

    /**
     * Obtener el nombre legible de la dificultad.
     */
    public function getDifficultyNameAttribute(): string
    {
        $difficulties = [
            'easy' => 'Fácil',
            'medium' => 'Medio',
            'hard' => 'Difícil',
            'expert' => 'Experto',
        ];

        return $difficulties[$this->difficulty] ?? $this->difficulty;
    }
}