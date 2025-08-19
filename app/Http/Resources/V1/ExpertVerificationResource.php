<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpertVerificationResource extends JsonResource
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
            'expertise_area' => $this->expertise_area,
            'expertise_area_label' => $this->getExpertiseAreaLabel(),
            'verification_level' => $this->verification_level,
            'verification_level_label' => $this->getVerificationLevelLabel(),
            'years_experience' => $this->years_experience,
            'expertise_description' => $this->expertise_description,
            'credentials' => $this->credentials ?? [],
            'verification_documents' => $this->when($this->canViewDocuments($request), $this->verification_documents ?? []),
            'certifications' => $this->certifications ?? [],
            'education' => $this->education ?? [],
            'work_history' => $this->work_history ?? [],
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'verification_fee' => $this->verification_fee,
            'verification_score' => $this->verification_score,
            'verification_notes' => $this->when($this->canViewNotes($request), $this->verification_notes),
            'rejection_reason' => $this->when($this->status === 'rejected', $this->rejection_reason),
            'is_public' => $this->is_public,
            'submitted_at' => $this->submitted_at,
            'reviewed_at' => $this->reviewed_at,
            'verified_at' => $this->verified_at,
            'expires_at' => $this->expires_at,
            'verifier' => $this->whenLoaded('verifier', [
                'id' => $this->verifier?->id,
                'name' => $this->verifier?->name,
            ]),
            'is_expired' => $this->isExpired(),
            'is_valid' => $this->isValid(),
            'processing_time_days' => $this->getProcessingTimeDays(),
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
            $authUser->id === $this->verified_by ||
            $authUser->hasRole('admin')
        );
    }

    /**
     * Determine if documents should be shown
     */
    private function canViewDocuments(Request $request): bool
    {
        $authUser = $request->user('sanctum');
        
        return $authUser && (
            $authUser->id === $this->user_id || 
            $authUser->id === $this->verified_by ||
            $authUser->hasRole('admin')
        );
    }

    /**
     * Determine if notes should be shown
     */
    private function canViewNotes(Request $request): bool
    {
        $authUser = $request->user('sanctum');
        
        return $authUser && (
            $authUser->id === $this->user_id || 
            $authUser->id === $this->verified_by ||
            $authUser->hasRole('admin')
        );
    }

    /**
     * Get human-readable expertise area label
     */
    private function getExpertiseAreaLabel(): string
    {
        return match ($this->expertise_area) {
            'solar' => 'Energía Solar',
            'wind' => 'Energía Eólica',
            'legal' => 'Legal y Regulatorio',
            'financial' => 'Financiero',
            'technical' => 'Técnico General',
            'installation' => 'Instalación y Mantenimiento',
            'grid' => 'Redes Eléctricas',
            'storage' => 'Almacenamiento',
            'efficiency' => 'Eficiencia Energética',
            'sustainability' => 'Sostenibilidad',
            default => ucfirst($this->expertise_area),
        };
    }

    /**
     * Get human-readable verification level label
     */
    private function getVerificationLevelLabel(): string
    {
        return match ($this->verification_level) {
            'basic' => 'Básico',
            'advanced' => 'Avanzado',
            'professional' => 'Profesional',
            'expert' => 'Experto',
            default => ucfirst($this->verification_level),
        };
    }

    /**
     * Get human-readable status label
     */
    private function getStatusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Pendiente',
            'under_review' => 'En Revisión',
            'approved' => 'Aprobado',
            'rejected' => 'Rechazado',
            'expired' => 'Expirado',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get processing time in days
     */
    private function getProcessingTimeDays(): ?int
    {
        if (!$this->submitted_at) {
            return null;
        }

        $endDate = $this->reviewed_at ?? now();
        
        return $this->submitted_at->diffInDays($endDate);
    }
}