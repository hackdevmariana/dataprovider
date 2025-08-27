<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class FestivalSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'festival_id',
        'date',
        'opening_time',
        'closing_time',
        'main_events',
        'side_activities',
        'special_notes',
        'weather_forecast',
        'expected_attendance',
        'transportation_info',
        'parking_info',
    ];

    protected $casts = [
        'date' => 'date',
        'opening_time' => 'datetime',
        'closing_time' => 'datetime',
        'main_events' => 'array',
        'side_activities' => 'array',
        'weather_forecast' => 'array',
        'expected_attendance' => 'integer',
        'transportation_info' => 'array',
        'parking_info' => 'array',
    ];

    // Relaciones
    public function festival(): BelongsTo
    {
        return $this->belongsTo(Festival::class);
    }

    // Atributos calculados
    public function getOpeningHoursAttribute(): string
    {
        if ($this->opening_time && $this->closing_time) {
            return $this->opening_time->format('H:i') . ' - ' . $this->closing_time->format('H:i');
        }
        return '';
    }

    public function getDurationHoursAttribute(): float
    {
        if ($this->opening_time && $this->closing_time) {
            return $this->opening_time->diffInHours($this->closing_time);
        }
        return 0;
    }

    public function getIsTodayAttribute(): bool
    {
        return $this->date->isToday();
    }

    public function getIsWeekendAttribute(): bool
    {
        return $this->date->isWeekend();
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->date->format('d/m/Y');
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

    public function scopeWeekend($query)
    {
        return $query->whereRaw('WEEKDAY(date) IN (5, 6)');
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    // Métodos
    public function isOpen(): bool
    {
        $now = Carbon::now();
        return $this->date->isToday() && 
               $this->opening_time && 
               $this->closing_time &&
               $now->between($this->opening_time, $this->closing_time);
    }

    public function getWeatherIconAttribute(): string
    {
        if (!$this->weather_forecast) {
            return '🌤️';
        }

        $weather = $this->weather_forecast['condition'] ?? '';
        
        return match ($weather) {
            'sunny' => '☀️',
            'cloudy' => '☁️',
            'rainy' => '🌧️',
            'stormy' => '⛈️',
            'snowy' => '❄️',
            default => '🌤️',
        };
    }

    public function getAttendanceStatusAttribute(): string
    {
        if (!$this->expected_attendance) {
            return 'Sin datos';
        }

        if ($this->expected_attendance < 100) {
            return 'Baja';
        } elseif ($this->expected_attendance < 500) {
            return 'Media';
        } else {
            return 'Alta';
        }
    }
}
