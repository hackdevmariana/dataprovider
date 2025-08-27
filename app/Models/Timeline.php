<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'events_count',
        'is_public',
        'created_by',
        'tags',
        'color_scheme',
        'display_options',
        'is_featured',
        'views_count',
        'likes_count',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'events_count' => 'integer',
        'is_public' => 'boolean',
        'tags' => 'array',
        'display_options' => 'array',
        'is_featured' => 'boolean',
        'views_count' => 'integer',
        'likes_count' => 'integer',
    ];

    // Atributos calculados
    public function getThemeLabelAttribute(): string
    {
        return match ($this->theme) {
            'historical' => 'Histórico',
            'personal' => 'Personal',
            'cultural' => 'Cultural',
            'scientific' => 'Científico',
            'artistic' => 'Artístico',
            'political' => 'Político',
            'social' => 'Social',
            'technological' => 'Tecnológico',
            'religious' => 'Religioso',
            'military' => 'Militar',
            'economic' => 'Económico',
            'environmental' => 'Ambiental',
            default => 'General',
        };
    }

    public function getThemeColorAttribute(): string
    {
        return match ($this->theme) {
            'historical' => 'dark',
            'personal' => 'primary',
            'cultural' => 'info',
            'scientific' => 'success',
            'artistic' => 'secondary',
            'political' => 'danger',
            'social' => 'warning',
            'technological' => 'info',
            'religious' => 'warning',
            'military' => 'danger',
            'economic' => 'success',
            'environmental' => 'success',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        if (!$this->is_public) {
            return 'Privada';
        }
        return $this->is_featured ? 'Destacada' : 'Pública';
    }

    public function getStatusColorAttribute(): string
    {
        if (!$this->is_public) {
            return 'secondary';
        }
        return $this->is_featured ? 'warning' : 'success';
    }

    public function getDurationAttribute(): int
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }
        return $this->start_date->diffInDays($this->end_date);
    }

    public function getDurationLabelAttribute(): string
    {
        $days = $this->duration;
        if ($days === 0) {
            return 'Un día';
        } elseif ($days < 7) {
            return $days . ' días';
        } elseif ($days < 30) {
            $weeks = ceil($days / 7);
            return $weeks . ' semana' . ($weeks > 1 ? 's' : '');
        } elseif ($days < 365) {
            $months = ceil($days / 30);
            return $months . ' mes' . ($months > 1 ? 'es' : '');
        } else {
            $years = ceil($days / 365);
            return $years . ' año' . ($years > 1 ? 's' : '');
        }
    }

    public function getFormattedStartDateAttribute(): string
    {
        return $this->start_date ? $this->start_date->format('d/m/Y') : 'Sin fecha';
    }

    public function getFormattedEndDateAttribute(): string
    {
        return $this->end_date ? $this->end_date->format('d/m/Y') : 'Sin fecha';
    }

    public function getIsActiveAttribute(): bool
    {
        if (!$this->start_date || !$this->end_date) {
            return false;
        }
        $now = Carbon::now();
        return $now->between($this->start_date, $this->end_date);
    }

    public function getIsUpcomingAttribute(): bool
    {
        if (!$this->start_date) {
            return false;
        }
        return Carbon::now()->lt($this->start_date);
    }

    public function getIsCompletedAttribute(): bool
    {
        if (!$this->end_date) {
            return false;
        }
        return Carbon::now()->gt($this->end_date);
    }

    public function getProgressAttribute(): float
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }
        
        $total = $this->start_date->diffInDays($this->end_date);
        if ($total === 0) {
            return 100;
        }
        
        $elapsed = $this->start_date->diffInDays(Carbon::now());
        $progress = ($elapsed / $total) * 100;
        
        return min(100, max(0, $progress));
    }

    public function getProgressLabelAttribute(): string
    {
        $progress = $this->progress;
        if ($progress === 0) {
            return 'No iniciado';
        } elseif ($progress < 25) {
            return 'Iniciando';
        } elseif ($progress < 50) {
            return 'En progreso';
        } elseif ($progress < 75) {
            return 'Avanzado';
        } elseif ($progress < 100) {
            return 'Casi completo';
        } else {
            return 'Completado';
        }
    }

    public function getProgressColorAttribute(): string
    {
        $progress = $this->progress;
        if ($progress === 0) {
            return 'secondary';
        } elseif ($progress < 25) {
            return 'info';
        } elseif ($progress < 50) {
            return 'warning';
        } elseif ($progress < 75) {
            return 'primary';
        } elseif ($progress < 100) {
            return 'success';
        } else {
            return 'success';
        }
    }

    public function getPopularityScoreAttribute(): float
    {
        if ($this->views_count === 0) {
            return 0;
        }
        
        // Fórmula: (likes * 2 + views) / (views * 0.1)
        $score = ($this->likes_count * 2 + $this->views_count) / ($this->views_count * 0.1);
        return min(1.0, max(0.0, $score));
    }

    public function getPopularityLabelAttribute(): string
    {
        $score = $this->popularity_score;
        if ($score >= 0.8) {
            return 'Muy Popular';
        } elseif ($score >= 0.6) {
            return 'Popular';
        } elseif ($score >= 0.4) {
            return 'Moderado';
        } else {
            return 'Poco Popular';
        }
    }

    public function getPopularityColorAttribute(): string
    {
        $score = $this->popularity_score;
        if ($score >= 0.8) {
            return 'success';
        } elseif ($score >= 0.6) {
            return 'info';
        } elseif ($score >= 0.4) {
            return 'warning';
        } else {
            return 'secondary';
        }
    }

    public function getTagsCountAttribute(): int
    {
        if (is_array($this->tags)) {
            return count($this->tags);
        }
        return 0;
    }

    public function getFormattedViewsCountAttribute(): string
    {
        if ($this->views_count >= 1000000) {
            return round($this->views_count / 1000000, 1) . 'M';
        } elseif ($this->views_count >= 1000) {
            return round($this->views_count / 1000, 1) . 'K';
        }
        return number_format($this->views_count);
    }

    public function getFormattedLikesCountAttribute(): string
    {
        if ($this->likes_count >= 1000000) {
            return round($this->likes_count / 1000000, 1) . 'M';
        } elseif ($this->likes_count >= 1000) {
            return round($this->likes_count / 1000, 1) . 'K';
        }
        return number_format($this->likes_count);
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

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByTheme($query, string $theme)
    {
        return $query->where('theme', $theme);
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

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', Carbon::now());
    }

    public function scopeCompleted($query)
    {
        return $query->where('end_date', '<', Carbon::now());
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate]);
    }

    public function scopePopular($query, float $minScore = 0.6)
    {
        return $query->whereRaw('(likes_count * 2 + views_count) / (views_count * 0.1) >= ?', [$minScore]);
    }

    public function scopeByViews($query, int $minViews)
    {
        return $query->where('views_count', '>=', $minViews);
    }

    public function scopeByLikes($query, int $minLikes)
    {
        return $query->where('likes_count', '>=', $minLikes);
    }

    public function scopeOrderByPopularity($query)
    {
        return $query->orderByRaw('(likes_count * 2 + views_count) / (views_count * 0.1) DESC');
    }

    public function scopeOrderByStartDate($query)
    {
        return $query->orderBy('start_date', 'asc');
    }

    public function scopeOrderByEndDate($query)
    {
        return $query->orderBy('end_date', 'desc');
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%')
              ->orWhere('theme', 'like', '%' . $search . '%');
        });
    }

    // Métodos
    public function isPublic(): bool
    {
        return $this->is_public;
    }

    public function isPrivate(): bool
    {
        return !$this->is_public;
    }

    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isUpcoming(): bool
    {
        return $this->is_upcoming;
    }

    public function isCompleted(): bool
    {
        return $this->is_completed;
    }

    public function isPopular(): bool
    {
        return $this->popularity_score >= 0.6;
    }

    public function hasEvents(): bool
    {
        return $this->events_count > 0;
    }

    public function hasTags(): bool
    {
        return $this->tags_count > 0;
    }

    public function hasStartDate(): bool
    {
        return !is_null($this->start_date);
    }

    public function hasEndDate(): bool
    {
        return !is_null($this->end_date);
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function incrementLikes(): void
    {
        $this->increment('likes_count');
    }

    public function decrementLikes(): void
    {
        $this->decrement('likes_count');
    }

    public function getTagsList(): array
    {
        if (is_array($this->tags)) {
            return $this->tags;
        }
        return [];
    }

    public function getDisplayOptionsList(): array
    {
        if (is_array($this->display_options)) {
            return $this->display_options;
        }
        return [];
    }

    public function getDaysUntilStart(): int
    {
        if (!$this->start_date) {
            return 0;
        }
        return Carbon::now()->diffInDays($this->start_date, false);
    }

    public function getDaysUntilEnd(): int
    {
        if (!$this->end_date) {
            return 0;
        }
        return Carbon::now()->diffInDays($this->end_date, false);
    }

    public function getFormattedTimeRemainingAttribute(): string
    {
        if ($this->is_completed) {
            return 'Completado';
        } elseif ($this->is_upcoming) {
            $days = $this->days_until_start;
            if ($days === 0) {
                return 'Hoy';
            } elseif ($days === 1) {
                return 'Mañana';
            } else {
                return 'En ' . $days . ' días';
            }
        } else {
            $days = $this->days_until_end;
            if ($days === 0) {
                return 'Termina hoy';
            } elseif ($days === 1) {
                return 'Termina mañana';
            } else {
                return 'Termina en ' . $days . ' días';
            }
        }
    }
}
