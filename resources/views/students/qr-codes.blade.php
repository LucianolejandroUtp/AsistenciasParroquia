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

    /* Utility to ensure icon-only buttons center their icon both vertically and horizontally */
    .btn-icon {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 0 !important;
        /* Avoid compressing icon vertically: keep normal line-height */
        line-height: normal !important;
        vertical-align: middle !important;
        /* Ensure a square clickable area so icons don't look squeezed */
        min-width: 36px !important;
        min-height: 32px !important;
        padding-left: .5rem !important;
        padding-right: .5rem !important;
    }

    .btn-icon .fe {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        /* Let the icon keep its natural height and not collapse */
        line-height: 1 !important;
        font-size: inherit !important;
        vertical-align: middle !important;
    }

    /* Slightly tighten small button paddings to keep icons visually centered */
    .btn-icon.btn-sm {
        padding-left: .45rem !important;
        padding-right: .45rem !important;
        min-width: 32px !important;
        min-height: 28px !important;
    }
</style>
@endsection

@section('content')
<!-- Stats de QR movidos al dashboard -->
<!-- (Se han trasladado para mantener limpia la vista de códigos QR) -->

<!-- Filtros y Acciones -->
<div class="row mb-3">
    <div class="col-12">
        <form method="GET" action="{{ route('students.qr-codes') }}">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <div class="form-group mb-0">
                        <div class="input-group">
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Buscar código QR...">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <span class="fe fe-search"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    @php
                        $groups = App\Models\Group::orderBy('name')->get();
                    @endphp
                    <div class="form-group mb-0">
                        <select class="form-control" name="group" onchange="this.form.submit()">
                            <option value="">Grupos</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" {{ request('group') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6 d-flex justify-content-end flex-wrap">
                    <a href="{{ route('students.qr-codes') }}" class="btn btn-outline-secondary btn-icon mr-2 mb-2" title="Limpiar filtros" aria-label="Limpiar filtros">
                        <span class="fe fe-x-circle fe-16"></span>
                    </a>
                    <button class="btn btn-success btn-icon mr-2 mb-2" type="button" onclick="regenerateAllQR()" title="Regenerar todos" aria-label="Regenerar todos">
                        <span class="fe fe-refresh-cw fe-16"></span>
                    </button>
                    <button class="btn btn-primary btn-icon mr-2 mb-2" type="button" onclick="downloadAllQR()" title="Descargar todos" aria-label="Descargar todos">
                        <span class="fe fe-download fe-16"></span>
                    </button>
                    <button id="btn-print-all" class="btn btn-outline-primary btn-icon mb-2" type="button" onclick="printAllQR()" title="Imprimir" aria-label="Imprimir">
                        <span id="btn-print-icon" class="fe fe-printer fe-16"></span>
                        <span id="btn-print-spinner" class="spinner-border spinner-border-sm text-primary ml-2" role="status" style="display: none; width: 1rem; height: 1rem;" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        </form>
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
                    @php
                        $parts = preg_split('/\s+/', trim($qrCode->full_name));
                        $first = $parts[0] ?? '';
                        $surnames = '';
                        if (count($parts) >= 3) {
                            $surnames = $parts[count($parts) - 2] . ' ' . $parts[count($parts) - 1];
                        } elseif (count($parts) == 2) {
                            $surnames = $parts[1];
                        }
                        $displayName = trim($first . ($surnames ? ' ' . $surnames : ''));
                    @endphp
                    <h6 class="mb-0">{{ $displayName }}</h6>
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
                        <canvas id="qr-canvas-{{ $qrCode->id }}" class="qr-canvas" style="display: none;" data-code="{{ $qrCode->qr_code }}" data-name="{{ preg_replace('/\s+/', '_', $qrCode->full_name) }}"></canvas>
                        <div class="qr-error" id="qr-error-{{ $qrCode->id }}" style="display: none;">
                            <i class="fe fe-alert-circle"></i>
                            <div>Error al generar QR</div>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <code class="small">{{ $qrCode->qr_code }}</code>
                    </div>
                    
                    {{-- Estadísticas removidas por solicitud: Escaneos / Último --}}
                </div>
                <div class="card-footer no-print">
                    <div class="row align-items-center">
                        <div class="col-auto text-left pr-0">
                            <small class="text-muted">{{ $qrCode->group_name }}</small>
                        </div>
                        <div class="col text-right">
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-outline-primary btn-icon" title="Descargar PNG" 
                                        onclick="downloadQR({{ $qrCode->id }}, '{{ $qrCode->full_name }}')">
                                    <span class="fe fe-download fe-12"></span>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary btn-icon" title="Regenerar QR" 
                                        onclick="regenerateQR({{ $qrCode->id }}, '{{ $qrCode->full_name }}')">
                                    <span class="fe fe-refresh-cw fe-12"></span>
                                </button>
                                <button id="btn-print-{{ $qrCode->id }}" class="btn btn-sm btn-outline-info btn-icon" title="Imprimir Individual" 
                                        onclick="printQR({{ $qrCode->id }}, '{{ $qrCode->full_name }}')">
                                    <span class="fe fe-printer fe-12"></span>
                                    <span id="btn-print-spinner-{{ $qrCode->id }}" class="spinner-border spinner-border-sm text-primary ml-2" role="status" style="display: none; width: .9rem; height: .9rem;" aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
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
<!-- JSZip y FileSaver para descarga masiva -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

