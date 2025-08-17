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
<!-- Información de la Sesión Activa -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="text-white mb-1">
                            <i class="fe fe-qr-code mr-2"></i>
                            {{ $mockSession->title }}
                        </h4>
                        <p class="text-white-50 mb-0">
                            <i class="fe fe-calendar mr-1"></i>
                            {{ \Carbon\Carbon::parse($mockSession->date)->format('l, d \d\e F Y') }}
                            <i class="fe fe-clock ml-3 mr-1"></i>
                            {{ \Carbon\Carbon::createFromFormat('H:i', $mockSession->time)->format('H:i A') }}
                        </p>
                    </div>
                    <div class="col-auto">
                        <div class="text-right">
                            <span class="badge badge-light badge-lg">Escáner Activo</span>
                            <div class="small text-white-50 mt-1">
                                <i class="fe fe-wifi"></i> Conectado
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
                                        <span class="badge badge-{{ $scan->group == 'A' ? 'primary' : 'info' }} badge-sm ml-1">
                                            Grupo {{ $scan->group }}
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
                    <button class="btn btn-outline-primary btn-block mb-2" onclick="window.location.href='{{ route('attendances.register') }}'">
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
@endsection

@section('additional-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let isScanning = false;
    let scanMode = 'continuous';
    
    // Elementos del DOM
    const startCameraBtn = document.getElementById('start-camera');
    const stopCameraBtn = document.getElementById('stop-camera');
    const cameraContainer = document.getElementById('camera-container');
    const scanOverlay = document.getElementById('scan-overlay');
    const manualInput = document.getElementById('manual-input');
    const manualSubmit = document.getElementById('manual-submit');
    const recentScansContainer = document.getElementById('recent-scans-container');
    
    // Iniciar cámara
    startCameraBtn.addEventListener('click', function() {
        startCamera();
    });
    
    // Detener cámara
    stopCameraBtn.addEventListener('click', function() {
        stopCamera();
    });
    
    // Entrada manual
    manualSubmit.addEventListener('click', function() {
        const qrCode = manualInput.value.trim();
        if (qrCode) {
            processQRCode(qrCode);
            manualInput.value = '';
        }
    });
    
    manualInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            manualSubmit.click();
        }
    });
    
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
        
        // Simulación de escaneo automático después de 3 segundos
        setTimeout(() => {
            if (isScanning) {
                simulateQRScan();
            }
        }, 3000);
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
    
    function simulateQRScan() {
        // Simulación de códigos QR para demo
        const sampleCodes = [
            'A-PEDRO-MAR-LOP',
            'B-LUCIA-SAN-GOM', 
            'A-MIGUEL-RUI-VAS',
            'B-SOFIA-CAS-HER'
        ];
        
        const randomCode = sampleCodes[Math.floor(Math.random() * sampleCodes.length)];
        processQRCode(randomCode);
    }
    
    function processQRCode(qrCode) {
        console.log('Procesando QR:', qrCode);
        
        // Simulación de procesamiento
        const studentNames = {
            'A-ANTONY-ALF-VILCH': 'Antony Alexander Alférez Vilchez',
            'A-MARIA-GON-PER': 'María Elena González Pérez',
            'B-CARLOS-RAM-SIL': 'Carlos Eduardo Ramírez Silva',
            'B-ANA-MEN-CAS': 'Ana Sofía Mendoza Castro',
            'A-PEDRO-MAR-LOP': 'Pedro Martín López',
            'B-LUCIA-SAN-GOM': 'Lucía Sánchez Gómez',
            'A-MIGUEL-RUI-VAS': 'Miguel Ruiz Vasco',
            'B-SOFIA-CAS-HER': 'Sofía Castro Herrera'
        };
        
        const studentName = studentNames[qrCode] || 'Estudiante Desconocido';
        const group = qrCode.startsWith('A-') ? 'A' : 'B';
        const isLate = Math.random() > 0.8; // 20% de probabilidad de llegar tarde
        const status = qrCode in studentNames ? (isLate ? 'late' : 'present') : 'error';
        
        // Mostrar modal de confirmación
        showScanResult(studentName, qrCode, group, status);
        
        // Agregar a escaneos recientes
        addRecentScan(studentName, qrCode, group, status);
        
        // Actualizar estadísticas
        updateScanStats();
        
        // Si está en modo continuo, continuar escaneando
        if (scanMode === 'continuous' && isScanning) {
            setTimeout(() => {
                if (isScanning) {
                    simulateQRScan();
                }
            }, 5000);
        }
    }
    
    function showScanResult(studentName, qrCode, group, status) {
        const modal = document.getElementById('scanConfirmModal');
        const content = document.getElementById('scan-result-content');
        
        let statusBadge, statusIcon, statusText;
        
        if (status === 'present') {
            statusBadge = 'success';
            statusIcon = 'check-circle';
            statusText = 'Presente';
        } else if (status === 'late') {
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
                <span class="avatar-title rounded-circle bg-soft-${group === 'A' ? 'primary' : 'info'} text-${group === 'A' ? 'primary' : 'info'}">
                    ${studentName.charAt(0)}
                </span>
            </div>
            <h5>${studentName}</h5>
            <p class="text-muted">
                <code>${qrCode}</code><br>
                <span class="badge badge-${group === 'A' ? 'primary' : 'info'}">Grupo ${group}</span>
            </p>
            <div class="alert alert-${statusBadge}">
                <i class="fe fe-${statusIcon} mr-2"></i>
                Estado: <strong>${statusText}</strong>
            </div>
            <small class="text-muted">
                Escaneado el ${new Date().toLocaleString('es-ES')}
            </small>
        `;
        
        $(modal).modal('show');
    }
    
    function addRecentScan(studentName, qrCode, group, status) {
        const scanElement = document.createElement('div');
        scanElement.className = `card mb-2 recent-scan scan-${status}`;
        scanElement.innerHTML = `
            <div class="card-body py-2">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="mb-0">${studentName}</h6>
                        <small class="text-muted">
                            <code>${qrCode}</code>
                            <span class="badge badge-${group === 'A' ? 'primary' : 'info'} badge-sm ml-1">
                                Grupo ${group}
                            </span>
                        </small>
                    </div>
                    <div class="col-auto">
                        <div class="text-right">
                            <span class="badge badge-${status === 'present' ? 'success' : (status === 'late' ? 'warning' : 'danger')}">
                                <i class="fe fe-${status === 'present' ? 'check' : (status === 'late' ? 'clock' : 'x')}"></i>
                                ${status === 'present' ? 'Presente' : (status === 'late' ? 'Tarde' : 'Error')}
                            </span>
                            <div class="small text-muted">${new Date().toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'})}</div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        recentScansContainer.insertBefore(scanElement, recentScansContainer.firstChild);
        
        // Mantener solo los últimos 5 escaneos
        const scans = recentScansContainer.querySelectorAll('.recent-scan');
        if (scans.length > 5) {
            scans[scans.length - 1].remove();
        }
    }
    
    function updateScanStats() {
        // Actualizar estadísticas en tiempo real
        console.log('Actualizando estadísticas de escaneo...');
    }
    
    // Configuración de modo de escaneo
    document.getElementById('scan-mode').addEventListener('change', function() {
        scanMode = this.value;
    });
    
    // Acciones rápidas
    document.getElementById('export-session').addEventListener('click', function() {
        alert('Exportando datos de la sesión...');
    });
    
    document.getElementById('finish-session').addEventListener('click', function() {
        if (confirm('¿Finalizar la sesión actual? No podrá registrar más asistencias.')) {
            alert('Sesión finalizada exitosamente');
        }
    });
});
</script>
@endsection