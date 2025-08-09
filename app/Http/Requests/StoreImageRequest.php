<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slug' => ['nullable', 'string', 'unique:images,slug'],
            'url' => ['required', 'url'],
            'alt_text' => ['nullable', 'string'],
            'source' => ['nullable', 'string'],
            'width' => ['nullable', 'integer', 'min:0'],
            'height' => ['nullable', 'integer', 'min:0'],
        ];
    }
}


