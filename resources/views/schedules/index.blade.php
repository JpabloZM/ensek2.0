@extends('layouts.admin')

@section('page-title', 'Calendario de Agendamientos')

@section('content')
<div class="container-fluid px-0 px-sm-3 overflow-hidden">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6 col-12 mb-2 mb-md-0">
            <div class="d-flex align-items-center flex-wrap">
                <div class="btn-group me-2 mb-2 mb-sm-0">
                    <button id="prev-btn" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-chevron-left"></i><span class="d-none d-sm-inline"> Anterior</span>
                    </button>
                    <button id="next-btn" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-chevron-right"></i><span class="d-none d-sm-inline"> Siguiente</span>
                    </button>
                </div>
                <h4 id="calendar-title" class="mb-0 text-truncate">Calendario</h4>
            </div>
        </div>
        <div class="col-md-6 col-12 text-md-end">
            <div class="d-flex justify-content-end flex-wrap">
                <button type="button" class="btn btn-success me-2 mb-2 mb-md-0" data-bs-toggle="modal" data-bs-target="#newTechnicianModal">
                    <i class="fas fa-user-plus"></i><span class="d-none d-sm-inline"> Agregar Técnico</span>
                </button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newScheduleModal">
                    <i class="fas fa-plus"></i><span class="d-none d-sm-inline"> Nuevo Agendamiento</span>
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Solicitudes de servicio pendientes -->
        <div class="col-lg-3 col-md-4 d-none d-md-block mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white py-2">
                    <h6 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-clipboard-list me-2"></i>
                        Solicitudes Pendientes
                        <span class="badge bg-light text-primary ms-2">{{ count($pendingRequests) }}</span>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush pending-requests-list" style="max-height: 650px; overflow-y: auto;">
                        @forelse($pendingRequests as $request)
                            <div class="list-group-item list-group-item-action py-3 lh-sm">
                                <div class="d-flex w-100 justify-content-between mb-1">
                                    <h6 class="mb-1 text-primary">{{ $request->service->name }}</h6>
                                    <small class="text-muted">{{ $request->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">{{ $request->client_name }}</p>
                                <small class="d-block mb-2">{{ Str::limit($request->description, 60) }}</small>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.schedules.create', ['service_request_id' => $request->id]) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-calendar-plus"></i> Agendar
                                    </a>
                                    <a href="{{ route('admin.service-requests.show', $request->id) }}" class="btn btn-outline-info">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item py-4 text-center text-muted">
                                <i class="fas fa-check-circle fa-2x mb-2"></i>
                                <p class="mb-0">No hay solicitudes pendientes</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Calendario -->
        <div class="col-lg-9 col-md-8 col-12 mb-3">
            <div class="card">
                <div class="card-body p-0 p-sm-3">
                    <div class="calendar-container overflow-hidden">
                        <div id="technician-calendar" class="technician-calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Botón flotante para mostrar solicitudes en móviles -->
    <button type="button" class="btn btn-primary rounded-circle position-fixed d-md-none" id="showPendingRequestsBtn" style="bottom: 20px; right: 20px; width: 60px; height: 60px; z-index: 1050;">
        <i class="fas fa-clipboard-list fa-lg"></i>
    </button>
</div>

<!-- Modal para crear nuevo agendamiento -->
<div class="modal fade" id="newScheduleModal" tabindex="-1" aria-labelledby="newScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newScheduleModalLabel">Nuevo Agendamiento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.schedules.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="service_request_id" class="form-label">Solicitud de Servicio</label>
                            <select class="form-select" id="service_request_id" name="service_request_id" required>
                                <option value="">Seleccione una solicitud...</option>
                                @foreach($pendingRequests as $request)
                                    <option value="{{ $request->id }}">
                                        {{ $request->service->name }} - {{ $request->client_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="technician_id" class="form-label">Técnico</label>
                            <select class="form-select" id="technician_id" name="technician_id" required>
                                <option value="">Seleccione un técnico...</option>
                                @foreach($technicians as $technician)
                                    <option value="{{ $technician->id }}">
                                        {{ $technician->user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="scheduled_date" class="form-label">Fecha y Hora</label>
                        <input type="datetime-local" class="form-control" id="scheduled_date" name="scheduled_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notas</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para ver detalles de agendamiento -->
<div class="modal fade" id="scheduleDetailsModal" tabindex="-1" aria-labelledby="scheduleDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scheduleDetailsModalLabel">Detalles del Agendamiento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="scheduleDetailsContent">
                <!-- El contenido se cargará dinámicamente -->
            </div>
            <div class="modal-footer">
                <div class="d-flex flex-wrap w-100">
                    <button type="button" class="btn btn-secondary me-md-2 mb-2 mb-md-0 flex-fill" data-bs-dismiss="modal">Cerrar</button>
                    <a href="#" id="editScheduleBtn" class="btn btn-primary flex-fill">Editar</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear nuevo técnico -->
<div class="modal fade" id="newTechnicianModal" tabindex="-1" aria-labelledby="newTechnicianModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="newTechnicianModalLabel">
                    <i class="fas fa-user-plus me-2"></i>Agregar Nuevo Técnico
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addTechnicianForm">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        El técnico se agregará al sistema con una contraseña temporal y aparecerá inmediatamente en el calendario.
                    </div>
                    
                    <div class="mb-3">
                        <label for="tech_name" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="tech_name" name="tech_name" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tech_email" class="form-label">Email <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="tech_email" name="tech_email" required>
                        </div>
                        <div class="form-text">Este email se usará para iniciar sesión en el sistema</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tech_phone" class="form-label">Teléfono</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="text" class="form-control" id="tech_phone" name="tech_phone">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tech_specialty" class="form-label">Especialidad</label>
                        <select class="form-select" id="tech_specialty" name="tech_specialty">
                            <option value="">Seleccione una especialidad...</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tech_skills" class="form-label">Habilidades</label>
                        <textarea class="form-control" id="tech_skills" name="tech_skills" rows="2" placeholder="Ej: Electricidad, Fontanería, Carpintería, etc."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tech_availability" class="form-label">Disponibilidad</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                            <input type="text" class="form-control" id="tech_availability" name="tech_availability" placeholder="Lunes a Viernes 9am-5pm">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success" id="saveTechnicianBtn">
                        <i class="fas fa-save me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para mostrar solicitudes pendientes en dispositivos móviles -->
<div class="modal fade" id="pendingRequestsModal" tabindex="-1" aria-labelledby="pendingRequestsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="pendingRequestsModalLabel">
                    <i class="fas fa-clipboard-list me-2"></i>Solicitudes Pendientes
                    <span class="badge bg-light text-primary ms-2">{{ count($pendingRequests) }}</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($pendingRequests as $request)
                        <div class="list-group-item list-group-item-action py-3 lh-sm">
                            <div class="d-flex w-100 justify-content-between mb-1">
                                <h6 class="mb-1 text-primary">{{ $request->service->name }}</h6>
                                <small class="text-muted">{{ $request->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1">{{ $request->client_name }}</p>
                            <small class="d-block mb-2">{{ Str::limit($request->description, 60) }}</small>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.schedules.create', ['service_request_id' => $request->id]) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-calendar-plus"></i> Agendar
                                </a>
                                <a href="{{ route('admin.service-requests.show', $request->id) }}" class="btn btn-outline-info">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item py-4 text-center text-muted">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <p class="mb-0">No hay solicitudes pendientes</p>
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .calendar-container {
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
    }

    .technician-calendar {
        height: 650px;
        max-width: 100%;
    }
    
    .fc-header-toolbar {
        flex-wrap: wrap;
    }
    
    .fc .fc-toolbar-title {
        font-size: 1.4em;
    }
    
    .fc-view-harness {
        width: 100% !important;
        overflow: hidden !important;
    }
    
    /* Ensure calendar content stays in bounds */
    .fc-scrollgrid {
        width: 100% !important;
        max-width: 100%;
        border-collapse: collapse;
    }
    
    .fc-scrollgrid-section-header, 
    .fc-scrollgrid-section-body {
        width: 100%;
    }
    
    .fc-daygrid-event {
        cursor: pointer;
    }
    
    .fc-timeline-slot-lane {
        height: 60px;
    }
    
    .fc-col-header-cell {
        background-color: #f8f9fa;
        font-weight: bold;
    }
    
    .fc-resource-cell {
        font-weight: bold;
        vertical-align: middle !important;
        background-color: #f8f9fa;
    }
    
    .schedule-pending {
        background-color: #ffc107 !important;
        border-color: #ffc107 !important;
    }
    
    .schedule-inprogress {
        background-color: #17a2b8 !important;
        border-color: #17a2b8 !important;
    }
    
    .schedule-completed {
        background-color: #28a745 !important;
        border-color: #28a745 !important;
    }
    
    .schedule-cancelled {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
    }
    
    /* Fix for FullCalendar resource timeline */
    .fc-timeline .fc-timeline-slots {
        width: 100%;
    }
    
    /* Estilos para la lista de solicitudes pendientes */
    .pending-requests-list {
        scrollbar-width: thin;
    }
    
    .pending-requests-list::-webkit-scrollbar {
        width: 6px;
    }
    
    .pending-requests-list::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    .pending-requests-list::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }
    
    /* Estilos para el botón flotante */
    #showPendingRequestsBtn {
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        transition: transform 0.2s;
    }
    
    #showPendingRequestsBtn:hover {
        transform: scale(1.05);
    }
    
    /* Estilos responsivos para el calendario */
    @media (max-width: 992px) {
        .technician-calendar {
            height: 600px;
        }
        
        .fc-resource-timeline-divider {
            width: 1px !important;
        }
        
        .fc-toolbar-chunk {
            margin-bottom: 0.5rem;
        }
        
        /* Ajuste para evitar que los eventos se desborden en tablets */
        .fc-event-title {
            white-space: normal !important;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            display: box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            box-orient: vertical;
        }
    }
    
    @media (max-width: 768px) {
        .technician-calendar {
            height: 500px;
        }
        
        .fc-resource-timeline-divider {
            width: 1px !important;
        }
        
        .fc-header-toolbar {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        
        .fc-toolbar-chunk {
            margin-bottom: 0.5rem;
            width: 100%;
        }
        
        .fc .fc-toolbar-title {
            font-size: 1.2em;
        }
        
        /* Optimizar visualización en móviles */
        .fc-resource-timeline-divider {
            display: none !important;
        }
        
        .fc-event {
            margin: 1px 0 !important;
            padding: 2px !important;
        }
    }
    
    /* Optimizaciones adicionales para pantallas muy pequeñas */
    @media (max-width: 480px) {
        .fc-toolbar-chunk .fc-button-group {
            display: flex;
            width: 100%;
        }
        
        .fc-toolbar-chunk .fc-button-group .fc-button {
            flex: 1;
        }
        
        .fc-resource-timeline-header {
            font-size: 0.9em;
        }
        
        .technician-calendar {
            height: 450px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Datos de recursos (técnicos)
        const resources = JSON.parse('@json($resourcesJson)');
        
        // Datos de eventos (agendamientos)
        const events = JSON.parse('@json($eventsJson)');
        
        // Detectar si es móvil o tablet
        const isMobile = window.innerWidth < 768;
        const isTablet = window.innerWidth >= 768 && window.innerWidth < 992;
        
        // Configuración para el botón de solicitudes pendientes en móviles
        const showPendingRequestsBtn = document.getElementById('showPendingRequestsBtn');
        if (showPendingRequestsBtn) {
            showPendingRequestsBtn.addEventListener('click', function() {
                const pendingRequestsModal = new bootstrap.Modal(document.getElementById('pendingRequestsModal'));
                pendingRequestsModal.show();
            });
        }
        
        // Inicializar el calendario con más funcionalidades
        const calendarEl = document.getElementById('technician-calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            // Vista principal - ajustar según tamaño de pantalla
            initialView: isMobile ? 'timeGridDay' : 'resourceTimelineDay',
            schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
            
            // Barra de herramientas personalizada
            headerToolbar: {
                left: isMobile ? 'today' : 'today dayGridMonth,timeGridWeek,resourceTimelineDay',
                center: 'title',
                right: isMobile ? 'prev,next' : 'prevYear,prev,next,nextYear'
            },
            
            // Configuración de tiempo
            slotDuration: isMobile ? '01:00:00' : '00:30:00',
            slotMinTime: '07:00:00',
            slotMaxTime: '19:00:00',
            snapDuration: '00:15:00',
            
            // Personalización de la vista
            resourceAreaWidth: isMobile ? '25%' : (isTablet ? '20%' : '15%'),
            height: isMobile ? 'auto' : undefined,
            
            // Datos iniciales
            resources: resources,
            events: events,
            
            // Funcionalidades de interacción
            editable: true,
            eventResourceEditable: true,
            nowIndicator: true,
            navLinks: !isMobile, // desactivar en móviles
            selectable: true,
            selectMirror: true,
            allDaySlot: false,
            scrollTimeReset: false,
            
            // Texto en español
            locale: 'es',
            buttonText: {
                today: 'Hoy',
                month: 'Mes',
                week: 'Semana',
                day: 'Día'
            },
            // Personalización de la columna de recursos (técnicos)
            resourceLabelDidMount: function(info) {
                const techName = document.createElement('div');
                techName.innerHTML = info.resource.title;
                techName.classList.add('fw-bold');
                
                info.el.querySelector('.fc-datagrid-cell-main').innerHTML = '';
                info.el.querySelector('.fc-datagrid-cell-main').appendChild(techName);
                
                // Añadir color personalizado
                const resourceColor = info.resource.extendedProps && info.resource.extendedProps.color;
                if (resourceColor) {
                    info.el.style.borderLeft = `4px solid ${resourceColor}`;
                }
            },
            
            // Permitir seleccionar un rango para crear un nuevo agendamiento
            select: function(info) {
                // Mostrar un modal para crear un agendamiento
                const startDate = info.startStr;
                const endDate = info.endStr;
                const resourceId = info.resource ? info.resource.id : null;
                
                if (resourceId) {
                    // Preseleccionar técnico en el modal
                    const technicianSelect = document.getElementById('technician_id');
                    if (technicianSelect) {
                        technicianSelect.value = resourceId;
                    }
                    
                    // Establecer la fecha y hora
                    const dateInput = document.getElementById('scheduled_date');
                    if (dateInput) {
                        // Formatear la fecha para el input datetime-local
                        const formattedDate = startDate.slice(0, 16);
                        dateInput.value = formattedDate;
                    }
                    
                    // Mostrar el modal
                    const modal = new bootstrap.Modal(document.getElementById('newScheduleModal'));
                    modal.show();
                }
            },
            eventClassNames: function(arg) {
                // Agregar clase según el estado del agendamiento
                if (arg.event.extendedProps.status === 'pendiente') {
                    return ['schedule-pending'];
                } else if (arg.event.extendedProps.status === 'en proceso') {
                    return ['schedule-inprogress'];
                } else if (arg.event.extendedProps.status === 'completado') {
                    return ['schedule-completed'];
                } else if (arg.event.extendedProps.status === 'cancelado') {
                    return ['schedule-cancelled'];
                }
            },
            eventClick: function(info) {
                // Mostrar detalles del agendamiento al hacer clic
                const scheduleId = info.event.id;
                showScheduleDetails(scheduleId);
            },
            eventDrop: function(info) {
                // Actualizar fecha y técnico cuando se arrastra un evento
                const scheduleId = info.event.id;
                const resourceId = info.event.getResources()[0].id;
                const startTime = info.event.start.toISOString();
                
                // Pedir confirmación antes de actualizar
                Swal.fire({
                    title: '¿Confirmar cambio?',
                    html: `
                        <p>¿Está seguro de cambiar este agendamiento?</p>
                        <p><strong>Técnico:</strong> ${info.event.getResources()[0].title}</p>
                        <p><strong>Nueva fecha:</strong> ${new Date(startTime).toLocaleString('es-ES')}</p>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, cambiar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateSchedule(scheduleId, resourceId, startTime);
                    } else {
                        // Revertir el cambio si el usuario cancela
                        info.revert();
                    }
                });
            },
            
            // Cuando se redimensiona un evento (cambiar duración)
            eventResize: function(info) {
                const scheduleId = info.event.id;
                const startTime = info.event.start.toISOString();
                const endTime = info.event.end.toISOString();
                
                // Pedir confirmación antes de actualizar
                Swal.fire({
                    title: '¿Confirmar cambio de duración?',
                    html: `
                        <p>¿Está seguro de cambiar la duración de este agendamiento?</p>
                        <p><strong>Nueva hora inicio:</strong> ${new Date(startTime).toLocaleTimeString('es-ES')}</p>
                        <p><strong>Nueva hora fin:</strong> ${new Date(endTime).toLocaleTimeString('es-ES')}</p>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, cambiar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Actualizar con la nueva duración
                        updateScheduleDuration(scheduleId, startTime, endTime);
                    } else {
                        info.revert();
                    }
                });
            }
        });
        
        calendar.render();
        
        // Actualizar título del calendario
        updateCalendarTitle(calendar);
        
        // Botones de navegación
        document.getElementById('prev-btn').addEventListener('click', function() {
            calendar.prev();
            updateCalendarTitle(calendar);
        });
        
        document.getElementById('next-btn').addEventListener('click', function() {
            calendar.next();
            updateCalendarTitle(calendar);
        });
        
        // Función para actualizar el título del calendario
        function updateCalendarTitle(calendar) {
            const dateStr = calendar.view.currentStart;
            const date = new Date(dateStr);
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('calendar-title').textContent = date.toLocaleDateString('es-ES', options);
        }
        
        // Responder a cambios en el tamaño de la ventana
        window.addEventListener('resize', function() {
            const width = window.innerWidth;
            const isMobile = width < 768;
            const isTablet = width >= 768 && width < 992;
            
            // Actualizar configuración del calendario según el tamaño de pantalla
            calendar.setOption('slotDuration', isMobile ? '01:00:00' : '00:30:00');
            calendar.setOption('resourceAreaWidth', isMobile ? '25%' : (isTablet ? '20%' : '15%'));
            
            // Ajustar vista del calendario según el dispositivo
            if (isMobile && calendar.view.type.includes('resource')) {
                calendar.changeView('timeGridDay');
                calendar.setOption('headerToolbar', {
                    left: 'today',
                    center: 'title',
                    right: 'prev,next'
                });
            } else if (!isMobile && calendar.view.type === 'timeGridDay') {
                calendar.changeView('resourceTimelineDay');
                calendar.setOption('headerToolbar', {
                    left: 'today dayGridMonth,timeGridWeek,resourceTimelineDay',
                    center: 'title',
                    right: 'prevYear,prev,next,nextYear'
                });
            }
            
            // Forzar refrescado del calendario
            calendar.updateSize();
            
            // Refrescar eventos para asegurar que se muestran correctamente
            if (width < 500) {
                calendar.setOption('eventMaxStack', 1);
            } else if (width < 768) {
                calendar.setOption('eventMaxStack', 2);
            } else {
                calendar.setOption('eventMaxStack', 3);
            }
        });
        
        // Función para mostrar detalles de un agendamiento
        function showScheduleDetails(scheduleId) {
            fetch(`/admin/schedules/${scheduleId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const schedule = data.schedule;
                        
                        let statusBadge = '';
                        if (schedule.status === 'pendiente') {
                            statusBadge = '<span class="badge bg-warning">Pendiente</span>';
                        } else if (schedule.status === 'en proceso') {
                            statusBadge = '<span class="badge bg-info">En proceso</span>';
                        } else if (schedule.status === 'completado') {
                            statusBadge = '<span class="badge bg-success">Completado</span>';
                        } else {
                            statusBadge = '<span class="badge bg-danger">Cancelado</span>';
                        }
                        
                        let content = `
                            <div class="mb-3">
                                <h6>Cliente:</h6>
                                <p>${schedule.service_request.client_name}</p>
                            </div>
                            <div class="mb-3">
                                <h6>Servicio:</h6>
                                <p>${schedule.service_request.service.name}</p>
                            </div>
                            <div class="mb-3">
                                <h6>Técnico:</h6>
                                <p>${schedule.technician.user.name}</p>
                            </div>
                            <div class="mb-3">
                                <h6>Fecha y hora:</h6>
                                <p>${new Date(schedule.scheduled_date).toLocaleString('es-ES')}</p>
                            </div>
                            <div class="mb-3">
                                <h6>Estado:</h6>
                                <p>${statusBadge}</p>
                            </div>`;
                            
                        if (schedule.notes) {
                            content += `
                            <div class="mb-3">
                                <h6>Notas:</h6>
                                <p>${schedule.notes}</p>
                            </div>`;
                        }
                        
                        document.getElementById('scheduleDetailsContent').innerHTML = content;
                        document.getElementById('editScheduleBtn').href = `/admin/schedules/${scheduleId}/edit`;
                        
                        const scheduleDetailsModal = new bootstrap.Modal(document.getElementById('scheduleDetailsModal'));
                        scheduleDetailsModal.show();
                    } else {
                        alert('No se pudo cargar la información del agendamiento.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar los detalles del agendamiento.');
                });
        }
        
        // Función para actualizar un agendamiento (al arrastrarlo)
        function updateSchedule(scheduleId, technicianId, scheduledDate) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Mostrar indicador de carga
            Swal.fire({
                title: 'Actualizando agendamiento...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(`/admin/schedules/${scheduleId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    technician_id: technicianId,
                    scheduled_date: scheduledDate
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Actualizado',
                        text: 'Agendamiento actualizado correctamente',
                        icon: 'success',
                        timer: 2000
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo actualizar el agendamiento',
                        icon: 'error'
                    });
                    calendar.refetchEvents(); // Recargar eventos en caso de error
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Error al actualizar el agendamiento',
                    icon: 'error'
                });
                calendar.refetchEvents(); // Recargar eventos en caso de error
            });
        }
        
        // Función para actualizar la duración de un agendamiento
        function updateScheduleDuration(scheduleId, startDate, endDate) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Mostrar indicador de carga
            Swal.fire({
                title: 'Actualizando duración...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(`/admin/schedules/${scheduleId}/update-duration`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    start_date: startDate,
                    end_date: endDate
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Actualizado',
                        text: 'Duración actualizada correctamente',
                        icon: 'success',
                        timer: 2000
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'No se pudo actualizar la duración',
                        icon: 'error'
                    });
                    calendar.refetchEvents(); // Recargar eventos en caso de error
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Error al actualizar la duración del agendamiento',
                    icon: 'error'
                });
                calendar.refetchEvents(); // Recargar eventos en caso de error
            });
        }
        
        // Manejar el formulario para añadir un nuevo técnico
        document.getElementById('addTechnicianForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validar formulario
            const nameField = document.getElementById('tech_name');
            const emailField = document.getElementById('tech_email');
            
            if (!nameField.value.trim()) {
                alert('Por favor ingrese el nombre del técnico');
                nameField.focus();
                return;
            }
            
            if (!emailField.value.trim() || !emailField.value.includes('@')) {
                alert('Por favor ingrese un email válido');
                emailField.focus();
                return;
            }
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const formData = new FormData();
            
            // Recolectar datos del formulario
            formData.append('name', nameField.value);
            formData.append('email', emailField.value);
            formData.append('phone', document.getElementById('tech_phone').value);
            formData.append('specialty_id', document.getElementById('tech_specialty').value);
            formData.append('skills', document.getElementById('tech_skills').value);
            formData.append('availability', document.getElementById('tech_availability').value);
            
            // Mostrar indicador de carga
            document.getElementById('saveTechnicianBtn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
            document.getElementById('saveTechnicianBtn').disabled = true;
            
            fetch('/admin/technicians/quick-add', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor: ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Añadir el nuevo técnico al calendario
                    calendar.addResource({
                        id: data.technician.id,
                        title: data.technician.name
                    });
                    
                    // Cerrar el modal y limpiar el formulario
                    const modal = bootstrap.Modal.getInstance(document.getElementById('newTechnicianModal'));
                    modal.hide();
                    document.getElementById('addTechnicianForm').reset();
                    
                    // Mostrar mensaje de éxito con la contraseña temporal
                    Swal.fire({
                        title: 'Técnico agregado con éxito',
                        html: `
                            <div class="text-start">
                                <p><strong>Nombre:</strong> ${data.technician.name}</p>
                                <p><strong>Email:</strong> ${data.technician.email}</p>
                                <p><strong>Contraseña temporal:</strong> <span class="text-danger">${data.password}</span></p>
                                <p class="text-warning"><small>Guarde esta contraseña, no se mostrará nuevamente.</small></p>
                            </div>
                        `,
                        icon: 'success',
                        confirmButtonText: 'Entendido'
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'No se pudo agregar el técnico',
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Error al agregar el técnico. Por favor, inténtelo de nuevo.',
                    icon: 'error'
                });
            })
            .finally(() => {
                document.getElementById('saveTechnicianBtn').innerHTML = '<i class="fas fa-save me-1"></i>Guardar';
                document.getElementById('saveTechnicianBtn').disabled = false;
            });
        });
    });
</script>
@endpush
