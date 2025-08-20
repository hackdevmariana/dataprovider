<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaderboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type,
            'type_label' => $this->getTypeLabel(),
            'period' => $this->period,
            'period_label' => $this->getPeriodLabel(),
            'scope' => $this->scope,
            'scope_label' => $this->getScopeLabel(),
            'scope_id' => $this->scope_id,
            'max_positions' => $this->max_positions,
            'criteria' => $this->criteria ?? [],
            'rules' => $this->rules ?? [],
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_active' => $this->is_active,
            'is_public' => $this->is_public,
            'is_featured' => $this->is_featured,
            'last_calculated_at' => $this->last_calculated_at,
            'metadata' => $this->metadata,
            'status' => $this->getStatus(),
            'status_label' => $this->getStatusLabel(),
            'is_currently_active' => $this->isCurrentlyActive(),
            'days_remaining' => $this->getDaysRemaining(),
            'participants_count' => $this->getParticipantsCount(),
            'update_frequency' => $this->getUpdateFrequency(),
            'next_update_at' => $this->getNextUpdateAt(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Get human-readable type label
     */
    private function getTypeLabel(): string
    {
        return match ($this->type) {
            'energy_savings' => 'Ahorro de Energía',
            'reputation' => 'Reputación',
            'contributions' => 'Contribuciones',
            'projects' => 'Proyectos',
            'community_engagement' => 'Participación Comunitaria',
            default => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }

    /**
     * Get human-readable period label
     */
    private function getPeriodLabel(): string
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
     * Get human-readable scope label
     */
    private function getScopeLabel(): string
    {
        return match ($this->scope) {
            'global' => 'Global',
            'cooperative' => 'Cooperativa',
            'regional' => 'Regional',
            'topic' => 'Tema',
            default => ucfirst($this->scope),
        };
    }

    /**
     * Get leaderboard status
     */
    private function getStatus(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        }

        if ($this->end_date && $this->end_date->isPast()) {
            return 'ended';
        }

        if ($this->start_date->isFuture()) {
            return 'upcoming';
        }

        return 'active';
    }

    /**
     * Get human-readable status label
     */
    private function getStatusLabel(): string
    {
        return match ($this->getStatus()) {
            'inactive' => 'Inactivo',
            'ended' => 'Finalizado',
            'upcoming' => 'Próximamente',
            'active' => 'Activo',
            default => 'Desconocido',
        };
    }

    /**
     * Check if leaderboard is currently active
     */
    private function isCurrentlyActive(): bool
    {
        return $this->is_active && 
               $this->start_date->isPast() && 
               (!$this->end_date || $this->end_date->isFuture());
    }

    /**
     * Get days remaining until end
     */
    private function getDaysRemaining(): ?int
    {
        if (!$this->end_date) {
            return null; // No end date = indefinite
        }

        if ($this->end_date->isPast()) {
            return 0; // Already ended
        }

        return now()->diffInDays($this->end_date);
    }

    /**
     * Get participants count (simulated for now)
     */
    private function getParticipantsCount(): int
    {
        // En una implementación real, esto consultaría la base de datos
        // Por ahora devolvemos un número simulado basado en el scope
        return match ($this->scope) {
            'global' => rand(1000, 5000),
            'cooperative' => rand(50, 500),
            'regional' => rand(200, 1000),
            'topic' => rand(20, 200),
            default => rand(10, 100),
        };
    }

    /**
     * Get update frequency description
     */
    private function getUpdateFrequency(): string
    {
        return match ($this->period) {
            'daily' => 'Se actualiza cada día a las 00:00',
            'weekly' => 'Se actualiza cada lunes a las 00:00',
            'monthly' => 'Se actualiza el primer día de cada mes',
            'yearly' => 'Se actualiza el 1 de enero de cada año',
            'all_time' => 'Se actualiza en tiempo real',
            default => 'Frecuencia no definida',
        };
    }

    /**
     * Get next update timestamp
     */
    private function getNextUpdateAt(): ?string
    {
        if (!$this->isCurrentlyActive()) {
            return null;
        }

        $nextUpdate = match ($this->period) {
            'daily' => now()->addDay()->startOfDay(),
            'weekly' => now()->addWeek()->startOfWeek(),
            'monthly' => now()->addMonth()->startOfMonth(),
            'yearly' => now()->addYear()->startOfYear(),
            'all_time' => now()->addMinutes(5), // Tiempo real, próxima actualización en 5 min
            default => null,
        };

        return $nextUpdate?->toISOString();
    }
}