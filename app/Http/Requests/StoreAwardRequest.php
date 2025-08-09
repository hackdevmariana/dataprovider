<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAwardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:awards,slug',
            'description' => 'nullable|string',
            'awarded_by' => 'nullable|string|max:255',
            'first_year_awarded' => 'nullable|integer|min:1800|max:' . date('Y'),
            'category' => 'nullable|string|max:255',
        ];
    }
}


