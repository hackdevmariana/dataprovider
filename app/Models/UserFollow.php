<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class UserFollow extends Model
{
    use HasFactory;

    protected $fillable = [
        'follower_id',
        'following_id',
        'follow_type',
        'notify_new_activity',
        'notify_achievements',
        'notify_projects',
        'notify_investments',
        'notify_milestones',
        'notify_content',
        'notification_frequency',
        'show_in_main_feed',
        'prioritize_in_feed',
        'feed_weight',
        'follow_reason',
        'interests',
        'tags',
        'is_mutual',
        'mutual_since',
        'interactions_count',
        'last_interaction_at',
        'engagement_score',
        'content_views',
        'is_public',
        'show_to_followed',
        'allow_followed_to_see_activity',
        'content_filters',
        'activity_filters',
        'minimum_relevance_score',
        'status',
        'status_changed_at',
        'status_reason',
        'followed_at',
        'last_seen_activity_at',
        'days_following',
        'relevance_decay_rate',
        'algorithm_preferences',
    ];

    protected $casts = [
        'interests' => 'array',
        'tags' => 'array',
        'content_filters' => 'array',
        'activity_filters' => 'array',
        'algorithm_preferences' => 'array',
        'mutual_since' => 'datetime',
        'last_interaction_at' => 'datetime',
        'status_changed_at' => 'datetime',
        'followed_at' => 'datetime',
        'last_seen_activity_at' => 'datetime',
        'engagement_score' => 'decimal:2',
        'minimum_relevance_score' => 'decimal:2',
        'relevance_decay_rate' => 'decimal:2',
        'notify_new_activity' => 'boolean',
        'notify_achievements' => 'boolean',
        'notify_projects' => 'boolean',
        'notify_investments' => 'boolean',
        'notify_milestones' => 'boolean',
        'notify_content' => 'boolean',
        'show_in_main_feed' => 'boolean',
        'prioritize_in_feed' => 'boolean',
        'is_mutual' => 'boolean',
        'is_public' => 'boolean',
        'show_to_followed' => 'boolean',
        'allow_followed_to_see_activity' => 'boolean',
    ];

    // Relaciones

    /**
     * Usuario que sigue (follower)
     */
    public function follower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    /**
     * Usuario seguido (following)
     */
    public function following(): BelongsTo
    {
        return $this->belongsTo(User::class, 'following_id');
    }

    // Scopes para consultas

    /**
     * Seguimientos activos
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Seguimientos mutuos
     */
    public function scopeMutual(Builder $query): Builder
    {
        return $query->where('is_mutual', true);
    }

    /**
     * Seguimientos públicos
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    /**
     * Seguimientos por tipo
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('follow_type', $type);
    }

    /**
     * Seguimientos que aparecen en feed principal
     */
    public function scopeInMainFeed(Builder $query): Builder
    {
        return $query->where('show_in_main_feed', true);
    }

    /**
     * Seguimientos prioritarios en feed
     */
    public function scopePrioritized(Builder $query): Builder
    {
        return $query->where('prioritize_in_feed', true);
    }

    /**
     * Seguimientos con notificaciones habilitadas
     */
    public function scopeWithNotifications(Builder $query): Builder
    {
        return $query->where(function ($query) {
            $query->where('notify_new_activity', true)
                  ->orWhere('notify_achievements', true)
                  ->orWhere('notify_projects', true)
                  ->orWhere('notify_investments', true)
                  ->orWhere('notify_milestones', true)
                  ->orWhere('notify_content', true);
        });
    }

    /**
     * Seguimientos con alta interacción
     */
    public function scopeHighEngagement(Builder $query, float $minScore = 50.0): Builder
    {
        return $query->where('engagement_score', '>=', $minScore);
    }

    /**
     * Seguimientos recientes
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('followed_at', '>=', now()->subDays($days));
    }

    /**
     * Seguimientos de larga duración
     */
    public function scopeLongTerm(Builder $query, int $days = 365): Builder
    {
        return $query->where('followed_at', '<=', now()->subDays($days));
    }

    /**
     * Seguimientos por frecuencia de notificación
     */
    public function scopeByNotificationFrequency(Builder $query, string $frequency): Builder
    {
        return $query->where('notification_frequency', $frequency);
    }

    /**
     * Seguimientos que deben recibir notificación instantánea
     */
    public function scopeInstantNotification(Builder $query): Builder
    {
        return $query->where('notification_frequency', 'instant')
                    ->withNotifications();
    }

    /**
     * Seguimientos para resumen diario
     */
    public function scopeDailyDigest(Builder $query): Builder
    {
        return $query->where('notification_frequency', 'daily_digest')
                    ->withNotifications();
    }

    /**
     * Seguimientos de un seguidor específico
     */
    public function scopeByFollower(Builder $query, User $follower): Builder
    {
        return $query->where('follower_id', $follower->id);
    }

    /**
     * Seguimientos de un usuario seguido específico
     */
    public function scopeByFollowing(Builder $query, User $following): Builder
    {
        return $query->where('following_id', $following->id);
    }

    // Métodos auxiliares

    /**
     * Verificar si el seguimiento debe notificar sobre un tipo de actividad
     */
    public function shouldNotifyFor(string $activityType): bool
    {
        if ($this->status !== 'active' || $this->notification_frequency === 'never') {
            return false;
        }

        return match ($activityType) {
            'energy_saved', 'solar_generated', 'installation_completed', 'carbon_milestone' => $this->notify_new_activity,
            'achievement_unlocked', 'milestone_reached' => $this->notify_achievements,
            'project_funded', 'roof_published', 'cooperative_joined' => $this->notify_projects,
            'investment_made', 'production_right_sold' => $this->notify_investments,
            'content_published', 'topic_created', 'review_published' => $this->notify_content,
            default => $this->notify_new_activity,
        };
    }

    /**
     * Verificar si debe incluir una actividad en el feed
     */
    public function shouldIncludeInFeed(ActivityFeed $activity): bool
    {
        if (!$this->show_in_main_feed || $this->status !== 'active') {
            return false;
        }

        // Verificar score mínimo de relevancia
        if ($activity->relevance_score < $this->minimum_relevance_score) {
            return false;
        }

        // Verificar filtros de tipo de actividad
        if ($this->activity_filters && 
            in_array($activity->activity_type, $this->activity_filters)) {
            return false;
        }

        // Verificar filtros de contenido
        if ($this->content_filters && $activity->related_type &&
            in_array($activity->related_type, $this->content_filters)) {
            return false;
        }

        return true;
    }

    /**
     * Marcar como mutual
     */
    public function markAsMutual(): void
    {
        $this->update([
            'is_mutual' => true,
            'mutual_since' => now(),
        ]);
    }

    /**
     * Desmarcar como mutual
     */
    public function unmarkAsMutual(): void
    {
        $this->update([
            'is_mutual' => false,
            'mutual_since' => null,
        ]);
    }

    /**
     * Incrementar contador de interacciones
     */
    public function incrementInteractions(): void
    {
        $this->increment('interactions_count');
        $this->update(['last_interaction_at' => now()]);
    }

    /**
     * Incrementar contador de visualizaciones de contenido
     */
    public function incrementContentViews(): void
    {
        $this->increment('content_views');
    }

    /**
     * Actualizar score de engagement
     */
    public function updateEngagementScore(): void
    {
        $score = 0;

        // Factor de interacciones
        $score += min(50, $this->interactions_count * 2);

        // Factor de visualizaciones
        $score += min(25, $this->content_views * 0.5);

        // Factor de tiempo siguiendo
        $score += min(25, $this->days_following * 0.1);

        // Factor de reciprocidad
        if ($this->is_mutual) {
            $score += 20;
        }

        // Factor de actividad reciente
        if ($this->last_interaction_at && 
            $this->last_interaction_at->isAfter(now()->subDays(7))) {
            $score += 15;
        }

        $this->update(['engagement_score' => round($score, 2)]);
    }

    /**
     * Actualizar días siguiendo
     */
    public function updateDaysFollowing(): void
    {
        $days = $this->followed_at->diffInDays(now());
        $this->update(['days_following' => $days]);
    }

    /**
     * Pausar temporalmente
     */
    public function pause(string $reason = null): void
    {
        $this->update([
            'status' => 'paused',
            'status_changed_at' => now(),
            'status_reason' => $reason,
        ]);
    }

    /**
     * Silenciar notificaciones
     */
    public function mute(string $reason = null): void
    {
        $this->update([
            'status' => 'muted',
            'status_changed_at' => now(),
            'status_reason' => $reason,
        ]);
    }

    /**
     * Bloquear seguimiento
     */
    public function block(string $reason = null): void
    {
        $this->update([
            'status' => 'blocked',
            'status_changed_at' => now(),
            'status_reason' => $reason,
        ]);
    }

    /**
     * Reactivar seguimiento
     */
    public function activate(): void
    {
        $this->update([
            'status' => 'active',
            'status_changed_at' => now(),
            'status_reason' => null,
        ]);
    }

    /**
     * Obtener peso para algoritmo de feed
     */
    public function getFeedWeight(): int
    {
        $weight = $this->feed_weight;

        // Ajustar por engagement
        $engagementBonus = min(20, $this->engagement_score / 5);
        
        // Ajustar por prioridad
        if ($this->prioritize_in_feed) {
            $weight += 30;
        }

        // Ajustar por mutualidad
        if ($this->is_mutual) {
            $weight += 15;
        }

        // Ajustar por actividad reciente
        if ($this->last_interaction_at && 
            $this->last_interaction_at->isAfter(now()->subDays(3))) {
            $weight += 10;
        }

        return min(200, $weight + $engagementBonus);
    }

    // Eventos del modelo

    protected static function booted()
    {
        // Al crear un seguimiento, verificar reciprocidad
        static::created(function (UserFollow $follow) {
            $follow->checkMutualFollowing();
        });

        // Al eliminar un seguimiento, actualizar reciprocidad del otro
        static::deleted(function (UserFollow $follow) {
            $follow->updateMutualFollowing();
        });
    }

    /**
     * Verificar y marcar seguimiento mutuo
     */
    private function checkMutualFollowing(): void
    {
        $reciprocal = static::where('follower_id', $this->following_id)
                           ->where('following_id', $this->follower_id)
                           ->where('status', 'active')
                           ->first();

        if ($reciprocal) {
            $this->markAsMutual();
            $reciprocal->markAsMutual();
        }
    }

    /**
     * Actualizar seguimiento mutuo al eliminar
     */
    private function updateMutualFollowing(): void
    {
        $reciprocal = static::where('follower_id', $this->following_id)
                           ->where('following_id', $this->follower_id)
                           ->first();

        if ($reciprocal && $reciprocal->is_mutual) {
            $reciprocal->unmarkAsMutual();
        }
    }
}
