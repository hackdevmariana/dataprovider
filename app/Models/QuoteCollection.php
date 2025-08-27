<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class QuoteCollection extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'created_by',
        'theme',
        'tags',
        'quotes_count',
        'is_public',
        'is_featured',
        'views_count',
        'likes_count',
    ];

    protected $casts = [
        'tags' => 'array',
        'quotes_count' => 'integer',
        'is_public' => 'boolean',
        'is_featured' => 'boolean',
        'views_count' => 'integer',
        'likes_count' => 'integer',
    ];

    // Relaciones
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function quotes(): BelongsToMany
    {
        return $this->belongsToMany(Quote::class, 'quote_collection_quotes');
    }

    // Atributos calculados
    public function getStatusLabelAttribute(): string
    {
        return $this->is_public ? 'Pública' : 'Privada';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->is_public ? 'success' : 'secondary';
    }

    public function getFeaturedLabelAttribute(): string
    {
        return $this->is_featured ? 'Destacada' : 'Normal';
    }

    public function getFeaturedColorAttribute(): string
    {
        return $this->is_featured ? 'warning' : 'gray';
    }

    public function getTagsCountAttribute(): int
    {
        if ($this->tags && is_array($this->tags)) {
            return count($this->tags);
        }
        return 0;
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCreator($query, int $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopeByTheme($query, string $theme)
    {
        return $query->where('theme', $theme);
    }

    public function scopePopular($query)
    {
        return $query->orderBy('views_count', 'desc');
    }

    public function scopeLiked($query)
    {
        return $query->orderBy('likes_count', 'desc');
    }

    // Métodos
    public function isPublic(): bool
    {
        return $this->is_public;
    }

    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    public function isOwnedBy(int $userId): bool
    {
        return $this->created_by === $userId;
    }

    public function canBeViewedBy(int $userId): bool
    {
        return $this->is_public || $this->isOwnedBy($userId);
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function incrementLikes(): void
    {
        $this->increment('likes_count');
    }

    public function decrementLikes(): void
    {
        $this->decrement('likes_count');
    }
}
