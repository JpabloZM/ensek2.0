/**
 * Script para manejar el formulario de agendamiento directo mejorado
 * Permite la creación de agendamientos sin solicitud previa
 * Versión: 2.0
 */

document.addEventListener("DOMContentLoaded", function () {
    console.log("📅 Script de agendamiento directo iniciado");

    // Verificar que el modal existe
    const directModal = document.getElementById("newDirectScheduleModal");
    if (directModal) {
        console.log("✅ Modal de agendamiento directo encontrado");
    } else {
        console.error("❌ ERROR: Modal de agendamiento directo no encontrado");
        return;
    }

    // Función para aplicar efecto de resaltado a un elemento modificado
    function highlightField(element) {
        if (!element) return;
        element.classList.add("field-highlight");
        setTimeout(() => {
            element.classList.remove("field-highlight");
        }, 1500);
    }
    
    // Escuchar evento personalizado para preparar el modal
    document.addEventListener('prepareDirectModal', function(event) {
        console.log('📣 Evento prepareDirectModal recibido con datos:', event.detail);
        
        // Almacenar datos para uso posterior
        window.directModalData = event.detail;
    });

    // Función global para abrir el modal programáticamente desde cualquier parte
    window.abrirModalDirecto = function (data = {}) {
        console.log("🔄 Función global abrirModalDirecto() ejecutada", data);

        const directModal = document.getElementById("newDirectScheduleModal");
        if (!directModal) {
            console.error("❌ ERROR: Modal no encontrado en la función global");
            return false;
        }
        
        // Almacenar datos para uso posterior
        window.directModalData = data;

        // Intentar mostrar el modal
        try {
            const bsModal = new bootstrap.Modal(directModal);
            bsModal.show();
            return true;
        } catch (err) {
            console.error("❌ Error en función global:", err);
            return false;
        }
    };
    
    // Elementos del formulario - Información del servicio
    const directServiceSelect = document.getElementById("direct_service_id");
    const directDescriptionTextarea = document.getElementById("direct_description");
    const directAddressInput = document.getElementById("direct_address");
    
    // Elementos del formulario - Información del cliente
    const directClientNameInput = document.getElementById("direct_client_name");
    const directClientPhoneInput = document.getElementById("direct_client_phone");
    const directClientEmailInput = document.getElementById("direct_client_email");
    
    // Elementos del formulario - Programación
    const directTechnicianSelect = document.getElementById("direct_technician_id");
    const directScheduledDateInput = document.getElementById("direct_scheduled_date");
    const directStartTimeInput = document.getElementById("direct_start_time");
    const directEndTimeInput = document.getElementById("direct_end_time");
    const directDurationInput = document.getElementById("direct_duration");
    const directEstimatedCostInput = document.getElementById("direct_estimated_cost");
    
    // Elementos del formulario - Notas
    const directNotesTextarea = document.getElementById("direct_notes");
    
    // Botón de envío
    const submitButton = document.querySelector('#directScheduleForm button[type="submit"]');
    
    // Actualizar el título del modal con información de la cita
    function updateModalTitle(technicianName, serviceName, date, startTime, endTime) {
        const modalTitle = document.getElementById("newDirectScheduleModalLabel");
        if (!modalTitle) return;
        
        let title = "Nuevo Servicio";
        
        if (technicianName) {
            title += ` - ${technicianName}`;
        }
        
        if (startTime && endTime) {
            title += ` (${startTime} a ${endTime})`;
        }
        
        modalTitle.innerHTML = title;
    }

    // Función para actualizar la duración basada en el servicio seleccionado
    if (directServiceSelect) {
        directServiceSelect.addEventListener("change", function () {
            console.log("Servicio cambiado");
            const selectedOption = this.options[this.selectedIndex];

            if (selectedOption && selectedOption.value) {
                console.log("Opción seleccionada:", selectedOption.text);
                
                // Actualizar título del modal
                const techName = directTechnicianSelect ? 
                    directTechnicianSelect.options[directTechnicianSelect.selectedIndex]?.text : '';
                updateModalTitle(techName, selectedOption.text);
                
                // Actualizar duración si está disponible
                if (selectedOption.dataset.duration && directDurationInput) {
                    directDurationInput.value = selectedOption.dataset.duration;
                    highlightField(directDurationInput);
                    updateEndTime();
                }

                // Actualizar precio estimado si está disponible
                if (selectedOption.dataset.price && directEstimatedCostInput) {
                    directEstimatedCostInput.value = selectedOption.dataset.price;
                    highlightField(directEstimatedCostInput);
                }
            }
        });
    }
    
    // Actualizar el título cuando cambia el técnico
    if (directTechnicianSelect) {
        directTechnicianSelect.addEventListener("change", function() {
            const selectedTech = this.options[this.selectedIndex];
            const selectedService = directServiceSelect ? 
                directServiceSelect.options[directServiceSelect.selectedIndex]?.text : '';
                
            if (selectedTech && selectedTech.value) {
                updateModalTitle(selectedTech.text, selectedService);
            }
        });
    }

    // Función para actualizar la hora de finalización basada en la duración
    function updateEndTime() {
        if (
            directScheduledDateInput &&
            directStartTimeInput &&
            directDurationInput &&
            directEndTimeInput
        ) {
            const dateString = directScheduledDateInput.value;
            const timeString = directStartTimeInput.value;

            if (dateString && timeString) {
                // Crear fecha completa con fecha y hora
                const [hours, minutes] = timeString.split(":");
                const startDate = new Date(dateString);
                startDate.setHours(parseInt(hours) || 0);
                startDate.setMinutes(parseInt(minutes) || 0);

                if (!isNaN(startDate.getTime())) {
                    // Obtener la duración en minutos
                    const duration = parseInt(directDurationInput.value) || 60;

                    // Calcular la hora de finalización
                    const endDate = new Date(
                        startDate.getTime() + duration * 60 * 1000
                    );

                    // Formatear la hora para el input time
                    const endHours = endDate.getHours().toString().padStart(2, "0");
                    const endMinutes = endDate.getMinutes().toString().padStart(2, "0");
                    directEndTimeInput.value = `${endHours}:${endMinutes}`;
                    highlightField(directEndTimeInput);
                    
                    // Actualizar título del modal con el rango de horas
                    const techName = directTechnicianSelect ? 
                        directTechnicianSelect.options[directTechnicianSelect.selectedIndex]?.text : '';
                    const serviceName = directServiceSelect ? 
                        directServiceSelect.options[directServiceSelect.selectedIndex]?.text : '';
                    updateModalTitle(techName, serviceName, dateString, timeString, `${endHours}:${endMinutes}`);
                }
            }
        }
    }
    
    // Actualizar duración cuando cambia la hora de fin
    function updateDuration() {
        if (
            directScheduledDateInput &&
            directStartTimeInput &&
            directEndTimeInput &&
            directDurationInput
        ) {
            const dateString = directScheduledDateInput.value;
            const startTimeString = directStartTimeInput.value;
            const endTimeString = directEndTimeInput.value;

            if (dateString && startTimeString && endTimeString) {
                // Crear fechas para inicio y fin
                const [startHours, startMinutes] = startTimeString.split(":");
                const [endHours, endMinutes] = endTimeString.split(":");
                
                const startDate = new Date(dateString);
                startDate.setHours(parseInt(startHours) || 0);
                startDate.setMinutes(parseInt(startMinutes) || 0);
                
                const endDate = new Date(dateString);
                endDate.setHours(parseInt(endHours) || 0);
                endDate.setMinutes(parseInt(endMinutes) || 0);
                
                // Si la hora de fin es anterior a la de inicio, asumimos que es del día siguiente
                if (endDate < startDate) {
                    endDate.setDate(endDate.getDate() + 1);
                }

                // Calcular la duración en minutos
                const durationMs = endDate.getTime() - startDate.getTime();
                const durationMinutes = Math.round(durationMs / (60 * 1000));
                
                if (durationMinutes > 0) {
                    directDurationInput.value = durationMinutes;
                    highlightField(directDurationInput);
                }
            }
        }
    }

    // Eventos para actualización de fecha/hora
    if (directScheduledDateInput) {
        directScheduledDateInput.addEventListener("change", updateEndTime);
    }

    if (directStartTimeInput) {
        directStartTimeInput.addEventListener("change", updateEndTime);
        directStartTimeInput.addEventListener("input", updateEndTime);
    }

    if (directEndTimeInput) {
        directEndTimeInput.addEventListener("change", updateDuration);
        directEndTimeInput.addEventListener("input", updateDuration);
    }

    if (directDurationInput) {
        directDurationInput.addEventListener("change", updateEndTime);
        directDurationInput.addEventListener("input", updateEndTime);
    }

    // Inicializar formulario cuando se abre el modal
    const directScheduleModal = document.getElementById("newDirectScheduleModal");
    if (directScheduleModal) {
        directScheduleModal.addEventListener("shown.bs.modal", function () {
            console.log("🎉 MODAL DIRECTO ABIERTO CORRECTAMENTE");

            // Enfocar el selector de servicios primero
            setTimeout(() => {
                if (directServiceSelect) {
                    directServiceSelect.focus();
                    directServiceSelect.classList.add('border-highlight');
                    setTimeout(() => {
                        directServiceSelect.classList.remove('border-highlight');
                    }, 1500);
                }
            }, 300);

            // Obtener datos precargados
            let technicianId, technicianName, date, startTime, endTime, duration;

            // Intentar múltiples fuentes de datos
            if (window.directModalData) {
                console.log("📌 Usando datos pasados globalmente:", window.directModalData);
                technicianId = window.directModalData.technicianId;
                technicianName = window.directModalData.technicianName;
                date = window.directModalData.date;
                startTime = window.directModalData.startTime;
                endTime = window.directModalData.endTime;
                duration = window.directModalData.duration;
            }
            // Del contenedor temporal
            else {
                const tempDataHolder = document.getElementById("temp-modal-data");
                if (tempDataHolder) {
                    technicianId = tempDataHolder.dataset.technicianId;
                    technicianName = tempDataHolder.dataset.technicianName;
                    date = tempDataHolder.dataset.date;
                    startTime = tempDataHolder.dataset.startTime;
                    endTime = tempDataHolder.dataset.endTime;
                    duration = tempDataHolder.dataset.duration;
                    tempDataHolder.remove();
                }
            }

            // Aplicar los datos a los campos
            console.log("Datos a aplicar:", { technicianId, technicianName, date, startTime, endTime, duration });
            
            // Actualizar título del modal
            updateModalTitle(technicianName, null, null, startTime, endTime);

            // Aplicar datos de fecha y hora
            if (date && directScheduledDateInput) {
                directScheduledDateInput.value = date;
                highlightField(directScheduledDateInput);
            }

            if (startTime && directStartTimeInput) {
                directStartTimeInput.value = startTime;
                highlightField(directStartTimeInput);
            }

            if (endTime && directEndTimeInput) {
                directEndTimeInput.value = endTime;
                highlightField(directEndTimeInput);
            }

            if (duration && directDurationInput) {
                directDurationInput.value = duration;
                highlightField(directDurationInput);
            }

            // Configurar técnico
            if (technicianId && directTechnicianSelect) {
                directTechnicianSelect.value = technicianId;
                highlightField(directTechnicianSelect);
            }
            
            // Establecer fecha de hoy si no hay fecha
            if (!date && directScheduledDateInput && !directScheduledDateInput.value) {
                const today = new Date();
                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');
                directScheduledDateInput.value = `${year}-${month}-${day}`;
            }
        });

        // También detectar cuando se oculta el modal
        directScheduleModal.addEventListener("hidden.bs.modal", function () {
            console.log("Modal directo cerrado");
            // Limpiar datos almacenados
            window.directModalData = null;
        });
    }

    // Evento de envío del formulario
    const directScheduleForm = document.getElementById("directScheduleForm");
    if (directScheduleForm) {
        directScheduleForm.addEventListener("submit", function (event) {
            // Campos requeridos según el controlador
            const requiredFields = [
                directClientNameInput,        // client_name
                directClientPhoneInput,       // client_phone
                directServiceSelect,          // service_id
                directDescriptionTextarea,    // description
                directTechnicianSelect,       // technician_id
                directScheduledDateInput,     // scheduled_date
                directStartTimeInput,         // implícito para scheduled_date
                directEndTimeInput            // end_time
            ];

            let isValid = true;
            let firstInvalidField = null;

            // Validar campos requeridos
            requiredFields.forEach(field => {
                if (field && !field.value) {
                    field.classList.add("is-invalid");
                    isValid = false;
                    if (!firstInvalidField) firstInvalidField = field;
                } else if (field) {
                    field.classList.remove("is-invalid");
                }
            });

            if (!isValid) {
                event.preventDefault();
                
                // Enfocar el primer campo inválido
                if (firstInvalidField) {
                    firstInvalidField.focus();
                }
                
                // Alerta de campos requeridos
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos incompletos',
                    text: 'Por favor complete todos los campos requeridos',
                    confirmButtonColor: '#87c947'
                });
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