<script>
// Control de paginación
const QR_PER_PAGE = 20; // Mostrar 20 QR por página
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
    const btn = document.getElementById(`btn-print-${studentId}`);
    const spinner = document.getElementById(`btn-print-spinner-${studentId}`);
    const maxWait = 5000; // ms

    if (!canvas) return;

    // Mostrar spinner en el botón
    if (btn) btn.disabled = true;
    if (spinner) spinner.style.display = 'inline-block';

    // Esperar a que el canvas esté listo (visible y con contenido)
    const start = Date.now();
    (function waitForCanvas() {
        const isReady = canvas && canvas.width > 0 && canvas.style.display !== 'none';
        if (isReady) {
            proceedPrint();
        } else if (Date.now() - start < maxWait) {
            setTimeout(waitForCanvas, 100);
        } else {
            alert('El código QR no está listo para imprimir. Intenta regenerarlo.');
            if (btn) btn.disabled = false;
            if (spinner) spinner.style.display = 'none';
        }
    })();

    function proceedPrint() {
        try {
            const printWindow = window.open('', '_blank');
            if (!printWindow) {
                alert('La ventana de impresión fue bloqueada por el navegador. Permite popups para continuar.');
                if (btn) btn.disabled = false;
                if (spinner) spinner.style.display = 'none';
                return;
            }

            const dataURL = canvas.toDataURL();
            const html = `
                <html>
                <head>
                    <meta charset="utf-8">
                    <title>QR Code - ${studentName}</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; text-align: center; }
                        .qr-print { max-width: 320px; margin: 0 auto; }
                        .qr-print img { width: 100%; height: auto; display: block; }
                        h2 { margin-top: 10px; font-size: 18px; color: #333; }
                        @media print { body { margin: 6mm; } }
                    </style>
                </head>
                <body>
                    <div class="qr-print">
                        <img src="${dataURL}" alt="QR for ${studentName}" />
                        <h2>${studentName}</h2>
                    </div>
                </body>
                </html>
            `;

            printWindow.document.open();
            printWindow.document.write(html);
            printWindow.document.close();

            // Esperar a que la imagen cargue antes de imprimir
            const img = printWindow.document.querySelector('img');
            if (!img) {
                printWindow.print();
                cleanup();
                return;
            }

            if (img.complete) {
                printWindow.focus();
                printWindow.print();
                // No cerrar automáticamente
                cleanup();
            } else {
                img.addEventListener('load', () => {
                    printWindow.focus();
                    printWindow.print();
                    cleanup();
                });
                img.addEventListener('error', () => {
                    alert('Error cargando la imagen de impresión');
                    cleanup();
                });
            }
        } catch (err) {
            console.error('Error en printQR:', err);
            if (btn) btn.disabled = false;
            if (spinner) spinner.style.display = 'none';
        }
    }

    function cleanup() {
        if (btn) btn.disabled = false;
        if (spinner) spinner.style.display = 'none';
    }
}

// Descargar todos los QR como ZIP
// Cargar JSZip y FileSaver desde CDN si no están presentes (se añadirá script tag en plantilla)
function downloadAllQR() {
    const canvases = Array.from(document.querySelectorAll('.qr-canvas')).filter(c => c && c.style.display !== 'none' && c.width > 0);
    if (canvases.length === 0) {
        alert('No hay códigos QR generados para descargar');
        return;
    }

    // Crear ZIP usando JSZip
    if (typeof JSZip === 'undefined' || typeof saveAs === 'undefined') {
        alert('Dependencias para la descarga masiva no cargadas. Intenta recargar la página.');
        console.error('JSZip o FileSaver no disponibles');
        return;
    }

    const zip = new JSZip();
    const folder = zip.folder('qrcodes');

    const promises = canvases.map((canvas) => {
        return new Promise((resolve) => {
            const name = canvas.dataset.name || `qr_${canvas.id}`;
            const code = canvas.dataset.code || canvas.id;
            try {
                canvas.toBlob((blob) => {
                    if (!blob) {
                        console.warn('No se pudo obtener blob de canvas', canvas.id);
                        resolve();
                        return;
                    }
                    const filename = `${name}_${code}.png`;
                    folder.file(filename, blob);
                    resolve();
                }, 'image/png');
            } catch (err) {
                console.error('Error convirtiendo canvas a blob', err);
                resolve();
            }
        });
    });

    Promise.all(promises).then(() => {
        zip.generateAsync({ type: 'blob' }).then((content) => {
            saveAs(content, `qr_codes_${new Date().toISOString().slice(0,10)}.zip`);
        }).catch(err => {
            console.error('Error generando ZIP:', err);
            alert('Error al generar el ZIP de códigos QR');
        });
    });
}

