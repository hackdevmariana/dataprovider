<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DailyAnniversary extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'title',
        'description',
        'category',
        'significance_level',
        'country',
        'historical_period',
        'related_events',
        'celebration_type',
        'is_public_holiday',
        'tags',
        'image_url',
        'external_links',
    ];

    protected $casts = [
        'date' => 'date',
        'related_events' => 'array',
        'tags' => 'array',
        'external_links' => 'array',
        'is_public_holiday' => 'boolean',
    ];

    // Atributos calculados
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'historical' => 'Histórico',
            'cultural' => 'Cultural',
            'religious' => 'Religioso',
            'scientific' => 'Científico',
            'political' => 'Político',
            'social' => 'Social',
            'artistic' => 'Artístico',
            'sports' => 'Deportivo',
            'literary' => 'Literario',
            'musical' => 'Musical',
            default => 'Otro',
        };
    }

    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            'historical' => 'dark',
            'cultural' => 'info',
            'religious' => 'warning',
            'scientific' => 'success',
            'political' => 'danger',
            'social' => 'primary',
            'artistic' => 'secondary',
            'sports' => 'success',
            'literary' => 'info',
            'musical' => 'warning',
            default => 'gray',
        };
    }

    public function getSignificanceLevelLabelAttribute(): string
    {
        return match ($this->significance_level) {
            'critical' => 'Crítico',
            'major' => 'Mayor',
            'important' => 'Importante',
            'notable' => 'Notable',
            'minor' => 'Menor',
            default => 'Sin especificar',
        };
    }

    public function getSignificanceLevelColorAttribute(): string
    {
        return match ($this->significance_level) {
            'critical' => 'danger',
            'major' => 'warning',
            'important' => 'info',
            'notable' => 'success',
            'minor' => 'secondary',
            default => 'gray',
        };
    }

    public function getCelebrationTypeLabelAttribute(): string
    {
        return match ($this->celebration_type) {
            'national' => 'Nacional',
            'regional' => 'Regional',
            'local' => 'Local',
            'international' => 'Internacional',
            'religious' => 'Religioso',
            'cultural' => 'Cultural',
            'commemorative' => 'Conmemorativo',
            'festive' => 'Festivo',
            default => 'Sin especificar',
        };
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->date->format('d/m/Y');
    }

    public function getDayOfWeekAttribute(): string
    {
        return $this->date->format('l');
    }

    public function getDayOfWeekLabelAttribute(): string
    {
        return match ($this->day_of_week) {
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo',
            default => 'Desconocido',
        };
    }

    public function getIsTodayAttribute(): bool
    {
        return $this->date->isToday();
    }

    public function getIsThisMonthAttribute(): bool
    {
        return $this->date->isCurrentMonth();
    }

    public function getIsThisYearAttribute(): bool
    {
        return $this->date->isCurrentYear();
    }

    public function getYearsAgoAttribute(): int
    {
        return $this->date->diffInYears(now());
    }

    public function getCenturyAttribute(): int
    {
        return ceil($this->date->year / 100);
    }

    public function getCenturyLabelAttribute(): string
    {
        $century = $this->century;
        if ($century === 1) {
            return 'Siglo I';
        } elseif ($century <= 10) {
            return 'Siglo ' . $this->numberToRoman($century);
        } else {
            return 'Siglo ' . $century;
        }
    }

    public function getTagsCountAttribute(): int
    {
        if (is_array($this->tags)) {
            return count($this->tags);
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

    public function getExternalLinksCountAttribute(): int
    {
        if (is_array($this->external_links)) {
            return count($this->external_links);
        }
        return 0;
    }

    // Scopes
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeBySignificanceLevel($query, string $level)
    {
        return $query->where('significance_level', $level);
    }

    public function scopeByCountry($query, string $country)
    {
        return $query->where('country', $country);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', Carbon::today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', Carbon::now()->month);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('date', Carbon::now()->year);
    }

    public function scopeByHistoricalPeriod($query, string $period)
    {
        return $query->where('historical_period', $period);
    }

    public function scopePublicHolidays($query)
    {
        return $query->where('is_public_holiday', true);
    }

    public function scopeByCelebrationType($query, string $type)
    {
        return $query->where('celebration_type', $type);
    }

    public function scopeHighSignificance($query)
    {
        return $query->whereIn('significance_level', ['critical', 'major', 'important']);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%')
              ->orWhere('country', 'like', '%' . $search . '%');
        });
    }

    // Métodos
    public function isToday(): bool
    {
        return $this->is_today;
    }

    public function isThisMonth(): bool
    {
        return $this->is_this_month;
    }

    public function isThisYear(): bool
    {
        return $this->is_this_year;
    }

    public function isPublicHoliday(): bool
    {
        return $this->is_public_holiday;
    }

    public function isCritical(): bool
    {
        return $this->significance_level === 'critical';
    }

    public function isMajor(): bool
    {
        return $this->significance_level === 'major';
    }

    public function isImportant(): bool
    {
        return $this->significance_level === 'important';
    }

    public function hasTags(): bool
    {
        return $this->tags_count > 0;
    }

    public function hasRelatedEvents(): bool
    {
        return $this->related_events_count > 0;
    }

    public function hasExternalLinks(): bool
    {
        return $this->external_links_count > 0;
    }

    public function hasImage(): bool
    {
        return !empty($this->image_url);
    }

    public function getTagsList(): array
    {
        if (is_array($this->tags)) {
            return $this->tags;
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

    public function getExternalLinksList(): array
    {
        if (is_array($this->external_links)) {
            return $this->external_links;
        }
        return [];
    }

    private function numberToRoman(int $number): string
    {
        $romans = [
            1000 => 'M', 900 => 'CM', 500 => 'D', 400 => 'CD',
            100 => 'C', 90 => 'XC', 50 => 'L', 40 => 'XL',
            10 => 'X', 9 => 'IX', 5 => 'V', 4 => 'IV', 1 => 'I'
        ];

        $result = '';
        foreach ($romans as $value => $roman) {
            while ($number >= $value) {
                $result .= $roman;
                $number -= $value;
            }
        }
        return $result;
    }
}
