<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CooperativeResource extends JsonResource
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
            'legal_name' => $this->legal_name,
            'cooperative_type' => $this->cooperative_type,
            'cooperative_type_name' => $this->cooperative_type_name,
            'scope' => $this->scope,
            'scope_name' => $this->scope_name,
            'nif' => $this->nif,
            'founded_at' => $this->founded_at?->format('Y-m-d'),
            'years_since_founded' => $this->years_since_founded,
            'contact' => [
                'phone' => $this->phone,
                'email' => $this->email,
                'website' => $this->website,
                'address' => $this->address,
            ],
            'logo_url' => $this->logo_url,
            'location' => [
                'address' => $this->address,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'municipality' => $this->whenLoaded('municipality', function() {
                    return [
                        'id' => $this->municipality->id,
                        'name' => $this->municipality->name,
                        'slug' => $this->municipality->slug,
                    ];
                }),
            ],
            'description' => $this->description,
            'number_of_members' => $this->number_of_members,
            'active_members' => $this->whenLoaded('users', function() {
                return $this->users->where('pivot.is_active', true)->count();
            }),
            'main_activity' => $this->main_activity,
            'features' => [
                'is_open_to_new_members' => $this->is_open_to_new_members,
                'has_energy_market_access' => $this->has_energy_market_access,
                'accepts_new_installations' => $this->accepts_new_installations,
                'is_active_for_projects' => $this->is_active_for_projects,
            ],
            'legal_form' => $this->legal_form,
            'statutes_url' => $this->statutes_url,
            'source' => $this->source,
            'image' => $this->whenLoaded('image', function() {
                return [
                    'id' => $this->image->id,
                    'url' => $this->image->url ?? $this->image->path,
                    'alt' => $this->image->alt_text,
                ];
            }),
            'members' => $this->whenLoaded('users', function() {
                return $this->users->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->pivot->role,
                        'joined_at' => $user->pivot->joined_at,
                        'is_active' => $user->pivot->is_active,
                    ];
                });
            }),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}