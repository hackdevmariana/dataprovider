<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class AnniversaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'day' => (int) $this->day,
            'month' => (int) $this->month,
            'year' => $this->year ? (int) $this->year : null,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'tags' => $this->whenLoaded('tags', function () {
                return $this->tags->pluck('name');
            }),
        ];
    }
}
