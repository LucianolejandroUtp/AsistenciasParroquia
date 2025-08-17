import './bootstrap';

// Sistema de Asistencias - Primera Comunión
// Funcionalidades principales del sistema

// Configuración global
window.AsistenciasApp = {
    // Configuración del escaneo QR
    qrConfig: {
        fps: 10,
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0
    },
    
    // Estados de asistencia
    attendanceStates: {
        PRESENT: 'present',
        ABSENT: 'absent', 
        JUSTIFIED: 'justified'
    },
    
    // URLs de la API (se configurarán desde las vistas)
    apiRoutes: {},
    
    // Función para mostrar notificaciones
    showNotification: function(message, type = 'success') {
        // Crear elemento de notificación
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            <i class="fe fe-${type === 'success' ? 'check-circle' : 'alert-circle'} mr-2"></i>
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove después de 5 segundos
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    },
    
    // Función para formatear fecha
    formatDate: function(date) {
        return new Intl.DateTimeFormat('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }).format(new Date(date));
    },
    
    // Función para confirmar acciones
    confirmAction: function(message, callback) {
        if (confirm(message)) {
            callback();
        }
    }
};

// Inicialización cuando el documento esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Configurar CSRF token para todas las peticiones AJAX
    const token = document.querySelector('meta[name="csrf-token"]');
    if (token) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
    }
    
    // Inicializar tooltips de Bootstrap si existen
    if (typeof $!== 'undefined' && $.fn.tooltip) {
        $('[data-toggle="tooltip"]').tooltip();
    }
    
    // Configurar auto-refresh para notificaciones (cada 30 segundos)
    setInterval(() => {
        // Aquí se puede implementar lógica para actualizar notificaciones
        // por ejemplo, verificar nuevas asistencias, alertas, etc.
    }, 30000);
    
    console.log('Sistema de Asistencias - Primera Comunión inicializado');
});
