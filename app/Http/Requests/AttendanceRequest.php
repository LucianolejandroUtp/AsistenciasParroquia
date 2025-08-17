<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Student;

class AttendanceRequest extends FormRequest
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
        $rules = [
            'session_id' => [
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
            'attendances' => 'required|array|min:1',
            'attendances.*.student_id' => [
                'required',
                'integer',
                'exists:students,id',
            ],
            'attendances.*.status' => [
                'required',
                'string',
                'in:' . implode(',', Attendance::getValidStatuses()),
            ],
            'attendances.*.notes' => 'nullable|string|max:500',
        ];

        return $rules;
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'session_id.required' => 'La sesión es obligatoria.',
            'session_id.exists' => 'La sesión seleccionada no existe.',
            'attendances.required' => 'Debe registrar al menos una asistencia.',
            'attendances.array' => 'El formato de asistencias es inválido.',
            'attendances.min' => 'Debe registrar al menos una asistencia.',
            'attendances.*.student_id.required' => 'El estudiante es obligatorio.',
            'attendances.*.student_id.exists' => 'El estudiante seleccionado no existe.',
            'attendances.*.status.required' => 'El estado de asistencia es obligatorio.',
            'attendances.*.status.in' => 'El estado de asistencia debe ser: presente, ausente, tarde o justificado.',
            'attendances.*.notes.max' => 'Las observaciones no pueden exceder los 500 caracteres.',
        ];
    }

    /**
     * Get custom attribute names for error messages.
     */
    public function attributes(): array
    {
        return [
            'session_id' => 'sesión',
            'attendances.*.student_id' => 'estudiante',
            'attendances.*.status' => 'estado de asistencia',
            'attendances.*.notes' => 'observaciones',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Limpiar observaciones vacías
        if ($this->has('attendances')) {
            $attendances = $this->attendances;
            foreach ($attendances as $key => $attendance) {
                if (isset($attendance['notes'])) {
                    $attendances[$key]['notes'] = trim($attendance['notes']) ?: null;
                }
            }
            $this->merge(['attendances' => $attendances]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validar que no haya estudiantes duplicados
            if ($this->has('attendances')) {
                $studentIds = collect($this->attendances)->pluck('student_id');
                $duplicates = $studentIds->duplicates();
                
                if ($duplicates->isNotEmpty()) {
                    $validator->errors()->add('attendances', 'No se pueden registrar múltiples asistencias para el mismo estudiante.');
                }

                // Validar que todos los estudiantes pertenezcan a grupos activos
                $students = Student::whereIn('id', $studentIds)->where('estado', '!=', 'ACTIVO')->count();
                if ($students > 0) {
                    $validator->errors()->add('attendances', 'Algunos estudiantes seleccionados no están activos.');
                }
            }
        });
    }
}