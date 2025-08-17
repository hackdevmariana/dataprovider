<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnergyCompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'website' => $this->website,
            'contact' => [
                'phone_customer' => $this->phone_customer,
                'phone_commercial' => $this->phone_commercial,
                'email_customer' => $this->email_customer,
                'email_commercial' => $this->email_commercial,
            ],
            'highlighted_offer' => $this->highlighted_offer,
            'cnmc_id' => $this->cnmc_id,
            'logo_url' => $this->logo_url,
            'company_type' => $this->company_type,
            'address' => $this->address,
            'location' => $this->when($this->latitude && $this->longitude, [
                'latitude' => (float) $this->latitude,
                'longitude' => (float) $this->longitude,
            ]),
            'coverage_scope' => $this->coverage_scope,
            'municipality' => $this->whenLoaded('municipality', function () {
                return [
                    'id' => $this->municipality->id,
                    'name' => $this->municipality->name,
                    'slug' => $this->municipality->slug,
                ];
            }),
            'image' => $this->whenLoaded('image', function () {
                return [
                    'id' => $this->image->id,
                    'url' => $this->image->url,
                    'alt_text' => $this->image->alt_text,
                ];
            }),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}