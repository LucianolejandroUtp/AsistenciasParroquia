@extends('layouts.app')

@section('title', 'Sesiones de Catequesis')

@section('breadcrumbs')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h2 class="mb-0">Sesiones de Catequesis</h2>
                </div>
                <ul class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Sesiones</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fe fe-calendar me-2"></i>Gestión de Sesiones
                </h5>
                @can('create', App\Models\AttendanceSession::class)
                <div class="btn-group">
                    <a href="{{ route('sessions.create') }}" class="btn btn-primary">
                        <i class="fe fe-plus me-1"></i>Nueva Sesión
                    </a>
                    <button type="button" class="btn btn-outline-primary" id="toggleCalendar">
                        <i class="fe fe-calendar me-1"></i>Vista Calendario
                    </button>
                </div>
                @endcan
            </div>

            <!-- Filtros de Búsqueda -->
            <div class="card-body border-bottom">
                <form method="GET" action="{{ route('sessions.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Fecha Desde</label>
                        <input type="date" name="fecha_desde" class="form-control" 
                               value="{{ request('fecha_desde') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fecha Hasta</label>
                        <input type="date" name="fecha_hasta" class="form-control" 
                               value="{{ request('fecha_hasta') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Buscar</label>
                        <input type="text" name="buscar" class="form-control" 
                               placeholder="Título o notas..." value="{{ request('buscar') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fe fe-search"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Lista de Sesiones -->
            <div class="card-body" id="sessionsTable">
                @if($sessions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Título</th>
                                    <th>Creado por</th>
                                    <th>Asistencias</th>
                                    <th>Estado</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sessions as $session)
                                <tr>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ $session->date->format('d/m/Y') }}
                                        </span>
                                        @if($session->isToday())
                                            <span class="badge bg-warning ms-1">HOY</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($session->time)
                                            <i class="fe fe-clock me-1"></i>
                                            {{ $session->time->format('H:i') }}
                                        @else
                                            <span class="text-muted">--:--</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('sessions.show', $session) }}" 
                                           class="text-decoration-none">
                                            <strong>{{ $session->display_title }}</strong>
                                        </a>
                                        @if($session->notes)
                                            <br><small class="text-muted">{{ Str::limit($session->notes, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-title bg-primary rounded-circle">
                                                    {{ substr($session->creator->names, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $session->creator->names }}</div>
                                                <small class="text-muted">{{ $session->creator->userType->name }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php $stats = $session->attendance_stats @endphp
                                        @if($stats['total'] > 0)
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-success" 
                                                     style="width: {{ $stats['attendance_rate'] }}%"
                                                     data-bs-toggle="tooltip"
                                                     title="{{ $stats['present'] + $stats['late'] }}/{{ $stats['total'] }} presentes">
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ $stats['attendance_rate'] }}% asistencia</small>
                                        @else
                                            <span class="badge bg-light text-muted">Sin registros</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($session->isFuture())
                                            <span class="badge bg-info">Programada</span>
                                        @elseif($session->isToday())
                                            <span class="badge bg-warning">En curso</span>
                                        @else
                                            <span class="badge bg-success">Completada</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('sessions.show', $session) }}" 
                                               class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Ver detalles">
                                                <i class="fe fe-eye"></i>
                                            </a>
                                            @can('update', $session)
                                                @if(!$session->isPast() || $session->isToday())
                                                <a href="{{ route('sessions.edit', $session) }}" 
                                                   class="btn btn-outline-warning" data-bs-toggle="tooltip" title="Editar">
                                                    <i class="fe fe-edit"></i>
                                                </a>
                                                @endif
                                                <a href="{{ route('sessions.duplicate', $session) }}" 
                                                   class="btn btn-outline-info" data-bs-toggle="tooltip" title="Duplicar">
                                                    <i class="fe fe-copy"></i>
                                                </a>
                                            @endcan
                                            @can('delete', $session)
                                                @if($session->canBeDeleted())
                                                <button type="button" class="btn btn-outline-danger" 
                                                        data-bs-toggle="tooltip" title="Eliminar"
                                                        data-session-id="{{ $session->id }}" 
                                                        data-session-title="{{ $session->display_title }}"
                                                        onclick="confirmDelete(this.dataset.sessionId, this.dataset.sessionTitle)">
                                                    <i class="fe fe-trash-2"></i>
                                                </button>
                                                @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Mostrando {{ $sessions->firstItem() }} a {{ $sessions->lastItem() }} 
                            de {{ $sessions->total() }} sesiones
                        </div>
                        {{ $sessions->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fe fe-calendar text-muted" style="font-size: 3rem;"></i>
                        <h4 class="mt-3 text-muted">No hay sesiones registradas</h4>
                        <p class="text-muted">Comienza creando una nueva sesión de catequesis.</p>
                        @can('create', App\Models\AttendanceSession::class)
                        <a href="{{ route('sessions.create') }}" class="btn btn-primary">
                            <i class="fe fe-plus me-1"></i>Crear Primera Sesión
                        </a>
                        @endcan
                    </div>
                @endif
            </div>

            <!-- Vista Calendario (oculta por defecto) -->
            <div class="card-body d-none" id="calendarView">
                <div id="sessionCalendar"></div>
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
                <p>¿Estás seguro de que deseas eliminar la sesión "<span id="sessionTitle"></span>"?</p>
                <div class="alert alert-warning">
                    <i class="fe fe-alert-triangle me-2"></i>
                    Esta acción no se puede deshacer.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar Sesión</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Toggle Vista Calendario
    const toggleBtn = document.getElementById('toggleCalendar');
    const tableView = document.getElementById('sessionsTable');
    const calendarView = document.getElementById('calendarView');
    let calendar;

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            if (tableView.classList.contains('d-none')) {
                // Mostrar tabla
                tableView.classList.remove('d-none');
                calendarView.classList.add('d-none');
                toggleBtn.innerHTML = '<i class="fe fe-calendar me-1"></i>Vista Calendario';
            } else {
                // Mostrar calendario
                tableView.classList.add('d-none');
                calendarView.classList.remove('d-none');
                toggleBtn.innerHTML = '<i class="fe fe-list me-1"></i>Vista Lista';
                
                if (!calendar) {
                    initCalendar();
                }
            }
        });
    }

    function initCalendar() {
        const calendarEl = document.getElementById('sessionCalendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,listWeek'
            },
            events: '{{ route("sessions.calendar") }}',
            eventClick: function(info) {
                window.location.href = info.event.url;
            },
            height: 'auto'
        });
        calendar.render();
    }
});

function confirmDelete(sessionId, sessionTitle) {
    document.getElementById('sessionTitle').textContent = sessionTitle;
    document.getElementById('deleteForm').action = `/sessions/${sessionId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush