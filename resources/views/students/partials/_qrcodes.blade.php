<?php /** Partial: modal content para mostrar QR individual - inyectar en un modal externo */ ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12 text-center">
            <h5 class="mb-3">Código QR de {{ $student->full_name }}</h5>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-auto">
            <div class="qr-code-display mb-3" id="qr-container-{{ $student->id }}">
                <div class="qr-loading" id="qr-loading-{{ $student->id }}">
                    <div class="spinner-border text-primary" role="status"><span class="sr-only">Generando QR...</span></div>
                    <div class="mt-2">Generando QR...</div>
                </div>
                <canvas id="qr-canvas-{{ $student->id }}" class="qr-canvas" style="display:none;" data-code="{{ $student->qr_code }}" data-name="{{ preg_replace('/\s+/', '_', $student->full_name) }}"></canvas>
                <div class="qr-error" id="qr-error-{{ $student->id }}" style="display:none;">
                    <i class="fe fe-alert-circle"></i>
                    <div>Error al generar QR</div>
                </div>
            </div>

            <div class="d-flex justify-content-center" style="gap:8px;">
                <button class="btn btn-sm btn-primary" onclick="downloadQR({{ $student->id }}, '{{ $student->full_name }}')">
                    <span class="fe fe-download fe-12"></span> Descargar
                </button>
                <button class="btn btn-sm btn-outline-info" onclick="printQR({{ $student->id }}, '{{ $student->full_name }}')">
                    <span class="fe fe-printer fe-12"></span> Imprimir
                </button>
            </div>
        </div>
    </div>

    <script>
    // Inicializar QR al inyectar el partial en el modal.
    // Si el DOM ya está listo (cuando se inyecta vía AJAX), ejecutamos inmediatamente;
    // si no, nos suscribimos a DOMContentLoaded.
    (function() {
        function initQR() {
            try {
                // Priorizar la función global generateQRCode (definida en qr-utils.js)
                if (typeof generateQRCode === 'function') {
                    generateQRCode({{ $student->id }}, '{{ $student->qr_code }}', 50);
                    return;
                }

                // Si existe la librería QRCode con toCanvas (opción preferida)
                if (typeof QRCode !== 'undefined' && typeof QRCode.toCanvas === 'function') {
                    const canvas = document.getElementById('qr-canvas-{{ $student->id }}');
                    try {
                        QRCode.toCanvas(canvas, '{{ $student->qr_code }}', { errorCorrectionLevel: 'M' }, function(err) {
                            if (!err) {
                                const loading = document.getElementById('qr-loading-{{ $student->id }}');
                                if (loading) loading.style.display = 'none';
                                canvas.style.display = 'block';
                            } else {
                                console.error('QRCode.toCanvas error', err);
                                const loading = document.getElementById('qr-loading-{{ $student->id }}');
                                if (loading) loading.style.display = 'none';
                                const error = document.getElementById('qr-error-{{ $student->id }}');
                                if (error) error.style.display = 'block';
                            }
                        });
                        return;
                    } catch (e) { console.error(e); }
                }

                // Fallback: qrcode-generator (global `qrcode`) — dibujar en canvas
                if (typeof qrcode !== 'undefined') {
                    try {
                        const canvas = document.getElementById('qr-canvas-{{ $student->id }}');
                        const loading = document.getElementById('qr-loading-{{ $student->id }}');
                        const error = document.getElementById('qr-error-{{ $student->id }}');
                        const qr = qrcode(4, 'M');
                        qr.addData('{{ $student->qr_code }}');
                        qr.make();
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
                                    ctx.fillRect(col * cellSize, row * cellSize, Math.ceil(cellSize), Math.ceil(cellSize));
                                }
                            }
                        }
                        if (loading) loading.style.display = 'none';
                        canvas.style.display = 'block';
                        if (error) error.style.display = 'none';
                        return;
                    } catch (e) {
                        console.error('qrcode fallback error', e);
                    }
                }

                // Último recurso: mostrar el texto del código en el área de error
                const loading = document.getElementById('qr-loading-{{ $student->id }}');
                if (loading) loading.style.display = 'none';
                const errorEl = document.getElementById('qr-error-{{ $student->id }}');
                if (errorEl) {
                    errorEl.style.display = 'block';
                    errorEl.innerHTML = '<div class="small">{{ $student->qr_code }}</div>';
                }
            } catch (err) {
                console.error('Error inicializando QR en modal', err);
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initQR);
        } else {
            // Se inyectó dinámicamente después del DOMContentLoaded: inicializar de inmediato
            initQR();
        }
    })();

    </script>

</div>
