<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_proposal_id',
        'author_id',
        'title',
        'content',
        'type',
        'progress_percentage',
        'current_production_kwh',
        'financial_impact',
        'metrics',
        'images',
        'is_milestone',
        'milestone_description',
        'visibility',
        'priority',
        'tags',
        'affected_timeline',
        'next_steps',
        'published_at',
    ];

    protected $casts = [
        'metrics' => 'array',
        'images' => 'array',
        'tags' => 'array',
        'next_steps' => 'array',
        'progress_percentage' => 'decimal:2',
        'current_production_kwh' => 'decimal:2',
        'financial_impact' => 'decimal:2',
        'is_milestone' => 'boolean',
        'affected_timeline' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Get the project proposal that this update belongs to
     */
    public function projectProposal()
    {
        return $this->belongsTo(ProjectProposal::class);
    }

    /**
     * Get the author of the update
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Scope for published updates
     */
    public function scopePublished($query)
    {
        return $query->where('published_at', '<=', now());
    }

    /**
     * Scope for milestone updates
     */
    public function scopeMilestones($query)
    {
        return $query->where('is_milestone', true);
    }

    /**
     * Scope for specific update type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for updates with progress
     */
    public function scopeWithProgress($query)
    {
        return $query->whereNotNull('progress_percentage');
    }

    /**
     * Get human-readable type label
     */
    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'progress' => 'Progreso',
            'milestone' => 'Hito',
            'financial' => 'Financiero',
            'technical' => 'Técnico',
            'announcement' => 'Anuncio',
            'issue' => 'Incidencia',
            default => ucfirst($this->type),
        };
    }

    /**
     * Check if update is published
     */
    public function isPublished(): bool
    {
        return $this->published_at && $this->published_at->isPast();
    }

    /**
     * Get update summary for notifications
     */
    public function getSummary(int $length = 100): string
    {
        return str_limit(strip_tags($this->content), $length);
    }

    /**
     * Get progress change since last update
     */
    public function getProgressChange(): ?float
    {
        if (!$this->progress_percentage) {
            return null;
        }

        $previousUpdate = self::where('project_proposal_id', $this->project_proposal_id)
            ->where('id', '<', $this->id)
            ->whereNotNull('progress_percentage')
            ->orderBy('id', 'desc')
            ->first();

        if (!$previousUpdate) {
            return $this->progress_percentage;
        }

        return $this->progress_percentage - $previousUpdate->progress_percentage;
    }

    /**
     * Get the priority label
     */
    public function getPriorityLabel(): string
    {
        return match ($this->priority) {
            'low' => 'Baja',
            'medium' => 'Media',
            'high' => 'Alta',
            'urgent' => 'Urgente',
            default => 'Normal',
        };
    }

    /**
     * Get visibility label
     */
    public function getVisibilityLabel(): string
    {
        return match ($this->visibility) {
            'public' => 'Público',
            'investors' => 'Solo Inversores',
            'team' => 'Solo Equipo',
            'private' => 'Privado',
            default => 'Público',
        };
    }
}