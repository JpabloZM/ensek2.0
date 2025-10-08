/**
 * Script para manejar la selección entre agendamiento directo y agendamiento con solicitud previa
 * Este script es crítico para asegurar que se muestre el modal correcto
 */

document.addEventListener("DOMContentLoaded", function() {
    console.log("🔄 Schedule Type Selector iniciado");
    
    // Función principal para manejar la selección de tipo de agendamiento
    function setupScheduleTypeSelection() {
        console.log("🛠️ Configurando selector de tipo de agendamiento");
        
        // Verificar que los modales existen
        const modalSolicitud = document.getElementById("newScheduleModal");
        const modalDirecto = document.getElementById("newDirectScheduleModal");
        
        if (!modalSolicitud) {
            console.error("❌ Modal de solicitud existente no encontrado");
        }
        
        if (!modalDirecto) {
            console.error("❌ Modal de agendamiento directo no encontrado");
        }
        
        // Método global para abrir el modal directo desde cualquier parte
        window.abrirModalDirecto = function(data = {}) {
            console.log("🚀 Ejecutando función global abrirModalDirecto()", data);
            
            if (!modalDirecto) {
                console.error("❌ Modal directo no encontrado en la función global");
                return false;
            }
            
            // Preparar datos para el modal si se proporcionan
            if (data.technicianId) {
                const techSelect = document.getElementById("direct_technician_id");
                if (techSelect) techSelect.value = data.technicianId;
            }
            
            if (data.date) {
                const dateInput = document.getElementById("direct_scheduled_date");
                if (dateInput) dateInput.value = data.date;
            }
            
            if (data.startTime) {
                const startInput = document.getElementById("direct_start_time");
                if (startInput) startInput.value = data.startTime;
            }
            
            if (data.endTime) {
                const endInput = document.getElementById("direct_end_time");
                if (endInput) endInput.value = data.endTime;
            }
            
            // Intentar abrir el modal de múltiples formas para mayor compatibilidad
            try {
                // Método 1: Bootstrap 5 nativo
                const directModal = new bootstrap.Modal(modalDirecto);
                directModal.show();
                console.log("✅ Modal abierto con método Bootstrap 5 nativo");
                return true;
            } catch (error) {
                console.warn("⚠️ Error al abrir modal con método nativo:", error);
                
                try {
                    // Método 2: jQuery si está disponible
                    if (window.jQuery) {
                        jQuery(modalDirecto).modal('show');
                        console.log("✅ Modal abierto con método jQuery");
                        return true;
                    }
                } catch (jqError) {
                    console.warn("⚠️ Error al abrir modal con jQuery:", jqError);
                }
                
                try {
                    // Método 3: Disparar evento en botón auxiliar
                    const btnAux = document.getElementById("btnOpenDirectModal");
                    if (btnAux) {
                        btnAux.click();
                        console.log("✅ Modal abierto con botón auxiliar");
                        return true;
                    }
                } catch (btnError) {
                    console.warn("⚠️ Error al usar botón auxiliar:", btnError);
                }
                
                console.error("❌ No se pudo abrir el modal directo");
                return false;
            }
        };
        
        // Función para manejar click en botón "Crear Servicio Nuevo" en la selección SweetAlert
        window.handleNewDirectService = function(data = {}) {
            console.log("🔄 Manejando selección de servicio nuevo directo", data);
            
            // Cerrar el diálogo de SweetAlert si está abierto
            if (window.Swal && Swal.isVisible()) {
                Swal.close();
            }
            
            // Intentar abrir el modal con los datos proporcionados
            return window.abrirModalDirecto(data);
        };
        
        // Configurar un botón de prueba en la página para abrir el modal directo
        const testButton = document.getElementById("testDirectModalBtn");
        if (testButton) {
            testButton.addEventListener("click", function() {
                console.log("🧪 Botón de prueba clickeado");
                window.abrirModalDirecto();
            });
        }
    }
    
    // Iniciar la configuración con un pequeño delay para asegurar que todos los elementos están cargados
    setTimeout(setupScheduleTypeSelection, 500);
});