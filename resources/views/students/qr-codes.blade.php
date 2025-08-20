@extends('layouts.app')

@section('title', 'Códigos QR - Sistema de Asistencias')

@section('page-title', 'Códigos QR de Estudiantes')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Estudiantes</a></li>
    <li class="breadcrumb-item active">Códigos QR</li>
@endsection

@section('additional-css')
<style>
    /* Minimal QR styles only — pagination uses template classes (TinyDash/Bootstrap) */
    .qr-code-display {
    min-height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    position: relative;
}

.qr-canvas {
    max-width: 100%;
    height: auto;
    border-radius: 4px;
}

.qr-loading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #6c757d;
}

.qr-error {
    color: #dc3545;
    font-size: 0.875rem;
    text-align: center;
    padding: 1rem;
}

.qr-card {
    transition: transform 0.2s ease-in-out;
}

.qr-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

@media print {
    .no-print { display: none !important; }
    .qr-card { break-inside: avoid; }
    .qr-canvas { max-width: 150px !important; }
}

    /* No más reglas globales de paginación aquí: usar las clases del template TinyDash/Bootstrap */
    
    /* Ensure pagination renders in one line on desktop (Tailwind 'sm' breakpoint ~640px) */
    @media (min-width: 640px) {
        nav[aria-label="Pagination Navigation"] {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            flex-wrap: nowrap !important;
            gap: 12px !important;
            white-space: nowrap !important;
        }

        /* Make each direct child render inline and avoid wrapping */
        nav[aria-label="Pagination Navigation"] > * {
            display: inline-flex !important;
            align-items: center !important;
            flex-wrap: nowrap !important;
            gap: 8px !important;
            vertical-align: middle !important;
        }

        /* The typical Laravel paginator puts page buttons inside a relative.inline-flex group */
        nav[aria-label="Pagination Navigation"] .relative.inline-flex,
        nav[aria-label="Pagination Navigation"] .relative.z-0.inline-flex {
            display: inline-flex !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
        }

        /* Reduce padding slightly on page buttons to help fit one line */
        nav[aria-label="Pagination Navigation"] a.relative.inline-flex.items-center,
        nav[aria-label="Pagination Navigation"] span.relative.inline-flex.items-center,
        nav[aria-label="Pagination Navigation"] .page-link {
            padding-left: 6px !important;
            padding-right: 6px !important;
        }

        /* Avoid page number list expanding to full width */
        nav[aria-label="Pagination Navigation"] ul,
        nav[aria-label="Pagination Navigation"] .pagination {
            display: inline-flex !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
        }

        /* Prevent elements with classes like `sm:flex-1` from growing to full width inside the pagination */
        nav[aria-label="Pagination Navigation"] [class*="sm:flex-1"],
        nav[aria-label="Pagination Navigation"] [class*="sm:flex\-1"] {
            flex: 0 0 auto !important;
            max-width: none !important;
        }
    }
</style>
@endsection

@section('content')
<!-- Stats de QR -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <span class="h2 mb-0">{{ $qrStats->total_codes }}</span>
                        <p class="small text-muted mb-0">Códigos QR Total</p>
                    </div>
                    <div class="col-auto">
                        <span class="fe fe-qr-code fe-32 text-primary"></span>
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
                        <span class="h2 mb-0">{{ $qrStats->active_codes }}</span>
                        <p class="small text-muted mb-0">Códigos Activos</p>
                    </div>
                    <div class="col-auto">
                        <span class="fe fe-check-circle fe-32 text-success"></span>
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
                        <span class="h2 mb-0">{{ $qrStats->total_scans_today }}</span>
                        <p class="small text-muted mb-0">Escaneos Hoy</p>
                    </div>
                    <div class="col-auto">
                        <span class="fe fe-scan fe-32 text-warning"></span>
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
                        <span class="h6 mb-0">{{ \Carbon\Carbon::parse($qrStats->last_generated)->format('d/m/Y') }}</span>
                        <p class="small text-muted mb-0">Última Generación</p>
                    </div>
                    <div class="col-auto">
                        <span class="fe fe-calendar fe-32 text-info"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros y Acciones -->
