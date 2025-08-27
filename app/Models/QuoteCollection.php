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
        return $this->belongsToMany(Quote::class, 'quote_collection_quotes')
                    ->withTimestamps();
    }

    // Atributos calculados
    public function getStatusLabelAttribute(): string
    {
        if (!$this->is_public) {
            return 'Privada';
        }
        return $this->is_featured ? 'Destacada' : 'Pública';
    }

    public function getStatusColorAttribute(): string
    {
        if (!$this->is_public) {
            return 'secondary';
        }
        return $this->is_featured ? 'warning' : 'success';
    }

    public function getPopularityScoreAttribute(): float
    {
        if ($this->views_count === 0) {
            return 0;
        }
        
        // Fórmula: (likes * 2 + views) / (views * 0.1)
        $score = ($this->likes_count * 2 + $this->views_count) / ($this->views_count * 0.1);
        return min(1.0, max(0.0, $score));
    }

    public function getPopularityLabelAttribute(): string
    {
        $score = $this->popularity_score;
        if ($score >= 0.8) {
            return 'Muy Popular';
        } elseif ($score >= 0.6) {
            return 'Popular';
        } elseif ($score >= 0.4) {
            return 'Moderado';
        } else {
            return 'Poco Popular';
        }
    }

    public function getPopularityColorAttribute(): string
    {
        $score = $this->popularity_score;
        if ($score >= 0.8) {
            return 'success';
        } elseif ($score >= 0.6) {
            return 'info';
        } elseif ($score >= 0.4) {
            return 'warning';
        } else {
            return 'secondary';
        }
    }

    public function getTagsCountAttribute(): int
    {
        if (is_array($this->tags)) {
            return count($this->tags);
        }
        return 0;
    }

    public function getFormattedViewsCountAttribute(): string
    {
        if ($this->views_count >= 1000000) {
            return round($this->views_count / 1000000, 1) . 'M';
        } elseif ($this->views_count >= 1000) {
            return round($this->views_count / 1000, 1) . 'K';
        }
        return number_format($this->views_count);
    }

    public function getFormattedLikesCountAttribute(): string
    {
        if ($this->likes_count >= 1000000) {
            return round($this->likes_count / 1000000, 1) . 'M';
        } elseif ($this->likes_count >= 1000) {
            return round($this->likes_count / 1000, 1) . 'K';
        }
        return number_format($this->likes_count);
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
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

    public function scopePopular($query, float $minScore = 0.6)
    {
        return $query->whereRaw('(likes_count * 2 + views_count) / (views_count * 0.1) >= ?', [$minScore]);
    }

    public function scopeByViews($query, int $minViews)
    {
        return $query->where('views_count', '>=', $minViews);
    }

    public function scopeByLikes($query, int $minLikes)
    {
        return $query->where('likes_count', '>=', $minLikes);
    }

    public function scopeOrderByPopularity($query)
    {
        return $query->orderByRaw('(likes_count * 2 + views_count) / (views_count * 0.1) DESC');
    }

    public function scopeOrderByViews($query)
    {
        return $query->orderBy('views_count', 'desc');
    }

    public function scopeOrderByLikes($query)
    {
        return $query->orderBy('likes_count', 'desc');
    }

    public function scopeOrderByQuotesCount($query)
    {
        return $query->orderBy('quotes_count', 'desc');
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%')
              ->orWhere('theme', 'like', '%' . $search . '%');
        });
    }

    // Métodos
    public function isPublic(): bool
    {
        return $this->is_public;
    }

    public function isPrivate(): bool
    {
        return !$this->is_public;
    }

    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    public function isPopular(): bool
    {
        return $this->popularity_score >= 0.6;
    }

    public function hasQuotes(): bool
    {
        return $this->quotes_count > 0;
    }

    public function hasTags(): bool
    {
        return $this->tags_count > 0;
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

    public function addQuote(Quote $quote): void
    {
        $this->quotes()->attach($quote->id);
        $this->increment('quotes_count');
    }

    public function removeQuote(Quote $quote): void
    {
        $this->quotes()->detach($quote->id);
        $this->decrement('quotes_count');
    }

    public function updateQuotesCount(): void
    {
        $this->quotes_count = $this->quotes()->count();
        $this->save();
    }

    public function getTagsList(): array
    {
        if (is_array($this->tags)) {
            return $this->tags;
        }
        return [];
    }

    public function getRandomQuote()
    {
        return $this->quotes()->inRandomOrder()->first();
    }

    public function getTopQuotes(int $limit = 5)
    {
        return $this->quotes()
                   ->orderBy('popularity_score', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public function getQuotesByMood(string $mood, int $limit = 10)
    {
        return $this->quotes()
                   ->where('mood', $mood)
                   ->orderBy('popularity_score', 'desc')
                   ->limit($limit)
                   ->get();
    }
}
