<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class NewsAggregation extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_id',
        'article_id',
        'aggregated_at',
        'processing_status',
        'duplicate_check',
        'quality_score',
        'processing_metadata',
        'processed_at',
        'processing_notes',
    ];

    protected $casts = [
        'aggregated_at' => 'datetime',
        'quality_score' => 'decimal:2',
        'processing_metadata' => 'array',
        'processed_at' => 'datetime',
        'duplicate_check' => 'boolean',
    ];

    // Relaciones
    public function source(): BelongsTo
    {
        return $this->belongsTo(NewsSource::class, 'source_id');
    }

    // Atributos calculados
    public function getProcessingStatusLabelAttribute(): string
    {
        return match ($this->processing_status) {
            'pending' => 'Pendiente',
            'processing' => 'Procesando',
            'completed' => 'Completado',
            'failed' => 'Fallido',
            'skipped' => 'Omitido',
            default => 'Desconocido',
        };
    }

    public function getProcessingStatusColorAttribute(): string
    {
        return match ($this->processing_status) {
            'pending' => 'warning',
            'processing' => 'info',
            'completed' => 'success',
            'failed' => 'danger',
            'skipped' => 'secondary',
            default => 'gray',
        };
    }

    public function getQualityLabelAttribute(): string
    {
        if ($this->quality_score >= 0.8) {
            return 'Excelente';
        } elseif ($this->quality_score >= 0.6) {
            return 'Buena';
        } elseif ($this->quality_score >= 0.4) {
            return 'Regular';
        } else {
            return 'Baja';
        }
    }

    public function getQualityColorAttribute(): string
    {
        if ($this->quality_score >= 0.8) {
            return 'success';
        } elseif ($this->quality_score >= 0.6) {
            return 'info';
        } elseif ($this->quality_score >= 0.4) {
            return 'warning';
        } else {
            return 'danger';
        }
    }

    public function getProcessingTimeAttribute(): ?int
    {
        if ($this->aggregated_at && $this->processed_at) {
            return $this->aggregated_at->diffInSeconds($this->processed_at);
        }
        return null;
    }

    public function getFormattedProcessingTimeAttribute(): string
    {
        $time = $this->processing_time;
        if (!$time) {
            return 'N/A';
        }

        if ($time < 60) {
            return $time . 's';
        } elseif ($time < 3600) {
            return round($time / 60, 1) . 'm';
        } else {
            return round($time / 3600, 1) . 'h';
        }
    }

    // Scopes
    public function scopeByStatus($query, string $status)
    {
        return $query->where('processing_status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('processing_status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('processing_status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('processing_status', 'failed');
    }

    public function scopeByQuality($query, float $minScore)
    {
        return $query->where('quality_score', '>=', $minScore);
    }

    public function scopeHighQuality($query)
    {
        return $query->where('quality_score', '>=', 0.8);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('aggregated_at', $date);
    }

    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('aggregated_at', '>=', Carbon::now()->subHours($hours));
    }

    // Métodos
    public function isCompleted(): bool
    {
        return $this->processing_status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->processing_status === 'failed';
    }

    public function isPending(): bool
    {
        return $this->processing_status === 'pending';
    }

    public function isHighQuality(): bool
    {
        return $this->quality_score >= 0.8;
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'processing_status' => 'completed',
            'processed_at' => now(),
        ]);
    }

    public function markAsFailed(string $notes = null): void
    {
        $this->update([
            'processing_status' => 'failed',
            'processing_notes' => $notes,
            'processed_at' => now(),
        ]);
    }
}
