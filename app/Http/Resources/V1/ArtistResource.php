<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class ArtistResource extends JsonResource
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
            'description' => $this->description,
            'birth_date' => $this->birth_date?->toDateString(),
            'genre' => $this->genre,
            'stage_name' => $this->stage_name,
            'group_name' => $this->group_name,
            'active_years_start' => $this->active_years_start,
            'active_years_end' => $this->active_years_end,
            'bio' => $this->bio,
            'photo' => $this->photo,
            'social_links' => $this->social_links,
            'person' => $this->whenLoaded('person', function () {
                return [
                    'id' => $this->person->id,
                    'name' => $this->person->name,
                ];
            }),
            'language' => $this->whenLoaded('language', function () {
                return [
                    'id' => $this->language->id,
                    'name' => $this->language->name,
                ];
            }),
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
