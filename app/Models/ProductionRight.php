<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Sistema de derechos de producción energética.
 * 
 * Permite la compra/venta de derechos sobre la producción
 * energética de instalaciones solares.
 */
class ProductionRight extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'buyer_id',
        'installation_id',
        'project_proposal_id',
        'title',
        'slug',
        'description',
        'right_identifier',
        'right_type',
        'total_capacity_kw',
        'available_capacity_kw',
        'reserved_capacity_kw',
        'sold_capacity_kw',
        'estimated_annual_production_kwh',
        'guaranteed_annual_production_kwh',
        'actual_annual_production_kwh',
        'valid_from',
        'valid_until',
        'duration_years',
        'renewable_right',
        'renewal_period_years',
        'pricing_model',
        'price_per_kwh',
        'market_premium_percentage',
        'minimum_guaranteed_price',
        'maximum_price_cap',
        'price_escalation_terms',
        'upfront_payment',
        'periodic_payment',
        'payment_frequency',
        'security_deposit',
        'payment_terms',
        'penalty_clauses',
        'production_guaranteed',
        'production_guarantee_percentage',
        'insurance_included',
        'insurance_details',
        'risk_allocation',
        'buyer_rights',
        'buyer_obligations',
        'seller_rights',
        'seller_obligations',
        'is_transferable',
        'max_transfers',
        'current_transfers',
        'transfer_restrictions',
        'transfer_fee_percentage',
        'status',
        'status_notes',
        'contract_signed_at',
        'activated_at',
        'current_month_production_kwh',
        'ytd_production_kwh',
        'lifetime_production_kwh',
        'performance_ratio',
        'monthly_production_history',
        'regulatory_framework',
        'applicable_regulations',
        'grid_code_compliant',
        'certifications',
        'legal_documents',
        'contract_template_version',
        'electronic_signature_valid',
        'signature_details',
        'views_count',
        'inquiries_count',
        'offers_received',
        'highest_offer_price',
        'average_market_price',
        'is_active',
        'is_featured',
        'auto_accept_offers',
        'auto_accept_threshold',
        'allow_partial_sales',
        'minimum_sale_capacity_kw',
    ];

    protected $casts = [
        'total_capacity_kw' => 'decimal:2',
        'available_capacity_kw' => 'decimal:2',
        'reserved_capacity_kw' => 'decimal:2',
        'sold_capacity_kw' => 'decimal:2',
        'estimated_annual_production_kwh' => 'decimal:2',
        'guaranteed_annual_production_kwh' => 'decimal:2',
        'actual_annual_production_kwh' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'renewable_right' => 'boolean',
        'price_per_kwh' => 'decimal:4',
        'market_premium_percentage' => 'decimal:2',
        'minimum_guaranteed_price' => 'decimal:4',
        'maximum_price_cap' => 'decimal:4',
        'price_escalation_terms' => 'array',
        'upfront_payment' => 'decimal:2',
        'periodic_payment' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'payment_terms' => 'array',
        'penalty_clauses' => 'array',
        'production_guaranteed' => 'boolean',
        'production_guarantee_percentage' => 'decimal:2',
        'insurance_included' => 'boolean',
        'risk_allocation' => 'array',
        'buyer_rights' => 'array',
        'buyer_obligations' => 'array',
        'seller_rights' => 'array',
        'seller_obligations' => 'array',
        'is_transferable' => 'boolean',
        'transfer_restrictions' => 'array',
        'transfer_fee_percentage' => 'decimal:2',
        'contract_signed_at' => 'datetime',
        'activated_at' => 'datetime',
        'current_month_production_kwh' => 'decimal:2',
        'ytd_production_kwh' => 'decimal:2',
        'lifetime_production_kwh' => 'decimal:2',
        'performance_ratio' => 'decimal:2',
        'monthly_production_history' => 'array',
        'applicable_regulations' => 'array',
        'grid_code_compliant' => 'boolean',
        'certifications' => 'array',
        'legal_documents' => 'array',
        'electronic_signature_valid' => 'boolean',
        'signature_details' => 'array',
        'highest_offer_price' => 'decimal:4',
        'average_market_price' => 'decimal:4',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'auto_accept_offers' => 'boolean',
        'auto_accept_threshold' => 'decimal:4',
        'allow_partial_sales' => 'boolean',
        'minimum_sale_capacity_kw' => 'decimal:2',
    ];

    /**
     * Usuario vendedor del derecho.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Usuario comprador del derecho.
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Instalación energética asociada.
     */
    public function installation(): BelongsTo
    {
        return $this->belongsTo(EnergyInstallation::class, 'installation_id');
    }

    /**
     * Proyecto asociado al derecho.
     */
    public function projectProposal(): BelongsTo
    {
        return $this->belongsTo(ProjectProposal::class);
    }

    /**
     * Boot del modelo.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($right) {
            if (empty($right->slug)) {
                $right->slug = static::generateUniqueSlug($right->title);
            }
            
            if (empty($right->right_identifier)) {
                $right->right_identifier = static::generateRightIdentifier();
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
     * Generar identificador único del derecho.
     */
    public static function generateRightIdentifier(): string
    {
        do {
            $identifier = 'PR-' . now()->format('Y') . '-' . strtoupper(Str::random(8));
        } while (static::where('right_identifier', $identifier)->exists());

        return $identifier;
    }

    /**
     * Verificar si el derecho está disponible para venta.
     */
    public function isAvailableForSale(): bool
    {
        return $this->status === 'available' &&
               $this->is_active &&
               $this->available_capacity_kw > 0 &&
               $this->valid_from <= now()->toDateString() &&
               ($this->valid_until === null || $this->valid_until >= now()->toDateString());
    }

    /**
     * Calcular precio actual según modelo de pricing.
     */
    public function getCurrentPrice(): float
    {
        return match($this->pricing_model) {
            'fixed_price_kwh' => $this->price_per_kwh,
            'market_price' => $this->average_market_price ?? 0.15,
            'premium_over_market' => ($this->average_market_price ?? 0.15) * (1 + ($this->market_premium_percentage / 100)),
            default => $this->price_per_kwh ?? 0,
        };
    }

    /**
     * Calcular valor total del derecho.
     */
    public function getTotalValue(): float
    {
        $currentPrice = $this->getCurrentPrice();
        $annualProduction = $this->estimated_annual_production_kwh ?? 0;
        $duration = $this->duration_years ?? 20;
        
        return $currentPrice * $annualProduction * $duration;
    }

    /**
     * Reservar capacidad.
     */
    public function reserveCapacity(float $capacityKw, User $buyer): bool
    {
        if ($capacityKw > $this->available_capacity_kw) {
            return false;
        }

        $this->decrement('available_capacity_kw', $capacityKw);
        $this->increment('reserved_capacity_kw', $capacityKw);
        
        // Si no hay comprador asignado, asignarlo
        if (!$this->buyer_id) {
            $this->update(['buyer_id' => $buyer->id]);
        }

        return true;
    }

    /**
     * Confirmar venta.
     */
    public function confirmSale(float $capacityKw): bool
    {
        if ($capacityKw > $this->reserved_capacity_kw) {
            return false;
        }

        $this->decrement('reserved_capacity_kw', $capacityKw);
        $this->increment('sold_capacity_kw', $capacityKw);
        
        // Si se vendió toda la capacidad, cambiar estado
        if ($this->available_capacity_kw <= 0 && $this->reserved_capacity_kw <= 0) {
            $this->update(['status' => 'contracted']);
        }

        return true;
    }

    /**
     * Activar derecho (cuando la instalación comienza a producir).
     */
    public function activate(): void
    {
        $this->update([
            'status' => 'active',
            'activated_at' => now(),
        ]);
    }

    /**
     * Registrar producción mensual.
     */
    public function recordMonthlyProduction(float $productionKwh, string $month = null): void
    {
        $month = $month ?? now()->format('Y-m');
        
        $this->update([
            'current_month_production_kwh' => $productionKwh,
            'ytd_production_kwh' => $this->ytd_production_kwh + $productionKwh,
            'lifetime_production_kwh' => $this->lifetime_production_kwh + $productionKwh,
        ]);

        // Actualizar historial mensual
        $history = $this->monthly_production_history ?? [];
        $history[$month] = $productionKwh;
        $this->update(['monthly_production_history' => $history]);

        // Calcular ratio de rendimiento
        $this->updatePerformanceRatio();
    }

    /**
     * Actualizar ratio de rendimiento.
     */
    public function updatePerformanceRatio(): void
    {
        if (!$this->estimated_annual_production_kwh) {
            return;
        }

        $monthsActive = $this->activated_at ? 
            $this->activated_at->diffInMonths(now()) + 1 : 1;
        
        $expectedProduction = ($this->estimated_annual_production_kwh / 12) * $monthsActive;
        
        if ($expectedProduction > 0) {
            $ratio = ($this->ytd_production_kwh / $expectedProduction) * 100;
            $this->update(['performance_ratio' => round($ratio, 2)]);
        }
    }

    /**
     * Transferir derecho a otro usuario.
     */
    public function transfer(User $newBuyer, float $transferFee = null): bool
    {
        if (!$this->is_transferable || $this->current_transfers >= $this->max_transfers) {
            return false;
        }

        $fee = $transferFee ?? ($this->transfer_fee_percentage ? 
            $this->getTotalValue() * ($this->transfer_fee_percentage / 100) : 0);

        $this->update([
            'buyer_id' => $newBuyer->id,
            'current_transfers' => $this->current_transfers + 1,
        ]);

        return true;
    }

    /**
     * Obtener derechos disponibles.
     */
    public static function getAvailable(array $filters = [], int $limit = 20)
    {
        $query = static::where('status', 'available')
                      ->where('is_active', true)
                      ->where('available_capacity_kw', '>', 0);

        if (isset($filters['right_type'])) {
            $query->where('right_type', $filters['right_type']);
        }

        if (isset($filters['min_capacity'])) {
            $query->where('available_capacity_kw', '>=', $filters['min_capacity']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price_per_kwh', '<=', $filters['max_price']);
        }

        if (isset($filters['pricing_model'])) {
            $query->where('pricing_model', $filters['pricing_model']);
        }

        return $query->with(['seller', 'installation'])
                    ->orderBy('is_featured', 'desc')
                    ->orderBy('views_count', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Obtener derechos destacados.
     */
    public static function getFeatured(int $limit = 10)
    {
        return static::where('is_featured', true)
                    ->where('is_active', true)
                    ->where('status', 'available')
                    ->with(['seller', 'installation'])
                    ->orderBy('views_count', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Buscar derechos.
     */
    public static function search(string $term, array $filters = [], int $limit = 20)
    {
        $query = static::where('is_active', true)
                      ->where('status', 'available')
                      ->where(function ($q) use ($term) {
                          $q->where('title', 'like', "%{$term}%")
                            ->orWhere('description', 'like', "%{$term}%")
                            ->orWhere('right_identifier', 'like', "%{$term}%");
                      });

        foreach ($filters as $key => $value) {
            if ($value !== null) {
                $query->where($key, $value);
            }
        }

        return $query->with(['seller', 'installation'])
                    ->orderBy('views_count', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Obtener estadísticas del mercado de derechos.
     */
    public static function getMarketStats(): array
    {
        return [
            'total_rights' => static::count(),
            'available_rights' => static::where('status', 'available')->count(),
            'active_rights' => static::where('status', 'active')->count(),
            'total_capacity_kw' => static::sum('total_capacity_kw'),
            'available_capacity_kw' => static::sum('available_capacity_kw'),
            'average_price_per_kwh' => static::whereNotNull('price_per_kwh')->avg('price_per_kwh'),
            'by_right_type' => static::selectRaw('right_type, COUNT(*) as count')
                                   ->groupBy('right_type')
                                   ->pluck('count', 'right_type'),
            'by_pricing_model' => static::selectRaw('pricing_model, COUNT(*) as count')
                                      ->groupBy('pricing_model')
                                      ->pluck('count', 'pricing_model'),
        ];
    }
}
