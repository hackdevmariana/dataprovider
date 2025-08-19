<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Marketplace de techos y espacios para instalaciones solares.
 * 
 * Permite a los propietarios ofertar sus techos/espacios
 * para instalaciones solares colaborativas.
 */
class RoofMarketplace extends Model
{
    use HasFactory;

    protected $table = 'roof_marketplace';

    protected $fillable = [
        'owner_id',
        'municipality_id',
        'title',
        'slug',
        'description',
        'space_type',
        'address',
        'latitude',
        'longitude',
        'postal_code',
        'access_instructions',
        'nearby_landmarks',
        'total_area_m2',
        'usable_area_m2',
        'max_installable_power_kw',
        'roof_orientation',
        'roof_inclination_degrees',
        'roof_material',
        'roof_condition',
        'roof_age_years',
        'max_load_capacity_kg_m2',
        'annual_solar_irradiation_kwh_m2',
        'annual_sunny_days',
        'shading_analysis',
        'has_shading_issues',
        'shading_description',
        'access_difficulty',
        'access_description',
        'crane_access',
        'vehicle_access',
        'distance_to_electrical_panel_m',
        'has_building_permits',
        'community_approval_required',
        'community_approval_obtained',
        'required_permits',
        'obtained_permits',
        'legal_restrictions',
        'offering_type',
        'monthly_rent_eur',
        'sale_price_eur',
        'energy_share_percentage',
        'contract_duration_years',
        'renewable_contract',
        'additional_terms',
        'includes_maintenance',
        'includes_insurance',
        'includes_permits_management',
        'includes_monitoring',
        'included_services',
        'additional_costs',
        'availability_status',
        'available_from',
        'available_until',
        'availability_notes',
        'owner_lives_onsite',
        'owner_involvement',
        'owner_preferences',
        'owner_requirements',
        'views_count',
        'inquiries_count',
        'bookmarks_count',
        'rating',
        'reviews_count',
        'images',
        'documents',
        'technical_reports',
        'solar_analysis_reports',
        'is_active',
        'is_featured',
        'is_verified',
        'verified_by',
        'verified_at',
        'auto_respond_inquiries',
        'auto_response_message',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'nearby_landmarks' => 'array',
        'total_area_m2' => 'decimal:2',
        'usable_area_m2' => 'decimal:2',
        'max_installable_power_kw' => 'decimal:2',
        'max_load_capacity_kg_m2' => 'decimal:2',
        'annual_solar_irradiation_kwh_m2' => 'decimal:2',
        'shading_analysis' => 'array',
        'has_shading_issues' => 'boolean',
        'crane_access' => 'boolean',
        'vehicle_access' => 'boolean',
        'distance_to_electrical_panel_m' => 'decimal:2',
        'has_building_permits' => 'boolean',
        'community_approval_required' => 'boolean',
        'community_approval_obtained' => 'boolean',
        'required_permits' => 'array',
        'obtained_permits' => 'array',
        'monthly_rent_eur' => 'decimal:2',
        'sale_price_eur' => 'decimal:2',
        'energy_share_percentage' => 'decimal:2',
        'renewable_contract' => 'boolean',
        'additional_terms' => 'array',
        'includes_maintenance' => 'boolean',
        'includes_insurance' => 'boolean',
        'includes_permits_management' => 'boolean',
        'includes_monitoring' => 'boolean',
        'included_services' => 'array',
        'additional_costs' => 'array',
        'available_from' => 'date',
        'available_until' => 'date',
        'owner_lives_onsite' => 'boolean',
        'owner_preferences' => 'array',
        'rating' => 'decimal:1',
        'images' => 'array',
        'documents' => 'array',
        'technical_reports' => 'array',
        'solar_analysis_reports' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'auto_respond_inquiries' => 'boolean',
    ];

    /**
     * Propietario del espacio.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Municipio donde se ubica.
     */
    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    /**
     * Usuario que verificó el espacio.
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Boot del modelo.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($roof) {
            if (empty($roof->slug)) {
                $roof->slug = static::generateUniqueSlug($roof->title);
            }
        });
    }

    /**
     * Generar slug único.
     */
    public static function generateUniqueSlug(string $title): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Verificar si el espacio está disponible.
     */
    public function isAvailable(): bool
    {
        if (!$this->is_active || $this->availability_status !== 'available') {
            return false;
        }

        $now = now()->toDateString();

        if ($this->available_from && $this->available_from > $now) {
            return false;
        }

        if ($this->available_until && $this->available_until < $now) {
            return false;
        }

        return true;
    }

    /**
     * Calcular potencial energético estimado.
     */
    public function getEnergyPotential(): array
    {
        $usableArea = $this->usable_area_m2 ?? 0;
        $irradiation = $this->annual_solar_irradiation_kwh_m2 ?? 1400; // Media España
        
        // Asumiendo 200W/m² de densidad de instalación
        $estimatedPowerKw = $usableArea * 0.2;
        $estimatedAnnualProduction = $estimatedPowerKw * $irradiation;
        
        return [
            'estimated_power_kw' => round($estimatedPowerKw, 2),
            'estimated_annual_production_kwh' => round($estimatedAnnualProduction, 2),
            'estimated_co2_savings_kg' => round($estimatedAnnualProduction * 0.3, 2),
            'estimated_annual_savings_eur' => round($estimatedAnnualProduction * 0.15, 2),
        ];
    }

