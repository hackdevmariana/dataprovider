<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'original_title',
        'synopsis',
        'author',
        'isbn',
        'publisher',
        'publication_date',
        'language',
        'genre',
        'pages',
        'format',
        'rating',
        'ratings_count',
        'reviews_count',
        'awards',
        'tags',
        'cover_image',
        'is_available',
    ];

    protected $casts = [
        'publication_date' => 'date',
        'rating' => 'decimal:2',
        'ratings_count' => 'integer',
        'reviews_count' => 'integer',
        'pages' => 'integer',
        'awards' => 'array',
        'tags' => 'array',
        'is_available' => 'boolean',
    ];

    // Relaciones
    public function editions(): HasMany
    {
        return $this->hasMany(BookEdition::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(BookReview::class);
    }

    // Atributos calculados
    public function getRatingLabelAttribute(): string
    {
        if (!$this->rating) {
            return 'Sin valoraciones';
        }

        if ($this->rating >= 4.5) {
            return 'Excelente';
        } elseif ($this->rating >= 4.0) {
            return 'Muy bueno';
        } elseif ($this->rating >= 3.5) {
            return 'Bueno';
        } elseif ($this->rating >= 3.0) {
            return 'Regular';
        } else {
            return 'Pobre';
        }
    }

    public function getRatingColorAttribute(): string
    {
        if (!$this->rating) {
            return 'gray';
        }

        if ($this->rating >= 4.5) {
            return 'success';
        } elseif ($this->rating >= 4.0) {
            return 'info';
        } elseif ($this->rating >= 3.5) {
            return 'warning';
        } else {
            return 'danger';
        }
    }

    public function getPublicationYearAttribute(): ?int
    {
        return $this->publication_date ? $this->publication_date->year : null;
    }

    public function getAgeAttribute(): ?int
    {
        if (!$this->publication_date) {
            return null;
        }
        return $this->publication_date->diffInYears(now());
    }

    public function getAgeLabelAttribute(): string
    {
        $age = $this->age;
        if (!$age) {
            return 'Sin fecha';
        }

        if ($age < 1) {
            return 'Este año';
        } elseif ($age < 10) {
            return 'Hace ' . $age . ' años';
        } elseif ($age < 50) {
            return 'Hace ' . $age . ' años';
        } elseif ($age < 100) {
            return 'Hace ' . round($age / 10) . ' décadas';
        } else {
            return 'Hace ' . round($age / 100, 1) . ' siglos';
        }
    }

    public function getFormattedPublicationDateAttribute(): string
    {
        return $this->publication_date ? $this->publication_date->format('d/m/Y') : 'Sin fecha';
    }

    public function getTagsCountAttribute(): int
    {
        if (is_array($this->tags)) {
            return count($this->tags);
        }
        return 0;
    }

    public function getAwardsCountAttribute(): int
    {
        if (is_array($this->awards)) {
            return count($this->awards);
        }
        return 0;
    }

    public function getEditionsCountAttribute(): int
    {
        return $this->editions()->count();
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeByGenre($query, string $genre)
    {
        return $query->where('genre', $genre);
    }

    public function scopeByAuthor($query, string $author)
    {
        return $query->where('author', 'like', '%' . $author . '%');
    }

    public function scopeByLanguage($query, string $language)
    {
        return $query->where('language', $language);
    }

    public function scopeByFormat($query, string $format)
    {
        return $query->where('format', $format);
    }

    public function scopeHighRated($query, float $minRating = 4.0)
    {
        return $query->where('rating', '>=', $minRating);
    }

    public function scopePopular($query, int $minReviews = 10)
    {
        return $query->where('reviews_count', '>=', $minReviews);
    }

    public function scopeRecent($query, int $years = 10)
    {
        return $query->where('publication_date', '>=', now()->subYears($years));
    }

    public function scopeClassic($query, int $years = 50)
    {
        return $query->where('publication_date', '<=', now()->subYears($years));
    }

    public function scopeByPublicationYear($query, int $year)
    {
        return $query->whereYear('publication_date', $year);
    }

    public function scopeByDecade($query, int $decade)
    {
        $startYear = $decade * 10;
        $endYear = $startYear + 9;
        return $query->whereBetween('publication_date', [
            now()->setYear($startYear)->startOfYear(),
            now()->setYear($endYear)->endOfYear()
        ]);
    }

    // Métodos
    public function isAvailable(): bool
    {
        return $this->is_available;
    }

    public function isHighRated(): bool
    {
        return $this->rating >= 4.0;
    }

    public function isPopular(): bool
    {
        return $this->reviews_count >= 10;
    }

    public function isRecent(): bool
    {
        return $this->age < 10;
    }

    public function isClassic(): bool
    {
        return $this->age >= 50;
    }

    public function hasEditions(): bool
    {
        return $this->editions_count > 0;
    }

    public function hasReviews(): bool
    {
        return $this->reviews_count > 0;
    }

    public function hasTags(): bool
    {
        return $this->tags_count > 0;
    }

    public function hasAwards(): bool
    {
        return $this->awards_count > 0;
    }

    public function getAverageRating(): float
    {
        return $this->rating ?? 0.0;
    }

    public function getReviewsAverage(): float
    {
        if ($this->reviews_count === 0) {
            return 0.0;
        }
        return $this->reviews()->avg('rating') ?? 0.0;
    }

    public function getTagsList(): array
    {
        if (is_array($this->tags)) {
            return $this->tags;
        }
        return [];
    }

    public function getAwardsList(): array
    {
        if (is_array($this->awards)) {
            return $this->awards;
        }
        return [];
    }
}
