<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Student;

class AttendanceController extends Controller
{
    /**
     * Mostrar interfaz de registro de asistencias.
     */
    public function register()
    {
        // Datos mock para registro de asistencias
        $mockSession = (object) [
            'id' => 1,
            'title' => 'Sesión de Catequesis - Los Sacramentos',
            'date' => '2025-08-17',
            'time' => '10:00',
            'groups' => 'A,B',
            'status' => 'active'
        ];

        $mockStudents = [
            (object) [
                'id' => 1,
                'full_name' => 'Antony Alexander Alférez Vilchez',
                'qr_code' => 'A-ANTONY-ALF-VILCH',
                'group_name' => 'Grupo A',
                'order_number' => 1,
                'attendance_status' => null, // No marcado aún
                'last_attendance' => '2025-08-15'
            ],
            (object) [
                'id' => 2,
                'full_name' => 'María Elena González Pérez',
                'qr_code' => 'A-MARIA-GON-PER',
                'group_name' => 'Grupo A',
                'order_number' => 2,
                'attendance_status' => 'present',
                'attendance_time' => '10:05'
            ],
            (object) [
                'id' => 3,
                'full_name' => 'Carlos Eduardo Ramírez Silva',
                'qr_code' => 'B-CARLOS-RAM-SIL',
                'group_name' => 'Grupo B',
                'order_number' => 1,
                'attendance_status' => 'late',
                'attendance_time' => '10:15'
            ],
            (object) [
                'id' => 4,
                'full_name' => 'Ana Sofía Mendoza Castro',
                'qr_code' => 'B-ANA-MEN-CAS',
                'group_name' => 'Grupo B',
                'order_number' => 2,
                'attendance_status' => 'present',
                'attendance_time' => '10:02'
            ]
        ];

        $stats = (object) [
            'total_students' => 78,
            'registered_today' => 45,
            'present_count' => 42,
            'late_count' => 3,
            'absent_count' => 33
        ];

        return view('attendances.register', compact('mockSession', 'mockStudents', 'stats'));
    }

    /**
     * Mostrar interfaz de escaneo QR.
     */
    public function qrScanner()
    {
        // Datos mock para el escáner QR
        $mockSession = (object) [
            'id' => 1,
            'title' => 'Sesión de Catequesis - Los Sacramentos',
            'date' => '2025-08-17',
            'time' => '10:00',
            'status' => 'active'
        ];

        $recentScans = [
            (object) [
                'student_name' => 'María Elena González Pérez',
                'qr_code' => 'A-MARIA-GON-PER',
                'scan_time' => '10:05',
                'status' => 'present',
                'group' => 'A'
            ],
            (object) [
                'student_name' => 'Ana Sofía Mendoza Castro',
                'qr_code' => 'B-ANA-MEN-CAS',
                'scan_time' => '10:02',
                'status' => 'present',
                'group' => 'B'
            ],
            (object) [
                'student_name' => 'Carlos Eduardo Ramírez Silva',
                'qr_code' => 'B-CARLOS-RAM-SIL',
                'scan_time' => '10:15',
                'status' => 'late',
                'group' => 'B'
            ]
        ];

        $scanStats = (object) [
            'total_scans' => 45,
            'successful_scans' => 42,
            'error_scans' => 3,
            'scan_rate' => 93
        ];

        return view('attendances.qr-scanner', compact('mockSession', 'recentScans', 'scanStats'));
    }

    /**
     * Mostrar historial de asistencias.
     */
    public function history()
    {
        // Mock data para historial de sesiones
        $mockSessions = collect([
            (object)[
                'id' => 1,
                'title' => 'Sesión 1: Los Sacramentos',
                'date' => '2025-08-14',
                'time' => '16:00',
                'status' => 'completed',
                'attendance_percentage' => 92,
                'present_count' => 72,
                'late_count' => 3,
                'absent_count' => 3,
                'total_students' => 78,
            ],
            (object)[
                'id' => 2,
                'title' => 'Sesión 2: La Eucaristía',
                'date' => '2025-08-07',
                'time' => '16:00',
                'status' => 'completed',
                'attendance_percentage' => 85,
                'present_count' => 66,
                'late_count' => 4,
                'absent_count' => 8,
                'total_students' => 78,
            ],
            (object)[
                'id' => 3,
                'title' => 'Sesión 3: La Oración del Señor',
                'date' => '2025-07-31',
                'time' => '16:00',
                'status' => 'completed',
                'attendance_percentage' => 78,
                'present_count' => 61,
                'late_count' => 5,
                'absent_count' => 12,
                'total_students' => 78,
            ],
            (object)[
                'id' => 4,
                'title' => 'Sesión 4: El Amor de Dios',
                'date' => '2025-07-24',
                'time' => '16:00',
                'status' => 'completed',
                'attendance_percentage' => 88,
                'present_count' => 69,
                'late_count' => 2,
                'absent_count' => 7,
                'total_students' => 78,
            ],
            (object)[
                'id' => 5,
                'title' => 'Sesión 5: Los Mandamientos',
                'date' => '2025-07-17',
                'time' => '16:00',
                'status' => 'completed',
                'attendance_percentage' => 94,
                'present_count' => 73,
                'late_count' => 2,
                'absent_count' => 3,
                'total_students' => 78,
            ],
            (object)[
                'id' => 6,
                'title' => 'Sesión 6: La Reconciliación',
                'date' => '2025-07-10',
                'time' => '16:00',
                'status' => 'completed',
                'attendance_percentage' => 58,
                'present_count' => 45,
                'late_count' => 8,
                'absent_count' => 25,
                'total_students' => 78,
            ],
        ]);

        // Mock estadísticas del historial
        $historyStats = (object)[
            'total_sessions' => 15,
            'average_attendance' => 82,
            'best_session' => 'Sesión 5: Los Mandamientos (94%)',
            'lowest_session' => 'Sesión 6: La Reconciliación (58%)',
        ];

        return view('attendances.history', compact('mockSessions', 'historyStats'));
    }
}