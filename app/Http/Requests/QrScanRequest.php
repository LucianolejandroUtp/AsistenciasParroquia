<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\AttendanceSession;

class QrScanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Solo Admin y Profesor pueden escanear QRs
        return Auth::check() && in_array(Auth::user()->userType->name, ['Admin', 'Profesor']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'qr_code' => [
                'required',
                'string',
                'regex:/^[AB]-[A-Z]+-[A-Z]+-[A-Z]+$/', // Formato: A-ANTONY-ALF-VILCH
                function ($attribute, $value, $fail) {
                    $student = Student::where('student_code', $value)->where('estado', 'ACTIVO')->first();
                    if (!$student) {
                        $fail('Código QR no válido o estudiante inactivo.');
                    }
                },
            ],
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
            'status' => [
                'nullable',
                'string',
                'in:present,late', // Solo permitir presente o tarde para QR
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
            'qr_code.required' => 'El código QR es obligatorio.',
            'qr_code.regex' => 'El formato del código QR no es válido.',
            'attendance_session_id.required' => 'La sesión es obligatoria.',
            'attendance_session_id.exists' => 'La sesión seleccionada no existe.',
            'status.in' => 'El estado debe ser presente o tarde.',
            'notes.max' => 'Las observaciones no pueden exceder los 500 caracteres.',
        ];
    }

    /**
     * Get custom attribute names for error messages.
     */
    public function attributes(): array
    {
        return [
            'qr_code' => 'código QR',
            'attendance_session_id' => 'sesión',
            'status' => 'estado de asistencia',
            'notes' => 'observaciones',
        ];
    }

    /**
     * Get the validated data with defaults.
     */
    public function validatedWithDefaults(): array
    {
        $data = $this->validated();
        
        // Si no se especifica status, usar 'present' por defecto
        if (!isset($data['status'])) {
            $data['status'] = 'present';
        }

        return $data;
    }

    /**
     * Get the student from the QR code.
     */
    public function getStudent(): ?Student
    {
        return Student::where('student_code', $this->qr_code)
            ->where('estado', 'ACTIVO')
            ->first();
    }
}