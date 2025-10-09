/**
 * Calendar Time Synchronization
 * Script para sincronizar los campos de tiempo en el formulario de agendamiento
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔄 Inicializando sincronización de campos de tiempo (v2)');
    
    // Función para sincronizar campos de tiempo en el modal de agendamiento
    window.syncTimeFields = function(dateInputId, startTimeInputId, endTimeInputId, durationInputId, infoBoxId) {
        console.log(`⚙️ Configurando sincronización para ${dateInputId}, ${endTimeInputId}, ${durationInputId}`);
        const dateInput = document.getElementById(dateInputId);
        const startTimeInput = document.getElementById(startTimeInputId);
        const endTimeInput = document.getElementById(endTimeInputId);
        const durationInput = document.getElementById(durationInputId);
        const infoBox = document.getElementById(infoBoxId);
        
        // Validar que tenemos los campos necesarios (manejar caso de fecha+tiempo combinados vs. separados)
        if (!dateInput || !endTimeInput || !durationInput) {
            console.error('⚠️ No se encontraron todos los campos necesarios para sincronización:', 
                         {dateInput, startTimeInput, endTimeInput, durationInput});
            return;
        }
        
        console.log('✅ Campos validados correctamente:', 
                   {dateInput: dateInput.id, 
                    startTimeInput: startTimeInput ? startTimeInput.id : 'N/A', 
                    endTimeInput: endTimeInput.id, 
                    durationInput: durationInput.id});
        
        // Remover listeners previos clonando los elementos
        const newDateInput = dateInput.cloneNode(true);
        dateInput.parentNode.replaceChild(newDateInput, dateInput);
        
        if (startTimeInput) {
            const newStartTimeInput = startTimeInput.cloneNode(true);
            startTimeInput.parentNode.replaceChild(newStartTimeInput, startTimeInput);
        }
        
        const newEndTimeInput = endTimeInput.cloneNode(true);
        endTimeInput.parentNode.replaceChild(newEndTimeInput, endTimeInput);
        
        const newDurationInput = durationInput.cloneNode(true);
        durationInput.parentNode.replaceChild(newDurationInput, durationInput);
        
        // Obtener referencias a los nuevos elementos
        const updatedDateInput = document.getElementById(dateInputId);
        const updatedStartTimeInput = startTimeInputId ? document.getElementById(startTimeInputId) : null;
        const updatedEndTimeInput = document.getElementById(endTimeInputId);
        const updatedDurationInput = document.getElementById(durationInputId);
        
        // Función para actualizar la infobox de tiempo
        function updateTimeInfoBox(start, end, duration) {
            if (infoBox) {
                const infoText = document.querySelector('#' + infoBoxId + ' .selected-time-text');
                if (infoText) {
                    const formattedStart = start.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                    const formattedEnd = end.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                    infoText.textContent = `${formattedStart} - ${formattedEnd} (${duration} minutos)`;
                    infoBox.classList.remove('d-none');
                    console.log(`ℹ️ InfoBox actualizado: ${formattedStart} - ${formattedEnd} (${duration} min)`);
                } else {
                    console.warn('⚠️ Elemento de texto de tiempo no encontrado en infoBox');
                }
            } else {
                console.warn('⚠️ InfoBox no encontrado para actualización de tiempo');
            }
        }
        
        // FUNCIONES PARA MANEJAR CASOS ESPECIALES
        
        // Función especial para detectar si se trata de un horario de 8am a 10am (o cualquier horario de 2 horas exactas)
        function detectSpecialTimeRanges(startHour, endHour, durationMins) {
            console.log(`🔍 Analizando rango de tiempo: ${startHour}:00 - ${endHour}:00 (${durationMins} min)`);
            
            // Caso especial: selección de 8am a 10am (2 horas)
            if (startHour === 8 && endHour === 10 && Math.abs(durationMins - 120) < 10) {
                console.log('✓ Detectada selección especial: 8am a 10am (2 horas)');
                return { duration: 120, description: '8:00 AM - 10:00 AM (2 horas exactas)' };
            }
            
            // Caso especial: Cualquier rango de 2 horas exactas
            if (endHour - startHour === 2 && Math.abs(durationMins - 120) < 10) {
                console.log(`✓ Detectada selección especial: ${startHour}:00 a ${endHour}:00 (2 horas exactas)`);
                return { duration: 120, description: `${startHour}:00 - ${endHour}:00 (2 horas exactas)` };
            }
            
            // Caso especial: Inicio a las 8:00 AM (sugerir 2 horas)
            if (startHour === 8 && durationMins < 180) {
                console.log('✓ Detectado inicio a las 8:00 AM - sugiriendo 2 horas');
                return { duration: 120, description: '8:00 AM - 10:00 AM (2 horas sugeridas)' };
            }
            
            return null;
        }
        
        // Establecer listeners para sincronizar campos
        
        // 1. Cuando cambia la duración
        updatedDurationInput.addEventListener('input', function() {
            console.log(`🕒 Evento: Cambio de duración a ${this.value} minutos`);
            // Obtener fecha y hora de inicio
            const dateValue = updatedDateInput.value;
            const startTimeValue = updatedStartTimeInput ? updatedStartTimeInput.value : null;
            
            let startDateTime;
            
            // Si hay campos separados de fecha y hora
            if (startTimeValue) {
                startDateTime = new Date(`${dateValue}T${startTimeValue}`);
                console.log(`📅 Fecha y hora de inicio separadas: ${dateValue}T${startTimeValue}`);
            } else {
                // Si es un único campo datetime-local
                startDateTime = new Date(dateValue);
                console.log(`📅 Fecha y hora de inicio combinadas: ${dateValue}`);
            }
            
            // Calcular hora de finalización
            const durationValue = parseInt(this.value) || 60;
            const endDateTime = new Date(startDateTime.getTime() + durationValue * 60000);
            
            // Formatear hora de fin
            const endHours = endDateTime.getHours().toString().padStart(2, '0');
            const endMinutes = endDateTime.getMinutes().toString().padStart(2, '0');
            
            // Actualizar campo de hora fin
            updatedEndTimeInput.value = `${endHours}:${endMinutes}`;
            console.log(`⏱️ Hora de fin calculada: ${endHours}:${endMinutes}`);
            
            // Actualizar infobox
            updateTimeInfoBox(startDateTime, endDateTime, durationValue);
            
            // Efecto visual
            updatedEndTimeInput.classList.add('field-highlight');
            setTimeout(() => updatedEndTimeInput.classList.remove('field-highlight'), 1000);
        });
        
        // 2. Cuando cambia la hora de fin
        updatedEndTimeInput.addEventListener('input', function() {
            console.log(`🕒 Evento: Cambio de hora de fin a ${this.value}`);
            // Obtener fecha y hora de inicio
            const dateValue = updatedDateInput.value;
            const startTimeValue = updatedStartTimeInput ? updatedStartTimeInput.value : null;
            
            let startDateTime;
            
            // Si hay campos separados de fecha y hora
            if (startTimeValue) {
                startDateTime = new Date(`${dateValue}T${startTimeValue}`);
                console.log(`📅 Fecha y hora de inicio separadas: ${dateValue}T${startTimeValue}`);
            } else {
                // Si es un único campo datetime-local
                startDateTime = new Date(dateValue);
                console.log(`📅 Fecha y hora de inicio combinadas: ${dateValue}`);
            }
            
            // Obtener hora de fin
            const endTimeArr = this.value.split(':');
            const endHours = parseInt(endTimeArr[0]);
            const endMinutes = parseInt(endTimeArr[1]);
            
            if (!isNaN(endHours) && !isNaN(endMinutes)) {
                console.log(`🕒 Hora de fin analizada: ${endHours}:${endMinutes}`);
                // Crear objeto de fecha para la hora de fin
                const endDateTime = new Date(startDateTime);
                endDateTime.setHours(endHours, endMinutes);
                
                // Si la hora fin es anterior a inicio, asumimos día siguiente
                if (endDateTime < startDateTime) {
                    console.log('⚠️ Hora fin anterior a inicio, ajustando a día siguiente');
                    endDateTime.setDate(endDateTime.getDate() + 1);
                }
                
                // Calcular duración
                const durationMinutes = Math.round((endDateTime - startDateTime) / 60000);
                console.log(`⏱️ Duración calculada: ${durationMinutes} minutos`);
                
                // Detectar casos especiales
                const specialCase = detectSpecialTimeRanges(startDateTime.getHours(), endDateTime.getHours(), durationMinutes);
                
                // Si es un caso especial, usar su duración específica
                if (specialCase) {
                    console.log(`🔍 Caso especial detectado: ${specialCase.description}`);
                    updatedDurationInput.value = specialCase.duration;
                    
                    // Recalcular hora de fin para mantener consistencia
                    const recalcEndTime = new Date(startDateTime.getTime() + specialCase.duration * 60000);
                    const recalcHours = recalcEndTime.getHours().toString().padStart(2, '0');
                    const recalcMinutes = recalcEndTime.getMinutes().toString().padStart(2, '0');
                    
                    // Actualizar valor del campo evitando recursión
                    if (`${recalcHours}:${recalcMinutes}` !== this.value) {
                        console.log(`📝 Ajustando hora de fin a ${recalcHours}:${recalcMinutes}`);
                        this.value = `${recalcHours}:${recalcMinutes}`;
                    }
                    
                    // Actualizar infobox
                    updateTimeInfoBox(startDateTime, recalcEndTime, specialCase.duration);
                } else if (durationMinutes > 0) {
                    // Caso normal
                    console.log(`📝 Aplicando duración estándar de ${durationMinutes} minutos`);
                    updatedDurationInput.value = durationMinutes;
                    
                    // Actualizar infobox
                    updateTimeInfoBox(startDateTime, endDateTime, durationMinutes);
                }
                
                // Efecto visual
                updatedDurationInput.classList.add('field-highlight');
                setTimeout(() => updatedDurationInput.classList.remove('field-highlight'), 1000);
            }
        });
        
        // 3. Cuando cambia la fecha/hora de inicio
        function handleStartTimeChange() {
            console.log('🕒 Evento: Cambio en fecha/hora de inicio');
            // Obtener fecha y hora de inicio
            const dateValue = updatedDateInput.value;
            if (!dateValue) {
                console.warn('⚠️ Valor de fecha vacío, abortando actualización');
                return;
            }
            
            const startTimeValue = updatedStartTimeInput ? updatedStartTimeInput.value : null;
            
            let startDateTime;
            
            // Si hay campos separados de fecha y hora
            if (startTimeValue) {
                try {
                    startDateTime = new Date(`${dateValue}T${startTimeValue}`);
                    console.log(`📅 Fecha y hora de inicio separadas: ${dateValue}T${startTimeValue}`);
                } catch (e) {
                    console.error(`❌ Error al parsear fecha/hora: ${dateValue}T${startTimeValue}`, e);
                    return;
                }
            } else {
                // Si es un único campo datetime-local
                try {
                    startDateTime = new Date(dateValue);
                    console.log(`📅 Fecha y hora de inicio combinadas: ${dateValue}`);
                } catch (e) {
                    console.error(`❌ Error al parsear fecha/hora: ${dateValue}`, e);
                    return;
                }
            }
            
            // Detectar casos especiales basados en la hora de inicio
            const startHour = startDateTime.getHours();
            const startMinutes = startDateTime.getMinutes();
            
            // Caso especial: Si empieza a las 8:00 AM, sugerir 2 horas de duración
            if (startHour === 8 && startMinutes === 0) {
                console.log('🔍 Caso especial detectado: Inicio a las 8:00 AM - aplicando duración de 2 horas');
                updatedDurationInput.value = 120;
                
                // Recalcular hora fin para 2 horas
                const specialEndTime = new Date(startDateTime.getTime() + 120 * 60000);
                const specialEndHours = specialEndTime.getHours().toString().padStart(2, '0');
                const specialEndMinutes = specialEndTime.getMinutes().toString().padStart(2, '0');
                
                console.log(`⏱️ Estableciendo hora de fin para caso especial: ${specialEndHours}:${specialEndMinutes}`);
                updatedEndTimeInput.value = `${specialEndHours}:${specialEndMinutes}`;
                
                // Actualizar infobox
                updateTimeInfoBox(startDateTime, specialEndTime, 120);
                
                // Efecto visual para ambos campos
                updatedEndTimeInput.classList.add('field-highlight');
                updatedDurationInput.classList.add('field-highlight');
                setTimeout(() => {
                    updatedEndTimeInput.classList.remove('field-highlight');
                    updatedDurationInput.classList.remove('field-highlight');
                }, 1000);
            } else {
                // Caso normal: usar la duración actual (o 60 min por defecto)
                const durationValue = parseInt(updatedDurationInput.value) || 60;
                console.log(`📝 Usando duración estándar de ${durationValue} minutos`);
                
                // Calcular nueva hora de fin
                const endDateTime = new Date(startDateTime.getTime() + durationValue * 60000);
                
                // Formatear hora fin
                const endHours = endDateTime.getHours().toString().padStart(2, '0');
                const endMinutes = endDateTime.getMinutes().toString().padStart(2, '0');
                
                console.log(`⏱️ Calculando hora de fin: ${endHours}:${endMinutes}`);
                // Actualizar campo de hora fin
                updatedEndTimeInput.value = `${endHours}:${endMinutes}`;
                
                // Actualizar infobox
                updateTimeInfoBox(startDateTime, endDateTime, durationValue);
                
                // Efecto visual
                updatedEndTimeInput.classList.add('field-highlight');
                setTimeout(() => updatedEndTimeInput.classList.remove('field-highlight'), 1000);
            }
        }
        
        // Aplicar listener a la fecha
        updatedDateInput.addEventListener('input', handleStartTimeChange);
        
        // Aplicar listener a la hora de inicio si existe
        if (updatedStartTimeInput) {
            updatedStartTimeInput.addEventListener('input', handleStartTimeChange);
        }
        
        // Ejecutar sincronización inicial para configurar todo correctamente basado en los valores actuales
        console.log('🔄 Ejecutando sincronización inicial para configurar correctamente los campos');
        handleStartTimeChange();
        
        console.log(`✅ Sincronización de campos establecida para ${dateInputId}, ${startTimeInputId || 'N/A'}, ${endTimeInputId}, ${durationInputId}`);
    };
    
    // Escuchar al evento shown.bs.modal para inicializar los campos en el formulario
    document.addEventListener('shown.bs.modal', function(event) {
        // Si es el modal de agendamiento
        if (event.target.id === 'newScheduleModal') {
            console.log('🔄 Modal de agendamiento abierto, inicializando sincronización');
            setTimeout(() => {
                window.syncTimeFields('scheduled_date', null, 'end_time', 'duration', 'selected-time-info');
            }, 100); // Pequeño retraso para asegurar que todo está cargado
        }
        // Si es el modal directo
        else if (event.target.id === 'newDirectScheduleModal') {
            console.log('🔄 Modal de agendamiento directo abierto, inicializando sincronización');
            setTimeout(() => {
                window.syncTimeFields('direct_scheduled_date', 'direct_start_time', 'direct_end_time', 'direct_duration', 'direct-selected-time-info');
            }, 100); // Pequeño retraso para asegurar que todo está cargado
        }
    });
    
    // Configurar evento personalizado que podemos disparar manualmente cuando sea necesario
    document.addEventListener('reinitializeTimeSyncFields', function(event) {
        if (event.detail && event.detail.modalId) {
            console.log(`🔄 Evento personalizado: reinicializando campos para ${event.detail.modalId}`);
            
            if (event.detail.modalId === 'newScheduleModal') {
                window.syncTimeFields('scheduled_date', null, 'end_time', 'duration', 'selected-time-info');
            } 
            else if (event.detail.modalId === 'newDirectScheduleModal') {
                window.syncTimeFields('direct_scheduled_date', 'direct_start_time', 'direct_end_time', 'direct_duration', 'direct-selected-time-info');
            }
        }
    });
    
    // Iniciar sincronización cuando la página esté completamente cargada
    window.addEventListener('load', function() {
        console.log('🔄 Página cargada completamente, verificando modales abiertos');
        
        // Comprobar si algún modal está abierto y aplicar sincronización
        const scheduleModal = document.getElementById('newScheduleModal');
        if (scheduleModal && scheduleModal.classList.contains('show')) {
            console.log('🔄 Modal de agendamiento encontrado abierto, aplicando sincronización');
            window.syncTimeFields('scheduled_date', null, 'end_time', 'duration', 'selected-time-info');
        }
        
        const directModal = document.getElementById('newDirectScheduleModal');
        if (directModal && directModal.classList.contains('show')) {
            console.log('🔄 Modal directo encontrado abierto, aplicando sincronización');
            window.syncTimeFields('direct_scheduled_date', 'direct_start_time', 'direct_end_time', 'direct_duration', 'direct-selected-time-info');
        }
    });
});