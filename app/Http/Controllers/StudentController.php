<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Group;

class StudentController extends Controller
{
    /**
     * Mostrar lista completa de estudiantes.
     */
    public function index()
    {
        // Obtener todos los estudiantes con sus grupos y estadísticas de asistencia
        $students = Student::with(['group', 'attendances.attendanceSession'])
            ->orderBy('names')
            ->orderBy('paternal_surname')
            ->get()
            ->map(function ($student) {
                // Calcular estadísticas de asistencia
                $totalSessions = $student->attendances->pluck('attendanceSession')->unique('id')->count();
                $attendedSessions = $student->attendances->count();
                $attendancePercentage = $totalSessions > 0 ? round(($attendedSessions / $totalSessions) * 100) : 0;

                return (object) [
                    'id' => $student->id,
                    'full_name' => $student->names . ' ' . $student->paternal_surname . ($student->maternal_surname ? ' ' . $student->maternal_surname : ''),
                    'names' => $student->names,
                    'paternal_surname' => $student->paternal_surname,
                    'maternal_surname' => $student->maternal_surname,
                    'qr_code' => $student->qr_code,
                    'student_code' => $student->student_code,
                    'group_name' => $student->group ? $student->group->name : 'Sin Grupo',
                    'group_id' => $student->group_id,
                    'order_number' => $student->order_number,
                    'status' => $student->estado,
                    'total_sessions' => $totalSessions,
                    'attended_sessions' => $attendedSessions,
                    'attendance_percentage' => $attendancePercentage,
                    'created_at' => $student->created_at->format('d/m/Y'),
                    'updated_at' => $student->updated_at->format('d/m/Y H:i')
                ];
            });

        // Obtener todos los grupos para filtros
        $groups = Group::orderBy('name')->get();

        return view('students.index', compact('students', 'groups'));
    }





    /**
     * Mostrar códigos QR de estudiantes.
     */
    public function qrCodes(Request $request)
    {
        // Parámetros de paginación
    $perPage = $request->get('per_page', 20); // 20 por defecto
        $page = $request->get('page', 1);
        
        // Construir query y aplicar filtros (grupo y búsqueda global)
        $query = Student::with('group')
            ->where('estado', 'ACTIVO');

        // Filtrar por grupo si se proporciona
        if ($request->filled('group')) {
            $query->where('group_id', $request->input('group'));
        }

        // Búsqueda global: nombres, apellidos, código QR y código de estudiante
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('names', 'like', "%{$q}%")
                    ->orWhere('paternal_surname', 'like', "%{$q}%")
                    ->orWhere('maternal_surname', 'like', "%{$q}%")
                    ->orWhere('student_code', 'like', "%{$q}%");
            });
        }

        // Ejecutar paginación y mantener query string para links
        $qrCodes = $query->orderBy('names')->paginate($perPage)->withQueryString();

        // Mapear datos para la vista
        $qrCodes->getCollection()->transform(function ($student) {
            return (object) [
                'id' => $student->id,
                'full_name' => $student->names . ' ' . $student->paternal_surname . ($student->maternal_surname ? ' ' . $student->maternal_surname : ''),
                'qr_code' => $student->student_code,
                'group_name' => $student->group ? $student->group->name : 'Sin Grupo',
                'last_scanned' => $student->updated_at->format('Y-m-d H:i:s'),
                'total_scans' => rand(5, 15) // Mock temporal para total_scans
            ];
        });

        $qrStats = (object) [
            'total_codes' => Student::where('estado', 'ACTIVO')->count(),
            'active_codes' => Student::where('estado', 'ACTIVO')->count(),
            'total_scans_today' => 45,
            'last_generated' => '2025-08-10 09:00:00'
        ];

        return view('students.qr-codes', compact('qrCodes', 'qrStats'));
    }

    /**
     * Devuelve partial con detalles del estudiante (para peticiones AJAX)
     */
    public function details(Request $request, Student $student)
    {
        // Mapear a objeto simple igual que en index
        $totalSessions = $student->attendances->pluck('attendanceSession')->unique('id')->count();
        $attendedSessions = $student->attendances->count();
        $attendancePercentage = $totalSessions > 0 ? round(($attendedSessions / $totalSessions) * 100) : 0;

        $dto = (object) [
            'id' => $student->id,
            'full_name' => $student->names . ' ' . $student->paternal_surname . ($student->maternal_surname ? ' ' . $student->maternal_surname : ''),
            'names' => $student->names,
            'paternal_surname' => $student->paternal_surname,
            'maternal_surname' => $student->maternal_surname,
            'group_name' => $student->group ? $student->group->name : 'Sin Grupo',
            'order_number' => $student->order_number,
            'status' => $student->estado,
            'total_sessions' => $totalSessions,
            'attended_sessions' => $attendedSessions,
            'attendance_percentage' => $attendancePercentage,
        ];

        if ($request->ajax()) {
            return view('students.partials._details', ['student' => $dto]);
        }

        // Fallback: redirigir a la vista completa (o mostrar una página simple)
        return redirect()->route('students.index');
    }

    /**
     * Devuelve partial con el formulario de edición (AJAX)
     */
    public function edit(Request $request, Student $student)
    {
        // Mapear a objeto simple para la vista
        $dto = (object) [
            'id' => $student->id,
            'names' => $student->names,
            'paternal_surname' => $student->paternal_surname,
            'maternal_surname' => $student->maternal_surname,
            'group_id' => $student->group_id,
            'order_number' => $student->order_number,
            'student_code' => $student->student_code,
            'status' => $student->estado,
        ];

        if ($request->ajax()) {
            $groups = Group::orderBy('name')->get();
            return view('students.partials._edit_form', ['student' => $dto, 'groups' => $groups]);
        }

        return redirect()->route('students.index');
    }

    /**
     * Actualiza los datos del estudiante (PUT)
     */
    public function update(Request $request, Student $student)
    {
        // Validar campos; usar nombres exactos según migración
        $data = $request->validate([
            'names' => 'required|string|max:191',
            'paternal_surname' => 'required|string|max:191',
            'maternal_surname' => 'nullable|string|max:191',
            'group_id' => 'nullable|exists:groups,id',
            'order_number' => 'required|integer|min:1',
            'student_code' => 'nullable|string|max:100|unique:students,student_code,' . $student->id,
            'status' => 'required|in:ACTIVO,INACTIVO,ELIMINADO'
        ]);

        // Asignar y guardar
        $student->names = $data['names'];
        $student->paternal_surname = $data['paternal_surname'];
        $student->maternal_surname = $data['maternal_surname'] ?? null;
        $student->group_id = $data['group_id'] ?? null;
        $student->order_number = $data['order_number'];
        $student->student_code = $data['student_code'] ?? null;
        $student->estado = $data['status'];
        $student->save();

        if ($request->ajax()) {
            return response()->json(['message' => 'Estudiante actualizado correctamente', 'student_id' => $student->id]);
        }

        return redirect()->route('students.index')->with('success', 'Estudiante actualizado correctamente');
    }
}