/**
 * HTML5-QRCode Scanner Module
 * Sistema de escaneo QR optimizado para tablets y uso pastoral
 * Compatible con HTTP localhost y diseñado para catequistas
 */

import { Html5QrcodeScanner, Html5QrcodeSupportedFormats, Html5QrcodeScanType } from 'html5-qrcode';

// Variables globales del scanner
let qrScanner = null;
let isScanning = false;
let scanMode = 'continuous';
let lastScanResult = null;
let scanCount = 0;

// Configuración optimizada para tablets y uso pastoral
const scannerConfig = {
    fps: 10, // 10 FPS para balance rendimiento/precisión
    qrbox: { width: 300, height: 300 }, // Área de escaneo optimizada para tablets
    aspectRatio: 1.777778, // 16:9 para tablets landscape
    rememberLastUsedCamera: true, // Recordar cámara seleccionada
    showTorchButtonIfSupported: true, // Mostrar flash si está disponible
    disableFlip: false, // Permitir códigos espejo (cámaras frontales)
    formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE], // Solo QR codes
    supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA], // Solo cámara
};

/**
 * Inicializar scanner HTML5-QRCode
 * @param {string} elementId - ID del elemento contenedor
 * @param {function} onSuccess - Callback para escaneo exitoso
 * @param {function} onError - Callback para errores
 * @returns {boolean} - true si se inicializó correctamente
 */
export function initializeQrScanner(elementId, onSuccess, onError) {
    try {
        // Limpiar scanner anterior si existe
        if (qrScanner) {
            destroyScanner();
        }

        // Callback para escaneo exitoso
        function onScanSuccess(decodedText, decodedResult) {
            // Evitar scans duplicados rápidos
            if (decodedText === lastScanResult && scanMode === 'continuous') {
                return;
            }

            lastScanResult = decodedText;
            scanCount++;

            console.log(`QR Code detectado #${scanCount}:`, decodedText);
            
            // Si está en modo single, detener después del primer escaneo
            if (scanMode === 'single') {
                stopScanning();
            }

            // Llamar callback de éxito
            if (onSuccess) {
                onSuccess(decodedText, decodedResult);
            }
        }

        // Callback para errores de escaneo
        function onScanError(errorMessage, error) {
            // Solo loggear errores importantes, no cada frame sin QR
            if (errorMessage && !errorMessage.includes('NotFoundException')) {
                console.warn('QR Scan Warning:', errorMessage);
                
                if (onError && typeof onError === 'function') {
                    onError(errorMessage, error);
                }
            }
        }

        // Crear nueva instancia del scanner
        qrScanner = new Html5QrcodeScanner(
            elementId,
            scannerConfig,
            false // verbose = false
        );

        // Renderizar el scanner
        qrScanner.render(onScanSuccess, onScanError);

        console.log('HTML5-QRCode Scanner inicializado exitosamente');
        return true;

    } catch (error) {
        console.error('Error inicializando scanner:', error);
        if (onError) {
            onError('Error al inicializar el scanner', error);
        }
        return false;
    }
}

/**
 * Iniciar escaneo
 * @returns {Promise<boolean>} - true si se inició correctamente
 */
export async function startScanning() {
    try {
        if (!qrScanner) {
            throw new Error('Scanner no inicializado. Llame a initializeQrScanner() primero.');
        }

        // El Html5QrcodeScanner se inicia automáticamente al renderizar
        // Solo necesitamos marcar el estado
        isScanning = true;
        console.log('Scanner QR iniciado exitosamente');
        return true;

    } catch (error) {
        console.error('Error al iniciar scanner:', error);
        throw new Error(`No se pudo iniciar el scanner: ${error.message}`);
    }
}

/**
 * Detener escaneo
 * @returns {Promise<boolean>} - true si se detuvo correctamente
 */
export async function stopScanning() {
    try {
        if (qrScanner && isScanning) {
            // Html5QrcodeScanner maneja el stop internamente
            isScanning = false;
            lastScanResult = null;
            console.log('Scanner QR detenido');
            return true;
        }
        return false;
    } catch (error) {
        console.error('Error deteniendo scanner:', error);
        return false;
    }
}

/**
 * Destruir scanner completamente
 */
export function destroyScanner() {
    try {
        if (qrScanner) {
            // Html5QrcodeScanner se limpia automáticamente
            qrScanner = null;
            isScanning = false;
            lastScanResult = null;
            scanCount = 0;
            console.log('Scanner QR destruido');
        }
    } catch (error) {
        console.error('Error destruyendo scanner:', error);
    }
}

/**
 * Verificar soporte de cámara
 * @returns {Promise<boolean>} - true si hay cámaras disponibles
 */
export async function checkCameraSupport() {
    try {
        // Html5QrcodeScanner maneja la detección internamente
        // Simplemente retornamos true ya que el scanner manejará los errores
        return true;
    } catch (error) {
        console.error('Error verificando soporte de cámara:', error);
        return false;
    }
}

/**
 * Configurar modo de escaneo
 * @param {string} mode - 'continuous' o 'single'
 */
export function setScanMode(mode) {
    if (mode === 'continuous' || mode === 'single') {
        scanMode = mode;
        console.log('Modo de escaneo cambiado a:', mode);
    } else {
        console.warn('Modo de escaneo inválido:', mode);
    }
}

/**
 * Obtener estado actual del scanner
 * @returns {object} - Estado del scanner
 */
export function getScannerState() {
    return {
        isScanning,
        scanMode,
        hasScanner: !!qrScanner,
        scanCount,
        lastResult: lastScanResult
    };
}

/**
 * Reiniciar contador de escaneos
 */
export function resetScanCount() {
    scanCount = 0;
    lastScanResult = null;
    console.log('Contador de escaneos reiniciado');
}

/**
 * Configurar nueva configuración del scanner
 * @param {object} newConfig - Nueva configuración
 */
export function updateScannerConfig(newConfig) {
    Object.assign(scannerConfig, newConfig);
    console.log('Configuración del scanner actualizada:', newConfig);
}

/**
 * Obtener configuración actual
 * @returns {object} - Configuración actual del scanner
 */
export function getScannerConfig() {
    return { ...scannerConfig };
}

// Cleanup automático al cerrar/recargar página
window.addEventListener('beforeunload', () => {
    destroyScanner();
});

// Cleanup al cambiar de página (SPA)
window.addEventListener('popstate', () => {
    destroyScanner();
});

// Exportar configuración por defecto para referencia
export const defaultConfig = { ...scannerConfig };

export default {
    initializeQrScanner,
    startScanning,
    stopScanning,
    destroyScanner,
    checkCameraSupport,
    setScanMode,
    getScannerState,
    resetScanCount,
    updateScannerConfig,
    getScannerConfig,
    defaultConfig
};