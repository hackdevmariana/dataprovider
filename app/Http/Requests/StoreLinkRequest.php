<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // O lógica de autorización
    }

    public function rules(): array
    {
        return [
            'url' => 'required|url|max:255',
            'label' => 'nullable|string|max:255',
            'related_type' => 'required|string|max:255',
            'related_id' => 'required|integer',
            'type' => 'nullable|string|max:100',
            'is_primary' => 'boolean',
            'opens_in_new_tab' => 'boolean',
        ];
    }
}
