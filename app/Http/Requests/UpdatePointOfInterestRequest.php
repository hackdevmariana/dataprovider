<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePointOfInterestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name' => 'sometimes|string',
            'slug' => "sometimes|string|unique:point_of_interests,slug,$id",
            'address' => 'nullable|string',
            'type' => 'nullable|string',
            'latitude' => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',
            'municipality_id' => 'sometimes|exists:municipalities,id',
            'source' => 'nullable|string',
            'description' => 'nullable|string',
            'is_cultural_center' => 'boolean',
            'is_energy_installation' => 'boolean',
            'is_cooperative_office' => 'boolean',
            'opening_hours' => 'nullable|array',
        ];
    }
}


