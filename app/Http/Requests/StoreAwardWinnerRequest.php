<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAwardWinnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'person_id' => 'required|exists:people,id',
            'award_id' => 'required|exists:awards,id',
            'year' => 'required|integer|min:1800|max:' . date('Y'),
            'classification' => 'nullable|string|max:255',
            'work_id' => 'nullable|exists:works,id',
            'municipality_id' => 'nullable|exists:municipalities,id',
        ];
    }
}


