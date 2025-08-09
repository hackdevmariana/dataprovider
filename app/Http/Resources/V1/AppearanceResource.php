<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppearanceResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'height_cm' => $this->height_cm,
            'weight_kg' => $this->weight_kg,
            'body_type' => $this->body_type,
        ];
    }
}


