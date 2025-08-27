<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TrendingTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic',
        'trending_score',
        'mentions_count',
        'growth_rate',
        'geographic_spread',
        'category',
        'related_keywords',
        'geographic_data',
        'peak_time',
        'peak_score',
        'trend_analysis',
        'is_breaking',
    ];

    protected $casts = [
        'trending_score' => 'decimal:2',
        'mentions_count' => 'integer',
        'growth_rate' => 'decimal:2',
        'geographic_spread' => 'array',
        'related_keywords' => 'array',
        'geographic_data' => 'array',
        'peak_time' => 'datetime',
        'peak_score' => 'decimal:2',
        'trend_analysis' => 'array',
        'is_breaking' => 'boolean',
    ];

    // Atributos calculados
    public function getTrendingLevelAttribute(): string
    {
        if ($this->trending_score >= 0.8) {
            return 'Viral';
        } elseif ($this->trending_score >= 0.6) {
            return 'Trending';
        } elseif ($this->trending_score >= 0.4) {
            return 'Emergente';
        } else {
            return 'Estable';
        }
    }

    public function getTrendingLevelColorAttribute(): string
    {
        if ($this->trending_score >= 0.8) {
            return 'danger';
        } elseif ($this->trending_score >= 0.6) {
            return 'warning';
        } elseif ($this->trending_score >= 0.4) {
            return 'info';
        } else {
            return 'success';
        }
    }

    public function getGrowthLabelAttribute(): string
    {
        if ($this->growth_rate >= 0.5) {
            return 'Alto crecimiento';
        } elseif ($this->growth_rate >= 0.2) {
            return 'Crecimiento moderado';
        } elseif ($this->growth_rate >= 0) {
            return 'Crecimiento lento';
        } else {
            return 'En declive';
        }
    }

    public function getGrowthColorAttribute(): string
    {
        if ($this->growth_rate >= 0.5) {
            return 'success';
        } elseif ($this->growth_rate >= 0.2) {
            return 'info';
        } elseif ($this->growth_rate >= 0) {
            return 'warning';
        } else {
            return 'danger';
        }
    }

    public function getFormattedPeakTimeAttribute(): string
    {
        if ($this->peak_time) {
            return $this->peak_time->diffForHumans();
        }
        return 'N/A';
    }

    public function getGeographicSpreadCountAttribute(): int
    {
        if ($this->geographic_spread && is_array($this->geographic_spread)) {
            return count($this->geographic_spread);
        }
        return 0;
    }

    public function getRelatedKeywordsCountAttribute(): int
    {
        if ($this->related_keywords && is_array($this->related_keywords)) {
            return count($this->related_keywords);
        }
        return 0;
    }

    // Scopes
    public function scopeBreaking($query)
    {
        return $query->where('is_breaking', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeHighTrending($query, float $minScore = 0.6)
    {
        return $query->where('trending_score', '>=', $minScore);
    }

    public function scopeGrowing($query, float $minRate = 0.1)
    {
        return $query->where('growth_rate', '>=', $minRate);
    }

    public function scopeByGeographicSpread($query, string $location)
    {
        return $query->whereJsonContains('geographic_spread', $location);
    }

    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('created_at', '>=', Carbon::now()->subHours($hours));
    }

    public function scopeByMentions($query, int $minMentions)
    {
        return $query->where('mentions_count', '>=', $minMentions);
    }

    // Métodos
    public function isViral(): bool
    {
        return $this->trending_score >= 0.8;
    }

    public function isTrending(): bool
    {
        return $this->trending_score >= 0.6;
    }

    public function isGrowing(): bool
    {
        return $this->growth_rate > 0;
    }

    public function isDeclining(): bool
    {
        return $this->growth_rate < 0;
    }

    public function hasReachedPeak(): bool
    {
        return $this->peak_time && $this->peak_time->isPast();
    }

    public function getCurrentScore(): float
    {
        if ($this->hasReachedPeak()) {
            // Calcular score actual basado en el tiempo transcurrido desde el pico
            $hoursSincePeak = $this->peak_time->diffInHours(now());
            $decayRate = 0.1; // 10% de decaimiento por hora
            $currentScore = $this->peak_score * (1 - ($decayRate * $hoursSincePeak));
            return max(0, $currentScore);
        }
        return $this->trending_score;
    }

    public function getTrendDirection(): string
    {
        if ($this->growth_rate > 0.1) {
            return '↗️ Subiendo';
        } elseif ($this->growth_rate > -0.1) {
            return '→ Estable';
        } else {
            return '↘️ Bajando';
        }
    }
}
