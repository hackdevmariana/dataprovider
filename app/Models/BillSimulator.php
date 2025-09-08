<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BillSimulator extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'energy_type',
        'zone',
        'monthly_consumption',
        'consumption_unit',
        'contract_type',
        'power_contracted',
        'tariff_details',
        'estimated_monthly_bill',
        'estimated_annual_bill',
        'breakdown',
        'simulation_date',
        'assumptions',
    ];

    protected $casts = [
        'tariff_details' => 'array',
        'breakdown' => 'array',
        'assumptions' => 'array',
        'simulation_date' => 'datetime',
        'monthly_consumption' => 'decimal:2',
        'power_contracted' => 'decimal:2',
        'estimated_monthly_bill' => 'decimal:2',
        'estimated_annual_bill' => 'decimal:2',
    ];

    /**
     * Relación con el usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para filtrar por tipo de energía
     */
    public function scopeEnergyType($query, string $type)
    {
        return $query->where('energy_type', $type);
    }

    /**
     * Scope para filtrar por zona
     */
    public function scopeZone($query, string $zone)
    {
        return $query->where('zone', $zone);
    }

    /**
     * Scope para filtrar por tipo de contrato
     */
    public function scopeContractType($query, string $type)
    {
        return $query->where('contract_type', $type);
    }

    /**
     * Scope para simulaciones recientes
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('simulation_date', '>=', now()->subDays($days));
    }

    /**
     * Accessor para el tipo de energía formateado
     */
    public function getEnergyTypeFormattedAttribute(): string
    {
        return match ($this->energy_type) {
            'electricity' => '⚡ Electricidad',
            'gas' => '🔥 Gas Natural',
            default => $this->energy_type,
        };
    }

    /**
     * Accessor para la zona formateada
     */
    public function getZoneFormattedAttribute(): string
    {
        return match ($this->zone) {
            'peninsula' => '🏔️ Península',
            'canary_islands' => '🏝️ Islas Canarias',
            'balearic_islands' => '🏖️ Islas Baleares',
            default => $this->zone,
        };
    }

    /**
     * Accessor para el tipo de contrato formateado
     */
    public function getContractTypeFormattedAttribute(): string
    {
        return match ($this->contract_type) {
            'fixed' => '🔒 Tarifa Fija',
            'variable' => '📈 Tarifa Variable',
            default => $this->contract_type,
        };
    }

    /**
     * Accessor para el consumo formateado
     */
    public function getConsumptionFormattedAttribute(): string
    {
        return number_format($this->monthly_consumption, 2) . ' ' . $this->consumption_unit;
    }

    /**
     * Accessor para la factura mensual formateada
     */
    public function getMonthlyBillFormattedAttribute(): string
    {
        return '€' . number_format($this->estimated_monthly_bill, 2);
    }

    /**
     * Accessor para la factura anual formateada
     */
    public function getAnnualBillFormattedAttribute(): string
    {
        return '€' . number_format($this->estimated_annual_bill, 2);
    }
}
