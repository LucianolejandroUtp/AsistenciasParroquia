/* QR utilities copied/adapted from resources/views/students/qr-codes.blade.php
   Purpose: provide generateQRCode, downloadQR, printQR globally so AJAX-injected partials can use them
*/
(function () {
    // Configuración QR (mirar QR_CONFIG en la vista original)
    const QR_CONFIG = {
        errorCorrectionLevel: 'M',
        type: 'image/png',
        quality: 0.92,
        margin: 1,
        scale: 4,
        color: { dark: '#000000', light: '#FFFFFF' }
    };

    // Exponer funciones globalmente
    window.generateQRCode = function (studentId, qrCodeText, delay = 0) {
        setTimeout(() => {
            try {
                const canvas = document.getElementById(`qr-canvas-${studentId}`);
                const loading = document.getElementById(`qr-loading-${studentId}`);
                const error = document.getElementById(`qr-error-${studentId}`);

                if (!canvas || !qrCodeText) {
                    if (loading) loading.style.display = 'none';
                    if (error) error.style.display = 'block';
                    return;
                }

                // Fallback: prefer QRCode.toCanvas if available, else use qrcode-generator style
                if (typeof QRCode !== 'undefined' && typeof QRCode.toCanvas === 'function') {
                    QRCode.toCanvas(canvas, qrCodeText, QR_CONFIG, function (err) {
                        if (err) {
                            console.error('Error generando QR:', err);
                            if (loading) loading.style.display = 'none';
                            if (error) error.style.display = 'block';
                        } else {
                            if (loading) loading.style.display = 'none';
                            canvas.style.display = 'block';
                            if (error) error.style.display = 'none';
                        }
                    });
                } else if (typeof qrcode !== 'undefined') {
                    try {
                        const qr = qrcode(4, 'M');
                        qr.addData(qrCodeText);
                        qr.make();
                        const ctx = canvas.getContext('2d');
                        const size = 200;
                        canvas.width = size;
                        canvas.height = size;
                        const cellSize = size / qr.getModuleCount();
                        ctx.fillStyle = '#FFFFFF';
                        ctx.fillRect(0, 0, size, size);
                        for (let r = 0; r < qr.getModuleCount(); r++) {
                            for (let c = 0; c < qr.getModuleCount(); c++) {
                                ctx.fillStyle = qr.isDark(r, c) ? '#000000' : '#FFFFFF';
                                ctx.fillRect(c * cellSize, r * cellSize, cellSize, cellSize);
                            }
                        }
                        if (loading) loading.style.display = 'none';
                        canvas.style.display = 'block';
                        if (error) error.style.display = 'none';
                    } catch (err) {
                        console.error('Fallback QR error', err);
                        if (loading) loading.style.display = 'none';
                        if (error) error.style.display = 'block';
                    }
                } else {
                    // No library available
                    if (loading) loading.style.display = 'none';
                    if (error) error.style.display = 'block';
                    console.warn('No QR libraries available (QRCode or qrcode)');
                }
            } catch (err) {
                console.error('generateQRCode error', err);
            }
        }, delay);
    };

    window.downloadQR = function (studentId, studentName) {
        try {
            const canvas = document.getElementById(`qr-canvas-${studentId}`);
            if (!canvas) return;
            const link = document.createElement('a');
            link.download = `QR_${(studentName || '').replace(/\s+/g, '_')}.png`;
            link.href = canvas.toDataURL();
            link.click();
        } catch (err) {
            console.error('downloadQR error', err);
        }
    };

    window.printQR = function (studentId, studentName) {
        try {
            const canvas = document.getElementById(`qr-canvas-${studentId}`);
            const btn = document.getElementById(`btn-print-${studentId}`) || null;
            const spinner = document.getElementById(`btn-print-spinner-${studentId}`) || null;
            const maxWait = 5000; // ms
            if (!canvas) {
                console.warn('printQR: canvas no encontrado', studentId);
                return;
            }

            if (btn) btn.disabled = true;
            if (spinner) spinner.style.display = 'inline-block';

            const start = Date.now();
            (function waitForCanvas() {
                const isReady = canvas && canvas.width > 0 && canvas.style.display !== 'none';
                if (isReady) {
                    proceedPrint();
                } else if (Date.now() - start < maxWait) {
                    setTimeout(waitForCanvas, 100);
                } else {
                    alert('El código QR no está listo para imprimir. Intenta regenerarlo o recarga la página.');
                    if (btn) btn.disabled = false;
                    if (spinner) spinner.style.display = 'none';
                }
            })();

            function proceedPrint() {
                try {
                    const dataURL = canvas.toDataURL();
                    const printWindow = window.open('', '_blank');
                    if (!printWindow) { alert('Permite popups para imprimir.'); if (btn) btn.disabled = false; if (spinner) spinner.style.display = 'none'; return; }

                    const html = `<!doctype html><html><head><meta charset="utf-8"><title>QR - ${studentName}</title>` +
                        `<style>body{font-family:Arial, sans-serif;text-align:center;padding:20px;}img{max-width:320px;width:100%;}</style></head><body>` +
                        `<img src="${dataURL}" alt="QR" /><div style="margin-top:8px;font-size:14px;">${studentName}</div></body></html>`;

                    printWindow.document.open();
                    printWindow.document.write(html);
                    printWindow.document.close();

                    // Esperar a que la imagen esté lista en la nueva ventana
                    const img = printWindow.document.querySelector('img');
                    if (!img) {
                        printWindow.print();
                        cleanup();
                        return;
                    }

                    if (img.complete) {
                        printWindow.focus();
                        printWindow.print();
                        cleanup();
                    } else {
                        img.addEventListener('load', () => { printWindow.focus(); printWindow.print(); cleanup(); });
                        img.addEventListener('error', () => { alert('Error cargando la imagen para imprimir'); cleanup(); });
                    }
                } catch (err) {
                    console.error('printQR proceedPrint error', err);
                    if (btn) btn.disabled = false;
                    if (spinner) spinner.style.display = 'none';
                }
            }

            function cleanup() {
                if (btn) btn.disabled = false;
                if (spinner) spinner.style.display = 'none';
            }

        } catch (err) {
            console.error('printQR error', err);
        }
    };

})();
