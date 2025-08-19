<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoofMarketplaceResource extends JsonResource
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
            'space_type' => $this->space_type,
            'availability_status' => $this->availability_status,
            
            // Ubicación
            'location' => [
                'address' => $this->address,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'postal_code' => $this->postal_code,
                'municipality' => $this->whenLoaded('municipality', function () {
                    return [
                        'id' => $this->municipality->id,
                        'name' => $this->municipality->name,
                        'province' => $this->municipality->province?->name,
                    ];
                }),
                'nearby_landmarks' => $this->nearby_landmarks,
                'access_instructions' => $this->access_instructions,
            ],
            
            // Características físicas
            'physical_characteristics' => [
                'total_area_m2' => $this->total_area_m2,
                'usable_area_m2' => $this->usable_area_m2,
                'max_installable_power_kw' => $this->max_installable_power_kw,
                'roof_orientation' => $this->roof_orientation,
                'roof_inclination_degrees' => $this->roof_inclination_degrees,
                'roof_material' => $this->roof_material,
                'roof_condition' => $this->roof_condition,
                'roof_age_years' => $this->roof_age_years,
                'max_load_capacity_kg_m2' => $this->max_load_capacity_kg_m2,
            ],
            
            // Condiciones ambientales
            'environmental_conditions' => [
                'annual_solar_irradiation_kwh_m2' => $this->annual_solar_irradiation_kwh_m2,
                'annual_sunny_days' => $this->annual_sunny_days,
                'has_shading_issues' => $this->has_shading_issues,
                'shading_description' => $this->shading_description,
                'shading_analysis' => $this->shading_analysis,
            ],
            
            // Acceso y logística
            'access_logistics' => [
                'access_difficulty' => $this->access_difficulty,
                'access_description' => $this->access_description,
                'crane_access' => $this->crane_access,
                'vehicle_access' => $this->vehicle_access,
                'distance_to_electrical_panel_m' => $this->distance_to_electrical_panel_m,
            ],
            
            // Aspectos legales
            'legal_aspects' => [
                'has_building_permits' => $this->has_building_permits,
                'community_approval_required' => $this->community_approval_required,
                'community_approval_obtained' => $this->community_approval_obtained,
                'required_permits' => $this->required_permits,
                'obtained_permits' => $this->obtained_permits,
                'legal_restrictions' => $this->legal_restrictions,
            ],
            
            // Términos comerciales
            'commercial_terms' => [
                'offering_type' => $this->offering_type,
                'monthly_rent_eur' => $this->monthly_rent_eur,
                'sale_price_eur' => $this->sale_price_eur,
                'energy_share_percentage' => $this->energy_share_percentage,
                'contract_duration_years' => $this->contract_duration_years,
                'renewable_contract' => $this->renewable_contract,
                'additional_terms' => $this->additional_terms,
            ],
            
            // Servicios incluidos
            'included_services' => [
                'includes_maintenance' => $this->includes_maintenance,
                'includes_insurance' => $this->includes_insurance,
                'includes_permits_management' => $this->includes_permits_management,
                'includes_monitoring' => $this->includes_monitoring,
                'included_services' => $this->included_services,
                'additional_costs' => $this->additional_costs,
            ],
            
            // Disponibilidad
            'availability' => [
                'available_from' => $this->available_from?->toDateString(),
                'available_until' => $this->available_until?->toDateString(),
                'availability_notes' => $this->availability_notes,
                'is_available' => $this->isAvailable(),
            ],
            
            // Información del propietario
            'owner_info' => [
                'owner' => $this->whenLoaded('owner', function () {
                    return [
                        'id' => $this->owner->id,
                        'name' => $this->owner->name,
                        'avatar' => $this->owner->avatar ?? null,
                    ];
                }),
                'owner_lives_onsite' => $this->owner_lives_onsite,
                'owner_involvement' => $this->owner_involvement,
                'owner_preferences' => $this->owner_preferences,
                'owner_requirements' => $this->owner_requirements,
                'auto_respond_inquiries' => $this->auto_respond_inquiries,
            ],
            
            // Potencial energético
            'energy_potential' => $this->getEnergyPotential(),
            'attractiveness_score' => $this->getAttractivenessScore(),
            
            // Métricas
            'metrics' => [
                'views_count' => $this->views_count,
                'inquiries_count' => $this->inquiries_count,
                'bookmarks_count' => $this->bookmarks_count,
                'rating' => $this->rating,
                'reviews_count' => $this->reviews_count,
            ],
            
            // Documentación
            'documentation' => [
                'images' => $this->images,
                'documents' => $this->documents,
                'technical_reports' => $this->technical_reports,
                'solar_analysis_reports' => $this->solar_analysis_reports,
            ],
            
            // Estado y configuración
            'configuration' => [
                'is_active' => $this->is_active,
                'is_featured' => $this->is_featured,
                'is_verified' => $this->is_verified,
                'verified_at' => $this->verified_at?->toISOString(),
                'verified_by' => $this->when($this->is_verified, function () {
                    return $this->verifiedBy?->name;
                }),
            ],
            
            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
