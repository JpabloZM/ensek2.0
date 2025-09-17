@extends('admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Calendario de Citas</h1>
        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAppointmentModal">
                <i class="fas fa-plus-circle"></i> Nueva Cita
            </button>
        </div>
    </div>

    <div class="filters-container mb-3 p-2 bg-light rounded shadow-sm">
        <div class="row align-items-center">
            <div class="col-md-3 mb-2 mb-md-0">
                <label for="technicianFilter" class="form-label small mb-1">Técnico</label>
                <select id="technicianFilter" class="form-select form-select-sm">
                    <option value="">Todos los Técnicos</option>
                    @foreach($technicians as $technician)
                        <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-2 mb-md-0">
                <label for="statusFilter" class="form-label small mb-1">Estado</label>
                <select id="statusFilter" class="form-select form-select-sm">
                    <option value="">Todos los Estados</option>
                    <option value="scheduled">Programada</option>
                    <option value="completed">Completada</option>
                    <option value="cancelled">Cancelada</option>
                    <option value="rescheduled">Reprogramada</option>
                </select>
            </div>
            <div class="col-md-3 mb-2 mb-md-0">
                <label for="confirmationFilter" class="form-label small mb-1">Confirmación</label>
                <select id="confirmationFilter" class="form-select form-select-sm">
                    <option value="">Todas las Confirmaciones</option>
                    <option value="confirmed">Confirmadas</option>
                    <option value="pending">Pendientes</option>
                    <option value="declined">Rechazadas</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">&nbsp;</label>
                <div class="d-flex">
                    <button id="refreshCalendar" class="btn btn-sm btn-outline-primary flex-grow-1 me-1">
                        <i class="fas fa-sync-alt"></i> Actualizar
                    </button>
                    <button id="sendRemindersBtn" class="btn btn-sm btn-warning flex-shrink-0">
                        <i class="fas fa-bell"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Calendario</h6>
                </div>
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear cita -->
<div class="modal fade" id="createAppointmentModal" tabindex="-1" aria-labelledby="createAppointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createAppointmentModalLabel">Nueva Cita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createAppointmentForm" action="{{ route('appointments.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="service_request_id" class="form-label">Solicitud de Servicio</label>
                        <select class="form-select" id="service_request_id" name="service_request_id" required>
                            <option value="">Seleccione una solicitud</option>
                            @foreach($pendingRequests as $request)
                                <option value="{{ $request->id }}">
                                    {{ $request->client->name }} - {{ $request->service->name }} ({{ $request->address }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="date" name="date" required min="{{ date('Y-m-d') }}">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="start_time" class="form-label">Hora Inicio</label>
                            <input type="time" class="form-control" id="start_time" name="start_time" required>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="end_time" class="form-label">Hora Fin</label>
                            <input type="time" class="form-control" id="end_time" name="end_time" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="technician_id" class="form-label">Técnico</label>
                        <div class="input-group">
                            <select class="form-select" id="technician_id" name="technician_id" required>
                                <option value="">Seleccione un técnico</option>
                                @foreach($technicians as $technician)
                                    <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-outline-primary" type="button" id="suggestTechnician">
                                <i class="fas fa-magic"></i> Sugerir
                            </button>
                        </div>
                        <div class="form-text" id="technicianHelp">
                            Seleccione un técnico o haga clic en "Sugerir" para obtener recomendaciones basadas en disponibilidad y especialidad.
                        </div>
                    </div>

                    <div id="techniciansRecommendation" class="mb-3 d-none">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Técnicos Recomendados</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush" id="techniciansRecommendationList">
                                    <!-- Lista de técnicos recomendados (se llenará con JavaScript) -->
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notas</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="submitAppointment">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver detalles de la cita -->
<div class="modal fade" id="appointmentDetailsModal" tabindex="-1" aria-labelledby="appointmentDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="appointmentDetailsModalLabel">Detalles de la Cita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6>Servicio</h6>
                    <p id="eventService" class="mb-0"></p>
                </div>
                <div class="mb-3">
                    <h6>Cliente</h6>
                    <p id="eventClient" class="mb-0"></p>
                </div>
                <div class="mb-3">
                    <h6>Técnico</h6>
                    <p id="eventTechnician" class="mb-0"></p>
                </div>
                <div class="mb-3">
                    <h6>Fecha y Hora</h6>
                    <p id="eventDateTime" class="mb-0"></p>
                </div>
                <div class="mb-3">
                    <h6>Dirección</h6>
                    <p id="eventAddress" class="mb-0"></p>
                </div>
                <div class="mb-3">
                    <h6>Estado</h6>
                    <p id="eventStatus" class="mb-0"></p>
                </div>
                <div class="mb-3">
                    <h6>Confirmación</h6>
                    <p id="eventConfirmation" class="mb-0"></p>
                </div>
                <div class="mb-3">
                    <h6>Notas</h6>
                    <p id="eventNotes" class="mb-0"></p>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-primary" id="editAppointmentBtn">Editar</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de conflicto de horario -->
<div class="modal fade" id="conflictModal" tabindex="-1" aria-labelledby="conflictModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="conflictModalLabel"><i class="fas fa-exclamation-triangle"></i> Conflicto Detectado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>El técnico seleccionado ya tiene una cita programada en el horario especificado.</p>
                <div class="alert alert-warning">
                    <h6>Detalles del conflicto:</h6>
                    <div id="conflictDetails">
                        <!-- Aquí se mostrarán los detalles del conflicto -->
                    </div>
                </div>
                <p>Por favor seleccione otro técnico o cambie el horario de la cita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para confirmar envío de recordatorios -->
<div class="modal fade" id="sendRemindersModal" tabindex="-1" aria-labelledby="sendRemindersModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendRemindersModalLabel">Enviar Recordatorios</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Desea enviar recordatorios para las citas programadas para mañana?</p>
                <p>Esta acción enviará correos electrónicos a los clientes y técnicos.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmSendReminders">
                    <span id="sendRemindersSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    Enviar Recordatorios
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Estilos básicos de FullCalendar -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<!-- Nuestros estilos personalizados para FullCalendar -->
<link href="{{ asset('css/fullcalendar-custom.css') }}" rel="stylesheet">

<!-- Librerías en orden correcto -->
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>

<!-- FullCalendar Bundle (incluye todos los plugins básicos) -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js"></script>

<!-- FullCalendar Scheduler (para vistas de recursos) -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@5.11.3/main.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar FullCalendar con vista de recursos
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
            locale: 'es',
            initialView: 'resourceTimeGridDay',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'resourceTimeGridDay,resourceTimeGridWeek'
            },
            // Configuración de recursos (técnicos como columnas)
            resources: function(fetchInfo, successCallback, failureCallback) {
                // Obtener todos los técnicos
                fetch("{{ route('appointments.technicians') }}")
                    .then(response => response.json())
                    .then(data => {
                        console.log("Técnicos cargados:", data); // Depuración
                        // Formatear los técnicos como recursos
                        const resources = data.map(tech => ({
                            id: tech.id,
                            title: tech.name,
                            eventColor: tech.eventColor || '#87c947' // Color ENSEK para los eventos de este técnico
                        }));
                        successCallback(resources);
                    })
                    .catch(error => {
                        console.error('Error cargando técnicos:', error);
                        failureCallback(error);
                    });
            },
            allDaySlot: false,
            slotMinTime: '08:00:00',
            slotMaxTime: '20:00:00',
            slotDuration: '00:30:00',
            navLinks: true,
            selectable: true,
            selectMirror: true,
            editable: true,
            dayMaxEvents: false, // Permitir mostrar todos los eventos
            nowIndicator: true,
            businessHours: {
                daysOfWeek: [1, 2, 3, 4, 5], // Lunes a viernes
                startTime: '08:00',
                endTime: '18:00',
            },
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            },
            events: function(info, successCallback, failureCallback) {
                const technicianId = $('#technicianFilter').val();
                const status = $('#statusFilter').val();
                const confirmation = $('#confirmationFilter').val();
                
                let url = "{{ route('appointments.calendar-data') }}?";
                let params = [];
                
                if (technicianId) params.push("technician_id=" + technicianId);
                if (status) params.push("status=" + status);
                if (confirmation) params.push("confirmation=" + confirmation);
                
                url += params.join("&");
                
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        console.log("Eventos cargados:", data); // Depuración
                        // Adaptar datos para la vista de recursos
                        const adaptedEvents = data.map(event => {
                            // Asegurarnos de que el evento tenga un resourceId válido
                            return {
                                id: event.id,
                                title: event.title,
                                start: event.start,
                                end: event.end,
                                resourceId: event.extendedProps.technician_id.toString(), // Asegurarse de que sea string
                                color: event.color,
                                extendedProps: event.extendedProps
                            };
                        });
                        console.log("Eventos adaptados:", adaptedEvents); // Depuración
                        successCallback(adaptedEvents);
                    })
                    .catch(error => {
                        console.error('Error cargando eventos:', error);
                        failureCallback(error);
                    });
            },
            eventClick: function(info) {
                // Mostrar detalles del evento
                showAppointmentDetails(info.event);
            },
            select: function(info) {
                // Preparar modal para crear una nueva cita
                // Si el recurso (técnico) está seleccionado, lo pre-seleccionamos en el formulario
                const resourceId = info.resource ? info.resource.id : '';
                prepareCreateModal(info.startStr, info.endStr, resourceId);
            },
            eventDrop: function(info) {
                // Cuando un evento se arrastra y suelta en otra posición
                const event = info.event;
                const resourceId = info.newResource ? info.newResource.id : info.event.getResources()[0].id;
                const startDateTime = event.start;
                const endDateTime = event.end || new Date(startDateTime.getTime() + 60*60*1000); // Si no hay hora de fin, añadir 1 hora
                
                // Confirmar cambio
                if (confirm('¿Está seguro de reprogramar esta cita a ' + 
                          startDateTime.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) + ' - ' + 
                          endDateTime.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) + '?')) {
                    
                    // Convertir fechas a formato adecuado para el servidor
                    const date = startDateTime.toISOString().split('T')[0];
                    const startTime = startDateTime.toTimeString().substr(0, 5);
                    const endTime = endDateTime.toTimeString().substr(0, 5);
                    
                    // Enviar actualización al servidor
                    fetch("{{ route('appointments.update', '') }}/" + event.id, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            technician_id: resourceId,
                            date: date,
                            start_time: startTime,
                            end_time: endTime,
                            status: 'rescheduled', // Marcar como reprogramada
                            _method: 'PUT' // Para métodos PUT en formularios
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            // Si hay un error, revertir el cambio
                            info.revert();
                            return response.json().then(data => Promise.reject(data));
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Mostrar mensaje de éxito
                        alert('Cita reprogramada exitosamente');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al reprogramar la cita: ' + (error.message || 'Error desconocido'));
                        info.revert();
                    });
                } else {
                    // Si el usuario cancela, revertir el cambio
                    info.revert();
                }
            },
            eventResize: function(info) {
                // Cuando un evento se redimensiona (cambiar duración)
                const event = info.event;
                const startDateTime = event.start;
                const endDateTime = event.end;
                
                // Confirmar cambio
                if (confirm('¿Está seguro de cambiar la duración de esta cita a ' + 
                          startDateTime.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) + ' - ' + 
                          endDateTime.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) + '?')) {
                    
                    // Convertir fechas a formato adecuado para el servidor
                    const date = startDateTime.toISOString().split('T')[0];
                    const startTime = startDateTime.toTimeString().substr(0, 5);
                    const endTime = endDateTime.toTimeString().substr(0, 5);
                    
                    // Enviar actualización al servidor
                    fetch("{{ route('appointments.update', '') }}/" + event.id, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            date: date,
                            start_time: startTime,
                            end_time: endTime,
                            status: 'rescheduled', // Marcar como reprogramada
                            _method: 'PUT' // Para métodos PUT en formularios
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            // Si hay un error, revertir el cambio
                            info.revert();
                            return response.json().then(data => Promise.reject(data));
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Mostrar mensaje de éxito
                        alert('Duración de cita actualizada exitosamente');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al actualizar la duración de la cita: ' + (error.message || 'Error desconocido'));
                        info.revert();
                    });
                } else {
                    // Si el usuario cancela, revertir el cambio
                    info.revert();
                }
            }
        });
        
        calendar.render();
        
        // Manejar filtros del calendario
        $('#technicianFilter, #statusFilter, #confirmationFilter').change(function() {
            calendar.refetchEvents();
        });
        
        // Manejar botón de actualizar
        $('#refreshCalendar').click(function() {
            calendar.refetchEvents();
        });

        // Manejar botón de enviar recordatorios
        $('#sendRemindersBtn').click(function() {
            $('#sendRemindersModal').modal('show');
        });
        
        // Función para mostrar detalles de la cita
        function showAppointmentDetails(event) {
            var eventData = event.extendedProps;
            
            // Rellenar el modal con la información del evento
            document.getElementById('eventService').textContent = event.title;
            document.getElementById('eventClient').textContent = eventData.client;
            document.getElementById('eventTechnician').textContent = eventData.technician;
            document.getElementById('eventDateTime').textContent = event.start.toLocaleDateString() + ' ' + 
                                                                event.start.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) + ' - ' + 
                                                                event.end.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            document.getElementById('eventAddress').textContent = eventData.address;
            
            // Estado con formato de badge
            var statusBadge = getStatusBadge(eventData.status);
            document.getElementById('eventStatus').innerHTML = statusBadge;
            
            // Confirmación con formato de badge
            var confirmationBadge = getConfirmationBadge(eventData.confirmation_status || 'pending');
            document.getElementById('eventConfirmation').innerHTML = confirmationBadge;
            
            document.getElementById('eventNotes').textContent = eventData.notes || 'Sin notas';
            
            // Configurar el botón de editar
            var editBtn = document.getElementById('editAppointmentBtn');
            editBtn.href = "{{ route('appointments.edit', '') }}/" + event.id;
            
            // Mostrar el modal
            $('#appointmentDetailsModal').modal('show');
        }
        
        // Función para preparar el modal de creación con la fecha y hora seleccionadas
        function prepareCreateModal(startStr, endStr, resourceId = '') {
            var start = new Date(startStr);
            var end = new Date(endStr);
            
            // Formatear la fecha para el input de fecha
            var dateStr = start.toISOString().split('T')[0];
            
            // Formatear las horas para los inputs de hora
            var startTimeStr = start.toTimeString().substr(0, 5);
            var endTimeStr = end.toTimeString().substr(0, 5);
            
            // Establecer los valores en el formulario
            document.getElementById('date').value = dateStr;
            document.getElementById('start_time').value = startTimeStr;
            document.getElementById('end_time').value = endTimeStr;
            
            // Limpiar otros campos
            document.getElementById('service_request_id').value = '';
            document.getElementById('technician_id').value = resourceId; // Pre-seleccionar el técnico si se ha proporcionado
            document.getElementById('notes').value = '';
            
            // Ocultar la sección de recomendaciones ya que estamos seleccionando manualmente
            document.getElementById('techniciansRecommendation').classList.add('d-none');
            
            // Mostrar el modal
            $('#createAppointmentModal').modal('show');
        }
        
        // Función para obtener un badge HTML para el estado de la cita
        function getStatusBadge(status) {
            var badgeClass, statusText;
            
            switch(status) {
                case 'scheduled':
                    badgeClass = 'bg-primary';
                    statusText = 'Programada';
                    break;
                case 'completed':
                    badgeClass = 'bg-success';
                    statusText = 'Completada';
                    break;
                case 'cancelled':
                    badgeClass = 'bg-danger';
                    statusText = 'Cancelada';
                    break;
                case 'rescheduled':
                    badgeClass = 'bg-warning';
                    statusText = 'Reprogramada';
                    break;
                default:
                    badgeClass = 'bg-secondary';
                    statusText = status;
            }
            
            return '<span class="badge ' + badgeClass + '">' + statusText + '</span>';
        }
        
        // Función para obtener un badge HTML para el estado de confirmación
        function getConfirmationBadge(status) {
            var badgeClass, statusText;
            
            switch(status) {
                case 'confirmed':
                    badgeClass = 'bg-success';
                    statusText = 'Confirmada';
                    break;
                case 'declined':
                    badgeClass = 'bg-danger';
                    statusText = 'Rechazada';
                    break;
                case 'pending':
                default:
                    badgeClass = 'bg-warning';
                    statusText = 'Pendiente';
            }
            
            return '<span class="badge ' + badgeClass + '">' + statusText + '</span>';
        }
        
        // Evento para sugerir técnicos
        document.getElementById('suggestTechnician').addEventListener('click', function() {
            var serviceRequestId = document.getElementById('service_request_id').value;
            var date = document.getElementById('date').value;
            var startTime = document.getElementById('start_time').value;
            var endTime = document.getElementById('end_time').value;
            
            if (!serviceRequestId || !date || !startTime || !endTime) {
                alert('Por favor complete todos los campos necesarios: solicitud, fecha, hora inicio y hora fin.');
                return;
            }
            
            // Mostrar spinner de carga
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Buscando...';
            this.disabled = true;
            
            // Realizar la petición AJAX para obtener recomendaciones
            fetch("{{ route('appointments.suggest-technicians') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    service_request_id: serviceRequestId,
                    date: date,
                    start_time: startTime,
                    end_time: endTime
                })
            })
            .then(response => response.json())
            .then(data => {
                // Restablecer el botón
                document.getElementById('suggestTechnician').innerHTML = '<i class="fas fa-magic"></i> Sugerir';
                document.getElementById('suggestTechnician').disabled = false;
                
                if (data.error) {
                    alert(data.error);
                    return;
                }
                
                if (data.technicians && data.technicians.length > 0) {
                    // Mostrar la sección de recomendaciones
                    document.getElementById('techniciansRecommendation').classList.remove('d-none');
                    
                    // Llenar la lista de técnicos recomendados
                    var list = document.getElementById('techniciansRecommendationList');
                    list.innerHTML = '';
                    
                    data.technicians.forEach(function(tech) {
                        var item = document.createElement('a');
                        item.href = '#';
                        item.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
                        if (tech.id === data.recommended) {
                            item.classList.add('active');
                            // Seleccionar automáticamente el técnico recomendado
                            document.getElementById('technician_id').value = tech.id;
                        }
                        
                        var badge = '';
                        if (tech.workload === 0) {
                            badge = '<span class="badge bg-success">Sin citas hoy</span>';
                        } else {
                            badge = '<span class="badge bg-info">' + tech.workload + ' citas hoy</span>';
                        }
                        
                        var specialistBadge = '';
                        if (tech.is_specialist) {
                            specialistBadge = '<span class="badge bg-primary ms-2">Especialista</span>';
                        }
                        
                        item.innerHTML = '<div><strong>' + tech.name + '</strong>' + specialistBadge + '</div>' + badge;
                        
                        item.addEventListener('click', function(e) {
                            e.preventDefault();
                            document.getElementById('technician_id').value = tech.id;
                            
                            // Marcar como activo
                            list.querySelectorAll('a').forEach(function(el) {
                                el.classList.remove('active');
                            });
                            this.classList.add('active');
                        });
                        
                        list.appendChild(item);
                    });
                } else {
                    alert('No se encontraron técnicos disponibles para el horario seleccionado.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('suggestTechnician').innerHTML = '<i class="fas fa-magic"></i> Sugerir';
                document.getElementById('suggestTechnician').disabled = false;
                alert('Ocurrió un error al buscar técnicos disponibles.');
            });
        });
        
        // Evento para verificar conflictos antes de guardar
        document.getElementById('submitAppointment').addEventListener('click', function() {
            var form = document.getElementById('createAppointmentForm');
            
            // Validar el formulario primero
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            var technicianId = document.getElementById('technician_id').value;
            var date = document.getElementById('date').value;
            var startTime = document.getElementById('start_time').value;
            var endTime = document.getElementById('end_time').value;
            
            // Verificar conflictos antes de enviar
            fetch("{{ route('appointments.check-conflicts') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    technician_id: technicianId,
                    date: date,
                    start_time: startTime,
                    end_time: endTime
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.conflicts && data.conflicts.length > 0) {
                    // Hay conflictos, mostrar el modal
                    var conflictDetails = document.getElementById('conflictDetails');
                    conflictDetails.innerHTML = '';
                    
                    data.conflicts.forEach(function(conflict) {
                        var item = document.createElement('div');
                        item.className = 'mb-2';
                        item.innerHTML = '<strong>' + conflict.service_name + '</strong><br>' +
                                        conflict.start_time + ' - ' + conflict.end_time;
                        conflictDetails.appendChild(item);
                    });
                    
                    $('#conflictModal').modal('show');
                } else {
                    // No hay conflictos, enviar el formulario
                    form.submit();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // En caso de error, permitir enviar el formulario de todos modos
                form.submit();
            });
        });
        
        // Evento para enviar recordatorios
        document.getElementById('confirmSendReminders').addEventListener('click', function() {
            // Mostrar spinner
            document.getElementById('sendRemindersSpinner').classList.remove('d-none');
            this.disabled = true;
            
            // Enviar la solicitud para los recordatorios
            fetch("{{ route('appointments.send-reminders') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Ocultar spinner
                document.getElementById('sendRemindersSpinner').classList.add('d-none');
                this.disabled = false;
                
                // Cerrar el modal
                $('#sendRemindersModal').modal('hide');
                
                // Mostrar mensaje de éxito
                alert(data.message);
            })
            .catch(error => {
                console.error('Error:', error);
                // Ocultar spinner
                document.getElementById('sendRemindersSpinner').classList.add('d-none');
                this.disabled = false;
                
                // Mostrar mensaje de error
                alert('Ocurrió un error al enviar los recordatorios.');
            });
        });
    });
</script>
@endsection