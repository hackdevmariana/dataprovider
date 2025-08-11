<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'start_datetime' => $this->start_datetime?->toIso8601String(),
            'end_datetime' => $this->end_datetime?->toIso8601String(),
            'venue_id' => $this->venue_id,
            'event_type_id' => $this->event_type_id,
            'festival_id' => $this->festival_id,
            'language_id' => $this->language_id,
            'timezone_id' => $this->timezone_id,
            'municipality_id' => $this->municipality_id,
            'point_of_interest_id' => $this->point_of_interest_id,
            'work_id' => $this->work_id,
            'price' => $this->price,
            'is_free' => $this->is_free,
            'audience_size_estimate' => $this->audience_size_estimate,
            'source_url' => $this->source_url,
            'artists' => $this->whenLoaded('artists', function () {
                return $this->artists->pluck('id');
            }),
            'tags' => $this->whenLoaded('tags', function () {
                return $this->tags->pluck('name');
            }),
        ];
    }
}
