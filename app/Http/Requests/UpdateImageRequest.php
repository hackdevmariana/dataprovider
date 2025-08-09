<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $imageId = $this->route('id');

        return [
            'slug' => ['nullable', 'string', Rule::unique('images', 'slug')->ignore($imageId)],
            'url' => ['sometimes', 'url'],
            'alt_text' => ['nullable', 'string'],
            'source' => ['nullable', 'string'],
            'width' => ['nullable', 'integer', 'min:0'],
            'height' => ['nullable', 'integer', 'min:0'],
        ];
    }
}


