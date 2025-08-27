<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class NewsSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'type',
        'reliability_score',
        'update_frequency',
        'last_scraped',
        'is_active',
        'categories',
        'geographic_scope',
        'language',
        'api_credentials',
        'scraping_rules',
        'articles_per_day',
        'last_error',
        'error_message',
    ];

    protected $casts = [
        'reliability_score' => 'decimal:2',
        'last_scraped' => 'datetime',
        'is_active' => 'boolean',
        'categories' => 'array',
        'geographic_scope' => 'array',
        'api_credentials' => 'encrypted:array',
        'scraping_rules' => 'array',
        'articles_per_day' => 'integer',
        'last_error' => 'datetime',
    ];

    // Relaciones
    public function aggregations(): HasMany
    {
        return $this->hasMany(NewsAggregation::class, 'source_id');
    }

    // Atributos calculados
    public function getReliabilityLabelAttribute(): string
    {
        if ($this->reliability_score >= 0.8) {
            return 'Muy Alta';
        } elseif ($this->reliability_score >= 0.6) {
            return 'Alta';
        } elseif ($this->reliability_score >= 0.4) {
            return 'Media';
        } elseif ($this->reliability_score >= 0.2) {
            return 'Baja';
        } else {
            return 'Muy Baja';
        }
    }

    public function getReliabilityColorAttribute(): string
    {
        if ($this->reliability_score >= 0.8) {
            return 'success';
        } elseif ($this->reliability_score >= 0.6) {
            return 'info';
        } elseif ($this->reliability_score >= 0.4) {
            return 'warning';
        } else {
            return 'danger';
        }
    }

    public function getLastScrapedFormattedAttribute(): string
    {
        if ($this->last_scraped) {
            return $this->last_scraped->diffForHumans();
        }
        return 'Nunca';
    }

    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'Inactiva';
        }
        
        if ($this->last_error && $this->last_error->diffInHours(now()) < 24) {
            return 'Con errores';
        }
        
        if ($this->last_scraped && $this->last_scraped->diffInHours(now()) > 24) {
            return 'Desactualizada';
        }
        
        return 'Activa';
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'Activa' => 'success',
            'Desactualizada' => 'warning',
            'Con errores' => 'danger',
            'Inactiva' => 'secondary',
            default => 'info',
        };
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByLanguage($query, string $language)
    {
        return $query->where('language', $language);
    }

    public function scopeByReliability($query, float $minScore)
    {
        return $query->where('reliability_score', '>=', $minScore);
    }

    public function scopeWithErrors($query)
    {
        return $query->whereNotNull('last_error');
    }

    public function scopeRecentlyScraped($query, int $hours = 24)
    {
        return $query->where('last_scraped', '>=', Carbon::now()->subHours($hours));
    }

    // Métodos
    public function isReliable(): bool
    {
        return $this->reliability_score >= 0.6;
    }

    public function needsScraping(): bool
    {
        if (!$this->is_active) {
            return false;
        }
        
        if (!$this->last_scraped) {
            return true;
        }
        
        $frequency = $this->update_frequency ?? 'daily';
        $hours = match ($frequency) {
            'hourly' => 1,
            'daily' => 24,
            'weekly' => 168,
            'monthly' => 720,
            default => 24,
        };
        
        return $this->last_scraped->diffInHours(now()) >= $hours;
    }

    public function hasErrors(): bool
    {
        return $this->last_error && $this->last_error->diffInHours(now()) < 24;
    }

    public function getDomainAttribute(): string
    {
        if ($this->url) {
            return parse_url($this->url, PHP_URL_HOST);
        }
        return '';
    }
}
