# DataTables - Documentación Completa y Opciones de Configuración

> **Documento generado:** 19 de Agosto de 2025  
> **Fuente:** Documentación oficial de DataTables vía MCP Context7  
> **Propósito:** Guía completa para desarrolladores del Sistema de Asistencias

---

## 📋 Índice

1. [Introducción y Conceptos Básicos](#introducción-y-conceptos-básicos)
2. [Configuración e Inicialización](#configuración-e-inicialización)
3. [Opciones de Configuración Principal](#opciones-de-configuración-principal)
4. [API de DataTables](#api-de-datatables)
5. [Eventos y Callbacks](#eventos-y-callbacks)
6. [Configuración de Columnas (columnDefs)](#configuración-de-columnas-columndefs)
7. [Configuración DOM y Layout](#configuración-dom-y-layout)
8. [Internacionalización y Idiomas](#internacionalización-y-idiomas)
9. [Filtrado y Búsqueda Avanzada](#filtrado-y-búsqueda-avanzada)
10. [Extensiones y Plug-ins](#extensiones-y-plug-ins)
11. [Optimización y Rendimiento](#optimización-y-rendimiento)
12. [Ejemplos Prácticos](#ejemplos-prácticos)
13. [Integración con Bootstrap](#integración-con-bootstrap)
14. [Solución de Problemas Comunes](#solución-de-problemas-comunes)

---

## 1. Introducción y Conceptos Básicos

### ¿Qué es DataTables?

DataTables es un potente plug-in para jQuery que convierte tablas HTML normales en tablas interactivas con funcionalidades avanzadas como:

- **Ordenamiento** de columnas
- **Filtrado y búsqueda** global y por columnas
- **Paginación** automática
- **Información** de registros mostrados
- **Scrolling** horizontal y vertical
- **Responsive design**
- **Exportación** de datos
- **API completa** para manipulación programática

### Inicialización Básica

```javascript
// Configuración mínima (zero configuration)
$('#example').DataTable();

// Con opciones personalizadas
$('#example').DataTable({
    paginate: false,
    scrollY: 300,
    language: {
        url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
    }
});
```

---

## 2. Configuración e Inicialización

### Métodos de Inicialización

```javascript
// Método 1: Configuración directa
$(document).ready(function() {
    $('#example').DataTable({
        // opciones aquí
    });
});

// Método 2: Almacenar referencia para API
$(document).ready(function() {
    var table = $('#example').DataTable({
        // opciones aquí
    });
    
    // Usar API posteriormente
    table.row.add(['datos']).draw();
});

// Método 3: Configuración con múltiples instancias
$(document).ready(function() {
    $('#table1').DataTable({ /* opciones */ });
    $('#table2').DataTable({ /* opciones */ });
});
```

### Establecer Valores por Defecto

```javascript
// Configurar opciones por defecto para todas las instancias
$.extend(true, $.fn.dataTable.defaults, {
    "searching": false,
    "ordering": false,
    "responsive": true,
    "language": {
        "url": "path/to/spanish.json"
    }
});

// Después de esto, todas las instancias usarán estas opciones
$('#table1').DataTable(); // Heredará las opciones por defecto
```

---

## 3. Opciones de Configuración Principal

### Funcionalidades Básicas

```javascript
$('#example').DataTable({
    // Habilitar/deshabilitar funcionalidades
    "paging": true,        // Paginación
    "searching": true,     // Búsqueda global
    "ordering": true,      // Ordenamiento de columnas
    "info": true,          // Información de registros
    "autoWidth": true,     // Ancho automático
    "responsive": true,    // Diseño responsive
    
    // Configuración de paginación
    "pageLength": 25,      // Registros por página
    "lengthMenu": [        // Opciones del menú de longitud
        [10, 25, 50, 100, -1],
        [10, 25, 50, 100, "Todos"]
    ],
    "pagingType": "full_numbers", // Tipo de paginación
    
    // Configuración de scrolling
    "scrollY": "400px",    // Altura de scroll vertical
    "scrollX": true,       // Scroll horizontal
    "scrollCollapse": true, // Colapsar si hay menos datos
    
    // Estado de la tabla
    "stateSave": true,     // Guardar estado en localStorage
    "stateDuration": 60 * 60 * 24, // Duración del estado (segundos)
    
    // Procesamiento
    "processing": false,   // Mostrar indicador de procesamiento
    "deferRender": true,   // Renderizado diferido para performance
});
```

### Configuración de Ordenamiento

```javascript
$('#example').DataTable({
    // Ordenamiento inicial
    "order": [[1, "asc"]],  // Ordenar por columna 1 ascendente
    
    // Ordenamiento múltiple
    "order": [
        [0, "asc"],
        [1, "desc"]
    ],
    
    // Secuencia de ordenamiento
    "orderSequence": ["asc", "desc"], // Solo ascendente y descendente
    
    // Ordenamiento fijo
    "orderFixed": [[0, 'asc']], // Siempre ordenar por columna 0 primero
    
    // Deshabilitar ordenamiento globalmente
    "ordering": false,
    
    // Control de ordenamiento multi-columna
    "orderMulti": true,  // Permitir ordenamiento múltiple
});
```

---

## 4. API de DataTables

### Acceso a la API

```javascript
// Obtener instancia de API
var table = $('#example').DataTable();
var api = table.api(); // Método alternativo

// Usar API directamente
$('#example').DataTable().row.add(['datos']).draw();
```

### Métodos de Manipulación de Datos

```javascript
var table = $('#example').DataTable();

// === FILAS ===
// Agregar fila
table.row.add(['Col1', 'Col2', 'Col3']).draw();

// Eliminar fila
table.row(0).remove().draw();

// Obtener datos de fila
var data = table.row(0).data();

// Actualizar datos de fila
table.row(0).data(['Nuevo1', 'Nuevo2', 'Nuevo3']).draw();

// Seleccionar filas
var selectedRows = table.rows({ selected: true });

// Iterar sobre filas
table.rows().every(function(rowIdx, tableLoop, rowLoop) {
    var data = this.data();
    console.log('Fila:', data);
});

// === COLUMNAS ===
// Obtener datos de columna
var columnData = table.column(0).data();

// Buscar en columna específica
table.column(2).search('valor').draw();

// Mostrar/ocultar columna
table.column(3).visible(false);

// === CELDAS ===
// Obtener/modificar celda específica
var cellData = table.cell(0, 1).data();
table.cell(0, 1).data('Nuevo valor').draw();

// === BÚSQUEDA ===
// Búsqueda global
table.search('término').draw();

// Búsqueda con regex
table.search('patrón.*', true, false).draw();

// Limpiar búsquedas
table.search('').columns().search('').draw();

// === OTROS ===
// Redibujar tabla
table.draw();

// Limpiar tabla
table.clear().draw();

// Recargar datos (si usa Ajax)
table.ajax.reload();

// Destruir instancia
table.destroy();
```

### Métodos de Información

```javascript
var table = $('#example').DataTable();

// Información de página
var pageInfo = table.page.info();
/*
{
    page: 0,        // Página actual (base 0)
    pages: 5,       // Total de páginas
    start: 0,       // Índice del primer registro
    end: 9,         // Índice del último registro
    length: 10,     // Registros por página
    recordsTotal: 50,    // Total de registros
    recordsDisplay: 50   // Registros después de filtrar
}
*/

// Navegar páginas
table.page('first').draw('page');
table.page('last').draw('page');
table.page('next').draw('page');
table.page('previous').draw('page');
table.page(2).draw('page'); // Ir a página específica

// Información de ordenamiento
var orderInfo = table.order();

// Configuración actual
var settings = table.settings();
```

---

## 5. Eventos y Callbacks

### Eventos de DataTables

```javascript
var table = $('#example').DataTable();

// Eventos principales
table.on('draw.dt', function() {
    console.log('Tabla redibujada');
});

table.on('order.dt', function() {
    console.log('Ordenamiento cambiado');
});

table.on('search.dt', function() {
    console.log('Búsqueda aplicada');
});

table.on('page.dt', function() {
    console.log('Página cambiada');
});

table.on('length.dt', function() {
    console.log('Longitud de página cambiada');
});

// Eventos de filas
table.on('select.dt', function(e, dt, type, indexes) {
    console.log('Filas seleccionadas:', indexes);
});

table.on('deselect.dt', function(e, dt, type, indexes) {
    console.log('Filas deseleccionadas:', indexes);
});

// Eventos personalizados en elementos
$('#example tbody').on('click', 'tr', function() {
    var data = table.row(this).data();
    console.log('Fila clickeada:', data);
});

$('#example tbody').on('click', 'button', function() {
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    console.log('Botón clickeado en fila:', row.data());
});
```

### Callbacks de Inicialización

```javascript
$('#example').DataTable({
    // Callback al completar inicialización
    "initComplete": function(settings, json) {
        var api = this.api();
        console.log('DataTable inicializado');
        
        // Ejemplo: agregar filtros por columna
        api.columns().every(function() {
            var column = this;
            var select = $('<select><option value=""></option></select>')
                .appendTo($(column.footer()).empty())
                .on('change', function() {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                    column.search(val ? '^' + val + '$' : '', true, false).draw();
                });
            
            column.data().unique().sort().each(function(d) {
                select.append('<option value="' + d + '">' + d + '</option>');
            });
        });
    },
    
    // Callback para cada fila creada
    "createdRow": function(row, data, dataIndex) {
        // Agregar clases CSS condicionales
        if (data[3] == "Activo") {
            $(row).addClass('table-success');
        } else if (data[3] == "Inactivo") {
            $(row).addClass('table-warning');
        }
        
        // Agregar atributos de datos
        $(row).attr('data-id', data[0]);
    },
    
    // Callback para footer
    "footerCallback": function(row, data, start, end, display) {
        var api = this.api();
        
        // Función para convertir a número
        var intVal = function(i) {
            return typeof i === 'string' ?
                i.replace(/[\$,]/g, '') * 1 :
                typeof i === 'number' ?
                    i : 0;
        };
        
        // Total de todas las páginas
        var total = api
            .column(4)
            .data()
            .reduce(function(a, b) {
                return intVal(a) + intVal(b);
            }, 0);
        
        // Total de página actual
        var pageTotal = api
            .column(4, { page: 'current' })
            .data()
            .reduce(function(a, b) {
                return intVal(a) + intVal(b);
            }, 0);
        
        // Actualizar footer
        $(api.column(4).footer()).html(
            '$' + pageTotal + ' (página) - $' + total + ' (total)'
        );
    },
    
    // Callback antes de cada dibujado
    "preDrawCallback": function(settings) {
        console.log('Antes de dibujar');
    },
    
    // Callback después de cada dibujado
    "drawCallback": function(settings) {
        console.log('Después de dibujar');
        
        // Re-inicializar tooltips u otros componentes
        $('[data-toggle="tooltip"]').tooltip();
    }
});
```

---

## 6. Configuración de Columnas (columnDefs)

### Configuración Básica de Columnas

```javascript
$('#example').DataTable({
    "columnDefs": [
        // Por índice de columna
        {
            "targets": 0,           // Primera columna
            "width": "10%",
            "className": "text-center",
            "orderable": false
        },
        
        // Múltiples columnas
        {
            "targets": [1, 2, 3],   // Columnas 1, 2 y 3
            "searchable": false
        },
        
        // Por clase CSS
        {
            "targets": "no-sort",   // Columnas con clase 'no-sort'
            "orderable": false
        },
        
        // Última columna
        {
            "targets": -1,          // -1 = última columna
            "orderable": false,
            "searchable": false,
            "width": "100px"
        }
    ]
});
```

### Renderizado Personalizado de Columnas

```javascript
$('#example').DataTable({
    "columnDefs": [
        // Columna con botones
        {
            "targets": -1,
            "data": null,
            "defaultContent": '<button class="btn btn-sm btn-primary edit">Editar</button> <button class="btn btn-sm btn-danger delete">Eliminar</button>'
        },
        
        // Formateo de fecha
        {
            "targets": 4,
            "render": function(data, type, row) {
                if (type === 'display' || type === 'type') {
                    return moment(data).format('DD/MM/YYYY');
                }
                return data;
            }
        },
        
        // Formateo de moneda
        {
            "targets": 5,
            "render": function(data, type, row) {
                if (type === 'display') {
                    return '$' + parseFloat(data).toLocaleString('es-ES', {
                        minimumFractionDigits: 2
                    });
                }
                return data;
            }
        },
        
        // Enlaces dinámicos
        {
            "targets": 1,
            "render": function(data, type, row) {
                if (type === 'display') {
                    return '<a href="/usuario/' + row[0] + '">' + data + '</a>';
                }
                return data;
            }
        },
        
        // Badges de estado
        {
            "targets": 3,
            "render": function(data, type, row) {
                if (type === 'display') {
                    var badgeClass = data === 'Activo' ? 'badge-success' : 'badge-secondary';
                    return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                }
                return data;
            }
        },
        
        // Imágenes
        {
            "targets": 2,
            "render": function(data, type, row) {
                if (type === 'display') {
                    return '<img src="' + data + '" class="img-thumbnail" width="50">';
                }
                return data;
            }
        },
        
        // Barras de progreso
        {
            "targets": 6,
            "render": function(data, type, row) {
                if (type === 'display') {
                    var percentage = Math.round(data);
                    var progressClass = percentage >= 70 ? 'bg-success' : 
                                       percentage >= 40 ? 'bg-warning' : 'bg-danger';
                    return '<div class="progress" style="height: 20px;">' +
                           '<div class="progress-bar ' + progressClass + '" role="progressbar" ' +
                           'style="width: ' + percentage + '%" aria-valuenow="' + percentage + '" ' +
                           'aria-valuemin="0" aria-valuemax="100">' + percentage + '%</div></div>';
                }
                return data;
            }
        }
    ]
});
```

### Tipos de Datos y Ordenamiento

```javascript
$('#example').DataTable({
    "columnDefs": [
        // Tipo de datos específico
        {
            "targets": 3,
            "type": "date"      // date, num, string, html
        },
        
        // Ordenamiento personalizado
        {
            "targets": 4,
            "type": "num",
            "render": function(data, type, row) {
                if (type === 'display') {
                    return data + '%';
                }
                return parseFloat(data);
            }
        },
        
        // Datos ortogonales (diferentes para display y sort)
        {
            "targets": 2,
            "render": {
                "_": "display",     // Para mostrar
                "sort": "sort",     // Para ordenar
                "type": "type"      // Para detección de tipo
            }
        }
    ]
});
```

---

## 7. Configuración DOM y Layout

### Opción DOM Básica

```javascript
// Elementos DOM estándar:
// l - Length changing input control (selector de longitud)
// f - Filtering input (campo de búsqueda)
// t - The table itself (la tabla)
// i - Information summary (información de registros)
// p - Pagination control (controles de paginación)
// r - Processing display element (indicador de procesamiento)

$('#example').DataTable({
    // Layout estándar
    "dom": 'lfrtip',
    
    // Layout con Bootstrap
    "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
           "<'row'<'col-sm-12'tr>>" +
           "<'row'<'col-sm-5'i><'col-sm-7'p>>",
    
    // Layout personalizado con botones
    "dom": "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
           "<'row'<'col-sm-12'tr>>" +
           "<'row'<'col-sm-5'i><'col-sm-7'p>>",
    
    // Elementos duplicados (paginación arriba y abajo)
    "dom": "<'top'iflp<'clear'>>" +
           "rt" +
           "<'bottom'iflp<'clear'>>"
});
```

### Layout Responsive con Bootstrap

```javascript
$('#example').DataTable({
    "dom": 
        // Fila superior: Length menu, botones, filtro
        "<'row mb-3'<'col-md-4'l><'col-md-4 text-center'B><'col-md-4'f>>" +
        
        // Fila de la tabla con scroll responsive
        "<'row'<'col-12'tr>>" +
        
        // Fila inferior: Info y paginación
        "<'row mt-3'<'col-md-6'i><'col-md-6'p>>",
    
    "buttons": [
        'copy', 'csv', 'excel', 'pdf', 'print'
    ],
    
    "responsive": true
});
```

### Elementos DOM Personalizados

```javascript
$('#example').DataTable({
    "dom": 
        "<'toolbar'>" +  // Elemento personalizado
        "<'row'<'col-6'l><'col-6'f>>" +
        "<'row'<'col-12'tr>>" +
        "<'row'<'col-6'i><'col-6'p>>",
    
    "initComplete": function() {
        // Agregar contenido personalizado al toolbar
        $("div.toolbar").html(
            '<div class="d-flex justify-content-between">' +
                '<h4>Lista de Estudiantes</h4>' +
                '<div>' +
                    '<button class="btn btn-success" id="addNew">Agregar Nuevo</button>' +
                    '<button class="btn btn-info" id="refresh">Actualizar</button>' +
                '</div>' +
            '</div>'
        );
    }
});
```

---

## 8. Internacionalización y Idiomas

### Configuración de Idioma Inline

```javascript
$('#example').DataTable({
    "language": {
        // Mensajes principales
        "lengthMenu": "Mostrar _MENU_ registros por página",
        "zeroRecords": "No se encontraron resultados",
        "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
        "infoEmpty": "Mostrando 0 a 0 de 0 registros",
        "infoFiltered": "(filtrado de _MAX_ registros totales)",
        "search": "Buscar:",
        "emptyTable": "No hay datos disponibles en la tabla",
        "processing": "Procesando...",
        
        // Paginación
        "paginate": {
            "first": "Primero",
            "last": "Último",
            "next": "Siguiente",
            "previous": "Anterior"
        },
        
        // Ordenamiento
        "aria": {
            "sortAscending": ": activar para ordenar la columna de manera ascendente",
            "sortDescending": ": activar para ordenar la columna de manera descendente"
        },
        
        // Números y decimales
        "decimal": ",",
        "thousands": ".",
        
        // Mensajes de estado
        "loadingRecords": "Cargando...",
        "searchPlaceholder": "Término de búsqueda",
        
        // Botones (si se usa extensión Buttons)
        "buttons": {
            "copy": "Copiar",
            "csv": "CSV",
            "excel": "Excel",
            "pdf": "PDF",
            "print": "Imprimir",
            "colvis": "Visibilidad de columnas"
        }
    }
});
```

### Carga de Archivos de Idioma

```javascript
// Desde CDN
$('#example').DataTable({
    "language": {
        "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
    }
});

// Desde archivo local
$('#example').DataTable({
    "language": {
        "url": "/assets/js/datatables/spanish.json"
    }
});

// Configuración mixta
$('#example').DataTable({
    "language": {
        "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json",
        // Sobrescribir mensajes específicos
        "search": "Filtrar:",
        "lengthMenu": "Ver _MENU_ elementos"
    }
});
```

### Archivo de Idioma Personalizado (spanish.json)

```json
{
    "sProcessing": "Procesando...",
    "sLengthMenu": "Mostrar _MENU_ registros",
    "sZeroRecords": "No se encontraron resultados",
    "sEmptyTable": "Ningún dato disponible en esta tabla",
    "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
    "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
    "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
    "sInfoPostFix": "",
    "sSearch": "Buscar:",
    "sUrl": "",
    "sInfoThousands": ",",
    "sLoadingRecords": "Cargando...",
    "oPaginate": {
        "sFirst": "Primero",
        "sLast": "Último",
        "sNext": "Siguiente",
        "sPrevious": "Anterior"
    },
    "oAria": {
        "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
    },
    "buttons": {
        "copy": "Copiar",
        "colvis": "Visibilidad"
    }
}
```

---

## 9. Filtrado y Búsqueda Avanzada

### Filtros Personalizados

```javascript
// Filtro personalizado global
$.fn.dataTable.ext.search.push(
    function(settings, data, dataIndex) {
        // Solo aplicar a tablas específicas
        if (settings.nTable.id !== 'example') {
            return true;
        }
        
        // Ejemplo: filtrar por rango de fechas
        var min = $('#min-date').val();
        var max = $('#max-date').val();
        var date = data[3]; // Columna de fecha
        
        if (
            (min === "" || date >= min) &&
            (max === "" || date <= max)
        ) {
            return true;
        }
        return false;
    }
);

// Filtro por rangos numéricos
$.fn.dataTable.ext.search.push(
    function(settings, data, dataIndex) {
        var min = parseInt($('#min-age').val(), 10);
        var max = parseInt($('#max-age').val(), 10);
        var age = parseFloat(data[4]) || 0; // Columna de edad
        
        if ((isNaN(min) && isNaN(max)) ||
            (isNaN(min) && age <= max) ||
            (min <= age && isNaN(max)) ||
            (min <= age && age <= max)) {
            return true;
        }
        return false;
    }
);

// Activar filtros
$('#min-date, #max-date, #min-age, #max-age').on('change', function() {
    $('#example').DataTable().draw();
});
```

### Filtros por Columna

```javascript
var table = $('#example').DataTable();

// Filtro en columna específica
$('#column-filter').on('change', function() {
    var value = this.value;
    table.column(2).search(value).draw();
});

// Filtros múltiples por columna
table.columns().every(function() {
    var column = this;
    var select = $('<select><option value=""></option></select>')
        .appendTo($(column.footer()).empty())
        .on('change', function() {
            var val = $.fn.dataTable.util.escapeRegex($(this).val());
            column.search(val ? '^' + val + '$' : '', true, false).draw();
        });
    
    // Obtener valores únicos de la columna
    column.data().unique().sort().each(function(d, j) {
        select.append('<option value="' + d + '">' + d + '</option>');
    });
});

// Búsqueda con regex
table.column(1).search('patrón.*', true, false).draw();

// Búsqueda exacta
table.column(2).search('^valor exacto$', true, false).draw();

// Limpiar filtros de todas las columnas
$('#clear-filters').on('click', function() {
    table.search('').columns().search('').draw();
});
```

### Búsqueda Inteligente

```javascript
$('#example').DataTable({
    // Configuración de búsqueda
    "search": {
        "caseInsensitive": true,    // Insensible a mayúsculas
        "regex": false,             // Permitir regex
        "smart": true,              // Búsqueda inteligente
        "return": false,            // Buscar al presionar Enter
        "placeholder": "Buscar estudiantes..."
    },
    
    // Configuración de búsqueda por columna
    "columnDefs": [
        {
            "targets": [0, 3],      // Columnas 0 y 3
            "searchable": false     // No incluir en búsqueda global
        }
    ]
});

// Búsqueda programática
var table = $('#example').DataTable();

// Búsqueda global
table.search('término').draw();

// Búsqueda con opciones
table.search('patrón', true, true, true).draw(); // regex, smart, caseInsensitive
```

---

## 10. Extensiones y Plug-ins

### Extensiones Oficiales Comunes

```javascript
// Buttons - Botones de exportación
$('#example').DataTable({
    "dom": 'Bfrtip',
    "buttons": [
        'copy',
        'csv',
        'excel',
        'pdf',
        'print',
        {
            extend: 'collection',
            text: 'Exportar',
            buttons: [
                'copy',
                'excel',
                'csv',
                'pdf',
                'print'
            ]
        }
    ]
});

// ColReorder - Reordenar columnas
$('#example').DataTable({
    "colReorder": true
});

// FixedColumns - Columnas fijas
$('#example').DataTable({
    "scrollX": true,
    "fixedColumns": {
        "leftColumns": 2,
        "rightColumns": 1
    }
});

// FixedHeader - Encabezado fijo
$('#example').DataTable({
    "fixedHeader": true
});

// Responsive - Diseño responsive
$('#example').DataTable({
    "responsive": true
});

// RowGroup - Agrupación de filas
$('#example').DataTable({
    "rowGroup": {
        "dataSrc": 2,       // Agrupar por columna 2
        "startRender": function(rows, group) {
            return 'Grupo: ' + group + ' (' + rows.count() + ' filas)';
        }
    }
});

// Select - Selección de filas
$('#example').DataTable({
    "select": {
        "style": "multi",       // single, multi, os, api
        "selector": 'td:first-child'
    }
});

// SearchPanes - Paneles de búsqueda
$('#example').DataTable({
    "dom": 'Pfrtip',
    "searchPanes": {
        "cascadePanes": true,
        "viewTotal": true
    }
});
```

### Crear Plug-ins Personalizados

```javascript
// API plug-in personalizado
$.fn.dataTable.Api.register('sum()', function() {
    return this.flatten().reduce(function(a, b) {
        if (typeof a === 'string') {
            a = a.replace(/[^\d]/g, '') * 1;
        }
        if (typeof b === 'string') {
            b = b.replace(/[^\d]/g, '') * 1;
        }
        return a + b;
    }, 0);
});

// Uso del plug-in
var table = $('#example').DataTable();
var total = table.column(4).data().sum();

// Plug-in de tipo de dato personalizado
$.fn.dataTable.ext.type.order['currency-pre'] = function(data) {
    return data == '-' ? 0 : parseFloat(data.replace(/[^\d\.-]/g, ''));
};

// Aplicar tipo personalizado
$('#example').DataTable({
    "columnDefs": [
        {
            "targets": 3,
            "type": "currency"
        }
    ]
});

// Plug-in de filtro personalizado
$.fn.dataTable.ext.search.push(
    function(settings, data, dataIndex, rowData, counter) {
        // Lógica de filtro personalizada
        return true; // o false para excluir
    }
);
```

---

## 11. Optimización y Rendimiento

### Configuración para Grandes Datasets

```javascript
$('#example').DataTable({
    // Renderizado diferido
    "deferRender": true,
    
    // Scrolling en lugar de paginación
    "scrollY": "400px",
    "scrollCollapse": true,
    "paging": false,
    
    // Deshabilitar funcionalidades innecesarias
    "searching": false,
    "ordering": false,
    "info": false,
    
    // Optimizar anchuras
    "autoWidth": false,
    "columnDefs": [
        { "width": "20%", "targets": 0 },
        { "width": "30%", "targets": 1 },
        { "width": "25%", "targets": 2 },
        { "width": "25%", "targets": 3 }
    ]
});
```

### Procesamiento del Lado del Servidor

```javascript
$('#example').DataTable({
    "processing": true,
    "serverSide": true,
    "ajax": {
        "url": "/api/students",
        "type": "POST",
        "data": function(d) {
            // Agregar parámetros personalizados
            d.custom_param = $('#custom-filter').val();
        }
    },
    "columns": [
        { "data": "id" },
        { "data": "name" },
        { "data": "group" },
        { "data": "status" }
    ],
    
    // Configuración de parámetros del servidor
    "searchDelay": 500,     // Delay en búsqueda
    "stateSave": true,      // Guardar estado
    
    // Manejo de errores
    "error": function(xhr, error, thrown) {
        console.error('Error en DataTable:', error);
    }
});

// Formato de respuesta del servidor requerido:
/*
{
    "draw": 1,
    "recordsTotal": 1000,
    "recordsFiltered": 250,
    "data": [
        ["1", "Juan Pérez", "Grupo A", "Activo"],
        ["2", "María López", "Grupo B", "Inactivo"]
    ]
}
*/
```

### Optimización de Memoria

```javascript
// Destruir instancias no utilizadas
var table = $('#example').DataTable();
// ... usar tabla
table.destroy(); // Liberar memoria

// Limpiar eventos personalizados
$('#example').off('.dt');

// Configuración eficiente para móviles
$('#example').DataTable({
    "responsive": true,
    "pageLength": 10,       // Menos registros por página
    "searching": true,
    "ordering": true,
    "lengthChange": false,  // Deshabilitar cambio de longitud
    "info": false          // Deshabilitar información de registros
});
```

---

## 12. Ejemplos Prácticos

### Ejemplo Completo: Tabla de Estudiantes

```javascript
$(document).ready(function() {
    var table = $('#studentsTable').DataTable({
        // Configuración básica
        "responsive": true,
        "pageLength": 25,
        "order": [[1, "asc"]],
        
        // Internacionalización
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "zeroRecords": "No se encontraron estudiantes",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ estudiantes",
            "infoEmpty": "Mostrando 0 a 0 de 0 estudiantes",
            "infoFiltered": "(filtrado de _MAX_ estudiantes totales)",
            "search": "Buscar:",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },
        
        // Layout DOM con Bootstrap
        "dom": "<'row mb-3'<'col-md-4'l><'col-md-4 text-center'><'col-md-4'f>>" +
               "<'row'<'col-12'tr>>" +
               "<'row mt-3'<'col-md-6'i><'col-md-6'p>>",
        
        // Configuración de columnas
        "columnDefs": [
            {
                "targets": 0,
                "width": "50px",
                "className": "text-center"
            },
            {
                "targets": 2,
                "render": function(data, type, row) {
                    var badgeClass = data === 'Grupo A' ? 'badge-primary' : 'badge-info';
                    return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                }
            },
            {
                "targets": 4,
                "render": function(data, type, row) {
                    var percentage = parseInt(data);
                    var progressClass = percentage >= 90 ? 'bg-success' : 
                                       percentage >= 70 ? 'bg-warning' : 'bg-danger';
                    return '<div class="progress">' +
                           '<div class="progress-bar ' + progressClass + '" ' +
                           'style="width: ' + percentage + '%">' + percentage + '%</div></div>';
                }
            },
            {
                "targets": 5,
                "render": function(data, type, row) {
                    var badgeClass = data === 'ACTIVO' ? 'badge-success' : 'badge-secondary';
                    return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                }
            },
            {
                "targets": -1,
                "orderable": false,
                "searchable": false,
                "render": function(data, type, row) {
                    return '<div class="btn-group btn-group-sm">' +
                           '<button class="btn btn-outline-primary edit-btn" data-id="' + row[0] + '">' +
                           '<i class="fe fe-edit"></i></button>' +
                           '<button class="btn btn-outline-info view-btn" data-id="' + row[0] + '">' +
                           '<i class="fe fe-eye"></i></button>' +
                           '</div>';
                }
            }
        ],
        
        // Callbacks
        "initComplete": function() {
            console.log('Tabla de estudiantes inicializada');
            
            // Agregar filtros personalizados
            this.api().columns([2, 5]).every(function() {
                var column = this;
                var select = $('<select class="form-control form-control-sm"><option value=""></option></select>')
                    .appendTo($('.custom-filters'))
                    .on('change', function() {
                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                        column.search(val ? '^' + val + '$' : '', true, false).draw();
                    });
                
                column.data().unique().sort().each(function(d) {
                    select.append('<option value="' + d + '">' + d + '</option>');
                });
            });
        },
        
        "drawCallback": function() {
            // Re-inicializar tooltips
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
    
    // Eventos personalizados
    $('#studentsTable tbody').on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        var rowData = table.row($(this).closest('tr')).data();
        console.log('Editar estudiante:', id, rowData);
        // Lógica de edición
    });
    
    $('#studentsTable tbody').on('click', '.view-btn', function() {
        var id = $(this).data('id');
        var rowData = table.row($(this).closest('tr')).data();
        console.log('Ver estudiante:', id, rowData);
        // Lógica de visualización
    });
    
    // Filtros personalizados
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'studentsTable') {
                return true;
            }
            
            var attendanceFilter = $('#attendanceFilter').val();
            if (attendanceFilter === '') {
                return true;
            }
            
            var percentage = parseInt(data[4]);
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
    
    $('#attendanceFilter').on('change', function() {
        table.draw();
    });
});
```

### HTML correspondiente

```html
<div class="card">
    <div class="card-header">
        <h4>Lista de Estudiantes</h4>
        <div class="custom-filters row">
            <div class="col-md-3">
                <label>Filtrar por Asistencia:</label>
                <select id="attendanceFilter" class="form-control form-control-sm">
                    <option value="">Todos</option>
                    <option value="high">Alta (≥90%)</option>
                    <option value="medium">Media (70-89%)</option>
                    <option value="low">Baja (<70%)</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table id="studentsTable" class="table table-hover">
            <thead>
                <tr>
                    <th>Orden</th>
                    <th>Nombre Completo</th>
                    <th>Grupo</th>
                    <th>Código</th>
                    <th>Asistencia</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Los datos se cargan aquí -->
            </tbody>
        </table>
    </div>
</div>
```

---

## 13. Integración con Bootstrap

### Configuración para Bootstrap 4/5

```javascript
$('#example').DataTable({
    // DOM optimizado para Bootstrap
    "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
           "<'row'<'col-sm-12'tr>>" +
           "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    
    // Clases CSS de Bootstrap
    "responsive": true,
    
    // Configuración específica para Bootstrap
    "pageLength": 25,
    "lengthMenu": [
        [10, 25, 50, 100, -1],
        [10, 25, 50, 100, "Todos"]
    ],
    
    // Integración con componentes Bootstrap
    "initComplete": function() {
        // Aplicar clases Bootstrap a elementos de DataTables
        $('.dataTables_length select').addClass('form-control form-control-sm');
        $('.dataTables_filter input').addClass('form-control form-control-sm');
        
        // Personalizar paginación
        $('.pagination').addClass('pagination-sm');
    }
});
```

### CSS Personalizado para Bootstrap

```css
/* Mejorar la integración visual */
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    margin-bottom: 1rem;
}

.dataTables_wrapper .dataTables_filter input {
    margin-left: 0.5rem;
}

.dataTables_wrapper .dataTables_length select {
    margin: 0 0.5rem;
}

/* Responsive table */
.table-responsive .dataTables_wrapper {
    overflow-x: visible;
}

/* Mejorar la apariencia de la paginación */
.dataTables_wrapper .dataTables_paginate .pagination {
    justify-content: flex-end;
}

/* Estilo para filtros personalizados */
.custom-filters {
    margin-bottom: 1rem;
    padding: 1rem;
    background-color: #f8f9fa;
    border-radius: 0.375rem;
}

.custom-filters label {
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: #495057;
}
```

---

## 14. Solución de Problemas Comunes

### Problemas de Renderizado

```javascript
// Problema: Tabla no se muestra correctamente en tabs
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
    $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
});

// Problema: Anchos de columna incorrectos
$('#example').DataTable({
    "autoWidth": false,
    "columnDefs": [
        { "width": "20%", "targets": 0 },
        { "width": "30%", "targets": 1 }
    ]
});

// Forzar recálculo de anchos
table.columns.adjust().draw();

// Problema: Tabla en modals
$('#myModal').on('shown.bs.modal', function() {
    $('#example').DataTable().columns.adjust();
});
```

### Problemas de Datos

```javascript
// Problema: Datos no se actualizan
// Solución: Usar clear() y rows.add()
table.clear();
table.rows.add(newData);
table.draw();

// Problema: Ordenamiento incorrecto
// Solución: Especificar tipo de datos
$('#example').DataTable({
    "columnDefs": [
        {
            "targets": 3,
            "type": "date"
        }
    ]
});

// Problema: Búsqueda no funciona en datos generados
// Solución: Invalidar caché después de cambios
table.rows().invalidate().draw();
```

### Problemas de Performance

```javascript
// Problema: Lentitud con muchos datos
// Solución: Usar server-side processing
$('#example').DataTable({
    "processing": true,
    "serverSide": true,
    "ajax": "/api/data"
});

// Problema: Lentitud en redibujado
// Solución: Usar draw(false) para no resetear paginación
table.row.add(data).draw(false);

// Problema: Memoria alta
// Solución: Destruir instancias no utilizadas
if ($.fn.DataTable.isDataTable('#example')) {
    $('#example').DataTable().destroy();
}
```

### Debugging y Troubleshooting

```javascript
// Habilitar modo debug
$.fn.dataTable.ext.errMode = 'throw';

// Verificar estado de la tabla
console.log('¿Es DataTable?', $.fn.DataTable.isDataTable('#example'));
console.log('Configuración:', table.settings());
console.log('Datos actuales:', table.data().toArray());

// Eventos de debug
$('#example').on('error.dt', function(e, settings, techNote, message) {
    console.error('Error en DataTable:', message);
});

// Información de API
var api = table;
console.log('Página actual:', api.page.info());
console.log('Búsqueda actual:', api.search());
console.log('Orden actual:', api.order());
```

---

## 🔧 Herramientas y Recursos Adicionales

### Links Útiles

- **Documentación Oficial:** https://datatables.net/
- **Generador de CDN:** https://datatables.net/download/
- **Ejemplos:** https://datatables.net/examples/
- **Plug-ins:** https://datatables.net/plug-ins/
- **Foro de Soporte:** https://datatables.net/forums/

### Extensiones Recomendadas

1. **Buttons** - Exportación de datos
2. **Responsive** - Diseño responsive
3. **FixedHeader** - Encabezado fijo
4. **Select** - Selección de filas
5. **ColReorder** - Reordenar columnas
6. **RowGroup** - Agrupación de filas

### CDN Recomendado

```html
<!-- CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css"/>

<!-- JavaScript -->
<script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
```

---

## 📝 Notas Finales

Esta documentación cubre las principales características y opciones de configuración de DataTables. Para implementaciones específicas en el Sistema de Asistencias, considerar:

1. **Usar configuración inline para traducciones** (evitar dependencias CDN)
2. **Optimizar para dispositivos móviles** (tablets para registro de asistencia)
3. **Implementar filtros dinámicos** basados en datos de la base de datos
4. **Mantener consistencia** con el diseño TinyDash Bootstrap
5. **Documentar configuraciones específicas** del proyecto

Para casos de uso específicos o problemas no cubiertos, consultar la documentación oficial o el foro de DataTables.

---

**Última actualización:** 19 de Agosto de 2025  
**Versión del documento:** 1.0  
**Compatible con:** DataTables 1.13.x, Bootstrap 4/5, jQuery 3.x