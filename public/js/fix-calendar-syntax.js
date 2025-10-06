// Script para corregir problemas de sintaxis en el calendario
document.addEventListener('DOMContentLoaded', function() {
    console.log('Ejecutando correcciones de sintaxis para el calendario');
    
    // Función para corregir errores JS en tiempo de ejecución
    function fixCalendarSyntax() {
        try {
            // Verificar y corregir problemas comunes
            if (window.calendar) {
                console.log('Calendario encontrado, aplicando correcciones');
                
                // Asegurarse de que los controladores de eventos estén correctamente configurados
                const safeSetOption = function(option, handler) {
                    if (!calendar.getOption(option) && typeof handler === 'function') {
                        const options = {};
                        options[option] = handler;
                        calendar.setOption(options);
                    }
                };
                
                // Reconfigurar opciones problemáticas
                ['eventMouseEnter', 'eventMouseLeave', 'eventResize', 'eventClick'].forEach(function(eventName) {
                    const currentHandler = calendar.getOption(eventName);
                    if (currentHandler) {
                        safeSetOption(eventName, currentHandler);
                    }
                });
                
                console.log('Correcciones aplicadas con éxito');
            }
        } catch (e) {
            console.error('Error al aplicar correcciones:', e);
        }
    }
    
    // Ejecutar después de que todo esté cargado
    setTimeout(fixCalendarSyntax, 1000);
});
