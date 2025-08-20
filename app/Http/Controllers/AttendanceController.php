<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Student;
use App\Models\Group;
use App\Http\Requests\AttendanceRequest;
use App\Http\Requests\QrScanRequest;
use App\Http\Requests\IndividualAttendanceRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /**
     * Mostrar interfaz de registro de asistencias.
     */
    public function register(Request $request)
    {
        // Obtener sesiones activas para el selector
        $activeSessions = AttendanceSession::where('estado', 'ACTIVO')
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->get();

        // Obtener sesión seleccionada o la más reciente
        $sessionId = $request->get('session_id');
        $selectedSession = null;
        
        if ($sessionId) {
            $selectedSession = AttendanceSession::with(['attendances.student'])
                ->where('id', $sessionId)
                ->where('estado', 'ACTIVO')
                ->first();
        } else {
            $selectedSession = $activeSessions->first();
        }

        $students = collect();
        $stats = (object) [
            'total_students' => 0,
            'registered_today' => 0,
            'present_count' => 0,
            'late_count' => 0,
            'absent_count' => 0
        ];

        if ($selectedSession) {
            // Obtener todos los estudiantes activos (ya que no hay restricción por grupos en esta sesión)
            $students = Student::with(['group', 'attendances' => function($query) use ($selectedSession) {
                    $query->where('attendance_session_id', $selectedSession->id);
                }])
                ->where('estado', 'ACTIVO')
                ->orderBy('group_id')
                ->orderBy('order_number')
                ->get()
                ->map(function($student) use ($selectedSession) {
                    $attendance = $student->attendances->first();
                    
                    return (object) [
                        'id' => $student->id,
                        'full_name' => $student->full_name,
                        'qr_code' => $student->qr_code,
                        'group_name' => $student->group->name,
                        'order_number' => $student->order_number,
                        'attendance_status' => $attendance?->status,
                        'attendance_time' => $attendance?->created_at?->format('H:i'),
                        'last_attendance' => $student->attendances()
                            ->where('attendance_session_id', '!=', $selectedSession->id)
                            ->latest('created_at')
                            ->first()?->created_at?->format('Y-m-d')
                    ];
                });

            // Calcular estadísticas reales
            $totalStudents = $students->count();
            $presentCount = $students->where('attendance_status', 'present')->count();
            $lateCount = $students->where('attendance_status', 'late')->count();
            $registeredToday = $presentCount + $lateCount;
            $absentCount = $totalStudents - $registeredToday;

            $stats = (object) [
                'total_students' => $totalStudents,
                'registered_today' => $registeredToday,
                'present_count' => $presentCount,
                'late_count' => $lateCount,
                'absent_count' => $absentCount
            ];
        }

        return view('attendances.register', compact('activeSessions', 'selectedSession', 'students', 'stats'));
    }

    /**
     * Guardar registro de asistencia individual.
     */
    public function store(IndividualAttendanceRequest $request)
    {
        try {
            DB::beginTransaction();

            $student = Student::findOrFail($request->student_id);
            $session = AttendanceSession::findOrFail($request->attendance_session_id);

            // Verificar que la sesión permita registro de asistencias
            if (!$session->canTakeAttendance()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pueden registrar asistencias en esta sesión. La sesión puede estar cerrada o inactiva.'
                ], 422);
            }

            // Marcar asistencia usando el método del modelo
            $attendance = Attendance::markAttendance(
                $session->id,
                $student->id,
                $request->status,
                $request->notes
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Asistencia registrada para {$student->full_name}",
                'data' => [
                    'attendance_id' => $attendance->id,
                    'student_name' => $student->full_name,
                    'status' => $attendance->status_display,
                    'marked_at' => $attendance->created_at->format('H:i'),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar asistencia: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Mostrar interfaz de escaneo QR.
     */
    public function qrScanner(Request $request)
    {
        // Obtener sesiones activas
        $activeSessions = AttendanceSession::where('estado', 'ACTIVO')
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->get();

        // Obtener sesión seleccionada
        $sessionId = $request->get('session_id');
        $selectedSession = null;
        
        if ($sessionId) {
            $selectedSession = AttendanceSession::findOrFail($sessionId);
        } else {
            $selectedSession = $activeSessions->first();
        }

        $recentScans = collect();
        $scanStats = (object) [
            'total_scans' => 0,
            'successful_scans' => 0,
            'error_scans' => 0,
            'scan_rate' => 0
        ];

        if ($selectedSession) {
            // Obtener escaneos recientes de la sesión (últimos 10)
            $recentScans = Attendance::with(['student.group'])
                ->where('attendance_session_id', $selectedSession->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function($attendance) {
                    return (object) [
                        'student_name' => $attendance->student->full_name,
                        'qr_code' => $attendance->student->qr_code, // Usar accessor
                        'scan_time' => $attendance->created_at->format('H:i'),
                        'status' => $attendance->status,
                        'group' => $attendance->student->group->name
                    ];
                });

            // Estadísticas de escaneo (asumiendo que todos los registros son exitosos)
            $totalScans = Attendance::where('attendance_session_id', $selectedSession->id)->count();
            $scanStats = (object) [
                'total_scans' => $totalScans,
                'successful_scans' => $totalScans,
                'error_scans' => 0,
                'scan_rate' => $totalScans > 0 ? 100 : 0
            ];
        }

        return view('attendances.qr-scanner', compact('activeSessions', 'selectedSession', 'recentScans', 'scanStats'));
    }

    /**
     * Procesar escaneo de código QR.
     */
    public function processQrScan(QrScanRequest $request)
    {
        try {
            DB::beginTransaction();

            // Buscar estudiante por código QR
            $student = Student::where('student_code', $request->qr_code)
                ->where('estado', 'ACTIVO')
                ->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Código QR no válido o estudiante no encontrado'
                ], 404);
            }

            $session = AttendanceSession::findOrFail($request->attendance_session_id);

            // Verificar que la sesión permita registro de asistencias
            if (!$session->canTakeAttendance()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pueden registrar asistencias en esta sesión. La sesión puede estar cerrada o inactiva.'
                ], 422);
            }

            // En este esquema, todas las sesiones permiten todos los estudiantes
            // No hay restricción por grupos específicos

            // Obtener datos validados con defaults aplicados
            $validatedData = $request->validatedWithDefaults();

            // Marcar asistencia (el status se determina automáticamente en QrScanRequest)
            $attendance = Attendance::markAttendance(
                $session->id,
                $student->id,
                $validatedData['status'],
                $validatedData['notes'] ?? null
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "¡Asistencia registrada exitosamente!",
                'data' => [
                    'student_name' => $student->full_name,
                    'group' => $student->group->name,
                    'status' => $attendance->status_display,
                    'marked_at' => $attendance->created_at->format('H:i'),
                    'qr_code' => $student->qr_code
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar código QR: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Mostrar historial de asistencias.
     */
    public function history(Request $request)
    {
        // Filtros
        $groupId = $request->get('group_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Query para sesiones con asistencias
        $sessionsQuery = AttendanceSession::with(['attendances.student'])
            ->where('estado', 'ACTIVO')
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc');

        // Aplicar filtros
        if ($groupId) {
            $sessionsQuery->whereHas('attendances.student', function($query) use ($groupId) {
                $query->where('group_id', $groupId);
            });
        }

        if ($startDate) {
            $sessionsQuery->where('date', '>=', Carbon::parse($startDate));
        }

        if ($endDate) {
            $sessionsQuery->where('date', '<=', Carbon::parse($endDate));
        }

        $sessions = $sessionsQuery->paginate(10)->through(function($session) {
            $summary = $session->getSessionSummary();
            
            return (object)[
                'id' => $session->id,
                'title' => $session->title,
                'date' => $session->date->format('Y-m-d'),
                'time' => $session->time ? $session->time->format('H:i') : '00:00',
                'status' => 'completed', // Todas las sesiones se consideran completadas
                'attendance_percentage' => $summary['attendance_percentage'],
                'present_count' => $summary['present_count'],
                'late_count' => $summary['late_count'],
                'absent_count' => $summary['absent_count'],
                'total_students' => $summary['total_students'],
            ];
        });

        // Estadísticas generales del historial
        $allSessions = AttendanceSession::with(['attendances'])
            ->where('estado', 'ACTIVO')
            ->get();

        $totalSessions = $allSessions->count();
        $averageAttendance = $allSessions->isNotEmpty() 
            ? round($allSessions->avg(function($session) {
                $summary = $session->getSessionSummary();
                return $summary['attendance_percentage'];
            })) 
            : 0;

        $bestSession = $allSessions->sortByDesc(function($session) {
            return $session->getSessionSummary()['attendance_percentage'];
        })->first();

        $lowestSession = $allSessions->sortBy(function($session) {
            return $session->getSessionSummary()['attendance_percentage'];
        })->first();

        $historyStats = (object)[
            'total_sessions' => $totalSessions,
            'average_attendance' => $averageAttendance,
            'best_session' => $bestSession ? $bestSession->title . " ({$bestSession->getSessionSummary()['attendance_percentage']}%)" : 'N/A',
            'lowest_session' => $lowestSession ? $lowestSession->title . " ({$lowestSession->getSessionSummary()['attendance_percentage']}%)" : 'N/A',
        ];

        // Obtener grupos para el filtro
        $groups = Group::where('estado', 'ACTIVO')->orderBy('name')->get();

        return view('attendances.history', compact('sessions', 'historyStats', 'groups', 'groupId', 'startDate', 'endDate'));
    }
}