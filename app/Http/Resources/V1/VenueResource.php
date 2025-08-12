<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class VenueResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'address' => $this->address,
            'municipality' => $this->whenLoaded('municipality', function () {
                return [
                    'id' => $this->municipality->id,
                    'name' => $this->municipality->name,
                ];
            }),
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'capacity' => $this->capacity,
            'description' => $this->description,
            'venue_type' => $this->venue_type,
            'venue_status' => $this->venue_status,
            'is_verified' => $this->is_verified,
            'events' => $this->whenLoaded('events', function () {
                return $this->events->map(function ($event) {
                    return [
                        'id' => $event->id,
                        'title' => $event->title,
                    ];
                });
            }),
        ];
    }
}
