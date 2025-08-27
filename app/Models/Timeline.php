<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Timeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'theme',
        'start_date',
        'end_date',
        'events',
        'view_type',
        'categories',
        'is_public',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'events' => 'array',
        'categories' => 'array',
        'is_public' => 'boolean',
    ];

    // Relaciones
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Atributos calculados
    public function getDurationAttribute(): int
    {
        if ($this->start_date && $this->end_date) {
            return $this->start_date->diffInDays($this->end_date);
        }
        return 0;
    }

    public function getDurationYearsAttribute(): int
    {
        if ($this->start_date && $this->end_date) {
            return $this->start_date->diffInYears($this->end_date);
        }
        return 0;
    }

    public function getDurationMonthsAttribute(): int
    {
        if ($this->start_date && $this->end_date) {
            return $this->start_date->diffInMonths($this->end_date);
        }
        return 0;
    }

    public function getFormattedDurationAttribute(): string
    {
        $years = $this->duration_years;
        $months = $this->duration_months % 12;
        $days = $this->duration % 30;

        $parts = [];
        if ($years > 0) {
            $parts[] = $years . ' año' . ($years > 1 ? 's' : '');
        }
        if ($months > 0) {
            $parts[] = $months . ' mes' . ($months > 1 ? 'es' : '');
        }
        if ($days > 0) {
            $parts[] = $days . ' día' . ($days > 1 ? 's' : '');
        }

        return implode(', ', $parts);
    }

    public function getFormattedStartDateAttribute(): string
    {
        return $this->start_date ? $this->start_date->format('d/m/Y') : 'Sin fecha';
    }

    public function getFormattedEndDateAttribute(): string
    {
        return $this->end_date ? $this->end_date->format('d/m/Y') : 'Sin fecha';
    }

    public function getViewTypeLabelAttribute(): string
    {
        return match ($this->view_type) {
            'chronological' => 'Cronológico',
            'thematic' => 'Temático',
            'geographic' => 'Geográfico',
            'biographical' => 'Biográfico',
            'interactive' => 'Interactivo',
            'timeline' => 'Línea de tiempo',
            'calendar' => 'Calendario',
            'map' => 'Mapa',
            'tree' => 'Árbol',
            'network' => 'Red',
            default => 'Desconocido',
        };
    }

    public function getViewTypeIconAttribute(): string
    {
        return match ($this->view_type) {
            'chronological' => '📅',
            'thematic' => '🏷️',
            'geographic' => '🗺️',
            'biographical' => '👤',
            'interactive' => '🖱️',
            'timeline' => '⏱️',
            'calendar' => '📆',
            'map' => '🗺️',
            'tree' => '🌳',
            'network' => '🕸️',
            default => '📋',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        if (!$this->start_date || !$this->end_date) {
            return 'Sin fechas';
        }

        $now = Carbon::now();
        if ($now < $this->start_date) {
            return 'Futuro';
        } elseif ($now > $this->end_date) {
            return 'Completado';
        } else {
            return 'En curso';
        }
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'Futuro' => 'info',
            'En curso' => 'success',
            'Completado' => 'secondary',
            'Sin fechas' => 'warning',
            default => 'gray',
        };
    }

    public function getEventsCountAttribute(): int
    {
        if ($this->events && is_array($this->events)) {
            return count($this->events);
        }
        return 0;
    }

    public function getCategoriesCountAttribute(): int
    {
        if ($this->categories && is_array($this->categories)) {
            return count($this->categories);
        }
        return 0;
    }

    public function getProgressAttribute(): float
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }

        $now = Carbon::now();
        $total = $this->start_date->diffInDays($this->end_date);
        $elapsed = $this->start_date->diffInDays($now);

        if ($total <= 0) {
            return 0;
        }

        $progress = ($elapsed / $total) * 100;
        return min(100, max(0, $progress));
    }

    public function getProgressLabelAttribute(): string
    {
        $progress = $this->progress;
        
        if ($progress <= 0) {
            return 'No iniciado';
        } elseif ($progress < 25) {
            return 'Iniciado';
        } elseif ($progress < 50) {
            return 'En desarrollo';
        } elseif ($progress < 75) {
            return 'Avanzado';
        } elseif ($progress < 100) {
            return 'Casi completo';
        } else {
            return 'Completado';
        }
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    public function scopeByTheme($query, string $theme)
    {
        return $query->where('theme', $theme);
    }

    public function scopeByViewType($query, string $viewType)
    {
        return $query->where('view_type', $viewType);
    }

    public function scopeByCreator($query, int $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopeActive($query)
    {
        $now = Carbon::now();
        return $query->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
    }

    public function scopeCompleted($query)
    {
        return $query->where('end_date', '<', Carbon::now());
    }

    public function scopeFuture($query)
    {
        return $query->where('start_date', '>', Carbon::now());
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function ($subQ) use ($startDate, $endDate) {
                  $subQ->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
              });
        });
    }

    public function scopeWithEvents($query)
    {
        return $query->whereJsonLength('events', '>', 0);
    }

    // Métodos
    public function isPublic(): bool
    {
        return $this->is_public;
    }

    public function isActive(): bool
    {
        return $this->status === 'En curso';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'Completado';
    }

    public function isFuture(): bool
    {
        return $this->status === 'Futuro';
    }

    public function hasEvents(): bool
    {
        return $this->events_count > 0;
    }

    public function hasCategories(): bool
    {
        return $this->categories_count > 0;
    }

    public function hasDates(): bool
    {
        return !is_null($this->start_date) && !is_null($this->end_date);
    }

    public function isOwnedBy(int $userId): bool
    {
        return $this->created_by === $userId;
    }

    public function canBeViewedBy(int $userId): bool
    {
        return $this->is_public || $this->isOwnedBy($userId);
    }

    public function getRemainingDays(): int
    {
        if (!$this->end_date) {
            return 0;
        }

        $now = Carbon::now();
        if ($now > $this->end_date) {
            return 0;
        }

        return $now->diffInDays($this->end_date, false);
    }

    public function getElapsedDays(): int
    {
        if (!$this->start_date) {
            return 0;
        }

        $now = Carbon::now();
        if ($now < $this->start_date) {
            return 0;
        }

        return $this->start_date->diffInDays($now);
    }
}
