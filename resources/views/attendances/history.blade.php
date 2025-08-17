@extends('layouts.app')

@section('title', 'Historial de Asistencias - Sistema de Asistencias')

@section('page-title', 'Historial de Asistencias')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Asistencias</li>
    <li class="breadcrumb-item active">Historial</li>
@endsection

@section('additional-css')
<style>
.session-card {
    transition: all 0.2s ease-in-out;
    cursor: pointer;
}

.session-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.attendance-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.progress-ring {
    transform: rotate(-90deg);
}

.chart-container {
    position: relative;
    height: 200px;
}
</style>
@endsection

@section('content')
<!-- Estadísticas Generales -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <span class="h2 mb-0">{{ $historyStats->total_sessions }}</span>
                        <p class="small text-muted mb-0">Total Sesiones</p>
                    </div>
                    <div class="col-auto">
                        <span class="fe fe-calendar fe-32 text-primary"></span>
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
                        <span class="h2 mb-0">{{ $historyStats->average_attendance }}%</span>
                        <p class="small text-muted mb-0">Promedio General</p>
                    </div>
                    <div class="col-auto">
                        <span class="fe fe-trending-up fe-32 text-success"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-success">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <span class="h6 mb-0 text-success">{{ $historyStats->best_session }}</span>
                        <p class="small text-muted mb-0">Mejor Sesión</p>
                    </div>
                    <div class="col-auto">
                        <span class="fe fe-award fe-32 text-success"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-warning">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <span class="h6 mb-0 text-warning">{{ $historyStats->lowest_session }}</span>
                        <p class="small text-muted mb-0">Menor Asistencia</p>
                    </div>
                    <div class="col-auto">
                        <span class="fe fe-alert-triangle fe-32 text-warning"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros y Búsqueda -->
<div class="row mb-3">
    <div class="col-md-4">
        <div class="form-group">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Buscar sesión..." id="search-session">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button">
                        <span class="fe fe-search"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <select class="form-control" id="filter-month">
            <option value="">Todos los meses</option>
            <option value="2025-08">Agosto 2025</option>
            <option value="2025-07">Julio 2025</option>
            <option value="2025-06">Junio 2025</option>
        </select>
    </div>
    <div class="col-md-2">
        <select class="form-control" id="filter-attendance">
            <option value="">Todas</option>
            <option value="high">Alta (>80%)</option>
            <option value="medium">Media (60-80%)</option>
            <option value="low">Baja (<60%)</option>
        </select>
    </div>
    <div class="col-md-4 text-right">
        <button class="btn btn-primary">
            <span class="fe fe-download mr-1"></span>Exportar Historial
        </button>
        <button class="btn btn-outline-primary">
            <span class="fe fe-bar-chart-2 mr-1"></span>Generar Reporte
        </button>
    </div>
</div>

