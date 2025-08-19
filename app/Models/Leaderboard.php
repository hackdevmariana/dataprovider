<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Leaderboard extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'period',
        'scope',
        'scope_id',
        'criteria',
        'rules',
        'is_active',
        'is_public',
        'max_positions',
        'start_date',
        'end_date',
        'last_calculated_at',
        'current_rankings',
        'metadata',
    ];

    protected $casts = [
        'criteria' => 'array',
        'rules' => 'array',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'current_rankings' => 'array',
        'metadata' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'last_calculated_at' => 'datetime',
    ];

    /**
     * Get the scope entity (polymorphic)
     */
    public function scopeEntity(): MorphTo
    {
        return $this->morphTo('scope', 'scope', 'scope_id');
    }

    /**
     * Get current rankings
     */
    public function getRankings(): array
    {
        return $this->current_rankings ?? [];
    }

    /**
     * Update rankings
     */
    public function updateRankings(array $rankings): void
    {
        $this->update([
            'current_rankings' => array_slice($rankings, 0, $this->max_positions),
            'last_calculated_at' => now(),
        ]);
    }

    /**
     * Get user's position in leaderboard
     */
    public function getUserPosition(int $userId): ?int
    {
        $rankings = $this->getRankings();
        
        foreach ($rankings as $position => $entry) {
            if ($entry['user_id'] === $userId) {
                return $position + 1; // 1-based position
            }
        }
        
        return null;
    }

    /**
     * Check if leaderboard is currently active
     */
    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now()->toDateString();
        
        if ($this->start_date && $this->start_date > $now) {
            return false;
        }
        
        if ($this->end_date && $this->end_date < $now) {
            return false;
        }
        
        return true;
    }

    /**
     * Get type label
     */
    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'energy_savings' => 'Ahorro de Energía',
            'reputation' => 'Reputación',
            'contributions' => 'Contribuciones',
            'projects' => 'Proyectos',
            'community_engagement' => 'Participación Comunitaria',
            default => ucwords(str_replace('_', ' ', $this->type)),
        };
    }

    /**
     * Get period label
     */
    public function getPeriodLabel(): string
    {
        return match ($this->period) {
            'daily' => 'Diario',
            'weekly' => 'Semanal',
            'monthly' => 'Mensual',
            'yearly' => 'Anual',
            'all_time' => 'Histórico',
            default => ucfirst($this->period),
        };
    }

    /**
     * Scope for active leaderboards
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for public leaderboards
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for current leaderboards
     */
    public function scopeCurrent($query)
    {
        $now = now()->toDateString();
        
        return $query->where('is_active', true)
                    ->where(function ($q) use ($now) {
                        $q->whereNull('start_date')
                          ->orWhere('start_date', '<=', $now);
                    })
                    ->where(function ($q) use ($now) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', $now);
                    });
    }

    /**
     * Scope by type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope by period
     */
    public function scopeByPeriod($query, string $period)
    {
        return $query->where('period', $period);
    }
}