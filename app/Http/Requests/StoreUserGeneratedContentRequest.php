<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest para validar la creación de contenido generado por usuarios.
 */
class StoreUserGeneratedContentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Permitir contenido público
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'related_type' => [
                'required',
                'string',
                'in:App\Models\NewsArticle,App\Models\MediaOutlet,App\Models\Event,App\Models\Festival,App\Models\PlantSpecies'
            ],
            'related_id' => 'required|integer|min:1',
            'content_type' => [
                'required',
                'string',
                'in:comment,review,question,answer,report,suggestion,testimonial,complaint,compliment'
            ],
            'content' => 'required|string|min:10|max:2000',
            'title' => 'nullable|string|max:255',
            'rating' => 'nullable|numeric|min:1|max:5|decimal:0,1',
            'parent_id' => 'nullable|integer|exists:user_generated_contents,id',
            
            // Información opcional para usuarios anónimos
            'user_name' => 'nullable|string|max:100',
            'user_email' => 'nullable|email|max:255',
            
            // Metadatos opcionales
            'location_name' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'related_type.required' => 'El tipo de contenido relacionado es obligatorio.',
            'related_type.in' => 'El tipo de contenido relacionado no es válido.',
            'related_id.required' => 'El ID del contenido relacionado es obligatorio.',
            'related_id.integer' => 'El ID del contenido relacionado debe ser un número.',
            'content_type.required' => 'El tipo de contenido es obligatorio.',
            'content_type.in' => 'El tipo de contenido debe ser: comment, review, question, answer, report, suggestion, testimonial, complaint o compliment.',
            'content.required' => 'El contenido es obligatorio.',
            'content.min' => 'El contenido debe tener al menos 10 caracteres.',
            'content.max' => 'El contenido no puede exceder 2000 caracteres.',
            'title.max' => 'El título no puede exceder 255 caracteres.',
            'rating.numeric' => 'La calificación debe ser un número.',
            'rating.min' => 'La calificación mínima es 1.',
            'rating.max' => 'La calificación máxima es 5.',
            'parent_id.exists' => 'El contenido padre no existe.',
            'user_name.max' => 'El nombre no puede exceder 100 caracteres.',
            'user_email.email' => 'El email debe tener un formato válido.',
            'user_email.max' => 'El email no puede exceder 255 caracteres.',
            'location_name.max' => 'El nombre de ubicación no puede exceder 255 caracteres.',
            'latitude.between' => 'La latitud debe estar entre -90 y 90.',
            'longitude.between' => 'La longitud debe estar entre -180 y 180.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'related_type' => 'tipo de contenido relacionado',
            'related_id' => 'ID del contenido relacionado',
            'content_type' => 'tipo de contenido',
            'content' => 'contenido',
            'title' => 'título',
            'rating' => 'calificación',
            'parent_id' => 'contenido padre',
            'user_name' => 'nombre de usuario',
            'user_email' => 'email de usuario',
            'location_name' => 'nombre de ubicación',
            'latitude' => 'latitud',
            'longitude' => 'longitud',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validar que si se proporciona rating, el content_type sea review
            if ($this->filled('rating') && $this->content_type !== 'review') {
                $validator->errors()->add('rating', 'La calificación solo es válida para reseñas (content_type: review).');
            }

            // Validar que si se proporciona parent_id, el content_type permita respuestas
            if ($this->filled('parent_id')) {
                $allowedReplies = ['comment', 'answer'];
                if (!in_array($this->content_type, $allowedReplies)) {
                    $validator->errors()->add('parent_id', 'Solo los comentarios y respuestas pueden tener un contenido padre.');
                }
            }

            // Si no hay usuario autenticado, requerir información anónima
            if (!auth()->check()) {
                if (!$this->filled('user_name')) {
                    $validator->errors()->add('user_name', 'El nombre es obligatorio para usuarios anónimos.');
                }
                if (!$this->filled('user_email')) {
                    $validator->errors()->add('user_email', 'El email es obligatorio para usuarios anónimos.');
                }
            }

            // Validar coordenadas juntas
            if ($this->filled('latitude') && !$this->filled('longitude')) {
                $validator->errors()->add('longitude', 'La longitud es obligatoria cuando se proporciona la latitud.');
            }
            if ($this->filled('longitude') && !$this->filled('latitude')) {
                $validator->errors()->add('latitude', 'La latitud es obligatoria cuando se proporciona la longitud.');
            }
        });
    }

    /**
     * Get the validated data from the request with additional processing.
     */
    public function getProcessedData(): array
    {
        $data = $this->validated();
        
        // Agregar información del usuario autenticado o anónimo
        if (auth()->check()) {
            $data['user_id'] = auth()->id();
            $data['is_anonymous'] = false;
        } else {
            $data['user_id'] = null;
            $data['is_anonymous'] = true;
        }

        // Agregar información técnica
        $data['user_ip'] = $this->ip();
        $data['user_agent'] = $this->userAgent();
        $data['language'] = 'es';
        $data['visibility'] = 'public';
        $data['status'] = 'pending'; // Siempre pendiente para moderación

        return $data;
    }
}