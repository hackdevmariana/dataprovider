<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AppearanceResource;

class PersonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'                       => $this->id,
            'name'                     => $this->name,
            'birth_name'              => $this->birth_name,
            'slug'                     => $this->slug,
            'birth_date'              => optional($this->birth_date)->toDateString(),
            'death_date'              => optional($this->death_date)->toDateString(),
            'birth_place'            => $this->birth_place,
            'death_place'            => $this->death_place,
            'gender'                  => $this->gender,
            'official_website'       => $this->official_website,
            'wikidata_id'            => $this->wikidata_id,
            'wikipedia_url'          => $this->wikipedia_url,
            'notable_for'            => $this->notable_for,
            'occupation_summary'     => $this->occupation_summary,
            'social_handles'         => $this->social_handles,
            'is_influencer'          => $this->is_influencer,
            'search_boost'           => $this->search_boost,
            'short_bio'              => $this->short_bio,
            'long_bio'               => $this->long_bio,
            'source_url'             => $this->source_url,
            'last_updated_from_source' => optional($this->last_updated_from_source)->toIso8601String(),

            // Relaciones
            'nationality' => $this->whenLoaded('nationality', function () {
                return [
                    'id'   => $this->nationality->id,
                    'name' => $this->nationality->name,
                    'slug' => $this->nationality->slug,
                ];
            }),

            'language' => $this->whenLoaded('language', function () {
                return [
                    'id'   => $this->language->id,
                    'name' => $this->language->name,
                    'slug' => $this->language->slug,
                ];
            }),

            'image' => $this->whenLoaded('image', function () {
                return [
                    'id'  => $this->image->id,
                    'url' => $this->image->url,
                    'alt' => $this->image->alt ?? null,
                ];
            }),

            'appearance' => new AppearanceResource($this->whenLoaded('appearance')),
            'aliases' => AliasResource::collection($this->whenLoaded('aliases')),


        ];
    }
}
