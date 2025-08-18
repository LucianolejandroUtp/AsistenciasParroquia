@extends('layouts.app')

@section('title', 'Registrar Asistencias - Sistema de Asistencias')

@section('page-title', 'Registro de Asistencias')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Asistencias</li>
    <li class="breadcrumb-item active">Registrar</li>
@endsection

@section('additional-css')
<style>
.student-card {
    transition: all 0.2s     // Marcar asistencia con botones directos
    document.addEventListener('click', function(e) {
        if (e.target.closest('.attendance-btn')) {
            e.preventDefault();
            const button = e.target.closest('.attendance-btn');
            const status = button.dataset.status;
            const studentCard = button.closest('.student-card');
            const studentItem = button.closest('.student-item');
            const studentId = studentCard.dataset.studentId;
            const sessionId = {{ $selectedSession ? $selectedSession->id : 'null' }};
            
            if (!sessionId) {
                showToast('Error: No hay sesión seleccionada', 'error');
                return;
            }
            
            // Guardar asistencia en el servidor
            saveAttendance(studentId, sessionId, status, studentCard, studentItem);
        }
    });rsor: pointer;
}

.student-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.student-card.present {
    border-left: 4px solid #28a745;
    background-color: #f8fff9;
}

.student-card.late {
    border-left: 4px solid #ffc107;
    background-color: #fffdf8;
}

.student-card.absent {
    border-left: 4px solid #dc3545;
    background-color: #fef8f8;
}

.attendance-toggle {
    min-width: 120px;
}

.attendance-btn {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    min-width: 70px;
    margin-bottom: 2px;
}

.attendance-btn:last-child {
    margin-bottom: 0;
}

.attendance-btn.btn-success,
.attendance-btn.btn-warning,
.attendance-btn.btn-danger,
.attendance-btn.btn-info {
    font-weight: 600;
}

.btn-group-vertical {
    min-width: 80px;
}

@media (max-width: 768px) {
    .student-card {
        margin-bottom: 0.5rem;
    }
    
    .btn-group-vertical {
        min-width: 70px;
    }
    
    .attendance-btn {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
        min-width: 60px;
    }
}
</style>
@endsection

@section('content')
@if($selectedSession)
<!-- Selector de Sesión y Información de Sesión en línea -->
<div class="row mb-4">
    @if($activeSessions->count() > 1)
    <!-- Selector de Sesión (mitad izquierda) -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="mb-0">Seleccionar Sesión</h6>
                    </div>
                    <div class="col-auto">
                        <form method="GET" action="{{ route('attendances.register') }}">
                            <select name="session_id" class="form-control" onchange="this.form.submit()">
                                @foreach($activeSessions as $session)
                                    <option value="{{ $session->id }}" {{ $selectedSession && $selectedSession->id == $session->id ? 'selected' : '' }}>
                                        {{ $session->title }} - {{ $session->date->format('d/m/Y') }} @if($session->time){{ $session->time->format('H:i') }}@endif
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Información de la Sesión Activa (mitad derecha o ancho completo si solo hay una sesión) -->
    <div class="col-md-{{ $activeSessions->count() > 1 ? '6' : '12' }}">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="text-white mb-1">{{ $selectedSession->title }}</h4>
                        <p class="text-white-50 mb-0">
                            <i class="fe fe-calendar mr-1"></i>
                            {{ $selectedSession->date->format('l, d \d\e F Y') }}
                            @if($selectedSession->time)
                            <i class="fe fe-clock ml-3 mr-1"></i>
                            {{ $selectedSession->time->format('H:i') }}
                            @endif
                        </p>
                        @if($selectedSession->description)
                        <small class="text-white-75">{{ $selectedSession->description }}</small>
                        @endif
                    </div>
                    <div class="col-auto">
                        <span class="badge badge-success badge-lg">Sesión Activa</span>
                        <div class="text-white-50 small mt-1">
                            Sesión de Catequesis
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Estudiantes -->

<!-- Estadísticas de Asistencia -->
<div class="row mb-4">
    <div class="col-md-2 col-6">
        <div class="card text-center">
            <div class="card-body">
                <span class="h3 mb-0">{{ $stats->total_students }}</span>
                <p class="small text-muted mb-0">Total</p>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card text-center border-success">
            <div class="card-body">
                <span class="h3 mb-0 text-success">{{ $stats->present_count }}</span>
                <p class="small text-muted mb-0">Presentes</p>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card text-center border-warning">
            <div class="card-body">
                <span class="h3 mb-0 text-warning">{{ $stats->late_count }}</span>
                <p class="small text-muted mb-0">Tarde</p>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card text-center border-danger">
            <div class="card-body">
                <span class="h3 mb-0 text-danger">{{ $stats->absent_count }}</span>
                <p class="small text-muted mb-0">Ausentes</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        @php $attendanceRate = $stats->total_students > 0 ? round(($stats->registered_today / $stats->total_students) * 100) : 0; @endphp
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ $attendanceRate }}%"></div>
                        </div>
                        <p class="small text-muted mb-0 mt-1">
                            {{ $attendanceRate }}% de asistencia
                        </p>
                    </div>
                    <div class="col-auto">
                        <span class="fe fe-trending-up fe-24 text-success"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros y Acciones Rápidas -->
