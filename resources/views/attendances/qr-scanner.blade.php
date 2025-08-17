@extends('layouts.app')

@section('title', 'Escanear QR - Sistema de Asistencias')

@section('page-title', 'Escáner de Códigos QR')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Asistencias</li>
    <li class="breadcrumb-item active">Escanear QR</li>
@endsection

@section('additional-css')
<style>
.camera-container {
    position: relative;
    background: #000;
    border-radius: 8px;
    overflow: hidden;
    min-height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.camera-placeholder {
    color: white;
    text-align: center;
}

.scan-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 200px;
    height: 200px;
    border: 2px solid #28a745;
    border-radius: 8px;
    box-shadow: 0 0 0 9999px rgba(0,0,0,0.5);
}

.scan-corners {
    position: absolute;
    width: 20px;
    height: 20px;
}

.scan-corners::before {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    border: 3px solid #28a745;
}

.scan-corners.top-left {
    top: -2px;
    left: -2px;
}

.scan-corners.top-left::before {
    border-right: none;
    border-bottom: none;
}

.scan-corners.top-right {
    top: -2px;
    right: -2px;
}

.scan-corners.top-right::before {
    border-left: none;
    border-bottom: none;
}

.scan-corners.bottom-left {
    bottom: -2px;
    left: -2px;
}

.scan-corners.bottom-left::before {
    border-right: none;
    border-top: none;
}

.scan-corners.bottom-right {
    bottom: -2px;
    right: -2px;
}

.scan-corners.bottom-right::before {
    border-left: none;
    border-top: none;
}

