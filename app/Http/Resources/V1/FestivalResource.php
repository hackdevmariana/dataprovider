<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class FestivalResource extends JsonResource
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
            'month' => $this->month,
            'usual_days' => $this->usual_days,
            'recurring' => $this->recurring,
            'location_id' => $this->location_id,
            'logo_url' => $this->logo_url,
            'color_theme' => $this->color_theme,
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
