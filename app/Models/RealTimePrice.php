<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RealTimePrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'energy_type',
        'zone',
        'timestamp',
        'price',
        'currency',
        'unit',
        'source',
        'data_quality',
        'additional_data',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'price' => 'decimal:4',
        'additional_data' => 'array',
    ];

    // Atributos calculados
    public function getEnergyTypeLabelAttribute(): string
    {
        return match ($this->energy_type) {
            'electricity' => 'Electricidad',
            'gas' => 'Gas',
            'oil' => 'Petróleo',
            'coal' => 'Carbón',
            'renewable' => 'Renovable',
            'nuclear' => 'Nuclear',
            default => 'Desconocido',
        };
    }

    public function getEnergyTypeColorAttribute(): string
    {
        return match ($this->energy_type) {
            'electricity' => 'warning',
            'gas' => 'info',
            'oil' => 'dark',
            'coal' => 'secondary',
            'renewable' => 'success',
            'nuclear' => 'danger',
            default => 'gray',
        };
    }

    public function getZoneLabelAttribute(): string
    {
        return match ($this->zone) {
            'peninsula' => 'Península',
            'canarias' => 'Canarias',
            'baleares' => 'Baleares',
            'ceuta' => 'Ceuta',
            'melilla' => 'Melilla',
            default => 'Desconocida',
        };
    }

    public function getDataQualityLabelAttribute(): string
    {
        return match ($this->data_quality) {
            'high' => 'Alta',
            'medium' => 'Media',
            'low' => 'Baja',
            'estimated' => 'Estimada',
            default => 'Desconocida',
        };
    }

    public function getDataQualityColorAttribute(): string
    {
        return match ($this->data_quality) {
            'high' => 'success',
            'medium' => 'warning',
            'low' => 'danger',
            'estimated' => 'info',
            default => 'gray',
        };
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 4) . ' ' . ($this->currency ?? 'EUR') . '/' . ($this->unit ?? 'MWh');
    }

    public function getFormattedTimestampAttribute(): string
    {
        return $this->timestamp->format('d/m/Y H:i:s');
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->timestamp->diffForHumans();
    }

    public function getIsRecentAttribute(): bool
    {
        return $this->timestamp->diffInMinutes(now()) <= 60;
    }

    public function getIsTodayAttribute(): bool
    {
        return $this->timestamp->isToday();
    }

    public function getIsThisHourAttribute(): bool
    {
        return $this->timestamp->diffInHours(now()) === 0;
    }

    public function getAdditionalDataCountAttribute(): int
    {
        if (is_array($this->additional_data)) {
            return count($this->additional_data);
        }
        return 0;
    }

    // Scopes
    public function scopeByEnergyType($query, string $type)
    {
        return $query->where('energy_type', $type);
    }

    public function scopeByZone($query, string $zone)
    {
        return $query->where('zone', $zone);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('timestamp', $date);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('timestamp', Carbon::today());
    }

    public function scopeThisHour($query)
    {
        return $query->where('timestamp', '>=', Carbon::now()->startOfHour());
    }

    public function scopeRecent($query, int $minutes = 60)
    {
        return $query->where('timestamp', '>=', Carbon::now()->subMinutes($minutes));
    }

    public function scopeByPriceRange($query, float $min, float $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    public function scopeHighPrice($query, float $minPrice)
    {
        return $query->where('price', '>=', $minPrice);
    }

    public function scopeLowPrice($query, float $maxPrice)
    {
        return $query->where('price', '<=', $maxPrice);
    }

    public function scopeBySource($query, string $source)
    {
        return $query->where('source', $source);
    }

    public function scopeByDataQuality($query, string $quality)
    {
        return $query->where('data_quality', $quality);
    }

    public function scopeHighQuality($query)
    {
        return $query->where('data_quality', 'high');
    }

    public function scopeOrderByPrice($query, string $direction = 'desc')
    {
        return $query->orderBy('price', $direction);
    }

    public function scopeOrderByTimestamp($query, string $direction = 'desc')
    {
        return $query->orderBy('timestamp', $direction);
    }

    // Métodos
    public function isRecent(): bool
    {
        return $this->is_recent;
    }

    public function isToday(): bool
    {
        return $this->is_today;
    }

    public function isThisHour(): bool
    {
        return $this->is_this_hour;
    }

    public function isHighQuality(): bool
    {
        return $this->data_quality === 'high';
    }

    public function isEstimated(): bool
    {
        return $this->data_quality === 'estimated';
    }

    public function hasAdditionalData(): bool
    {
        return $this->additional_data_count > 0;
    }

    public function getPriceInEuros(): float
    {
        if ($this->currency === 'EUR') {
            return $this->price;
        }
        // Aquí se podría implementar conversión de divisas
        return $this->price;
    }

    public function getPricePerKwh(): float
    {
        if ($this->unit === 'MWh') {
            return $this->price / 1000;
        }
        return $this->price;
    }

    public function getAdditionalDataList(): array
    {
        if (is_array($this->additional_data)) {
            return $this->additional_data;
        }
        return [];
    }

    public function getPriceTrend(): string
    {
        // Este método podría implementarse para calcular tendencias
        // comparando con precios anteriores
        return 'Estable';
    }

    public function isPeakHour(): bool
    {
        $hour = $this->timestamp->hour;
        // Horas pico típicas: 8-10h y 18-22h
        return ($hour >= 8 && $hour <= 10) || ($hour >= 18 && $hour <= 22);
    }

    public function isOffPeakHour(): bool
    {
        $hour = $this->timestamp->hour;
        // Horas valle típicas: 2-6h
        return $hour >= 2 && $hour <= 6;
    }

    public function getPriceCategory(): string
    {
        if ($this->price < 50) {
            return 'Muy Bajo';
        } elseif ($this->price < 100) {
            return 'Bajo';
        } elseif ($this->price < 150) {
            return 'Normal';
        } elseif ($this->price < 200) {
            return 'Alto';
        } else {
            return 'Muy Alto';
        }
    }

    public function getPriceCategoryColor(): string
    {
        if ($this->price < 50) {
            return 'success';
        } elseif ($this->price < 100) {
            return 'info';
        } elseif ($this->price < 150) {
            return 'warning';
        } elseif ($this->price < 200) {
            return 'danger';
        } else {
            return 'dark';
        }
    }
}
