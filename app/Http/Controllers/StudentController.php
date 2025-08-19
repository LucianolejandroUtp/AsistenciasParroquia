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

        // Calcular estadísticas generales
        $totalStudents = Student::count();
        $activeStudents = Student::where('estado', 'ACTIVO')->count();
        $averageAttendance = $students->avg('attendance_percentage');
        
        // Obtener estadísticas por grupo dinámicamente
        $groupStats = Group::withCount(['students' => function($query) {
            $query->where('estado', 'ACTIVO');
        }])->get();

        // Obtener todos los grupos para filtros
        $groups = Group::orderBy('name')->get();

        $stats = (object) [
            'total_students' => $totalStudents,
            'active_students' => $activeStudents,
            'average_attendance' => round($averageAttendance, 1),
            'groups' => $groupStats
        ];

        return view('students.index', compact('students', 'stats', 'groups'));
    }





    /**
     * Mostrar códigos QR de estudiantes.
     */
    public function qrCodes()
    {
        // Obtener datos reales de estudiantes con códigos QR
        $qrCodes = Student::with('group')
            ->where('estado', 'ACTIVO')
            ->orderBy('names')
            ->take(10) // Mostrar solo los primeros 10 para demo
            ->get()
            ->map(function ($student) {
                return (object) [
                    'id' => $student->id,
                    'full_name' => $student->names . ' ' . $student->paternal_surname . ($student->maternal_surname ? ' ' . $student->maternal_surname : ''),
                    'qr_code' => $student->qr_code,
                    'group_name' => $student->group ? $student->group->name : 'Sin Grupo',
                    'qr_svg' => '<svg>...</svg>', // Placeholder para QR generado
                    'last_scanned' => $student->updated_at->format('Y-m-d H:i:s'),
                    'total_scans' => rand(5, 15) // Mock temporal para total_scans
                ];
            });

        $qrStats = (object) [
            'total_codes' => 78,
            'active_codes' => 76,
            'total_scans_today' => 45,
            'last_generated' => '2025-08-10 09:00:00'
        ];

        return view('students.qr-codes', compact('qrCodes', 'qrStats'));
    }
}