@extends('layouts.app')

@section('title', 'Escáner QR - Asistencias')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('attendances.history') }}">Asistencias</a></li>
        <li class="breadcrumb-item active" aria-current="page">Escáner QR</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid">

<!-- Selección de Sesión y Estadísticas -->
<div class="row mb-4">
    <!-- Información de la Sesión -->
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-body">
                @if($selectedSession)
                    <!-- Información de la sesión seleccionada -->
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-1">{{ $selectedSession->title }}</h5>
                            @if($selectedSession->notes)
                                <p class="text-muted mb-2">{{ $selectedSession->notes }}</p>
                            @endif
                            <div class="d-flex flex-wrap">
                                <span class="badge badge-primary mr-2 mb-1">
                                    <i class="fe fe-calendar mr-1"></i>
                                    {{ $selectedSession->date->format('d/m/Y') }}
                                </span>
                                <span class="badge badge-info mr-2 mb-1">
                                    <i class="fe fe-clock mr-1"></i>
                                    {{ $selectedSession->time ? $selectedSession->time->format('H:i') : 'Sin hora' }}
                                </span>
                                <span class="badge badge-success mr-2 mb-1">
                                    <i class="fe fe-check-circle mr-1"></i>
                                    Sesión Activa
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <div class="btn-group" role="group">
                                <button class="btn btn-warning" data-toggle="modal" data-target="#changeSessionModal">
                                    <i class="fe fe-refresh-cw mr-2"></i>Cambiar Sesión
                                </button>
                                <a href="{{ route('attendances.register', ['session_id' => $selectedSession->id]) }}" class="btn btn-info">
                                    <i class="fe fe-users mr-2"></i>Registro Manual
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Selector de sesión -->
                    <div class="text-center py-4">
                        <i class="fe fe-calendar-x mb-3" style="font-size: 48px; color: #6c757d;"></i>
                        <h5 class="text-muted">No hay sesión seleccionada</h5>
                        <p class="mb-3">Seleccione una sesión activa para comenzar el escaneo de códigos QR</p>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#changeSessionModal">
                            <i class="fe fe-plus mr-1"></i>Seleccionar Sesión
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if($selectedSession)
    <!-- Estadísticas de Escaneo -->
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                @if($scanStats->total_scans > 0)
                    <div class="text-center mb-2">
                        <div class="h3 mb-0 text-primary">{{ $scanStats->scan_rate }}%</div>
                        <small class="text-muted">Tasa de Éxito</small>
                    </div>

                    @php
                        $successPercent = ($scanStats->successful_scans / $scanStats->total_scans) * 100;
                        $errorPercent = ($scanStats->error_scans / $scanStats->total_scans) * 100;
                    @endphp

                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-success" 
                             style="width: {{ round($successPercent, 2) }}%"
                             data-bs-toggle="tooltip" title="Exitosos: {{ $scanStats->successful_scans }}">
                        </div>
                        <div class="progress-bar bg-danger" 
                             style="width: {{ round($errorPercent, 2) }}%"
                             data-bs-toggle="tooltip" title="Errores: {{ $scanStats->error_scans }}">
                        </div>
                    </div>

                    <div class="row text-center">
                        <div class="col-4">
                            <div class="h6 mb-0 text-info">{{ $scanStats->total_scans }}</div>
                            <small class="text-muted">Total</small>
                        </div>
                        <div class="col-4">
                            <div class="h6 mb-0 text-success">{{ $scanStats->successful_scans }}</div>
                            <small class="text-muted">Exitosos</small>
                        </div>
                        <div class="col-4">
                            <div class="h6 mb-0 text-danger">{{ $scanStats->error_scans }}</div>
                            <small class="text-muted">Errores</small>
                        </div>
                    </div>
                @else
                    <div class="text-center text-muted">
                        <i class="fe fe-activity" style="font-size: 1.5rem; margin-right: 0.5rem;"></i>
                        <p class="mt-2 mb-0 small">Sin escaneos</p>
                        <small>Comience escaneando códigos QR.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

