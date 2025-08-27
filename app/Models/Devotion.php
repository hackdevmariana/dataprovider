<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Devotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'saint_id',
        'name',
        'description',
        'prayer_text',
        'novena_days',
        'special_intentions',
        'miracles',
        'origin',
        'popularity_level',
        'practices',
        'traditions',
        'is_approved',
    ];

    protected $casts = [
        'novena_days' => 'array',
        'special_intentions' => 'array',
        'miracles' => 'array',
        'practices' => 'array',
        'traditions' => 'array',
        'is_approved' => 'boolean',
    ];

    // Relaciones
    public function saint(): BelongsTo
    {
        return $this->belongsTo(CatholicSaint::class, 'saint_id');
    }

    // Atributos calculados
    public function getPopularityLabelAttribute(): string
    {
        return match ($this->popularity_level) {
            'very_high' => 'Muy Alta',
            'high' => 'Alta',
            'medium' => 'Media',
            'low' => 'Baja',
            'very_low' => 'Muy Baja',
            default => 'Sin especificar',
        };
    }

    public function getPopularityColorAttribute(): string
    {
        return match ($this->popularity_level) {
            'very_high' => 'danger',
            'high' => 'warning',
            'medium' => 'info',
            'low' => 'success',
            'very_low' => 'secondary',
            default => 'gray',
        };
    }

    public function getNovenaDaysCountAttribute(): int
    {
        if ($this->novena_days && is_array($this->novena_days)) {
            return count($this->novena_days);
        }
        return 0;
    }

    public function getSpecialIntentionsCountAttribute(): int
    {
        if ($this->special_intentions && is_array($this->special_intentions)) {
            return count($this->special_intentions);
        }
        return 0;
    }

    public function getMiraclesCountAttribute(): int
    {
        if ($this->miracles && is_array($this->miracles)) {
            return count($this->miracles);
        }
        return 0;
    }

    public function getPracticesCountAttribute(): int
    {
        if ($this->practices && is_array($this->practices)) {
            return count($this->practices);
        }
        return 0;
    }

    public function getTraditionsCountAttribute(): int
    {
        if ($this->traditions && is_array($this->traditions)) {
            return count($this->traditions);
        }
        return 0;
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_approved ? 'Aprobada' : 'Pendiente';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->is_approved ? 'success' : 'warning';
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    public function scopeByPopularity($query, string $level)
    {
        return $query->where('popularity_level', $level);
    }

    public function scopeHighPopularity($query)
    {
        return $query->whereIn('popularity_level', ['very_high', 'high']);
    }

    public function scopeBySaint($query, int $saintId)
    {
        return $query->where('saint_id', $saintId);
    }

    public function scopeWithMiracles($query)
    {
        return $query->whereJsonLength('miracles', '>', 0);
    }

    public function scopeWithNovena($query)
    {
        return $query->whereJsonLength('novena_days', '>', 0);
    }

    // Métodos
    public function isApproved(): bool
    {
        return $this->is_approved;
    }

    public function isPopular(): bool
    {
        return in_array($this->popularity_level, ['very_high', 'high']);
    }

    public function hasMiracles(): bool
    {
        return $this->miracles_count > 0;
    }

    public function hasNovena(): bool
    {
        return $this->novena_days_count > 0;
    }

    public function hasSpecialIntentions(): bool
    {
        return $this->special_intentions_count > 0;
    }

    public function hasPractices(): bool
    {
        return $this->practices_count > 0;
    }

    public function hasTraditions(): bool
    {
        return $this->traditions_count > 0;
    }

    public function getNovenaDuration(): int
    {
        return $this->novena_days_count;
    }

    public function isNovenaDevotion(): bool
    {
        return $this->hasNovena();
    }
}
