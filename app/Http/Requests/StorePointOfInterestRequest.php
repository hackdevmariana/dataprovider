<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePointOfInterestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'slug' => 'required|string|unique:point_of_interests,slug',
            'address' => 'nullable|string',
            'type' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'municipality_id' => 'required|exists:municipalities,id',
            'source' => 'nullable|string',
            'description' => 'nullable|string',
            'is_cultural_center' => 'boolean',
            'is_energy_installation' => 'boolean',
            'is_cooperative_office' => 'boolean',
            'opening_hours' => 'nullable|array',
        ];
    }
}


