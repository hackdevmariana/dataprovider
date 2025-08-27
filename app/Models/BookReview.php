<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'reviewer_name',
        'rating',
        'review_text',
        'review_date',
        'source',
        'url',
        'is_verified',
        'helpful_votes',
        'total_votes',
        'language',
        'sentiment',
        'tags',
        'is_featured',
    ];

    protected $casts = [
        'review_date' => 'date',
        'rating' => 'decimal:1',
        'helpful_votes' => 'integer',
        'total_votes' => 'integer',
        'is_verified' => 'boolean',
        'tags' => 'array',
        'is_featured' => 'boolean',
    ];

    // Relaciones
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    // Atributos calculados
    public function getRatingLabelAttribute(): string
    {
        if ($this->rating >= 4.5) {
            return 'Excelente';
        } elseif ($this->rating >= 4.0) {
            return 'Muy Bueno';
        } elseif ($this->rating >= 3.5) {
            return 'Bueno';
        } elseif ($this->rating >= 3.0) {
            return 'Regular';
        } elseif ($this->rating >= 2.0) {
            return 'Malo';
        } else {
            return 'Muy Malo';
        }
    }

    public function getRatingColorAttribute(): string
    {
        if ($this->rating >= 4.5) {
            return 'success';
        } elseif ($this->rating >= 4.0) {
            return 'info';
        } elseif ($this->rating >= 3.5) {
            return 'warning';
        } elseif ($this->rating >= 3.0) {
            return 'secondary';
        } else {
            return 'danger';
        }
    }

    public function getSentimentLabelAttribute(): string
    {
        return match ($this->sentiment) {
            'positive' => 'Positivo',
            'neutral' => 'Neutral',
            'negative' => 'Negativo',
            'mixed' => 'Mixto',
            default => 'Sin especificar',
        };
    }

    public function getSentimentColorAttribute(): string
    {
        return match ($this->sentiment) {
            'positive' => 'success',
            'neutral' => 'info',
            'negative' => 'danger',
            'mixed' => 'warning',
            default => 'gray',
        };
    }

    public function getFormattedRatingAttribute(): string
    {
        return number_format($this->rating, 1) . '/5.0';
    }

    public function getFormattedReviewDateAttribute(): string
    {
        return $this->review_date ? $this->review_date->format('d/m/Y') : 'Sin fecha';
    }

    public function getHelpfulPercentageAttribute(): float
    {
        if ($this->total_votes === 0) {
            return 0;
        }
        return round(($this->helpful_votes / $this->total_votes) * 100, 1);
    }

    public function getHelpfulPercentageLabelAttribute(): string
    {
        $percentage = $this->helpful_percentage;
        if ($percentage >= 90) {
            return 'Muy Útil';
        } elseif ($percentage >= 75) {
            return 'Útil';
        } elseif ($percentage >= 50) {
            return 'Moderadamente Útil';
        } else {
            return 'Poco Útil';
        }
    }

    public function getHelpfulPercentageColorAttribute(): string
    {
        $percentage = $this->helpful_percentage;
        if ($percentage >= 90) {
            return 'success';
        } elseif ($percentage >= 75) {
            return 'info';
        } elseif ($percentage >= 50) {
            return 'warning';
        } else {
            return 'danger';
        }
    }

    public function getExcerptAttribute(): string
    {
        return \Str::limit($this->review_text, 150);
    }

    public function getShortReviewAttribute(): string
    {
        return \Str::limit($this->review_text, 100);
    }

    public function getTagsCountAttribute(): int
    {
        if (is_array($this->tags)) {
            return count($this->tags);
        }
        return 0;
    }

    public function getIsRecentAttribute(): bool
    {
        if (!$this->review_date) {
            return false;
        }
        return $this->review_date->diffInDays(now()) <= 30;
    }

    public function getIsOldAttribute(): bool
    {
        if (!$this->review_date) {
            return false;
        }
        return $this->review_date->diffInDays(now()) >= 365;
    }

    // Scopes
    public function scopeByRating($query, float $minRating)
    {
        return $query->where('rating', '>=', $minRating);
    }

    public function scopeHighRating($query, float $minRating = 4.0)
    {
        return $query->where('rating', '>=', $minRating);
    }

    public function scopeLowRating($query, float $maxRating = 2.5)
    {
        return $query->where('rating', '<=', $maxRating);
    }

    public function scopeBySentiment($query, string $sentiment)
    {
        return $query->where('sentiment', $sentiment);
    }

    public function scopePositive($query)
    {
        return $query->where('sentiment', 'positive');
    }

    public function scopeNegative($query)
    {
        return $query->where('sentiment', 'negative');
    }

    public function scopeByLanguage($query, string $language)
    {
        return $query->where('language', $language);
    }

    public function scopeBySource($query, string $source)
    {
        return $query->where('source', $source);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByReviewer($query, string $reviewer)
    {
        return $query->where('reviewer_name', 'like', '%' . $reviewer . '%');
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('review_date', $date);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('review_date', '>=', now()->subDays($days));
    }

    public function scopeHelpful($query, float $minPercentage = 75)
    {
        return $query->whereRaw('(helpful_votes / NULLIF(total_votes, 0)) * 100 >= ?', [$minPercentage]);
    }

    public function scopeOrderByRating($query, string $direction = 'desc')
    {
        return $query->orderBy('rating', $direction);
    }

    public function scopeOrderByDate($query, string $direction = 'desc')
    {
        return $query->orderBy('review_date', $direction);
    }

    public function scopeOrderByHelpful($query)
    {
        return $query->orderByRaw('(helpful_votes / NULLIF(total_votes, 0)) DESC');
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('review_text', 'like', '%' . $search . '%')
              ->orWhere('reviewer_name', 'like', '%' . $search . '%')
              ->orWhere('source', 'like', '%' . $search . '%');
        });
    }

    // Métodos
    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    public function isHighRated(): bool
    {
        return $this->rating >= 4.0;
    }

    public function isLowRated(): bool
    {
        return $this->rating <= 2.5;
    }

    public function isPositive(): bool
    {
        return $this->sentiment === 'positive';
    }

    public function isNegative(): bool
    {
        return $this->sentiment === 'negative';
    }

    public function isNeutral(): bool
    {
        return $this->sentiment === 'neutral';
    }

    public function isRecent(): bool
    {
        return $this->is_recent;
    }

    public function isOld(): bool
    {
        return $this->is_old;
    }

    public function hasTags(): bool
    {
        return $this->tags_count > 0;
    }

    public function hasUrl(): bool
    {
        return !empty($this->url);
    }

    public function hasVotes(): bool
    {
        return $this->total_votes > 0;
    }

    public function isHelpful(): bool
    {
        return $this->helpful_percentage >= 75;
    }

    public function incrementHelpfulVotes(): void
    {
        $this->increment('helpful_votes');
        $this->increment('total_votes');
    }

    public function incrementTotalVotes(): void
    {
        $this->increment('total_votes');
    }

    public function getTagsList(): array
    {
        if (is_array($this->tags)) {
            return $this->tags;
        }
        return [];
    }

    public function getStarRatingAttribute(): string
    {
        $rating = $this->rating;
        $fullStars = floor($rating);
        $halfStar = ($rating - $fullStars) >= 0.5;
        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

        $stars = str_repeat('★', $fullStars);
        if ($halfStar) {
            $stars .= '☆';
        }
        $stars .= str_repeat('☆', $emptyStars);

        return $stars;
    }

    public function getRatingDescriptionAttribute(): string
    {
        $rating = $this->rating;
        if ($rating >= 4.5) {
            return 'Una obra maestra que supera todas las expectativas';
        } elseif ($rating >= 4.0) {
            return 'Un libro excelente que recomendaría sin dudar';
        } elseif ($rating >= 3.5) {
            return 'Un buen libro con algunas virtudes destacables';
        } elseif ($rating >= 3.0) {
            return 'Un libro aceptable pero con limitaciones';
        } elseif ($rating >= 2.0) {
            return 'Un libro decepcionante con pocos méritos';
        } else {
            return 'Un libro muy pobre que no recomendaría';
        }
    }
}
