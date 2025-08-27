<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PilgrimageSite extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'saint_id',
        'location',
        'latitude',
        'longitude',
        'country',
        'region',
        'city',
        'type',
        'facilities',
        'accommodation',
        'transportation',
        'best_time_to_visit',
        'annual_pilgrims',
        'special_dates',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'facilities' => 'array',
        'accommodation' => 'array',
        'transportation' => 'array',
        'special_dates' => 'array',
        'annual_pilgrims' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relaciones
    public function saint(): BelongsTo
    {
        return $this->belongsTo(CatholicSaint::class, 'saint_id');
    }

    // Atributos calculados
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'shrine' => 'Santuario',
            'basilica' => 'Basílica',
            'cathedral' => 'Catedral',
            'church' => 'Iglesia',
            'monastery' => 'Monasterio',
            'convent' => 'Convento',
            'hermitage' => 'Ermita',
            'chapel' => 'Capilla',
            default => 'Otro',
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'shrine' => 'danger',
            'basilica' => 'warning',
            'cathedral' => 'info',
            'church' => 'success',
            'monastery' => 'secondary',
            'convent' => 'primary',
            'hermitage' => 'gray',
            'chapel' => 'light',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Activo' : 'Inactivo';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->is_active ? 'success' : 'secondary';
    }

    public function getAnnualPilgrimsFormattedAttribute(): string
    {
        if (!$this->annual_pilgrims) {
            return 'Sin datos';
        }

        if ($this->annual_pilgrims >= 1000000) {
            return round($this->annual_pilgrims / 1000000, 1) . 'M';
        } elseif ($this->annual_pilgrims >= 1000) {
            return round($this->annual_pilgrims / 1000, 1) . 'K';
        }

        return number_format($this->annual_pilgrims);
    }

    public function getFacilitiesCountAttribute(): int
    {
        if (is_array($this->facilities)) {
            return count($this->facilities);
        }
        return 0;
    }

    public function getAccommodationCountAttribute(): int
    {
        if (is_array($this->accommodation)) {
            return count($this->accommodation);
        }
        return 0;
    }

    public function getTransportationCountAttribute(): int
    {
        if (is_array($this->transportation)) {
            return count($this->transportation);
        }
        return 0;
    }

    public function getSpecialDatesCountAttribute(): int
    {
        if (is_array($this->special_dates)) {
            return count($this->special_dates);
        }
        return 0;
    }

    public function getCoordinatesAttribute(): string
    {
        if ($this->latitude && $this->longitude) {
            return $this->latitude . ', ' . $this->longitude;
        }
        return 'Sin coordenadas';
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

    public function scopeByCountry($query, string $country)
    {
        return $query->where('country', $country);
    }

    public function scopeByRegion($query, string $region)
    {
        return $query->where('region', $region);
    }

    public function scopeByCity($query, string $city)
    {
        return $query->where('city', $city);
    }

    public function scopeWithSaint($query)
    {
        return $query->whereNotNull('saint_id');
    }

    public function scopePopular($query, int $minPilgrims = 10000)
    {
        return $query->where('annual_pilgrims', '>=', $minPilgrims);
    }

    public function scopeNearby($query, float $lat, float $lng, float $radiusKm = 50)
    {
        // Fórmula de Haversine para calcular distancia
        $sql = "(
            6371 * acos(
                cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) +
                sin(radians(?)) * sin(radians(latitude))
            )
        ) <= ?";
        
        return $query->whereRaw($sql, [$lat, $lng, $lat, $radiusKm]);
    }

    // Métodos
    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function hasSaint(): bool
    {
        return !is_null($this->saint_id);
    }

    public function hasCoordinates(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    public function isPopular(): bool
    {
        return $this->annual_pilgrims >= 10000;
    }

    public function hasFacilities(): bool
    {
        return $this->facilities_count > 0;
    }

    public function hasAccommodation(): bool
    {
        return $this->accommodation_count > 0;
    }

    public function hasTransportation(): bool
    {
        return $this->transportation_count > 0;
    }

    public function hasSpecialDates(): bool
    {
        return $this->special_dates_count > 0;
    }

    public function getDistanceFrom(float $lat, float $lng): ?float
    {
        if (!$this->hasCoordinates()) {
            return null;
        }

        $earthRadius = 6371; // Radio de la Tierra en km
        
        $latDiff = deg2rad($lat - $this->latitude);
        $lngDiff = deg2rad($lng - $this->longitude);
        
        $a = sin($latDiff / 2) * sin($latDiff / 2) +
             cos(deg2rad($this->latitude)) * cos(deg2rad($lat)) *
             sin($lngDiff / 2) * sin($lngDiff / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }

    public function getFormattedDistanceFrom(float $lat, float $lng): string
    {
        $distance = $this->getDistanceFrom($lat, $lng);
        if ($distance === null) {
            return 'Sin coordenadas';
        }

        if ($distance < 1) {
            return round($distance * 1000) . 'm';
        }

        return round($distance, 1) . 'km';
    }
}
