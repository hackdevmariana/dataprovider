<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserBadgeResource extends JsonResource
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
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->when($this->shouldShowEmail($request), $this->user->email),
            ],
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'badge_type' => $this->badge_type,
            'badge_type_label' => $this->getBadgeTypeLabel(),
            'category' => $this->category,
            'category_label' => $this->getCategoryLabel(),
            'icon' => $this->icon,
            'color' => $this->color ?? '#6B7280',
            'points_awarded' => $this->points_awarded ?? 0,
            'criteria' => $this->criteria ?? [],
            'metadata' => $this->metadata ?? [],
            'earned_at' => $this->earned_at,
            'expires_at' => $this->expires_at,
            'is_public' => $this->is_public,
            'is_featured' => $this->is_featured,
            'is_valid' => $this->isValid(),
            'is_expired' => $this->isExpired(),
            'awarded_by' => $this->whenLoaded('awarder', [
                'id' => $this->awarder?->id,
                'name' => $this->awarder?->name,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Determine if email should be shown based on privacy settings
     */
    private function shouldShowEmail(Request $request): bool
    {
        $authUser = $request->user('sanctum');
        
        // Show email if it's the user's own badge or if user is admin
        return $authUser && (
            $authUser->id === $this->user_id || 
            $authUser->hasRole('admin')
        );
    }

    /**
     * Get human-readable badge type label
     */
    private function getBadgeTypeLabel(): string
    {
        return match ($this->badge_type) {
            'bronze' => 'Bronce',
            'silver' => 'Plata',
            'gold' => 'Oro',
            'platinum' => 'Platino',
            'diamond' => 'Diamante',
            default => ucfirst($this->badge_type),
        };
    }

    /**
     * Get human-readable category label
     */
    private function getCategoryLabel(): string
    {
        return match ($this->category) {
            'energy_saver' => 'Ahorrador de Energía',
            'community_leader' => 'Líder Comunitario',
            'expert_contributor' => 'Contribuidor Experto',
            'project_creator' => 'Creador de Proyectos',
            'helpful_member' => 'Miembro Útil',
            'early_adopter' => 'Adoptador Temprano',
            'sustainability_champion' => 'Campeón de Sostenibilidad',
            default => ucfirst(str_replace('_', ' ', $this->category)),
        };
    }
}