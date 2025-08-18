<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Instalación energética para autoconsumo.
 * 
 * Representa una instalación de generación energética como placas solares,
 * aerogeneradores, sistemas de biomasa, etc. para autoconsumo residencial o industrial.
 * 
 * @property int $id
 * @property string $name Nombre de la instalación
 * @property string $type Tipo de instalación (solar, wind, hydro, biomass, other)
 * @property float $capacity_kw Capacidad en kilovatios
 * @property string $location Ubicación de la instalación
 * @property int|null $owner_id ID del propietario (usuario)
 * @property \Carbon\Carbon|null $commissioned_at Fecha de puesta en marcha
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * 
 * @property-read \App\Models\User|null $owner Propietario de la instalación
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EnergyTransaction[] $energyTransactions Transacciones energéticas
 */
class EnergyInstallation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'type', 'capacity_kw', 'location', 'owner_id', 'commissioned_at',
    ];

    protected $casts = [
        'capacity_kw' => 'float',
        'commissioned_at' => 'datetime',
    ];

    /**
     * Relación con el propietario de la instalación.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Relación con las transacciones energéticas de esta instalación.
     */
    public function energyTransactions()
    {
        return $this->hasMany(EnergyTransaction::class, 'installation_id');
    }

    /**
     * Scope para filtrar por tipo de instalación.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope para filtrar por capacidad mínima.
     */
    public function scopeMinCapacity($query, $capacity)
    {
        return $query->where('capacity_kw', '>=', $capacity);
    }

    /**
     * Scope para filtrar por capacidad máxima.
     */
    public function scopeMaxCapacity($query, $capacity)
    {
        return $query->where('capacity_kw', '<=', $capacity);
    }

    /**
     * Scope para instalaciones ya comisionadas.
     */
    public function scopeCommissioned($query)
    {
        return $query->whereNotNull('commissioned_at')
                    ->where('commissioned_at', '<=', now());
    }

    /**
     * Scope para instalaciones en construcción/planificación.
     */
    public function scopeInDevelopment($query)
    {
        return $query->where(function($q) {
            $q->whereNull('commissioned_at')
              ->orWhere('commissioned_at', '>', now());
        });
    }

    /**
     * Obtener el estado de la instalación.
     */
    public function getStatusAttribute()
    {
        if (!$this->commissioned_at) {
            return 'planificación';
        }
        
        if ($this->commissioned_at > now()) {
            return 'construcción';
        }
        
        return 'operativa';
    }

    /**
     * Obtener el nombre del tipo de instalación en español.
     */
    public function getTypeNameAttribute()
    {
        $types = [
            'solar' => 'Fotovoltaica',
            'wind' => 'Eólica',
            'hydro' => 'Hidráulica',
            'biomass' => 'Biomasa',
            'other' => 'Otro',
        ];

        return $types[$this->type] ?? 'Desconocido';
    }

    /**
     * Obtener producción total estimada por mes (kWh).
     */
    public function getEstimatedMonthlyProductionAttribute()
    {
        // Factores de capacidad típicos por tipo de instalación en España
        $capacityFactors = [
            'solar' => 0.16, // 16% factor de capacidad para solar en España
            'wind' => 0.25,  // 25% factor de capacidad para eólica
            'hydro' => 0.40, // 40% factor de capacidad para hidráulica
            'biomass' => 0.60, // 60% factor de capacidad para biomasa
            'other' => 0.20, // 20% factor promedio
        ];

        $factor = $capacityFactors[$this->type] ?? 0.20;
        
        // kW * factor * 24h * 30días = kWh/mes
        return round($this->capacity_kw * $factor * 24 * 30, 2);
    }
}
