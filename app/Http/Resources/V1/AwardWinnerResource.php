<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class AwardWinnerResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'person' => new PersonResource($this->whenLoaded('person')),
            'award' => new AwardResource($this->whenLoaded('award')),
            'year' => $this->year,
            'classification' => $this->classification,
            'work' => new WorkResource($this->whenLoaded('work')),
            'municipality' => new MunicipalityResource($this->whenLoaded('municipality')),
        ];
    }
}


