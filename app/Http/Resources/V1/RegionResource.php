<?php
namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class RegionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'area_km2' => $this->area_km2,
            'altitude_m' => $this->altitude_m,
            'province' => $this->province?->name,
            'autonomous_community' => $this->autonomousCommunity?->name,
            'country' => $this->country?->name,
            'timezone' => $this->timezone?->name,
        ];
    }
}


