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
            <div class="col-md-2">
                <select id="groupFilter" class="form-control form-control-sm">
                    <option value="">Grupos</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->name }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select id="statusFilter" class="form-control form-control-sm">
                    <option value="">Estados</option>
                    <option value="ACTIVO">Activo</option>
                    <option value="INACTIVO">Inactivo</option>
                </select>
            </div>
            <div class="col-md-2">
                <select id="attendanceFilter" class="form-control form-control-sm">
                    <option value="">Niveles</option>
                    <option value="high">Alta (≥90%)</option>
                    <option value="medium">Media (70-89%)</option>
                    <option value="low">Baja (<70%)</option>
                </select>
            </div>
            <div class="col-md-6 d-flex align-items-center justify-content-end">
                <div class="w-100 d-flex justify-content-end" style="gap:8px;">
                    <button id="clearFiltersBtn" class="btn btn-sm btn-outline-secondary">
                        <i class="fe fe-x-circle me-1"></i>
                        <span class="d-none d-sm-inline">Limpiar</span>
                    </button>

                    <button id="exportListBtn" class="btn btn-sm btn-primary" type="button">
                        <span class="fe fe-download fe-12 mr-2"></span>Exportar
                    </button>

                    <button id="createStudentBtn" class="btn btn-sm btn-success" type="button" data-toggle="modal" data-target="#createStudentModal">
                        <span class="fe fe-plus fe-12 mr-2"></span>Crear
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
                    <!-- Los datos se cargarán vía AJAX -->
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

<!-- Modal para Crear Nuevo Estudiante -->
<div class="modal fade" id="createStudentModal" tabindex="-1" role="dialog" aria-labelledby="createStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createStudentModalLabel">
                    <i class="fe fe-user-plus mr-2"></i>Crear Nuevo Estudiante
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="createStudentForm" action="{{ route('students.store') }}" method="POST">
                    @csrf
                    
                    <!-- Alert container for validation errors -->
                    <div id="createStudentFormErrors" class="alert alert-danger d-none" role="alert">
                        <ul class="mb-0"></ul>
                    </div>

                    <div class="row">
                        <!-- Nombres -->
                        <div class="col-md-12 mb-3">
                            <label for="names" class="form-label">Nombres <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="names" name="names" placeholder="Ej: Juan Carlos" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Apellidos -->
                        <div class="col-md-6 mb-3">
                            <label for="paternal_surname" class="form-label">Apellido Paterno <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="paternal_surname" name="paternal_surname" placeholder="Ej: García" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="maternal_surname" class="form-label">Apellido Materno</label>
                            <input type="text" class="form-control" id="maternal_surname" name="maternal_surname" placeholder="Ej: López">
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Grupo -->
                        <div class="col-md-6 mb-3">
                            <label for="group_id" class="form-label">Grupo <span class="text-danger">*</span></label>
                            <select class="form-control" id="group_id" name="group_id" required>
                                <option value="">Seleccionar grupo...</option>
                                @foreach($groups as $group)
                                    @if($group->estado === 'ACTIVO')
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Número de Orden -->
                        <div class="col-md-6 mb-3">
                            <label for="order_number" class="form-label">Número de Orden <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="order_number" name="order_number" 
                                   placeholder="Ej: 1" min="1" max="100" required>
                            <div class="invalid-feedback"></div>
                            <small class="form-text text-muted">Número único dentro del grupo (1-100)</small>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="alert alert-info" role="alert">
                        <i class="fe fe-info mr-2"></i>
                        <strong>Nota:</strong> El código QR del estudiante se generará automáticamente una vez creado el registro.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fe fe-x mr-1"></i>Cancelar
                </button>
                <button type="submit" form="createStudentForm" class="btn btn-success" id="createStudentSubmitBtn">
                    <i class="fe fe-save mr-1"></i>Crear Estudiante
                </button>
            </div>
        </div>
    </div>
