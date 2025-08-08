<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FamilyMemberResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'relationship_type'=> $this->relationship_type,
            'is_biological'    => $this->is_biological,
            'person'           => new PersonResource($this->whenLoaded('person')),
            'relative'         => new PersonResource($this->whenLoaded('relative')),
        ];
    }
}