<div class="row">
    <!-- Cámara y Escáner -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-header-title">
                            <i class="fe fe-camera mr-2"></i>
                            <!--Escáner de Códigos QR -->
                        </h4>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex align-items-center">
                            <span id="scanner-status" class="badge badge-secondary mr-2">Detenido</span>
                            <div class="btn-group btn-group-sm" role="group">
                                <button id="start-scanner-btn" class="btn btn-success">
                                    <i class="fe fe-play mr-1"></i>Iniciar Scanner
                                </button>
                                <button id="stop-scanner-btn" class="btn btn-danger" style="display: none;">
                                    <i class="fe fe-square mr-1"></i>Detener
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Controles de Escaneo -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Modo de Escaneo</label>
                        <select id="scan-mode-select" class="form-control form-control-sm">
                            <option value="continuous">Continuo (múltiples códigos)</option>
                            <option value="single">Individual (un código)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Cámara</label>
                        <select id="camera-select" class="form-control form-control-sm">
                            <option value="">Detectando cámaras...</option>
                        </select>
                    </div>
                </div>

                <!-- Área de Video/Scanner -->
                <div class="scanner-container position-relative bg-dark rounded" style="height: 400px; overflow: hidden !important;">
                    <!-- Contenedor para HTML5-QRCode Scanner -->
                    <div id="qr-reader" class="w-100 h-100" style="overflow: hidden !important;"></div>
                    
                    <!-- Overlay para mensajes cuando no está activo -->
                    <div id="scanner-overlay" class="position-absolute d-flex flex-column justify-content-center align-items-center text-center w-100 h-100 text-light" style="top: 0; left: 0; background: rgba(0,0,0,0.7);">
                        <div class="mb-3">
                            <i class="fe fe-camera" style="font-size: 48px;"></i>
                        </div>
                        <h5>Scanner Desactivado</h5>
                        <p class="mb-0">Haga clic en "Iniciar Scanner" para comenzar</p>
                    </div>
                </div>

                <!-- Información del último escaneo -->
                <div id="last-scan-info" class="mt-3 p-3 bg-light rounded" style="display: none;">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="mb-1">Último Código Escaneado</h6>
                            <code id="last-scan-code" class="d-block"></code>
                        </div>
                        <div class="col-auto">
                            <span id="last-scan-status" class="badge"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel de Control y Resultados -->
    <div class="col-lg-4">
        <!-- Escaneos Recientes -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-header-title">
                    <i class="fe fe-list mr-2"></i>
                    Escaneos Recientes
                </h4>
            </div>
            <div class="card-body p-0">
                <div id="recent-scans-container" class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                    @if($recentScans && count($recentScans) > 0)
                        @foreach($recentScans as $scan)
                        <div class="card mb-2 recent-scan scan-{{ $scan->status }}">
                            <div class="card-body py-2">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="mb-0">{{ $scan->student_name }}</h6>
                                        <small class="text-muted">
                                            <code>{{ $scan->qr_code }}</code>
                                            <span class="badge badge-{{ $scan->group === 'Grupo A' ? 'primary' : 'info' }} badge-sm ml-1">
                                                {{ $scan->group }}
                                            </span>
                                        </small>
                                    </div>
                                    <div class="col-auto">
                                        <div class="text-right">
                                            <span class="badge badge-{{ $scan->status === 'present' ? 'success' : ($scan->status === 'late' ? 'warning' : 'secondary') }}">
                                                <i class="fe fe-{{ $scan->status === 'present' ? 'check' : ($scan->status === 'late' ? 'clock' : 'user') }}"></i>
                                                {{ ucfirst($scan->status) }}
                                            </span>
                                            <div class="small text-muted">{{ $scan->scan_time }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fe fe-inbox mb-2" style="font-size: 24px;"></i>
                            <p class="mb-0">No hay escaneos recientes</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-header-title">
                    <i class="fe fe-zap mr-2"></i>
                    Acciones Rápidas
                </h4>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button id="export-session" class="btn btn-outline-primary btn-block">
                        <i class="fe fe-download mr-1"></i>
                        Exportar Sesión
                    </button>
                    <button id="finish-session" class="btn btn-outline-warning btn-block">
                        <i class="fe fe-check-square mr-1"></i>
                        Finalizar Sesión
                    </button>
                    <a href="{{ route('attendances.register', ['session_id' => $selectedSession->id]) }}" class="btn btn-outline-info btn-block">
                        <i class="fe fe-users mr-1"></i>
                        Registro Manual
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

</div>

<!-- Modal para cambiar sesión -->
<div class="modal fade" id="changeSessionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Seleccionar Sesión</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if($activeSessions && count($activeSessions) > 0)
                    <form method="GET" action="{{ route('attendances.qr-scanner') }}">
                        <div class="form-group">
                            <label for="session_id">Sesión Activa</label>
                            <select name="session_id" id="session_id" class="form-control" required>
                                <option value="">Seleccione una sesión...</option>
                                @foreach($activeSessions as $session)
                                <option value="{{ $session->id }}" {{ $selectedSession && $selectedSession->id == $session->id ? 'selected' : '' }}>
                                    {{ $session->title }} - {{ $session->date->format('d/m/Y') }} ({{ $session->time ? $session->time->format('H:i') : 'Sin hora' }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="text-right">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Seleccionar</button>
                        </div>
                    </form>
                @else
                    <div class="text-center py-4">
                        <i class="fe fe-alert-triangle mb-3 text-warning" style="font-size: 48px;"></i>
                        <h5>No hay sesiones activas</h5>
                        <p class="text-muted">No se encontraron sesiones de catequesis activas para hoy.</p>
                        <a href="{{ route('attendance-sessions.create') }}" class="btn btn-primary">
                            <i class="fe fe-plus mr-1"></i>Crear Nueva Sesión
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación de escaneo -->
<div class="modal fade" id="scanResultModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fe fe-check-circle text-success mr-2"></i>
                    Asistencia Registrada
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="scan-result-content">
                    <!-- Contenido dinámico del resultado -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Continuar Escaneando</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* ===== CSS OPTIMIZADO PARA QR SCANNER ===== */

/* Contenedor principal del scanner */
#qr-reader {
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    overflow: hidden !important;
    background: #000 !important;
    position: relative !important;
}

/* Video de la cámara - configuración base */
#qr-reader video {
    width: 100% !important;
    height: auto !important;
    max-width: 100% !important;
    max-height: 100% !important;
    object-fit: contain !important;
    border-radius: 8px;
    display: block !important;
    position: relative !important;
}

/* Canvas overlay para el marco de escaneo */
#qr-reader canvas {
    position: absolute !important;
    top: 50% !important;
    left: 50% !important;
    transform: translate(-50%, -50%) !important;
    max-width: 100% !important;
    max-height: 100% !important;
}