</div>

@section('additional-js')
<script src="{{ asset('tinydash/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('tinydash/js/dataTables.bootstrap4.min.js') }}"></script>
<!-- Librerías necesarias para generación de QR y descargas (copiadas de la vista qr-codes) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script src="{{ asset('js/qr-utils.js') }}"></script>
<script>
// Modal container (global) - ensure exists
if (!document.getElementById('ajaxGlobalModal')) {
        const modalHtml = `
        <div class="modal fade" id="ajaxGlobalModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Ver Código QR</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-3">Cargando...</div>
                </div>
            </div>
        </div>`;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
}

// Handler para botones Ver QR
document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-view-qr');
        if (!btn) return;
        e.preventDefault();
        const url = btn.getAttribute('data-url');
        const $modal = $('#ajaxGlobalModal');
        $modal.find('.modal-body').html('<div class="text-center py-3"><div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div></div>');
        $modal.modal('show');

        $.get(url)
                .done(function (html) {
                        $modal.find('.modal-body').html(html);
                })
                .fail(function () {
                        $modal.find('.modal-body').html('<div class="text-danger">Error cargando el código QR. Intenta recargar la página.</div>');
                });
});

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTables con AJAX
    var table = $('#studentsDataTable').DataTable({
        "ajax": {
            "url": "{{ route('students.ajax.data') }}",
            "type": "GET",
            "dataSrc": "data"
        },
        "processing": true,
        "serverSide": false,
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
            "processing": "Procesando...",
            "loadingRecords": "Cargando registros..."
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
    </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
        // Handler para botón Editar (AJAX: load form into modal)
        $(document).on('click', '.btn-edit-student', function(e) {
            e.preventDefault();
            var id = $(this).data('student-id');
            var url = '{{ url("/students") }}/' + id + '/edit';

            var $modal = $('#globalAjaxModal');
            var $body = $('#globalAjaxModalBody');
            $modal.data('lastActive', this);

            $body.html('<div class="text-center py-3"><div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div></div>');
            $modal.find('.modal-title').text('Editar Estudiante');
            $modal.modal('show');

            $.get(url)
                .done(function(html) {
                    $body.html(html);

                    // Interceptar submit del formulario para usar AJAX
                    var $form = $body.find('#studentEditForm');
                    $form.on('submit', function(ev) {
                        ev.preventDefault();
                        var action = $form.attr('action');
                        var method = ($form.find('input[name=_method]').val() || 'POST').toUpperCase();
                        var formData = $form.serialize();

                        $.ajax({
                            url: action,
                            method: method,
                            data: formData,
                            success: function(resp) {
                                // Mostrar notificación mínima y cerrar modal
                                                                showToast(resp.message || 'Guardado correctamente', 'success');
                                                                $modal.modal('hide');
                                                                // Actualizar la fila en el DataTable in-place usando el DTO devuelto
                                                                try {
                                                                    var updated = resp.student;
                                                                    var table = $('#studentsDataTable').DataTable();
                                                                    table.rows().every(function() {
                                                                        var data = this.data();
                                                                        var tmp = document.createElement('div');
                                                                        tmp.innerHTML = data[5] || '';
                                                                        var btn = tmp.querySelector('[data-student-id]');
                                                                        if (btn && String(btn.getAttribute('data-student-id')) === String(updated.id)) {
                                                                            // Construir nuevas celdas HTML
                                                                            var newOrden = '<span class="badge badge-soft-primary">' + (updated.order_number || '-') + '</span>';
                                                                            var newNombre = '<div><strong>' + (updated.full_name) + '</strong></div>';
                                                                            var newGrupo = '<span class="badge badge-' + (String(updated.group_name).indexOf('A') !== -1 ? 'primary' : 'info') + '">' + (updated.group_name || 'Sin Grupo') + '</span>';
                                                                            var newEstado = '<span class="badge badge-' + (updated.status == 'ACTIVO' ? 'success' : 'secondary') + '">' + updated.status + '</span>';
                                                                            data[0] = newOrden;
                                                                            data[1] = newNombre;
                                                                            data[2] = newGrupo;
                                                                            data[4] = newEstado;
                                                                            this.data(data).draw(false);
                                                                        }
                                                                    });
                                                                } catch (e) { console.error('Error actualizando fila:', e); }
                            },
                            error: function(xhr) {
                                if (xhr.status === 422) {
                                    // Errores de validación: mostrar mensajes
                                    var errors = xhr.responseJSON.errors || {};
                                    var errHtml = '<div class="alert alert-danger"><ul>';
                                    Object.keys(errors).forEach(function(k) {
                                        errors[k].forEach(function(msg) { errHtml += '<li>' + msg + '</li>'; });
                                    });
                                    errHtml += '</ul></div>';
                                    $body.prepend(errHtml);
                                    } else {
                                    showToast('Ocurrió un error al guardar. Revisa la consola.', 'danger');
                                    console.error(xhr);
                                }
                            }
                        });
                    });
                })
                .fail(function(xhr) {
                    var msg = 'Ocurrió un error al cargar el formulario de edición.';
                    if (xhr.status === 403) msg = 'No tienes permiso para editar este recurso.';
                    if (xhr.status === 404) msg = 'Estudiante no encontrado.';
                    $body.html('<div class="alert alert-danger" role="alert">' + msg + '</div>');
                });
        });

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
                showToast('No hay registros visibles para exportar.', 'warning');
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
            showToast('Ocurrió un error al exportar. Revisa la consola para más detalles.', 'danger');
        }
    });
});
</script>
<!-- Notifications container (TinyDash style) -->
<div id="globalNotifications" class="notifications" style="position: fixed; top: 1rem; right: 1rem; z-index: 1080; min-width: 280px;"></div>

