<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LanguageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'slug'     => $this->slug,
            'iso_code' => $this->iso_code,
            'countries' => CountryResource::collection($this->whenLoaded('countries')),
        ];
    }
}
