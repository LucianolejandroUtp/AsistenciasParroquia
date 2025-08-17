@extends('layouts.app')

@section('title', 'Dashboard - Sistema de Asistencias [HOT RELOAD ACTIVO]')

@section('page-title', 'Dashboard Principal')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<!-- Stats Row -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <span class="h2 mb-0">78</span>
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
                        <span class="h2 mb-0">2</span>
                        <p class="small text-muted mb-0">Grupos Activos</p>
                    </div>
                    <div class="col-auto">
                        <span class="fe fe-layers fe-32 text-success"></span>
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
                        <span class="h2 mb-0">{{ $sessionsThisWeek ?? '0' }}</span>
                        <p class="small text-muted mb-0">Sesiones esta semana</p>
                    </div>
                    <div class="col-auto">
                        <span class="fe fe-calendar fe-32 text-warning"></span>
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
                        <span class="h2 mb-0">{{ $averageAttendance ?? '85' }}%</span>
                        <p class="small text-muted mb-0">Asistencia Promedio</p>
                    </div>
                    <div class="col-auto">
                        <span class="fe fe-bar-chart-2 fe-32 text-info"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Row -->
<div class="row">
    <!-- Próximas Sesiones -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-header-title">Próximas Sesiones de Catequesis</h4>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-sm btn-primary" type="button">
                            <span class="fe fe-plus fe-12 mr-2"></span>Nueva Sesión
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(isset($upcomingSessions) && count($upcomingSessions) > 0)
                    @foreach($upcomingSessions as $session)
                    <div class="row align-items-center mb-3 pb-3 border-bottom">
                        <div class="col-auto">
                            <div class="avatar avatar-md">
                                <span class="avatar-title rounded-circle bg-primary">
                                    <i class="fe fe-calendar fe-16"></i>
                                </span>
                            </div>
                        </div>
                        <div class="col">
                            <h5 class="mb-1">{{ $session->title ?? 'Sesión de Catequesis' }}</h5>
                            <p class="small text-muted mb-0">
                                <i class="fe fe-clock fe-12 mr-1"></i>
                                @if($session->date)
                                    {{ \Carbon\Carbon::parse($session->date)->format('d/m/Y') }}
                                    @if($session->time)
                                        {{ \Carbon\Carbon::parse($session->time)->format('H:i') }}
                                    @endif
                                @else
                                    Por programar
                                @endif
                            </p>
                            <p class="small text-muted mb-0">
                                <i class="fe fe-users fe-12 mr-1"></i>
                                Ambos grupos (Por configurar)
                            </p>
                        </div>
                        <div class="col-auto">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-white" data-toggle="dropdown">
                                    <i class="fe fe-more-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#">Ver detalles</a>
                                    <a class="dropdown-item" href="#">Editar</a>
                                    <a class="dropdown-item" href="#">Registrar asistencias</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                <div class="text-center py-4">
                    <i class="fe fe-calendar fe-48 text-muted mb-3"></i>
                    <h5 class="text-muted">No hay sesiones programadas</h5>
                    <p class="text-muted">Programa tu primera sesión de catequesis para comenzar.</p>
                    <button class="btn btn-primary">
                        <span class="fe fe-plus fe-12 mr-2"></span>Programar Sesión
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Resumen Rápido -->
    <div class="col-md-4">
        <!-- Distribución por Grupos -->
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-header-title">Distribución de Estudiantes</h4>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <span class="text-muted">Grupo A</span>
                        </div>
                        <div class="col-auto">
                            <span class="h5 mb-0">40</span>
                        </div>
                    </div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 51%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <span class="text-muted">Grupo B</span>
                        </div>
                        <div class="col-auto">
                            <span class="h5 mb-0">38</span>
                        </div>
                    </div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 49%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-header-title">Acciones Rápidas</h4>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="#" class="list-group-item list-group-item-action border-0">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="fe fe-qr-code fe-16 text-primary"></span>
                            </div>
                            <div class="col">
                                <span>Escanear QR</span>
                                <p class="small text-muted mb-0">Registrar asistencia rápida</p>
                            </div>
                            <div class="col-auto">
                                <span class="fe fe-chevron-right fe-12"></span>
                            </div>
                        </div>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action border-0">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="fe fe-users fe-16 text-success"></span>
                            </div>
                            <div class="col">
                                <span>Lista de Estudiantes</span>
                                <p class="small text-muted mb-0">Ver todos los estudiantes</p>
                            </div>
                            <div class="col-auto">
                                <span class="fe fe-chevron-right fe-12"></span>
                            </div>
                        </div>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action border-0">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="fe fe-bar-chart-2 fe-16 text-warning"></span>
                            </div>
                            <div class="col">
                                <span>Reportes</span>
                                <p class="small text-muted mb-0">Estadísticas y exportar</p>
                            </div>
                            <div class="col-auto">
                                <span class="fe fe-chevron-right fe-12"></span>
                            </div>
                        </div>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action border-0">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="fe fe-calendar fe-16 text-info"></span>
                            </div>
                            <div class="col">
                                <span>Programar Sesión</span>
                                <p class="small text-muted mb-0">Nueva sesión de catequesis</p>
                            </div>
                            <div class="col-auto">
                                <span class="fe fe-chevron-right fe-12"></span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer informativo -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card bg-light">
            <div class="card-body text-center">
                <h5 class="card-title">
                    <i class="fe fe-heart text-primary mr-2"></i>
                    Bienvenido al Sistema de Asistencias
                </h5>
                <p class="card-text text-muted">
                    Sistema diseñado para facilitar el seguimiento de la participación de los 78 estudiantes 
                    de Primera Comunión distribuidos en 2 grupos. 
                    Utiliza códigos QR para un registro rápido y eficiente.
                </p>
                @auth
                <p class="small text-muted mb-0">
                    Conectado como: <strong>{{ auth()->user()->name }}</strong> 
                    ({{ auth()->user()->userType->name ?? 'Usuario' }})
                </p>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection

@section('additional-js')
<script>
// Funcionalidad específica del dashboard
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh de estadísticas cada 5 minutos
    setInterval(function() {
        // Aquí se puede implementar la lógica para actualizar estadísticas
        console.log('Actualizando estadísticas del dashboard...');
    }, 300000); // 5 minutos
    
    // Configurar tooltips para los elementos interactivos
    if (typeof $ !== 'undefined') {
        $('[data-toggle="tooltip"]').tooltip();
    }
});
</script>
@endsection