<div class="row mb-3">
    <div class="col-md-4">
        <div class="form-group">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Buscar código QR...">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button">
                        <span class="fe fe-search"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <select class="form-control">
            <option value="">Todos los grupos</option>
            @php
                $groups = App\Models\Group::orderBy('name')->get();
            @endphp
            @foreach($groups as $group)
                <option value="{{ $group->id }}">{{ $group->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 text-right">
        <button class="btn btn-success" type="button" onclick="regenerateAllQR()">
            <span class="fe fe-refresh-cw fe-12 mr-2"></span>Regenerar Todos
        </button>
        <button class="btn btn-primary" type="button" onclick="downloadAllQR()">
            <span class="fe fe-download fe-12 mr-2"></span>Descargar Todos
        </button>
        <button class="btn btn-outline-primary" type="button" onclick="window.print()">
            <span class="fe fe-printer fe-12 mr-2"></span>Imprimir
        </button>
    </div>
</div>

<!-- Modo de Vista removido: solo se muestra la cuadrícula -->

<!-- Vista en Cuadrícula (por defecto) -->
<div id="grid-view">
    <div class="row">
        @foreach($qrCodes as $qrCode)
        <div class="col-md-4 col-lg-3 mb-4">
            <div class="card qr-card">
                <div class="card-header text-center">
                    <h6 class="mb-0">{{ $qrCode->full_name }}</h6>
                    <small class="text-muted">{{ $qrCode->group_name }}</small>
                </div>
                <div class="card-body text-center">
                    <!-- QR Code Real -->
                    <div class="qr-code-display mb-3" id="qr-container-{{ $qrCode->id }}">
                        <div class="qr-loading" id="qr-loading-{{ $qrCode->id }}">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="sr-only">Generando QR...</span>
                            </div>
                            <div class="mt-2">Generando QR...</div>
                        </div>
                        <canvas id="qr-canvas-{{ $qrCode->id }}" class="qr-canvas" style="display: none;"></canvas>
                        <div class="qr-error" id="qr-error-{{ $qrCode->id }}" style="display: none;">
                            <i class="fe fe-alert-circle"></i>
                            <div>Error al generar QR</div>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <code class="small">{{ $qrCode->qr_code }}</code>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <small class="text-muted">Escaneos</small>
                            <div class="font-weight-bold">{{ $qrCode->total_scans }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Último</small>
                            <div class="small">{{ \Carbon\Carbon::parse($qrCode->last_scanned)->format('d/m') }}</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center no-print">
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-outline-primary" title="Descargar PNG" 
                                onclick="downloadQR({{ $qrCode->id }}, '{{ $qrCode->full_name }}')">
                            <span class="fe fe-download fe-12"></span>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" title="Regenerar QR" 
                                onclick="regenerateQR({{ $qrCode->id }}, '{{ $qrCode->full_name }}')">
                            <span class="fe fe-refresh-cw fe-12"></span>
                        </button>
                        <button class="btn btn-sm btn-outline-info" title="Imprimir Individual" 
                                onclick="printQR({{ $qrCode->id }}, '{{ $qrCode->full_name }}')">
                            <span class="fe fe-printer fe-12"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <!-- Paginación (usando markup del template TinyDash/Bootstrap) -->
    <div class="row">
        <div class="col-12 d-flex align-items-center">
            <div class="mr-auto small text-muted">
                Mostrando {{ $qrCodes->firstItem() }} a {{ $qrCodes->lastItem() }} de {{ $qrCodes->total() }} registros
            </div>

            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-end mb-0">
                    {{-- Previous --}}
                    <li class="page-item {{ $qrCodes->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $qrCodes->previousPageUrl() ?: '#' }}">&laquo; Anterior</a>
                    </li>

                    {{-- Page links (simple window) --}}
                    @php
                        $start = max(1, $qrCodes->currentPage() - 2);
                        $end = min($qrCodes->lastPage(), $qrCodes->currentPage() + 2);
                    @endphp
                    @for ($i = $start; $i <= $end; $i++)
                        <li class="page-item {{ $qrCodes->currentPage() === $i ? 'active' : '' }}">
                            <a class="page-link" href="{{ $qrCodes->url($i) }}">{{ $i }}</a>
                        </li>
                    @endfor

                    {{-- Next --}}
                    <li class="page-item {{ $qrCodes->hasMorePages() ? '' : 'disabled' }}">
                        <a class="page-link" href="{{ $qrCodes->nextPageUrl() ?: '#' }}">Siguiente &raquo;</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Lista eliminada: sólo queda vista en cuadrícula -->

<!-- Instrucciones de Uso -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card bg-light">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="fe fe-info text-primary mr-2"></i>
                    Instrucciones de Uso de Códigos QR
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="small font-weight-bold">Para Catequistas:</h6>
                        <ul class="small text-muted mb-0">
                            <li>Cada estudiante tiene un código QR único</li>
                            <li>Use la cámara del dispositivo para escanear</li>
                            <li>El código se registra automáticamente en la sesión activa</li>
                            <li>Verifique el nombre del estudiante tras el escaneo</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="small font-weight-bold">Para Administradores:</h6>
                        <ul class="small text-muted mb-0">
                            <li>Los códigos se generan automáticamente</li>
                            <li>Pueden regenerarse si es necesario</li>
                            <li>Imprima códigos para estudiantes que olviden el suyo</li>
                            <li>Monitoree el historial de escaneos</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('additional-js')
<!-- QRCode.js Library from CDNJS (más confiable) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js"></script>

<script>
// Control de paginación
const QR_PER_PAGE = 12; // Mostrar 12 QR por página
let currentPage = 1;
let totalStudents = {{ $qrCodes->total() }};
let totalPages = Math.ceil(totalStudents / QR_PER_PAGE);

// Configuración QR Code
const QR_CONFIG = {
    errorCorrectionLevel: 'M', // Cambiar a M para mejor rendimiento
    type: 'image/png',
    quality: 0.92,
    margin: 1, // Reducir margin
    scale: 4,  // Reducir scale para mejor rendimiento
    color: {
        dark: '#000000',
        light: '#FFFFFF'
    }
};

// Generar QR Code individual con fallback si QRCode no está disponible
function generateQRCode(studentId, qrCodeText, delay = 0) {
    setTimeout(() => {
        const canvas = document.getElementById(`qr-canvas-${studentId}`);
        const loading = document.getElementById(`qr-loading-${studentId}`);
        const error = document.getElementById(`qr-error-${studentId}`);
        
        if (!canvas || !qrCodeText) {
            showQRError(studentId);
            return;
        }
        
        // Verificar qué biblioteca está disponible
        if (typeof QRCode !== 'undefined') {
            // Usar QRCode.js (node-qrcode style)
            QRCode.toCanvas(canvas, qrCodeText, QR_CONFIG, function (err) {
                if (err) {
                    console.error('Error generando QR para estudiante', studentId, ':', err);
                    showQRError(studentId);
                } else {
                    showQRSuccess(studentId);
                }
            });
        } else if (typeof qrcode !== 'undefined') {
            // Usar qrcode-generator style
            try {
                const qr = qrcode(4, 'M');
                qr.addData(qrCodeText);
                qr.make();
                
                // Dibujar en canvas
                const ctx = canvas.getContext('2d');
                const size = 200;
                canvas.width = size;
                canvas.height = size;
                
                const cellSize = size / qr.getModuleCount();
                ctx.fillStyle = '#FFFFFF';
                ctx.fillRect(0, 0, size, size);
                ctx.fillStyle = '#000000';
                
                for (let row = 0; row < qr.getModuleCount(); row++) {
                    for (let col = 0; col < qr.getModuleCount(); col++) {
                        if (qr.isDark(row, col)) {
                            ctx.fillRect(col * cellSize, row * cellSize, cellSize, cellSize);
                        }
                    }
                }
                
                showQRSuccess(studentId);
            } catch (err) {
                console.error('Error con qrcode-generator:', err);
                showQRError(studentId);
            }
        } else {
            // Fallback - mostrar texto
            showQRFallback(studentId, qrCodeText);
        }
    }, delay);
}

// Mostrar éxito en generación QR
function showQRSuccess(studentId) {
    const loading = document.getElementById(`qr-loading-${studentId}`);
    const canvas = document.getElementById(`qr-canvas-${studentId}`);
    const error = document.getElementById(`qr-error-${studentId}`);
    
    if (loading) loading.style.display = 'none';
    if (canvas) canvas.style.display = 'block';
    if (error) error.style.display = 'none';
    
    console.log(`QR generado exitosamente para estudiante ${studentId}`);
}

// Fallback si no hay biblioteca disponible
function showQRFallback(studentId, qrCodeText) {
    const loading = document.getElementById(`qr-loading-${studentId}`);
    const canvas = document.getElementById(`qr-canvas-${studentId}`);
    const error = document.getElementById(`qr-error-${studentId}`);
    
    if (loading) loading.style.display = 'none';
    if (canvas) canvas.style.display = 'none';
    if (error) {
        error.style.display = 'block';
        error.innerHTML = `
            <div style="text-align: center; padding: 20px; background: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 8px;">
                <div style="font-size: 14px; font-weight: bold; margin-bottom: 10px;">${qrCodeText}</div>
                <small style="color: #6c757d;">Código QR (modo texto)</small>
            </div>
        `;
    }
}

// Mostrar error en generación QR
function showQRError(studentId) {
    const loading = document.getElementById(`qr-loading-${studentId}`);
    const canvas = document.getElementById(`qr-canvas-${studentId}`);
    const error = document.getElementById(`qr-error-${studentId}`);
    
    if (loading) loading.style.display = 'none';
    if (canvas) canvas.style.display = 'none';
    if (error) error.style.display = 'block';
}

// Descargar QR como PNG
function downloadQR(studentId, studentName) {
    const canvas = document.getElementById(`qr-canvas-${studentId}`);
    if (!canvas) return;
    
    // Crear enlace de descarga
    const link = document.createElement('a');
    link.download = `QR_${studentName.replace(/\s+/g, '_')}.png`;
    link.href = canvas.toDataURL();
    link.click();
}

// Regenerar QR (placeholder para funcionalidad futura)
function regenerateQR(studentId, studentName) {
    if (confirm(`¿Está seguro de regenerar el código QR de ${studentName}?`)) {
        const loading = document.getElementById(`qr-loading-${studentId}`);
        const canvas = document.getElementById(`qr-canvas-${studentId}`);
        
        // Mostrar loading
        if (loading) loading.style.display = 'block';
        if (canvas) canvas.style.display = 'none';
        
        // Simular regeneración (aquí iría llamada AJAX)
        setTimeout(() => {
            alert(`QR de ${studentName} regenerado exitosamente`);
            // Recargar página o regenerar QR específico
            location.reload();
        }, 1500);
    }
}

// Imprimir QR individual
function printQR(studentId, studentName) {
    const canvas = document.getElementById(`qr-canvas-${studentId}`);
    if (!canvas) return;
    
    const printWindow = window.open('', '_blank');
    const qrDataURL = canvas.toDataURL();
    
    printWindow.document.write(`
        <html>
            <head>
                <title>QR Code - ${studentName}</title>
                <style>
                    body { margin: 0; padding: 20px; text-align: center; font-family: Arial, sans-serif; }
                    .qr-print { max-width: 300px; margin: 0 auto; }
                    h2 { margin-top: 20px; color: #333; }
                    .student-info { margin-top: 10px; color: #666; }
                </style>
            </head>
            <body>
                <div class="qr-print">
                    <img src="${qrDataURL}" style="width: 100%;" />
                    <h2>${studentName}</h2>
                    <div class="student-info">Primera Comunión</div>
                </div>
            </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
}

// Descargar todos los QR como ZIP
function downloadAllQR() {
    const canvases = document.querySelectorAll('.qr-canvas[style*="display: block"], .qr-canvas:not([style*="display: none"])');
    if (canvases.length === 0) {
        alert('No hay códigos QR generados para descargar');
        return;
    }

    // Simulación de descarga masiva (requiere biblioteca ZIP.js)
    alert(`Preparando descarga de ${canvases.length} códigos QR...`);
    
    // TODO: Implementar descarga real con ZIP.js
    console.log('Descarga masiva de QR codes:', canvases.length);
}

// Regenerar todos los QR
function regenerateAllQR() {
    if (confirm('¿Está seguro de regenerar TODOS los códigos QR? Esta acción no se puede deshacer.')) {
        const loadings = document.querySelectorAll('[id^="qr-loading-"]');
        const canvases = document.querySelectorAll('[id^="qr-canvas-"]');
        
        // Mostrar loading en todos
        loadings.forEach(loading => loading.style.display = 'block');
        canvases.forEach(canvas => canvas.style.display = 'none');
        
        // Simular regeneración masiva
        setTimeout(() => {
            alert('Todos los códigos QR han sido regenerados exitosamente');
            location.reload();
        }, 2000);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar códigos QR progresivamente
    function initializeQRCodes() {
        console.log('Iniciando generación de códigos QR...');
        
        const studentData = [
            @foreach($qrCodes as $index => $qrCode)
                {id: {{ $qrCode->id }}, code: '{{ $qrCode->qr_code }}'},
            @endforeach
        ];
        
        studentData.forEach((student, index) => {
            const delay = index * 200; // 200ms entre cada generación
            generateQRCode(student.id, student.code, delay);
        });
    }
    
    // Esperar un poco para que se carguen las bibliotecas
    setTimeout(initializeQRCodes, 100);
    
    // Eliminado: Alternancia de vista (solo queda cuadrícula)
    
    // Funcionalidad de búsqueda
    const searchInput = document.querySelector('input[placeholder="Buscar código QR..."]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const qrCards = document.querySelectorAll('.qr-card');
            
            qrCards.forEach(card => {
                const studentName = card.querySelector('h6').textContent.toLowerCase();
                const qrCode = card.querySelector('code').textContent.toLowerCase();
                
                if (studentName.includes(searchTerm) || qrCode.includes(searchTerm)) {
                    card.parentElement.style.display = 'block';
                } else {
                    card.parentElement.style.display = 'none';
                }
            });
        });
    }
});
</script>
@endsection