/**
 * Calendar Selection Handler
 * Este script garantiza que la selección de rango en el calendario se traduzca correctamente
 * a los campos del formulario de agendamiento.
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔄 Inicializando manejador de selección de calendario');
    
    // Escuchar al evento personalizado para la preparación del modal directo
    document.addEventListener('prepareDirectModal', function(event) {
        console.log('📅 Evento prepareDirectModal recibido', event.detail);
        
        if (event.detail) {
            // Guardar datos temporales para acceso posterior
            window.lastCalendarSelection = event.detail;
        }
    });
    
    // Función especial para manejar selección de 8am a 10am
    function handle8To10Selection(formId) {
        console.log('🔍 Comprobando selección 8-10 para formulario:', formId);
        
        // Elementos según el tipo de formulario
        const dateInput = document.getElementById(formId === 'direct' ? 'direct_scheduled_date' : 'scheduled_date');
        const startTimeInput = document.getElementById(formId === 'direct' ? 'direct_start_time' : null);
        const endTimeInput = document.getElementById(formId === 'direct' ? 'direct_end_time' : 'end_time');
        const durationInput = document.getElementById(formId === 'direct' ? 'direct_duration' : 'duration');
        
        if (!dateInput || !endTimeInput || !durationInput) {
            console.warn('⚠️ No se encontraron todos los campos necesarios');
            return;
        }
        
        // Obtener fecha/hora actual
        let startDateTime;
        
        if (formId === 'direct' && startTimeInput) {
            // Formulario directo con campos separados
            try {
                startDateTime = new Date(`${dateInput.value}T${startTimeInput.value}`);
            } catch (e) {
                console.error('❌ Error al parsear fecha/hora', e);
                return;
            }
        } else {
            // Formulario con datetime-local
            try {
                startDateTime = new Date(dateInput.value);
            } catch (e) {
                console.error('❌ Error al parsear fecha/hora', e);
                return;
            }
        }
        
        const hours = startDateTime.getHours();
        const minutes = startDateTime.getMinutes();
        
        // Si es una selección que comienza a las 8:00, ajustar a 2 horas
        if (hours === 8 && minutes === 0) {
            console.log('✅ Detectada selección de 8:00 AM - ajustando a 2 horas');
            
            // Establecer duración de 2 horas
            durationInput.value = 120;
            
            // Calcular hora de fin (10:00)
            const endDateTime = new Date(startDateTime);
            endDateTime.setHours(10, 0, 0);
            
            // Actualizar campo de fin
            const endHours = endDateTime.getHours().toString().padStart(2, '0');
            const endMinutes = endDateTime.getMinutes().toString().padStart(2, '0');
            endTimeInput.value = `${endHours}:${endMinutes}`;
            
            console.log(`⏱️ Ajustado a 8:00 - 10:00 (${durationInput.value} minutos)`);
            
            // Aplicar efectos visuales
            durationInput.classList.add('field-highlight');
            endTimeInput.classList.add('field-highlight');
            
            setTimeout(() => {
                durationInput.classList.remove('field-highlight');
                endTimeInput.classList.remove('field-highlight');
            }, 1500);
            
            // Actualizar infobox si existe
            const infoBoxId = formId === 'direct' ? 'direct-selected-time-info' : 'selected-time-info';
            const infoBox = document.getElementById(infoBoxId);
            
            if (infoBox) {
                const infoText = infoBox.querySelector('.selected-time-text');
                if (infoText) {
                    const formattedStart = startDateTime.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                    const formattedEnd = endDateTime.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                    infoText.textContent = `${formattedStart} - ${formattedEnd} (120 minutos)`;
                    infoBox.classList.remove('d-none');
                }
            }
        }
    }
    
    // Detectar apertura de modales y aplicar la lógica especial
    document.addEventListener('shown.bs.modal', function(event) {
        if (event.target.id === 'newScheduleModal') {
            console.log('📅 Modal de agendamiento abierto - comprobando selección especial');
            setTimeout(() => handle8To10Selection('normal'), 300);
            
            // Disparar evento personalizado para reinicializar sincronización
            document.dispatchEvent(new CustomEvent('reinitializeTimeSyncFields', {
                detail: { modalId: 'newScheduleModal' }
            }));
        } 
        else if (event.target.id === 'newDirectScheduleModal') {
            console.log('📅 Modal directo abierto - comprobando selección especial');
            setTimeout(() => handle8To10Selection('direct'), 300);
            
            // Disparar evento personalizado para reinicializar sincronización
            document.dispatchEvent(new CustomEvent('reinitializeTimeSyncFields', {
                detail: { modalId: 'newDirectScheduleModal' }
            }));
        }
    });
});