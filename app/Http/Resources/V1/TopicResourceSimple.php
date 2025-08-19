<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopicResourceSimple extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id ?? null,
            'name' => $this->resource->name ?? null,
            'slug' => $this->resource->slug ?? null,
            'description' => $this->resource->description ?? null,
            'category' => $this->resource->category ?? null,
            'category_label' => $this->resource ? $this->resource->getCategoryLabel() : null,
            'visibility' => $this->resource->visibility ?? null,
            'icon' => $this->resource->icon ?? null,
            'color' => $this->resource->color ?? null,
            'members_count' => $this->resource->members_count ?? 0,
            'posts_count' => $this->resource->posts_count ?? 0,
            'comments_count' => $this->resource->comments_count ?? 0,
            'activity_score' => $this->resource->activity_score ?? 0,
            'is_active' => $this->resource->is_active ?? false,
            'is_featured' => $this->resource->is_featured ?? false,
            'creator_id' => $this->resource->creator_id ?? null,
            'created_at' => $this->resource->created_at ?? null,
            'updated_at' => $this->resource->updated_at ?? null,
        ];
    }
}
