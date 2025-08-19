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
        $groupACount = Student::whereHas('group', function($query) {
            $query->where('name', 'like', '%A%');
        })->count();
        $groupBCount = Student::whereHas('group', function($query) {
            $query->where('name', 'like', '%B%');
        })->count();
        $activeStudents = Student::where('estado', 'ACTIVO')->count();
        $averageAttendance = $students->avg('attendance_percentage');

        $stats = (object) [
            'total_students' => $totalStudents,
            'group_a_count' => $groupACount,
            'group_b_count' => $groupBCount,
            'active_students' => $activeStudents,
            'average_attendance' => round($averageAttendance, 1)
        ];

        return view('students.index', compact('students', 'stats'));
    }

    /**
     * Mostrar estudiantes del Grupo A.
     */
    public function groupA()
    {
        // Datos mock específicos del Grupo A
        $mockStudents = [
            (object) [
                'id' => 1,
                'full_name' => 'Antony Alexander Alférez Vilchez',
                'qr_code' => 'A-ANTONY-ALF-VILCH',
                'order_number' => 1,
                'status' => 'ACTIVO',
                'total_sessions' => 12,
                'attended_sessions' => 10,
                'attendance_percentage' => 83
            ],
            (object) [
                'id' => 2,
                'full_name' => 'María Elena González Pérez',
                'qr_code' => 'A-MARIA-GON-PER',
                'order_number' => 2,
                'status' => 'ACTIVO',
                'total_sessions' => 12,
                'attended_sessions' => 11,
                'attendance_percentage' => 92
            ],
            (object) [
                'id' => 3,
                'full_name' => 'José Miguel Torres Vargas',
                'qr_code' => 'A-JOSE-TOR-VAR',
                'order_number' => 3,
                'status' => 'ACTIVO',
                'total_sessions' => 12,
                'attended_sessions' => 8,
                'attendance_percentage' => 67
            ]
        ];

        $groupStats = (object) [
            'group_name' => 'Grupo A',
            'total_students' => 40,
            'active_students' => 39,
            'average_attendance' => 85,
            'next_session' => '2025-08-20 10:00:00'
        ];

        return view('students.group-a', compact('mockStudents', 'groupStats'));
    }

    /**
     * Mostrar estudiantes del Grupo B.
     */
    public function groupB()
    {
        // Datos mock específicos del Grupo B
        $mockStudents = [
            (object) [
                'id' => 41,
                'full_name' => 'Carlos Eduardo Ramírez Silva',
                'qr_code' => 'B-CARLOS-RAM-SIL',
                'order_number' => 1,
                'status' => 'ACTIVO',
                'total_sessions' => 12,
                'attended_sessions' => 9,
                'attendance_percentage' => 75
            ],
            (object) [
                'id' => 42,
                'full_name' => 'Ana Sofía Mendoza Castro',
                'qr_code' => 'B-ANA-MEN-CAS',
                'order_number' => 2,
                'status' => 'ACTIVO',
                'total_sessions' => 12,
                'attended_sessions' => 12,
                'attendance_percentage' => 100
            ],
            (object) [
                'id' => 43,
                'full_name' => 'Diego Alejandro Herrera López',
                'qr_code' => 'B-DIEGO-HER-LOP',
                'order_number' => 3,
                'status' => 'ACTIVO',
                'total_sessions' => 12,
                'attended_sessions' => 10,
                'attendance_percentage' => 83
            ]
        ];

        $groupStats = (object) [
            'group_name' => 'Grupo B',
            'total_students' => 38,
            'active_students' => 37,
            'average_attendance' => 89,
            'next_session' => '2025-08-20 15:00:00'
        ];

        return view('students.group-b', compact('mockStudents', 'groupStats'));
    }

    /**
     * Mostrar códigos QR de estudiantes.
     */
    public function qrCodes()
    {
        // Datos mock para códigos QR
        $mockQrCodes = [
            (object) [
                'id' => 1,
                'full_name' => 'Antony Alexander Alférez Vilchez',
                'qr_code' => 'A-ANTONY-ALF-VILCH',
                'group_name' => 'Grupo A',
                'qr_svg' => '<svg>...</svg>', // Placeholder para QR generado
                'last_scanned' => '2025-08-15 10:30:00',
                'total_scans' => 10
            ],
            (object) [
                'id' => 2,
                'full_name' => 'María Elena González Pérez',
                'qr_code' => 'A-MARIA-GON-PER',
                'group_name' => 'Grupo A',
                'qr_svg' => '<svg>...</svg>',
                'last_scanned' => '2025-08-16 10:25:00',
                'total_scans' => 11
            ],
            (object) [
                'id' => 3,
                'full_name' => 'Carlos Eduardo Ramírez Silva',
                'qr_code' => 'B-CARLOS-RAM-SIL',
                'group_name' => 'Grupo B',
                'qr_svg' => '<svg>...</svg>',
                'last_scanned' => '2025-08-14 15:15:00',
                'total_scans' => 9
            ]
        ];

        $qrStats = (object) [
            'total_codes' => 78,
            'active_codes' => 76,
            'total_scans_today' => 45,
            'last_generated' => '2025-08-10 09:00:00'
        ];

        return view('students.qr-codes', compact('mockQrCodes', 'qrStats'));
    }
}