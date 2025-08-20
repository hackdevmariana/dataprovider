<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPrivilegeResource extends JsonResource
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
            'privilege_type' => $this->privilege_type,
            'privilege_type_label' => $this->getPrivilegeTypeLabel(),
            'scope' => $this->scope,
            'scope_label' => $this->getScopeLabel(),
            'scope_id' => $this->scope_id,
            'level' => $this->level,
            'level_label' => $this->getLevelLabel(),
            'permissions' => $this->permissions ?? [],
            'limits' => $this->limits ?? [],
            'reputation_required' => $this->reputation_required ?? 0,
            'granted_at' => $this->granted_at,
            'expires_at' => $this->expires_at,
            'grantor' => $this->whenLoaded('grantor', [
                'id' => $this->grantor?->id,
                'name' => $this->grantor?->name,
            ]),
            'reason' => $this->reason,
            'is_active' => $this->is_active,
            'is_expired' => $this->isExpired(),
            'is_valid' => $this->isValid(),
            'days_until_expiry' => $this->getDaysUntilExpiry(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Determine if email should be shown
     */
    private function shouldShowEmail(Request $request): bool
    {
        $authUser = $request->user('sanctum');
        
        return $authUser && (
            $authUser->id === $this->user_id || 
            $authUser->id === $this->granted_by ||
            $authUser->hasRole('admin')
        );
    }

    /**
     * Get human-readable privilege type label
     */
    private function getPrivilegeTypeLabel(): string
    {
        return match ($this->privilege_type) {
            'posting' => 'Publicación',
            'voting' => 'Votación',
            'moderation' => 'Moderación',
            'verification' => 'Verificación',
            'administration' => 'Administración',
            'content_creation' => 'Creación de Contenido',
            'expert_answers' => 'Respuestas de Experto',
            'project_approval' => 'Aprobación de Proyectos',
            default => ucfirst(str_replace('_', ' ', $this->privilege_type)),
        };
    }

    /**
     * Get human-readable scope label
     */
    private function getScopeLabel(): string
    {
        return match ($this->scope) {
            'global' => 'Global',
            'topic' => 'Tema',
            'cooperative' => 'Cooperativa',
            'project' => 'Proyecto',
            'region' => 'Región',
            default => ucfirst($this->scope),
        };
    }

    /**
     * Get human-readable level label
     */
    private function getLevelLabel(): string
    {
        return match ($this->level) {
            1 => 'Nivel 1 - Básico',
            2 => 'Nivel 2 - Intermedio',
            3 => 'Nivel 3 - Avanzado',
            4 => 'Nivel 4 - Experto',
            5 => 'Nivel 5 - Máximo',
            default => "Nivel {$this->level}",
        };
    }

    /**
     * Check if privilege is expired
     */
    private function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if privilege is valid (active and not expired)
     */
    private function isValid(): bool
    {
        return $this->is_active && !$this->isExpired();
    }

    /**
     * Get days until expiry
     */
    private function getDaysUntilExpiry(): ?int
    {
        if (!$this->expires_at) {
            return null; // Never expires
        }

        $days = now()->diffInDays($this->expires_at, false);
        
        return $days >= 0 ? $days : 0; // Return 0 if already expired
    }
}