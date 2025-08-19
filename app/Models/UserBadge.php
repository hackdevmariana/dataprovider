<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBadge extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'badge_type',
        'category',
        'name',
        'description',
        'icon_url',
        'color',
        'criteria',
        'metadata',
        'points_awarded',
        'is_public',
        'is_featured',
        'earned_at',
        'expires_at',
    ];

    protected $casts = [
        'criteria' => 'array',
        'metadata' => 'array',
        'is_public' => 'boolean',
        'is_featured' => 'boolean',
        'earned_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected $dates = [
        'earned_at',
        'expires_at',
    ];

    /**
     * Get the user that owns the badge
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if badge is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if badge is still valid
     */
    public function isValid(): bool
    {
        return !$this->isExpired();
    }

    /**
     * Get badge type label
     */
    public function getBadgeTypeLabel(): string
    {
        return match ($this->badge_type) {
            'bronze' => 'Bronce',
            'silver' => 'Plata',
            'gold' => 'Oro',
            'platinum' => 'Platino',
            'diamond' => 'Diamante',
            default => ucfirst($this->badge_type),
        };
    }

    /**
     * Get category label
     */
    public function getCategoryLabel(): string
    {
        return match ($this->category) {
            'energy_saver' => 'Ahorrador de Energía',
            'community_leader' => 'Líder Comunitario',
            'expert_contributor' => 'Contribuidor Experto',
            'project_creator' => 'Creador de Proyectos',
            'helpful_member' => 'Miembro Útil',
            'early_adopter' => 'Adoptador Temprano',
            'sustainability_champion' => 'Campeón de Sostenibilidad',
            default => ucwords(str_replace('_', ' ', $this->category)),
        };
    }

    /**
     * Get badge rarity based on type
     */
    public function getRarity(): string
    {
        return match ($this->badge_type) {
            'bronze' => 'common',
            'silver' => 'uncommon',
            'gold' => 'rare',
            'platinum' => 'epic',
            'diamond' => 'legendary',
            default => 'common',
        };
    }

    /**
     * Scope for public badges
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for featured badges
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for valid (non-expired) badges
     */
    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope by badge type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('badge_type', $type);
    }
}