<script>
// TinyDash-style Notifications helper (compatible API: showToast(message, type))
// Types: info, success, warning, danger
// Notifications helper mapped to TinyDash/Bootstrap alert markup
// Uses the same structure as `resources/js/app.js` to ensure styles match the template
function showToast(message, type) {
    type = type || 'info'; // info, success, warning, danger
    var iconName = type === 'success' ? 'check-circle' : (type === 'danger' ? 'alert-circle' : (type === 'warning' ? 'alert-triangle' : 'info'));

    var id = 'alert-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
    var container = document.createElement('div');
    container.id = id;
    container.className = 'alert alert-' + (type === 'info' ? 'info' : type) + ' alert-dismissible fade show position-fixed';
    container.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    container.setAttribute('role', 'alert');
    container.innerHTML = '\n' +
        '<i class="fe fe-' + iconName + ' mr-2"></i>' +
        '<span class="align-middle">' + message + '</span>' +
        '<button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">' +
            '<span aria-hidden="true">&times;</span>' +
        '</button>';

    // Append to body for consistent placement with other template notifications
    document.body.appendChild(container);

    // Auto-remove after ~6000ms
    setTimeout(function() {
        if (container && container.parentNode) {
            // Use Bootstrap's fade out if available
            try {
                $(container).alert('close');
            } catch (e) {
                container.remove();
            }
        }
    }, 6000);

    // Allow manual close via the button (Bootstrap will handle removal)
}

