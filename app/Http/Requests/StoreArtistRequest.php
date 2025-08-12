<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArtistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:artists,slug',
            'description' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'genre' => 'nullable|string|max:255',
            'person_id' => 'nullable|exists:people,id',
            'stage_name' => 'nullable|string|max:255',
            'group_name' => 'nullable|string|max:255',
            'active_years_start' => 'nullable|integer|min:1800|max:' . date('Y'),
            'active_years_end' => 'nullable|integer|min:1800|max:' . date('Y'),
            'bio' => 'nullable|string',
            'photo' => 'nullable|string',
            'social_links' => 'nullable|array',
            'language_id' => 'nullable|exists:languages,id',
        ];
    }
}
