<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DailyAnniversary extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'years_ago',
        'original_date',
        'category',
        'type',
        'related_people',
        'related_places',
        'significance',
        'is_recurring',
        'celebration_info',
    ];

    protected $casts = [
        'original_date' => 'date',
        'years_ago' => 'integer',
        'related_people' => 'array',
        'related_places' => 'array',
        'is_recurring' => 'boolean',
        'celebration_info' => 'array',
    ];

    // Atributos calculados
    public function getCurrentDateAttribute(): Carbon
    {
        return Carbon::now()->subYears($this->years_ago);
    }

    public function getAnniversaryDateAttribute(): Carbon
    {
        return Carbon::now()->subYears($this->years_ago);
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'historical' => 'Histórico',
            'cultural' => 'Cultural',
            'scientific' => 'Científico',
            'political' => 'Político',
            'social' => 'Social',
            'artistic' => 'Artístico',
            'literary' => 'Literario',
            'musical' => 'Musical',
            'sports' => 'Deportivo',
            'religious' => 'Religioso',
            'military' => 'Militar',
            'economic' => 'Económico',
            'technological' => 'Tecnológico',
            'medical' => 'Médico',
            'educational' => 'Educativo',
            'environmental' => 'Ambiental',
            'space' => 'Espacial',
            'transportation' => 'Transporte',
            'communication' => 'Comunicación',
            'entertainment' => 'Entretenimiento',
            default => 'Otro',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'anniversary' => 'Aniversario',
            'birthday' => 'Cumpleaños',
            'death' => 'Fallecimiento',
            'discovery' => 'Descubrimiento',
            'invention' => 'Invención',
            'event' => 'Evento',
            'achievement' => 'Logro',
            'milestone' => 'Hito',
            'tragedy' => 'Tragedia',
            'victory' => 'Victoria',
            'treaty' => 'Tratado',
            'declaration' => 'Declaración',
            'revolution' => 'Revolución',
            'war' => 'Guerra',
            'peace' => 'Paz',
            default => 'Otro',
        };
    }

    public function getSignificanceLabelAttribute(): string
    {
        return match ($this->significance) {
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
        return match ($this->significance) {
            'very_high' => 'danger',
            'high' => 'warning',
            'moderate' => 'info',
            'low' => 'success',
            'very_low' => 'secondary',
            default => 'gray',
        };
    }

    public function getFormattedOriginalDateAttribute(): string
    {
        return $this->original_date->format('d/m/Y');
    }

    public function getFormattedCurrentDateAttribute(): string
    {
        return $this->current_date->format('d/m/Y');
    }

    public function getRelatedPeopleCountAttribute(): int
    {
        if ($this->related_people && is_array($this->related_people)) {
            return count($this->related_people);
        }
        return 0;
    }

    public function getRelatedPlacesCountAttribute(): int
    {
        if ($this->related_places && is_array($this->related_places)) {
            return count($this->related_places);
        }
        return 0;
    }

    public function getCelebrationInfoCountAttribute(): int
    {
        if ($this->celebration_info && is_array($this->celebration_info)) {
            return count($this->celebration_info);
        }
        return 0;
    }

    // Scopes
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeBySignificance($query, string $level)
    {
        return $query->where('significance', $level);
    }

    public function scopeHighSignificance($query)
    {
        return $query->whereIn('significance', ['very_high', 'high']);
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    public function scopeByYearsAgo($query, int $years)
    {
        return $query->where('years_ago', $years);
    }

    public function scopeByDecade($query, int $decade)
    {
        $startYear = $decade * 10;
        $endYear = ($decade + 1) * 10 - 1;
        return $query->whereBetween('years_ago', [$startYear, $endYear]);
    }

    public function scopeByCentury($query, int $century)
    {
        $startYear = ($century - 1) * 100;
        $endYear = $century * 100 - 1;
        return $query->whereBetween('years_ago', [$startYear, $endYear]);
    }

    public function scopeRecent($query, int $maxYears = 100)
    {
        return $query->where('years_ago', '<=', $maxYears);
    }

    public function scopeAncient($query, int $minYears = 1000)
    {
        return $query->where('years_ago', '>=', $minYears);
    }

    public function scopeToday($query)
    {
        $today = Carbon::today();
        return $query->whereMonth('original_date', $today->month)
                    ->whereDay('original_date', $today->day);
    }

    public function scopeThisMonth($query)
    {
        $today = Carbon::today();
        return $query->whereMonth('original_date', $today->month);
    }

    // Métodos
    public function isRecurring(): bool
    {
        return $this->is_recurring;
    }

    public function isHighSignificance(): bool
    {
        return in_array($this->significance, ['very_high', 'high']);
    }

    public function isToday(): bool
    {
        $today = Carbon::today();
        return $this->original_date->month === $today->month && 
               $this->original_date->day === $today->day;
    }

    public function isThisMonth(): bool
    {
        $today = Carbon::today();
        return $this->original_date->month === $today->month;
    }

    public function isRecent(): bool
    {
        return $this->years_ago <= 100;
    }

    public function isAncient(): bool
    {
        return $this->years_ago >= 1000;
    }

    public function hasRelatedPeople(): bool
    {
        return $this->related_people_count > 0;
    }

    public function hasRelatedPlaces(): bool
    {
        return $this->related_places_count > 0;
    }

    public function hasCelebrationInfo(): bool
    {
        return $this->celebration_info_count > 0;
    }

    public function getAgeDescription(): string
    {
        if ($this->years_ago < 1) {
            return 'Este año';
        } elseif ($this->years_ago < 10) {
            return 'Hace ' . $this->years_ago . ' años';
        } elseif ($this->years_ago < 100) {
            return 'Hace ' . $this->years_ago . ' años';
        } elseif ($this->years_ago < 1000) {
            return 'Hace ' . round($this->years_ago / 100, 1) . ' siglos';
        } else {
            return 'Hace ' . round($this->years_ago / 1000, 1) . ' milenios';
        }
    }

    public function getNextOccurrence(): Carbon
    {
        $nextYear = Carbon::now()->addYear();
        return $nextYear->setMonth($this->original_date->month)
                       ->setDay($this->original_date->day);
    }

    public function getDaysUntilNext(): int
    {
        $next = $this->getNextOccurrence();
        return Carbon::now()->diffInDays($next, false);
    }
}
