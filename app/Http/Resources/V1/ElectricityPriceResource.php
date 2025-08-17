<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ElectricityPriceResource extends JsonResource
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
            'date' => $this->date->format('Y-m-d'),
            'hour' => $this->hour,
            'datetime' => $this->hour !== null ? $this->date->format('Y-m-d') . ' ' . sprintf('%02d:00:00', $this->hour) : null,
            'type' => $this->type,
            'price_eur_mwh' => (float) $this->price_eur_mwh,
            'price_eur_kwh' => (float) $this->price_eur_mwh / 1000, // Conversión a €/kWh
            'price_min' => $this->when($this->price_min, (float) $this->price_min),
            'price_max' => $this->when($this->price_max, (float) $this->price_max),
            'price_avg' => $this->when($this->price_avg, (float) $this->price_avg),
            'forecast_for_tomorrow' => $this->forecast_for_tomorrow,
            'source' => $this->source,
            'price_unit' => $this->whenLoaded('priceUnit', function () {
                return [
                    'id' => $this->priceUnit->id,
                    'name' => $this->priceUnit->name,
                    'short_name' => $this->priceUnit->short_name,
                    'unit_code' => $this->priceUnit->unit_code,
                ];
            }),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}