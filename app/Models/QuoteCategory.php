<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'color',
        'icon',
        'quotes_count',
        'popularity_score',
        'is_active',
    ];

    protected $casts = [
        'quotes_count' => 'integer',
        'popularity_score' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relaciones
    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'category');
    }

    // Atributos calculados
    public function getPopularityLabelAttribute(): string
    {
        if ($this->popularity_score >= 0.8) {
            return 'Muy Alta';
        } elseif ($this->popularity_score >= 0.6) {
            return 'Alta';
        } elseif ($this->popularity_score >= 0.4) {
            return 'Media';
        } elseif ($this->popularity_score >= 0.2) {
            return 'Baja';
        } else {
            return 'Muy Baja';
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

    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Activa' : 'Inactiva';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->is_active ? 'success' : 'secondary';
    }

    public function getFormattedQuotesCountAttribute(): string
    {
        if ($this->quotes_count >= 1000) {
            return number_format($this->quotes_count / 1000, 1) . 'K';
        }
        return number_format($this->quotes_count);
    }

    public function getIconDisplayAttribute(): string
    {
        return $this->icon ?: '💭';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopePopular($query, float $minScore = 0.6)
    {
        return $query->where('popularity_score', '>=', $minScore);
    }

    public function scopeByPopularity($query, string $level)
    {
        return match ($level) {
            'very_high' => $query->where('popularity_score', '>=', 0.8),
            'high' => $query->where('popularity_score', '>=', 0.6),
            'medium' => $query->where('popularity_score', '>=', 0.4),
            'low' => $query->where('popularity_score', '>=', 0.2),
            'very_low' => $query->where('popularity_score', '<', 0.2),
            default => $query,
        };
    }

    public function scopeWithQuotes($query, int $minQuotes = 1)
    {
        return $query->where('quotes_count', '>=', $minQuotes);
    }

    public function scopeOrderByPopularity($query, string $direction = 'desc')
    {
        return $query->orderBy('popularity_score', $direction);
    }

    public function scopeOrderByQuotesCount($query, string $direction = 'desc')
    {
        return $query->orderBy('quotes_count', $direction);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%');
        });
    }

    // Métodos
    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isPopular(): bool
    {
        return $this->popularity_score >= 0.6;
    }

    public function hasQuotes(): bool
    {
        return $this->quotes_count > 0;
    }

    public function isEmpty(): bool
    {
        return $this->quotes_count === 0;
    }

    public function isWellPopulated(): bool
    {
        return $this->quotes_count >= 10;
    }

    public function getQuotesPercentage(): float
    {
        // Calcular el porcentaje de citas en esta categoría vs total
        $totalQuotes = Quote::count();
        if ($totalQuotes === 0) {
            return 0;
        }
        return round(($this->quotes_count / $totalQuotes) * 100, 2);
    }

    public function getFormattedQuotesPercentageAttribute(): string
    {
        return $this->getQuotesPercentage() . '%';
    }

    public function getAverageQuotePopularity(): float
    {
        if ($this->quotes_count === 0) {
            return 0;
        }

        $avgPopularity = $this->quotes()->avg('popularity_score');
        return round($avgPopularity ?? 0, 2);
    }

    public function getFormattedAverageQuotePopularityAttribute(): string
    {
        $avg = $this->getAverageQuotePopularity();
        if ($avg >= 0.8) {
            return 'Muy Alta';
        } elseif ($avg >= 0.6) {
            return 'Alta';
        } elseif ($avg >= 0.4) {
            return 'Media';
        } else {
            return 'Baja';
        }
    }

    public function getTopQuotes(int $limit = 5)
    {
        return $this->quotes()
                   ->orderBy('popularity_score', 'desc')
                   ->orderBy('usage_count', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public function getRecentQuotes(int $limit = 5)
    {
        return $this->quotes()
                   ->orderBy('created_at', 'desc')
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

    public function getQuotesByLanguage(string $language, int $limit = 10)
    {
        return $this->quotes()
                   ->where('language', $language)
                   ->orderBy('popularity_score', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public function getQuotesByDifficulty(string $difficulty, int $limit = 10)
    {
        return $this->quotes()
                   ->where('difficulty_level', $difficulty)
                   ->orderBy('popularity_score', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public function getRandomQuote()
    {
        return $this->quotes()->inRandomOrder()->first();
    }

    public function getDailyQuote()
    {
        // Obtener una cita basada en el día del año para consistencia
        $dayOfYear = now()->dayOfYear;
        $quotes = $this->quotes()->get();
        
        if ($quotes->isEmpty()) {
            return null;
        }
        
        $index = $dayOfYear % $quotes->count();
        return $quotes[$index];
    }

    public function updateQuotesCount(): void
    {
        $this->quotes_count = $this->quotes()->count();
        $this->save();
    }

    public function updatePopularityScore(): void
    {
        if ($this->quotes_count === 0) {
            $this->popularity_score = 0;
        } else {
            $avgPopularity = $this->quotes()->avg('popularity_score');
            $this->popularity_score = round($avgPopularity ?? 0, 2);
        }
        $this->save();
    }

    public function refreshStats(): void
    {
        $this->updateQuotesCount();
        $this->updatePopularityScore();
    }
}