/* Elementos internos de HTML5-QRCode */
#qr-reader > div {
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    width: 100% !important;
    height: 100% !important;
    overflow: hidden !important;
    position: relative !important;
}

/* Contención universal para todos los elementos */
#qr-reader * {
    max-width: 100% !important;
    max-height: 100% !important;
}

/* SOLUCIÓN ESPECÍFICA: Videos grandes que se desbordan */
@media (min-width: 768px) {
    .scanner-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 400px !important;
        overflow: hidden !important;
    }
    
    /* Escalar videos anchos automáticamente */
    #qr-reader video[style*="width: 606px"], 
    #qr-reader video[style*="width: 600px"], 
    #qr-reader video[style*="width: 5"],
    #qr-reader video[style*="width: 4"] {
        transform: scale(0.65) !important;
        transform-origin: center !important;
    }
}

/* Comportamiento en móviles */
@media (max-width: 767px) {
    #qr-reader video {
        object-fit: cover !important;
    }
}

/* Botones de control de la librería */
#qr-reader button {
    position: absolute !important;
    bottom: 10px !important;
    right: 10px !important;
    z-index: 10 !important;
}
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
console.log('=== QR SCANNER INICIADO ===');
// Declaraciones globales para funciones del scanner
let initializeScanner;
let stopScanner;

function setupQRScanner() {
    console.log('setupQRScanner ejecutado - configuración inicial completada');
}

// Configurar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupQRScanner);
} else {
    setupQRScanner();
}

