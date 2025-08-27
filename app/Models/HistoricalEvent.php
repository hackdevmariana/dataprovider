<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class HistoricalEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'event_date',
        'era',
        'category',
        'location',
        'country',
        'key_figures',
        'consequences',
        'significance_level',
        'sources',
        'is_verified',
        'related_events',
    ];

    protected $casts = [
        'event_date' => 'date',
        'key_figures' => 'array',
        'consequences' => 'array',
        'sources' => 'array',
        'is_verified' => 'boolean',
        'related_events' => 'array',
    ];

    // Atributos calculados
    public function getYearsAgoAttribute(): int
    {
        return $this->event_date->diffInYears(Carbon::now());
    }

    public function getCenturyAttribute(): int
    {
        return ceil($this->event_date->year / 100);
    }

    public function getMillenniumAttribute(): int
    {
        return ceil($this->event_date->year / 1000);
    }

    public function getEraLabelAttribute(): string
    {
        return match ($this->era) {
            'ancient' => 'Antigüedad',
            'medieval' => 'Edad Media',
            'renaissance' => 'Renacimiento',
            'modern' => 'Edad Moderna',
            'contemporary' => 'Edad Contemporánea',
            'prehistoric' => 'Prehistoria',
            'classical' => 'Clásica',
            'enlightenment' => 'Ilustración',
            'industrial' => 'Revolución Industrial',
            'digital' => 'Era Digital',
            default => 'Sin especificar',
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'politics' => 'Política',
            'war' => 'Guerra',
            'science' => 'Ciencia',
            'culture' => 'Cultura',
            'religion' => 'Religión',
            'economy' => 'Economía',
            'social' => 'Social',
            'technology' => 'Tecnología',
            'exploration' => 'Exploración',
            'art' => 'Arte',
            'literature' => 'Literatura',
            'music' => 'Música',
            'architecture' => 'Arquitectura',
            'medicine' => 'Medicina',
            'philosophy' => 'Filosofía',
            'education' => 'Educación',
            'sports' => 'Deportes',
            'environment' => 'Medio Ambiente',
            default => 'Otro',
        };
    }

    public function getSignificanceLabelAttribute(): string
    {
        return match ($this->significance_level) {
            'very_high' => 'Muy Alta',
            'high' => 'Alta',
            'moderate' => 'Moderada',
            'low' => 'Baja',
            'very_low' => 'Muy Baja',
            default => 'Sin especificar',
        };
    }

    public function getSignificanceColorAttribute(): string
    {
        return match ($this->significance_level) {
            'very_high' => 'danger',
            'high' => 'warning',
            'moderate' => 'info',
            'low' => 'success',
            'very_low' => 'secondary',
            default => 'gray',
        };
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->event_date->format('d/m/Y');
    }

    public function getKeyFiguresCountAttribute(): int
    {
        if ($this->key_figures && is_array($this->key_figures)) {
            return count($this->key_figures);
        }
        return 0;
    }

    public function getConsequencesCountAttribute(): int
    {
        if ($this->consequences && is_array($this->consequences)) {
            return count($this->consequences);
        }
        return 0;
    }

    public function getSourcesCountAttribute(): int
    {
        if ($this->sources && is_array($this->sources)) {
            return count($this->sources);
        }
        return 0;
    }

    public function getRelatedEventsCountAttribute(): int
    {
        if ($this->related_events && is_array($this->related_events)) {
            return count($this->related_events);
        }
        return 0;
    }

    // Scopes
    public function scopeByEra($query, string $era)
    {
        return $query->where('era', $era);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByCountry($query, string $country)
    {
        return $query->where('country', $country);
    }

    public function scopeBySignificance($query, string $level)
    {
        return $query->where('significance_level', $level);
    }

    public function scopeHighSignificance($query)
    {
        return $query->whereIn('significance_level', ['very_high', 'high']);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('event_date', $date);
    }

    public function scopeByYear($query, int $year)
    {
        return $query->whereYear('event_date', $year);
    }

    public function scopeByCentury($query, int $century)
    {
        $startYear = ($century - 1) * 100 + 1;
        $endYear = $century * 100;
        return $query->whereBetween('event_date', [
            Carbon::createFromDate($startYear, 1, 1),
            Carbon::createFromDate($endYear, 12, 31)
        ]);
    }

    public function scopeRecent($query, int $years = 100)
    {
        return $query->where('event_date', '>=', Carbon::now()->subYears($years));
    }

    public function scopeAncient($query, int $years = 1000)
    {
        return $query->where('event_date', '<=', Carbon::now()->subYears($years));
    }

    // Métodos
    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    public function isHighSignificance(): bool
    {
        return in_array($this->significance_level, ['very_high', 'high']);
    }

    public function isAncient(): bool
    {
        return $this->event_date->year < 500;
    }

    public function isMedieval(): bool
    {
        return $this->event_date->year >= 500 && $this->event_date->year < 1500;
    }

    public function isModern(): bool
    {
        return $this->event_date->year >= 1500 && $this->event_date->year < 1800;
    }

    public function isContemporary(): bool
    {
        return $this->event_date->year >= 1800;
    }

    public function hasKeyFigures(): bool
    {
        return $this->key_figures_count > 0;
    }

    public function hasConsequences(): bool
    {
        return $this->consequences_count > 0;
    }

    public function hasSources(): bool
    {
        return $this->sources_count > 0;
    }

    public function hasRelatedEvents(): bool
    {
        return $this->related_events_count > 0;
    }

    public function getAgeDescription(): string
    {
        $years = $this->years_ago;
        
        if ($years < 1) {
            return 'Este año';
        } elseif ($years < 10) {
            return 'Hace ' . $years . ' años';
        } elseif ($years < 100) {
            return 'Hace ' . $years . ' años';
        } elseif ($years < 1000) {
            return 'Hace ' . round($years / 100, 1) . ' siglos';
        } else {
            return 'Hace ' . round($years / 1000, 1) . ' milenios';
        }
    }
}
