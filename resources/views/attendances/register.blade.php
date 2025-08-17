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
    transition: all 0.2s ease-in-out;
    cursor: pointer;
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

@media (max-width: 768px) {
    .student-card {
        margin-bottom: 0.5rem;
    }
}
</style>
@endsection

@section('content')
<!-- Información de la Sesión Activa -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="text-white mb-1">{{ $mockSession->title }}</h4>
                        <p class="text-white-50 mb-0">
                            <i class="fe fe-calendar mr-1"></i>
                            {{ \Carbon\Carbon::parse($mockSession->date)->format('l, d \d\e F Y') }}
                            <i class="fe fe-clock ml-3 mr-1"></i>
                            {{ \Carbon\Carbon::createFromFormat('H:i', $mockSession->time)->format('H:i A') }}
                        </p>
                    </div>
                    <div class="col-auto">
                        <span class="badge badge-success badge-lg">Sesión Activa</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ ($stats->present_count / $stats->total_students) * 100 }}%"></div>
                        </div>
                        <p class="small text-muted mb-0 mt-1">
                            {{ round(($stats->present_count / $stats->total_students) * 100) }}% de asistencia
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
            <option value="">Todos</option>
            <option value="A">Grupo A</option>
            <option value="B">Grupo B</option>
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
        <button class="btn btn-outline-primary" onclick="window.location.href='{{ route('attendances.qr-scanner') }}'">
            <span class="fe fe-qr-code mr-1"></span>Escanear QR
        </button>
    </div>
</div>

<!-- Lista de Estudiantes para Registro -->
<div class="row" id="students-container">
    @foreach($mockStudents as $student)
    <div class="col-lg-6 col-xl-4 mb-3 student-item" 
         data-group="{{ str_replace('Grupo ', '', $student->group_name) }}" 
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
                                        {{ $student->group_name }}
                                    </span>
                                    <code class="small">{{ $student->qr_code }}</code>
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="dropdown">
                            <button class="btn btn-sm attendance-toggle
                                @if($student->attendance_status == 'present') btn-success
                                @elseif($student->attendance_status == 'late') btn-warning  
                                @elseif($student->attendance_status == 'absent') btn-danger
                                @else btn-outline-secondary
                                @endif" 
                                data-toggle="dropdown">
                                @if($student->attendance_status == 'present')
                                    <span class="fe fe-check mr-1"></span>Presente
                                @elseif($student->attendance_status == 'late')
                                    <span class="fe fe-clock mr-1"></span>Tarde
                                @elseif($student->attendance_status == 'absent')
                                    <span class="fe fe-x mr-1"></span>Ausente
                                @else
                                    <span class="fe fe-more-horizontal mr-1"></span>Marcar
                                @endif
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item attendance-option" data-status="present" href="#">
                                    <span class="fe fe-check text-success mr-2"></span>Presente
                                </a>
                                <a class="dropdown-item attendance-option" data-status="late" href="#">
                                    <span class="fe fe-clock text-warning mr-2"></span>Tarde
                                </a>
                                <a class="dropdown-item attendance-option" data-status="absent" href="#">
                                    <span class="fe fe-x text-danger mr-2"></span>Ausente
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item attendance-option" data-status="justified" href="#">
                                    <span class="fe fe-info text-info mr-2"></span>Justificado
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if(isset($student->attendance_time))
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
    
    <!-- Placeholder para más estudiantes -->
    <div class="col-12">
        <div class="card border-dashed text-center">
            <div class="card-body">
                <span class="fe fe-plus fe-24 text-muted mb-2"></span>
                <h6 class="text-muted">{{ 78 - count($mockStudents) }} estudiantes adicionales</h6>
                <p class="text-muted small mb-0">Esta es una vista de demostración mostrando algunos estudiantes</p>
            </div>
        </div>
    </div>
</div>

<!-- Acciones de Guardado -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="mb-0">Acciones de Sesión</h6>
                        <small class="text-muted">Guarde los cambios o finalice la sesión</small>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-outline-secondary mr-2">
                            <span class="fe fe-save mr-1"></span>Guardar Borrador
                        </button>
                        <button class="btn btn-success">
                            <span class="fe fe-check-circle mr-1"></span>Finalizar Sesión
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('additional-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const studentsContainer = document.getElementById('students-container');
    const searchInput = document.getElementById('search-student');
    const filterGroup = document.getElementById('filter-group');
    const filterStatus = document.getElementById('filter-status');
    
    // Funcionalidad de búsqueda
    searchInput.addEventListener('input', function() {
        filterStudents();
    });
    
    // Filtros
    filterGroup.addEventListener('change', filterStudents);
    filterStatus.addEventListener('change', filterStudents);
    
    function filterStudents() {
        const searchTerm = searchInput.value.toLowerCase();
        const groupFilter = filterGroup.value;
        const statusFilter = filterStatus.value;
        
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
    
    // Marcar asistencia
    document.addEventListener('click', function(e) {
        if (e.target.closest('.attendance-option')) {
            e.preventDefault();
            const option = e.target.closest('.attendance-option');
            const status = option.dataset.status;
            const studentCard = option.closest('.student-card');
            const studentItem = option.closest('.student-item');
            const button = studentCard.querySelector('.attendance-toggle');
            
            // Actualizar estado visual
            updateAttendanceStatus(studentCard, studentItem, button, status);
            
            // Actualizar estadísticas
            updateStats();
        }
    });
    
    function updateAttendanceStatus(card, item, button, status) {
        // Limpiar clases previas
        card.className = 'card student-card ' + status;
        item.dataset.status = status;
        
        // Actualizar botón
        button.className = 'btn btn-sm attendance-toggle';
        
        switch(status) {
            case 'present':
                button.classList.add('btn-success');
                button.innerHTML = '<span class="fe fe-check mr-1"></span>Presente';
                break;
            case 'late':
                button.classList.add('btn-warning');
                button.innerHTML = '<span class="fe fe-clock mr-1"></span>Tarde';
                break;
            case 'absent':
                button.classList.add('btn-danger');
                button.innerHTML = '<span class="fe fe-x mr-1"></span>Ausente';
                break;
            case 'justified':
                button.classList.add('btn-info');
                button.innerHTML = '<span class="fe fe-info mr-1"></span>Justificado';
                break;
        }
        
        // Agregar timestamp
        let timeInfo = card.querySelector('.mt-2');
        if (!timeInfo) {
            timeInfo = document.createElement('div');
            timeInfo.className = 'row mt-2';
            timeInfo.innerHTML = `
                <div class="col">
                    <small class="text-muted">
                        <i class="fe fe-clock mr-1"></i>
                        Registrado a las ${new Date().toLocaleTimeString('es-ES', {hour: '2-digit', minute:'2-digit'})}
                    </small>
                </div>
            `;
            card.querySelector('.card-body').appendChild(timeInfo);
        }
    }
    
    function updateStats() {
        // Aquí se actualizarían las estadísticas en tiempo real
        console.log('Actualizando estadísticas...');
    }
    
    // Marcar todos como presentes
    document.getElementById('mark-all-present').addEventListener('click', function() {
        if (confirm('¿Marcar todos los estudiantes como presentes?')) {
            document.querySelectorAll('.student-card').forEach(card => {
                const item = card.closest('.student-item');
                const button = card.querySelector('.attendance-toggle');
                updateAttendanceStatus(card, item, button, 'present');
            });
            updateStats();
        }
    });
});
</script>
@endsection