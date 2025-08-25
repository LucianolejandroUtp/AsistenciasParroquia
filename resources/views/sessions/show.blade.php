@extends('layouts.app')

@section('title', 'Detalles de Sesión')

@section('breadcrumbs')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h2 class="mb-0">{{ $session->display_title }}</h2>
                </div>
                <ul class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('sessions.index') }}">Sesiones</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detalles</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Información de la Sesión -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fe fe-info me-2"></i>{{ $session->display_title }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-5 fw-semibold">Fecha:</div>
                    <div class="col-7">
                        <span class="fw-semibold">{{ $session->date->locale('es')->isoFormat('DD MMM YYYY') }}</span>
                        @if($session->isClosed())
                            <span class="badge bg-danger ms-1">CERRADA</span>
                        @elseif($session->isToday())
                            <span class="badge bg-warning ms-1">HOY</span>
                        @elseif($session->isFuture())
                            <span class="badge bg-info ms-1">PROGRAMADA</span>
                        @else
                            <span class="badge bg-success ms-1">COMPLETADA</span>
                        @endif
                    </div>
                </div>

                @if($session->time)
                <div class="row mb-3">
                    <div class="col-5 fw-semibold">Hora:</div>
                    <div class="col-7">
                        {{ $session->time->format('g:i A') }}
                    </div>
                </div>
                @endif

                <div class="row mb-3">
                    <div class="col-5 fw-semibold">Creado por:</div>
                    <div class="col-7">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-2">
                                <span class="avatar-title bg-primary rounded-circle">
                                    {{ substr($session->creator->names, 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <div>{{ $session->creator->names }}</div>
                                <small class="text-muted">{{ $session->creator->userType->name }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-5 fw-semibold">Estado:</div>
                    <div class="col-7">
                        @if($session->estado === 'ACTIVO')
                            <span class="badge bg-success">Activa</span>
                        @elseif($session->estado === 'CERRADO')
                            <span class="badge bg-danger">Cerrada</span>
                        @elseif($session->estado === 'INACTIVO')
                            <span class="badge bg-warning">Inactiva</span>
                        @elseif($session->estado === 'ELIMINADO')
                            <span class="badge bg-secondary">Eliminada</span>
                        @else
                            <span class="badge bg-light text-dark">{{ ucfirst(strtolower($session->estado ?? 'Sin estado')) }}</span>
                        @endif
                    </div>
                </div>

                @if($session->notes)
                <div class="mb-3">
                    <div class="fw-semibold mb-2">Observaciones:</div>
                    <div class="text-muted">{{ $session->notes }}</div>
                </div>
                @endif
            </div>

            <!-- Acciones -->
            <div class="card-footer">
                <div class="btn-group w-100">
                    @can('update', $session)
                        @if(!$session->isPast() || $session->isToday())
                        <a href="{{ route('sessions.edit', $session) }}" class="btn btn-outline-warning" data-bs-toggle="tooltip" title="Editar">
                            <i class="fe fe-edit"></i>
                        </a>
                        @endif
                        <a href="{{ route('sessions.duplicate', $session) }}" class="btn btn-outline-info" data-bs-toggle="tooltip" title="Duplicar">
                            <i class="fe fe-copy"></i>
                        </a>
                        @if($session->canBeClosed())
                        <button type="button" class="btn btn-outline-secondary" 
                                onclick="confirmClose()" data-bs-toggle="tooltip" title="Cerrar sesión">
                            <i class="fe fe-lock"></i>
                        </button>
                        @elseif($session->canBeReopened())
                        <button type="button" class="btn btn-outline-success" 
                                onclick="confirmReopen()" data-bs-toggle="tooltip" title="Reabrir sesión">
                            <i class="fe fe-unlock"></i>
                        </button>
                        @endif
                    @endcan
                    @can('delete', $session)
                        @if($session->canBeDeleted())
                        <button type="button" class="btn btn-outline-danger" 
                                onclick="confirmDelete()" data-bs-toggle="tooltip" title="Eliminar sesión">
                            <i class="fe fe-trash-2"></i>
                        </button>
                        @endif
                    @endcan
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fe fe-bar-chart-2 me-2"></i>Estadísticas de Asistencia
                </h5>
            </div>
            <div class="card-body">
                @if($stats['total'] > 0)
                    <div class="text-center mb-3">
                        <div class="h2 mb-0 text-primary">{{ $stats['attendance_rate'] }}%</div>
                        <small class="text-muted">Tasa de Asistencia</small>
                    </div>

                    @php
                        $presentPercent = ($stats['present'] / $stats['total']) * 100;
                        $latePercent = ($stats['late'] / $stats['total']) * 100;
                        $justifiedPercent = ($stats['justified'] / $stats['total']) * 100;
                        $absentPercent = ($stats['absent'] / $stats['total']) * 100;
                    @endphp

                    <div class="progress mb-3" style="height: 12px;">
                        <div class="progress-bar bg-success" 
                             style="width: {{ round($presentPercent, 2) }}%"
                             data-bs-toggle="tooltip" title="Presentes: {{ $stats['present'] }}">
                        </div>
                        <div class="progress-bar bg-warning" 
                             style="width: {{ round($latePercent, 2) }}%"
                             data-bs-toggle="tooltip" title="Tardanzas: {{ $stats['late'] }}">
                        </div>
                        <div class="progress-bar bg-info" 
                             style="width: {{ round($justifiedPercent, 2) }}%"
                             data-bs-toggle="tooltip" title="Justificados: {{ $stats['justified'] }}">
                        </div>
                        <div class="progress-bar bg-danger" 
                             style="width: {{ round($absentPercent, 2) }}%"
                             data-bs-toggle="tooltip" title="Ausentes: {{ $stats['absent'] }}">
                        </div>
                    </div>

                    <div class="row text-center">
                        <div class="col-3">
                            <div class="h4 mb-0 text-success">{{ $stats['present'] }}</div>
                            <small class="text-muted">Presentes</small>
                        </div>
                        <div class="col-3">
                            <div class="h4 mb-0 text-warning">{{ $stats['late'] }}</div>
                            <small class="text-muted">Tardanzas</small>
                        </div>
                        <div class="col-3">
                            <div class="h4 mb-0 text-info">{{ $stats['justified'] }}</div>
                            <small class="text-muted">Justificados</small>
                        </div>
                        <div class="col-3">
                            <div class="h4 mb-0 text-danger">{{ $stats['absent'] }}</div>
                            <small class="text-muted">Ausentes</small>
                        </div>
                    </div>
                @else
                    <div class="text-center text-muted">
                        <i class="fe fe-users" style="font-size: 2rem;"></i>
                        <p class="mt-2 mb-0">Aún no hay asistencias registradas</p>
                        @if($session->canTakeAttendance())
                        <small>Puedes comenzar a registrar asistencias cuando esté lista la sesión.</small>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Lista de Asistencias -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fe fe-users me-2"></i>Asistencias
                </h5>
                @if($session->canTakeAttendance())
                <div class="btn-group">
                    <a href="{{ route('attendances.register', ['session_id' => $session->id]) }}" class="btn btn-outline-primary">
                        <i class="fe fe-edit me-1"></i>Manual
                    </a>
                    <a href="{{ route('attendances.qr-scanner', ['session_id' => $session->id]) }}" class="btn btn-primary">
                        <i class="fe fe-camera me-1"></i>QR
                    </a>
                </div>
                @endif
            </div>

            <div class="card-body">
                @if($session->attendances->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Estudiante</th>
                                    <th>Grupo</th>
                                    <th>Estado</th>
                                    <th>Observaciones</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($session->attendances->sortBy('student.order_number') as $attendance)
                                <tr>
                                    <td>{{ $attendance->student->order_number }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $attendance->student->names }}</div>
                                        <small class="text-muted">{{ $attendance->student->surnames }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            Grupo {{ $attendance->student->group->code }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $attendance->status_css_class }}">
                                            <i class="{{ $attendance->status_icon }} me-1"></i>
                                            {{ $attendance->status_display }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($attendance->notes)
                                            <small class="text-muted">{{ Str::limit($attendance->notes, 50) }}</small>
                                        @else
                                            <span class="text-muted">--</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @can('update', $session)
                                            @if($session->canTakeAttendance())
                                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                                    onclick="confirmDeleteAttendance({{ $attendance->id }}, '{{ $attendance->student->full_name }}')"
                                                    data-bs-toggle="tooltip" title="Eliminar registro">
                                                <i class="fe fe-trash-2"></i>
                                            </button>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fe fe-clipboard text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-muted">No hay asistencias registradas</h5>
                        <p class="text-muted">
                            @if($session->canTakeAttendance())
                                Comienza registrando las asistencias de los estudiantes para esta sesión.
                            @else
                                Las asistencias se podrán registrar cuando llegue la fecha de la sesión.
                            @endif
                        </p>
                        @if($session->canTakeAttendance())
                        <div class="btn-group">
                            <a href="{{ route('attendances.register', ['session_id' => $session->id]) }}" class="btn btn-outline-primary">
                                <i class="fe fe-edit me-1"></i>Registro Manual
                            </a>
                            <a href="{{ route('attendances.qr-scanner', ['session_id' => $session->id]) }}" class="btn btn-primary">
                                <i class="fe fe-camera me-1"></i>Registro QR
                            </a>
                        </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar esta sesión?</p>
                <div class="alert alert-warning">
                    <i class="fe fe-alert-triangle me-2"></i>
                    Esta acción no se puede deshacer. Se eliminará toda la información de la sesión.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ route('sessions.destroy', $session) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar Sesión</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación para Cerrar Sesión -->
<div class="modal fade" id="closeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Cierre de Sesión</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas cerrar esta sesión?</p>
                <div class="alert alert-warning">
                    <i class="fe fe-alert-triangle me-2"></i>
                    Una vez cerrada, no se podrán registrar más asistencias para esta sesión.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ route('sessions.close', $session) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-warning">Cerrar Sesión</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación para Reabrir Sesión -->
<div class="modal fade" id="reopenModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Reapertura de Sesión</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas reabrir esta sesión?</p>
                <div class="alert alert-info">
                    <i class="fe fe-info me-2"></i>
                    La sesión se reactivará y se podrán registrar asistencias nuevamente.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ route('sessions.reopen', $session) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success">Reabrir Sesión</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación para Eliminar Asistencia -->
<div class="modal fade" id="deleteAttendanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación de Asistencia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar el registro de asistencia de <strong id="studentNameToDelete"></strong>?</p>
                <div class="alert alert-warning">
                    <i class="fe fe-alert-triangle me-2"></i>
                    Esta acción no se puede deshacer. El estudiante no aparecerá como registrado en esta sesión.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteAttendanceForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar Registro</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

function confirmDelete() {
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function confirmClose() {
    new bootstrap.Modal(document.getElementById('closeModal')).show();
}

function confirmReopen() {
    new bootstrap.Modal(document.getElementById('reopenModal')).show();
}

function confirmDeleteAttendance(attendanceId, studentName) {
    document.getElementById('studentNameToDelete').textContent = studentName;
    document.getElementById('deleteAttendanceForm').action = `{{ url('/') }}/attendances/${attendanceId}`;
    new bootstrap.Modal(document.getElementById('deleteAttendanceModal')).show();
}
</script>
@endpush