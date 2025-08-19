<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialComparison extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'comparison_type',
        'period',
        'scope',
        'scope_id',
        'user_value',
        'unit',
        'average_value',
        'median_value',
        'best_value',
        'user_rank',
        'total_participants',
        'percentile',
        'breakdown',
        'metadata',
        'is_public',
        'comparison_date',
    ];

    protected $casts = [
        'breakdown' => 'array',
        'metadata' => 'array',
        'is_public' => 'boolean',
        'comparison_date' => 'date',
    ];

    /**
     * Get the user this comparison belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get performance category based on percentile
     */
    public function getPerformanceCategory(): string
    {
        if (!$this->percentile) {
            return 'unknown';
        }

        return match (true) {
            $this->percentile >= 90 => 'excellent',
            $this->percentile >= 75 => 'good',
            $this->percentile >= 50 => 'average',
            $this->percentile >= 25 => 'below_average',
            default => 'needs_improvement',
        };
    }

    /**
     * Get formatted user value with unit
     */
    public function getFormattedUserValue(): string
    {
        return number_format($this->user_value, 2) . ' ' . $this->unit;
    }

    /**
     * Calculate improvement needed to reach next percentile
     */
    public function getImprovementToNextLevel(): ?float
    {
        if (!$this->percentile || $this->percentile >= 90) {
            return null;
        }

        $nextPercentile = match (true) {
            $this->percentile < 25 => 25,
            $this->percentile < 50 => 50,
            $this->percentile < 75 => 75,
            default => 90,
        };

        // This would need actual data to calculate properly
        // For now, return a placeholder
        return ($this->average_value ?? 0) * ($nextPercentile / 100);
    }

    /**
     * Scope by comparison type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('comparison_type', $type);
    }

    /**
     * Scope by period
     */
    public function scopeByPeriod($query, string $period)
    {
        return $query->where('period', $period);
    }

    /**
     * Scope for public comparisons
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for recent comparisons
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('comparison_date', '>=', now()->subDays($days));
    }
}