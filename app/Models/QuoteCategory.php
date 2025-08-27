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
        return $this->hasMany(Quote::class, 'category', 'name');
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
        } else {
            return 'Baja';
        }
    }

    public function getPopularityColorAttribute(): string
    {
        if ($this->popularity_score >= 0.8) {
            return 'success';
        } elseif ($this->popularity_score >= 0.6) {
            return 'info';
        } elseif ($this->popularity_score >= 0.4) {
            return 'warning';
        } else {
            return 'secondary';
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
        if ($this->quotes_count >= 1000000) {
            return round($this->quotes_count / 1000000, 1) . 'M';
        } elseif ($this->quotes_count >= 1000) {
            return round($this->quotes_count / 1000, 1) . 'K';
        }
        return number_format($this->quotes_count);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePopular($query, float $minScore = 0.6)
    {
        return $query->where('popularity_score', '>=', $minScore);
    }

    public function scopeByQuotesCount($query, int $minCount)
    {
        return $query->where('quotes_count', '>=', $minCount);
    }

    public function scopeOrderByPopularity($query)
    {
        return $query->orderBy('popularity_score', 'desc');
    }

    public function scopeOrderByQuotesCount($query)
    {
        return $query->orderBy('quotes_count', 'desc');
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

    public function getQuotesCount(): int
    {
        return $this->quotes_count;
    }

    public function updateQuotesCount(): void
    {
        $this->quotes_count = $this->quotes()->count();
        $this->save();
    }

    public function incrementQuotesCount(): void
    {
        $this->increment('quotes_count');
    }

    public function decrementQuotesCount(): void
    {
        $this->decrement('quotes_count');
    }
}
