<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SocialInteraction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'interactable_type',
        'interactable_id',
        'interaction_type',
        'interaction_note',
        'interaction_data',
        'source',
        'device_type',
        'latitude',
        'longitude',
        'is_public',
        'notify_author',
        'show_in_activity',
        'engagement_weight',
        'quality_score',
        'interaction_expires_at',
        'is_temporary',
        'status',
    ];

    protected $attributes = [
        'status' => 'active',
        'is_public' => true,
        'notify_author' => true,
        'show_in_activity' => true,
        'is_temporary' => false,
    ];

    protected $casts = [
        'interaction_data' => 'array',
        'interaction_expires_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'quality_score' => 'decimal:2',
        'is_public' => 'boolean',
        'notify_author' => 'boolean',
        'show_in_activity' => 'boolean',
        'is_temporary' => 'boolean',
    ];

    // Relaciones

    /**
     * Usuario que realiza la interacción
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Objeto con el que se interactúa (polimórfico)
     */
    public function interactable(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes para consultas

    /**
     * Interacciones activas
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Interacciones públicas
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    /**
     * Interacciones por tipo
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('interaction_type', $type);
    }

    /**
     * Likes
     */
    public function scopeLikes(Builder $query): Builder
    {
        return $query->where('interaction_type', 'like');
    }

    /**
     * Loves
     */
    public function scopeLoves(Builder $query): Builder
    {
        return $query->where('interaction_type', 'love');
    }

    /**
     * Shares
     */
    public function scopeShares(Builder $query): Builder
    {
        return $query->where('interaction_type', 'share');
    }

    /**
     * Bookmarks
     */
    public function scopeBookmarks(Builder $query): Builder
    {
        return $query->where('interaction_type', 'bookmark');
    }

    /**
     * Interacciones positivas (like, love, wow, celebrate, support)
     */
    public function scopePositive(Builder $query): Builder
    {
        return $query->whereIn('interaction_type', ['like', 'love', 'wow', 'celebrate', 'support']);
    }

    /**
     * Interacciones de engagement (excluye report, hide, block)
     */
    public function scopeEngagement(Builder $query): Builder
    {
        return $query->whereNotIn('interaction_type', ['report', 'hide', 'block']);
    }

    /**
     * Interacciones recientes
     */
    public function scopeRecent(Builder $query, int $hours = 24): Builder
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    /**
     * Interacciones no expiradas
     */
    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where(function ($query) {
            $query->whereNull('interaction_expires_at')
                  ->orWhere('interaction_expires_at', '>', now());
        });
    }

    /**
     * Interacciones de un usuario específico
     */
    public function scopeByUser(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * Interacciones con un objeto específico
     */
    public function scopeWithObject(Builder $query, string $type, int $id): Builder
    {
        return $query->where('interactable_type', $type)
                    ->where('interactable_id', $id);
    }

    /**
     * Interacciones que deben mostrar notificación
     */
    public function scopeShouldNotify(Builder $query): Builder
    {
        return $query->where('notify_author', true)
                    ->where('status', 'active');
    }

    // Métodos auxiliares

    /**
     * Verificar si la interacción ha expirado
     */
    public function hasExpired(): bool
    {
        return $this->interaction_expires_at && 
               $this->interaction_expires_at->isPast();
    }

    /**
     * Marcar como expirada
     */
    public function markAsExpired(): void
    {
        $this->update(['status' => 'expired']);
    }

    /**
     * Retirar la interacción
     */
    public function withdraw(): void
    {
        $this->update(['status' => 'withdrawn']);
    }

    /**
     * Obtener peso de engagement según el tipo
     */
    public function getEngagementWeight(): int
    {
        return match ($this->interaction_type) {
            'like' => 1,
            'love' => 2,
            'wow' => 2,
            'celebrate' => 3,
            'support' => 3,
            'share' => 5,
            'bookmark' => 2,
            'follow' => 1,
            'subscribe' => 1,
            default => 0,
        };
    }

    /**
     * Verificar si es una interacción positiva
     */
    public function isPositive(): bool
    {
        return in_array($this->interaction_type, [
            'like', 'love', 'wow', 'celebrate', 'support', 'share', 'bookmark', 'follow', 'subscribe'
        ]);
    }

    /**
     * Verificar si es una interacción negativa
     */
    public function isNegative(): bool
    {
        return in_array($this->interaction_type, ['report', 'hide', 'block']);
    }

    /**
     * Obtener descripción legible de la interacción
     */
    public function getReadableDescription(): string
    {
        return match ($this->interaction_type) {
            'like' => 'Le gusta',
            'love' => 'Le encanta',
            'wow' => 'Le asombra',
            'celebrate' => 'Celebra',
            'support' => 'Apoya',
            'share' => 'Comparte',
            'bookmark' => 'Guarda en favoritos',
            'follow' => 'Sigue',
            'subscribe' => 'Se suscribe',
            'report' => 'Reporta',
            'hide' => 'Oculta',
            'block' => 'Bloquea',
            default => 'Interactúa con',
        };
    }

    /**
     * Obtener emoji representativo
     */
    public function getEmoji(): string
    {
        return match ($this->interaction_type) {
            'like' => '👍',
            'love' => '❤️',
            'wow' => '😮',
            'celebrate' => '🎉',
            'support' => '🤝',
            'share' => '📤',
            'bookmark' => '🔖',
            'follow' => '👀',
            'subscribe' => '🔔',
            'report' => '🚨',
            'hide' => '👁️‍🗨️',
            'block' => '🚫',
            default => '👆',
        };
    }

    // Eventos del modelo

    protected static function booted()
    {
        // Al crear una interacción, actualizar contadores del objeto interactuado
        static::created(function (SocialInteraction $interaction) {
            $interaction->updateInteractableCounters('increment');
        });

        // Al eliminar una interacción, actualizar contadores del objeto interactuado
        static::deleted(function (SocialInteraction $interaction) {
            $interaction->updateInteractableCounters('decrement');
        });

        // Al cambiar el estado, actualizar contadores si es necesario
        static::updated(function (SocialInteraction $interaction) {
            if ($interaction->isDirty('status')) {
                $original = $interaction->getOriginal('status');
                if ($original === 'active' && $interaction->status !== 'active') {
                    $interaction->updateInteractableCounters('decrement');
                } elseif ($original !== 'active' && $interaction->status === 'active') {
                    $interaction->updateInteractableCounters('increment');
                }
            }
        });
    }

    /**
     * Actualizar contadores del objeto interactuado
     */
    private function updateInteractableCounters(string $operation): void
    {
        if (!$this->interactable) {
            return;
        }

        $increment = $operation === 'increment' ? 1 : -1;
        
        // Solo contar interacciones activas y positivas
        if ($this->status !== 'active' || !$this->isPositive()) {
            return;
        }

        // Actualizar contadores específicos según el tipo
        $counters = [];
        switch ($this->interaction_type) {
            case 'like':
                $counters['likes_count'] = $increment;
                break;
            case 'love':
                $counters['loves_count'] = $increment;
                break;
            case 'wow':
                $counters['wow_count'] = $increment;
                break;
            case 'share':
                $counters['shares_count'] = $increment;
                break;
            case 'bookmark':
                $counters['bookmarks_count'] = $increment;
                break;
        }

        // Actualizar engagement score general
        $engagementIncrement = $this->getEngagementWeight() * $increment;
        $counters['engagement_score'] = DB::raw("engagement_score + {$engagementIncrement}");

        if (!empty($counters)) {
            $this->interactable->update($counters);
        }
    }
}