    /**
     * Calcular score de atractivo del espacio.
     */
    public function getAttractivenessScore(): float
    {
        $score = 0;

        // Área utilizable (hasta 30 puntos)
        $score += min(($this->usable_area_m2 ?? 0) / 10, 30);

        // Irradiación solar (hasta 25 puntos)
        if ($this->annual_solar_irradiation_kwh_m2) {
            $score += min(($this->annual_solar_irradiation_kwh_m2 - 1000) / 20, 25);
        }

        // Condición del techo (hasta 15 puntos)
        $conditionScores = [
            'excellent' => 15,
            'good' => 12,
            'fair' => 8,
            'needs_repair' => 4,
            'poor' => 0,
        ];
        $score += $conditionScores[$this->roof_condition] ?? 0;

        // Accesibilidad (hasta 10 puntos)
        $accessScores = [
            'easy' => 10,
            'moderate' => 7,
            'difficult' => 4,
            'very_difficult' => 1,
        ];
        $score += $accessScores[$this->access_difficulty] ?? 0;

        // Permisos (hasta 10 puntos)
        if ($this->has_building_permits) $score += 5;
        if (!$this->community_approval_required || $this->community_approval_obtained) $score += 5;

        // Problemas de sombreado (penalización)
        if ($this->has_shading_issues) $score -= 10;

        // Servicios incluidos (hasta 10 puntos)
        if ($this->includes_maintenance) $score += 3;
        if ($this->includes_insurance) $score += 3;
        if ($this->includes_permits_management) $score += 4;

        return max(0, min(100, $score));
    }

    /**
     * Incrementar vistas.
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Incrementar consultas.
     */
    public function incrementInquiries(): void
    {
        $this->increment('inquiries_count');
    }

    /**
     * Marcar como reservado.
     */
    public function reserve(): void
    {
        $this->update(['availability_status' => 'reserved']);
    }

    /**
     * Marcar como contratado.
     */
    public function contract(): void
    {
        $this->update(['availability_status' => 'contracted']);
    }

    /**
     * Verificar espacio.
     */
    public function verify(User $verifier): void
    {
        $this->update([
            'is_verified' => true,
            'verified_by' => $verifier->id,
            'verified_at' => now(),
        ]);
    }

    /**
     * Obtener espacios disponibles.
     */
    public static function getAvailable(array $filters = [], int $limit = 20)
    {
        $query = static::where('is_active', true)
                      ->where('availability_status', 'available');

        if (isset($filters['space_type'])) {
            $query->where('space_type', $filters['space_type']);
        }

        if (isset($filters['min_area'])) {
            $query->where('usable_area_m2', '>=', $filters['min_area']);
        }

        if (isset($filters['max_area'])) {
            $query->where('usable_area_m2', '<=', $filters['max_area']);
        }

        if (isset($filters['municipality_id'])) {
            $query->where('municipality_id', $filters['municipality_id']);
        }

        if (isset($filters['offering_type'])) {
            $query->where('offering_type', $filters['offering_type']);
        }

        if (isset($filters['max_rent'])) {
            $query->where('monthly_rent_eur', '<=', $filters['max_rent']);
        }

        if (isset($filters['verified_only']) && $filters['verified_only']) {
            $query->where('is_verified', true);
        }

        return $query->with(['owner', 'municipality'])
                    ->orderBy('is_featured', 'desc')
                    ->orderBy('views_count', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Obtener espacios destacados.
     */
    public static function getFeatured(int $limit = 10)
    {
        return static::where('is_featured', true)
                    ->where('is_active', true)
                    ->where('availability_status', 'available')
                    ->with(['owner', 'municipality'])
                    ->orderBy('views_count', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Buscar espacios por ubicación.
     */
    public static function searchNearby(float $latitude, float $longitude, float $radiusKm = 50, array $filters = [])
    {
        // Cálculo básico de distancia
        $latRange = $radiusKm / 111;
        $lngRange = $radiusKm / (111 * cos(deg2rad($latitude)));

        $query = static::where('is_active', true)
                      ->where('availability_status', 'available')
                      ->whereBetween('latitude', [$latitude - $latRange, $latitude + $latRange])
                      ->whereBetween('longitude', [$longitude - $lngRange, $longitude + $lngRange]);

        // Aplicar filtros adicionales
        foreach ($filters as $key => $value) {
            if ($value !== null) {
                $query->where($key, $value);
            }
        }

        return $query->with(['owner', 'municipality'])
                    ->get();
    }

    /**
     * Obtener estadísticas del marketplace.
     */
    public static function getMarketplaceStats(): array
    {
        return [
            'total_spaces' => static::count(),
            'available_spaces' => static::where('availability_status', 'available')->count(),
            'verified_spaces' => static::where('is_verified', true)->count(),
            'total_area_m2' => static::sum('usable_area_m2'),
            'estimated_total_power_kw' => static::sum('max_installable_power_kw'),
            'by_space_type' => static::selectRaw('space_type, COUNT(*) as count')
                                   ->groupBy('space_type')
                                   ->pluck('count', 'space_type'),
            'by_offering_type' => static::selectRaw('offering_type, COUNT(*) as count')
                                      ->groupBy('offering_type')
                                      ->pluck('count', 'offering_type'),
            'average_rating' => static::whereNotNull('rating')->avg('rating'),
        ];
    }
}