<div class="row mb-3">
    <div class="col-md-4">
        <div class="form-group">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Buscar estudiante..." id="search-student">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button">
                        <span class="fe fe-search"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <select class="form-control" id="filter-group">
            <option value="">Todos los grupos</option>
            <option value="Grupo A">Grupo A</option>
            <option value="Grupo B">Grupo B</option>
        </select>
    </div>
    <div class="col-md-2">
        <select class="form-control" id="filter-status">
            <option value="">Estado</option>
            <option value="present">Presentes</option>
            <option value="late">Tarde</option>
            <option value="absent">Ausentes</option>
            <option value="pending">Sin marcar</option>
        </select>
    </div>
    <div class="col-md-4 text-right">
        <button class="btn btn-success" id="mark-all-present">
            <span class="fe fe-check-circle mr-1"></span>Marcar Todos Presentes
        </button>
        <button class="btn btn-outline-primary" onclick="window.location.href='{{ route('attendances.qr-scanner', ['session_id' => $selectedSession->id]) }}'">
            <span class="fe fe-qr-code mr-1"></span>Escanear QR
        </button>
    </div>
</div>

<!-- Lista de Estudiantes para Registro -->
<div class="row" id="students-container">
    @foreach($students as $student)
    <div class="col-lg-6 col-xl-4 mb-3 student-item" 
         data-group="{{ $student->group_name }}" 
         data-status="{{ $student->attendance_status ?? 'pending' }}"
         data-name="{{ strtolower($student->full_name) }}">
        <div class="card student-card {{ $student->attendance_status ?? '' }}" data-student-id="{{ $student->id }}">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="media align-items-center">
                            <div class="avatar avatar-sm mr-3">
                                <span class="avatar-title rounded-circle bg-soft-{{ str_contains($student->group_name, 'A') ? 'primary' : 'info' }} text-{{ str_contains($student->group_name, 'A') ? 'primary' : 'info' }}">
                                    {{ substr($student->full_name, 0, 1) }}
                                </span>
                            </div>
                            <div class="media-body">
                                <h6 class="mb-0">{{ $student->full_name }}</h6>
                                <small class="text-muted">
                                    <span class="badge badge-{{ str_contains($student->group_name, 'A') ? 'primary' : 'info' }} badge-sm mr-1">
                                        {{ $student->group_name }} - #{{ $student->order_number }}
                                    </span>
                                    <code class="small">{{ $student->qr_code }}</code>
                                </small>
                                @if($student->last_attendance)
                                <div class="small text-muted mt-1">
                                    Última asistencia: {{ \Carbon\Carbon::parse($student->last_attendance)->format('d/m/Y') }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group-vertical btn-group-sm" role="group">
                            <button class="btn attendance-btn {{ $student->attendance_status == 'present' ? 'btn-success' : 'btn-outline-success' }}" 
                                    data-status="present" title="Marcar como Presente">
                                <span class="fe fe-check mr-1"></span>
                                @if($student->attendance_status == 'present')
                                    Presente
                                    @if($student->attendance_time)
                                    <small class="d-block">{{ $student->attendance_time }}</small>
                                    @endif
                                @else
                                    Presente
                                @endif
                            </button>
                            <button class="btn attendance-btn {{ $student->attendance_status == 'late' ? 'btn-warning' : 'btn-outline-warning' }}" 
                                    data-status="late" title="Marcar Tardanza">
                                <span class="fe fe-clock mr-1"></span>Tarde
                            </button>
                            <button class="btn attendance-btn {{ $student->attendance_status == 'absent' ? 'btn-danger' : 'btn-outline-danger' }}" 
                                    data-status="absent" title="Marcar como Ausente">
                                <span class="fe fe-x mr-1"></span>Ausente
                            </button>
                            <button class="btn attendance-btn {{ $student->attendance_status == 'justified' ? 'btn-info' : 'btn-outline-info' }}" 
                                    data-status="justified" title="Marcar como Justificado">
                                <span class="fe fe-info mr-1"></span>Just.
                            </button>
                        </div>
                    </div>
                </div>
                
                @if($student->attendance_time)
                <div class="row mt-2">
                    <div class="col">
                        <small class="text-muted">
                            <i class="fe fe-clock mr-1"></i>
                            Registrado a las {{ $student->attendance_time }}
                        </small>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endforeach
    
    @if($students->isEmpty())
    <div class="col-12">
        <div class="card border-dashed text-center">
            <div class="card-body py-5">
                <span class="fe fe-users fe-48 text-muted mb-3 d-block"></span>
                <h5 class="text-muted">No hay estudiantes disponibles</h5>
                <p class="text-muted">No se encontraron estudiantes para los grupos de esta sesión.</p>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Acciones de Guardado -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="mb-0">Acciones de Sesión</h6>
                        <small class="text-muted">Los cambios se guardan automáticamente al marcar asistencia</small>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-outline-secondary mr-2" onclick="window.print()">
                            <span class="fe fe-printer mr-1"></span>Imprimir Lista
                        </button>
                        <button class="btn btn-info" onclick="exportAttendance()">
                            <span class="fe fe-download mr-1"></span>Exportar
                        </button>
                    </div>
                </div>
            </div>
        </div>
@else
<!-- Sin sesiones activas -->
<div class="row">
    <div class="col-12">
        <div class="card text-center">
            <div class="card-body py-5">
                <span class="fe fe-calendar-x fe-48 text-muted mb-3 d-block"></span>
                <h4 class="text-muted">No hay sesiones activas</h4>
                <p class="text-muted mb-4">
                    No se encontraron sesiones activas para registrar asistencias.<br>
                    Cree una nueva sesión o active una existente para continuar.
                </p>
                <a href="{{ route('sessions.create') }}" class="btn btn-primary">
                    <span class="fe fe-plus mr-1"></span>Crear Nueva Sesión
                </a>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('additional-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const studentsContainer = document.getElementById('students-container');
    const searchInput = document.getElementById('search-student');
    const filterGroup = document.getElementById('filter-group');
    const filterStatus = document.getElementById('filter-status');
    
    // Token CSRF para las peticiones AJAX
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Funcionalidad de búsqueda
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            filterStudents();
        });
    }
    
    // Filtros
    if (filterGroup) filterGroup.addEventListener('change', filterStudents);
    if (filterStatus) filterStatus.addEventListener('change', filterStudents);
    
    function filterStudents() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const groupFilter = filterGroup ? filterGroup.value : '';
        const statusFilter = filterStatus ? filterStatus.value : '';
        
        document.querySelectorAll('.student-item').forEach(item => {
            const name = item.dataset.name;
            const group = item.dataset.group;
            const status = item.dataset.status;
            
            let show = true;
            
            if (searchTerm && !name.includes(searchTerm)) show = false;
            if (groupFilter && group !== groupFilter) show = false;
            if (statusFilter && status !== statusFilter) show = false;
            
            item.style.display = show ? 'block' : 'none';
        });
    }
    
    // Marcar asistencia con botones directos
    document.addEventListener('click', function(e) {
        if (e.target.closest('.attendance-btn')) {
            e.preventDefault();
            const button = e.target.closest('.attendance-btn');
            const status = button.dataset.status;
            const studentCard = button.closest('.student-card');
            const studentItem = button.closest('.student-item');
            const studentId = studentCard.dataset.studentId;
            const sessionId = {{ $selectedSession ? $selectedSession->id : 'null' }};
            
            if (!sessionId) {
                showToast('Error: No hay sesión seleccionada', 'error');
                return;
            }
            
            // Guardar asistencia en el servidor
            saveAttendance(studentId, sessionId, status, studentCard, studentItem);
        }
    });
    
    function saveAttendance(studentId, sessionId, status, card, item) {
        const buttonGroup = card.querySelector('.btn-group-vertical');
        const clickedButton = buttonGroup.querySelector(`[data-status="${status}"]`);
        const originalContent = clickedButton.innerHTML;
        
        // Mostrar estado de carga en el botón específico
        clickedButton.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        clickedButton.disabled = true;
        
        fetch('{{ route("attendances.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                student_id: studentId,
                attendance_session_id: sessionId,
                status: status
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar estado visual
                updateAttendanceStatus(card, item, clickedButton, status, data.data.marked_at);
                
                // Actualizar estadísticas
                updateStats();
                
                // Mostrar mensaje de éxito
                showToast('Asistencia registrada: ' + data.data.student_name, 'success');
            } else {
                throw new Error(data.message || 'Error al guardar la asistencia');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            clickedButton.innerHTML = originalContent;
            clickedButton.disabled = false;
            showToast('Error al guardar asistencia: ' + error.message, 'error');
        });
    }
    
    function updateAttendanceStatus(card, item, clickedButton, status, markedAt) {
        // Limpiar clases previas de la tarjeta
        card.className = 'card student-card ' + status;
        item.dataset.status = status;
        
        // Actualizar todos los botones de asistencia en esta tarjeta
        const buttonGroup = card.querySelector('.btn-group-vertical');
        const buttons = buttonGroup.querySelectorAll('.attendance-btn');
        
        buttons.forEach(btn => {
            const btnStatus = btn.dataset.status;
            btn.disabled = false;
            
            // Remover todas las clases de estado previas
            btn.className = btn.className.replace(/btn-(success|warning|danger|info|outline-success|outline-warning|outline-danger|outline-info)/g, '');
            
            if (btnStatus === status) {
                // Botón activo
                switch(status) {
                    case 'present':
                        btn.classList.add('btn-success');
                        btn.innerHTML = '<span class="fe fe-check mr-1"></span>Presente' + (markedAt ? '<small class="d-block">' + markedAt + '</small>' : '');
                        break;
                    case 'late':
                        btn.classList.add('btn-warning');
                        btn.innerHTML = '<span class="fe fe-clock mr-1"></span>Tarde' + (markedAt ? '<small class="d-block">' + markedAt + '</small>' : '');
                        break;
                    case 'absent':
                        btn.classList.add('btn-danger');
                        btn.innerHTML = '<span class="fe fe-x mr-1"></span>Ausente';
                        break;
                    case 'justified':
                        btn.classList.add('btn-info');
                        btn.innerHTML = '<span class="fe fe-info mr-1"></span>Just.' + (markedAt ? '<small class="d-block">' + markedAt + '</small>' : '');
                        break;
                }
            } else {
                // Botón inactivo
                switch(btnStatus) {
                    case 'present':
                        btn.classList.add('btn-outline-success');
                        btn.innerHTML = '<span class="fe fe-check mr-1"></span>Presente';
                        break;
                    case 'late':
                        btn.classList.add('btn-outline-warning');
                        btn.innerHTML = '<span class="fe fe-clock mr-1"></span>Tarde';
                        break;
                    case 'absent':
                        btn.classList.add('btn-outline-danger');
                        btn.innerHTML = '<span class="fe fe-x mr-1"></span>Ausente';
                        break;
                    case 'justified':
                        btn.classList.add('btn-outline-info');
                        btn.innerHTML = '<span class="fe fe-info mr-1"></span>Just.';
                        break;
                }
            }
        });
    }
    
    function updateStats() {
        // Recalcular estadísticas localmente
        const items = document.querySelectorAll('.student-item[style*="block"], .student-item:not([style])');
        let present = 0, late = 0, absent = 0, total = items.length;
        
        items.forEach(item => {
            const status = item.dataset.status;
            if (status === 'present') present++;
            else if (status === 'late') late++;
            else if (status === 'absent') absent++;
        });
        
        // Actualizar los contadores
        const registered = present + late;
        const absentCount = total - registered;
        
        document.querySelector('.border-success .h3').textContent = present;
        document.querySelector('.border-warning .h3').textContent = late;
        document.querySelector('.border-danger .h3').textContent = absentCount;
        
        // Actualizar barra de progreso
        const rate = total > 0 ? Math.round((registered / total) * 100) : 0;
        document.querySelector('.progress-bar').style.width = rate + '%';
        document.querySelector('.progress').nextElementSibling.textContent = rate + '% de asistencia';
    }
    
    function showToast(message, type) {
        // Implementación simple de toast
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 5000);
    }
    
    // Marcar todos como presentes
    const markAllButton = document.getElementById('mark-all-present');
    if (markAllButton) {
        markAllButton.addEventListener('click', function() {
            const visibleStudents = document.querySelectorAll('.student-item[style*="block"], .student-item:not([style])');
            
            if (confirm(`¿Marcar ${visibleStudents.length} estudiantes como presentes?`)) {
                let processed = 0;
                const total = visibleStudents.length;
                
                visibleStudents.forEach((item, index) => {
                    const card = item.querySelector('.student-card');
                    const studentId = card.dataset.studentId;
                    const sessionId = {{ $selectedSession ? $selectedSession->id : 'null' }};
                    
                    if (!sessionId) return;
                    
                    setTimeout(() => {
                        saveAttendance(studentId, sessionId, 'present', card, item);
                        processed++;
                        
                        if (processed === total) {
                            showToast(`${total} estudiantes marcados como presentes`, 'success');
                        }
                    }, index * 100); // Espaciar las peticiones
                });
            }
        });
    }
});

function exportAttendance() {
    window.location.href = '{{ route("attendances.history") }}';
}
</script>
@endsection