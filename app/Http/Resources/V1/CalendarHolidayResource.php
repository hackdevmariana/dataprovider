<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CalendarHolidayResource extends JsonResource
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
            'date' => $this->date,
            'slug' => $this->slug,
            'municipality_id' => $this->municipality_id,
            'locations' => $this->whenLoaded('locations', function () {
                return $this->locations->pluck('id');
            }),
        ];
    }
}