.recent-scan {
    animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(100px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.scan-success {
    border-left: 4px solid #28a745;
    background-color: #f8fff9;
}

.scan-late {
    border-left: 4px solid #ffc107;
    background-color: #fffdf8;
}

.scan-error {
    border-left: 4px solid #dc3545;
    background-color: #fef8f8;
}

@media (max-width: 768px) {
    .scan-overlay {
        width: 150px;
        height: 150px;
    }
}
</style>
@endsection

@section('content')
<!-- Selector de Sesión -->
@if($activeSessions->count() > 1)
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="mb-0">Seleccionar Sesión para Escaneo</h6>
                    </div>
                    <div class="col-auto">
                        <form method="GET" action="{{ route('attendances.qr-scanner') }}">
                            <select name="session_id" class="form-control" onchange="this.form.submit()">
                                @foreach($activeSessions as $session)
                                    <option value="{{ $session->id }}" {{ $selectedSession && $selectedSession->id == $session->id ? 'selected' : '' }}>
                                        {{ $session->title }} - {{ $session->date->format('d/m/Y') }} @if($session->time){{ $session->time->format('H:i') }}@endif
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@if($selectedSession)
<!-- Información de la Sesión Activa -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="text-white mb-1">
                            <i class="fe fe-qr-code mr-2"></i>
                            {{ $selectedSession->title }}
                        </h4>
                        <p class="text-white-50 mb-0">
                            <i class="fe fe-calendar mr-1"></i>
                            {{ $selectedSession->date->format('l, d \d\e F Y') }}
                            @if($selectedSession->time)
                            <i class="fe fe-clock ml-3 mr-1"></i>
                            {{ $selectedSession->time->format('H:i') }}
                            @endif
                        </p>
                        @if($selectedSession->description)
                        <small class="text-white-75">{{ $selectedSession->description }}</small>
                        @endif
                    </div>
                    <div class="col-auto">
                        <div class="text-right">
                            <span class="badge badge-light badge-lg">Escáner Activo</span>
                            <div class="small text-white-50 mt-1">
                                <i class="fe fe-wifi"></i> Sesión Activa
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estadísticas de Escaneo -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <span class="h3 mb-0 text-success">{{ $scanStats->total_scans }}</span>
                <p class="small text-muted mb-0">Total Escaneos</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <span class="h3 mb-0 text-primary">{{ $scanStats->successful_scans }}</span>
                <p class="small text-muted mb-0">Exitosos</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <span class="h3 mb-0 text-danger">{{ $scanStats->error_scans }}</span>
                <p class="small text-muted mb-0">Errores</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <span class="h3 mb-0 text-info">{{ $scanStats->scan_rate }}%</span>
                <p class="small text-muted mb-0">Tasa de Éxito</p>
            </div>
        </div>
    </div>
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
                            Escáner de Códigos QR
                        </h4>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-success" id="start-camera">
                            <span class="fe fe-play mr-1"></span>Iniciar Cámara
                        </button>
                        <button class="btn btn-outline-secondary" id="stop-camera" style="display: none;">
                            <span class="fe fe-square mr-1"></span>Detener
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Área de la Cámara -->
                <div class="camera-container" id="camera-container">
                    <div class="camera-placeholder">
                        <div class="mb-3">
                            <i class="fe fe-camera" style="font-size: 48px;"></i>
                        </div>
                        <h5>Cámara Desactivada</h5>
                        <p class="mb-0">Haga clic en "Iniciar Cámara" para comenzar el escaneo</p>
                    </div>
                    
                    <!-- Overlay de escaneo (oculto por defecto) -->
                    <div class="scan-overlay" id="scan-overlay" style="display: none;">
                        <div class="scan-corners top-left"></div>
                        <div class="scan-corners top-right"></div>
                        <div class="scan-corners bottom-left"></div>
                        <div class="scan-corners bottom-right"></div>
                    </div>
                </div>
                
                <!-- Controles adicionales -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Configuración de Cámara</label>
                            <select class="form-control" id="camera-select">
                                <option value="auto">Selección Automática</option>
                                <option value="rear">Cámara Trasera</option>
                                <option value="front">Cámara Frontal</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Modo de Escaneo</label>
                            <select class="form-control" id="scan-mode">
                                <option value="continuous">Continuo</option>
                                <option value="single">Un solo escaneo</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Entrada manual alternativa -->
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="fe fe-info mr-2"></i>
                                Entrada Manual
                            </h6>
                            <div class="input-group">
                                <input type="text" class="form-control" 
                                       placeholder="Escribe o escanea el código QR aquí..." 
                                       id="manual-input">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" id="manual-submit">
                                        <span class="fe fe-check"></span>
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted mt-1">
                                Usa este campo si la cámara no funciona o para entrada rápida
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Panel de Escaneos Recientes -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-header-title">
                    <i class="fe fe-activity mr-2"></i>
                    Escaneos Recientes
                </h4>
            </div>
            <div class="card-body">
                <div id="recent-scans-container">
                    @foreach($recentScans as $scan)
                    <div class="card mb-2 recent-scan scan-{{ $scan->status }}">
                        <div class="card-body py-2">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="mb-0">{{ $scan->student_name }}</h6>
                                    <small class="text-muted">
                                        <code>{{ $scan->qr_code }}</code>
                                        <span class="badge badge-{{ str_contains($scan->group, 'A') ? 'primary' : 'info' }} badge-sm ml-1">
                                            {{ $scan->group }}
                                        </span>
                                    </small>
                                </div>
                                <div class="col-auto">
                                    <div class="text-right">
                                        <span class="badge badge-{{ $scan->status == 'present' ? 'success' : ($scan->status == 'late' ? 'warning' : 'danger') }}">
                                            @if($scan->status == 'present')
                                                <i class="fe fe-check"></i> Presente
                                            @elseif($scan->status == 'late')
                                                <i class="fe fe-clock"></i> Tarde
                                            @else
                                                <i class="fe fe-x"></i> Error
                                            @endif
                                        </span>
                                        <div class="small text-muted">{{ $scan->scan_time }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    
                    @if($recentScans->isEmpty())
                    <div class="text-center text-muted py-3">
                        <i class="fe fe-qr-code fe-24 mb-2"></i>
                        <p class="mb-0">No hay escaneos recientes</p>
                        <small>Los códigos QR escaneados aparecerán aquí</small>
                    </div>
                    @endif
                </div>
                
                <!-- Botón para ver historial completo -->
                <div class="text-center mt-3">
                    <button class="btn btn-outline-primary btn-sm" onclick="window.location.href='{{ route('attendances.history') }}'">
                        Ver Historial Completo
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Acciones Rápidas -->
        <div class="card mt-3">
            <div class="card-body">
                <h6 class="card-title">Acciones Rápidas</h6>
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary btn-block mb-2" onclick="window.location.href='{{ route('attendances.register', ['session_id' => $selectedSession->id]) }}'">
                        <span class="fe fe-edit mr-2"></span>Registro Manual
                    </button>
                    <button class="btn btn-outline-info btn-block mb-2" id="export-session">
                        <span class="fe fe-download mr-2"></span>Exportar Sesión
                    </button>
                    <button class="btn btn-outline-warning btn-block" id="finish-session">
                        <span class="fe fe-check-circle mr-2"></span>Finalizar Sesión
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Escaneo -->
<div class="modal fade" id="scanConfirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fe fe-check-circle text-success mr-2"></i>
                    Código QR Escaneado
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="scan-result-content">
                    <!-- Contenido dinámico del resultado -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" id="confirm-attendance">Confirmar Asistencia</button>
            </div>
        </div>
    </div>
</div>

@else
<!-- Sin sesiones activas -->
<div class="row">
    <div class="col-12">
        <div class="card text-center">
            <div class="card-body py-5">
                <span class="fe fe-calendar-x fe-48 text-muted mb-3 d-block"></span>
                <h4 class="text-muted">No hay sesiones activas</h4>
                <p class="text-muted mb-4">
                    No se encontraron sesiones activas para escanear códigos QR.<br>
                    Cree una nueva sesión o active una existente para continuar.
                </p>
                <a href="{{ route('sessions.create') }}" class="btn btn-primary">
                    <span class="fe fe-plus mr-1"></span>Crear Nueva Sesión
                </a>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('additional-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let isScanning = false;
    let scanMode = 'continuous';
    
    // Token CSRF para las peticiones AJAX
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const sessionId = {{ $selectedSession ? $selectedSession->id : 'null' }};
    
    // Elementos del DOM
    const startCameraBtn = document.getElementById('start-camera');
    const stopCameraBtn = document.getElementById('stop-camera');
    const cameraContainer = document.getElementById('camera-container');
    const scanOverlay = document.getElementById('scan-overlay');
    const manualInput = document.getElementById('manual-input');
    const manualSubmit = document.getElementById('manual-submit');
    const recentScansContainer = document.getElementById('recent-scans-container');
    
    // Verificar que hay sesión seleccionada
    if (!sessionId) {
        alert('No hay sesión seleccionada para escanear códigos QR');
        return;
    }
    
    // Iniciar cámara
    if (startCameraBtn) {
        startCameraBtn.addEventListener('click', function() {
            startCamera();
        });
    }
    
    // Detener cámara
    if (stopCameraBtn) {
        stopCameraBtn.addEventListener('click', function() {
            stopCamera();
        });
    }
    
    // Entrada manual
    if (manualSubmit) {
        manualSubmit.addEventListener('click', function() {
            const qrCode = manualInput.value.trim();
            if (qrCode) {
                processQRCode(qrCode);
                manualInput.value = '';
            }
        });
    }
    
    if (manualInput) {
        manualInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                manualSubmit.click();
            }
        });
    }
    
    function startCamera() {
        // Simulación de activación de cámara
        cameraContainer.innerHTML = `
            <div class="camera-placeholder">
                <div class="mb-3">
                    <i class="fe fe-camera" style="font-size: 48px; color: #28a745;"></i>
                </div>
                <h5 style="color: #28a745;">Cámara Activa</h5>
                <p class="mb-0">Apunte el código QR hacia la cámara</p>
                <div class="mt-3">
                    <div class="spinner-border text-success" role="status">
                        <span class="sr-only">Escaneando...</span>
                    </div>
                </div>
            </div>
        `;
        
        scanOverlay.style.display = 'block';
        startCameraBtn.style.display = 'none';
        stopCameraBtn.style.display = 'inline-block';
        isScanning = true;
    }
    
    function stopCamera() {
        cameraContainer.innerHTML = `
            <div class="camera-placeholder">
                <div class="mb-3">
                    <i class="fe fe-camera" style="font-size: 48px;"></i>
                </div>
                <h5>Cámara Desactivada</h5>
                <p class="mb-0">Haga clic en "Iniciar Cámara" para comenzar el escaneo</p>
            </div>
        `;
        
        scanOverlay.style.display = 'none';
        startCameraBtn.style.display = 'inline-block';
        stopCameraBtn.style.display = 'none';
        isScanning = false;
    }
    
    function processQRCode(qrCode) {
        console.log('Procesando QR:', qrCode);
        
        // Enviar al servidor para procesar
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
                // Mostrar modal de confirmación
                showScanResult(data.data);
                
                // Agregar a escaneos recientes
                addRecentScan(data.data);
                
                // Actualizar estadísticas
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
        });
    }
    
    function showScanResult(scanData) {
        const modal = document.getElementById('scanConfirmModal');
        const content = document.getElementById('scan-result-content');
        
        let statusBadge, statusIcon, statusText;
        
        if (scanData.status === 'Presente') {
            statusBadge = 'success';
            statusIcon = 'check-circle';
            statusText = 'Presente';
        } else if (scanData.status === 'Tarde') {
            statusBadge = 'warning';
            statusIcon = 'clock';
            statusText = 'Tarde';
        } else {
            statusBadge = 'danger';
            statusIcon = 'x-circle';
            statusText = 'Error';
        }
        
        content.innerHTML = `
            <div class="avatar avatar-lg mb-3">
                <span class="avatar-title rounded-circle bg-soft-${scanData.group === 'Grupo A' ? 'primary' : 'info'} text-${scanData.group === 'Grupo A' ? 'primary' : 'info'}">
                    ${scanData.student_name.charAt(0)}
                </span>
            </div>
            <h5>${scanData.student_name}</h5>
            <p class="text-muted">
                <code>${scanData.qr_code}</code><br>
                <span class="badge badge-${scanData.group === 'Grupo A' ? 'primary' : 'info'}">${scanData.group}</span>
            </p>
            <div class="alert alert-${statusBadge}">
                <i class="fe fe-${statusIcon} mr-2"></i>
                Estado: <strong>${statusText}</strong>
            </div>
            <small class="text-muted">
                Registrado a las ${scanData.marked_at}
            </small>
        `;
        
        $(modal).modal('show');
    }
    
    function addRecentScan(scanData) {
        const statusClass = scanData.status === 'Presente' ? 'success' : (scanData.status === 'Tarde' ? 'late' : 'error');
        const statusIcon = scanData.status === 'Presente' ? 'check' : (scanData.status === 'Tarde' ? 'clock' : 'x');
        const badgeClass = scanData.status === 'Presente' ? 'success' : (scanData.status === 'Tarde' ? 'warning' : 'danger');
        
        const scanElement = document.createElement('div');
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
        
        // Eliminar mensaje de "no hay escaneos" si existe
        const noScansMessage = recentScansContainer.querySelector('.text-center.text-muted');
        if (noScansMessage) {
            noScansMessage.remove();
        }
        
        recentScansContainer.insertBefore(scanElement, recentScansContainer.firstChild);
        
        // Mantener solo los últimos 5 escaneos
        const scans = recentScansContainer.querySelectorAll('.recent-scan');
        if (scans.length > 5) {
            scans[scans.length - 1].remove();
        }
    }
    
    function updateScanStats() {
        // Incrementar contador de escaneos exitosos
        const totalScans = document.querySelector('.text-success.h3');
        const successfulScans = document.querySelector('.text-primary.h3');
        
        if (totalScans && successfulScans) {
            const currentTotal = parseInt(totalScans.textContent) + 1;
            const currentSuccessful = parseInt(successfulScans.textContent) + 1;
            
            totalScans.textContent = currentTotal;
            successfulScans.textContent = currentSuccessful;
            
            // Actualizar tasa de éxito
            const successRate = document.querySelector('.text-info.h3');
            if (successRate) {
                const rate = Math.round((currentSuccessful / currentTotal) * 100);
                successRate.textContent = rate + '%';
            }
        }
    }
    
    function showToast(message, type) {
        // Implementación simple de toast
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
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
    
    // Configuración de modo de escaneo
    const scanModeSelect = document.getElementById('scan-mode');
    if (scanModeSelect) {
        scanModeSelect.addEventListener('change', function() {
            scanMode = this.value;
        });
    }
    
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
                // Aquí iría la lógica para finalizar la sesión
                showToast('Sesión finalizada exitosamente', 'success');
            }
        });
    }
});
</script>
@endsection