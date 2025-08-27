<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'author',
        'source',
        'language',
        'category',
        'tags',
        'mood',
        'difficulty_level',
        'word_count',
        'character_count',
        'popularity_score',
        'usage_count',
        'translations',
        'is_verified',
    ];

    protected $casts = [
        'tags' => 'array',
        'translations' => 'array',
        'popularity_score' => 'decimal:2',
        'word_count' => 'integer',
        'character_count' => 'integer',
        'usage_count' => 'integer',
        'is_verified' => 'boolean',
    ];

    // Relaciones
    public function category(): BelongsTo
    {
        return $this->belongsTo(QuoteCategory::class, 'category', 'name');
    }

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(QuoteCollection::class, 'quote_collection_quotes');
    }

    // Atributos calculados
    public function getDifficultyLabelAttribute(): string
    {
        return match ($this->difficulty_level) {
            'easy' => 'Fácil',
            'medium' => 'Medio',
            'hard' => 'Difícil',
            'expert' => 'Experto',
            default => 'Sin especificar',
        };
    }

    public function getDifficultyColorAttribute(): string
    {
        return match ($this->difficulty_level) {
            'easy' => 'success',
            'medium' => 'info',
            'hard' => 'warning',
            'expert' => 'danger',
            default => 'gray',
        };
    }

    public function getMoodLabelAttribute(): string
    {
        return match ($this->mood) {
            'inspiring' => 'Inspirador',
            'motivational' => 'Motivacional',
            'philosophical' => 'Filosófico',
            'humorous' => 'Humorístico',
            'romantic' => 'Romántico',
            'melancholic' => 'Melancólico',
            'energetic' => 'Energético',
            'calm' => 'Tranquilo',
            'mysterious' => 'Misterioso',
            'optimistic' => 'Optimista',
            'pessimistic' => 'Pesimista',
            default => 'Sin especificar',
        };
    }

    public function getMoodColorAttribute(): string
    {
        return match ($this->mood) {
            'inspiring' => 'success',
            'motivational' => 'warning',
            'philosophical' => 'info',
            'humorous' => 'light',
            'romantic' => 'danger',
            'melancholic' => 'secondary',
            'energetic' => 'warning',
            'calm' => 'success',
            'mysterious' => 'dark',
            'optimistic' => 'success',
            'pessimistic' => 'danger',
            default => 'gray',
        };
    }

    public function getPopularityLabelAttribute(): string
    {
        if ($this->popularity_score >= 0.8) {
            return 'Muy Popular';
        } elseif ($this->popularity_score >= 0.6) {
            return 'Popular';
        } elseif ($this->popularity_score >= 0.4) {
            return 'Moderado';
        } else {
            return 'Poco Popular';
        }
    }

    public function getPopularityColorAttribute(): string
    {
        if ($this->popularity_score >= 0.8) {
            return 'danger';
        } elseif ($this->popularity_score >= 0.6) {
            return 'warning';
        } elseif ($this->popularity_score >= 0.4) {
            return 'info';
        } else {
            return 'secondary';
        }
    }

    public function getExcerptAttribute(): string
    {
        return \Str::limit($this->text, 100);
    }

    public function getShortTextAttribute(): string
    {
        return \Str::limit($this->text, 50);
    }

    public function getReadingTimeAttribute(): int
    {
        // Tiempo de lectura estimado: 200 palabras por minuto
        return max(1, ceil($this->word_count / 200));
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

    public function getTagsCountAttribute(): int
    {
        if (is_array($this->tags)) {
            return count($this->tags);
        }
        return 0;
    }

    public function getTranslationsCountAttribute(): int
    {
        if (is_array($this->translations)) {
            return count($this->translations);
        }
        return 0;
    }

    // Scopes
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByLanguage($query, string $language)
    {
        return $query->where('language', $language);
    }

    public function scopeByMood($query, string $mood)
    {
        return $query->where('mood', $mood);
    }

    public function scopeByDifficulty($query, string $level)
    {
        return $query->where('difficulty_level', $level);
    }

    public function scopePopular($query, float $minScore = 0.6)
    {
        return $query->where('popularity_score', '>=', $minScore);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByAuthor($query, string $author)
    {
        return $query->where('author', 'like', '%' . $author . '%');
    }

    public function scopeShort($query, int $maxWords = 20)
    {
        return $query->where('word_count', '<=', $maxWords);
    }

    public function scopeLong($query, int $minWords = 50)
    {
        return $query->where('word_count', '>=', $minWords);
    }

    public function scopeByUsage($query, int $minUsage = 10)
    {
        return $query->where('usage_count', '>=', $minUsage);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('text', 'like', '%' . $search . '%')
              ->orWhere('author', 'like', '%' . $search . '%')
              ->orWhere('source', 'like', '%' . $search . '%');
        });
    }

    // Métodos
    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    public function isPopular(): bool
    {
        return $this->popularity_score >= 0.6;
    }

    public function isShort(): bool
    {
        return $this->word_count <= 20;
    }

    public function isLong(): bool
    {
        return $this->word_count >= 50;
    }

    public function hasAuthor(): bool
    {
        return !empty($this->author);
    }

    public function hasSource(): bool
    {
        return !empty($this->source);
    }

    public function hasTags(): bool
    {
        return $this->tags_count > 0;
    }

    public function hasTranslations(): bool
    {
        return $this->translations_count > 0;
    }

    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    public function getTagsList(): array
    {
        if (is_array($this->tags)) {
            return $this->tags;
        }
        return [];
    }

    public function getTranslationsList(): array
    {
        if (is_array($this->translations)) {
            return $this->translations;
        }
        return [];
    }
}
