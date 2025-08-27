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
        'pages' => 'integer',
        'rating' => 'decimal:2',
        'ratings_count' => 'integer',
        'reviews_count' => 'integer',
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
    public function getFormattedPublicationDateAttribute(): string
    {
        return $this->publication_date ? $this->publication_date->format('d/m/Y') : 'Sin fecha';
    }

    public function getLanguageLabelAttribute(): string
    {
        return match ($this->language) {
            'es' => 'Español',
            'en' => 'Inglés',
            'fr' => 'Francés',
            'de' => 'Alemán',
            'it' => 'Italiano',
            'pt' => 'Portugués',
            'ca' => 'Catalán',
            'eu' => 'Euskera',
            'gl' => 'Gallego',
            'la' => 'Latín',
            'gr' => 'Griego',
            'ar' => 'Árabe',
            'zh' => 'Chino',
            'ja' => 'Japonés',
            'ko' => 'Coreano',
            'ru' => 'Ruso',
            default => 'Desconocido',
        };
    }

    public function getGenreLabelAttribute(): string
    {
        return match ($this->genre) {
            'fiction' => 'Ficción',
            'non_fiction' => 'No Ficción',
            'mystery' => 'Misterio',
            'romance' => 'Romance',
            'sci_fi' => 'Ciencia Ficción',
            'fantasy' => 'Fantasía',
            'thriller' => 'Thriller',
            'horror' => 'Terror',
            'biography' => 'Biografía',
            'history' => 'Historia',
            'philosophy' => 'Filosofía',
            'science' => 'Ciencia',
            'poetry' => 'Poesía',
            'drama' => 'Drama',
            'comedy' => 'Comedia',
            'adventure' => 'Aventura',
            'crime' => 'Crimen',
            'war' => 'Guerra',
            'western' => 'Western',
            'children' => 'Infantil',
            'young_adult' => 'Juvenil',
            'classic' => 'Clásico',
            'contemporary' => 'Contemporáneo',
            default => 'Otro',
        };
    }

    public function getFormatLabelAttribute(): string
    {
        return match ($this->format) {
            'paperback' => 'Tapa Blanda',
            'hardcover' => 'Tapa Dura',
            'ebook' => 'E-book',
            'audiobook' => 'Audiolibro',
            'pdf' => 'PDF',
            'epub' => 'EPUB',
            'mobi' => 'MOBI',
            'audio_cd' => 'CD de Audio',
            'mp3' => 'MP3',
            'streaming' => 'Streaming',
            default => 'Desconocido',
        };
    }

    public function getRatingLabelAttribute(): string
    {
        if (!$this->rating) {
            return 'Sin calificación';
        }

        if ($this->rating >= 4.5) {
            return 'Excelente';
        } elseif ($this->rating >= 4.0) {
            return 'Muy Bueno';
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

    public function getTagsCountAttribute(): int
    {
        if ($this->tags && is_array($this->tags)) {
            return count($this->tags);
        }
        return 0;
    }

    public function getAwardsCountAttribute(): int
    {
        if ($this->awards && is_array($this->awards)) {
            return count($this->awards);
        }
        return 0;
    }

    public function getEditionsCountAttribute(): int
    {
        return $this->editions()->count();
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    public function getExcerptAttribute(): string
    {
        return \Str::limit($this->synopsis, 150);
    }

    public function getShortTitleAttribute(): string
    {
        return \Str::limit($this->title, 50);
    }

    // Scopes
    public function scopeByGenre($query, string $genre)
    {
        return $query->where('genre', $genre);
    }

    public function scopeByLanguage($query, string $language)
    {
        return $query->where('language', $language);
    }

    public function scopeByAuthor($query, string $author)
    {
        return $query->where('author', 'like', '%' . $author . '%');
    }

    public function scopeByPublisher($query, string $publisher)
    {
        return $query->where('publisher', 'like', '%' . $publisher . '%');
    }

    public function scopeByRating($query, float $minRating)
    {
        return $query->where('rating', '>=', $minRating);
    }

    public function scopeHighRated($query, float $minRating = 4.0)
    {
        return $query->where('rating', '>=', $minRating);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeByPublicationYear($query, int $year)
    {
        return $query->whereYear('publication_date', $year);
    }

    public function scopeRecent($query, int $years = 10)
    {
        return $query->where('publication_date', '>=', now()->subYears($years));
    }

    public function scopeClassic($query, int $years = 50)
    {
        return $query->where('publication_date', '<=', now()->subYears($years));
    }

    public function scopeByPages($query, int $min, int $max = null)
    {
        if ($max) {
            return $query->whereBetween('pages', [$min, $max]);
        }
        return $query->where('pages', '>=', $min);
    }

    public function scopeShort($query, int $maxPages = 200)
    {
        return $query->where('pages', '<=', $maxPages);
    }

    public function scopeLong($query, int $minPages = 500)
    {
        return $query->where('pages', '>=', $minPages);
    }

    public function scopeWithAwards($query)
    {
        return $query->whereJsonLength('awards', '>', 0);
    }

    public function scopeWithTags($query, array $tags)
    {
        return $query->whereJsonContains('tags', $tags);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', '%' . $search . '%')
              ->orWhere('author', 'like', '%' . $search . '%')
              ->orWhere('synopsis', 'like', '%' . $search . '%')
              ->orWhere('publisher', 'like', '%' . $search . '%');
        });
    }

    // Métodos
    public function isAvailable(): bool
    {
        return $this->is_available;
    }

    public function isHighRated(): bool
    {
        return $this->rating && $this->rating >= 4.0;
    }

    public function isClassic(): bool
    {
        return $this->publication_date && $this->publication_date->diffInYears(now()) >= 50;
    }

    public function isRecent(): bool
    {
        return $this->publication_date && $this->publication_date->diffInYears(now()) <= 10;
    }

    public function isShort(): bool
    {
        return $this->pages && $this->pages <= 200;
    }

    public function isLong(): bool
    {
        return $this->pages && $this->pages >= 500;
    }

    public function hasAwards(): bool
    {
        return $this->awards_count > 0;
    }

    public function hasTags(): bool
    {
        return $this->tags_count > 0;
    }

    public function hasEditions(): bool
    {
        return $this->editions_count > 0;
    }

    public function hasReviews(): bool
    {
        return $this->reviews_count > 0;
    }

    public function getAverageRating(): float
    {
        return $this->rating ?? 0;
    }

    public function getPublicationAge(): int
    {
        if (!$this->publication_date) {
            return 0;
        }
        return $this->publication_date->diffInYears(now());
    }

    public function getPublicationAgeLabel(): string
    {
        $age = $this->publication_age;
        
        if ($age === 0) {
            return 'Este año';
        } elseif ($age === 1) {
            return 'Hace 1 año';
        } elseif ($age < 10) {
            return 'Hace ' . $age . ' años';
        } elseif ($age < 100) {
            return 'Hace ' . round($age / 10) * 10 . ' años';
        } else {
            return 'Hace ' . round($age / 100) * 100 . ' años';
        }
    }

    public function getReadingTime(): int
    {
        // Tiempo de lectura estimado: 200 palabras por página
        if (!$this->pages) {
            return 0;
        }
        return max(1, ceil($this->pages * 200 / 200)); // 200 palabras por minuto
    }

    public function getFormattedReadingTime(): string
    {
        $minutes = $this->reading_time;
        if ($minutes < 60) {
            return $minutes . ' min';
        } else {
            $hours = floor($minutes / 60);
            $remainingMinutes = $minutes % 60;
            if ($remainingMinutes === 0) {
                return $hours . 'h';
            } else {
                return $hours . 'h ' . $remainingMinutes . 'm';
            }
        }
    }
}
