@extends('layouts.app')

@section('title', 'Grupo B - Sistema de Asistencias')

@section('page-title', 'Estudiantes del Grupo B')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Estudiantes</a></li>
    <li class="breadcrumb-item active">Grupo B</li>
@endsection

@section('content')
<!-- Header del Grupo -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="text-white mb-1">{{ $groupStats->group_name }}</h2>
                        <p class="text-white-50 mb-0">Primera Comunión 2025</p>
                    </div>
                    <div class="col-auto">
                        <div class="avatar avatar-lg">
                            <span class="avatar-title rounded-circle bg-white text-info fs-24">B</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats del Grupo -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <span class="h2 mb-0">{{ $groupStats->total_students }}</span>
                        <p class="small text-muted mb-0">Total Estudiantes</p>
                    </div>
                    <div class="col-auto">
                        <span class="fe fe-users fe-32 text-info"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <span class="h2 mb-0">{{ $groupStats->active_students }}</span>
                        <p class="small text-muted mb-0">Activos</p>
                    </div>
                    <div class="col-auto">
                        <span class="fe fe-user-check fe-32 text-success"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <span class="h2 mb-0">{{ $groupStats->average_attendance }}%</span>
                        <p class="small text-muted mb-0">Asistencia Promedio</p>
                    </div>
                    <div class="col-auto">
                        <span class="fe fe-trending-up fe-32 text-warning"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <span class="h6 mb-0">{{ \Carbon\Carbon::parse($groupStats->next_session)->format('d/m H:i') }}</span>
                        <p class="small text-muted mb-0">Próxima Sesión</p>
                    </div>
                    <div class="col-auto">
                        <span class="fe fe-calendar fe-32 text-primary"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Acciones del Grupo -->
<div class="row mb-3">
    <div class="col-md-6">
        <div class="form-group">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Buscar en Grupo B...">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button">
                        <span class="fe fe-search"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 text-right">
        <button class="btn btn-info" type="button">
            <span class="fe fe-check-circle fe-12 mr-2"></span>Registrar Asistencia
        </button>
        <button class="btn btn-outline-info" type="button">
            <span class="fe fe-qr-code fe-12 mr-2"></span>QR del Grupo
        </button>
        <button class="btn btn-outline-secondary" type="button">
            <span class="fe fe-download fe-12 mr-2"></span>Exportar
        </button>
    </div>
</div>

<!-- Lista de Estudiantes del Grupo B -->
<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="card-header-title">Estudiantes del Grupo B</h4>
            </div>
            <div class="col-auto">
                <span class="badge badge-info">{{ count($mockStudents) }} de {{ $groupStats->total_students }} mostrados</span>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-borderless table-hover">
                <thead>
                    <tr>
                        <th>Orden</th>
                        <th>Nombre Completo</th>
                        <th>Código QR</th>
                        <th>Asistencia</th>
                        <th>Estado</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mockStudents as $student)
                    <tr>
                        <td>
                            <span class="badge badge-info">{{ $student->order_number }}</span>
                        </td>
                        <td>
                            <div class="media align-items-center">
                                <div class="avatar avatar-sm mr-3">
                                    <span class="avatar-title rounded-circle bg-soft-info text-info">
                                        {{ substr($student->full_name, 0, 1) }}
                                    </span>
                                </div>
                                <div class="media-body">
                                    <strong>{{ $student->full_name }}</strong>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <code class="small mr-2">{{ $student->qr_code }}</code>
                                <button class="btn btn-sm btn-outline-info" title="Ver QR">
                                    <span class="fe fe-qr-code fe-12"></span>
                                </button>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-fill mr-2" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $student->attendance_percentage >= 80 ? 'success' : ($student->attendance_percentage >= 60 ? 'warning' : 'danger') }}" 
                                         style="width: {{ $student->attendance_percentage }}%"></div>
                                </div>
                                <span class="small font-weight-bold">{{ $student->attendance_percentage }}%</span>
                            </div>
                            <small class="text-muted">{{ $student->attended_sessions }}/{{ $student->total_sessions }} sesiones</small>
                        </td>
                        <td>
                            <span class="badge badge-{{ $student->status == 'ACTIVO' ? 'success' : 'secondary' }}">
                                {{ $student->status }}
                            </span>
                        </td>
                        <td class="text-right">
                            <button class="btn btn-sm btn-success mr-1" title="Marcar Presente">
                                <span class="fe fe-check fe-12"></span>
                            </button>
                            <div class="dropdown d-inline-block">
                                <button class="btn btn-sm btn-white" data-toggle="dropdown">
                                    <span class="fe fe-more-vertical fe-12"></span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="#">
                                        <span class="fe fe-eye fe-12 mr-2"></span>Ver Perfil
                                    </a>
                                    <a class="dropdown-item" href="#">
                                        <span class="fe fe-bar-chart-2 fe-12 mr-2"></span>Historial
                                    </a>
                                    <a class="dropdown-item" href="#">
                                        <span class="fe fe-qr-code fe-12 mr-2"></span>Código QR
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#">
                                        <span class="fe fe-edit-2 fe-12 mr-2"></span>Editar
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Información adicional del grupo -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="mb-1">Mejor Asistencia</h6>
                        <p class="text-muted mb-0">Ana Sofía Mendoza - 100%</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="mb-1">Promedio General</h6>
                        <p class="text-muted mb-0">{{ $groupStats->average_attendance }}% asistencia</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Comparación con Grupo A -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Comparación entre Grupos</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="text-center">
                            <h5 class="text-primary">Grupo A</h5>
                            <div class="progress mb-2" style="height: 10px;">
                                <div class="progress-bar bg-primary" style="width: 85%"></div>
                            </div>
                            <small>85% promedio de asistencia</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-center">
                            <h5 class="text-info">Grupo B</h5>
                            <div class="progress mb-2" style="height: 10px;">
                                <div class="progress-bar bg-info" style="width: {{ $groupStats->average_attendance }}%"></div>
                            </div>
                            <small>{{ $groupStats->average_attendance }}% promedio de asistencia</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Acciones Rápidas -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Acciones Rápidas para Grupo B</h6>
                <div class="row">
                    <div class="col-md-3">
                        <button class="btn btn-outline-primary btn-block">
                            <span class="fe fe-users fe-16"></span><br>
                            <small>Ver Grupo A</small>
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-outline-success btn-block">
                            <span class="fe fe-check-circle fe-16"></span><br>
                            <small>Marcar Todos</small>
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-outline-warning btn-block">
                            <span class="fe fe-calendar fe-16"></span><br>
                            <small>Programar Sesión</small>
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-outline-info btn-block">
                            <span class="fe fe-bar-chart-2 fe-16"></span><br>
                            <small>Generar Reporte</small>
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
    // Funcionalidad de búsqueda específica del grupo
    const searchInput = document.querySelector('input[placeholder="Buscar en Grupo B..."]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            console.log('Buscando en Grupo B:', this.value);
        });
    }
    
    // Botones de acción rápida
    document.querySelectorAll('.btn-success[title="Marcar Presente"]').forEach(btn => {
        btn.addEventListener('click', function() {
            // Simulación de marcado de asistencia
            this.classList.remove('btn-success');
            this.classList.add('btn-warning');
            this.title = 'Marcado como Presente';
            this.innerHTML = '<span class="fe fe-check-circle fe-12"></span>';
        });
    });
});
</script>
@endsection