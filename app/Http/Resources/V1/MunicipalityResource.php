<?php
namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class MunicipalityResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'ine_code' => $this->ine_code,
            'postal_code' => $this->postal_code,
            'population' => $this->population,
            'is_capital' => $this->is_capital,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'area_km2' => $this->area_km2,
            'altitude_m' => $this->altitude_m,
        ];
    }
}


