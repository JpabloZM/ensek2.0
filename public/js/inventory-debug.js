/**
 * Debug Logger para la página de inventario
 * Este archivo es solo para depuración y puede eliminarse en producción
 */

// Sobrescribir console.log para que sea más visible
(function() {
    const originalConsoleLog = console.log;
    const originalConsoleError = console.error;
    const originalConsoleWarn = console.warn;
    
    // Crear un contador de errores
    window.errorCount = 0;
    window.warningCount = 0;
    
    // Mantener un registro de mensajes importantes
    window.debugLog = [];
    
    console.log = function(...args) {
        window.debugLog.push({type: 'log', time: new Date(), message: args.join(' ')});
        originalConsoleLog.apply(console, args);
    };
    
    console.error = function(...args) {
        window.errorCount++;
        window.debugLog.push({type: 'error', time: new Date(), message: args.join(' ')});
        originalConsoleError.apply(console, args);
    };
    
    console.warn = function(...args) {
        window.warningCount++;
        window.debugLog.push({type: 'warning', time: new Date(), message: args.join(' ')});
        originalConsoleWarn.apply(console, args);
    };
    
    // Informar sobre errores globales
    window.addEventListener('error', function(event) {
        window.errorCount++;
        window.debugLog.push({
            type: 'global-error',
            time: new Date(),
            message: event.message,
            file: event.filename,
            line: event.lineno,
            col: event.colno
        });
    });
    
    // Monitorizar promesas rechazadas sin manejar
    window.addEventListener('unhandledrejection', function(event) {
        window.errorCount++;
        window.debugLog.push({
            type: 'unhandled-promise',
            time: new Date(),
            message: event.reason.toString()
        });
    });
    
    console.log('%c[DEBUG] Sistema de monitoreo iniciado', 'background:purple; color:white; font-size: 12px');
    
    // Verificar recursos críticos
    window.setTimeout(function() {
        if (typeof Chart === 'undefined') {
            console.error('[DEBUG] No se detectó Chart.js');
        } else {
            console.log('[DEBUG] Chart.js disponible:', Chart.version);
        }
        
        const summaryChartElement = document.getElementById('inventorySummaryChart');
        if (!summaryChartElement) {
            console.error('[DEBUG] No se encontró el elemento canvas #inventorySummaryChart');
        } else {
            console.log('[DEBUG] Canvas encontrado correctamente');
        }
        
        if (typeof $ === 'undefined' || typeof jQuery === 'undefined') {
            console.error('[DEBUG] jQuery no está disponible');
        } else {
            console.log('[DEBUG] jQuery disponible:', $.fn.jquery);
        }
        
        // Verificar si hay errores globales después de un tiempo
        setTimeout(function() {
            console.log(`[DEBUG] Resumen: ${window.errorCount} errores, ${window.warningCount} advertencias`);
        }, 5000);
    }, 1000);
})();

// Verificar la carga de la página
window.addEventListener('load', function() {
    console.log('[DEBUG] Evento window.load disparado');
    
    // Verificar si hay elementos críticos cargados
    setTimeout(function() {
        const chartContainer = document.querySelector('.chart-container');
        if (chartContainer) {
            if (chartContainer.innerHTML.trim() === '') {
                console.error('[DEBUG] chart-container está vacío');
            } else if (chartContainer.innerHTML.includes('Error')) {
                console.warn('[DEBUG] chart-container muestra un error');
            } else {
                console.log('[DEBUG] chart-container parece tener contenido válido');
            }
        }
    }, 2000);
});
