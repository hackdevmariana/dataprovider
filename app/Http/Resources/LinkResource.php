<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LinkResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'label' => $this->label,
            'type' => $this->type,
            'is_primary' => $this->is_primary,
            'opens_in_new_tab' => $this->opens_in_new_tab,
            'related_type' => $this->related_type,
            'related_id' => $this->related_id,
        ];
    }
}
