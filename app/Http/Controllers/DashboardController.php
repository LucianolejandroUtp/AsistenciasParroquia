<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Group;
use App\Models\AttendanceSession;
use App\Models\Attendance;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Mostrar el dashboard principal del sistema de asistencias.
     */
    public function index()
    {
        // Obtener estadísticas básicas
        $totalStudents = Student::count();
        $totalGroups = Group::count();
        
        // Sesiones de esta semana
        $startOfWeek = Carbon::now()->startOfWeek()->toDateString();
        $endOfWeek = Carbon::now()->endOfWeek()->toDateString();
        $sessionsThisWeek = AttendanceSession::whereBetween('date', [$startOfWeek, $endOfWeek])->count();
        
        // Calcular asistencia promedio (últimas 4 semanas)
        $fourWeeksAgo = Carbon::now()->subWeeks(4)->toDateString();
        $today = Carbon::now()->toDateString();
        $recentSessions = AttendanceSession::where('date', '>=', $fourWeeksAgo)
                                         ->where('date', '<=', $today)
                                         ->get();
        
        $averageAttendance = 0;
        if ($recentSessions->count() > 0) {
            $totalAttendances = 0;
            $totalPossibleAttendances = 0;
            
            foreach ($recentSessions as $session) {
                $attendanceCount = Attendance::where('attendance_session_id', $session->id)
                                           ->where('status', 'present')
                                           ->count();
                $totalAttendances += $attendanceCount;
                
                // Calcular total posible basado en asignación de grupo
                // Nota: Por ahora asumimos ambos grupos hasta implementar group_assignment
                $possibleCount = Student::count(); // Para ambos grupos por defecto
                $totalPossibleAttendances += $possibleCount;
            }
            
            if ($totalPossibleAttendances > 0) {
                $averageAttendance = round(($totalAttendances / $totalPossibleAttendances) * 100);
            }
        }
        
        // Próximas sesiones (siguientes 2 semanas)
        $today = Carbon::now()->toDateString();
        $twoWeeksFromNow = Carbon::now()->addWeeks(2)->toDateString();
        $upcomingSessions = AttendanceSession::where('date', '>', $today)
                                           ->where('date', '<=', $twoWeeksFromNow)
                                           ->orderBy('date', 'asc')
                                           ->orderBy('time', 'asc')
                                           ->limit(5)
                                           ->get();
        
        // Datos de grupos para el gráfico
        $groupA = Student::where('group_id', 1)->count();
        $groupB = Student::where('group_id', 2)->count();
        
        return view('dashboard', compact(
            'totalStudents',
            'totalGroups',
            'sessionsThisWeek',
            'averageAttendance',
            'upcomingSessions',
            'groupA',
            'groupB'
        ));
    }
    
    /**
     * Obtener estadísticas actualizadas vía AJAX.
     */
    public function getStats()
    {
        $startOfWeek = Carbon::now()->startOfWeek()->toDateString();
        $endOfWeek = Carbon::now()->endOfWeek()->toDateString();
        
        $stats = [
            'total_students' => Student::count(),
            'total_groups' => Group::count(),
            'sessions_this_week' => AttendanceSession::whereBetween('date', [
                $startOfWeek,
                $endOfWeek
            ])->count(),
            'recent_attendances' => Attendance::where('created_at', '>=', Carbon::now()->subDays(7))->count()
        ];
        
        return response()->json($stats);
    }

    /**
     * Dashboard específico para Administradores
     */
    public function adminDashboard()
    {
        // TODO: Implementar vista específica de admin
        return view('dashboard.admin')->with([
            'title' => 'Panel de Administración',
            'message' => 'Vista de administrador - En desarrollo'
        ]);
    }

    /**
     * Dashboard específico para Catequistas/Profesores
     */
    public function profesorDashboard()
    {
        // TODO: Implementar vista específica de profesor
        return view('dashboard.profesor')->with([
            'title' => 'Panel de Catequista',
            'message' => 'Vista de catequista - En desarrollo'
        ]);
    }

    /**
     * Dashboard específico para Personal de Apoyo
     */
    public function staffDashboard()
    {
        // TODO: Implementar vista específica de staff
        return view('dashboard.staff')->with([
            'title' => 'Panel de Personal',
            'message' => 'Vista de personal de apoyo - En desarrollo'
        ]);
    }

    /**
     * Gestión de usuarios (Admin)
     */
    public function manageUsers()
    {
        return view('admin.users');
    }

    /**
     * Configuración del sistema (Admin)
     */
    public function systemSettings()
    {
        return view('admin.settings');
    }

    /**
     * Gestión de asistencias (Profesor)
     */
    public function manageAttendance()
    {
        // TODO: Implementar gestión de asistencias
        return response()->json(['message' => 'Gestión de asistencias - En desarrollo']);
    }

    /**
     * Gestión de estudiantes (Profesor)
     */
    public function manageStudents()
    {
        // TODO: Implementar gestión de estudiantes
        return response()->json(['message' => 'Gestión de estudiantes - En desarrollo']);
    }

    /**
     * Visualización de reportes (Staff)
     */
    public function viewReports()
    {
        // TODO: Implementar visualización de reportes
        return response()->json(['message' => 'Visualización de reportes - En desarrollo']);
    }

    /**
     * Obtener datos para gráficos (API)
     */
    public function getChartData()
    {
        // TODO: Implementar datos para gráficos
        return response()->json(['message' => 'Datos de gráficos - En desarrollo']);
    }

    /**
     * Búsqueda de estudiantes (API)
     */
    public function searchStudents(Request $request)
    {
        // TODO: Implementar búsqueda de estudiantes
        return response()->json(['message' => 'Búsqueda de estudiantes - En desarrollo']);
    }

    /**
     * Obtener asistencias recientes (API)
     */
    public function getRecentAttendance()
    {
        // TODO: Implementar asistencias recientes
        return response()->json(['message' => 'Asistencias recientes - En desarrollo']);
    }

    /**
     * Estadísticas de usuarios (API Admin)
     */
    public function getUserStats()
    {
        // TODO: Implementar estadísticas de usuarios
        return response()->json(['message' => 'Estadísticas de usuarios - En desarrollo']);
    }

    /**
     * Estado del sistema (API Admin)
     */
    public function getSystemStatus()
    {
        // TODO: Implementar estado del sistema
        return response()->json(['message' => 'Estado del sistema - En desarrollo']);
    }
}
