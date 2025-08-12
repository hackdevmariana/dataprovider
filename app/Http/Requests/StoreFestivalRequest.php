<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFestivalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:festivals,slug',
            'description' => 'nullable|string',
            'month' => 'nullable|integer|min:1|max:12',
            'usual_days' => 'nullable|string',
            'recurring' => 'boolean',
            'location_id' => 'nullable|exists:municipalities,id',
            'logo_url' => 'nullable|string',
            'color_theme' => 'nullable|string|max:32',
        ];
    }
}
