<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSession;
use App\Http\Requests\AttendanceSessionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceSessionController extends Controller
{

    /**
     * Display a listing of attendance sessions.
     */
    public function index(Request $request)
    {
        $query = AttendanceSession::query()
            ->with(['creator'])
            ->activeOrClosed(); // Mostrar sesiones activas y cerradas, no eliminadas

        // Filtros opcionales
        if ($request->filled('fecha_desde')) {
            $query->where('date', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('date', '<=', $request->fecha_hasta);
        }

        if ($request->filled('buscar')) {
            $search = $request->buscar;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Ordenar por fecha más reciente
        $sessions = $query->latest()->paginate(15);

        return view('sessions.index', compact('sessions'));
    }

    /**
     * Show the form for creating a new attendance session.
     */
    public function create()
    {
        return view('sessions.create');
    }

    /**
     * Store a newly created attendance session.
     */
    public function store(AttendanceSessionRequest $request)
    {
        try {
            $session = AttendanceSession::create([
                'created_by' => Auth::id(),
                'date' => $request->date,
                'time' => $request->time,
                'title' => $request->title ?: 'Catequesis del ' . date('d/m/Y', strtotime($request->date)),
                'notes' => $request->notes,
            ]);

            return redirect()
                ->route('sessions.show', $session)
                ->with('success', 'Sesión de catequesis creada exitosamente.');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al crear la sesión: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified attendance session.
     */
    public function show(AttendanceSession $session)
    {
        $session->load(['creator', 'attendances.student.group']);

        // Estadísticas de asistencia
        $stats = $session->attendance_stats;

        return view('sessions.show', compact('session', 'stats'));
    }

    /**
     * Show the form for editing the specified attendance session.
     */
    public function edit(AttendanceSession $session)
    {
        // No permitir editar sesiones cerradas
        if ($session->isClosed()) {
            return redirect()
                ->route('sessions.show', $session)
                ->with('warning', 'No se pueden editar sesiones cerradas. Debe reabrir la sesión primero.');
        }

        // Solo permitir editar sesiones futuras o del día actual
        if ($session->isPast() && !$session->isToday()) {
            return redirect()
                ->route('sessions.show', $session)
                ->with('warning', 'No se pueden editar sesiones pasadas.');
        }

        return view('sessions.edit', compact('session'));
    }

    /**
     * Update the specified attendance session.
     */
    public function update(AttendanceSessionRequest $request, AttendanceSession $session)
    {
        try {
            // Validar que no sea una sesión muy antigua
            if ($session->isPast() && !$session->isToday()) {
                return redirect()
                    ->route('sessions.show', $session)
                    ->with('error', 'No se pueden modificar sesiones pasadas.');
            }

            $session->update([
                'date' => $request->date,
                'time' => $request->time,
                'title' => $request->title,
                'notes' => $request->notes,
            ]);

            return redirect()
                ->route('sessions.show', $session)
                ->with('success', 'Sesión actualizada exitosamente.');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar la sesión: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified attendance session.
     */
    public function destroy(AttendanceSession $session)
    {
        try {
            // Verificar que no tenga asistencias registradas
            if (!$session->canBeDeleted()) {
                return redirect()
                    ->route('sessions.index')
                    ->with('error', 'No se puede eliminar una sesión que ya tiene asistencias registradas.');
            }

            $session->update(['estado' => 'ELIMINADO']);

            return redirect()
                ->route('sessions.index')
                ->with('success', 'Sesión eliminada exitosamente.');
                
        } catch (\Exception $e) {
            return redirect()
                ->route('sessions.index')
                ->with('error', 'Error al eliminar la sesión: ' . $e->getMessage());
        }
    }

    /**
     * API endpoint to get sessions for calendar view.
     */
    public function calendar(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        $sessions = AttendanceSession::active()
            ->betweenDates($start, $end)
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->id,
                    'title' => $session->display_title,
                    'start' => $session->date->format('Y-m-d'),
                    'time' => $session->time ? $session->time->format('H:i') : null,
                    'url' => route('sessions.show', $session),
                    'className' => $session->isToday() ? 'event-today' : ($session->isFuture() ? 'event-future' : 'event-past'),
                ];
            });

        return response()->json($sessions);
    }

    /**
     * Duplicate a session for creating recurring sessions.
     */
    public function duplicate(AttendanceSession $session)
    {
        $newSession = $session->replicate();
        $newSession->created_by = Auth::id();
        $newSession->date = now()->addWeek()->toDateString(); // Próxima semana por defecto
        $newSession->title = $session->title . ' (Copia)';
        $newSession->created_at = now();
        $newSession->updated_at = now();

        return view('sessions.create', ['session' => $newSession]);
    }

    /**
     * Close a session to prevent further attendance registrations.
     */
    public function close(AttendanceSession $session)
    {
        try {
            if (!$session->canBeClosed()) {
                return redirect()
                    ->route('sessions.show', $session)
                    ->with('error', 'Esta sesión no puede ser cerrada en su estado actual.');
            }

            $session->update(['estado' => 'CERRADO']);

            return redirect()
                ->route('sessions.show', $session)
                ->with('success', 'Sesión cerrada exitosamente. No se podrán registrar más asistencias.');
                
        } catch (\Exception $e) {
            return redirect()
                ->route('sessions.show', $session)
                ->with('error', 'Error al cerrar la sesión: ' . $e->getMessage());
        }
    }

    /**
     * Reopen a closed session to allow attendance registrations again.
     */
    public function reopen(AttendanceSession $session)
    {
        try {
            if (!$session->canBeReopened()) {
                return redirect()
                    ->route('sessions.show', $session)
                    ->with('error', 'Esta sesión no puede ser reabierta en su estado actual.');
            }

            $session->update(['estado' => 'ACTIVO']);

            return redirect()
                ->route('sessions.show', $session)
                ->with('success', 'Sesión reabierta exitosamente. Se pueden registrar asistencias nuevamente.');
                
        } catch (\Exception $e) {
            return redirect()
                ->route('sessions.show', $session)
                ->with('error', 'Error al reabrir la sesión: ' . $e->getMessage());
        }
    }
}