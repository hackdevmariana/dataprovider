<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVenueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:venues,slug',
            'address' => 'nullable|string|max:255',
            'municipality_id' => 'required|exists:municipalities,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'capacity' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'venue_type' => 'nullable|string|max:255',
            'venue_status' => 'nullable|string|max:255',
            'is_verified' => 'boolean',
        ];
    }
}
