<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlantSpeciesResource extends JsonResource
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
            'scientific_name' => $this->scientific_name,
            'family' => $this->family,
            'plant_type' => $this->plant_type,
            'plant_type_name' => $this->plant_type_name,
            'size_category' => $this->size_category,
            'size_category_name' => $this->size_category_name,
            'description' => $this->description,
            'co2_absorption' => [
                'kg_per_year' => $this->co2_absorption_kg_per_year,
                'min_kg_per_year' => $this->co2_absorption_min,
                'max_kg_per_year' => $this->co2_absorption_max,
                'level' => $this->co2_absorption_level,
                'color' => $this->co2_absorption_color,
            ],
            'physical_characteristics' => [
                'height_min_m' => $this->height_min,
                'height_max_m' => $this->height_max,
                'lifespan_years' => $this->lifespan_years,
                'growth_rate_cm_year' => $this->growth_rate_cm_year,
            ],
            'environmental_conditions' => [
                'climate_zones' => $this->climate_zones,
                'soil_types' => $this->soil_types,
                'water_needs_mm_year' => $this->water_needs_mm_year,
                'drought_resistant' => $this->drought_resistant,
                'frost_resistant' => $this->frost_resistant,
            ],
            'suitability' => [
                'is_endemic' => $this->is_endemic,
                'is_invasive' => $this->is_invasive,
                'suitable_for_reforestation' => $this->suitable_for_reforestation,
                'suitable_for_urban' => $this->suitable_for_urban,
                'reforestation_score' => $this->reforestation_score,
                'project_recommendation' => $this->project_recommendation,
            ],
            'seasonality' => [
                'flowering_season' => $this->flowering_season,
                'fruit_season' => $this->fruit_season,
            ],
            'benefits' => [
                'provides_food' => $this->provides_food,
                'provides_timber' => $this->provides_timber,
                'medicinal_use' => $this->medicinal_use,
                'additional_benefits' => $this->additional_benefits,
            ],
            'economics' => [
                'planting_cost_eur' => $this->planting_cost_eur,
                'maintenance_cost_eur_year' => $this->maintenance_cost_eur_year,
                'survival_rate_percent' => $this->survival_rate_percent,
                'co2_efficiency' => $this->co2_efficiency,
            ],
            'location' => [
                'native_region' => $this->whenLoaded('nativeRegion', function() {
                    return [
                        'id' => $this->nativeRegion->id,
                        'name' => $this->nativeRegion->name,
                        'slug' => $this->nativeRegion->slug,
                    ];
                }),
            ],
            'verification' => [
                'is_verified' => $this->is_verified,
                'verification_entity' => $this->verification_entity,
                'source' => $this->source,
                'source_url' => $this->source_url,
            ],
            'image' => $this->whenLoaded('image', function() {
                return [
                    'id' => $this->image->id,
                    'url' => $this->image->url ?? $this->image->path,
                    'alt' => $this->image->alt_text,
                ];
            }),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}