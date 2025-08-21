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
<!-- Tabla de Estudiantes con DataTables -->
<div class="card">
    <div class="card-body">
        <!-- Filtros del DataTable -->
        <div class="row mb-3">
            <div class="col-md-3">
                <select id="groupFilter" class="form-control form-control-sm">
                    <option value="">Grupos</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->name }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select id="statusFilter" class="form-control form-control-sm">
                    <option value="">Estados</option>
                    <option value="ACTIVO">Activo</option>
                    <option value="INACTIVO">Inactivo</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="attendanceFilter" class="form-control form-control-sm">
                    <option value="">Niveles</option>
                    <option value="high">Alta (≥90%)</option>
                    <option value="medium">Media (70-89%)</option>
                    <option value="low">Baja (<70%)</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-center justify-content-end">
                <div class="w-100 d-flex justify-content-end" style="gap:8px;">
                    <button id="clearFiltersBtn" class="btn btn-sm btn-outline-secondary">
                        <i class="fe fe-x-circle me-1"></i>
                        <span class="d-none d-sm-inline">Limpiar Filtros</span>
                    </button>

                    <button id="exportListBtn" class="btn btn-sm btn-primary" type="button">
                        <span class="fe fe-download fe-12 mr-2"></span>Exportar Lista
                    </button>
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover datatables" id="studentsDataTable">
                <thead>
                    <tr>
                        <th>Orden</th>
                        <th>Nombre Completo</th>
                        <th>Grupo</th>
                        <th>Asistencia</th>
                        <th>Estado</th>
                        <!-- Columna 'Registro' eliminada -->
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
                        <!-- Columna 'Registro' removida -->
                        <td class="text-right">
                            <div class="btn-group btn-group-sm" role="group" aria-label="Acciones">
                                <button type="button" class="btn btn-outline-primary btn-view-details" title="Ver Detalles" data-student-id="{{ $student->id }}">
                                    <span class="fe fe-eye fe-12"></span>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" title="Editar">
                                    <span class="fe fe-edit-2 fe-12"></span>
                                </button>
                                <button type="button" class="btn btn-outline-info" title="Ver QR">
                                    <span class="fe fe-qr-code fe-12"></span>
                                </button>
                                <button type="button" class="btn btn-outline-warning" title="Historial">
                                    <span class="fe fe-bar-chart-2 fe-12"></span>
                                </button>
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
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "infoEmpty": "Mostrando 0 a 0 de 0 registros",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "search": "Buscar:",
            "paginate": {
                "first": "Primero",
                "last": "Último", 
                "next": "Siguiente",
                "previous": "Anterior"
            },
            "emptyTable": "No hay datos disponibles en la tabla",
            "processing": "Procesando..."
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
                "targets": [3], // Columna Asistencia
                "orderable": true,
                "type": "num"
            },
            {
                "targets": [5], // Columna Acciones (ajustada después de eliminar 'Registro')
                "orderable": false,
                "searchable": false,
                "width": "160px"
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

            // Extraer porcentaje de asistencia de la columna 3 (índice 3)
            var attendanceText = data[3] || '';
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
        table.column(4).search(selectedStatus).draw(); // Columna 4 es "Estado"
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
    
    console.log('DataTables inicializado correctamente');
    console.log('Filtros dinámicos activados: Grupo, Estado, Asistencia');
});
</script>
<!-- Global modal container for AJAX-loaded content -->
<div class="modal fade" id="globalAjaxModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detalle del Estudiante</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="globalAjaxModalBody">
                        <div class="text-center py-3">
                            <div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
        // Handler para botón Ver Detalles (AJAX)
        $(document).on('click', '.btn-view-details', function(e) {
                e.preventDefault();
                var id = $(this).data('student-id');
                var url = '{{ url("/students") }}/' + id + '/details';

                var $modal = $('#globalAjaxModal');
                var $body = $('#globalAjaxModalBody');
                // Guardar elemento que abrió el modal para restaurar foco al cerrarlo
                $modal.data('lastActive', this);

                $body.html('<div class="text-center py-3"><div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div></div>');
                $modal.modal('show');

                $.get(url)
                        .done(function(html) {
                                $body.html(html);
                        })
                        .fail(function(xhr) {
                                var msg = 'Ocurrió un error al cargar los detalles.';
                                if (xhr.status === 403) msg = 'No tienes permiso para ver este recurso.';
                                if (xhr.status === 404) msg = 'Estudiante no encontrado.';
                                $body.html('<div class="alert alert-danger" role="alert">' + msg + '</div>');
                        });
        });
});
</script>
<script>
// Exportar CSV de filas visibles (excluye columna de acciones)
document.addEventListener('DOMContentLoaded', function() {
    function downloadCSV(filename, csvContent) {
        // Añadir BOM para compatibilidad con Excel (UTF-8)
        const bom = '\uFEFF';
        const blob = new Blob([bom + csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function rowToCsv(fields) {
        return fields.map(function(field) {
            if (field === null || typeof field === 'undefined') return '';
            const str = String(field);
            // Escapar comillas dobles
            if (str.indexOf(',') !== -1 || str.indexOf('"') !== -1 || str.indexOf('\n') !== -1) {
                return '"' + str.replace(/"/g, '""') + '"';
            }
            return str;
        }).join(',');
    }

    document.getElementById('exportListBtn').addEventListener('click', function() {
        try {
            const table = $('#studentsDataTable').DataTable();
            const rows = table.rows({ search: 'applied' }).data().toArray();

            if (!rows || rows.length === 0) {
                alert('No hay registros visibles para exportar.');
                return;
            }

            // Definir encabezados (excluir Acciones y Registro)
            const headers = ['Orden', 'Nombre Completo', 'Grupo', 'Asistencia', 'Estado'];
            const csvRows = [rowToCsv(headers)];

            rows.forEach(function(row) {
                // row es un array de celdas en HTML; extraer texto según índice
                // Columnas ahora: 0 Orden, 1 Nombre, 2 Grupo, 3 Asistencia, 4 Estado, 5 Acciones
                const getText = function(cell) {
                    // Crear elemento temporal para limpiar HTML
                    const tmp = document.createElement('div');
                    tmp.innerHTML = cell;
                    return tmp.textContent.trim().replace(/\s+/g, ' ');
                };

                const orden = getText(row[0]);
                const nombre = getText(row[1]);
                const grupo = getText(row[2]);
                const asistencia = getText(row[3]);
                const estado = getText(row[4]);

                csvRows.push(rowToCsv([orden, nombre, grupo, asistencia, estado]));
            });

            const csvContent = csvRows.join('\n');
            const filename = 'students_export_' + new Date().toISOString().slice(0,10) + '.csv';
            downloadCSV(filename, csvContent);
        } catch (err) {
            console.error('Error exportando CSV:', err);
            alert('Ocurrió un error al exportar. Revisa la consola para más detalles.');
        }
    });
});
</script>
<script>
// Modal focus management to avoid aria-hidden focus warnings
document.addEventListener('DOMContentLoaded', function() {
    var $globalModal = $('#globalAjaxModal');

    $globalModal.on('shown.bs.modal', function () {
        try {
            // Enfocar el contenedor del body para que el foco esté dentro del modal
            var $body = $(this).find('#globalAjaxModalBody');
            $body.attr('tabindex', -1).focus();
        } catch (e) {}
    });

    $globalModal.on('hide.bs.modal', function () {
        try {
            var active = document.activeElement;
            if (active && this.contains(active)) {
                // Desenfocar elementos activos dentro del modal
                active.blur();
            }
        } catch (e) {}
    });

    $globalModal.on('hidden.bs.modal', function () {
        var opener = $(this).data('lastActive');
        if (opener && opener.focus) {
            try { opener.focus(); } catch (e) {}
        }
        $(this).removeData('lastActive');
    });
});
</script>
@endsection