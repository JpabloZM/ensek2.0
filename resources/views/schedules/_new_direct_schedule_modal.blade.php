<!-- Modal para crear nuevo servicio directo -->
<div class="modal fade" id="newDirectScheduleModal" tabindex="-1" aria-labelledby="newDirectScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="newDirectScheduleModalLabel">Nuevo Servicio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="directScheduleForm" action="{{ route('admin.schedules.store-direct') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Alerta para horario seleccionado -->
                    <div class="alert alert-info rounded-4 border-0 shadow-sm mb-4 d-none" id="direct-selected-time-info" style="background-color: #e5f6fd;">
                        <div class="d-flex align-items-center">
                            <div class="me-3 text-info" style="font-size: 1.5rem;">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">Horario seleccionado</h6>
                                <span class="selected-time-text text-dark">No se ha seleccionado un horario</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Columna izquierda: Información del servicio -->
                        <div class="col-md-6">
                            <div class="card direct-schedule-card mb-3">
                                <div class="card-header d-flex align-items-center">
                                    <span class="card-header-icon"><i class="fas fa-tools"></i></span>
                                    Información del Servicio
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Tipo de Servicio</label>
                                        <select class="form-select" id="direct_service_id" name="service_id" required>
                                            <option value="">Seleccione un servicio...</option>
                                            @foreach($services as $service)
                                                <option value="{{ $service->id }}" 
                                                    data-duration="{{ $service->duration ?? 60 }}" 
                                                    data-price="{{ $service->price ?? 0 }}">
                                                    {{ $service->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Descripción</label>
                                        <textarea class="form-control" id="direct_description" name="description" rows="3" placeholder="Descripción detallada del servicio a realizar..." required></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Dirección</label>
                                        <input type="text" class="form-control" id="direct_address" name="address" placeholder="Dirección donde se realizará el servicio">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card direct-schedule-card">
                                <div class="card-header d-flex align-items-center">
                                    <span class="card-header-icon"><i class="fas fa-user"></i></span>
                                    Información del Cliente
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Nombre del Cliente</label>
                                        <input type="text" class="form-control" id="direct_client_name" name="client_name" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Teléfono</label>
                                        <input type="tel" class="form-control" id="direct_client_phone" name="client_phone" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" id="direct_client_email" name="client_email">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Columna derecha: Programación y detalles -->
                        <div class="col-md-6">
                            <div class="card direct-schedule-card mb-3">
                                <div class="card-header d-flex align-items-center">
                                    <span class="card-header-icon"><i class="fas fa-calendar-alt"></i></span>
                                    Programación
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Técnico</label>
                                        <select class="form-select" id="direct_technician_id" name="technician_id" required>
                                            <option value="">Seleccione un técnico...</option>
                                            @foreach($technicians as $technician)
                                                <option value="{{ $technician->id }}">
                                                    {{ $technician->user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Fecha</label>
                                        <input type="date" class="form-control" id="direct_scheduled_date" name="scheduled_date" required>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <label class="form-label">Hora Inicio</label>
                                            <input type="time" class="form-control" id="direct_start_time" name="start_time" required>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label class="form-label">Hora Fin</label>
                                            <input type="time" class="form-control" id="direct_end_time" name="end_time" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <label class="form-label">Duración (min)</label>
                                            <input type="number" class="form-control" id="direct_duration" name="duration" min="15" step="15" value="60">
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label class="form-label">Costo ($)</label>
                                            <input type="number" class="form-control" id="direct_estimated_cost" name="estimated_cost" step="0.01">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card direct-schedule-card">
                                <div class="card-header d-flex align-items-center">
                                    <span class="card-header-icon"><i class="fas fa-clipboard-list"></i></span>
                                    Notas Adicionales
                                </div>
                                <div class="card-body">
                                    <textarea class="form-control" id="direct_notes" name="notes" rows="3" placeholder="Instrucciones especiales, observaciones..."></textarea>
                                    <input type="hidden" name="status" value="pendiente">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveDirectScheduleBtn">
                        <i class="fas fa-save me-1"></i>Guardar
                    </button>
                </div>
                
                <script>
                    // CONFIGURACIÓN INMEDIATA DEL MODAL DIRECTO (VERSIÓN CORREGIDA)
                    // Este script integra todas las funciones en un único lugar
                    // para simplificar la gestión de los campos de tiempo
                    
                    // Elementos del formulario
                    const serviceSelect = document.getElementById('direct_service_id');
                    const dateInput = document.getElementById('direct_scheduled_date');
                    const startTimeInput = document.getElementById('direct_start_time');
                    const endTimeInput = document.getElementById('direct_end_time');
                    const durationInput = document.getElementById('direct_duration');
                    const costInput = document.getElementById('direct_estimated_cost');
                    const infoBox = document.getElementById('direct-selected-time-info');
                    const infoText = infoBox.querySelector(".selected-time-text");
                    const form = document.getElementById('directScheduleForm');
                    const modalTitle = document.getElementById('newDirectScheduleModalLabel');
                    
                    // Función para extraer información de horario del título del modal
                    function extractTimeFromTitle() {
                        if (!modalTitle) return null;
                        
                        const titleText = modalTitle.textContent || '';
                        const timeMatch = titleText.match(/(\d{1,2}:\d{2}) a (\d{1,2}:\d{2})/);
                        
                        if (timeMatch && timeMatch.length === 3) {
                            return {
                                start: timeMatch[1],
                                end: timeMatch[2]
                            };
                        }
                        return null;
                    }
                    
                    // Función para sincronizar el título con los campos
                    function syncTitleWithFields() {
                        const timeInfo = extractTimeFromTitle();
                        if (!timeInfo) return;
                        
                        console.log("⚠️ Detectado título con horario, sincronizando campos:", timeInfo);
                        
                        // Actualizar campos con los valores del título
                        if (startTimeInput && timeInfo.start) {
                            startTimeInput.value = timeInfo.start;
                        }
                        
                        if (endTimeInput && timeInfo.end) {
                            endTimeInput.value = timeInfo.end;
                        }
                        
                        // Calcular duración basada en los horarios
                        calculateDurationFromTimes(timeInfo.start, timeInfo.end);
                        
                        // Actualizar InfoBox
                        updateInfoBoxFromTimes(timeInfo.start, timeInfo.end);
                    }
                    
                    // Calcular duración basada en horarios de inicio y fin
                    function calculateDurationFromTimes(start, end) {
                        if (!start || !end || !durationInput || !dateInput.value) return;
                        
                        try {
                            const date = dateInput.value;
                            const startDateTime = new Date(`${date}T${start}`);
                            
                            // Parsear hora fin
                            const [endHours, endMinutes] = end.split(':').map(Number);
                            const endDateTime = new Date(startDateTime);
                            endDateTime.setHours(endHours, endMinutes);
                            
                            // Si fin es antes que inicio, asumir día siguiente
                            if (endDateTime < startDateTime) {
                                endDateTime.setDate(endDateTime.getDate() + 1);
                            }
                            
                            // Calcular diferencia en minutos
                            const diffMins = Math.round((endDateTime - startDateTime) / 60000);
                            
                            if (diffMins > 0) {
                                durationInput.value = diffMins;
                                console.log(`⏱️ Duración calculada: ${diffMins} minutos`);
                            }
                        } catch (error) {
                            console.error("Error calculando duración:", error);
                        }
                    }
                    
                    // Actualizar infoBox con horarios formateados
                    function updateInfoBoxFromTimes(start, end) {
                        if (!infoText || !infoBox) return;
                        
                        const duration = durationInput ? durationInput.value : "?";
                        infoText.textContent = `${start} - ${end} (${duration} minutos)`;
                        infoBox.classList.remove('d-none');
                    }
                    
                    // 1. CAMBIO DE SERVICIO
                    // Cuando cambia el servicio seleccionado, actualizar duración y precio
                    serviceSelect.addEventListener('change', function() {
                        const selectedOption = this.options[this.selectedIndex];
                        if (selectedOption && selectedOption.value) {
                            // Obtener datos del servicio
                            const duration = selectedOption.getAttribute("data-duration") || "60";
                            const price = selectedOption.getAttribute("data-price") || "0";
                            
                            console.log("✨ Servicio seleccionado:", { 
                                service: selectedOption.text,
                                duration: duration,
                                price: price
                            });
                            
                            // Actualizar campos
                            durationInput.value = duration;
                            costInput.value = price;
                            
                            // Aplicar efecto visual
                            durationInput.classList.add("field-highlight");
                            costInput.classList.add("field-highlight");
                            setTimeout(() => {
                                durationInput.classList.remove("field-highlight");
                                costInput.classList.remove("field-highlight");
                            }, 1000);
                            
                            // Actualizar hora fin basada en la nueva duración
                            updateEndTimeFromDuration();
                        }
                    });
                    
                    // 2. CAMBIO DE HORA INICIO O FECHA
                    // Al cambiar fecha u hora inicio, recalcular hora fin
                    startTimeInput.addEventListener('input', updateEndTimeFromDuration);
                    dateInput.addEventListener('input', updateEndTimeFromDuration);
                    
                    // 3. CAMBIO DE DURACIÓN
                    // Al cambiar duración, recalcular hora fin
                    durationInput.addEventListener('input', updateEndTimeFromDuration);
                    
                    // 4. CAMBIO DE HORA FIN
                    // Al cambiar hora fin, recalcular duración
                    endTimeInput.addEventListener('input', updateDurationFromEndTime);
                    
                    // 5. FUNCIONES DE ACTUALIZACIÓN
                    
                    // Actualizar hora fin basada en duración
                    function updateEndTimeFromDuration() {
                        if (!dateInput.value || !startTimeInput.value) {
                            return; // No hay datos suficientes
                        }
                        
                        try {
                            // Construir fecha/hora inicio
                            const startDateTime = new Date(`${dateInput.value}T${startTimeInput.value}`);
                            
                            // Obtener duración
                            const durationMins = parseInt(durationInput.value) || 60;
                            
                            // Calcular hora fin
                            const endDateTime = new Date(startDateTime.getTime() + durationMins * 60000);
                            
                            // Formatear hora fin
                            const endHours = endDateTime.getHours().toString().padStart(2, "0");
                            const endMinutes = endDateTime.getMinutes().toString().padStart(2, "0");
                            
                            // Actualizar campo
                            endTimeInput.value = `${endHours}:${endMinutes}`;
                            
                            // Efecto visual
                            endTimeInput.classList.add("field-highlight");
                            setTimeout(() => endTimeInput.classList.remove("field-highlight"), 1000);
                            
                            // Actualizar infoBox
                            updateInfoBox(startDateTime, endDateTime, durationMins);
                            
                        } catch (error) {
                            console.error("Error calculando hora fin:", error);
                        }
                    }
                    
                    // Actualizar duración basada en hora fin
                    function updateDurationFromEndTime() {
                        if (!dateInput.value || !startTimeInput.value || !endTimeInput.value) {
                            return; // No hay datos suficientes
                        }
                        
                        try {
                            // Obtener componentes de tiempo
                            const startDateTime = new Date(`${dateInput.value}T${startTimeInput.value}`);
                            const endTimeParts = endTimeInput.value.split(':');
                            
                            if (endTimeParts.length !== 2) {
                                return; // Formato incorrecto
                            }
                            
                            // Crear fecha de fin con misma fecha que inicio
                            const endDateTime = new Date(startDateTime);
                            endDateTime.setHours(parseInt(endTimeParts[0]), parseInt(endTimeParts[1]));
                            
                            // Si fin es anterior a inicio, asumir día siguiente
                            if (endDateTime < startDateTime) {
                                endDateTime.setDate(endDateTime.getDate() + 1);
                            }
                            
                            // Calcular diferencia en minutos
                            const durationMins = Math.round((endDateTime - startDateTime) / 60000);
                            
                            // Actualizar campo duración
                            durationInput.value = durationMins > 0 ? durationMins : 60;
                            
                            // Efecto visual
                            durationInput.classList.add("field-highlight");
                            setTimeout(() => durationInput.classList.remove("field-highlight"), 1000);
                            
                            // Actualizar infoBox
                            updateInfoBox(startDateTime, endDateTime, durationMins);
                            
                        } catch (error) {
                            console.error("Error calculando duración:", error);
                        }
                    }
                    
                    // Actualizar infoBox con información de horario
                    function updateInfoBox(startDateTime, endDateTime, duration) {
                        if (!infoText) return;
                        
                        // Formatear horas
                        const formattedStart = startDateTime.toLocaleTimeString("es-ES", {
                            hour: "2-digit", 
                            minute: "2-digit"
                        });
                        
                        const formattedEnd = endDateTime.toLocaleTimeString("es-ES", {
                            hour: "2-digit", 
                            minute: "2-digit"
                        });
                        
                        // Actualizar texto
                        infoText.textContent = `${formattedStart} - ${formattedEnd} (${duration} minutos)`;
                        
                        // Mostrar infoBox
                        infoBox.classList.remove("d-none");
                    }
                    
                    // Inicializar cálculos al abrir el modal
                    document.querySelector('#newDirectScheduleModal').addEventListener('shown.bs.modal', function() {
                        console.log("🔄 Modal directo abierto - inicializando...");
                        
                        // 1. PRIMERO: Comprobar si hay información de horario en el título
                        syncTitleWithFields();
                        
                        // 2. Si ya hay valores y no se detectó horario en el título, calcular hora fin
                        if (dateInput.value && startTimeInput.value && durationInput.value) {
                            updateEndTimeFromDuration();
                        }
                    });
                    
                    // Ejecutar también en DOMContentLoaded para garantizar que los campos
                    // se sincronicen incluso si el modal ya estaba abierto
                    document.addEventListener('DOMContentLoaded', function() {
                        // Si el modal está visible (ya abierto), sincronizar
                        if (document.querySelector('#newDirectScheduleModal').classList.contains('show')) {
                            console.log("🔍 Modal ya estaba abierto - sincronizando título y campos...");
                            syncTitleWithFields();
                        }
                    });
                    
                    // Envío del formulario
                    form.addEventListener('submit', function(e) {
                        // El refresco del calendario lo maneja el script calendar-auto-refresh.js
                    });
                </script>
            </form>
        </div>
    </div>
</div>