<!-- Vista de Sesiones -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="mb-0">Vista del Historial</h6>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary active" id="view-cards">
                                <span class="fe fe-grid"></span> Tarjetas
                            </button>
                            <button type="button" class="btn btn-outline-primary" id="view-table">
                                <span class="fe fe-list"></span> Tabla
                            </button>
                            <button type="button" class="btn btn-outline-primary" id="view-chart">
                                <span class="fe fe-bar-chart-2"></span> Gráfico
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Vista en Tarjetas (por defecto) -->
<div id="cards-view">
    <div class="row">
        @foreach($mockSessions as $session)
        <div class="col-lg-4 col-md-6 mb-4 session-item" 
             data-title="{{ strtolower($session->title) }}" 
             data-month="{{ \Carbon\Carbon::parse($session->date)->format('Y-m') }}"
             data-attendance="{{ $session->attendance_percentage }}">
            <div class="card session-card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="mb-0">{{ $session->title }}</h6>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($session->date)->format('d/m/Y') }} - 
                                {{ \Carbon\Carbon::createFromFormat('H:i', $session->time)->format('H:i') }}
                            </small>
                        </div>
                        <div class="col-auto">
                            <span class="badge badge-{{ $session->status == 'completed' ? 'success' : 'warning' }}">
                                {{ $session->status == 'completed' ? 'Completada' : 'Pendiente' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Progreso de Asistencia -->
                    <div class="row align-items-center mb-3">
                        <div class="col">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small font-weight-bold">Asistencia</span>
                                <span class="small font-weight-bold">{{ $session->attendance_percentage }}%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-{{ $session->attendance_percentage >= 80 ? 'success' : ($session->attendance_percentage >= 60 ? 'warning' : 'danger') }}" 
                                     style="width: {{ $session->attendance_percentage }}%"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Estadísticas de la Sesión -->
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="mb-1">
                                <span class="h6 text-success">{{ $session->present_count }}</span>
                            </div>
                            <small class="text-muted">Presentes</small>
                        </div>
                        <div class="col-4">
                            <div class="mb-1">
                                <span class="h6 text-warning">{{ $session->late_count }}</span>
                            </div>
                            <small class="text-muted">Tarde</small>
                        </div>
                        <div class="col-4">
                            <div class="mb-1">
                                <span class="h6 text-danger">{{ $session->absent_count }}</span>
                            </div>
                            <small class="text-muted">Ausentes</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col">
                            <small class="text-muted">
                                <i class="fe fe-users mr-1"></i>
                                {{ $session->total_students }} estudiantes
                            </small>
                        </div>
                        <div class="col-auto">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-white" data-toggle="dropdown">
                                    <span class="fe fe-more-vertical"></span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="#">
                                        <span class="fe fe-eye mr-2"></span>Ver Detalles
                                    </a>
                                    <a class="dropdown-item" href="#">
                                        <span class="fe fe-download mr-2"></span>Exportar
                                    </a>
                                    <a class="dropdown-item" href="#">
                                        <span class="fe fe-edit-2 mr-2"></span>Editar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        
        <!-- Indicador de más sesiones -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-dashed text-center" style="border-style: dashed; border-color: #dee2e6;">
                <div class="card-body d-flex flex-column justify-content-center" style="min-height: 250px;">
                    <span class="fe fe-calendar fe-32 text-muted mb-2"></span>
                    <h6 class="text-muted">Más Sesiones</h6>
                    <small class="text-muted">+9 sesiones en el historial</small>
                    <button class="btn btn-outline-primary btn-sm mt-2">
                        Cargar Más
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Vista en Tabla (oculta por defecto) -->
<div id="table-view" style="display: none;">
    <div class="card">
        <div class="card-header">
            <h4 class="card-header-title">Historial de Sesiones</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-borderless table-hover">
                    <thead>
                        <tr>
                            <th>Sesión</th>
                            <th>Fecha</th>
                            <th>Presentes</th>
                            <th>Tarde</th>
                            <th>Ausentes</th>
                            <th>Asistencia</th>
                            <th>Estado</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mockSessions as $session)
                        <tr>
                            <td>
                                <strong>{{ $session->title }}</strong>
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($session->date)->format('d/m/Y') }}<br>
                                <small class="text-muted">{{ \Carbon\Carbon::createFromFormat('H:i', $session->time)->format('H:i') }}</small>
                            </td>
                            <td>
                                <span class="badge badge-success">{{ $session->present_count }}</span>
                            </td>
                            <td>
                                <span class="badge badge-warning">{{ $session->late_count }}</span>
                            </td>
                            <td>
                                <span class="badge badge-danger">{{ $session->absent_count }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-fill mr-2" style="height: 6px;">
                                        <div class="progress-bar bg-{{ $session->attendance_percentage >= 80 ? 'success' : ($session->attendance_percentage >= 60 ? 'warning' : 'danger') }}" 
                                             style="width: {{ $session->attendance_percentage }}%"></div>
                                    </div>
                                    <span class="small">{{ $session->attendance_percentage }}%</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-{{ $session->status == 'completed' ? 'success' : 'warning' }}">
                                    {{ $session->status == 'completed' ? 'Completada' : 'Pendiente' }}
                                </span>
                            </td>
                            <td class="text-right">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-white" data-toggle="dropdown">
                                        <span class="fe fe-more-vertical"></span>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#">
                                            <span class="fe fe-eye mr-2"></span>Ver Detalles
                                        </a>
                                        <a class="dropdown-item" href="#">
                                            <span class="fe fe-download mr-2"></span>Exportar
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Vista en Gráfico (oculta por defecto) -->
<div id="chart-view" style="display: none;">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-header-title">Tendencia de Asistencia</h4>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-header-title">Resumen Estadístico</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Promedio:</span>
                            <strong>{{ $historyStats->average_attendance }}%</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Mejor:</span>
                            <strong class="text-success">94%</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Menor:</span>
                            <strong class="text-danger">58%</strong>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Total Sesiones:</span>
                            <strong>{{ $historyStats->total_sessions }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Información adicional -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card bg-light">
            <div class="card-body text-center">
                <h6 class="card-title">
                    <i class="fe fe-info text-primary mr-2"></i>
                    Información del Historial
                </h6>
                <p class="card-text small text-muted mb-0">
                    Esta vista muestra el historial completo de sesiones de catequesis con datos de asistencia.
                    Los datos son ejemplos para demostración de la interfaz.
                    <br>
                    <strong>Próxima funcionalidad:</strong> Integración con datos reales y filtros avanzados.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('additional-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elementos de vista
    const viewCards = document.getElementById('view-cards');
    const viewTable = document.getElementById('view-table');
    const viewChart = document.getElementById('view-chart');
    const cardsView = document.getElementById('cards-view');
    const tableView = document.getElementById('table-view');
    const chartView = document.getElementById('chart-view');
    
    // Cambio de vistas
    viewCards.addEventListener('click', function() {
        showView('cards');
    });
    
    viewTable.addEventListener('click', function() {
        showView('table');
    });
    
    viewChart.addEventListener('click', function() {
        showView('chart');
        initChart();
    });
    
    function showView(view) {
        // Ocultar todas las vistas
        cardsView.style.display = 'none';
        tableView.style.display = 'none';
        chartView.style.display = 'none';
        
        // Remover clase activa de todos los botones
        viewCards.classList.remove('active');
        viewTable.classList.remove('active');
        viewChart.classList.remove('active');
        
        // Mostrar vista seleccionada
        switch(view) {
            case 'cards':
                cardsView.style.display = 'block';
                viewCards.classList.add('active');
                break;
            case 'table':
                tableView.style.display = 'block';
                viewTable.classList.add('active');
                break;
            case 'chart':
                chartView.style.display = 'block';
                viewChart.classList.add('active');
                break;
        }
    }
    
    // Filtros
    const searchInput = document.getElementById('search-session');
    const filterMonth = document.getElementById('filter-month');
    const filterAttendance = document.getElementById('filter-attendance');
    
    searchInput.addEventListener('input', filterSessions);
    filterMonth.addEventListener('change', filterSessions);
    filterAttendance.addEventListener('change', filterSessions);
    
    function filterSessions() {
        const searchTerm = searchInput.value.toLowerCase();
        const monthFilter = filterMonth.value;
        const attendanceFilter = filterAttendance.value;
        
        document.querySelectorAll('.session-item').forEach(item => {
            const title = item.dataset.title;
            const month = item.dataset.month;
            const attendance = parseInt(item.dataset.attendance);
            
            let show = true;
            
            if (searchTerm && !title.includes(searchTerm)) show = false;
            if (monthFilter && month !== monthFilter) show = false;
            
            if (attendanceFilter) {
                switch(attendanceFilter) {
                    case 'high':
                        if (attendance <= 80) show = false;
                        break;
                    case 'medium':
                        if (attendance < 60 || attendance > 80) show = false;
                        break;
                    case 'low':
                        if (attendance >= 60) show = false;
                        break;
                }
            }
            
            item.style.display = show ? 'block' : 'none';
        });
    }
    
    function initChart() {
        // Placeholder para inicializar gráfico
        const ctx = document.getElementById('attendanceChart');
        if (ctx && !ctx.chart) {
            // Aquí se inicializaría Chart.js
            console.log('Inicializando gráfico de asistencia...');
        }
    }
});
</script>
@endsection