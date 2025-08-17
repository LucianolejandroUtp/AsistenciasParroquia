<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AttendanceSessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Solo Admin y Profesor pueden crear/editar sesiones
        return Auth::check() && in_array(Auth::user()->userType->name, ['Admin', 'Profesor']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'date' => [
                'required',
                'date',
                'after_or_equal:' . now()->subDays(7)->toDateString(), // No más de 7 días atrás
            ],
            'time' => 'nullable|date_format:H:i',
            'title' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ];

        // Validación adicional para creación (no fechas muy futuras)
        if ($this->isMethod('POST')) {
            $rules['date'][] = 'before_or_equal:' . now()->addMonths(6)->toDateString();
        }

        return $rules;
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'date.required' => 'La fecha de la sesión es obligatoria.',
            'date.date' => 'La fecha debe ser una fecha válida.',
            'date.after_or_equal' => 'No se pueden crear sesiones con más de 7 días de antigüedad.',
            'date.before_or_equal' => 'No se pueden crear sesiones con más de 6 meses de anticipación.',
            'time.date_format' => 'La hora debe tener el formato HH:MM (ejemplo: 16:30).',
            'title.max' => 'El título no puede exceder los 255 caracteres.',
            'notes.max' => 'Las observaciones no pueden exceder los 1000 caracteres.',
        ];
    }

    /**
     * Get custom attribute names for error messages.
     */
    public function attributes(): array
    {
        return [
            'date' => 'fecha',
            'time' => 'hora',
            'title' => 'título',
            'notes' => 'observaciones',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Limpiar y formatear datos antes de validación
        if ($this->has('title')) {
            $this->merge([
                'title' => trim($this->title) ?: null,
            ]);
        }

        if ($this->has('notes')) {
            $this->merge([
                'notes' => trim($this->notes) ?: null,
            ]);
        }

        // Formatear tiempo si se proporciona
        if ($this->has('time') && $this->time) {
            $this->merge([
                'time' => date('H:i', strtotime($this->time)),
            ]);
        }
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Agregar contexto adicional para errores de fecha
        if ($validator->errors()->has('date')) {
            $validator->errors()->add('date_context', 
                'Recuerda: Las sesiones se pueden crear desde 7 días atrás hasta 6 meses hacia adelante.'
            );
        }

        parent::failedValidation($validator);
    }
}