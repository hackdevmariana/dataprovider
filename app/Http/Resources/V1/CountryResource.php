<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'slug'       => $this->slug,
            'iso_code'   => $this->iso_code,
            'timezone'   => new TimezoneResource($this->whenLoaded('timezone')),
            'languages'  => LanguageResource::collection($this->whenLoaded('languages')),
        ];
    }
}


