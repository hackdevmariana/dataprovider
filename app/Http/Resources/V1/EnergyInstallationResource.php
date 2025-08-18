<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnergyInstallationResource extends JsonResource
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
            'type' => $this->type,
            'type_name' => $this->type_name,
            'capacity_kw' => $this->capacity_kw,
            'location' => $this->location,
            'status' => $this->status,
            'commissioned_at' => $this->commissioned_at?->format('Y-m-d'),
            'estimated_monthly_production_kwh' => $this->estimated_monthly_production,
            'owner' => $this->whenLoaded('owner', function() {
                return [
                    'id' => $this->owner->id,
                    'name' => $this->owner->name,
                    'email' => $this->owner->email,
                ];
            }),
            'energy_transactions_count' => $this->whenLoaded('energyTransactions', function() {
                return $this->energyTransactions->count();
            }),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}