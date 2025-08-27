<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class LiturgicalCalendar extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'liturgical_season',
        'feast_day',
        'saint_id',
        'celebration_level',
        'readings',
        'prayers',
        'traditions',
        'color',
        'description',
        'special_observances',
        'is_holiday',
    ];

    protected $casts = [
        'date' => 'date',
        'readings' => 'array',
        'prayers' => 'array',
        'traditions' => 'array',
        'special_observances' => 'array',
        'is_holiday' => 'boolean',
    ];

    // Relaciones
    public function saint(): BelongsTo
    {
        return $this->belongsTo(CatholicSaint::class, 'saint_id');
    }

    // Atributos calculados
    public function getIsTodayAttribute(): bool
    {
        return $this->date->isToday();
    }

    public function getIsThisYearAttribute(): bool
    {
        return $this->date->year === Carbon::now()->year;
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->date->format('d/m/Y');
    }

    public function getDayOfWeekAttribute(): string
    {
        return $this->date->locale('es')->dayName;
    }

    public function getCelebrationLevelLabelAttribute(): string
    {
        return match ($this->celebration_level) {
            'solemnity' => 'Solemnidad',
            'feast' => 'Fiesta',
            'memorial' => 'Memoria',
            'optional_memorial' => 'Memoria Opcional',
            'ferial' => 'Ferial',
            default => 'Desconocido',
        };
    }

    public function getCelebrationLevelColorAttribute(): string
    {
        return match ($this->celebration_level) {
            'solemnity' => 'danger',
            'feast' => 'warning',
            'memorial' => 'info',
            'optional_memorial' => 'secondary',
            'ferial' => 'success',
            default => 'gray',
        };
    }

    public function getColorLabelAttribute(): string
    {
        return match ($this->color) {
            'white' => 'Blanco',
            'red' => 'Rojo',
            'green' => 'Verde',
            'purple' => 'Morado',
            'pink' => 'Rosa',
            'black' => 'Negro',
            default => 'Sin especificar',
        };
    }

    public function getReadingsCountAttribute(): int
    {
        if ($this->readings && is_array($this->readings)) {
            return count($this->readings);
        }
        return 0;
    }

    public function getPrayersCountAttribute(): int
    {
        if ($this->prayers && is_array($this->prayers)) {
            return count($this->prayers);
        }
        return 0;
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('date', Carbon::today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
    }

    public function scopeBySeason($query, string $season)
    {
        return $query->where('liturgical_season', $season);
    }

    public function scopeByCelebrationLevel($query, string $level)
    {
        return $query->where('celebration_level', $level);
    }

    public function scopeHolidays($query)
    {
        return $query->where('is_holiday', true);
    }

    public function scopeByColor($query, string $color)
    {
        return $query->where('color', $color);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', Carbon::now()->month);
    }

    // Métodos
    public function isSolemnity(): bool
    {
        return $this->celebration_level === 'solemnity';
    }

    public function isFeast(): bool
    {
        return $this->celebration_level === 'feast';
    }

    public function isMemorial(): bool
    {
        return in_array($this->celebration_level, ['memorial', 'optional_memorial']);
    }

    public function isFerial(): bool
    {
        return $this->celebration_level === 'ferial';
    }

    public function hasSaint(): bool
    {
        return !is_null($this->saint_id);
    }

    public function hasReadings(): bool
    {
        return $this->readings_count > 0;
    }

    public function hasPrayers(): bool
    {
        return $this->prayers_count > 0;
    }

    public function getNextOccurrence(): ?Carbon
    {
        $nextYear = $this->date->copy()->addYear();
        return $nextYear;
    }

    public function getPreviousOccurrence(): ?Carbon
    {
        $prevYear = $this->date->copy()->subYear();
        return $prevYear;
    }
}
