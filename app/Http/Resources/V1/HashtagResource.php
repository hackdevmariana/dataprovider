<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HashtagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'color' => $this->color,
            'icon' => $this->icon,
            'category' => $this->category,
            'usage_count' => $this->usage_count,
            'posts_count' => $this->posts_count,
            'followers_count' => $this->followers_count,
            'trending_score' => $this->trending_score,
            'is_trending' => $this->is_trending,
            'is_verified' => $this->is_verified,
            'auto_suggest' => $this->auto_suggest,
            'related_hashtags' => $this->related_hashtags,
            'synonyms' => $this->synonyms,
            'created_by' => $this->whenLoaded('creator', function () {
                return [
                    'id' => $this->creator->id,
                    'name' => $this->creator->name,
                ];
            }),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
