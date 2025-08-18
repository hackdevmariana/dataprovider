<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarbonEquivalenceResource extends JsonResource
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
            'co2_kg_equivalent' => $this->co2_kg_equivalent,
            'description' => $this->description,
            'category' => $this->category,
            'category_name' => $this->category_name,
            'unit' => $this->unit,
            'efficiency_ratio' => $this->efficiency_ratio,
            'loss_factor' => $this->loss_factor,
            'calculation_method' => $this->calculation_method,
            'calculation_params' => $this->calculation_params,
            'source' => $this->source,
            'source_url' => $this->source_url,
            'verification' => [
                'is_verified' => $this->is_verified,
                'verification_entity' => $this->verification_entity,
                'last_updated' => $this->last_updated?->toISOString(),
            ],
            'impact' => [
                'level' => $this->impact_level,
                'color' => $this->impact_color,
                'is_low_impact' => $this->is_low_impact,
                'is_high_impact' => $this->is_high_impact,
            ],
            'common_equivalences' => $this->common_equivalences,
            'compensation_recommendations' => $this->compensation_recommendations,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}