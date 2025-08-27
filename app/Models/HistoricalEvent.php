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
        'related_events' => 'array',
        'is_verified' => 'boolean',
    ];

    // Atributos calculados
    public function getSignificanceLabelAttribute(): string
    {
        return match ($this->significance_level) {
            'critical' => 'Crítico',
            'major' => 'Mayor',
            'moderate' => 'Moderado',
            'minor' => 'Menor',
            'insignificant' => 'Insignificante',
            default => 'Sin especificar',
        };
    }

    public function getSignificanceColorAttribute(): string
    {
        return match ($this->significance_level) {
            'critical' => 'danger',
            'major' => 'warning',
            'moderate' => 'info',
            'minor' => 'success',
            'insignificant' => 'secondary',
            default => 'gray',
        };
    }

    public function getEraLabelAttribute(): string
    {
        return match ($this->era) {
            'ancient' => 'Antigüedad',
            'medieval' => 'Edad Media',
            'renaissance' => 'Renacimiento',
            'modern' => 'Edad Moderna',
            'contemporary' => 'Contemporánea',
            'prehistoric' => 'Prehistoria',
            default => 'Sin especificar',
        };
    }

    public function getEraColorAttribute(): string
    {
        return match ($this->era) {
            'ancient' => 'danger',
            'medieval' => 'warning',
            'renaissance' => 'info',
            'modern' => 'success',
            'contemporary' => 'primary',
            'prehistoric' => 'secondary',
            default => 'gray',
        };
    }

    public function getYearsAgoAttribute(): int
    {
        return $this->event_date->diffInYears(Carbon::now());
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->event_date->format('d/m/Y');
    }

    public function getCenturyAttribute(): int
    {
        return (int) ceil($this->event_date->year / 100);
    }

    public function getCenturyLabelAttribute(): string
    {
        $century = $this->century;
        if ($century === 1) {
            return 'Siglo I';
        } elseif ($century === 2) {
            return 'Siglo II';
        } elseif ($century === 3) {
            return 'Siglo III';
        } elseif ($century === 4) {
            return 'Siglo IV';
        } elseif ($century === 5) {
            return 'Siglo V';
        } elseif ($century === 6) {
            return 'Siglo VI';
        } elseif ($century === 7) {
            return 'Siglo VII';
        } elseif ($century === 8) {
            return 'Siglo VIII';
        } elseif ($century === 9) {
            return 'Siglo IX';
        } elseif ($century === 10) {
            return 'Siglo X';
        } elseif ($century === 11) {
            return 'Siglo XI';
        } elseif ($century === 12) {
            return 'Siglo XII';
        } elseif ($century === 13) {
            return 'Siglo XIII';
        } elseif ($century === 14) {
            return 'Siglo XIV';
        } elseif ($century === 15) {
            return 'Siglo XV';
        } elseif ($century === 16) {
            return 'Siglo XVI';
        } elseif ($century === 17) {
            return 'Siglo XVII';
        } elseif ($century === 18) {
            return 'Siglo XVIII';
        } elseif ($century === 19) {
            return 'Siglo XIX';
        } elseif ($century === 20) {
            return 'Siglo XX';
        } elseif ($century === 21) {
            return 'Siglo XXI';
        } else {
            return 'Siglo ' . $century;
        }
    }

    public function getKeyFiguresCountAttribute(): int
    {
        if (is_array($this->key_figures)) {
            return count($this->key_figures);
        }
        return 0;
    }

    public function getConsequencesCountAttribute(): int
    {
        if (is_array($this->consequences)) {
            return count($this->consequences);
        }
        return 0;
    }

    public function getSourcesCountAttribute(): int
    {
        if (is_array($this->sources)) {
            return count($this->sources);
        }
        return 0;
    }

    public function getRelatedEventsCountAttribute(): int
    {
        if (is_array($this->related_events)) {
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
        return $query->whereIn('significance_level', ['critical', 'major']);
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

    public function isCritical(): bool
    {
        return $this->significance_level === 'critical';
    }

    public function isMajor(): bool
    {
        return in_array($this->significance_level, ['critical', 'major']);
    }

    public function isAncient(): bool
    {
        return $this->years_ago >= 1000;
    }

    public function isRecent(): bool
    {
        return $this->years_ago <= 100;
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

    public function getKeyFiguresList(): array
    {
        if (is_array($this->key_figures)) {
            return $this->key_figures;
        }
        return [];
    }

    public function getConsequencesList(): array
    {
        if (is_array($this->consequences)) {
            return $this->consequences;
        }
        return [];
    }

    public function getSourcesList(): array
    {
        if (is_array($this->sources)) {
            return $this->sources;
        }
        return [];
    }

    public function getRelatedEventsList(): array
    {
        if (is_array($this->related_events)) {
            return $this->related_events;
        }
        return [];
    }
}
