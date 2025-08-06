<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TimezoneResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'offset'     => $this->offset,
            'dst_offset' => $this->dst_offset,
            'countries'  => CountryResource::collection($this->whenLoaded('countries')),
        ];
    }
}
