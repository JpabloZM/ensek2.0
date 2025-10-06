/**
 * Script para manejar el formulario de agendamiento directo
 * Permite la creación de agendamientos sin solicitud previa
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Script de agendamiento directo iniciado');
    
    // Verificar que el modal existe
    const directModal = document.getElementById('newDirectScheduleModal');
    if (directModal) {
        console.log('Modal de agendamiento directo encontrado');
    } else {
        console.error('ERROR: Modal de agendamiento directo no encontrado');
    }
    
    // Función auxiliar para resaltar un elemento modificado
    function flashElement(element) {
        if (!element) return;
        
        // Guardamos el color de fondo original
        const originalBg = element.style.backgroundColor;
        
        // Animación de resaltado
        element.style.backgroundColor = '#c7ffd8';
        setTimeout(() => {
            element.style.backgroundColor = originalBg;
        }, 1000);
    }
    
    // Función global para abrir el modal programáticamente desde cualquier parte
    window.abrirModalDirecto = function() {
        console.log('Función global abrirModalDirecto() ejecutada');
        
        const directModal = document.getElementById('newDirectScheduleModal');
        if (!directModal) {
            console.error('ERROR: Modal no encontrado en la función global');
            return false;
        }
        
        // Intentar mostrar el modal
        try {
            const bsModal = new bootstrap.Modal(directModal);
            bsModal.show();
            return true;
        } catch (err) {
            console.error('Error en función global:', err);
            return false;
        }
    };
    // Elementos del formulario
    const directServiceSelect = document.getElementById('direct_service_id');
    const directDurationInput = document.getElementById('direct_duration');
    const directEstimatedCostInput = document.getElementById('direct_estimated_cost');
    const directScheduledDateInput = document.getElementById('direct_scheduled_date');
    const directStartTimeInput = document.getElementById('direct_start_time');
    const directEndTimeInput = document.getElementById('direct_end_time');
    const directDescription = document.getElementById('direct_description');
    const directClientName = document.getElementById('direct_client_name');
    const directClientPhone = document.getElementById('direct_client_phone');
    const directClientEmail = document.getElementById('direct_client_email');
    const submitButton = document.querySelector('#directScheduleForm button[type="submit"]');
    
    // Función para resaltar campos que se actualizan automáticamente
    function highlightField(field) {
        if (field) {
            field.classList.add('field-highlight');
            setTimeout(() => {
                field.classList.remove('field-highlight');
            }, 1000);
        }
    }
    
    // Función para actualizar la duración basada en el servicio seleccionado
    if (directServiceSelect) {
        console.log('Añadiendo evento change al select de servicios');
        
        directServiceSelect.addEventListener('change', function() {
            console.log('Servicio cambiado');
            const selectedOption = this.options[this.selectedIndex];
            
            if (selectedOption && selectedOption.value) {
                console.log('Opción seleccionada:', selectedOption.text);
                console.log('Datos del servicio:', selectedOption.dataset);
                
                // Actualizar duración si está disponible
                if (selectedOption.dataset.duration && directDurationInput) {
                    directDurationInput.value = selectedOption.dataset.duration;
                    flashElement(directDurationInput);
                    console.log('Duración actualizada:', selectedOption.dataset.duration);
                    updateEndTime();
                }
                
                // Actualizar precio estimado si está disponible
                if (selectedOption.dataset.price && directEstimatedCostInput) {
                    directEstimatedCostInput.value = selectedOption.dataset.price;
                    flashElement(directEstimatedCostInput);
                    console.log('Precio actualizado:', selectedOption.dataset.price);
                }
                
                // Actualizar descripción con el nombre del servicio si está vacía
                if (directDescription) {
                    // Siempre establecer una descripción predeterminada basada en el servicio
                    directDescription.value = `Servicio de ${selectedOption.text.trim()}`;
                    flashElement(directDescription);
                    console.log('Descripción actualizada');
                }
            } else {
                console.log('No se seleccionó ninguna opción válida');
            }
        });
        
        // Añadir también evento focus para asegurar que el usuario ve los servicios
        directServiceSelect.addEventListener('focus', function() {
            console.log('Select de servicios enfocado');
        });
    } else {
        console.error('ERROR: El select de servicios no se encontró en el DOM');
    }
    
    // Función para actualizar la hora de finalización basada en la duración
    function updateEndTime() {
        if (directScheduledDateInput && directStartTimeInput && directDurationInput && directEndTimeInput) {
            const dateString = directScheduledDateInput.value;
            const timeString = directStartTimeInput.value;
            
            if (dateString && timeString) {
                // Crear fecha completa con fecha y hora
                const [hours, minutes] = timeString.split(':');
                const startDate = new Date(dateString);
                startDate.setHours(parseInt(hours) || 0);
                startDate.setMinutes(parseInt(minutes) || 0);
                
                if (!isNaN(startDate.getTime())) {
                    // Obtener la duración en minutos
                    const duration = parseInt(directDurationInput.value) || 60;
                    
                    // Calcular la hora de finalización
                    const endDate = new Date(startDate.getTime() + (duration * 60 * 1000));
                    
                    // Formatear la hora para el input time
                    const endHours = endDate.getHours().toString().padStart(2, '0');
                    const endMinutes = endDate.getMinutes().toString().padStart(2, '0');
                    directEndTimeInput.value = `${endHours}:${endMinutes}`;
                    highlightField(directEndTimeInput);
                }
            }
        }
    }
    
    // Actualizar la hora de finalización cuando cambie la fecha/hora de inicio o la duración
    if (directScheduledDateInput) {
        directScheduledDateInput.addEventListener('change', updateEndTime);
    }
    
    if (directStartTimeInput) {
        directStartTimeInput.addEventListener('change', updateEndTime);
        directStartTimeInput.addEventListener('input', updateEndTime);
    }
    
    if (directDurationInput) {
        directDurationInput.addEventListener('change', updateEndTime);
        directDurationInput.addEventListener('input', updateEndTime);
    }
    
    // Inicializar formulario cuando se abre el modal
    const directScheduleModal = document.getElementById('newDirectScheduleModal');
    if (directScheduleModal) {
        console.log('Configurando evento shown.bs.modal para el modal directo');
        
        directScheduleModal.addEventListener('shown.bs.modal', function(e) {
            console.log('MODAL DIRECTO ABIERTO CORRECTAMENTE');
            
            // PRIMER PASO: Enfocar el selector de servicios primero para asegurarnos
            // que el usuario vea los servicios disponibles
            setTimeout(() => {
                if (directServiceSelect) {
                    directServiceSelect.focus();
                    console.log('Enfocando el selector de servicios');
                }
            }, 300);
            
            // SEGUNDO PASO: Obtener datos precargados
            let technicianId, date, startTime, endTime, duration;
            
            // Intentar múltiples fuentes de datos
            // 1. Del contenedor específico
            const dataHolder = document.getElementById('direct-modal-data');
            if (dataHolder) {
                console.log('Datos encontrados en contenedor específico');
                technicianId = dataHolder.getAttribute('data-technician-id');
                date = dataHolder.getAttribute('data-date');
                startTime = dataHolder.getAttribute('data-start-time');
                endTime = dataHolder.getAttribute('data-end-time');
                duration = dataHolder.getAttribute('data-duration');
            } 
            // 2. Del relatedTarget (forma estándar)
            else if (e.relatedTarget && e.relatedTarget.dataset) {
                console.log('Datos encontrados en relatedTarget');
                technicianId = e.relatedTarget.dataset.technicianId;
                date = e.relatedTarget.dataset.date;
                startTime = e.relatedTarget.dataset.startTime;
                endTime = e.relatedTarget.dataset.endTime;
                duration = e.relatedTarget.dataset.duration;
            }
            
            // TERCER PASO: Aplicar los datos a los campos
            console.log('Datos a aplicar:', { technicianId, date, startTime, endTime, duration });
            
            // Aplicar datos de fecha y hora
            if (date && directScheduledDateInput) {
                directScheduledDateInput.value = date;
                flashElement(directScheduledDateInput);
            }
            
            if (startTime && directStartTimeInput) {
                directStartTimeInput.value = startTime;
                flashElement(directStartTimeInput);
            }
            
            if (endTime && directEndTimeInput) {
                directEndTimeInput.value = endTime;
                flashElement(directEndTimeInput);
            }
            
            if (duration && directDurationInput) {
                directDurationInput.value = duration;
                flashElement(directDurationInput);
            }
            
            // Configurar técnico
            if (technicianId && directTechnicianSelect) {
                directTechnicianSelect.value = technicianId;
                flashElement(directTechnicianSelect);
            }
            
            // CUARTO PASO: Actualizar hora de fin si tenemos inicio y duración
            if (directStartTimeInput && directStartTimeInput.value && 
                directDurationInput && directDurationInput.value) {
                updateEndTime();
            }
        });
        
        // También detectar cuando se oculta el modal
        directScheduleModal.addEventListener('hidden.bs.modal', function() {
            console.log('Modal directo cerrado');
        });
    } else {
        console.error('ERROR CRÍTICO: Modal directo no encontrado en el DOM');
    }
    
    // Validación básica del formulario antes de enviar
    const directScheduleForm = document.getElementById('directScheduleForm');
    if (directScheduleForm) {
        // Validación en tiempo real
        const validateField = (field) => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                return false;
            } else {
                field.classList.remove('is-invalid');
                return true;
            }
        };
        
        // Validación de email
        const validateEmail = (field) => {
            if (field.value.trim()) {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                const isValid = emailPattern.test(field.value.trim());
                
                if (!isValid) {
                    field.classList.add('is-invalid');
                    return false;
                }
            }
            
            return true;
        };
        
        // Validación de teléfono
        const validatePhone = (field) => {
            if (field.value.trim()) {
                const phonePattern = /^[\d\s\(\)\-\+]+$/;
                const isValid = phonePattern.test(field.value.trim());
                
                if (!isValid) {
                    field.classList.add('is-invalid');
                    return false;
                }
            }
            
            field.classList.remove('is-invalid');
            return true;
        };
        
        // Añadir validadores a los campos
        if (directClientName) directClientName.addEventListener('blur', () => validateField(directClientName));
        if (directClientPhone) directClientPhone.addEventListener('blur', () => validatePhone(directClientPhone));
        if (directClientEmail) directClientEmail.addEventListener('blur', () => validateEmail(directClientEmail));
        if (directDescription) directDescription.addEventListener('blur', () => validateField(directDescription));
        
        // Validar formulario al enviar
        directScheduleForm.addEventListener('submit', function(event) {
            // Verificar campos requeridos antes de enviar
            const requiredFields = [
                'direct_client_name',
                'direct_client_phone',
                'direct_service_id',
                'direct_description',
                'direct_technician_id',
                'direct_scheduled_date',
                'direct_start_time',
                'direct_end_time'
            ];
            
            let isValid = true;
            
            // Validar campos requeridos
            requiredFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    isValid = validateField(field) && isValid;
                }
            });
            
            // Validaciones específicas
            if (directClientEmail) {
                isValid = validateEmail(directClientEmail) && isValid;
            }
            
            if (directClientPhone) {
                isValid = validatePhone(directClientPhone) && isValid;
            }
            
            if (!isValid) {
                event.preventDefault();
                
                // Eliminar alertas anteriores
                const previousAlerts = directScheduleForm.querySelectorAll('.alert');
                previousAlerts.forEach(alert => alert.remove());
                
                // Mostrar alerta de error
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger alert-dismissible fade show mb-4';
                alertDiv.innerHTML = `
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Por favor, complete todos los campos requeridos correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                
                const modalBody = directScheduleForm.querySelector('.modal-body');
                if (modalBody) {
                    modalBody.insertBefore(alertDiv, modalBody.firstChild);
                }
                
                // Desplazarse al primer campo con error
                const firstInvalid = directScheduleForm.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            } else {
                // Mostrar indicador de carga
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Guardando...';
                }
            }
        });
    }
});