// Variables y funciones principales
document.addEventListener('DOMContentLoaded', function() {
    // Variables de configuración
    const csrfToken = '{{ csrf_token() }}';
    const sessionId = {{ $selectedSession ? $selectedSession->id : 'null' }};
    
    console.log('DOM Content Loaded - Session ID:', sessionId);
    
    // Elementos del DOM
    const startScannerBtn = document.getElementById('start-scanner-btn');
    const stopScannerBtn = document.getElementById('stop-scanner-btn');
    const scanModeSelect = document.getElementById('scan-mode-select');
    const scannerOverlay = document.getElementById('scanner-overlay');
    const scannerStatus = document.getElementById('scanner-status');
    const recentScansContainer = document.getElementById('recent-scans-container');
    const cameraSelect = document.getElementById('camera-select');
    
    // Configurar event listeners
    if (startScannerBtn) {
        startScannerBtn.addEventListener('click', function() {
            console.log('Botón Iniciar presionado');
            initializeScanner();
        });
    }
    
    if (stopScannerBtn) {
        stopScannerBtn.addEventListener('click', function() {
            console.log('Botón Detener presionado');
            stopScanner();
        });
        console.log('Event listener configurado para botón detener');
    } else {
        console.error('Botón detener no encontrado');
    }
    
    // Variables de estado
    let html5QrcodeScanner = null;
    let scannerInitialized = false;
    let scanMode = 'continuous';
    let lastScanResult = null;
    let lastScanTime = 0;
    let scanCooldown = 3000; // 3 segundos entre scans del mismo código
    let isProcessing = false;
    let availableCameras = [];
    let selectedCameraId = null;
    console.log('QR Scanner script loaded', { sessionId, startScannerBtn });
    
    console.log('Scanner variables inicializadas');
    
    // Función para mostrar overlay del scanner
    function showScannerOverlay() {
        if (scannerOverlay) {
            scannerOverlay.style.display = 'flex';
        }
    }
    
    // Función para cargar cámaras disponibles
    function loadAvailableCameras() {
        console.log('Cargando cámaras disponibles...');
        
        Html5Qrcode.getCameras()
            .then(cameras => {
                console.log('Cámaras detectadas:', cameras);
                
                // Reordenar cámaras: priorizar cámaras traseras
                cameras.sort((a, b) => {
                    const aIsBack = /back|rear|environment/i.test(a.label || '');
                    const bIsBack = /back|rear|environment/i.test(b.label || '');
                    
                    if (aIsBack && !bIsBack) return -1;
                    if (!aIsBack && bIsBack) return 1;
                    return 0;
                });
                
                console.log('Cámaras reordenadas (traseras primero):', cameras);
                availableCameras = cameras;
                
                if (cameraSelect) {
                    // Limpiar opciones existentes
                    cameraSelect.innerHTML = '';
                    
                    if (cameras && cameras.length > 0) {
                        cameras.forEach((camera, index) => {
                            const option = document.createElement('option');
                            option.value = camera.id;
                            option.textContent = camera.label || `Cámara ${index + 1}`;
                            cameraSelect.appendChild(option);
                        });
                        
                        // Seleccionar primera cámara por defecto
                        selectedCameraId = cameras[0].id;
                        cameraSelect.value = selectedCameraId;
                        
                        showToast(`${cameras.length} cámara(s) detectada(s)`, 'success');
                    } else {
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'No se encontraron cámaras';
                        cameraSelect.appendChild(option);
                        
                        showToast('No se encontraron cámaras disponibles', 'warning');
                    }
                }
            })
            .catch(err => {
                console.error('Error obteniendo cámaras:', err);
                
                if (cameraSelect) {
                    cameraSelect.innerHTML = '';
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'Error detectando cámaras';
                    cameraSelect.appendChild(option);
                }
                
                showToast('Error al detectar cámaras: ' + err.message, 'error');
            });
    }
    
    // Event listeners adicionales
    if (scanModeSelect) {
        scanModeSelect.addEventListener('change', function() {
            scanMode = this.value;
            console.log('Modo de escaneo cambiado a:', scanMode);
        });
    }
    
    if (cameraSelect) {
        cameraSelect.addEventListener('change', function() {
            selectedCameraId = this.value;
            console.log('Cámara seleccionada:', selectedCameraId);
            
            // Si el scanner está activo, reiniciar con nueva cámara
            if (scannerInitialized) {
                stopScanner();
                setTimeout(() => {
                    initializeScanner();
                }, 500);
            }
        });
    }
    initializeScanner = function() {
        console.log('initializeScanner called');
        if (!sessionId) {
            showToast('Debe seleccionar una sesión primero', 'error');
            return;
        }
        if (scannerInitialized) {
            showToast('El scanner ya está inicializado', 'warning');
            return;
        }
        showToast('Inicializando scanner...', 'info');
        updateScannerStatus('Iniciando...');
        console.log('Ocultando overlay...', scannerOverlay);
        if (scannerOverlay) {
            scannerOverlay.style.setProperty('display', 'none', 'important');
            console.log('Overlay ocultado');
        }
        // Configuración del scanner
        const config = {
            fps: 10,
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.333333, // 4:3 ratio para evitar franjas negras
            rememberLastUsedCamera: true,
            showTorchButtonIfSupported: true,
            formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE],
            supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
        };
        // Usar la cámara seleccionada o la primera disponible
        if (!selectedCameraId && availableCameras.length > 0) {
            selectedCameraId = availableCameras[0].id;
        }
        
        if (selectedCameraId) {
            html5QrcodeScanner = new Html5Qrcode('qr-reader');
            html5QrcodeScanner.start(selectedCameraId, config, handleQRDetection, handleScanError)
                        .then(() => {
                            scannerInitialized = true;
                            updateUIState(true);
                            updateScannerStatus('Activo');
                            showToast('Scanner iniciado exitosamente', 'success');
                        })
                        .catch(err => {
                            console.error('Error starting scanner:', err);
                            showToast('Error al iniciar el scanner: ' + err, 'error');
                            updateScannerStatus('Error');
                            showScannerOverlay();
                        });
        } else {
            showToast('No hay cámara seleccionada. Por favor, seleccione una cámara.', 'error');
            updateScannerStatus('Error');
            showScannerOverlay();
        }
    };

    // Función para detener el scanner
    stopScanner = function() {
        console.log('Deteniendo scanner...');
        if (html5QrcodeScanner && scannerInitialized) {
            html5QrcodeScanner.stop()
                .then(() => {
                    console.log('Scanner detenido exitosamente');
                    html5QrcodeScanner = null;
                    scannerInitialized = false;
                    updateUIState(false);
                    updateScannerStatus('Detenido');
                    showScannerOverlay();
                    showToast('Scanner detenido', 'info');
                })
                .catch(err => {
                    console.error('Error stopping scanner:', err);
                    // Forzar reset aunque haya error
                    html5QrcodeScanner = null;
                    scannerInitialized = false;
                    updateUIState(false);
                    updateScannerStatus('Detenido');
                    showScannerOverlay();
                    showToast('Scanner detenido (con errores)', 'warning');
                });
        } else {
            console.log('Scanner no está inicializado o ya está detenido');
        }
    };

    // Actualizar estado de la UI
    function updateUIState(isActive) {
        if (startScannerBtn && stopScannerBtn) {
            if (isActive) {
                startScannerBtn.style.display = 'none';
                stopScannerBtn.style.display = 'inline-block';
            } else {
                startScannerBtn.style.display = 'inline-block';
                stopScannerBtn.style.display = 'none';
            }
        }
    }

    // Actualizar estado del scanner
    function updateScannerStatus(status) {
        if (scannerStatus) {
            scannerStatus.textContent = status;
            scannerStatus.className = 'badge badge-' + 
                (status === 'Activo' ? 'success' : 
                 status === 'Error' ? 'danger' : 
                 status === 'Iniciando...' ? 'warning' : 'secondary');
        }
    }

    // Manejar detección de QR
    function handleQRDetection(decodedText, decodedResult) {
        console.log('QR detectado:', decodedText);
        
        const currentTime = Date.now();
        
        // Evitar scans duplicados rápidos del mismo código
        if (decodedText === lastScanResult && (currentTime - lastScanTime) < scanCooldown) {
            console.log('Scan duplicado ignorado - cooldown activo');
            return;
        }
        
        // Evitar procesar múltiples scans simultáneamente
        if (isProcessing) {
            console.log('Scan ignorado - ya procesando otro');
            return;
        }

        lastScanResult = decodedText;
        lastScanTime = currentTime;
        
        // Si está en modo single, detener después del primer escaneo
        if (scanMode === 'single') {
            stopScanner();
        }
        
        processQRCode(decodedText);
    }

    // Manejar errores de escaneo
    function handleScanError(errorMessage, error) {
        // Solo loggear errores importantes, no los de "No MultiFormat Readers"
        if (errorMessage && !errorMessage.includes('NotFoundException') && !errorMessage.includes('No MultiFormat Readers')) {
            console.warn('Error de escaneo:', errorMessage);
        }
    }

    // Procesar código QR
    function processQRCode(qrCode) {
        console.log('Procesando QR:', qrCode);
        
        // Marcar como procesando
        isProcessing = true;
        
        fetch('{{ route("attendances.qr-scan") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                qr_code: qrCode,
                attendance_session_id: sessionId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showScanResult(data.data);
                addRecentScan(data.data);
                updateScanStats();
                showToast('Asistencia registrada: ' + data.data.student_name, 'success');
            } else {
                showToast('Error: ' + data.message, 'error');
                console.error('Error al procesar QR:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error de conexión al procesar código QR', 'error');
        })
        .finally(() => {
            // Liberar el lock de procesamiento después de un delay
            setTimeout(() => {
                isProcessing = false;
            }, 1000);
        });
    }
    
    function showScanResult(scanData) {
        const modalContent = document.getElementById('scan-result-content');
        if (modalContent) {
            modalContent.innerHTML = `
                <div class="text-center">
                    <h5 class="mb-1">${scanData.student_name}</h5>
                    <p class="text-muted mb-3">
                        <span class="badge badge-${scanData.group === 'Grupo A' ? 'primary' : 'info'}">${scanData.group}</span>
                    </p>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h6 mb-0">${scanData.qr_code}</div>
                            <small class="text-muted">Código QR</small>
                        </div>
                        <div class="col-6">
                            <div class="h6 mb-0">${scanData.marked_at}</div>
                            <small class="text-muted">Hora de Registro</small>
                        </div>
                    </div>
                </div>
            `;
            $('#scanResultModal').modal('show');
        }
    }
    
    function addRecentScan(scanData) {
        const scanElement = document.createElement('div');
        const statusClass = scanData.status === 'present' ? 'success' : (scanData.status === 'late' ? 'warning' : 'secondary');
        const badgeClass = scanData.status === 'present' ? 'success' : (scanData.status === 'late' ? 'warning' : 'secondary');
        const statusIcon = scanData.status === 'present' ? 'check' : (scanData.status === 'late' ? 'clock' : 'user');
        
        scanElement.className = `card mb-2 recent-scan scan-${statusClass}`;
        scanElement.innerHTML = `
            <div class="card-body py-2">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="mb-0">${scanData.student_name}</h6>
                        <small class="text-muted">
                            <code>${scanData.qr_code}</code>
                            <span class="badge badge-${scanData.group === 'Grupo A' ? 'primary' : 'info'} badge-sm ml-1">
                                ${scanData.group}
                            </span>
                        </small>
                    </div>
                    <div class="col-auto">
                        <div class="text-right">
                            <span class="badge badge-${badgeClass}">
                                <i class="fe fe-${statusIcon}"></i>
                                ${scanData.status}
                            </span>
                            <div class="small text-muted">${scanData.marked_at}</div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        const noScansMessage = recentScansContainer.querySelector('.text-center.text-muted');
        if (noScansMessage) {
            noScansMessage.remove();
        }
        
        recentScansContainer.insertBefore(scanElement, recentScansContainer.firstChild);
        
        const scans = recentScansContainer.querySelectorAll('.recent-scan');
        if (scans.length > 5) {
            scans[scans.length - 1].remove();
        }
    }
    
    function updateScanStats() {
        const totalScans = document.querySelector('.text-success.h3');
        const successfulScans = document.querySelector('.text-primary.h3');
        
        if (totalScans && successfulScans) {
            const currentTotal = parseInt(totalScans.textContent) + 1;
            const currentSuccessful = parseInt(successfulScans.textContent) + 1;
            
            totalScans.textContent = currentTotal;
            successfulScans.textContent = currentSuccessful;
            
            const successRate = document.querySelector('.text-info.h3');
            if (successRate) {
                const rate = Math.round((currentSuccessful / currentTotal) * 100);
                successRate.textContent = rate + '%';
            }
        }
    }
    
    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : (type === 'error' ? 'danger' : 'info')} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 5000);
    }
    
    // Mostrar overlay inicial si no hay sesión
    if (!sessionId) {
        showScannerOverlay();
        updateScannerStatus('Sin sesión');
    }
    
    // Cargar cámaras disponibles al inicializar
    loadAvailableCameras();
    
    // Acciones rápidas
    const exportBtn = document.getElementById('export-session');
    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            window.location.href = '{{ route("attendances.history") }}';
        });
    }
    
    const finishBtn = document.getElementById('finish-session');
    if (finishBtn) {
        finishBtn.addEventListener('click', function() {
            if (confirm('¿Finalizar la sesión actual? No podrá registrar más asistencias.')) {
                // Crear un formulario para cerrar la sesión
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("sessions.close", $selectedSession->id ?? "") }}';
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Cleanup al salir
    window.addEventListener('beforeunload', function() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear();
        }
    });
});
</script>
@endpush