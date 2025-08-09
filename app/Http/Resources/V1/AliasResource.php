<?php 

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class AliasResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'is_primary' => $this->is_primary,
        ];
    }
}


