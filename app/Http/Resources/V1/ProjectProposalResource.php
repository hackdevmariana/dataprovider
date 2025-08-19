<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectProposalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'summary' => $this->summary,
            'objectives' => $this->objectives,
            'benefits' => $this->benefits,
            'project_type' => $this->project_type,
            'scale' => $this->scale,
            'status' => $this->status,
            
            // Ubicación
            'location' => [
                'municipality_id' => $this->municipality_id,
                'municipality' => $this->whenLoaded('municipality', function () {
                    return [
                        'id' => $this->municipality->id,
                        'name' => $this->municipality->name,
                        'province' => $this->municipality->province?->name,
                    ];
                }),
                'specific_location' => $this->specific_location,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ],
            
            // Características técnicas
            'technical' => [
                'estimated_power_kw' => $this->estimated_power_kw,
                'estimated_annual_production_kwh' => $this->estimated_annual_production_kwh,
                'technical_specifications' => $this->technical_specifications,
                'is_technically_validated' => $this->is_technically_validated,
                'technical_validation_date' => $this->technical_validation_date?->toISOString(),
                'has_permits' => $this->has_permits,
                'permits_status' => $this->permits_status,
            ],
            
            // Aspectos financieros
            'financial' => [
                'total_investment_required' => $this->total_investment_required,
                'investment_raised' => $this->investment_raised,
                'funding_percentage' => $this->getFundingPercentage(),
                'remaining_investment' => $this->getRemainingInvestment(),
                'min_investment_per_participant' => $this->min_investment_per_participant,
                'max_investment_per_participant' => $this->max_investment_per_participant,
                'max_participants' => $this->max_participants,
                'current_participants' => $this->current_participants,
                'estimated_roi_percentage' => $this->estimated_roi_percentage,
                'payback_period_years' => $this->payback_period_years,
                'estimated_annual_savings' => $this->estimated_annual_savings,
                'financial_projections' => $this->financial_projections,
            ],
            
            // Cronograma
            'timeline' => [
                'funding_deadline' => $this->funding_deadline?->toDateString(),
                'project_start_date' => $this->project_start_date?->toDateString(),
                'expected_completion_date' => $this->expected_completion_date?->toDateString(),
                'estimated_duration_months' => $this->estimated_duration_months,
                'project_milestones' => $this->project_milestones,
                'days_remaining_funding' => $this->funding_deadline ? 
                    now()->diffInDays($this->funding_deadline, false) : null,
            ],
            
            // Participantes
            'participants' => [
                'proposer' => $this->whenLoaded('proposer', function () {
                    return [
                        'id' => $this->proposer->id,
                        'name' => $this->proposer->name,
                        'avatar' => $this->proposer->avatar ?? null,
                    ];
                }),
                'cooperative' => $this->whenLoaded('cooperative', function () {
                    return [
                        'id' => $this->cooperative->id,
                        'name' => $this->cooperative->name,
                        'logo' => $this->cooperative->logo ?? null,
                    ];
                }),
                'technical_validator' => $this->when($this->is_technically_validated, function () {
                    return [
                        'id' => $this->technical_validator_id,
                        'name' => $this->technicalValidator?->name,
                    ];
                }),
            ],
            
            // Inversiones
            'investments' => $this->whenLoaded('investments', function () {
                return $this->investments->map(function ($investment) {
                    return [
                        'id' => $investment->id,
                        'amount' => $investment->investment_amount,
                        'type' => $investment->investment_type,
                        'status' => $investment->status,
                        'investor' => $investment->public_investor ? [
                            'id' => $investment->investor_id,
                            'name' => $investment->investor_alias ?? $investment->investor->name,
                        ] : null,
                        'created_at' => $investment->created_at?->toISOString(),
                    ];
                });
            }),
            
            // Actualizaciones recientes
            'recent_updates' => $this->whenLoaded('updates', function () {
                return $this->updates->map(function ($update) {
                    return [
                        'id' => $update->id,
                        'title' => $update->title,
                        'update_type' => $update->update_type,
                        'progress_percentage' => $update->progress_percentage,
                        'published_at' => $update->published_at?->toISOString(),
                    ];
                });
            }),
            
            // Documentación
            'documentation' => [
                'images' => $this->images,
                'documents' => $this->documents,
                'technical_reports' => $this->technical_reports,
            ],
            
            // Métricas
            'metrics' => [
                'views_count' => $this->views_count,
                'likes_count' => $this->likes_count,
                'comments_count' => $this->comments_count,
                'shares_count' => $this->shares_count,
                'bookmarks_count' => $this->bookmarks_count,
                'engagement_score' => $this->engagement_score,
            ],
            
            // Estado y configuración
            'configuration' => [
                'is_public' => $this->is_public,
                'is_featured' => $this->is_featured,
                'allow_comments' => $this->allow_comments,
                'allow_investments' => $this->allow_investments,
                'notify_updates' => $this->notify_updates,
                'is_open_for_investment' => $this->isOpenForInvestment(),
                'is_fully_funded' => $this->isFullyFunded(),
            ],
            
            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
