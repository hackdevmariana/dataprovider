<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class AwardResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'awarded_by' => $this->awarded_by,
            'first_year_awarded' => $this->first_year_awarded,
            'category' => $this->category,
            'award_winners' => AwardWinnerResource::collection($this->whenLoaded('awardWinners')),
        ];
    }
}