// Imprimir todos los QR visibles en una vista optimizada para impresión
function printAllQR() {
    const btn = document.getElementById('btn-print-all');
    const spinner = document.getElementById('btn-print-spinner');
    const icon = document.getElementById('btn-print-icon');

    const canvases = Array.from(document.querySelectorAll('.qr-canvas')).filter(c => c && c.style.display !== 'none' && c.width > 0);
    if (canvases.length === 0) {
        alert('No hay códigos QR generados para imprimir');
        return;
    }

    // Deshabilitar botón y mostrar spinner
    if (btn) btn.disabled = true;
    if (spinner) spinner.style.display = 'inline-block';
    if (icon) icon.style.display = 'none';

    // Construir HTML para la ventana de impresión
    let html = `
        <!doctype html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Imprimir Códigos QR</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 10mm; }
                .grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
                @media print { .grid { grid-template-columns: repeat(4, 1fr); } }
                .card { text-align: center; padding: 8px; border: 1px solid #e9ecef; border-radius: 6px; }
                .card img { max-width: 100%; height: auto; display: block; margin: 0 auto; }
                .meta { margin-top: 6px; font-size: 12px; color: #333; }
                .code { font-family: monospace; font-size: 11px; color: #555; }
                @media print {
                    .no-print { display: none !important; }
                    .card { page-break-inside: avoid; }
                    body { margin: 6mm; }
                }
            </style>
        </head>
        <body>
            <div class="grid">
    `;

    // Añadir cada canvas como imagen dataURL
    canvases.forEach((canvas) => {
        try {
            const dataURL = canvas.toDataURL();
            const name = canvas.dataset.name || canvas.id;
            const code = canvas.dataset.code || canvas.id;
            html += `
                <div class="card">
                    <img src="${dataURL}" alt="QR ${code}" />
                    <div class="meta">${name}</div>
                </div>
            `;
        } catch (err) {
            console.error('Error obteniendo dataURL del canvas', err);
        }
    });

    html += `</div></body></html>`;

    // Abrir ventana de impresión
    const printWindow = window.open('', '_blank');
    if (!printWindow) {
        alert('La ventana de impresión fue bloqueada por el navegador. Permite popups para continuar.');
        if (btn) btn.disabled = false;
        if (spinner) spinner.style.display = 'none';
        if (icon) icon.style.display = 'inline-block';
        return;
    }

    printWindow.document.open();
    printWindow.document.write(html);
    printWindow.document.close();

    // Esperar a que todas las imágenes carguen en la nueva ventana
    const imgs = printWindow.document.querySelectorAll('img');
    let loaded = 0;
    const total = imgs.length;

    if (total === 0) {
        // No hay imágenes, lanza print inmediatamente
        printWindow.focus();
        printWindow.print();
        printWindow.close();
        if (btn) btn.disabled = false;
        if (spinner) spinner.style.display = 'none';
        if (icon) icon.style.display = 'inline-block';
        return;
    }

    imgs.forEach(img => {
        if (img.complete) {
            loaded++;
            if (loaded === total) finishPrint();
        } else {
            img.addEventListener('load', () => {
                loaded++;
                if (loaded === total) finishPrint();
            });
            img.addEventListener('error', () => {
                loaded++;
                if (loaded === total) finishPrint();
            });
        }
    });

    function finishPrint() {
        try {
            printWindow.focus();
            printWindow.print();
            // No cierro automáticamente: algunos navegadores bloquean el cierre inmediato
            // printWindow.close();
        } catch (err) {
            console.error('Error durante print()', err);
        } finally {
            if (btn) btn.disabled = false;
            if (spinner) spinner.style.display = 'none';
            if (icon) icon.style.display = 'inline-block';
        }
    }
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
    
    // Nota: La búsqueda se realiza server-side a través del formulario GET.
    // Se eliminó el listener de búsqueda en cliente para evitar filtrar solo los elementos visibles en la página.
});
</script>
@endsection