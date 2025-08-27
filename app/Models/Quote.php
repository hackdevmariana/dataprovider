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
        return $this->belongsTo(QuoteCategory::class, 'category');
    }

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(QuoteCollection::class, 'quote_collection_quotes');
    }

    // Atributos calculados
    public function getCategoryLabelAttribute(): string
    {
        if ($this->category) {
            return $this->category->name;
        }
        return 'Sin categoría';
    }

    public function getMoodLabelAttribute(): string
    {
        return match ($this->mood) {
            'inspirational' => 'Inspiradora',
            'motivational' => 'Motivadora',
            'philosophical' => 'Filosófica',
            'humorous' => 'Humorística',
            'romantic' => 'Romántica',
            'melancholic' => 'Melancólica',
            'energetic' => 'Energética',
            'calm' => 'Tranquila',
            'mysterious' => 'Misteriosa',
            'dramatic' => 'Dramática',
            'peaceful' => 'Pacífica',
            'adventurous' => 'Aventurera',
            'wise' => 'Sabia',
            'challenging' => 'Desafiante',
            'comforting' => 'Consoladora',
            default => 'Sin especificar',
        };
    }

    public function getMoodIconAttribute(): string
    {
        return match ($this->mood) {
            'inspirational' => '✨',
            'motivational' => '🚀',
            'philosophical' => '🤔',
            'humorous' => '😄',
            'romantic' => '💕',
            'melancholic' => '😔',
            'energetic' => '⚡',
            'calm' => '😌',
            'mysterious' => '🔮',
            'dramatic' => '🎭',
            'peaceful' => '🕊️',
            'adventurous' => '🗺️',
            'wise' => '🧠',
            'challenging' => '💪',
            'comforting' => '🤗',
            default => '💭',
        };
    }

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

    public function getPopularityLabelAttribute(): string
    {
        if ($this->popularity_score >= 0.8) {
            return 'Muy Popular';
        } elseif ($this->popularity_score >= 0.6) {
            return 'Popular';
        } elseif ($this->popularity_score >= 0.4) {
            return 'Moderada';
        } elseif ($this->popularity_score >= 0.2) {
            return 'Poco Popular';
        } else {
            return 'Desconocida';
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
            return 'success';
        }
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

    public function getLanguageFlagAttribute(): string
    {
        return match ($this->language) {
            'es' => '🇪🇸',
            'en' => '🇬🇧',
            'fr' => '🇫🇷',
            'de' => '🇩🇪',
            'it' => '🇮🇹',
            'pt' => '🇵🇹',
            'ca' => '🏴󠁥󠁳󠁣󠁴󠁿',
            'eu' => '🏴󠁥󠁳󠁰󠁶󠁿',
            'gl' => '🏴󠁥󠁳󠁧󠁡󠁿',
            'la' => '🏛️',
            'gr' => '🇬🇷',
            'ar' => '🇸🇦',
            'zh' => '🇨🇳',
            'ja' => '🇯🇵',
            'ko' => '🇰🇷',
            'ru' => '🇷🇺',
            default => '🌍',
        };
    }

    public function getTagsCountAttribute(): int
    {
        if ($this->tags && is_array($this->tags)) {
            return count($this->tags);
        }
        return 0;
    }

    public function getTranslationsCountAttribute(): int
    {
        if ($this->translations && is_array($this->translations)) {
            return count($this->translations);
        }
        return 0;
    }

    public function getExcerptAttribute(): string
    {
        return \Str::limit($this->text, 100);
    }

    public function getShortTextAttribute(): string
    {
        return \Str::limit($this->text, 50);
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

    public function scopeByDifficulty($query, string $difficulty)
    {
        return $query->where('difficulty_level', $difficulty);
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

    public function scopeBySource($query, string $source)
    {
        return $query->where('source', 'like', '%' . $source . '%');
    }

    public function scopeByWordCount($query, int $min, int $max = null)
    {
        if ($max) {
            return $query->whereBetween('word_count', [$min, $max]);
        }
        return $query->where('word_count', '>=', $min);
    }

    public function scopeByUsage($query, int $minUsage)
    {
        return $query->where('usage_count', '>=', $minUsage);
    }

    public function scopeWithTags($query, array $tags)
    {
        return $query->whereJsonContains('tags', $tags);
    }

    public function scopeInspirational($query)
    {
        return $query->whereIn('mood', ['inspirational', 'motivational', 'wise']);
    }

    public function scopeShort($query, int $maxWords = 20)
    {
        return $query->where('word_count', '<=', $maxWords);
    }

    public function scopeLong($query, int $minWords = 50)
    {
        return $query->where('word_count', '>=', $minWords);
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

    public function isInspirational(): bool
    {
        return in_array($this->mood, ['inspirational', 'motivational', 'wise']);
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

    public function getTranslation(string $language): ?string
    {
        if (!$this->translations || !is_array($this->translations)) {
            return null;
        }

        return $this->translations[$language] ?? null;
    }

    public function getAvailableLanguages(): array
    {
        $languages = [$this->language];
        
        if ($this->translations && is_array($this->translations)) {
            $languages = array_merge($languages, array_keys($this->translations));
        }
        
        return array_unique($languages);
    }

    public function getReadingTime(): int
    {
        // Tiempo de lectura estimado: 200 palabras por minuto
        return max(1, ceil($this->word_count / 200));
    }

    public function getFormattedReadingTime(): string
    {
        $minutes = $this->reading_time;
        if ($minutes < 1) {
            return 'Menos de 1 min';
        } elseif ($minutes === 1) {
            return '1 min';
        } else {
            return $minutes . ' min';
        }
    }
}
