<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkRequest extends FormRequest
{
    public function authorize()
    {
        return true; // O aplicar lógica de roles si lo deseas
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:works,slug',
            'type' => 'required|string|in:movie,book,tv_series',
            'description' => 'nullable|string',
            'release_year' => 'nullable|integer|min:1800|max:' . now()->year,
            'person_id' => 'required|exists:people,id',
            'genre' => 'nullable|string|max:255',
            'language_id' => 'nullable|exists:languages,id',
            'link_id' => 'nullable|exists:links,id',
        ];
    }
}
