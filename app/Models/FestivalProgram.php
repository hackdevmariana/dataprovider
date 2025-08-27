<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class FestivalProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'festival_id',
        'day',
        'start_time',
        'end_time',
        'event_name',
        'description',
        'location',
        'artist_id',
        'group_id',
        'event_type',
        'is_free',
        'ticket_price',
        'capacity',
        'current_attendance',
        'additional_info',
    ];

    protected $casts = [
        'day' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_free' => 'boolean',
        'ticket_price' => 'decimal:2',
        'capacity' => 'integer',
        'current_attendance' => 'integer',
        'additional_info' => 'array',
    ];

    // Relaciones
    public function festival(): BelongsTo
    {
        return $this->belongsTo(Festival::class);
    }

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(FestivalActivity::class, 'program_id');
    }

    // Atributos calculados
    public function getDurationAttribute(): int
    {
        if ($this->start_time && $this->end_time) {
            return $this->start_time->diffInMinutes($this->end_time);
        }
        return 0;
    }

    public function getAttendancePercentageAttribute(): float
    {
        if ($this->capacity > 0) {
            return round(($this->current_attendance / $this->capacity) * 100, 2);
        }
        return 0;
    }

    public function getIsSoldOutAttribute(): bool
    {
        return $this->current_attendance >= $this->capacity;
    }

    public function getFormattedTimeAttribute(): string
    {
        if ($this->start_time && $this->end_time) {
            return $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');
        }
        return '';
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('day', Carbon::today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('day', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
    }

    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    public function scopePaid($query)
    {
        return $query->where('is_free', false);
    }

    public function scopeByEventType($query, string $type)
    {
        return $query->where('event_type', $type);
    }

    public function scopeWithAvailableCapacity($query)
    {
        return $query->whereRaw('current_attendance < capacity');
    }

    // Métodos
    public function isSoldOut(): bool
    {
        return $this->is_sold_out;
    }

    public function hasAvailableTickets(): bool
    {
        return $this->current_attendance < $this->capacity;
    }

    public function getAvailableTickets(): int
    {
        return max(0, $this->capacity - $this->current_attendance);
    }

    public function incrementAttendance(int $count = 1): bool
    {
        if ($this->hasAvailableTickets()) {
            $this->increment('current_attendance', $count);
            return true;
        }
        return false;
    }
}