// Modal focus management to avoid aria-hidden focus warnings
document.addEventListener('DOMContentLoaded', function() {
    var $globalModal = $('#globalAjaxModal');

    $globalModal.on('shown.bs.modal', function () {
        try {
            var $body = $(this).find('#globalAjaxModalBody');
            $body.attr('tabindex', -1).focus();
        } catch (e) {}
    });

    $globalModal.on('hide.bs.modal', function () {
        try {
            var active = document.activeElement;
            if (active && this.contains(active)) {
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

<!-- JavaScript para Crear Nuevo Estudiante -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const $createModal = $('#createStudentModal');
    const $createForm = $('#createStudentForm');
    const $submitBtn = $('#createStudentSubmitBtn');
    const $errorsContainer = $('#createStudentFormErrors');

    // Función para limpiar errores de validación
    function clearValidationErrors() {
        $errorsContainer.addClass('d-none').find('ul').empty();
        $createForm.find('.is-invalid').removeClass('is-invalid');
        $createForm.find('.invalid-feedback').text('');
    }

    // Función para mostrar errores de validación
    function showValidationErrors(errors) {
        $errorsContainer.removeClass('d-none');
        const $errorsList = $errorsContainer.find('ul');
        $errorsList.empty();

        Object.keys(errors).forEach(function(field) {
            const fieldErrors = errors[field];
            fieldErrors.forEach(function(error) {
                $errorsList.append('<li>' + error + '</li>');
            });

            // Marcar campo específico como inválido
            const $field = $createForm.find('[name="' + field + '"]');
            $field.addClass('is-invalid');
            $field.siblings('.invalid-feedback').text(fieldErrors[0]);
        });
    }

    // Función para resetear el formulario
    function resetCreateForm() {
        $createForm[0].reset();
        clearValidationErrors();
        $submitBtn.prop('disabled', false).html('<i class="fe fe-save mr-1"></i>Crear Estudiante');
    }

    // Event: Al abrir el modal, resetear formulario
    $createModal.on('show.bs.modal', function() {
        resetCreateForm();
    });

    // Event: Submit del formulario (AJAX)
    $createForm.on('submit', function(e) {
        e.preventDefault();
        
        // Deshabilitar botón para evitar doble submit
        $submitBtn.prop('disabled', true).html('<i class="fe fe-loader mr-1"></i>Creando...');
        
        // Limpiar errores previos
        clearValidationErrors();

        // Obtener datos del formulario
        const formData = $createForm.serialize();
        const actionUrl = $createForm.attr('action');

        // Enviar petición AJAX
        $.ajax({
            url: actionUrl,
            method: 'POST',
            data: formData,
            success: function(response) {
                // Cerrar modal
                $createModal.modal('hide');
                
                // Mostrar mensaje de éxito
                showToast(response.message || 'Estudiante creado correctamente', 'success');
                
                // Recargar DataTable para mostrar el nuevo estudiante
                $('#studentsDataTable').DataTable().ajax.reload(null, false);
                
                // Resetear formulario
                resetCreateForm();
            },
            error: function(xhr) {
                // Re-habilitar botón
                $submitBtn.prop('disabled', false).html('<i class="fe fe-save mr-1"></i>Crear Estudiante');
                
                if (xhr.status === 422) {
                    // Errores de validación
                    const errors = xhr.responseJSON.errors || {};
                    showValidationErrors(errors);
                    showToast('Por favor corrige los errores en el formulario', 'warning');
                } else if (xhr.status === 409) {
                    // Conflicto (ej: número de orden duplicado)
                    const message = xhr.responseJSON.message || 'Ya existe un estudiante con este número de orden en el grupo seleccionado';
                    showToast(message, 'warning');
                } else {
                    // Error general
                    const message = xhr.responseJSON.message || 'Ocurrió un error al crear el estudiante';
                    showToast(message, 'danger');
                    console.error('Error creating student:', xhr);
                }
            }
        });
    });

    // Event: Cambio de grupo - sugerir siguiente número disponible
    $('#group_id').on('change', function() {
        const groupId = $(this).val();
        if (!groupId) {
            $('#order_number').val('');
            return;
        }

        // Obtener el siguiente número disponible para este grupo
        // (Esto es opcional - podrías implementar un endpoint para esto)
        // Por simplicidad, dejaré que el usuario ingrese manualmente
        $('#order_number').focus();
    });

    console.log('Create Student modal functionality initialized');
});
</script>
@endsection