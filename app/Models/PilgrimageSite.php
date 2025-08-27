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
            'chapel' => 'Capilla',
            'grotto' => 'Gruta',
            'cave' => 'Cueva',
            'mountain' => 'Montaña',
            'spring' => 'Manantial',
            'tree' => 'Árbol',
            default => 'Otro',
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'shrine' => '⛪',
            'basilica' => '🏛️',
            'cathedral' => '⛪',
            'church' => '⛪',
            'monastery' => '🏰',
            'convent' => '🏰',
            'chapel' => '⛪',
            'grotto' => '🕳️',
            'cave' => '🕳️',
            'mountain' => '⛰️',
            'spring' => '💧',
            'tree' => '🌳',
            default => '📍',
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

    public function getFacilitiesCountAttribute(): int
    {
        if ($this->facilities && is_array($this->facilities)) {
            return count($this->facilities);
        }
        return 0;
    }

    public function getAccommodationCountAttribute(): int
    {
        if ($this->accommodation && is_array($this->accommodation)) {
            return count($this->accommodation);
        }
        return 0;
    }

    public function getTransportationCountAttribute(): int
    {
        if ($this->transportation && is_array($this->transportation)) {
            return count($this->transportation);
        }
        return 0;
    }

    public function getSpecialDatesCountAttribute(): int
    {
        if ($this->special_dates && is_array($this->special_dates)) {
            return count($this->special_dates);
        }
        return 0;
    }

    public function getPilgrimsLabelAttribute(): string
    {
        if (!$this->annual_pilgrims) {
            return 'Sin datos';
        }

        if ($this->annual_pilgrims < 1000) {
            return 'Bajo';
        } elseif ($this->annual_pilgrims < 10000) {
            return 'Medio';
        } elseif ($this->annual_pilgrims < 100000) {
            return 'Alto';
        } else {
            return 'Muy Alto';
        }
    }

    public function getPilgrimsColorAttribute(): string
    {
        if (!$this->annual_pilgrims) {
            return 'gray';
        }

        if ($this->annual_pilgrims < 1000) {
            return 'success';
        } elseif ($this->annual_pilgrims < 10000) {
            return 'info';
        } elseif ($this->annual_pilgrims < 100000) {
            return 'warning';
        } else {
            return 'danger';
        }
    }

    public function getCoordinatesAttribute(): string
    {
        if ($this->latitude && $this->longitude) {
            return $this->latitude . ', ' . $this->longitude;
        }
        return 'Sin coordenadas';
    }

    public function getFullLocationAttribute(): string
    {
        $parts = array_filter([$this->city, $this->region, $this->country]);
        return implode(', ', $parts);
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

    public function scopeWithCoordinates($query)
    {
        return $query->whereNotNull('latitude')->whereNotNull('longitude');
    }

    public function scopeBySaint($query, int $saintId)
    {
        return $query->where('saint_id', $saintId);
    }

    public function scopeHighPilgrims($query, int $minPilgrims = 10000)
    {
        return $query->where('annual_pilgrims', '>=', $minPilgrims);
    }

    // Métodos
    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function hasCoordinates(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    public function hasSaint(): bool
    {
        return !is_null($this->saint_id);
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

    public function isPopular(): bool
    {
        return $this->annual_pilgrims && $this->annual_pilgrims >= 10000;
    }

    public function getDistanceFrom($lat, $lng): ?float
    {
        if (!$this->hasCoordinates()) {
            return null;
        }

        $lat1 = deg2rad($this->latitude);
        $lng1 = deg2rad($this->longitude);
        $lat2 = deg2rad($lat);
        $lng2 = deg2rad($lng);

        $dlat = $lat2 - $lat1;
        $dlng = $lng2 - $lng1;

        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return 6371 * $c; // Radio de la Tierra en km
    }
}
