<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ActivityFeed extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity_type',
        'related_type',
        'related_id',
        'activity_data',
        'description',
        'summary',
        'energy_amount_kwh',
        'cost_savings_eur',
        'co2_savings_kg',
        'investment_amount_eur',
        'community_impact_score',
        'visibility',
        'is_featured',
        'is_milestone',
        'notify_followers',
        'show_in_feed',
        'allow_interactions',
        'engagement_score',
        'likes_count',
        'loves_count',
        'wow_count',
        'comments_count',
        'shares_count',
        'bookmarks_count',
        'views_count',
        'latitude',
        'longitude',
        'location_name',
        'activity_occurred_at',
        'is_real_time',
        'activity_group',
        'parent_activity_id',
        'relevance_score',
        'boost_until',
        'algorithm_data',
        'status',
        'flags_count',
        'flag_reasons',
        'moderated_by',
        'moderated_at',
    ];

    protected $casts = [
        'activity_data' => 'array',
        'flag_reasons' => 'array',
        'algorithm_data' => 'array',
        'activity_occurred_at' => 'datetime',
        'boost_until' => 'datetime',
        'moderated_at' => 'datetime',
        'energy_amount_kwh' => 'decimal:2',
        'cost_savings_eur' => 'decimal:2',
        'co2_savings_kg' => 'decimal:2',
        'investment_amount_eur' => 'decimal:2',
        'relevance_score' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_featured' => 'boolean',
        'is_milestone' => 'boolean',
        'notify_followers' => 'boolean',
        'show_in_feed' => 'boolean',
        'allow_interactions' => 'boolean',
        'is_real_time' => 'boolean',
    ];

    // Relaciones

    /**
     * Usuario que generó la actividad
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Objeto relacionado con la actividad (polimórfico)
     */
    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Actividad padre (para actividades agrupadas)
     */
    public function parentActivity(): BelongsTo
    {
        return $this->belongsTo(ActivityFeed::class, 'parent_activity_id');
    }

    /**
     * Actividades hijas (sub-actividades)
     */
    public function childActivities(): HasMany
    {
        return $this->hasMany(ActivityFeed::class, 'parent_activity_id');
    }

    /**
     * Usuario que moderó la actividad
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /**
     * Interacciones sociales de esta actividad
     */
    public function interactions(): HasMany
    {
        return $this->hasMany(SocialInteraction::class, 'interactable_id')
                    ->where('interactable_type', self::class);
    }

    /**
     * Likes de esta actividad
     */
    public function likes(): HasMany
    {
        return $this->interactions()->where('interaction_type', 'like');
    }

    /**
     * Loves de esta actividad
     */
    public function loves(): HasMany
    {
        return $this->interactions()->where('interaction_type', 'love');
    }

    /**
     * Shares de esta actividad
     */
    public function shares(): HasMany
    {
        return $this->interactions()->where('interaction_type', 'share');
    }

    /**
     * Bookmarks de esta actividad
     */
    public function bookmarks(): HasMany
    {
        return $this->interactions()->where('interaction_type', 'bookmark');
    }

    // Scopes para consultas

    /**
     * Actividades públicas
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('visibility', 'public');
    }

    /**
     * Actividades activas
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Actividades destacadas
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Actividades de hitos
     */
    public function scopeMilestones(Builder $query): Builder
    {
        return $query->where('is_milestone', true);
    }

    /**
     * Actividades por tipo
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('activity_type', $type);
    }

    /**
     * Actividades de energía
     */
    public function scopeEnergyRelated(Builder $query): Builder
    {
        return $query->whereIn('activity_type', [
            'energy_saved',
            'solar_generated',
            'installation_completed',
            'carbon_milestone',
            'efficiency_improvement',
            'grid_contribution',
            'sustainability_goal'
        ]);
    }

    /**
     * Actividades de proyectos
     */
    public function scopeProjectRelated(Builder $query): Builder
    {
        return $query->whereIn('activity_type', [
            'project_funded',
            'investment_made',
            'roof_published',
            'production_right_sold'
        ]);
    }

    /**
     * Actividades comunitarias
     */
    public function scopeCommunityRelated(Builder $query): Builder
    {
        return $query->whereIn('activity_type', [
            'cooperative_joined',
            'community_contribution',
            'topic_created',
            'expert_verified'
        ]);
    }

    /**
     * Actividades por rango de fechas
     */
    public function scopeInDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('activity_occurred_at', [$startDate, $endDate]);
    }

    /**
     * Actividades por ubicación (radio en km)
     */
    public function scopeNearLocation(Builder $query, float $lat, float $lng, int $radiusKm = 50): Builder
    {
        return $query->whereNotNull(['latitude', 'longitude'])
                    ->whereRaw(
                        '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?',
                        [$lat, $lng, $lat, $radiusKm]
                    );
    }

    /**
     * Actividades con alto engagement
     */
    public function scopeHighEngagement(Builder $query, int $minScore = 100): Builder
    {
        return $query->where('engagement_score', '>=', $minScore);
    }

    /**
     * Actividades visibles para un usuario específico
     */
    public function scopeVisibleFor(Builder $query, User $user): Builder
    {
        return $query->where(function ($query) use ($user) {
            $query->where('visibility', 'public')
                  ->orWhere(function ($query) use ($user) {
                      // Actividades de cooperativa si el usuario es miembro
                      $query->where('visibility', 'cooperative')
                            ->whereHas('user.cooperatives', function ($q) use ($user) {
                                $q->whereHas('members', function ($q2) use ($user) {
                                    $q2->where('user_id', $user->id);
                                });
                            });
                  })
                  ->orWhere(function ($query) use ($user) {
                      // Actividades de seguidos
                      $query->where('visibility', 'followers')
                            ->whereHas('user.followers', function ($q) use ($user) {
                                $q->where('follower_id', $user->id)
                                  ->where('status', 'active');
                            });
                  })
                  ->orWhere('user_id', $user->id); // Propias actividades
        });
    }

    /**
     * Feed personalizado para un usuario
     */
    public function scopeFeedFor(Builder $query, User $user): Builder
    {
        return $query->visibleFor($user)
                    ->active()
                    ->where('show_in_feed', true)
                    ->orderByDesc('relevance_score')
                    ->orderByDesc('engagement_score')
                    ->orderByDesc('created_at');
    }

    // Métodos auxiliares

    /**
     * Incrementar el engagement score
     */
    public function incrementEngagement(int $points = 1): void
    {
        $this->increment('engagement_score', $points);
    }

    /**
     * Calcular score de relevancia basado en múltiples factores
     */
    public function calculateRelevanceScore(): float
    {
        $score = 100; // Score base

        // Factor temporal (decaimiento por tiempo)
        $hoursOld = $this->created_at->diffInHours(now());
        $timeDecay = max(0, 100 - ($hoursOld * 2)); // Decae 2 puntos por hora
        
        // Factor de engagement
        $engagementBonus = min(50, $this->engagement_score / 10);
        
        // Factor de hito
        $milestoneBonus = $this->is_milestone ? 25 : 0;
        
        // Factor de impacto energético
        $energyBonus = 0;
        if ($this->energy_amount_kwh) {
            $energyBonus = min(30, $this->energy_amount_kwh / 10);
        }
        
        // Factor de impacto económico
        $economicBonus = 0;
        if ($this->cost_savings_eur) {
            $economicBonus = min(25, $this->cost_savings_eur / 50);
        }
        
        // Factor de impacto ambiental
        $environmentalBonus = 0;
        if ($this->co2_savings_kg) {
            $environmentalBonus = min(20, $this->co2_savings_kg / 5);
        }

        $finalScore = $timeDecay + $engagementBonus + $milestoneBonus + 
                     $energyBonus + $economicBonus + $environmentalBonus;

        return round($finalScore, 2);
    }

    /**
     * Actualizar score de relevancia
     */
    public function updateRelevanceScore(): void
    {
        $this->update(['relevance_score' => $this->calculateRelevanceScore()]);
    }

    /**
     * Obtener descripción legible de la actividad
     */
    public function getReadableDescription(): string
    {
        if ($this->description) {
            return $this->description;
        }

        // Generar descripción automática basada en el tipo
        return match ($this->activity_type) {
            'energy_saved' => "Ahorró {$this->energy_amount_kwh} kWh de energía",
            'solar_generated' => "Generó {$this->energy_amount_kwh} kWh con energía solar",
            'achievement_unlocked' => "Desbloqueó un nuevo logro",
            'project_funded' => "Financió un proyecto energético",
            'installation_completed' => "Completó una instalación solar",
            'cooperative_joined' => "Se unió a una cooperativa energética",
            'carbon_milestone' => "Alcanzó un hito de reducción de CO2",
            default => "Realizó una actividad energética",
        };
    }

    /**
     * Verificar si el usuario puede ver esta actividad
     */
    public function canBeViewedBy(User $user): bool
    {
        return match ($this->visibility) {
            'public' => true,
            'private' => $this->user_id === $user->id,
            'cooperative' => $this->user_id === $user->id || 
                            $this->user->cooperatives()
                                 ->whereHas('members', fn($q) => $q->where('user_id', $user->id))
                                 ->exists(),
            'followers' => $this->user_id === $user->id || 
                          $this->user->followers()
                               ->where('follower_id', $user->id)
                               ->where('status', 'active')
                               ->exists(),
            default => false,
        };
    }
}
