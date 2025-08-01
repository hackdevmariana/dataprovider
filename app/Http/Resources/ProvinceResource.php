<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProvinceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'ine_code' => $this->ine_code,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'area_km2' => $this->area_km2,
            'altitude_m' => $this->altitude_m,
            'timezone' => $this->timezone?->name,
            'municipalities' => MunicipalityResource::collection($this->whenLoaded('municipalities')),
        ];
    }
}
