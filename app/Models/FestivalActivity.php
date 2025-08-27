<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class FestivalActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'festival_id',
        'name',
        'type',
        'description',
        'start_time',
        'duration_minutes',
        'location',
        'organizer',
        'max_participants',
        'age_restriction',
        'requirements',
        'materials_provided',
        'requires_registration',
        'participation_fee',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'duration_minutes' => 'integer',
        'max_participants' => 'integer',
        'materials_provided' => 'array',
        'requires_registration' => 'boolean',
        'participation_fee' => 'decimal:2',
    ];

    // Relaciones
    public function festival(): BelongsTo
    {
        return $this->belongsTo(Festival::class);
    }

    // Atributos calculados
    public function getEndTimeAttribute(): ?Carbon
    {
        if ($this->start_time && $this->duration_minutes) {
            return $this->start_time->copy()->addMinutes($this->duration_minutes);
        }
        return null;
    }

    public function getFormattedDurationAttribute(): string
    {
        if ($this->duration_minutes) {
            $hours = intval($this->duration_minutes / 60);
            $minutes = $this->duration_minutes % 60;
            
            if ($hours > 0) {
                return $hours . 'h ' . $minutes . 'm';
            }
            return $minutes . 'm';
        }
        return '';
    }

    public function getIsFreeAttribute(): bool
    {
        return $this->participation_fee == 0;
    }

    public function getFormattedTimeAttribute(): string
    {
        if ($this->start_time) {
            return $this->start_time->format('H:i');
        }
        return '';
    }

    // Scopes
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeFree($query)
    {
        return $query->where('participation_fee', 0);
    }

    public function scopePaid($query)
    {
        return $query->where('participation_fee', '>', 0);
    }

    public function scopeRequiresRegistration($query)
    {
        return $query->where('requires_registration', true);
    }

    public function scopeByAgeRestriction($query, int $age)
    {
        return $query->where('age_restriction', '<=', $age);
    }

    // Métodos
    public function isRegistrationRequired(): bool
    {
        return $this->requires_registration;
    }

    public function isFree(): bool
    {
        return $this->is_free;
    }

    public function getFormattedFeeAttribute(): string
    {
        if ($this->is_free) {
            return 'Gratis';
        }
        return number_format($this->participation_fee, 2) . ' €';
    }
}
