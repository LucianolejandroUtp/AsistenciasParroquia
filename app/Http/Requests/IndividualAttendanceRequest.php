<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Student;

class IndividualAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Solo Admin y Profesor pueden registrar asistencias
        return Auth::check() && in_array(Auth::user()->userType->name, ['Admin', 'Profesor']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'attendance_session_id' => [
                'required',
                'integer',
                'exists:attendance_sessions,id',
                function ($attribute, $value, $fail) {
                    $session = AttendanceSession::find($value);
                    if (!$session || !$session->canTakeAttendance()) {
                        $fail('No se puede registrar asistencia para esta sesión.');
                    }
                },
            ],
            'student_id' => [
                'required',
                'integer',
                'exists:students,id',
                function ($attribute, $value, $fail) {
                    $student = Student::find($value);
                    if (!$student || $student->estado !== 'ACTIVO') {
                        $fail('El estudiante seleccionado no está activo.');
                    }
                },
            ],
            'status' => [
                'required',
                'string',
                'in:' . implode(',', Attendance::getValidStatuses()),
            ],
            'notes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'attendance_session_id.required' => 'La sesión es obligatoria.',
            'attendance_session_id.exists' => 'La sesión seleccionada no existe.',
            'student_id.required' => 'El estudiante es obligatorio.',
            'student_id.exists' => 'El estudiante seleccionado no existe.',
            'status.required' => 'El estado de asistencia es obligatorio.',
            'status.in' => 'El estado de asistencia debe ser: presente, ausente, tarde o justificado.',
            'notes.max' => 'Las observaciones no pueden exceder los 500 caracteres.',
        ];
    }

    /**
     * Get custom attribute names for error messages.
     */
    public function attributes(): array
    {
        return [
            'attendance_session_id' => 'sesión',
            'student_id' => 'estudiante',
            'status' => 'estado de asistencia',
            'notes' => 'observaciones',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Limpiar observaciones vacías
        if ($this->has('notes')) {
            $this->merge(['notes' => trim($this->notes) ?: null]);
        }
    }
}