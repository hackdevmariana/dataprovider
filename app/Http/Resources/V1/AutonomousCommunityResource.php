<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class AutonomousCommunityResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'code' => $this->code,
            'country' => $this->country?->name,
            'timezone' => $this->timezone?->name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'area_km2' => $this->area_km2,
            'altitude_m' => $this->altitude_m,
            'provinces' => ProvinceResource::collection($this->whenLoaded('provinces')),
        ];
    }
}


