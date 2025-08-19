<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class TopicMembership extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'user_id',
        'role',
        'status',
        'notifications_enabled',
        'email_notifications',
        'push_notifications',
        'digest_notifications',
        'notification_frequency',
        'notification_preferences',
        'notify_new_posts',
        'notify_replies',
        'notify_mentions',
        'notify_trending',
        'notify_announcements',
        'notify_events',
        'show_in_main_feed',
        'prioritize_in_feed',
        'feed_weight',
        'posts_count',
        'comments_count',
        'upvotes_received',
        'downvotes_received',
        'reputation_score',
        'helpful_answers_count',
        'best_answers_count',
        'days_active',
        'consecutive_days_active',
        'posts_this_week',
        'posts_this_month',
        'avg_post_score',
        'participation_rate',
        'joined_at',
        'last_activity_at',
        'last_post_at',
        'last_comment_at',
        'last_visit_at',
        'total_visits',
        'total_time_spent_minutes',
        'moderation_permissions',
        'can_pin_posts',
        'can_feature_posts',
        'can_delete_posts',
        'can_ban_users',
        'can_edit_topic',
        'ban_reason',
        'banned_until',
        'banned_by',
        'muted_until',
        'muted_by',
        'moderation_notes',
        'show_activity_publicly',
        'allow_direct_messages',
        'show_online_status',
        'topic_badges',
        'featured_posts_count',
        'trending_posts_count',
        'became_contributor_at',
        'became_moderator_at',
        'custom_settings',
        'custom_title',
        'custom_flair',
        'interests_in_topic',
        'invited_by',
        'join_source',
        'join_metadata',
    ];

    protected $casts = [
        'notification_preferences' => 'array',
        'moderation_permissions' => 'array',
        'topic_badges' => 'array',
        'custom_settings' => 'array',
        'interests_in_topic' => 'array',
        'join_metadata' => 'array',
        'joined_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'last_post_at' => 'datetime',
        'last_comment_at' => 'datetime',
        'last_visit_at' => 'datetime',
        'banned_until' => 'datetime',
        'muted_until' => 'datetime',
        'became_contributor_at' => 'datetime',
        'became_moderator_at' => 'datetime',
        'avg_post_score' => 'decimal:2',
        'participation_rate' => 'decimal:2',
        'notifications_enabled' => 'boolean',
        'email_notifications' => 'boolean',
        'push_notifications' => 'boolean',
        'digest_notifications' => 'boolean',
        'notify_new_posts' => 'boolean',
        'notify_replies' => 'boolean',
        'notify_mentions' => 'boolean',
        'notify_trending' => 'boolean',
        'notify_announcements' => 'boolean',
        'notify_events' => 'boolean',
        'show_in_main_feed' => 'boolean',
        'prioritize_in_feed' => 'boolean',
        'can_pin_posts' => 'boolean',
        'can_feature_posts' => 'boolean',
        'can_delete_posts' => 'boolean',
        'can_ban_users' => 'boolean',
        'can_edit_topic' => 'boolean',
        'show_activity_publicly' => 'boolean',
        'allow_direct_messages' => 'boolean',
        'show_online_status' => 'boolean',
    ];

    // Relaciones

    /**
     * Tema al que pertenece la membresía
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Usuario de la membresía
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Usuario que invitó (si aplica)
     */
    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Usuario que baneó (si aplica)
     */
    public function bannedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'banned_by');
    }

    /**
     * Usuario que silenció (si aplica)
     */
    public function mutedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'muted_by');
    }

    // Scopes para consultas

    /**
     * Membresías activas
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Membresías pendientes
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Membresías baneadas
     */
    public function scopeBanned(Builder $query): Builder
    {
        return $query->where('status', 'banned');
    }

    /**
     * Moderadores
     */
    public function scopeModerators(Builder $query): Builder
    {
        return $query->whereIn('role', ['moderator', 'admin']);
    }

    /**
     * Miembros por rol
     */
    public function scopeByRole(Builder $query, string $role): Builder
    {
        return $query->where('role', $role);
    }

    /**
     * Miembros con notificaciones habilitadas
     */
    public function scopeWithNotifications(Builder $query): Builder
    {
        return $query->where('notifications_enabled', true);
    }

    /**
     * Miembros activos recientemente
     */
    public function scopeRecentlyActive(Builder $query, int $days = 7): Builder
    {
        return $query->where('last_activity_at', '>=', now()->subDays($days));
    }

    /**
     * Miembros con alta reputación
     */
    public function scopeHighReputation(Builder $query, int $minScore = 100): Builder
    {
        return $query->where('reputation_score', '>=', $minScore);
    }

    /**
     * Contribuidores frecuentes
     */
    public function scopeFrequentContributors(Builder $query, int $minPosts = 5): Builder
    {
        return $query->where('posts_count', '>=', $minPosts)
                    ->orderByDesc('posts_count');
    }

    // Métodos auxiliares

    /**
     * Verificar si la membresía está activa
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Verificar si es moderador
     */
    public function isModerator(): bool
    {
        return in_array($this->role, ['moderator', 'admin', 'creator']);
    }

    /**
     * Verificar si está baneado
     */
    public function isBanned(): bool
    {
        return $this->status === 'banned' || 
               ($this->banned_until && $this->banned_until->isFuture());
    }

    /**
     * Verificar si está silenciado
     */
    public function isMuted(): bool
    {
        return $this->status === 'muted' || 
               ($this->muted_until && $this->muted_until->isFuture());
    }

    /**
     * Verificar si puede realizar una acción específica
     */
    public function canPerform(string $action): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        return match ($action) {
            'post' => true,
            'comment' => !$this->isMuted(),
            'pin_posts' => $this->can_pin_posts || $this->isModerator(),
            'feature_posts' => $this->can_feature_posts || $this->isModerator(),
            'delete_posts' => $this->can_delete_posts || $this->isModerator(),
            'ban_users' => $this->can_ban_users || $this->role === 'admin',
            'edit_topic' => $this->can_edit_topic || $this->role === 'admin' || $this->topic->creator_id === $this->user_id,
            default => false,
        };
    }

    /**
     * Banear usuario
     */
    public function ban(string $reason = null, ?\DateTime $until = null, ?User $bannedBy = null): void
    {
        $this->update([
            'status' => 'banned',
            'ban_reason' => $reason,
            'banned_until' => $until,
            'banned_by' => $bannedBy?->id,
        ]);
    }

    /**
     * Silenciar usuario
     */
    public function mute(\DateTime $until, ?User $mutedBy = null): void
    {
        $this->update([
            'status' => 'muted',
            'muted_until' => $until,
            'muted_by' => $mutedBy?->id,
        ]);
    }

    /**
     * Reactivar usuario
     */
    public function activate(): void
    {
        $this->update([
            'status' => 'active',
            'ban_reason' => null,
            'banned_until' => null,
            'banned_by' => null,
            'muted_until' => null,
            'muted_by' => null,
        ]);
    }

    /**
     * Promover a rol superior
     */
    public function promoteToRole(string $newRole): void
    {
        $this->update(['role' => $newRole]);

        if ($newRole === 'contributor' && !$this->became_contributor_at) {
            $this->update(['became_contributor_at' => now()]);
        }

        if (in_array($newRole, ['moderator', 'admin']) && !$this->became_moderator_at) {
            $this->update(['became_moderator_at' => now()]);
        }
    }

    /**
     * Actualizar actividad del usuario
     */
    public function updateActivity(): void
    {
        $this->update([
            'last_activity_at' => now(),
            'last_visit_at' => now(),
        ]);

        $this->increment('total_visits');
    }

    /**
     * Incrementar contador de posts
     */
    public function incrementPosts(): void
    {
        $this->increment('posts_count');
        $this->increment('posts_this_week');
        $this->increment('posts_this_month');
        $this->update(['last_post_at' => now()]);
        $this->updateActivity();
    }

    /**
     * Incrementar contador de comentarios
     */
    public function incrementComments(): void
    {
        $this->increment('comments_count');
        $this->update(['last_comment_at' => now()]);
        $this->updateActivity();
    }

    /**
     * Actualizar score de reputación
     */
    public function updateReputationScore(int $points): void
    {
        $this->increment('reputation_score', $points);
        
        // Auto-promoción basada en reputación
        if ($this->role === 'member' && $this->reputation_score >= 100) {
            $this->promoteToRole('contributor');
        }
    }

    /**
     * Calcular tasa de participación
     */
    public function calculateParticipationRate(): float
    {
        $daysSinceJoined = $this->joined_at->diffInDays(now());
        if ($daysSinceJoined === 0) {
            return 0;
        }

        $totalActivity = $this->posts_count + $this->comments_count;
        return round(($totalActivity / $daysSinceJoined) * 100, 2);
    }

    /**
     * Actualizar tasa de participación
     */
    public function updateParticipationRate(): void
    {
        $this->update(['participation_rate' => $this->calculateParticipationRate()]);
    }

    /**
     * Obtener configuración de notificaciones
     */
    public function getNotificationConfig(): array
    {
        return [
            'enabled' => $this->notifications_enabled,
            'frequency' => $this->notification_frequency,
            'email' => $this->email_notifications,
            'push' => $this->push_notifications,
            'digest' => $this->digest_notifications,
            'types' => [
                'new_posts' => $this->notify_new_posts,
                'replies' => $this->notify_replies,
                'mentions' => $this->notify_mentions,
                'trending' => $this->notify_trending,
                'announcements' => $this->notify_announcements,
                'events' => $this->notify_events,
            ],
        ];
    }

    /**
     * Verificar si debe recibir notificación para un tipo específico
     */
    public function shouldNotify(string $type): bool
    {
        if (!$this->notifications_enabled || !$this->isActive()) {
            return false;
        }

        return match ($type) {
            'new_post' => $this->notify_new_posts,
            'reply' => $this->notify_replies,
            'mention' => $this->notify_mentions,
            'trending' => $this->notify_trending,
            'announcement' => $this->notify_announcements,
            'event' => $this->notify_events,
            default => false,
        };
    }

    /**
     * Obtener estadísticas del miembro
     */
    public function getStats(): array
    {
        return [
            'reputation_score' => $this->reputation_score,
            'posts_count' => $this->posts_count,
            'comments_count' => $this->comments_count,
            'helpful_answers_count' => $this->helpful_answers_count,
            'best_answers_count' => $this->best_answers_count,
            'avg_post_score' => $this->avg_post_score,
            'participation_rate' => $this->participation_rate,
            'days_active' => $this->days_active,
            'total_visits' => $this->total_visits,
            'days_as_member' => $this->joined_at->diffInDays(now()),
            'role_label' => $this->getRoleLabel(),
        ];
    }

    /**
     * Obtener etiqueta legible del rol
     */
    public function getRoleLabel(): string
    {
        return match ($this->role) {
            'member' => 'Miembro',
            'contributor' => 'Contribuidor',
            'moderator' => 'Moderador',
            'admin' => 'Administrador',
            'creator' => 'Creador',
            default => 'Miembro',
        };
    }

    // Eventos del modelo

    protected static function booted()
    {
        // Actualizar contadores del tema al crear membresía
        static::created(function (TopicMembership $membership) {
            if ($membership->status === 'active') {
                $membership->topic->increment('members_count');
            }
        });

        // Actualizar contadores del tema al cambiar estado
        static::updated(function (TopicMembership $membership) {
            if ($membership->isDirty('status')) {
                $original = $membership->getOriginal('status');
                
                if ($original !== 'active' && $membership->status === 'active') {
                    $membership->topic->increment('members_count');
                } elseif ($original === 'active' && $membership->status !== 'active') {
                    $membership->topic->decrement('members_count');
                }
            }
        });

        // Actualizar contadores del tema al eliminar membresía
        static::deleted(function (TopicMembership $membership) {
            if ($membership->status === 'active') {
                $membership->topic->decrement('members_count');
            }
        });
    }
}