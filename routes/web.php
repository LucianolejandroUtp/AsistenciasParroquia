<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\AttendanceSessionController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AttendanceController;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistema de Asistencias Primera Comunión
|--------------------------------------------------------------------------
|
| Rutas organizadas por funcionalidad:
| - Autenticación (public)
| - Dashboard (auth requerido)
| - Perfiles por rol (auth + role requerido)
| - Perfil de usuario (auth requerido)
| - API endpoints (auth requerido)
|
*/

// =====================================================================
// RUTAS PÚBLICAS (Sin autenticación requerida)
// =====================================================================

// Ruta raíz - redirigir según estado de autenticación
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('auth.login');
})->name('home');

// =====================================================================
// RUTAS DE AUTENTICACIÓN
// =====================================================================

Route::prefix('auth')->name('auth.')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    // Register
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Check auth status (AJAX)
    Route::get('/check', [AuthController::class, 'checkAuth'])->name('check');
});

// =====================================================================
// RUTAS PROTEGIDAS (Autenticación requerida)
// =====================================================================

Route::middleware(['auth.custom'])->group(function () {
    
    // Dashboard principal (accesible para todos los usuarios autenticados)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // =====================================================================
    // DASHBOARDS ESPECÍFICOS POR ROL
    // =====================================================================
    
    // Dashboard de Administrador
    Route::middleware(['role:Admin'])->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard.admin');
        Route::get('/admin/users', [DashboardController::class, 'manageUsers'])->name('admin.users');
        Route::get('/admin/settings', [DashboardController::class, 'systemSettings'])->name('admin.settings');
    });
    
    // Dashboard de Catequista/Profesor
    Route::middleware(['role:Profesor'])->group(function () {
        Route::get('/profesor/dashboard', [DashboardController::class, 'profesorDashboard'])->name('dashboard.profesor');
        Route::get('/profesor/asistencias', [DashboardController::class, 'manageAttendance'])->name('profesor.asistencias');
        Route::get('/profesor/estudiantes', [DashboardController::class, 'manageStudents'])->name('profesor.estudiantes');
    });
    
    // Dashboard de Personal de Apoyo
    Route::middleware(['role:Staff'])->group(function () {
        Route::get('/staff/dashboard', [DashboardController::class, 'staffDashboard'])->name('dashboard.staff');
        Route::get('/staff/reportes', [DashboardController::class, 'viewReports'])->name('staff.reportes');
    });
    
    // =====================================================================
    // GESTIÓN DE SESIONES DE CATEQUESIS
    // =====================================================================
    
    Route::prefix('sessions')->name('sessions.')->group(function () {
        // Listar sesiones (todos los usuarios autenticados)
        Route::get('/', [AttendanceSessionController::class, 'index'])->name('index');
        
        // Vista de creación (temporal para testing - todos los usuarios)
        Route::get('/create', [AttendanceSessionController::class, 'create'])->name('create');
        
        // Ver sesión específica (todos los usuarios autenticados)
        Route::get('/{session}', [AttendanceSessionController::class, 'show'])->name('show');
        
        // Crear, editar y eliminar sesiones (solo Admin y Profesor)
        Route::middleware(['role:Admin,Profesor'])->group(function () {
            Route::post('/', [AttendanceSessionController::class, 'store'])->name('store');
            Route::get('/{session}/edit', [AttendanceSessionController::class, 'edit'])->name('edit');
            Route::put('/{session}', [AttendanceSessionController::class, 'update'])->name('update');
            Route::delete('/{session}', [AttendanceSessionController::class, 'destroy'])->name('destroy');
            Route::get('/{session}/duplicate', [AttendanceSessionController::class, 'duplicate'])->name('duplicate');
            Route::post('/{session}/close', [AttendanceSessionController::class, 'close'])->name('close');
            Route::post('/{session}/reopen', [AttendanceSessionController::class, 'reopen'])->name('reopen');
        });
        
        // API para calendario
        Route::get('/api/calendar', [AttendanceSessionController::class, 'calendar'])->name('calendar');
    });
    
    // =====================================================================
    // GESTIÓN DE ESTUDIANTES
    // =====================================================================
    
    Route::prefix('students')->name('students.')->group(function () {
        // Vistas principales de estudiantes (todos los usuarios autenticados)
        Route::get('/', [StudentController::class, 'index'])->name('index');
        Route::get('/qr-codes', [StudentController::class, 'qrCodes'])->name('qr-codes');
    });
    
    // =====================================================================
    // GESTIÓN DE ASISTENCIAS
    // =====================================================================
    
    Route::prefix('attendances')->name('attendances.')->group(function () {
        // Registro de asistencias (Admin y Profesor)
        Route::middleware(['role:Admin,Profesor'])->group(function () {
            Route::get('/register', [AttendanceController::class, 'register'])->name('register');
            Route::post('/store', [AttendanceController::class, 'store'])->name('store');
            Route::get('/qr-scanner', [AttendanceController::class, 'qrScanner'])->name('qr-scanner');
            Route::post('/qr-scan', [AttendanceController::class, 'processQrScan'])->name('qr-scan');
        });
        
        // Historial de asistencias (todos los usuarios autenticados)
        Route::get('/history', [AttendanceController::class, 'history'])->name('history');
    });
    
    // =====================================================================
    // REPORTES Y ESTADÍSTICAS
    // =====================================================================
    
    Route::prefix('reports')->name('reports.')->group(function () {
        // Estadísticas generales (todos los usuarios autenticados)
        Route::get('/statistics', function() {
            return view('reports.statistics');
        })->name('statistics');
        
        // Exportación de datos (Admin y Profesor)
        Route::middleware(['role:Admin,Profesor'])->group(function () {
            Route::get('/export', function() {
                return view('reports.export');
            })->name('export');
        });
    });
    
    // =====================================================================
    // ADMINISTRACIÓN
    // =====================================================================
    
    Route::prefix('admin')->name('admin.')->middleware(['role:Admin'])->group(function () {
        // Backup del sistema (solo Admin)
        Route::get('/backup', function() {
            return view('admin.backup');
        })->name('backup');
    });
    
    // =====================================================================
    // GESTIÓN DE PERFIL DE USUARIO
    // =====================================================================
    
    Route::prefix('profile')->name('profile.')->group(function () {
        // Ver perfil
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        
        // Editar perfil
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        
        // Cambiar contraseña
        Route::get('/change-password', [ProfileController::class, 'showChangePassword'])->name('change-password');
        Route::put('/change-password', [ProfileController::class, 'changePassword'])->name('update-password');
        
        // Info de usuario (AJAX)
        Route::get('/info', [ProfileController::class, 'getUserInfo'])->name('info');
    });
    
    // =====================================================================
    // API ENDPOINTS (Para AJAX y funcionalidades dinámicas)
    // =====================================================================
    
    Route::prefix('api')->name('api.')->group(function () {
        // Estadísticas del dashboard
        Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
        
        // Datos para gráficos
        Route::get('/dashboard/charts', [DashboardController::class, 'getChartData'])->name('dashboard.charts');
        
        // Búsqueda de estudiantes (para catequistas)
        Route::middleware(['role:Admin,Profesor'])->group(function () {
            Route::get('/students/search', [DashboardController::class, 'searchStudents'])->name('students.search');
            Route::get('/attendance/recent', [DashboardController::class, 'getRecentAttendance'])->name('attendance.recent');
        });
        
        // Endpoints solo para administradores
        Route::middleware(['role:Admin'])->group(function () {
            Route::get('/users/stats', [DashboardController::class, 'getUserStats'])->name('users.stats');
            Route::get('/system/status', [DashboardController::class, 'getSystemStatus'])->name('system.status');
        });
    });
});

// =====================================================================
// REDIRECCIONES Y ALIASES PARA COMPATIBILIDAD
// =====================================================================

// Redirección de login legacy
Route::get('/login', function () {
    return redirect()->route('auth.login');
});

// Redirección de logout legacy  
Route::post('/logout', function () {
    return redirect()->route('auth.logout');
});

// Fallback para rutas no encontradas
Route::fallback(function () {
    if (Auth::check()) {
        return redirect()->route('dashboard')->with('warning', 'La página solicitada no existe.');
    }
    return redirect()->route('auth.login')->with('error', 'Página no encontrada. Inicie sesión para continuar.');
});
