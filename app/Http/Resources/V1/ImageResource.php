<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'slug'      => $this->slug,
            'url'       => $this->url,
            'alt_text'  => $this->alt_text,
            'source'    => $this->source,
            'width'     => $this->width,
            'height'    => $this->height,
            'created_at'=> $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at'=> $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}


