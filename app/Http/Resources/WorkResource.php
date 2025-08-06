<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'type' => $this->type,
            'description' => $this->description,
            'release_year' => $this->release_year,
            'genre' => $this->genre,
            'person' => new PersonResource($this->whenLoaded('person')),
            'language' => new LanguageResource($this->whenLoaded('language')),
            'link' => new LinkResource($this->whenLoaded('link')),
        ];
    }
}
