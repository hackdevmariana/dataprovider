<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest para validar el registro de interacciones con contactos de medios.
 */
class RecordInteractionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Solo usuarios autenticados pueden registrar interacciones
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => [
                'required',
                'string',
                'in:email,phone,mobile_phone,meeting,event,interview,press_release,social_media,whatsapp,video_call,in_person'
            ],
            'description' => 'required|string|min:10|max:500',
            'successful' => 'boolean',
            'follow_up_needed' => 'boolean',
            'follow_up_date' => 'nullable|date|after:today',
            'context' => 'nullable|string|max:255',
            'outcome' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'type.required' => 'El tipo de interacción es obligatorio.',
            'type.in' => 'El tipo de interacción debe ser: email, phone, mobile_phone, meeting, event, interview, press_release, social_media, whatsapp, video_call o in_person.',
            'description.required' => 'La descripción es obligatoria.',
            'description.min' => 'La descripción debe tener al menos 10 caracteres.',
            'description.max' => 'La descripción no puede exceder 500 caracteres.',
            'successful.boolean' => 'El campo exitoso debe ser verdadero o falso.',
            'follow_up_needed.boolean' => 'El campo seguimiento necesario debe ser verdadero o falso.',
            'follow_up_date.date' => 'La fecha de seguimiento debe ser una fecha válida.',
            'follow_up_date.after' => 'La fecha de seguimiento debe ser posterior a hoy.',
            'context.max' => 'El contexto no puede exceder 255 caracteres.',
            'outcome.max' => 'El resultado no puede exceder 255 caracteres.',
            'notes.max' => 'Las notas no pueden exceder 1000 caracteres.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'type' => 'tipo de interacción',
            'description' => 'descripción',
            'successful' => 'exitoso',
            'follow_up_needed' => 'seguimiento necesario',
            'follow_up_date' => 'fecha de seguimiento',
            'context' => 'contexto',
            'outcome' => 'resultado',
            'notes' => 'notas',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Si se requiere seguimiento, debe tener fecha
            if ($this->boolean('follow_up_needed') && !$this->filled('follow_up_date')) {
                $validator->errors()->add('follow_up_date', 'La fecha de seguimiento es obligatoria cuando se requiere seguimiento.');
            }

            // Validaciones específicas por tipo de interacción
            $interactionType = $this->input('type');
            
            if (in_array($interactionType, ['meeting', 'event', 'interview']) && !$this->filled('context')) {
                $validator->errors()->add('context', 'El contexto es obligatorio para reuniones, eventos y entrevistas.');
            }

            if ($interactionType === 'press_release' && !$this->filled('outcome')) {
                $validator->errors()->add('outcome', 'El resultado es obligatorio para comunicados de prensa.');
            }
        });
    }

    /**
     * Get the processed data for creating the interaction record.
     */
    public function getProcessedData(): array
    {
        $data = $this->validated();
        
        // Agregar información del usuario que registra la interacción
        $data['recorded_by'] = auth()->id();
        $data['recorded_at'] = now();
        
        // Agregar información técnica
        $data['ip_address'] = $this->ip();
        $data['user_agent'] = $this->userAgent();
        
        return $data;
    }

    /**
     * Get the interaction type in Spanish.
     */
    public function getInteractionTypeName(): string
    {
        $types = [
            'email' => 'Email',
            'phone' => 'Teléfono',
            'mobile_phone' => 'Teléfono móvil',
            'meeting' => 'Reunión',
            'event' => 'Evento',
            'interview' => 'Entrevista',
            'press_release' => 'Comunicado de prensa',
            'social_media' => 'Redes sociales',
            'whatsapp' => 'WhatsApp',
            'video_call' => 'Videollamada',
            'in_person' => 'En persona',
        ];

        return $types[$this->input('type')] ?? 'Desconocido';
    }
}