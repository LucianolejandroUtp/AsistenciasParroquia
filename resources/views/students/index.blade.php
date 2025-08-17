@extends('layouts.app')

@section('title', 'Lista Completa de Estudiantes - Sistema de Asistencias')

@section('page-title', 'Lista Completa de Estudiantes')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Estudiantes</li>
    <li class="breadcrumb-item active">Lista Completa</li>
@endsection

@section('content')
<!-- Stats Row -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <span class="h2 mb-0">{{ $stats->total_students }}</span>
                        <p class="small text-muted mb-0">Total Estudiantes</p>
                    </div>
                    <div class="col-auto">
                        <span class="fe fe-users fe-32 text-primary"></span>
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
                        <span class="h2 mb-0">{{ $stats->group_a_count }}</span>
                        <p class="small text-muted mb-0">Grupo A</p>
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
                        <span class="h2 mb-0">{{ $stats->group_b_count }}</span>
                        <p class="small text-muted mb-0">Grupo B</p>
                    </div>
                    <div class="col-auto">
                        <span class="fe fe-user-plus fe-32 text-info"></span>
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
                        <span class="h2 mb-0">{{ $stats->average_attendance }}%</span>
                        <p class="small text-muted mb-0">Asistencia Promedio</p>
                    </div>
                    <div class="col-auto">
                        <span class="fe fe-trending-up fe-32 text-warning"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros y Acciones -->
<div class="row mb-3">
    <div class="col-md-6">
        <div class="form-group">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Buscar estudiante...">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button">
                        <span class="fe fe-search"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <select class="form-control">
            <option value="">Todos los grupos</option>
            <option value="1">Grupo A</option>
            <option value="2">Grupo B</option>
        </select>
    </div>
    <div class="col-md-3">
        <select class="form-control">
            <option value="">Estado</option>
            <option value="ACTIVO">Activos</option>
            <option value="INACTIVO">Inactivos</option>
        </select>
    </div>
</div>

<!-- Tabla de Estudiantes -->
<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="card-header-title">Lista de Estudiantes de Primera Comunión</h4>
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-primary" type="button">
                    <span class="fe fe-download fe-12 mr-2"></span>Exportar Lista
                </button>
                <button class="btn btn-sm btn-outline-primary" type="button">
                    <span class="fe fe-qr-code fe-12 mr-2"></span>Ver QR Codes
                </button>
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
                        <th>Grupo</th>
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
                            <span class="badge badge-soft-primary">{{ $student->order_number }}</span>
                        </td>
                        <td>
                            <div class="media align-items-center">
                                <div class="avatar avatar-sm mr-3">
                                    <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                        {{ substr($student->full_name, 0, 1) }}
                                    </span>
                                </div>
                                <div class="media-body">
                                    <strong>{{ $student->full_name }}</strong>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-{{ $student->group_id == 1 ? 'primary' : 'info' }}">
                                {{ $student->group_name }}
                            </span>
                        </td>
                        <td>
                            <code class="small">{{ $student->qr_code }}</code>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-fill mr-2" style="height: 6px;">
                                    <div class="progress-bar bg-{{ $student->attendance_percentage >= 80 ? 'success' : ($student->attendance_percentage >= 60 ? 'warning' : 'danger') }}" 
                                         style="width: {{ $student->attendance_percentage }}%"></div>
                                </div>
                                <span class="small text-muted">{{ $student->attendance_percentage }}%</span>
                            </div>
                            <small class="text-muted">{{ $student->attended_sessions }}/{{ $student->total_sessions }} sesiones</small>
                        </td>
                        <td>
                            <span class="badge badge-{{ $student->status == 'ACTIVO' ? 'success' : 'secondary' }}">
                                {{ $student->status }}
                            </span>
                        </td>
                        <td class="text-right">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-white" data-toggle="dropdown">
                                    <span class="fe fe-more-vertical fe-12"></span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="#">
                                        <span class="fe fe-eye fe-12 mr-2"></span>Ver Detalles
                                    </a>
                                    <a class="dropdown-item" href="#">
                                        <span class="fe fe-edit-2 fe-12 mr-2"></span>Editar
                                    </a>
                                    <a class="dropdown-item" href="#">
                                        <span class="fe fe-qr-code fe-12 mr-2"></span>Ver QR
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#">
                                        <span class="fe fe-bar-chart-2 fe-12 mr-2"></span>Historial
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Paginación Mock -->
        <div class="row mt-4">
            <div class="col-sm-12 col-md-5">
                <div class="dataTables_info">
                    Mostrando 1 a 4 de 78 estudiantes
                </div>
            </div>
            <div class="col-sm-12 col-md-7">
                <div class="dataTables_paginate">
                    <ul class="pagination">
                        <li class="paginate_button page-item previous disabled">
                            <a class="page-link" href="#">Anterior</a>
                        </li>
                        <li class="paginate_button page-item active">
                            <a class="page-link" href="#">1</a>
                        </li>
                        <li class="paginate_button page-item">
                            <a class="page-link" href="#">2</a>
                        </li>
                        <li class="paginate_button page-item">
                            <a class="page-link" href="#">3</a>
                        </li>
                        <li class="paginate_button page-item next">
                            <a class="page-link" href="#">Siguiente</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Información Adicional -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card bg-light">
            <div class="card-body text-center">
                <h6 class="card-title">
                    <i class="fe fe-info text-primary mr-2"></i>
                    Información de la Lista
                </h6>
                <p class="card-text small text-muted mb-0">
                    Esta es una vista de demostración. Los datos mostrados son ejemplos para visualizar 
                    la interfaz del sistema de gestión de estudiantes de Primera Comunión.
                    <br>
                    <strong>Próxima funcionalidad:</strong> Integración con base de datos real y funcionalidades CRUD.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('additional-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidad de búsqueda (demo)
    const searchInput = document.querySelector('input[placeholder="Buscar estudiante..."]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            console.log('Buscando:', this.value);
        });
    }
    
    // Tooltips
    if (typeof $ !== 'undefined') {
        $('[data-toggle="tooltip"]').tooltip();
    }
});
</script>
@endsection