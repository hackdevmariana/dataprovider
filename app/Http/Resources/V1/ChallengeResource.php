<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChallengeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'instructions' => $this->instructions,
            'type' => $this->type,
            'category' => $this->category,
            'difficulty' => $this->difficulty,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'goals' => $this->goals,
            'rewards' => $this->rewards,
            'max_participants' => $this->max_participants,
            'min_participants' => $this->min_participants,
            'entry_fee' => $this->entry_fee,
            'prize_pool' => $this->prize_pool,
            'icon' => $this->icon,
            'banner_color' => $this->banner_color,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'auto_join' => $this->auto_join,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
