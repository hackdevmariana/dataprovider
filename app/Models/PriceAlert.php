<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'energy_type',
        'zone',
        'alert_type',
        'threshold_price',
        'condition',
        'is_active',
        'last_triggered',
        'trigger_count',
        'notification_settings',
        'frequency',
    ];

    protected $casts = [
        'threshold_price' => 'decimal:4',
        'is_active' => 'boolean',
        'last_triggered' => 'datetime',
        'trigger_count' => 'integer',
        'notification_settings' => 'array',
    ];

    // Relaciones
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Atributos calculados
    public function getEnergyTypeLabelAttribute(): string
    {
        return match ($this->energy_type) {
            'electricity' => 'Electricidad',
            'gas' => 'Gas Natural',
            'oil' => 'Petróleo',
            'coal' => 'Carbón',
            'solar' => 'Solar',
            'wind' => 'Eólico',
            'hydro' => 'Hidroeléctrico',
            'nuclear' => 'Nuclear',
            'biomass' => 'Biomasa',
            'geothermal' => 'Geotérmico',
            'hybrid' => 'Híbrido',
            'all' => 'Todos',
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
            'solar' => 'success',
            'wind' => 'info',
            'hydro' => 'primary',
            'nuclear' => 'danger',
            'biomass' => 'success',
            'geothermal' => 'warning',
            'hybrid' => 'primary',
            'all' => 'light',
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
            'national' => 'Nacional',
            'international' => 'Internacional',
            default => 'Desconocida',
        };
    }

    public function getAlertTypeLabelAttribute(): string
    {
        return match ($this->alert_type) {
            'price_drop' => 'Bajada de Precio',
            'price_rise' => 'Subida de Precio',
            'price_threshold' => 'Umbral de Precio',
            'volatility' => 'Volatilidad',
            'spike' => 'Pico de Precio',
            'low_price' => 'Precio Bajo',
            'high_price' => 'Precio Alto',
            'average_price' => 'Precio Promedio',
            'forecast_change' => 'Cambio de Pronóstico',
            'market_alert' => 'Alerta de Mercado',
            default => 'Desconocido',
        };
    }

    public function getAlertTypeColorAttribute(): string
    {
        return match ($this->alert_type) {
            'price_drop' => 'success',
            'price_rise' => 'danger',
            'price_threshold' => 'warning',
            'volatility' => 'info',
            'spike' => 'danger',
            'low_price' => 'success',
            'high_price' => 'danger',
            'average_price' => 'info',
            'forecast_change' => 'warning',
            'market_alert' => 'primary',
            default => 'gray',
        };
    }

    public function getConditionLabelAttribute(): string
    {
        return match ($this->condition) {
            'below' => 'Por debajo de',
            'above' => 'Por encima de',
            'equals' => 'Igual a',
            'not_equals' => 'Diferente de',
            'between' => 'Entre',
            'outside' => 'Fuera de',
            default => 'Desconocido',
        };
    }

    public function getFrequencyLabelAttribute(): string
    {
        return match ($this->frequency) {
            'once' => 'Una vez',
            'daily' => 'Diario',
            'weekly' => 'Semanal',
            'monthly' => 'Mensual',
            'realtime' => 'Tiempo Real',
            'hourly' => 'Cada hora',
            'custom' => 'Personalizado',
            default => 'Desconocido',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Activa' : 'Inactiva';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->is_active ? 'success' : 'secondary';
    }

    public function getFormattedThresholdPriceAttribute(): string
    {
        return number_format($this->threshold_price, 4) . ' €/MWh';
    }

    public function getFormattedLastTriggeredAttribute(): string
    {
        return $this->last_triggered ? $this->last_triggered->format('d/m/Y H:i') : 'Nunca';
    }

    public function getIsRecentlyTriggeredAttribute(): bool
    {
        if (!$this->last_triggered) {
            return false;
        }
        return $this->last_triggered->diffInDays(now()) <= 7;
    }

    public function getIsFrequentlyTriggeredAttribute(): bool
    {
        return $this->trigger_count >= 10;
    }

    public function getIsNeverTriggeredAttribute(): bool
    {
        return $this->trigger_count === 0;
    }

    public function getNotificationChannelsAttribute(): array
    {
        if (is_array($this->notification_settings)) {
            return $this->notification_settings['channels'] ?? [];
        }
        return [];
    }

    public function getNotificationChannelsCountAttribute(): int
    {
        return count($this->notification_channels);
    }

    public function getHasEmailNotificationAttribute(): bool
    {
        return in_array('email', $this->notification_channels);
    }

    public function getHasSmsNotificationAttribute(): bool
    {
        return in_array('sms', $this->notification_channels);
    }

    public function getHasPushNotificationAttribute(): bool
    {
        return in_array('push', $this->notification_channels);
    }

    public function getHasWebhookAttribute(): bool
    {
        return in_array('webhook', $this->notification_channels);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeByEnergyType($query, string $type)
    {
        return $query->where('energy_type', $type);
    }

    public function scopeByZone($query, string $zone)
    {
        return $query->where('zone', $zone);
    }

    public function scopeByAlertType($query, string $type)
    {
        return $query->where('alert_type', $type);
    }

    public function scopeByCondition($query, string $condition)
    {
        return $query->where('condition', $condition);
    }

    public function scopeByFrequency($query, string $frequency)
    {
        return $query->where('frequency', $frequency);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeTriggered($query)
    {
        return $query->where('trigger_count', '>', 0);
    }

    public function scopeNeverTriggered($query)
    {
        return $query->where('trigger_count', 0);
    }

    public function scopeRecentlyTriggered($query, int $days = 7)
    {
        return $query->where('last_triggered', '>=', now()->subDays($days));
    }

    public function scopeFrequentlyTriggered($query, int $minCount = 10)
    {
        return $query->where('trigger_count', '>=', $minCount);
    }

    public function scopeByThresholdPrice($query, float $minPrice, float $maxPrice = null)
    {
        if ($maxPrice) {
            return $query->whereBetween('threshold_price', [$minPrice, $maxPrice]);
        }
        return $query->where('threshold_price', '>=', $minPrice);
    }

    public function scopeOrderByTriggerCount($query, string $direction = 'desc')
    {
        return $query->orderBy('trigger_count', $direction);
    }

    public function scopeOrderByLastTriggered($query, string $direction = 'desc')
    {
        return $query->orderBy('last_triggered', $direction);
    }

    public function scopeOrderByThresholdPrice($query, string $direction = 'asc')
    {
        return $query->orderBy('threshold_price', $direction);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('energy_type', 'like', '%' . $search . '%')
              ->orWhere('zone', 'like', '%' . $search . '%')
              ->orWhere('alert_type', 'like', '%' . $search . '%');
        });
    }

    // Métodos
    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isTriggered(): bool
    {
        return $this->trigger_count > 0;
    }

    public function isRecentlyTriggered(): bool
    {
        return $this->is_recently_triggered;
    }

    public function isFrequentlyTriggered(): bool
    {
        return $this->is_frequently_triggered;
    }

    public function isNeverTriggered(): bool
    {
        return $this->is_never_triggered;
    }

    public function hasEmailNotification(): bool
    {
        return $this->has_email_notification;
    }

    public function hasSmsNotification(): bool
    {
        return $this->has_sms_notification;
    }

    public function hasPushNotification(): bool
    {
        return $this->has_push_notification;
    }

    public function hasWebhook(): bool
    {
        return $this->has_webhook;
    }

    public function getNotificationChannelsList(): array
    {
        return $this->notification_channels;
    }

    public function getNotificationSettingsList(): array
    {
        if (is_array($this->notification_settings)) {
            return $this->notification_settings;
        }
        return [];
    }

    public function trigger(): void
    {
        $this->update([
            'last_triggered' => now(),
            'trigger_count' => $this->trigger_count + 1,
        ]);
    }

    public function resetTriggerCount(): void
    {
        $this->update(['trigger_count' => 0]);
    }

    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    public function getAlertDescriptionAttribute(): string
    {
        $energyType = $this->energy_type_label;
        $zone = $this->zone_label;
        $condition = $this->condition_label;
        $price = $this->formatted_threshold_price;
        
        return "Alerta de {$energyType} en {$zone}: {$condition} {$price}";
    }
}