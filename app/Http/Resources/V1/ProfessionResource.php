<?php
namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfessionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'category' => $this->category,
            'is_public_facing' => $this->is_public_facing,
        ];
    }
}


