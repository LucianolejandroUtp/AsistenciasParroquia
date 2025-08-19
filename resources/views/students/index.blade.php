@extends('layouts.app')

@section('title', 'Lista Completa de Estudiantes - Sistema de Asistencias')

@section('page-title', 'Lista Completa de Estudiantes')

@section('additional-css')
    <link rel="stylesheet" href="{{ asset('tinydash/css/dataTables.bootstrap4.css') }}">
@endsection

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
    <!-- Estadísticas dinámicas por grupo -->
    @if($stats->groups && $stats->groups->count() > 0)
        @foreach($stats->groups as $group)
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <span class="h2 mb-0">{{ $group->students_count }}</span>
                            <p class="small text-muted mb-0">{{ $group->name }}</p>
                        </div>
                        <div class="col-auto">
                            <span class="fe fe-user-check fe-32 text-{{ $loop->first ? 'success' : 'info' }}"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @endif
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

<!-- Tabla de Estudiantes con DataTables -->
<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="card-header-title">Lista de Estudiantes de Primera Comunión</h4>
                <p class="card-text small text-muted mb-0">
                    Gestiona todos los estudiantes registrados con funciones avanzadas de búsqueda, filtrado y ordenamiento.
                </p>
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
        <!-- Filtros del DataTable -->
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="groupFilter" class="form-label small text-muted">Filtrar por Grupo:</label>
                <select id="groupFilter" class="form-control form-control-sm">
                    <option value="">Todos los grupos</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->name }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="statusFilter" class="form-label small text-muted">Filtrar por Estado:</label>
                <select id="statusFilter" class="form-control form-control-sm">
                    <option value="">Todos los estados</option>
                    <option value="ACTIVO">Activo</option>
                    <option value="INACTIVO">Inactivo</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="attendanceFilter" class="form-label small text-muted">Filtrar por Asistencia:</label>
                <select id="attendanceFilter" class="form-control form-control-sm">
                    <option value="">Todos los niveles</option>
                    <option value="high">Alta (≥90%)</option>
                    <option value="medium">Media (70-89%)</option>
                    <option value="low">Baja (<70%)</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted d-block">&nbsp;</label>
                <button id="clearFiltersBtn" class="btn btn-sm btn-outline-secondary form-control-sm">
                    <i class="fe fe-x-circle me-1"></i>Limpiar Filtros
                </button>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover datatables" id="studentsDataTable">
                <thead>
                    <tr>
                        <th>Orden</th>
                        <th>Nombre Completo</th>
                        <th>Grupo</th>
                        <th>Código Estudiante</th>
                        <th>Asistencia</th>
                        <th>Estado</th>
                        <th>Registro</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr>
                        <td>
                            <span class="badge badge-soft-primary">{{ $student->order_number ?? '-' }}</span>
                        </td>
                        <td>
                            <div>
                                <strong>{{ $student->full_name }}</strong>
                                <br>
                                <small class="text-muted">{{ $student->names }} {{ $student->paternal_surname }}</small>
                            </div>
                        </td>
                        <td>
                            @if($student->group_name !== 'Sin Grupo')
                                <span class="badge badge-{{ strpos($student->group_name, 'A') !== false ? 'primary' : 'info' }}">
                                    {{ $student->group_name }}
                                </span>
                            @else
                                <span class="badge badge-secondary">Sin Grupo</span>
                            @endif
                        </td>
                        <td>
                            <code class="small">{{ $student->student_code }}</code>
                            @if($student->qr_code)
                                <br><small class="text-muted">QR: {{ $student->qr_code }}</small>
                            @endif
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
                        <td>
                            <small class="text-muted">{{ $student->created_at }}</small>
                            @if($student->updated_at !== $student->created_at)
                                <br><small class="text-muted">Mod: {{ $student->updated_at }}</small>
                            @endif
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
<script src="{{ asset('tinydash/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('tinydash/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTables
    var table = $('#studentsDataTable').DataTable({
        autoWidth: true,
        responsive: true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
        },
        "lengthMenu": [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "Todos"]
        ],
        "pageLength": 25,
        "order": [[1, "asc"]], // Ordenar por nombre por defecto
        "columnDefs": [
            {
                "targets": [0], // Columna Orden
                "type": "num",
                "width": "50px"
            },
            {
                "targets": [4], // Columna Asistencia
                "orderable": true,
                "type": "num"
            },
            {
                "targets": [7], // Columna Acciones
                "orderable": false,
                "searchable": false,
                "width": "100px"
            }
        ],
        "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
               '<"row"<"col-sm-12"tr>>' +
               '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        "search": {
            "placeholder": "Buscar estudiantes..."
        }
    });

    // Filtro personalizado para asistencia
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'studentsDataTable') {
                return true;
            }

            var attendanceFilter = $('#attendanceFilter').val();
            if (attendanceFilter === '') {
                return true;
            }

            // Extraer porcentaje de asistencia de la columna 4 (índice 4)
            var attendanceText = data[4] || '';
            var percentageMatch = attendanceText.match(/(\d+)%/);
            
            if (!percentageMatch) {
                return true;
            }

            var percentage = parseInt(percentageMatch[1]);

            switch (attendanceFilter) {
                case 'high':
                    return percentage >= 90;
                case 'medium':
                    return percentage >= 70 && percentage < 90;
                case 'low':
                    return percentage < 70;
                default:
                    return true;
            }
        }
    );

    // Event listeners para filtros
    $('#groupFilter').on('change', function() {
        var selectedGroup = this.value;
        table.column(2).search(selectedGroup).draw(); // Columna 2 es "Grupo"
    });

    $('#statusFilter').on('change', function() {
        var selectedStatus = this.value;
        table.column(5).search(selectedStatus).draw(); // Columna 5 es "Estado"
    });

    $('#attendanceFilter').on('change', function() {
        table.draw(); // Redibuja la tabla aplicando el filtro personalizado
    });

    // Botón para limpiar todos los filtros
    function clearAllFilters() {
        $('#groupFilter').val('');
        $('#statusFilter').val('');
        $('#attendanceFilter').val('');
        table.search('').columns().search('').draw();
    }

    // Event listener para el botón limpiar filtros
    $('#clearFiltersBtn').on('click', clearAllFilters);

    // Funcionalidad adicional
    if (typeof $ !== 'undefined') {
        $('[data-toggle="tooltip"]').tooltip();
    }
    
    console.log('DataTables inicializado correctamente para {{ $stats->total_students }} estudiantes');
    console.log('Filtros dinámicos activados: Grupo, Estado, Asistencia');
});
</script>
@endsection