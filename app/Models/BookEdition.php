<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookEdition extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'edition_number',
        'format',
        'publisher',
        'publication_date',
        'isbn',
        'pages',
        'cover_type',
        'price',
        'currency',
        'special_features',
        'translator',
        'illustrator',
        'is_limited',
        'print_run',
    ];

    protected $casts = [
        'publication_date' => 'date',
        'pages' => 'integer',
        'price' => 'decimal:2',
        'special_features' => 'array',
        'print_run' => 'integer',
        'is_limited' => 'boolean',
    ];

    // Relaciones
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    // Atributos calculados
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

    public function getFormatColorAttribute(): string
    {
        return match ($this->format) {
            'paperback' => 'success',
            'hardcover' => 'warning',
            'ebook' => 'info',
            'audiobook' => 'primary',
            'pdf' => 'danger',
            'epub' => 'secondary',
            'mobi' => 'dark',
            'audio_cd' => 'light',
            'mp3' => 'gray',
            'streaming' => 'info',
            default => 'gray',
        };
    }

    public function getCoverTypeLabelAttribute(): string
    {
        return match ($this->cover_type) {
            'soft' => 'Blanda',
            'hard' => 'Dura',
            'leather' => 'Cuero',
            'cloth' => 'Tela',
            'plastic' => 'Plástico',
            'metal' => 'Metal',
            default => 'Sin especificar',
        };
    }

    public function getFormattedPriceAttribute(): string
    {
        if (!$this->price) {
            return 'Sin precio';
        }
        return number_format($this->price, 2) . ' ' . ($this->currency ?? 'EUR');
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
        } else {
            return 'Hace ' . round($age / 10) . ' décadas';
        }
    }

    public function getFormattedPublicationDateAttribute(): string
    {
        return $this->publication_date ? $this->publication_date->format('d/m/Y') : 'Sin fecha';
    }

    public function getSpecialFeaturesCountAttribute(): int
    {
        if (is_array($this->special_features)) {
            return count($this->special_features);
        }
        return 0;
    }

    public function getIsbnFormattedAttribute(): string
    {
        if (!$this->isbn) {
            return 'Sin ISBN';
        }
        return $this->isbn;
    }

    // Scopes
    public function scopeByFormat($query, string $format)
    {
        return $query->where('format', $format);
    }

    public function scopeByPublisher($query, string $publisher)
    {
        return $query->where('publisher', 'like', '%' . $publisher . '%');
    }

    public function scopeByPublicationYear($query, int $year)
    {
        return $query->whereYear('publication_date', $year);
    }

    public function scopeByPriceRange($query, float $min, float $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    public function scopeByPages($query, int $min, int $max = null)
    {
        if ($max) {
            return $query->whereBetween('pages', [$min, $max]);
        }
        return $query->where('pages', '>=', $min);
    }

    public function scopeLimited($query)
    {
        return $query->where('is_limited', true);
    }

    public function scopeByCoverType($query, string $coverType)
    {
        return $query->where('cover_type', $coverType);
    }

    public function scopeWithSpecialFeatures($query)
    {
        return $query->whereNotNull('special_features')->where('special_features', '!=', '[]');
    }

    public function scopeByTranslator($query, string $translator)
    {
        return $query->where('translator', 'like', '%' . $translator . '%');
    }

    public function scopeByIllustrator($query, string $illustrator)
    {
        return $query->where('illustrator', 'like', '%' . $illustrator . '%');
    }

    // Métodos
    public function isLimited(): bool
    {
        return $this->is_limited;
    }

    public function isDigital(): bool
    {
        return in_array($this->format, ['ebook', 'pdf', 'epub', 'mobi', 'mp3', 'streaming']);
    }

    public function isPhysical(): bool
    {
        return in_array($this->format, ['paperback', 'hardcover', 'audio_cd']);
    }

    public function isAudio(): bool
    {
        return in_array($this->format, ['audiobook', 'audio_cd', 'mp3']);
    }

    public function hasPrice(): bool
    {
        return !is_null($this->price) && $this->price > 0;
    }

    public function hasSpecialFeatures(): bool
    {
        return $this->special_features_count > 0;
    }

    public function hasTranslator(): bool
    {
        return !empty($this->translator);
    }

    public function hasIllustrator(): bool
    {
        return !empty($this->illustrator);
    }

    public function hasIsbn(): bool
    {
        return !empty($this->isbn);
    }

    public function isRecent(): bool
    {
        return $this->age < 5;
    }

    public function isClassic(): bool
    {
        return $this->age >= 25;
    }

    public function getSpecialFeaturesList(): array
    {
        if (is_array($this->special_features)) {
            return $this->special_features;
        }
        return [];
    }

    public function getReadingTimeAttribute(): int
    {
        // Tiempo de lectura estimado: 200 palabras por página
        if (!$this->pages) {
            return 0;
        }
        return max(1, ceil($this->pages * 200 / 200)); // 200 palabras por minuto
    }

    public function getFormattedReadingTimeAttribute(): string
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
