@extends('layouts.admin')

@section('page-title', 'Calendario de Agendamientos')

@section('styles')
<!-- Estilos para la selección por arrastre en el calendario -->
<!-- Estilos unificados para el calendario -->
<link rel="stylesheet" href="{{ asset('css/calendar-drag-selection.css') }}">
<link rel="stylesheet" href="{{ asset('css/calendar-unified-selection.css') }}">
<link rel="stylesheet" href="{{ asset('css/calendar-smooth-motion.css') }}">
<!-- Estilos para el selector de agendamiento -->
<link rel="stylesheet" href="{{ asset('css/scheduling-selector.css') }}">
<!-- Estilos específicos para botón de servicio directo -->
<link rel="stylesheet" href="{{ asset('css/direct-service-button.css') }}">

<!-- Estilos para el resaltado de campos en modales -->
<link rel="stylesheet" href="{{ asset('css/field-animations.css') }}">
    /* Los estilos de animación ahora están en field-animations.css */
    
    /* Estilos para los servicios en el select */
    #direct_service_id option {
        padding: 8px;
    }
    
    /* Mejoras visuales para las tarjetas del modal directo */
    .direct-schedule-card {
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 2px 5px rgba(0,0,0,0.08);
    }
    
    .direct-schedule-card:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .direct-schedule-card .card-header {
        background: linear-gradient(to right, #004122, #006633);
        color: white;
        font-weight: bold;
        border: none;
    }
    
    .card-header-icon {
        margin-right: 8px;
        background-color: rgba(255,255,255,0.2);
        border-radius: 50%;
        padding: 5px;
        width: 24px;
        height: 24px;
        display: inline-flex;
        justify-content: center;
        align-items: center;
    }
    
    /* Enfatizar el selector de servicios */
    #direct_service_id {
        border: 2px solid #87c947;
        font-size: 1.1em;
        background-color: #f9fff5;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-0 px-sm-3 overflow-hidden">
    <!-- Encabezado con controles del calendario -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-6 col-12 mb-2 mb-md-0">
            <div class="d-flex align-items-center flex-wrap">
                <div class="btn-group me-3 mb-2 mb-sm-0">
                    <button id="prev-btn" class="btn btn-sm btn-outline-secondary border-dark-subtle">
                        <i class="fas fa-chevron-left"></i><span class="d-none d-sm-inline"> Anterior</span>
                    </button>
                    <button id="next-btn" class="btn btn-sm btn-outline-secondary border-dark-subtle">
                        <i class="fas fa-chevron-right"></i><span class="d-none d-sm-inline"> Siguiente</span>
                    </button>
                </div>
                <h4 id="calendar-title" class="mb-0 text-truncate fw-bold">Calendario</h4>
            </div>
        </div>
        <div class="col-md-6 col-12 text-md-end">
            <div class="d-flex justify-content-end flex-wrap">
                <button type="button" class="btn btn-success me-2 mb-2 mb-md-0 text-white" style="background-color: #004122; border: none;" data-bs-toggle="modal" data-bs-target="#newTechnicianModal">
                    <i class="fas fa-user-plus"></i><span class="d-none d-sm-inline"> Agregar Técnico</span>
                </button>
                <button type="button" class="btn btn-primary" style="background-color: #87c947; border: none; color: #004122;" data-bs-toggle="modal" data-bs-target="#newScheduleModal">
                    <i class="fas fa-plus"></i><span class="d-none d-sm-inline"> Nuevo Agendamiento</span>
                </button>
            </div>
        </div>
    </div>
    
    <div class="filters-container mb-3 p-3 bg-white rounded shadow-sm" style="border: 1px solid rgba(0, 65, 34, 0.1);">
        <div class="row align-items-center">
            <div class="col-md-3 mb-2 mb-md-0">
                <label for="technicianFilter" class="form-label small mb-1 fw-semibold text-secondary">
                    <i class="fas fa-user-hard-hat me-1 text-green-dark"></i> Técnico
                </label>
                <select id="technicianFilter" class="form-select form-select-sm border-light-subtle">
                    <option value="">Todos los Técnicos</option>
                    @foreach($technicians as $technician)
                        <option value="{{ $technician->id }}">{{ $technician->user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-2 mb-md-0">
                <label for="statusFilter" class="form-label small mb-1 fw-semibold text-secondary">
                    <i class="fas fa-tasks me-1 text-green-dark"></i> Estado
                </label>
                <select id="statusFilter" class="form-select form-select-sm border-light-subtle">
                    <option value="">Todos los Estados</option>
                    <option value="pending">Pendiente</option>
                    <option value="in_progress">En Proceso</option>
                    <option value="completed">Completado</option>
                    <option value="cancelled">Cancelado</option>
                </select>
            </div>
            <div class="col-md-3 mb-2 mb-md-0">
                <label for="confirmationFilter" class="form-label small mb-1 fw-semibold text-secondary">
                    <i class="fas fa-check-circle me-1 text-green-dark"></i> Confirmación
                </label>
                <select id="confirmationFilter" class="form-select form-select-sm border-light-subtle">
                    <option value="">Todas las Confirmaciones</option>
                    <option value="confirmed">Confirmadas</option>
                    <option value="pending">Pendientes</option>
                    <option value="declined">Rechazadas</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">&nbsp;</label>
                <div class="d-flex">
                    <button id="refreshCalendar" class="btn btn-sm btn-outline-primary flex-grow-1 me-2" style="border-color: #004122; color: #004122;">
                        <i class="fas fa-sync-alt"></i> Actualizar
                    </button>
                    <button id="sendRemindersBtn" class="btn btn-sm" style="background-color: #FFC107; color: #212529;">
                        <i class="fas fa-bell"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Solicitudes de servicio pendientes -->
        <div class="col-lg-3 col-md-4 d-none d-md-block mb-3">
            <div class="card shadow-sm h-100 border-0" style="border-radius: 8px;">
                <div class="card-header bg-green-dark text-white py-3" style="border-top-left-radius: 8px; border-top-right-radius: 8px;">
                    <h6 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-clipboard-list me-2"></i>
                        Solicitudes Pendientes
                        <span class="badge bg-light text-green-dark ms-2 fw-semibold">{{ count($pendingRequests) }}</span>
                    </h6>
                </div>
                <style>
                    .bg-green-dark {
                        background-color: #004122;
                    }
                    .text-green-dark {
                        color: #004122;
                    }
                    
                    /* Estilos para la leyenda del calendario */
                    .legend-dot {
                        width: 10px;
                        height: 10px;
                        border-radius: 50%;
                        margin-right: 5px;
                        display: inline-block;
                    }
                    
                    /* Mejoras para el panel lateral */
                    .pending-requests-list {
                        scrollbar-width: thin;
                        scrollbar-color: #adb5bd #f8f9fa;
                    }
                    
                    .pending-requests-list::-webkit-scrollbar {
                        width: 6px;
                    }
                    
                    .pending-requests-list::-webkit-scrollbar-track {
                        background: #f8f9fa;
                        border-radius: 3px;
                    }
                    
                    .pending-requests-list::-webkit-scrollbar-thumb {
                        background-color: #adb5bd;
                        border-radius: 3px;
                    }
                    
                    .pending-requests-list .list-group-item {
                        transition: all 0.2s ease;
                        border-left: 3px solid transparent;
                    }
                    
                    .pending-requests-list .list-group-item:hover {
                        background-color: #f8f9fa;
                        border-left-color: #87c947;
                    }
                </style>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush pending-requests-list" style="max-height: 650px; overflow-y: auto;">
                        @forelse($pendingRequests as $request)
                            <div class="list-group-item list-group-item-action py-3 lh-sm border-0 border-bottom">
                                <div class="d-flex w-100 justify-content-between mb-1">
                                    <h6 class="mb-1 fw-bold text-green-dark">{{ $request->service->name }}</h6>
                                    <small class="text-muted">{{ $request->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1 fw-semibold">{{ $request->client_name }}</p>
                                <small class="d-block mb-3 text-secondary">{{ Str::limit($request->description, 60) }}</small>
                                <div class="btn-group btn-group-sm w-100" role="group">
                                    <button data-service-request-id="{{ $request->id }}" 
                                           data-client-name="{{ $request->client_name }}"
                                           data-client-phone="{{ $request->client_phone }}"
                                           data-client-email="{{ $request->client_email ?? '' }}"
                                           data-service-id="{{ $request->service_id }}"
                                           data-service-name="{{ $request->service->name }}"
                                           data-description="{{ $request->description }}"
                                           data-address="{{ $request->address ?? '' }}"
                                           class="btn btn-sm schedule-from-request" style="background-color: #87c947; color: #004122; flex: 1; border: none;">
                                        <i class="fas fa-calendar-plus"></i> Agendar
                                    </button>
                                    <a href="{{ route('admin.service-requests.show', $request->id) }}" class="btn btn-sm btn-outline-secondary" style="flex: 1; border-color: #dee2e6;">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item py-5 text-center text-muted border-0">
                                <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                                <p class="mb-0 fw-semibold">No hay solicitudes pendientes</p>
                                <p class="small mb-0 mt-1">¡Todo está agendado!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Calendario -->
        <div class="col-lg-9 col-md-8 col-12 mb-3">
            <div class="card shadow-sm h-100 overflow-hidden border-0">
                <div class="card-header bg-white py-3 px-3 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="mb-0 fw-bold text-primary"><i class="fas fa-calendar-alt me-2"></i>Calendario de Servicios</h6>
                    <div class="d-flex align-items-center">
                        <div class="view-toggle-button me-3 d-none d-md-block">
                            <button class="btn btn-sm btn-outline-primary" id="dayViewBtn" style="background-color: #87c947; color: #004122; border: none;">
                                <i class="fas fa-calendar-day me-1"></i> DÍA
                            </button>
                        </div>
                        <div class="legend-item me-2 d-flex align-items-center">
                            <span class="legend-dot bg-warning"></span>
                            <span class="text-muted small">Pendiente</span>
                        </div>
                        <div class="legend-item me-2 d-flex align-items-center">
                            <span class="legend-dot bg-info"></span>
                            <span class="text-muted small">En proceso</span>
                        </div>
                        <div class="legend-item d-flex align-items-center">
                            <span class="legend-dot" style="background-color:#87c947"></span>
                            <span class="text-muted small">Completado</span>
                        </div>
                        <span class="badge bg-light text-dark ms-2">
                            <i class="far fa-clock me-1"></i>UTC-6
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <!-- Leyenda para tipos de eventos - DESTACADA -->
                    <div class="calendar-header-legend mb-2">
                        <div class="container-fluid p-0">
                            <div class="row g-0 align-items-center">
                                <div class="col-12">
                                    <div class="d-flex flex-wrap align-items-center py-2 px-3" style="border-bottom: 2px solid #eaeaea; background-color: #f8f9fa;">
                                        <span class="fw-bold me-3" style="color: #004122; font-size: 16px;">Tipos de eventos:</span>
                                        <div class="d-flex flex-wrap">
                                            <div class="legend-item me-2 mb-1">
                                                <span class="legend-color" style="background-color: #7CAAD4;"></span>
                                                <span>Citas con clientes</span>
                                            </div>
                                            <div class="legend-item me-2 mb-1">
                                                <span class="legend-color" style="background-color: #A8D7A8;"></span>
                                                <span>Reuniones</span>
                                            </div>
                                            <div class="legend-item me-2 mb-1">
                                                <span class="legend-color" style="background-color: #F9D971;"></span>
                                                <span>Descansos/Comidas</span>
                                            </div>
                                            <div class="legend-item mb-1">
                                                <span class="legend-color" style="background-color: #a2c4e5;"></span>
                                                <span>Conferencias/Llamadas</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Controles del calendario -->
                    <div class="calendar-controls mb-3">
                        <div class="date-nav">
                            <button id="prev-day" class="btn btn-sm btn-outline-secondary"><i class="fas fa-chevron-left"></i></button>
                            <span class="current-date" id="current-date">{{ now()->format('d') }} de {{ now()->locale('es')->format('F') }}, {{ now()->format('Y') }}</span>
                            <button id="next-day" class="btn btn-sm btn-outline-secondary"><i class="fas fa-chevron-right"></i></button>
                            <button class="btn btn-sm btn-outline-primary ms-2" id="today">Hoy</button>
                            <button class="btn btn-sm btn-outline-danger ms-2" id="currentHour">
                                <i class="far fa-clock me-1"></i>Hora Actual
                            </button>
                        </div>
                        
                        <div class="view-options">
                            <button class="btn btn-sm btn-outline-primary me-2" id="dayViewBtn">Día</button>
                            <button class="btn btn-sm btn-outline-secondary" id="weekViewBtn">Semana</button>
                        </div>
                    </div>

                    <!-- Nuevo contenedor del calendario estructurado -->
                    <div class="technician-calendar-container">
                        <!-- Cabecera del calendario -->
                        <div class="calendar-header">
                            <!-- Celda de hora en la cabecera -->
                            <div class="calendar-header-hour">Hora</div>
                            
                            <!-- Celdas de técnicos en la cabecera -->
                            @foreach($technicians as $technician)
                            <div class="calendar-header-tech">
                                <div class="calendar-header-tech-name" title="{{ $technician->user->name }}">{{ Str::limit($technician->user->name, 15) }}</div>
                                <div class="calendar-header-tech-specialty" title="{{ $technician->specialty ?? 'Técnico' }}">{{ $technician->specialty ?? 'Técnico' }}</div>
                            </div>
                            @endforeach
                        </div>
                        
                        <!-- Filas de horas (las 24 horas del día con media hora) -->
                        @php
                            $startHour = 0;
                            $endHour = 23;
                        @endphp
                        
                        @for($hour = $startHour; $hour <= $endHour; $hour++)
                        <!-- Hora exacta -->
                        <div class="calendar-row" data-hour="{{ $hour }}" data-minute="0">
                            <div class="calendar-hour-cell" data-hour="{{ $hour }}" data-minute="0">
                                {{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}:00
                            </div>
                            @foreach($technicians as $technician)
                            <div class="calendar-service-cell" data-hour="{{ $hour }}" data-minute="0" data-technician-id="{{ $technician->id }}">
                                <!-- Aquí se cargarán los servicios dinámicamente -->
                            </div>
                            @endforeach
                        </div>
                        
                        <!-- Media hora -->
                        <div class="calendar-row calendar-half-hour-row" data-hour="{{ $hour }}" data-minute="30">
                            <div class="calendar-hour-cell calendar-half-hour-cell" data-hour="{{ $hour }}" data-minute="30">
                                {{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}:30
                            </div>
                            @foreach($technicians as $technician)
                            <div class="calendar-service-cell calendar-half-hour-cell" data-hour="{{ $hour }}" data-minute="30" data-technician-id="{{ $technician->id }}">
                                <!-- Aquí se cargarán los servicios dinámicamente -->
                            </div>
                            @endforeach
                        </div>
                        @endfor
                        
                        <!-- Indicador de hora actual -->
                        <div class="current-time-indicator"></div>
                    </div>
                    
                    <!-- Contenedor original del calendario (oculto inicialmente) -->
                    <div class="calendar-container overflow-hidden d-none">
                        <div id="technician-calendar" class="technician-calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal para crear nuevo agendamiento (Diseño alternativo) -->
<div class="modal fade" id="newScheduleModal" tabindex="-1" aria-labelledby="newScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="background: linear-gradient(120deg, #f8f9fa 60%, #e9f5ee 100%);">
            <div class="modal-header bg-white border-0" style="border-bottom: 2px solid #87c947;">
                <h5 class="modal-title d-flex align-items-center fw-bold text-success" id="newScheduleModalLabel">
                    <span class="me-2" style="font-size: 2rem;"><i class="fas fa-calendar-plus"></i></span> Nuevo Agendamiento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.schedules.store-direct') }}" method="POST">
                @csrf
                <input type="hidden" name="direct_scheduling" value="1">
                <div class="modal-body px-3 py-4" style="max-height: 70vh; overflow-y: auto;">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm rounded-4 mb-4">
                                <div class="card-body">
                                    <h6 class="fw-bold text-primary mb-3"><i class="fas fa-tools fa-lg me-2"></i>Servicio</h6>
                                    <select class="form-select form-select-lg border-0 mb-2" id="service_id" name="service_id" required style="background-color: #e9f5ee;">
                                        <option value="">Seleccione un servicio...</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                                        @endforeach
                                    </select>
                                    <h6 class="fw-bold text-primary mt-4 mb-3"><i class="fas fa-user-hard-hat fa-lg me-2"></i>Técnico</h6>
                                    <select class="form-select form-select-lg border-0" id="technician_id" name="technician_id" required style="background-color: #e9f5ee;">
                                        <option value="">Seleccione un técnico...</option>
                                        @foreach($technicians as $technician)
                                            <option value="{{ $technician->id }}">{{ $technician->user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="card border-0 shadow-sm rounded-4">
                                <div class="card-body">
                                    <h6 class="fw-bold text-primary mb-3"><i class="fas fa-user fa-lg me-2"></i>Cliente</h6>
                                    <input type="text" class="form-control form-control-lg border-0 mb-3" id="client_name" name="client_name" required style="background-color: #e9f5ee;" placeholder="Nombre del cliente">
                                    <input type="tel" class="form-control form-control-lg border-0 mb-3" id="client_phone" name="client_phone" style="background-color: #e9f5ee;" placeholder="Teléfono de contacto">
                                    <input type="email" class="form-control form-control-lg border-0" id="client_email" name="client_email" style="background-color: #e9f5ee;" placeholder="Correo electrónico">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm rounded-4">
                                <div class="card-body">
                                    <h6 class="fw-bold text-info mb-3"><i class="fas fa-calendar-alt fa-lg me-2"></i>Programación</h6>
                                    <label for="scheduled_date" class="form-label fw-semibold">Fecha y Hora de Inicio</label>
                                    <input type="datetime-local" class="form-control form-control-lg border-0 mb-3" id="scheduled_date" name="scheduled_date" required style="background-color: #e9f5ee;">
                                    <label for="end_time" class="form-label fw-semibold">Hora de Finalización</label>
                                    <input type="time" class="form-control form-control-lg border-0 mb-3" id="end_time" name="end_time" required style="background-color: #e9f5ee;">
                                    <label for="duration" class="form-label fw-semibold">Duración (minutos)</label>
                                    <input type="number" class="form-control form-control-lg border-0 mb-3" id="duration" name="duration" min="15" step="15" value="60" style="background-color: #e9f5ee;">
                                    <label for="estimated_cost" class="form-label fw-semibold">Costo Estimado ($)</label>
                                    <input type="number" class="form-control form-control-lg border-0 mb-3" id="estimated_cost" name="estimated_cost" step="0.01" style="background-color: #e9f5ee;">
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" role="switch" id="send_notification" name="send_notification" checked>
                                        <label class="form-check-label fw-semibold" for="send_notification">Enviar notificación al cliente</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sección de notas adicionales que ocupa todo el ancho -->
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4">
                                <div class="card-body">
                                    <h6 class="fw-bold text-success mb-3"><i class="fas fa-sticky-note fa-lg me-2"></i>Notas Adicionales</h6>
                                    <textarea class="form-control border-0" id="notes" name="notes" rows="4" placeholder="Instrucciones especiales, requerimientos o detalles adicionales del servicio..." style="background-color: #e9f5ee;"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-0 pt-3">
                    <button type="button" class="btn btn-outline-secondary btn-lg px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success btn-lg px-4">
                        <i class="fas fa-calendar-check me-2"></i>Programar Servicio
                    </button>
                </div>
            </form>
            
            <script>
                // Script directo para el cálculo de hora y duración en el modal principal
                document.addEventListener('DOMContentLoaded', function() {
                    // Elementos principales
                    const dateTimeInput = document.getElementById('scheduled_date');
                    const endTimeInput = document.getElementById('end_time');
                    const durationInput = document.getElementById('duration');
                    const infoBox = document.getElementById('selected-time-info');
                    const infoText = infoBox ? infoBox.querySelector(".selected-time-text") : null;
                    
                    // 1. CAMBIO EN FECHA/HORA DE INICIO
                    if (dateTimeInput) {
                        dateTimeInput.addEventListener('input', function() {
                            console.log("📅 Cambio en fecha/hora inicio");
                            calculateEndTime();
                        });
                    }
                    
                    // 2. CAMBIO EN DURACIÓN
                    if (durationInput) {
                        durationInput.addEventListener('input', function() {
                            console.log("⏱️ Cambio en duración");
                            calculateEndTime();
                        });
                    }
                    
                    // 3. CAMBIO EN HORA FIN
                    if (endTimeInput) {
                        endTimeInput.addEventListener('input', function() {
                            console.log("🕒 Cambio en hora fin");
                            calculateDuration();
                        });
                    }
                    
                    // Calcular hora de fin basado en inicio y duración
                    function calculateEndTime() {
                        if (!dateTimeInput || !dateTimeInput.value || !durationInput || !endTimeInput) return;
                        
                        try {
                            const startDateTime = new Date(dateTimeInput.value);
                            const durationMins = parseInt(durationInput.value) || 60;
                            
                            const endDateTime = new Date(startDateTime.getTime() + durationMins * 60000);
                            const hours = endDateTime.getHours().toString().padStart(2, '0');
                            const minutes = endDateTime.getMinutes().toString().padStart(2, '0');
                            
                            endTimeInput.value = `${hours}:${minutes}`;
                            
                            // Aplicar efecto visual
                            endTimeInput.classList.add('field-highlight');
                            setTimeout(() => endTimeInput.classList.remove('field-highlight'), 1000);
                            
                            // Actualizar infoBox
                            updateInfoBox(startDateTime, endDateTime, durationMins);
                            
                        } catch (error) {
                            console.error('Error calculando hora fin:', error);
                        }
                    }
                    
                    // Calcular duración basado en inicio y fin
                    function calculateDuration() {
                        if (!dateTimeInput || !dateTimeInput.value || !endTimeInput || !endTimeInput.value || !durationInput) return;
                        
                        try {
                            const startDateTime = new Date(dateTimeInput.value);
                            
                            // Parsear hora fin
                            const [hours, minutes] = endTimeInput.value.split(':').map(Number);
                            const endDateTime = new Date(startDateTime);
                            endDateTime.setHours(hours, minutes);
                            
                            // Si fin es antes que inicio, asumir día siguiente
                            if (endDateTime < startDateTime) {
                                endDateTime.setDate(endDateTime.getDate() + 1);
                            }
                            
                            // Calcular diferencia en minutos
                            const diffMins = Math.round((endDateTime - startDateTime) / 60000);
                            
                            // Actualizar campo duración
                            durationInput.value = diffMins > 0 ? diffMins : 60;
                            
                            // Aplicar efecto visual
                            durationInput.classList.add('field-highlight');
                            setTimeout(() => durationInput.classList.remove('field-highlight'), 1000);
                            
                            // Actualizar infoBox
                            updateInfoBox(startDateTime, endDateTime, diffMins);
                            
                        } catch (error) {
                            console.error('Error calculando duración:', error);
                        }
                    }
                    
                    // Función de infoBox eliminada, ya no necesitamos mostrar la alerta
                    function updateInfoBox(start, end, duration) {
                        // La función se mantiene vacía pero existente para compatibilidad con el código existente
                        return; // No hace nada, ya que se eliminó la alerta
                    }
                    
                    // Inicializar cálculos cuando se abre el modal
                    document.querySelector('#newScheduleModal').addEventListener('shown.bs.modal', function() {
                        console.log("🔄 Modal de agendamiento abierto - inicializando cálculos");
                        
                        // Primero, verificar si hay información de horario en el título del modal
                        const modalTitle = document.querySelector('#newScheduleModalLabel');
                        if (modalTitle) {
                            const titleText = modalTitle.textContent || '';
                            const timeMatch = titleText.match(/(\d{1,2}:\d{2}) a (\d{1,2}:\d{2})/);
                            
                            if (timeMatch && timeMatch.length === 3) {
                                console.log("⚠️ Detectado título con horario:", timeMatch[1], "a", timeMatch[2]);
                                
                                // Si el modal tiene inicio/fin en el título, sincronizamos todo
                                const startTime = timeMatch[1];
                                const endTime = timeMatch[2];
                                
                                // Si tenemos fecha y hora, pero el final no coincide con el título
                                if (dateTimeInput && dateTimeInput.value) {
                                    // Extraer solo la hora del dateTimeInput
                                    const currentTime = dateTimeInput.value.split('T')[1];
                                    
                                    // Si la hora ya está correcta, no hacemos nada
                                    if (currentTime === startTime) {
                                        console.log("✅ Hora de inicio ya coincide con el título");
                                    } else {
                                        // Si no coincide, actualizamos la fecha+hora
                                        const currentDate = dateTimeInput.value.split('T')[0];
                                        dateTimeInput.value = `${currentDate}T${startTime}`;
                                        console.log("🔄 Hora de inicio actualizada según título:", startTime);
                                    }
                                    
                                    // Actualizar hora de fin
                                    endTimeInput.value = endTime;
                                    console.log("🔄 Hora de fin actualizada según título:", endTime);
                                    
                                    // Calcular duración
                                    try {
                                        const startDateTime = new Date(dateTimeInput.value);
                                        const [endHours, endMinutes] = endTime.split(':').map(Number);
                                        const endDateTime = new Date(startDateTime);
                                        endDateTime.setHours(endHours, endMinutes);
                                        
                                        // Si fin es antes que inicio, asumir día siguiente
                                        if (endDateTime < startDateTime) {
                                            endDateTime.setDate(endDateTime.getDate() + 1);
                                        }
                                        
                                        const diffMins = Math.round((endDateTime - startDateTime) / 60000);
                                        
                                        if (diffMins > 0) {
                                            durationInput.value = diffMins;
                                            console.log("⏱️ Duración calculada:", diffMins, "minutos");
                                        }
                                        
                                        // Actualizar infoBox
                                        const infoBox = document.getElementById('selected-time-info');
                                        if (infoBox) {
                                            const infoText = infoBox.querySelector(".selected-time-text");
                                            if (infoText) {
                                                infoText.textContent = `${startTime} - ${endTime} (${diffMins} minutos)`;
                                                infoBox.classList.remove('d-none');
                                            }
                                        }
                                    } catch (error) {
                                        console.error("Error calculando duración:", error);
                                    }
                                }
                            }
                        }
                        
                        // Si no se detectó información en el título o falló la sincronización,
                        // intentar calcular con los valores actuales
                        if (dateTimeInput && dateTimeInput.value && durationInput && durationInput.value) {
                            calculateEndTime();
                        }
                    });
                });
            </script>
        </div>
    </div>
</div>

<style>
    /* Estilo alternativo para el modal de agendamiento */
    .modal-content {
        border-radius: 1.5rem !important;
    }
    .card {
        border-radius: 1.25rem !important;
        transition: box-shadow 0.2s;
    }
    .card:hover {
        box-shadow: 0 8px 24px rgba(87, 201, 71, 0.12) !important;
    }
    .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 2px #87c94733 !important;
        border-color: #87c947 !important;
    }
    .modal-header {
        border-radius: 1.5rem 1.5rem 0 0 !important;
    }
    .modal-footer {
        border-radius: 0 0 1.5rem 1.5rem !important;
    }
    .modal-body::-webkit-scrollbar {
        width: 8px;
    }
    .modal-body::-webkit-scrollbar-thumb {
        background: #87c947;
        border-radius: 8px;
    }
    .modal-body::-webkit-scrollbar-track {
        background: #e9f5ee;
        border-radius: 8px;
    }
</style>

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
                <div class="row w-100">
                    <div class="col-12 col-md-6 mb-2 mb-md-0" id="additionalActions">
                        <!-- Botones adicionales se cargarán dinámicamente aquí -->
                    </div>
                    <div class="col-12 col-md-6 d-flex">
                        <button type="button" class="btn btn-secondary me-2 flex-fill" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cerrar
                        </button>
                        <a href="#" id="editScheduleBtn" class="btn btn-primary flex-fill">
                            <i class="fas fa-edit me-1"></i>Editar
                        </a>
                    </div>
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

<!-- Incluir el modal para crear agendamiento directo -->
@include('schedules._new_direct_schedule_modal')

<!-- Botón oculto para abrir el modal directo (solo para depuración) -->
<button id="btnOpenDirectModal" data-bs-toggle="modal" data-bs-target="#newDirectScheduleModal" style="display:none;">Abrir Modal Directo</button>
@endsection

@push('styles')
<!-- Estilos principales de FullCalendar -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<!-- Nuevo diseño moderno para el calendario -->
<link href="{{ asset('css/calendar-redesign.css') }}" rel="stylesheet">
<!-- Ajustes específicos para líneas horizontales y nombres de técnicos -->
<link href="{{ asset('css/calendar-specific-fixes.css') }}" rel="stylesheet">
<!-- AJUSTES URGENTES - PRIORIDAD MÁXIMA -->
<link href="{{ asset('css/urgent-fixes.css') }}" rel="stylesheet">
<!-- Mejoras visuales y efectos avanzados -->
<link href="{{ asset('css/calendar-enhancements.css') }}" rel="stylesheet">
<!-- Correcciones específicas de layout y distribución -->
<link href="{{ asset('css/calendar-layout-fixes.css') }}" rel="stylesheet">
<!-- Correcciones críticas para layout -->
<link href="{{ asset('css/calendar-critical-fixes.css') }}" rel="stylesheet">
<!-- Estilo para vista semanal -->
<link href="{{ asset('css/calendar-weekly-view.css') }}" rel="stylesheet">
<!-- Estilo para vista de recursos (técnicos en columnas) -->
<link href="{{ asset('css/calendar-resource-view.css') }}" rel="stylesheet">
<!-- Mejoras en la leyenda del calendario -->
<link href="{{ asset('css/calendar-legend-enhanced.css') }}" rel="stylesheet">
<link href="{{ asset('css/calendar-header-fixes.css') }}" rel="stylesheet">
<!-- Corrección urgente para el indicador de hora -->
<link href="{{ asset('css/calendar-critical-indicator-fix.css') }}" rel="stylesheet">
<!-- Estilos para el selector de tipo de agendamiento -->
<link href="{{ asset('css/scheduling-selector.css') }}" rel="stylesheet">
<style>
    .calendar-container {
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
        border-radius: 5px;
        background-color: #fff;
    }

    .technician-calendar {
        height: 700px; /* Altura fija para un mejor control del espacio */
        max-width: 100%;
        font-family: 'Arial', sans-serif;
    }
    
    /* Mejorar la apariencia de los encabezados */
    .fc-col-header-cell {
        background-color: #f1f5f9;
        font-weight: bold;
    }
    
    /* Mejorar la columna de recursos */
    .fc-resource-cell {
        background-color: #f8fafc;
        font-weight: bold;
        border-right: 2px solid #e2e8f0;
        padding: 10px !important;
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
    
    /* Estilo mejorado para los encabezados */
    .fc-col-header-cell {
        background-color: #f1f5f9;
        font-weight: bold;
        color: #334155;
        border-bottom: 2px solid #e2e8f0;
    }
    
    /* Mejorar el aspecto de los eventos */
    .fc-event {
        border-radius: 4px !important;
        border: none !important;
        padding: 2px 4px !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }
    
    /* Dar más espacio para las celdas del tiempo */
    .fc-timegrid-slot, .fc-timeline-slot {
        height: 3em !important;
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
    
    /* Estilos para estados de confirmación */
    .confirmation-confirmed {
        border-left: 4px solid #28a745 !important;
    }
    
    .confirmation-declined {
        border-left: 4px solid #dc3545 !important;
        opacity: 0.7;
    }
    
    .confirmation-pending {
        border-left: 4px solid #ffc107 !important;
    }
    
    /* Indicadores visuales para estados de confirmación */
    .confirmation-confirmed::before {
        content: "✓";
        position: absolute;
        top: 2px;
        right: 2px;
        background-color: #28a745;
        color: white;
        width: 16px;
        height: 16px;
        font-size: 10px;
        line-height: 16px;
        text-align: center;
        border-radius: 50%;
    }
    
    .confirmation-declined::before {
        content: "×";
        position: absolute;
        top: 2px;
        right: 2px;
        background-color: #dc3545;
        color: white;
        width: 16px;
        height: 16px;
        font-size: 10px;
        line-height: 16px;
        text-align: center;
        border-radius: 50%;
    }
    
    .confirmation-pending::before {
        content: "?";
        position: absolute;
        top: 2px;
        right: 2px;
        background-color: #ffc107;
        color: white;
        width: 16px;
        height: 16px;
        font-size: 10px;
        line-height: 16px;
        text-align: center;
        border-radius: 50%;
    }
    
    /* Fix for FullCalendar resource timeline */
    .fc-timeline .fc-timeline-slots {
        width: 100%;
    }
    
    /* Estilo personalizado con colores corporativos ENSEK */
    .fc-button-primary {
        background-color: #004122 !important;
        border-color: #004122 !important;
    }
    
    .fc-button-primary:hover {
        background-color: #005a30 !important;
        border-color: #005a30 !important;
    }
    
    .fc-button-primary:not(:disabled).fc-button-active, 
    .fc-button-primary:not(:disabled):active {
        background-color: #87c947 !important;
        border-color: #87c947 !important;
    }
    
    .fc-col-header {
        background-color: #f8fafc;
    }
    
    /* Eliminar bordes innecesarios */
    .fc-scrollgrid {
        border: none !important;
    }
    
    /* Añadir un poco de espacio entre filas */
    .fc-timeline-slot, .fc-timegrid-slot {
        padding-top: 1px !important;
        padding-bottom: 1px !important;
    }
    
    /* Hacer las barras de eventos más anchas */
    .fc-timeline-event {
        height: 24px !important;
        line-height: 24px !important;
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
    
    /* Nuevos estilos para mejorar contenido de eventos */
    .fc-event-content-wrapper {
        padding: 2px 4px;
        line-height: 1.2;
    }
    
    .fc-event-title {
        font-size: 13px;
        font-weight: 600 !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 2px;
    }
    
    .fc-event-client {
        font-size: 11px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 2px;
    }
    
    .fc-event-time {
        font-size: 10px;
        opacity: 0.9;
    }
    
    /* Mejorar la apariencia de la cabecera de recursos */
    .fc-resource-area-header {
        background-color: var(--ensek-green-dark) !important;
        color: white !important;
        font-weight: bold !important;
        text-align: center !important;
        padding: 8px 4px !important;
    }
    
    /* Mejorar apariencia de horas */
    .fc-timeline-slot-label-cushion {
        font-weight: 600;
        padding: 4px;
        border-radius: 4px;
        background-color: rgba(135, 201, 71, 0.15);
    }
    
    /* Separación más clara entre técnicos */
    .fc-timeline-lane {
        border-bottom: 2px solid rgba(0,0,0,0.1);
    }
    
    /* Estilos para la leyenda del calendario */
    .calendar-legend {
        background-color: #f8f9fa;
        font-size: 0.85rem;
    }
    
    .legend-title {
        font-weight: 600;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 0;
    }
    
    .legend-color {
        display: inline-block;
        width: 15px;
        height: 15px;
        border-radius: 3px;
        margin-right: 5px;
    }
    
    .appointment-color {
        background-color: #7CAAD4;
    }
    
    .meeting-color {
        background-color: #A8D7A8;
    }
    
    .break-color {
        background-color: #F9D971;
    }
    
    .conference-color {
        background-color: #a2c4e5;
    }
</style>
@endpush

@push('scripts')
<!-- Cargar las librerías en orden correcto -->
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>

<!-- FullCalendar Bundle (incluye todos los plugins básicos) -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js"></script>

<!-- FullCalendar Scheduler (para vistas de recursos) -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@5.11.3/main.min.js"></script>

<!-- Sweet Alert para diálogos interactivos -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Scripts personalizados -->
<script src="{{ asset('js/service-request-scheduler.js') }}"></script>
<script src="{{ asset('js/calendar-auto-refresh.js') }}"></script>

<!-- Contenedor de datos para JavaScript -->
<script type="application/json" id="technicians-data">
    @json($technicians)
</script>

<!-- Tooltips y Popovers de Bootstrap -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Activar todos los tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Botón de vista diaria
        document.getElementById('dayViewBtn').addEventListener('click', function() {
            calendar.changeView('resourceTimeGridDay');
            updateCalendarTitle(calendar);
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Datos de recursos (técnicos)
        const resources = JSON.parse('@json($resourcesJson)');
        
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
        
        // Inicializar el calendario con más funcionalidades y guardarlo como variable global
        const calendarEl = document.getElementById('technician-calendar');
        window.calendar = new FullCalendar.Calendar(calendarEl, {
            // Vista adaptable - Técnicos en columnas, horas en filas para una vista diaria
            initialView: isMobile ? 'timeGridDay' : 'resourceTimeGridDay',
            schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
            
            // Recursos (técnicos)
            resources: resources,
            
            // Barra de herramientas dentro del calendario - Simplificada
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'resourceTimeGridDay,timeGridDay,timeGridWeek,dayGridMonth'
            },
            
            // Títulos personalizados para los botones
            buttonText: {
                today: 'Hoy',
                resourceTimeGridDay: 'Técnicos',
                timeGridDay: 'Día',
                timeGridWeek: 'Semana',
                dayGridMonth: 'Mes'
            },
            
            // Configuraciones de tamaño y visualización optimizadas para vista semanal
            height: 'auto',
            expandRows: true,
            navLinks: true, // clickeable days/week names
            nowIndicator: true, // mostrar indicador de "ahora"
            allDaySlot: false, // no mostrar slot "todo el día"
            weekends: true, // incluir sábado y domingo
            slotDuration: '00:30:00', // intervalos de 30 min
            slotLabelInterval: '01:00', // etiquetas cada hora
            slotMinTime: '08:00:00', // hora de inicio del día
            slotMaxTime: '18:00:00', // hora de fin del día
            
            // Mejora en la visualización de recursos (técnicos)
            resourceAreaHeaderContent: 'Técnicos',
            resourceAreaWidth: '180px',
            
            // Configuración de la escala de tiempo
            slotDuration: '01:00:00', // slots de 1 hora
            snapDuration: '00:15:00', // snap a intervalos de 15 min
            slotLabelInterval: '01:00', // etiquetas cada hora
            slotLabelFormat: {
                hour: 'numeric',
                minute: '2-digit',
                omitZeroMinute: true,
                meridiem: 'short'
            },
            
            // Personalizar la apariencia y comportamiento de eventos
            eventBorderColor: '#fff',
            eventBackgroundColor: '#87c947',
            eventTextColor: '#fff',
            eventMinHeight: 30,
            
            // Formateo de los contenidos de los eventos para vista semanal
            eventContent: function(arg) {
                // Determinar el tipo de evento para estilo específico
                let eventType = '';
                let icon = '';
                
                // Identificar el tipo de evento según el título o servicio
                const eventTitle = arg.event.title.toLowerCase();
                const serviceName = arg.event.extendedProps.service_name || '';
                const serviceNameLower = serviceName.toLowerCase();
                
                if (eventTitle.includes('appointment') || eventTitle.includes('cita') || 
                    serviceNameLower.includes('appointment') || serviceNameLower.includes('cita')) {
                    eventType = 'event-appointment';
                    icon = '<i class="fas fa-user-clock me-1"></i>';
                } else if (eventTitle.includes('meeting') || eventTitle.includes('reunión') || 
                          serviceNameLower.includes('meeting') || serviceNameLower.includes('reunión')) {
                    eventType = 'event-meeting';
                    icon = '<i class="fas fa-users me-1"></i>';
                } else if (eventTitle.includes('lunch') || eventTitle.includes('break') || 
                          eventTitle.includes('almuerzo') || eventTitle.includes('descanso') ||
                          serviceNameLower.includes('lunch') || serviceNameLower.includes('break')) {
                    eventType = 'event-break';
                    icon = '<i class="fas fa-utensils me-1"></i>';
                } else if (eventTitle.includes('conference') || eventTitle.includes('call') || 
                          eventTitle.includes('conferencia') || eventTitle.includes('llamada') ||
                          serviceNameLower.includes('conference') || serviceNameLower.includes('call')) {
                    eventType = 'event-conference';
                    icon = '<i class="fas fa-phone-alt me-1"></i>';
                } else {
                    eventType = 'event-other';
                    icon = '<i class="fas fa-calendar-check me-1"></i>';
                }
                
                // Aplicar la clase CSS al elemento del evento
                if (arg.view.type === 'timeGridWeek' || arg.view.type === 'timeGridDay') {
                    arg.el.classList.add(eventType);
                }
                
                const clientName = arg.event.extendedProps.client_name || '';
                const displayTitle = arg.event.extendedProps.service_name || arg.event.title || '';
                
                // Solo mostrar hora si no es vista semanal (la vista semanal ya muestra horas)
                let timeInfo = '';
                if (arg.view.type !== 'timeGridWeek' && arg.view.type !== 'timeGridDay') {
                    if (arg.event.start) {
                        const startTime = new Date(arg.event.start).toLocaleTimeString('es-ES', {
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        });
                        
                        const endTime = arg.event.end ? new Date(arg.event.end).toLocaleTimeString('es-ES', {
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        }) : '';
                        
                        timeInfo = `<div class="fc-event-time">${startTime}${endTime ? ' - ' + endTime : ''}</div>`;
                    }
                }
                
                return { 
                    html: `
                        <div class="fc-event-content-wrapper">
                            <div class="fc-event-title">
                                ${icon} ${displayTitle}
                            </div>
                            ${clientName ? `<div class="fc-event-client">${clientName}</div>` : ''}
                            ${timeInfo}
                        </div>
                    `
                };
            },
            
            // Personalización de apariencia de eventos según su estado y tipo
            eventDidMount: function(info) {
                const event = info.event;
                const el = info.el;
                
                // Clasificar evento por tipo (para vista semanal)
                const eventTitle = (event.title || '').toLowerCase();
                const serviceName = (event.extendedProps.service_name || '').toLowerCase();
                
                // Clasificación por tipo de evento para color
                if (eventTitle.includes('appointment') || eventTitle.includes('cita') || 
                    serviceName.includes('appointment') || serviceName.includes('cita') ||
                    eventTitle.includes('client') || eventTitle.includes('cliente') || 
                    serviceName.includes('client') || serviceName.includes('cliente')) {
                    el.classList.add('event-appointment');
                } else if (eventTitle.includes('meeting') || eventTitle.includes('reunión') || 
                          serviceName.includes('meeting') || serviceName.includes('reunión')) {
                    el.classList.add('event-meeting');
                } else if (eventTitle.includes('lunch') || eventTitle.includes('break') || 
                          eventTitle.includes('almuerzo') || eventTitle.includes('descanso') ||
                          serviceName.includes('lunch') || serviceName.includes('break')) {
                    el.classList.add('event-break');
                } else if (eventTitle.includes('conference') || eventTitle.includes('call') || 
                          eventTitle.includes('conferencia') || eventTitle.includes('llamada') ||
                          serviceName.includes('conference') || serviceName.includes('call')) {
                    el.classList.add('event-conference');
                } 
                
                // Aplicar clases según el estado del evento (para vista de recursos)
                if (event.extendedProps.status) {
                    el.classList.add('status-' + event.extendedProps.status.replace(' ', '-').toLowerCase());
                } else {
                    el.classList.add('status-pending');
                }
                
                // Aplicar clases según estado de confirmación
                if (event.extendedProps.confirmation_status) {
                    el.classList.add('confirmation-' + event.extendedProps.confirmation_status);
                }
                
                // Si el evento fue modificado recientemente (menos de 1 minuto)
                if (event.extendedProps.updated_at) {
                    const updatedTime = new Date(event.extendedProps.updated_at);
                    const now = new Date();
                    const diffSeconds = (now - updatedTime) / 1000;
                    
                    if (diffSeconds < 60) {
                        el.classList.add('recently-modified');
                    }
                }
                
                // Añadir tooltip con información del evento
                let tooltipContent = event.title;
                if (event.extendedProps.client_name) {
                    tooltipContent += ' - ' + event.extendedProps.client_name;
                }
                
                if (event.start) {
                    const timeFormat = { hour: '2-digit', minute: '2-digit', hour12: true };
                    tooltipContent += '<br>' + new Date(event.start).toLocaleTimeString('es-ES', timeFormat);
                    
                    if (event.end) {
                        tooltipContent += ' - ' + new Date(event.end).toLocaleTimeString('es-ES', timeFormat);
                    }
                }
                
                // Agregar atributos para tooltip Bootstrap
                el.setAttribute('data-bs-toggle', 'tooltip');
                el.setAttribute('data-bs-html', 'true');
                el.setAttribute('data-bs-placement', 'top');
                el.setAttribute('title', tooltipContent);
                
                // Iniciar tooltips
                new bootstrap.Tooltip(el);
            },
            eventTextColor: '#fff',
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                meridiem: true,
                hour12: true
            },
            // Permitir edición precisa de eventos
            slotDuration: '00:15:00',     // Divisiones de 15 minutos para mejor precisión
            snapDuration: '00:05:00',     // Ajustar a intervalos de 5 minutos al arrastrar
            slotMinTime: '06:00:00',      // Comenzar a las 6 AM
            slotMaxTime: '22:00:00',      // Terminar a las 10 PM
            slotLabelInterval: '01:00',   // Mostrar etiquetas cada hora
            slotEventOverlap: false,      // No permitir superposición visual de eventos
            
            
            // Personalización avanzada para mejor legibilidad
            slotLabelFormat: {
                hour: 'numeric',
                minute: '2-digit',
                omitZeroMinute: true,
                meridiem: 'short',
                hour12: true
            },
            
            // Personalizar el área de recursos
            resourceAreaHeaderContent: 'Técnicos',
            resourceLabelClassNames: 'resource-label',
            resourceTimelineDay: {
                slotDuration: '00:30:00',
                slotLabelFormat: [
                    { hour: 'numeric', minute: '2-digit', omitZeroMinute: true, meridiem: 'short' }
                ],
                slotLabelInterval: '01:00',
                slotMinWidth: 100, // Ancho mínimo para cada columna de hora
                resourceAreaWidth: '180px', // Ancho fijo para columna de recursos
                resourceLabelDidMount: function(info) {
                    // Añadir clases y estilos a las etiquetas de recursos (técnicos)
                    const resource = info.resource;
                    const el = info.el;
                    
                    // Añadir un icono y mejorar presentación
                    el.innerHTML = `
                        <div class="resource-label-content">
                            <i class="fas fa-user-hard-hat me-1"></i>
                            <span>${resource.title}</span>
                        </div>
                    `;
                    
                    // Añadir tooltip con información adicional
                    el.setAttribute('title', 'Técnico: ' + resource.title);
                    el.style.cursor = 'pointer';
                }
            },
            
            // Mejorar aspecto general
            dayMaxEvents: false,
            stickyHeaderDates: true,
            nowIndicator: true,
            eventMaxStack: 2,
            slotEventOverlap: false,
            
            // Configuración de tiempo
            slotDuration: isMobile ? '01:00:00' : '00:30:00',
            slotMinTime: '07:00:00',
            slotMaxTime: '19:00:00',
            snapDuration: '00:15:00',
            
            // Personalización de la vista
            resourceAreaWidth: isMobile ? '25%' : (isTablet ? '18%' : '15%'),
            height: 'auto', // Ajustarse automáticamente al contenido
            
            // Datos iniciales
            resources: resources,
            events: function(info, successCallback, failureCallback) {
                // Obtener valores de los filtros
                const technicianId = $('#technicianFilter').val();
                const status = $('#statusFilter').val();
                const confirmation = $('#confirmationFilter').val();
                
                const startDate = info.start.toISOString();
                const endDate = info.end.toISOString();
                
                // Construir URL con parámetros de filtrado
                let url = `/admin/api/schedules?start=${startDate}&end=${endDate}`;
                if (technicianId) url += `&technician_id=${technicianId}`;
                if (status) url += `&status=${status}`;
                if (confirmation) url += `&confirmation=${confirmation}`;
                
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        successCallback(data);
                    })
                    .catch(error => {
                        console.error('Error cargando eventos:', error);
                        failureCallback(error);
                    });
            },
            
            // Funcionalidades de interacción mejoradas
            editable: true,              // Permitir edición de eventos
            eventResourceEditable: true, // Permitir cambiar eventos entre recursos (técnicos)
            eventDurationEditable: true, // Permitir cambiar la duración de eventos
            nowIndicator: true,          // Mostrar indicador de hora actual
            navLinks: !isMobile,         // Enlaces de navegación (desactivar en móviles)
            selectable: true,            // Permitir seleccionar rangos de tiempo
            selectMirror: true,          // Mostrar "fantasma" al seleccionar
            selectMinDistance: 5,        // Distancia mínima para considerar una selección (evita clics accidentales)
            selectConstraint: {          // Restringir selección a horas de trabajo
                startTime: '06:00:00',
                endTime: '22:00:00',
            },
            allDaySlot: false,           // No mostrar slot para eventos de día completo
            scrollTimeReset: false,      // Mantener la posición de desplazamiento al cambiar de vista
            unselectAuto: false,         // No deseleccionar automáticamente
            longPressDelay: 200,         // Tiempo para activar selección en dispositivos táctiles (ms)
            
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
                // Crear estructura para el nombre del técnico
                const techContainer = document.createElement('div');
                techContainer.style.width = '100%';
                techContainer.style.display = 'flex';
                techContainer.style.flexDirection = 'column';
                techContainer.style.alignItems = 'center';
                techContainer.style.justifyContent = 'center';
                techContainer.style.padding = '5px';
                techContainer.style.height = '100%';
                
                // Nombre del técnico con formato mejorado y muy visible
                const techName = document.createElement('div');
                techName.innerHTML = info.resource.title;
                techName.style.fontSize = '14px';
                techName.style.fontWeight = 'bold';
                techName.style.lineHeight = '1.2';
                techName.style.color = '#004122';
                techName.style.textAlign = 'center';
                techName.style.width = '100%';
                techName.style.padding = '2px';
                techName.style.wordBreak = 'break-word';
                
                // Añadir especialidad si está disponible
                if (info.resource.extendedProps && info.resource.extendedProps.specialty) {
                    const techSpecialty = document.createElement('div');
                    techSpecialty.innerHTML = info.resource.extendedProps.specialty;
                    techSpecialty.style.fontSize = '12px';
                    techSpecialty.style.color = '#666';
                    techSpecialty.style.marginTop = '3px';
                    techContainer.appendChild(techName);
                    techContainer.appendChild(techSpecialty);
                } else {
                    techContainer.appendChild(techName);
                }
                
                // Reemplazar contenido existente
                const cellMain = info.el.querySelector('.fc-datagrid-cell-main');
                cellMain.innerHTML = '';
                cellMain.style.width = '100%';
                cellMain.style.height = '100%';
                cellMain.appendChild(techContainer);
                
                // Añadir borde muy visible
                info.el.style.border = '2px solid #004122';
                info.el.style.borderLeft = '4px solid #87c947';
                
                // Forzar ancho mínimo en celda
                info.el.style.minWidth = '150px';
                info.el.style.width = '150px';
            },
            
            // Permitir seleccionar un rango para crear un nuevo agendamiento con mejor precisión horaria
            select: function(info) {
                // Mostrar un modal para crear un agendamiento
                const startDate = info.startStr;
                const endDate = info.endStr;
                const resourceId = info.resource ? info.resource.id : null;
                
                if (resourceId) {
                    // Obtener el nombre del técnico seleccionado
                    let technicianName = "Técnico";
                    // Para el modal de solicitudes existentes
                    const technicianSelect = document.getElementById('technician_id');
                    if (technicianSelect) {
                        technicianSelect.value = resourceId;
                        const selectedOption = technicianSelect.options[technicianSelect.selectedIndex];
                        technicianName = selectedOption.text;
                    }
                    
                    // Para el modal directo también
                    const directTechnicianSelect = document.getElementById('direct_technician_id');
                    if (directTechnicianSelect) {
                        directTechnicianSelect.value = resourceId;
                    }
                    
                    // Calcular y establecer el tiempo de finalización con una duración predeterminada
                    console.log('Datos de selección recibidos:', { startDate, endDate });
                    const start = new Date(startDate);
                    const end = new Date(endDate);
                    
                    // Mostrar información detallada sobre las fechas para depuración
                    console.log('Fecha inicio:', start.toLocaleString(), 'timestamp:', start.getTime());
                    console.log('Fecha fin:', end.toLocaleString(), 'timestamp:', end.getTime());
                    
                    // Calcular la duración correctamente según el rango
                    let durationInMinutes;
                    
                    // Detectar caso especial: selección de 8am a 10am
                    const startHour = start.getHours();
                    const endHour = end.getHours();
                    const startDay = start.getDate();
                    const endDay = end.getDate();
                    
                    if (startHour === 8 && startDay === endDay && (endHour === 10 || end.getTime() - start.getTime() >= 7200000)) {
                        // Caso especial: selección de 8am a 10am o equivalente (2 horas o más)
                        durationInMinutes = 120;
                        console.log('Detección especial: Selección de 2 horas (8am-10am) detectada');
                        
                        // Forzar la fecha de fin a exactamente 2 horas después
                        end.setTime(start.getTime() + 7200000);
                    } 
                    else if (end.getTime() > start.getTime()) {
                        // Calcular duración normal basada en tiempo seleccionado
                        durationInMinutes = Math.round((end.getTime() - start.getTime()) / (1000 * 60));
                        console.log(`Duración calculada: ${durationInMinutes} minutos entre ${start.toLocaleTimeString()} y ${end.toLocaleTimeString()}`);
                    } 
                    else {
                        // Si la fecha de fin es inválida o es anterior a la fecha de inicio
                        if (startHour === 8) {
                            // Si empieza a las 8am, asumir 2 horas por defecto
                            durationInMinutes = 120;
                        } else {
                            // Para otras horas usar 60 minutos
                            durationInMinutes = 60;
                        }
                        console.log(`Usando duración predeterminada: ${durationInMinutes} minutos para hora ${startHour}`);
                    }
                    
                    // Formatear horas para mostrar
                    const formatOptions = { hour: '2-digit', minute: '2-digit', hour12: true };
                    const startTimeFormatted = start.toLocaleTimeString('es-ES', formatOptions);
                    const endTimeFormatted = new Date(start.getTime() + durationInMinutes * 60 * 1000)
                        .toLocaleTimeString('es-ES', formatOptions);
                    
                    // Preparar datos para pasar a los modales
                    const appointmentData = {
                        technicianId: resourceId,
                        technicianName: technicianName,
                        date: start.toISOString().split('T')[0], // YYYY-MM-DD
                        startTime: start.toTimeString().slice(0, 5), // HH:MM
                        endTime: new Date(start.getTime() + durationInMinutes * 60 * 1000).toTimeString().slice(0, 5), // HH:MM
                        duration: Math.round(durationInMinutes)
                    };
                    
                    console.log('Datos preparados para el agendamiento:', appointmentData);
                    
                    // Mostrar un cuadro de selección para elegir el tipo de agendamiento
                    Swal.fire({
                        title: 'Crear Agendamiento',
                        html: `
                            <div class="mb-4 text-center">
                                <div class="badge bg-success mb-2 p-2">
                                    <i class="fas fa-user-check me-1"></i> ${technicianName}
                                </div>
                                <div class="d-block">
                                    <span class="badge bg-primary">
                                        <i class="far fa-clock me-1"></i> ${startTimeFormatted} - ${endTimeFormatted}
                                    </span>
                                </div>
                            </div>
                            <div class="scheduling-type-selection">
                                <h5 class="mb-3 text-center">¿Qué tipo de agendamiento desea crear?</h5>
                                <div class="btn-group d-flex w-100" role="group">
                                    <button type="button" class="btn btn-outline-primary flex-fill" id="btnExistingRequest">
                                        <i class="fas fa-clipboard-list"></i>
                                        <span class="d-block my-2">Usar Solicitud Existente</span>
                                        <small class="d-block text-muted">Seleccionar una solicitud pendiente</small>
                                    </button>
                                    <button type="button" class="btn btn-outline-success flex-fill" id="btnNewDirect">
                                        <i class="fas fa-plus-circle"></i>
                                        <span class="d-block my-2">Crear Servicio Nuevo</span>
                                        <small class="d-block text-muted">Seleccionar un servicio de la empresa</small>
                                    </button>
                                </div>
                            </div>
                        `,
                        showConfirmButton: false,
                        showCloseButton: true,
                        width: '550px',
                        didOpen: () => {
                            console.log('Diálogo de selección abierto');
                            // Guardar datos en el localStorage para recuperarlos si el diálogo se cierra accidentalmente
                            localStorage.setItem('lastAppointmentData', JSON.stringify(appointmentData));
                        }
                    });
                    
                    // Manejar clic en "Usar Solicitud Existente"
                    document.getElementById('btnExistingRequest').addEventListener('click', function(e) {
                        // Evitar que el evento se propague a otros elementos
                        e.preventDefault();
                        e.stopPropagation();
                        
                        // Cerrar el SweetAlert
                        Swal.close();
                        
                        // Configurar el modal con solicitudes existentes
                        document.getElementById('newScheduleModalLabel').innerHTML = `<span class="me-2" style="font-size: 2rem;"><i class="fas fa-calendar-plus"></i></span> Nuevo Agendamiento para ${technicianName}`;
                        
                        // Limpiar event listeners previos para evitar duplicidad
                        const dateInput = document.getElementById('scheduled_date');
                        const endTimeInput = document.getElementById('end_time');
                        const durationInput = document.getElementById('duration');
                        
                        if (dateInput && endTimeInput && durationInput) {
                            // Clonar y reemplazar elementos para eliminar listeners antiguos
                            const newDateInput = dateInput.cloneNode(true);
                            dateInput.parentNode.replaceChild(newDateInput, dateInput);
                            
                            const newEndTimeInput = endTimeInput.cloneNode(true);
                            endTimeInput.parentNode.replaceChild(newEndTimeInput, endTimeInput);
                            
                            const newDurationInput = durationInput.cloneNode(true);
                            durationInput.parentNode.replaceChild(newDurationInput, durationInput);
                            
                            // Usar referencias actualizadas
                            const updatedDateInput = document.getElementById('scheduled_date');
                            const updatedEndTimeInput = document.getElementById('end_time');
                            const updatedDurationInput = document.getElementById('duration');
                            
                            // Formatear la fecha para el input datetime-local
                            updatedDateInput.value = startDate.slice(0, 16); // Formato YYYY-MM-DDTHH:MM
                            
                            // Calcular y establecer la hora de finalización
                            const endDateTime = new Date(start.getTime() + durationInMinutes * 60 * 1000);
                            const hours = endDateTime.getHours().toString().padStart(2, '0');
                            const minutes = endDateTime.getMinutes().toString().padStart(2, '0');
                            updatedEndTimeInput.value = `${hours}:${minutes}`;
                            
                            // Establecer duración
                            updatedDurationInput.value = Math.round(durationInMinutes);
                            
                            // Mostrar información sobre el horario seleccionado
                            const selectedTimeInfo = document.getElementById('selected-time-info');
                            const selectedTimeText = document.getElementById('selected-time-text');
                            if (selectedTimeInfo && selectedTimeText) {
                                selectedTimeText.textContent = `${startTimeFormatted} - ${endTimeFormatted} (${Math.round(durationInMinutes)} minutos)`;
                                selectedTimeInfo.classList.remove('d-none');
                            }
                            
                            // Función para actualizar la información de tiempo
                            function updateTimeInfoBox(start, end, duration) {
                                if (selectedTimeText) {
                                    const formattedStart = start.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                                    const formattedEnd = end.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                                    selectedTimeText.textContent = `${formattedStart} - ${formattedEnd} (${duration} minutos)`;
                                    selectedTimeInfo.classList.remove('d-none');
                                }
                            }
                            
                            // Establecer listeners interactivos para mantener sincronizados los campos
                            
                            // 1. Cambio en duración -> actualiza hora fin
                            updatedDurationInput.addEventListener('input', function() {
                                const startDateTime = new Date(updatedDateInput.value);
                                const duration = parseInt(this.value) || 60;
                                const endDateTime = new Date(startDateTime.getTime() + duration * 60000);
                                
                                // Actualizar campo de hora fin
                                const endHours = endDateTime.getHours().toString().padStart(2, '0');
                                const endMinutes = endDateTime.getMinutes().toString().padStart(2, '0');
                                updatedEndTimeInput.value = `${endHours}:${endMinutes}`;
                                
                                // Actualizar infobox
                                updateTimeInfoBox(startDateTime, endDateTime, duration);
                                
                                // Efecto visual de actualización
                                updatedEndTimeInput.classList.add('field-highlight');
                                setTimeout(() => updatedEndTimeInput.classList.remove('field-highlight'), 1000);
                            });
                            
                            // 2. Cambio en hora fin -> calcula duración
                            updatedEndTimeInput.addEventListener('input', function() {
                                const startDateTime = new Date(updatedDateInput.value);
                                const endTimeArr = this.value.split(':');
                                const endHours = parseInt(endTimeArr[0]);
                                const endMinutes = parseInt(endTimeArr[1]);
                                
                                if (!isNaN(endHours) && !isNaN(endMinutes)) {
                                    const endDateTime = new Date(startDateTime);
                                    endDateTime.setHours(endHours, endMinutes);
                                    
                                    // Si la hora fin es anterior a inicio, asumimos día siguiente
                                    if (endDateTime < startDateTime) {
                                        endDateTime.setDate(endDateTime.getDate() + 1);
                                    }
                                    
                                    // Calcular duración
                                    const durationMinutes = Math.round((endDateTime - startDateTime) / 60000);
                                    if (durationMinutes > 0) {
                                        updatedDurationInput.value = durationMinutes;
                                        
                                        // Actualizar infobox
                                        updateTimeInfoBox(startDateTime, endDateTime, durationMinutes);
                                        
                                        // Efecto visual
                                        updatedDurationInput.classList.add('field-highlight');
                                        setTimeout(() => updatedDurationInput.classList.remove('field-highlight'), 1000);
                                    }
                                }
                            });
                            
                            // 3. Cambio en fecha/hora inicio -> recalcula hora fin
                            updatedDateInput.addEventListener('input', function() {
                                const startDateTime = new Date(this.value);
                                const duration = parseInt(updatedDurationInput.value) || 60;
                                const endDateTime = new Date(startDateTime.getTime() + duration * 60000);
                                
                                // Actualizar hora fin
                                const endHours = endDateTime.getHours().toString().padStart(2, '0');
                                const endMinutes = endDateTime.getMinutes().toString().padStart(2, '0');
                                updatedEndTimeInput.value = `${endHours}:${endMinutes}`;
                                
                                // Actualizar infobox
                                updateTimeInfoBox(startDateTime, endDateTime, duration);
                                
                                // Efecto visual
                                updatedEndTimeInput.classList.add('field-highlight');
                                setTimeout(() => updatedEndTimeInput.classList.remove('field-highlight'), 1000);
                            });
                        }
                        
                        console.log('Abriendo modal de agendamiento nuevo');
                        
                        // Mostrar el modal
                        const modal = new bootstrap.Modal(document.getElementById('newScheduleModal'));
                        modal.show();
                    });
                    
                    // Manejar clic en "Crear Servicio Nuevo" - IMPLEMENTACIÓN REFORZADA
                    document.getElementById('btnNewDirect').addEventListener('click', function(e) {
                        // Evitar que el evento se propague a otros elementos
                        e.preventDefault();
                        e.stopPropagation();
                        
                        // Formatear la fecha y hora para los campos del formulario
                        const formattedDate = start.toISOString().split('T')[0]; // YYYY-MM-DD
                        const startTimeFormatted = start.toTimeString().slice(0, 5); // HH:MM
                        const endDateTime = new Date(start.getTime() + durationInMinutes * 60 * 1000);
                        const endTimeFormatted = endDateTime.toTimeString().slice(0, 5); // HH:MM
                        
                        // Cerrar el diálogo de SweetAlert
                        Swal.close();
                        
                        console.log('Procesando selección de tiempo:', { startTime: start, endTime: endDateTime, duration: durationInMinutes });
                        
                        // Configurar el modal de agendamiento
                        document.getElementById('newScheduleModalLabel').innerHTML = `<span class="me-2" style="font-size: 2rem;"><i class="fas fa-calendar-plus"></i></span> Nuevo Agendamiento para ${technicianName}`;
                        
                        // Limpiar event listeners previos para evitar duplicidad
                        const dateInput = document.getElementById('scheduled_date');
                        const endTimeInput = document.getElementById('end_time');
                        const durationInput = document.getElementById('duration');
                        
                        if (dateInput && endTimeInput && durationInput) {
                            // Clonar y reemplazar elementos para eliminar listeners antiguos
                            const newDateInput = dateInput.cloneNode(true);
                            dateInput.parentNode.replaceChild(newDateInput, dateInput);
                            
                            const newEndTimeInput = endTimeInput.cloneNode(true);
                            endTimeInput.parentNode.replaceChild(newEndTimeInput, endTimeInput);
                            
                            const newDurationInput = durationInput.cloneNode(true);
                            durationInput.parentNode.replaceChild(newDurationInput, durationInput);
                            
                            // Usar referencias actualizadas
                            const updatedDateInput = document.getElementById('scheduled_date');
                            const updatedEndTimeInput = document.getElementById('end_time');
                            const updatedDurationInput = document.getElementById('duration');
                            
                            // Establecer valores en los campos
                            updatedDateInput.value = `${formattedDate}T${startTimeFormatted}`;
                            console.log('Fecha de inicio establecida:', updatedDateInput.value);
                            
                            const endHours = endDateTime.getHours().toString().padStart(2, '0');
                            const endMinutes = endDateTime.getMinutes().toString().padStart(2, '0');
                            updatedEndTimeInput.value = `${endHours}:${endMinutes}`;
                            console.log('Hora de finalización establecida:', updatedEndTimeInput.value);
                            
                            updatedDurationInput.value = Math.round(durationInMinutes);
                            console.log('Duración establecida:', updatedDurationInput.value, 'minutos');
                            
                            // Establecer técnico seleccionado
                            const technicianSelect = document.getElementById('technician_id');
                            if (technicianSelect) {
                                technicianSelect.value = resourceId;
                            }
                            
                            // Mostrar información sobre el horario seleccionado
                            const selectedTimeInfo = document.getElementById('selected-time-info');
                            const selectedTimeText = document.getElementById('selected-time-text');
                            if (selectedTimeInfo && selectedTimeText) {
                                const startFormatted = start.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                                const endFormatted = endDateTime.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                                
                                // Mostrar duración y horas formateadas
                                const durationText = `${startFormatted} - ${endFormatted} (${Math.round(durationInMinutes)} minutos)`;
                                selectedTimeText.textContent = durationText;
                                selectedTimeInfo.classList.remove('d-none');
                                console.log('Información de tiempo mostrada:', durationText);
                            }
                            
                            // Función para actualizar la información de tiempo
                            function updateTimeInfoBox(start, end, duration) {
                                if (selectedTimeText) {
                                    const formattedStart = start.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                                    const formattedEnd = end.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                                    selectedTimeText.textContent = `${formattedStart} - ${formattedEnd} (${duration} minutos)`;
                                    selectedTimeInfo.classList.remove('d-none');
                                }
                            }
                            
                            // Establecer listeners interactivos para mantener sincronizados los campos
                            
                            // 1. Cambio en duración -> actualiza hora fin
                            updatedDurationInput.addEventListener('input', function() {
                                const startDateTime = new Date(updatedDateInput.value);
                                const duration = parseInt(this.value) || 60;
                                const endDateTime = new Date(startDateTime.getTime() + duration * 60000);
                                
                                // Actualizar campo de hora fin
                                const endHours = endDateTime.getHours().toString().padStart(2, '0');
                                const endMinutes = endDateTime.getMinutes().toString().padStart(2, '0');
                                updatedEndTimeInput.value = `${endHours}:${endMinutes}`;
                                
                                // Actualizar infobox
                                updateTimeInfoBox(startDateTime, endDateTime, duration);
                                
                                // Efecto visual de actualización
                                updatedEndTimeInput.classList.add('field-highlight');
                                setTimeout(() => updatedEndTimeInput.classList.remove('field-highlight'), 1000);
                            });
                            
                            // 2. Cambio en hora fin -> calcula duración
                            updatedEndTimeInput.addEventListener('input', function() {
                                const startDateTime = new Date(updatedDateInput.value);
                                const endTimeArr = this.value.split(':');
                                const endHours = parseInt(endTimeArr[0]);
                                const endMinutes = parseInt(endTimeArr[1]);
                                
                                if (!isNaN(endHours) && !isNaN(endMinutes)) {
                                    const endDateTime = new Date(startDateTime);
                                    endDateTime.setHours(endHours, endMinutes);
                                    
                                    // Si la hora fin es anterior a inicio, asumimos día siguiente
                                    if (endDateTime < startDateTime) {
                                        endDateTime.setDate(endDateTime.getDate() + 1);
                                    }
                                    
                                    // Calcular duración
                                    const durationMinutes = Math.round((endDateTime - startDateTime) / 60000);
                                    if (durationMinutes > 0) {
                                        updatedDurationInput.value = durationMinutes;
                                        
                                        // Actualizar infobox
                                        updateTimeInfoBox(startDateTime, endDateTime, durationMinutes);
                                        
                                        // Efecto visual
                                        updatedDurationInput.classList.add('field-highlight');
                                        setTimeout(() => updatedDurationInput.classList.remove('field-highlight'), 1000);
                                    }
                                }
                            });
                            
                            // 3. Cambio en fecha/hora inicio -> recalcula hora fin
                            updatedDateInput.addEventListener('input', function() {
                                const startDateTime = new Date(this.value);
                                const duration = parseInt(updatedDurationInput.value) || 60;
                                const endDateTime = new Date(startDateTime.getTime() + duration * 60000);
                                
                                // Actualizar hora fin
                                const endHours = endDateTime.getHours().toString().padStart(2, '0');
                                const endMinutes = endDateTime.getMinutes().toString().padStart(2, '0');
                                updatedEndTimeInput.value = `${endHours}:${endMinutes}`;
                                
                                // Actualizar infobox
                                updateTimeInfoBox(startDateTime, endDateTime, duration);
                                
                                // Efecto visual
                                updatedEndTimeInput.classList.add('field-highlight');
                                setTimeout(() => updatedEndTimeInput.classList.remove('field-highlight'), 1000);
                            });
                        }
                        
                        // Mostrar el modal de agendamiento
                        const scheduleModal = new bootstrap.Modal(document.getElementById('newScheduleModal'));
                        scheduleModal.show();
                        
                        console.log('▶️ Abriendo modal de agendamiento con datos precompletados');
                        console.log('👨‍🔧 Técnico:', technicianName, '(ID:', resourceId, ')');
                        console.log('📅 Fecha:', formattedDate);
                        console.log('🕒 Horario:', startTimeFormatted, '-', endTimeFormatted);
                        
                        // Construir objeto de datos para referencia (aunque ya no lo necesitamos)
                        const modalData = {
                            technicianId: resourceId,
                            technicianName: technicianName,
                            date: formattedDate,
                            startTime: startTimeFormatted,
                            endTime: endTimeFormatted,
                            duration: Math.round(durationInMinutes)
                        };
                        
                        // Método 1: Usar botón físico (el más confiable)
                        const btnOpenModal = document.getElementById('btnOpenDirectModal');
                        if (btnOpenModal) {
                            console.log('Método 1: Usando botón físico para abrir modal directo');
                            
                            // Primero configurar los campos del formulario
                            // Establecer un contenedor de datos para transmitir información entre componentes
                            const dataContainer = document.createElement('div');
                            dataContainer.id = 'temp-modal-data';
                            dataContainer.style.display = 'none';
                            dataContainer.dataset.technicianId = resourceId;
                            dataContainer.dataset.technicianName = technicianName;
                            dataContainer.dataset.date = formattedDate;
                            dataContainer.dataset.startTime = startTimeFormatted;
                            dataContainer.dataset.endTime = endTimeFormatted;
                            dataContainer.dataset.duration = Math.round(durationInMinutes);
                            document.body.appendChild(dataContainer);
                            
                            // Disparar evento personalizado antes de hacer clic en el botón
                            document.dispatchEvent(new CustomEvent('prepareDirectModal', { 
                                detail: modalData
                            }));
                            
                            // Hacer clic en el botón
                            setTimeout(() => {
                                btnOpenModal.click();
                                
                                // Verificar después de un tiempo si el modal está abierto
                                setTimeout(() => {
                                    const modalDirecto = document.getElementById('newDirectScheduleModal');
                                    if (modalDirecto && !modalDirecto.classList.contains('show')) {
                                        console.warn('El modal no se abrió, probando método alternativo');
                                        intentarMetodoAlternativo();
                                    } else {
                                        console.log('Modal directo abierto correctamente');
                                        
                                        // Configurar campos del formulario
                                        setTimeout(configurarCamposFormulario, 300);
                                    }
                                }, 500);
                            }, 100);
                            
                            return;
                        }
                        
                        // Método 2: Usar función global
                        function intentarMetodoAlternativo() {
                            if (window.abrirModalDirecto) {
                                console.log('Método 2: Usando función global abrirModalDirecto');
                                const resultado = window.abrirModalDirecto(modalData);
                                
                                if (resultado) {
                                    console.log('Modal directo abierto con función global');
                                    setTimeout(configurarCamposFormulario, 300);
                                    return;
                                }
                            }
                            
                            // Método 3: Bootstrap nativo
                            console.log('Método 3: Usando Bootstrap nativo');
                            const modalElement = document.getElementById('newDirectScheduleModal');
                            if (modalElement) {
                                try {
                                    const directModal = new bootstrap.Modal(modalElement);
                                    directModal.show();
                                    setTimeout(configurarCamposFormulario, 300);
                                } catch (err) {
                                    console.error('Error al abrir modal:', err);
                                    
                                    // Método 4: Forzar jQuery
                                    console.log('Método 4: Forzando jQuery');
                                    try {
                                        if (window.jQuery) {
                                            jQuery('#newDirectScheduleModal').modal('show');
                                            setTimeout(configurarCamposFormulario, 300);
                                        } else {
                                            falloAperturaModal();
                                        }
                                    } catch (e) {
                                        falloAperturaModal();
                                    }
                                }
                            } else {
                                falloAperturaModal();
                            }
                        }
                        
                        function configurarCamposFormulario() {
                            console.log('Configurando campos del formulario directo');
                            try {
                                // Establecer título del modal
                                document.getElementById('newDirectScheduleModalLabel').innerHTML = 
                                    `<i class="fas fa-plus-circle me-1"></i> Crear Servicio Nuevo - ${technicianName}`;
                                
                                // Establecer campos del formulario
                                document.getElementById('direct_scheduled_date').value = formattedDate;
                                document.getElementById('direct_start_time').value = startTimeFormatted;
                                document.getElementById('direct_end_time').value = endTimeFormatted;
                                document.getElementById('direct_technician_id').value = resourceId;
                                document.getElementById('direct_duration').value = Math.round(durationInMinutes);
                                
                                // Enfocar el selector de servicios
                                const serviceSelect = document.getElementById('direct_service_id');
                                if (serviceSelect) {
                                    serviceSelect.focus();
                                    serviceSelect.classList.add('border-highlight');
                                }
                            } catch (err) {
                                console.warn('Error al configurar campos:', err);
                            }
                        }
                        
                        function falloAperturaModal() {
                            console.error('⚠️ FALLO CRÍTICO: No se pudo abrir el modal directo');
                            Swal.fire({
                                icon: 'error',
                                title: 'Error al abrir formulario',
                                html: `
                                    <p>No se pudo abrir el formulario para crear un servicio nuevo.</p>
                                    <p>Por favor intente nuevamente o recargue la página.</p>
                                `,
                                confirmButtonText: 'Entendido',
                                confirmButtonColor: '#87c947'
                            });
                        }
                        
                        // Iniciar con método alternativo si no hay botón físico
                        intentarMetodoAlternativo();
                    });
                }
            },
            eventClassNames: function(arg) {
                // Clases base según el estado del agendamiento
                let classes = [];
                
                // Estado del servicio
                if (arg.event.extendedProps.status === 'pendiente') {
                    classes.push('schedule-pending');
                } else if (arg.event.extendedProps.status === 'en proceso') {
                    classes.push('schedule-inprogress');
                } else if (arg.event.extendedProps.status === 'completado') {
                    classes.push('schedule-completed');
                } else if (arg.event.extendedProps.status === 'cancelado') {
                    classes.push('schedule-cancelled');
                }
                
                // Añadir clase para el estado de confirmación
                if (arg.event.extendedProps.confirmation_status) {
                    if (arg.event.extendedProps.confirmation_status === 'confirmed') {
                        classes.push('confirmation-confirmed');
                    } else if (arg.event.extendedProps.confirmation_status === 'declined') {
                        classes.push('confirmation-declined');
                    } else {
                        classes.push('confirmation-pending');
                    }
                } else {
                    classes.push('confirmation-pending');
                }
                
                return classes;
            },
            eventClick: function(info) {
                // Mostrar detalles del agendamiento al hacer clic
                const scheduleId = info.event.id;
                showScheduleDetails(scheduleId);
            },
            eventDrop: function(info) {
                // Obtener datos relevantes del evento
                const scheduleId = info.event.id;
                const resourceId = info.event.getResources()[0].id;
                const startTime = info.event.start.toISOString();
                const endTime = info.event.end ? info.event.end.toISOString() : null;
                
                // Obtener información del técnico antiguo y nuevo para comparar
                const oldTechnicianId = info.oldResource ? info.oldResource.id : null;
                const newTechnicianId = resourceId;
                const technicianChanged = oldTechnicianId !== newTechnicianId;
                
                const oldTechnicianName = info.oldResource ? info.oldResource.title : 'Sin técnico';
                const newTechnicianName = info.event.getResources()[0].title;
                
                // Obtener fechas y horas para comparar
                const oldStartDate = info.oldEvent.start ? new Date(info.oldEvent.start) : new Date();
                const oldEndDate = info.oldEvent.end ? new Date(info.oldEvent.end) : new Date(oldStartDate.getTime() + 3600000);
                
                const newStartDate = new Date(startTime);
                const newEndDate = info.event.end ? new Date(info.event.end) : new Date(newStartDate.getTime() + 3600000);
                
                // Calcular duración para mostrar
                const durationMs = newEndDate.getTime() - newStartDate.getTime();
                const durationHours = Math.floor(durationMs / (1000 * 60 * 60));
                const durationMinutes = Math.floor((durationMs % (1000 * 60 * 60)) / (1000 * 60));
                
                // Formatear opciones para mostrar fechas y horas
                const formatOptions = { hour: '2-digit', minute: '2-digit', hour12: true };
                const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                
                // Formatear fechas y horas
                const oldStartFormatted = oldStartDate.toLocaleTimeString('es-ES', formatOptions);
                const oldEndFormatted = oldEndDate.toLocaleTimeString('es-ES', formatOptions);
                const oldDateFormatted = oldStartDate.toLocaleDateString('es-ES', dateOptions);
                
                const newStartFormatted = newStartDate.toLocaleTimeString('es-ES', formatOptions);
                const newEndFormatted = newEndDate.toLocaleTimeString('es-ES', formatOptions);
                const newDateFormatted = newStartDate.toLocaleDateString('es-ES', dateOptions);
                
                // Determinar si cambió la fecha
                const dateChanged = oldDateFormatted !== newDateFormatted;
                
                // Determinar si cambió la hora
                const timeChanged = oldStartFormatted !== newStartFormatted || oldEndFormatted !== newEndFormatted;
                
                // Preparar mensaje de confirmación con detalles de los cambios
                let confirmTitle, confirmHTML;
                
                if (technicianChanged) {
                    confirmTitle = 'Cambio de Técnico y/o Horario';
                    confirmHTML = `
                        <div class="change-container">
                            <div class="change-summary p-3 mb-3 rounded">
                                <div class="mb-3">
                                    <div class="change-label"><i class="fas fa-user-alt"></i> Técnico:</div>
                                    <div class="change-item-container">
                                        <div class="change-old">${oldTechnicianName}</div>
                                        <div class="change-arrow"><i class="fas fa-long-arrow-alt-right"></i></div>
                                        <div class="change-new">${newTechnicianName}</div>
                                    </div>
                                </div>
                                
                                ${dateChanged ? `
                                <div class="mb-3">
                                    <div class="change-label"><i class="fas fa-calendar-day"></i> Fecha:</div>
                                    <div class="change-item-container">
                                        <div class="change-old">${oldDateFormatted}</div>
                                        <div class="change-arrow"><i class="fas fa-long-arrow-alt-right"></i></div>
                                        <div class="change-new">${newDateFormatted}</div>
                                    </div>
                                </div>
                                ` : ''}
                                
                                ${timeChanged ? `
                                <div>
                                    <div class="change-label"><i class="fas fa-clock"></i> Horario:</div>
                                    <div class="change-item-container">
                                        <div class="change-old">${oldStartFormatted} - ${oldEndFormatted}</div>
                                        <div class="change-arrow"><i class="fas fa-long-arrow-alt-right"></i></div>
                                        <div class="change-new">${newStartFormatted} - ${newEndFormatted}</div>
                                    </div>
                                </div>
                                ` : ''}
                            </div>
                            
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> 
                                Al cambiar de técnico, verifique que el nuevo técnico tenga las habilidades necesarias
                                para el servicio requerido.
                            </div>
                        </div>
                    `;
                } else if (dateChanged || timeChanged) {
                    confirmTitle = 'Cambio de Horario';
                    confirmHTML = `
                        <div class="change-container">
                            <div class="mb-3">
                                <div class="tech-info p-2 rounded">
                                    <i class="fas fa-user-alt"></i> <strong>Técnico:</strong> ${newTechnicianName}
                                </div>
                            </div>
                            
                            ${dateChanged ? `
                            <div class="change-summary p-3 mb-3 rounded">
                                <div class="change-label"><i class="fas fa-calendar-day"></i> Fecha:</div>
                                <div class="change-item-container">
                                    <div class="change-old">${oldDateFormatted}</div>
                                    <div class="change-arrow"><i class="fas fa-long-arrow-alt-right"></i></div>
                                    <div class="change-new">${newDateFormatted}</div>
                                </div>
                            </div>
                            ` : ''}
                            
                            <div class="change-summary p-3 mb-3 rounded">
                                <div class="change-label"><i class="fas fa-clock"></i> Horario:</div>
                                <div class="change-item-container">
                                    <div class="change-old">${oldStartFormatted} - ${oldEndFormatted}</div>
                                    <div class="change-arrow"><i class="fas fa-long-arrow-alt-right"></i></div>
                                    <div class="change-new">${newStartFormatted} - ${newEndFormatted}</div>
                                </div>
                            </div>
                            
                            <div class="duration-info text-center p-2 rounded">
                                <i class="fas fa-hourglass-half"></i> <strong>Duración:</strong> 
                                ${durationHours}h ${durationMinutes}min
                            </div>
                        </div>
                    `;
                } else {
                    // No hay cambios significativos
                    confirmTitle = 'Confirmar Agendamiento';
                    confirmHTML = `
                        <div class="text-start">
                            <p><strong><i class="fas fa-user-alt"></i> Técnico:</strong> ${newTechnicianName}</p>
                            <p><strong><i class="fas fa-calendar-day"></i> Fecha:</strong> ${newDateFormatted}</p>
                            <p><strong><i class="fas fa-clock"></i> Hora:</strong> ${newStartFormatted} - ${newEndFormatted}</p>
                            <p><strong><i class="fas fa-hourglass-half"></i> Duración:</strong> ${durationHours}h ${durationMinutes}min</p>
                        </div>
                    `;
                }
                
                // Agregar estilos para la confirmación
                const style = document.createElement('style');
                style.textContent = `
                    .change-container {
                        text-align: left;
                        padding: 5px;
                    }
                    .change-summary {
                        background-color: #f8f9fa;
                        border: 1px solid #e2e6ea;
                    }
                    .change-label {
                        font-weight: bold;
                        margin-bottom: 5px;
                    }
                    .change-item-container {
                        display: flex;
                        align-items: center;
                        flex-wrap: wrap;
                        gap: 8px;
                    }
                    .change-old {
                        color: #dc3545;
                        text-decoration: line-through;
                        background: rgba(220, 53, 69, 0.1);
                        padding: 3px 8px;
                        border-radius: 4px;
                    }
                    .change-arrow {
                        color: #6c757d;
                    }
                    .change-new {
                        color: #28a745;
                        font-weight: bold;
                        background: rgba(40, 167, 69, 0.1);
                        padding: 3px 8px;
                        border-radius: 4px;
                    }
                    .tech-info, .duration-info {
                        background-color: #e9f1fd;
                        border: 1px solid #c2d7f7;
                    }
                `;
                document.head.appendChild(style);
                
                // Pedir confirmación antes de actualizar
                Swal.fire({
                    title: confirmTitle,
                    html: confirmHTML,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-check"></i> Confirmar Cambio',
                    cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                    confirmButtonColor: '#87c947',
                    cancelButtonColor: '#6c757d',
                    focusConfirm: false,
                    width: technicianChanged ? '550px' : '500px',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Mostrar indicador de carga
                        Swal.fire({
                            title: 'Actualizando...',
                            text: technicianChanged ? 'Reasignando servicio' : 'Actualizando horario',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        // Llamar a función mejorada para actualizar con hora de finalización
                        updateScheduleWithEndTime(scheduleId, resourceId, startTime, endTime);
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
                
                // Calcular duración exacta para mostrar
                const start = new Date(startTime);
                const end = new Date(endTime);
                const durationMs = end.getTime() - start.getTime();
                const durationHours = Math.floor(durationMs / (1000 * 60 * 60));
                const durationMinutes = Math.floor((durationMs % (1000 * 60 * 60)) / (1000 * 60));
                
                // Formatear tiempo para mostrar
                const formatOptions = { hour: '2-digit', minute: '2-digit', hour12: true };
                const startTimeFormatted = start.toLocaleTimeString('es-ES', formatOptions);
                const endTimeFormatted = end.toLocaleTimeString('es-ES', formatOptions);
                
                // Pedir confirmación antes de actualizar
                Swal.fire({
                    title: '¿Confirmar nueva duración?',
                    html: `
                        <div class="text-start p-2">
                            <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded">
                                <div>
                                    <i class="fas fa-clock text-primary"></i> <strong>Hora inicio:</strong>
                                </div>
                                <div class="badge bg-primary">${startTimeFormatted}</div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded">
                                <div>
                                    <i class="fas fa-clock text-success"></i> <strong>Hora fin:</strong>
                                </div>
                                <div class="badge bg-success">${endTimeFormatted}</div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                <div>
                                    <i class="fas fa-hourglass-half text-info"></i> <strong>Nueva duración:</strong>
                                </div>
                                <div class="badge bg-info text-light">${durationHours}h ${durationMinutes}min</div>
                            </div>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-check"></i> Confirmar',
                    cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                    confirmButtonColor: '#87c947',
                    focusConfirm: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Mostrar indicador de carga
                        Swal.fire({
                            title: 'Actualizando...',
                            text: 'Guardando nueva duración',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        // Actualizar con la nueva duración
                        updateScheduleDuration(scheduleId, startTime, endTime);
                    } else {
                        info.revert();
                    }
                });
            },
            
            // Mostrar información detallada al pasar el cursor por eventos
            eventMouseEnter: function(info) {
                // Obtener detalles del evento
                const event = info.event;
                const title = event.title;
                const start = event.start;
                const end = event.end || new Date(start.getTime() + 60 * 60 * 1000);
                
                // Calcular duración
                const durationMs = end.getTime() - start.getTime();
                const durationHours = Math.floor(durationMs / (1000 * 60 * 60));
                const durationMinutes = Math.floor((durationMs % (1000 * 60 * 60)) / (1000 * 60));
                
                // Formatear horas
                const formatOptions = { hour: '2-digit', minute: '2-digit', hour12: true };
                const startTimeFormatted = start.toLocaleTimeString('es-ES', formatOptions);
                const endTimeFormatted = end.toLocaleTimeString('es-ES', formatOptions);
                
                // Obtener información adicional desde extendedProps
                const status = event.extendedProps.status || 'pendiente';
                const confirmation = event.extendedProps.confirmation_status || 'pending';
                const client = event.extendedProps.client_name || 'Cliente';
                const service = event.extendedProps.service_name || 'Servicio';
                
                // Crear tooltip con Bootstrap
                const tooltip = document.createElement('div');
                tooltip.classList.add('calendar-tooltip');
                tooltip.innerHTML = `
                    <div class="tooltip-header">
                        <strong>${service}</strong>
                    </div>
                    <div class="tooltip-body">
                        <div><i class="fas fa-user"></i> ${client}</div>
                        <div><i class="fas fa-clock"></i> ${startTimeFormatted} - ${endTimeFormatted}</div>
                        <div><i class="fas fa-hourglass-half"></i> ${durationHours}h ${durationMinutes}min</div>
                    </div>
                    <div class="tooltip-footer">
                        <span class="badge ${status === 'pendiente' ? 'bg-warning' : status === 'en proceso' ? 'bg-info' : status === 'completado' ? 'bg-success' : 'bg-danger'}">
                            ${status.charAt(0).toUpperCase() + status.slice(1)}
                        </span>
                        <span class="badge ${confirmation === 'confirmed' ? 'bg-success' : confirmation === 'pending' ? 'bg-warning' : 'bg-danger'}">
                            ${confirmation === 'confirmed' ? 'Confirmado' : confirmation === 'pending' ? 'Pendiente' : 'Rechazado'}
                        </span>
                    </div>
                `;
                
                // Aplicar estilos al tooltip
                Object.assign(tooltip.style, {
                    position: 'absolute',
                    top: `${info.jsEvent.pageY + 10}px`,
                    left: `${info.jsEvent.pageX + 10}px`,
                    backgroundColor: 'white',
                    border: '1px solid #ddd',
                    borderRadius: '4px',
                    padding: '8px',
                    zIndex: '9999',
                    boxShadow: '0 2px 8px rgba(0,0,0,0.15)',
                    minWidth: '200px',
                    maxWidth: '300px',
                    fontSize: '12px'
                });
                
                // Añadir estilos específicos para las secciones
                const style = document.createElement('style');
                style.textContent = `
                    .calendar-tooltip .tooltip-header {
                        font-weight: bold;
                        border-bottom: 1px solid #eee;
                        padding-bottom: 5px;
                        margin-bottom: 5px;
                    }
                    .calendar-tooltip .tooltip-body {
                        padding: 5px 0;
                    }
                    .calendar-tooltip .tooltip-body div {
                        margin-bottom: 3px;
                    }
                    .calendar-tooltip .tooltip-footer {
                        margin-top: 5px;
                        padding-top: 5px;
                        border-top: 1px solid #eee;
                        display: flex;
                        justify-content: space-between;
                    }
                    .calendar-tooltip i {
                        width: 14px;
                        text-align: center;
                        margin-right: 5px;
                        opacity: 0.7;
                    }
                `;
                
                document.head.appendChild(style);
                document.body.appendChild(tooltip);
                
                // Guardar referencia para eliminar el tooltip después
                info.el.tooltip = tooltip;
            },
            
            eventMouseLeave: function(info) {
                // Eliminar el tooltip cuando el cursor sale del evento
                if (info.el.tooltip) {
                    info.el.tooltip.remove();
                    delete info.el.tooltip;
                }
            }
        });
        
        calendar.render();
        
        // Actualizar título del calendario
        updateCalendarTitle(calendar);
        
        // Aplicar correcciones urgentes inmediatamente
        setTimeout(syncResourceAndTimelineHeights, 500);
        setTimeout(syncResourceAndTimelineHeights, 1000);
        setTimeout(syncResourceAndTimelineHeights, 1500);
        
        // Botones de navegación - conectamos los botones externos con el calendario
        document.getElementById('prev-btn').addEventListener('click', function() {
            calendar.prev();
            updateCalendarTitle(calendar);
        });
        
        document.getElementById('next-btn').addEventListener('click', function() {
            calendar.next();
            updateCalendarTitle(calendar);
        });
        
        // Actualizar título al inicio
        updateCalendarTitle(calendar);
        
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
        
        // Evento para enviar recordatorios
        $('#confirmSendReminders').click(function() {
            // Mostrar spinner
            $('#sendRemindersSpinner').removeClass('d-none');
            $(this).prop('disabled', true);
            
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
                $('#sendRemindersSpinner').addClass('d-none');
                $(this).prop('disabled', false);
                
                // Cerrar el modal
                $('#sendRemindersModal').modal('hide');
                
                // Mostrar mensaje de éxito
                alert(data.message);
            })
            .catch(error => {
                console.error('Error:', error);
                // Ocultar spinner
                $('#sendRemindersSpinner').addClass('d-none');
                $(this).prop('disabled', false);
                
                // Mostrar mensaje de error
                alert('Ocurrió un error al enviar los recordatorios.');
            });
        });
        
        // Función mejorada para actualizar el título del calendario
        function updateCalendarTitle(calendar) {
            const dateStr = calendar.view.currentStart;
            const date = new Date(dateStr);
            
            // Formato diferente según la vista
            let formattedDate = '';
            if (calendar.view.type === 'resourceTimelineDay' || calendar.view.type === 'timeGridDay') {
                // Para vista diaria
                const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
                formattedDate = date.toLocaleDateString('es-ES', options);
                // Capitalizar primera letra
                formattedDate = formattedDate.charAt(0).toUpperCase() + formattedDate.slice(1);
            } else if (calendar.view.type === 'timeGridWeek') {
                // Para vista semanal
                const endDate = new Date(calendar.view.currentEnd);
                endDate.setDate(endDate.getDate() - 1); // Ajustar porque la fecha final es exclusiva
                
                const startDay = date.getDate();
                const endDay = endDate.getDate();
                const monthName = date.toLocaleDateString('es-ES', { month: 'long' });
                const yearNum = date.getFullYear();
                
                formattedDate = `${startDay} al ${endDay} de ${monthName}, ${yearNum}`;
            } else if (calendar.view.type === 'dayGridMonth') {
                // Para vista mensual
                const options = { month: 'long', year: 'numeric' };
                formattedDate = date.toLocaleDateString('es-ES', options);
                // Capitalizar primera letra
                formattedDate = formattedDate.charAt(0).toUpperCase() + formattedDate.slice(1);
            }
            
            document.getElementById('calendar-title').textContent = formattedDate;
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
            if (isMobile) {
                calendar.changeView('timeGridDay');
                calendar.setOption('headerToolbar', {
                    left: 'today',
                    center: 'title',
                    right: 'prev,next'
                });
            } else if (!isMobile && calendar.view.type === 'timeGridDay') {
                calendar.changeView('timeGridWeek');
                calendar.setOption('headerToolbar', {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'timeGridDay,timeGridWeek,dayGridMonth,resourceTimelineDay'
                });
            }
            
            // Forzar refrescado del calendario
            calendar.updateSize();
            
            // Asegurar coherencia entre columnas y filas
            syncResourceAndTimelineHeights();
            
            // Refrescar eventos para asegurar que se muestran correctamente
            if (width < 500) {
                calendar.setOption('eventMaxStack', 1);
            } else if (width < 768) {
                calendar.setOption('eventMaxStack', 2);
            } else {
                calendar.setOption('eventMaxStack', 3);
            }
        });
        
        // Función para asegurar coherencia de alturas entre columnas de técnicos y celdas de tiempo
        function syncResourceAndTimelineHeights() {
            // Pequeño delay para asegurar que el DOM está actualizado
            setTimeout(() => {
                console.log("Aplicando correcciones urgentes...");
                
                // MEJORA 1: FORZAR NOMBRES DE TÉCNICOS VISIBLES
                const resourceCells = document.querySelectorAll('.fc-datagrid-cell');
                resourceCells.forEach(cell => {
                    cell.style.minWidth = '150px';
                    cell.style.width = '150px';
                    cell.style.borderRight = '2px solid #004122';
                    cell.style.borderBottom = '2px solid #004122';
                    cell.style.backgroundColor = '#f0f8ff';
                    
                    // Asegurarse que el contenido es visible
                    const cushion = cell.querySelector('.fc-datagrid-cell-cushion');
                    if (cushion) {
                        cushion.style.whiteSpace = 'normal';
                        cushion.style.overflow = 'visible';
                        cushion.style.fontSize = '14px';
                        cushion.style.fontWeight = 'bold';
                        cushion.style.wordBreak = 'break-word';
                        cushion.style.padding = '5px';
                    }
                });
                
                // MEJORA 2: LÍNEAS HORIZONTALES COMPLETAS
                const standardHeight = 68; // Altura estándar fija
                
                // Aplicar líneas horizontales a todas las filas
                const rows = document.querySelectorAll('.fc-timeline-slots tr');
                rows.forEach(row => {
                    row.style.borderTop = '2px solid #87c947';
                    row.style.display = 'table-row';
                    row.style.width = '100%';
                });
                
                // Hacer muy visibles las líneas horizontales
                const timeSlots = document.querySelectorAll('.fc-timeline-slot-lane');
                timeSlots.forEach(slot => {
                    slot.style.borderTop = '2px solid #87c947';
                    slot.style.height = `${standardHeight}px`;
                });
                
                // Marcar claramente las líneas de horas
                const timeLabels = document.querySelectorAll('.fc-timeline-slot-label');
                timeLabels.forEach(label => {
                    label.style.borderRight = '3px solid #004122';
                    label.style.backgroundColor = '#e0f0d9';
                });
                
                // Forzar que las tablas de timeline ocupen todo el ancho
                const tables = document.querySelectorAll('.fc-timeline-body table');
                tables.forEach(table => {
                    table.style.width = '100%';
                });
                
                // Centrar eventos verticalmente
                const events = document.querySelectorAll('.fc-timeline-event');
                events.forEach(event => {
                    event.style.height = '32px';
                    event.style.lineHeight = '32px';
                    event.style.top = '50%';
                    event.style.transform = 'translateY(-50%)';
                    event.style.zIndex = '1000';
                });
                
                // Mejorar apariencia de las cabeceras
                const headerHeight = parseInt(styles.getPropertyValue('--header-height') || '50px');
                const headers = document.querySelectorAll('.fc-col-header-cell, .fc-datagrid-header .fc-datagrid-cell');
                headers.forEach(header => {
                    header.style.height = `${headerHeight}px`;
                    header.style.minHeight = `${headerHeight}px`;
                    header.style.maxHeight = `${headerHeight}px`;
                });
            }, 150);
        }
        
        // Llamar a la función de sincronización después de que el calendario se renderiza
        calendar.on('datesSet', function() {
            syncResourceAndTimelineHeights();
        });
        
        // Asegurar sincronización también cuando se actualiza la vista
        calendar.on('viewDidMount', function() {
            setTimeout(syncResourceAndTimelineHeights, 150);
        });
        
        // Sincronizar cuando cambia el tamaño de ventana
        window.addEventListener('resize', function() {
            setTimeout(syncResourceAndTimelineHeights, 150);
        });
        
        // Función para mostrar detalles de un agendamiento
        function showScheduleDetails(scheduleId) {
            // Mostrar modal con indicador de carga mientras se obtienen los detalles
            const loadingContent = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando detalles del agendamiento...</p>
                </div>
            `;
            
            document.getElementById('scheduleDetailsContent').innerHTML = loadingContent;
            const scheduleDetailsModal = new bootstrap.Modal(document.getElementById('scheduleDetailsModal'));
            scheduleDetailsModal.show();
            
            fetch(`/admin/schedules/${scheduleId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const schedule = data.schedule;
                        
                        // Generar badges para estados
                        let statusBadge, confirmationBadge = '';
                        
                        // Estado del agendamiento
                        if (schedule.status === 'pendiente') {
                            statusBadge = '<span class="badge bg-warning"><i class="fas fa-clock me-1"></i>Pendiente</span>';
                        } else if (schedule.status === 'en proceso') {
                            statusBadge = '<span class="badge bg-info"><i class="fas fa-spinner me-1"></i>En proceso</span>';
                        } else if (schedule.status === 'completado') {
                            statusBadge = '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Completado</span>';
                        } else {
                            statusBadge = '<span class="badge bg-danger"><i class="fas fa-times me-1"></i>Cancelado</span>';
                        }
                        
                        // Estado de confirmación
                        if (schedule.confirmation_status === 'confirmed') {
                            confirmationBadge = '<span class="badge bg-success"><i class="fas fa-user-check me-1"></i>Confirmado</span>';
                        } else if (schedule.confirmation_status === 'pending') {
                            confirmationBadge = '<span class="badge bg-warning"><i class="fas fa-user-clock me-1"></i>Pendiente de confirmación</span>';
                        } else if (schedule.confirmation_status === 'rejected') {
                            confirmationBadge = '<span class="badge bg-danger"><i class="fas fa-user-times me-1"></i>Rechazado</span>';
                        }
                        
                        // Formato de fecha y hora
                        const scheduledDate = new Date(schedule.scheduled_date);
                        const endDate = schedule.end_date ? new Date(schedule.end_date) : new Date(scheduledDate.getTime() + 3600000);
                        
                        // Calcular duración
                        const durationMs = endDate.getTime() - scheduledDate.getTime();
                        const durationHours = Math.floor(durationMs / (1000 * 60 * 60));
                        const durationMinutes = Math.floor((durationMs % (1000 * 60 * 60)) / (1000 * 60));
                        
                        const formattedDate = scheduledDate.toLocaleDateString('es-ES', {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });
                        
                        const formattedStartTime = scheduledDate.toLocaleTimeString('es-ES', { 
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        });
                        
                        const formattedEndTime = endDate.toLocaleTimeString('es-ES', { 
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        });
                        
                        let content = `
                            <div class="card mb-3 shadow-sm">
                                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información del Servicio</h6>
                                    <div>
                                        ${statusBadge} ${confirmationBadge}
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-4 text-muted">
                                            <i class="fas fa-user me-2"></i>Cliente:
                                        </div>
                                        <div class="col-md-8 fw-bold">
                                            ${schedule.service_request.client_name}
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-4 text-muted">
                                            <i class="fas fa-tools me-2"></i>Servicio:
                                        </div>
                                        <div class="col-md-8">
                                            ${schedule.service_request.service.name}
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-4 text-muted">
                                            <i class="fas fa-phone me-2"></i>Contacto:
                                        </div>
                                        <div class="col-md-8">
                                            ${schedule.service_request.client_phone || 'No disponible'}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mb-3 shadow-sm">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Detalles del Agendamiento</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-4 text-muted">
                                            <i class="fas fa-user-hard-hat me-2"></i>Técnico:
                                        </div>
                                        <div class="col-md-8">
                                            ${schedule.technician.user.name}
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-4 text-muted">
                                            <i class="fas fa-calendar-day me-2"></i>Fecha:
                                        </div>
                                        <div class="col-md-8">
                                            ${formattedDate}
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-4 text-muted">
                                            <i class="fas fa-clock me-2"></i>Horario:
                                        </div>
                                        <div class="col-md-8">
                                            ${formattedStartTime} - ${formattedEndTime}
                                            <span class="ms-2 text-muted">
                                                (${durationHours}h ${durationMinutes}min)
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                            
                        if (schedule.notes) {
                            content += `
                            <div class="card mb-3 shadow-sm">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Notas</h6>
                                </div>
                                <div class="card-body">
                                    <div class="p-3 bg-light rounded">
                                        ${schedule.notes}
                                    </div>
                                </div>
                            </div>`;
                        }
                        
                        // Información de historial si está disponible
                        if (schedule.created_at || schedule.updated_at) {
                            const createdAt = schedule.created_at ? new Date(schedule.created_at).toLocaleString('es-ES') : 'Desconocido';
                            const updatedAt = schedule.updated_at ? new Date(schedule.updated_at).toLocaleString('es-ES') : 'Desconocido';
                            
                            content += `
                            <div class="card shadow-sm">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-history me-2"></i>Historial</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row small text-muted">
                                        <div class="col-md-6">
                                            <i class="fas fa-plus-circle me-1"></i>Creado: ${createdAt}
                                        </div>
                                        <div class="col-md-6">
                                            <i class="fas fa-edit me-1"></i>Última actualización: ${updatedAt}
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                        }
                        
                        document.getElementById('scheduleDetailsContent').innerHTML = content;
                        document.getElementById('editScheduleBtn').href = `/admin/schedules/${scheduleId}/edit`;
                        document.getElementById('editScheduleBtn').innerHTML = '<i class="fas fa-edit me-1"></i>Editar';
                        
                        // Agregar botón para enviar recordatorio si está disponible
                        if (schedule.status !== 'cancelado' && schedule.status !== 'completado') {
                            document.getElementById('additionalActions').innerHTML = `
                                <button class="btn btn-outline-info" onclick="sendReminderEmail(${scheduleId})">
                                    <i class="fas fa-envelope me-1"></i>Enviar Recordatorio
                                </button>
                            `;
                        } else {
                            document.getElementById('additionalActions').innerHTML = '';
                        }
                        
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
        
        // Función para enviar recordatorio por email
        function sendReminderEmail(scheduleId) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Mostrar indicador de carga
            Swal.fire({
                title: 'Enviando recordatorio...',
                text: 'Esto puede tomar unos momentos',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(`/admin/schedules/${scheduleId}/send-reminder`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Recordatorio Enviado',
                        text: 'Se ha enviado el recordatorio al cliente correctamente',
                        icon: 'success',
                        confirmButtonColor: '#87c947'
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'No se pudo enviar el recordatorio',
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Error al enviar el recordatorio',
                    icon: 'error'
                });
            });
        }
        
        // Función para actualizar horario y técnico con hora de finalización
        function updateScheduleWithEndTime(scheduleId, technicianId, startDate, endDate) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch(`/admin/schedules/${scheduleId}/update-complete`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    technician_id: technicianId,
                    start_date: startDate,
                    end_date: endDate
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
                        text: data.message || 'No se pudo actualizar el agendamiento',
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

<!-- Script para el nuevo calendario de técnicos -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar el calendario nuevo
        initTechnicianCalendar();
        
        // Botones de navegación
        document.getElementById('prev-day').addEventListener('click', function() {
            navigateCalendar(-1);
        });
        
        document.getElementById('next-day').addEventListener('click', function() {
            navigateCalendar(1);
        });
        
        document.getElementById('today').addEventListener('click', function() {
            const today = new Date();
            updateCalendarDate(today);
            loadServices(formatDate(today));
        });
        
        // Botón para ir a la hora actual
        document.getElementById('currentHour').addEventListener('click', function() {
            scrollToCurrentHour();
        });
        
        // Botones de vista
        document.getElementById('dayViewBtn').addEventListener('click', function() {
            document.querySelector('.technician-calendar-container').classList.remove('d-none');
            document.querySelector('.calendar-container').classList.add('d-none');
            this.classList.add('btn-outline-primary');
            this.classList.remove('btn-outline-secondary');
            document.getElementById('weekViewBtn').classList.add('btn-outline-secondary');
            document.getElementById('weekViewBtn').classList.remove('btn-outline-primary');
        });
        
        document.getElementById('weekViewBtn').addEventListener('click', function() {
            document.querySelector('.technician-calendar-container').classList.add('d-none');
            document.querySelector('.calendar-container').classList.remove('d-none');
            this.classList.add('btn-outline-primary');
            this.classList.remove('btn-outline-secondary');
            document.getElementById('dayViewBtn').classList.add('btn-outline-secondary');
            document.getElementById('dayViewBtn').classList.remove('btn-outline-primary');
            
            // Actualizar el calendario original
            if (window.calendar) {
                window.calendar.render();
            }
        });
        
        // Eventos para celdas de servicio (crear nuevos servicios con doble click)
        document.querySelectorAll('.calendar-service-cell').forEach(cell => {
            cell.addEventListener('dblclick', function(event) {
                if (!event.target.closest('.calendar-service')) {
                    const hour = this.getAttribute('data-hour');
                    const technicianId = this.getAttribute('data-technician-id');
                    openNewServiceModal(hour, technicianId);
                }
            });
        });
        
        // Actualizar indicador de hora actual
        updateCurrentTimeIndicator();
        
        // Calcular el tiempo restante hasta el próximo minuto exacto para sincronizar actualizaciones
        const nowTime = new Date();
        const timeToNextMinute = (60 - nowTime.getSeconds()) * 1000;
        
        // Primera actualización sincronizada al minuto exacto
        setTimeout(() => {
            updateCurrentTimeIndicator();
            // Luego, actualizar regularmente cada minuto
            setInterval(updateCurrentTimeIndicator, 60000);
        }, timeToNextMinute);
    });
    
    /**
     * Inicializa el calendario con la fecha actual
     */
    function initTechnicianCalendar() {
        // Configurar la fecha actual
        const today = new Date();
        updateCalendarDate(today);
        
        // Cargar datos de servicios para la fecha actual
        loadServices(formatDate(today));
    }
    
    /**
     * Navega el calendario un día hacia adelante o hacia atrás
     * @param {number} direction - Dirección de navegación (-1 para atrás, 1 para adelante)
     */
    function navigateCalendar(direction) {
        const currentDateEl = document.getElementById('current-date');
        const currentDateAttr = currentDateEl.getAttribute('data-date');
        let currentDate;
        
        if (currentDateAttr) {
            currentDate = new Date(currentDateAttr);
        } else {
            currentDate = new Date();
            currentDateEl.setAttribute('data-date', formatDate(currentDate));
        }
        
        // Avanzar o retroceder un día
        currentDate.setDate(currentDate.getDate() + direction);
        
        // Actualizar fecha y cargar servicios
        updateCalendarDate(currentDate);
        loadServices(formatDate(currentDate));
    }
    
    /**
     * Actualiza la visualización de la fecha del calendario
     * @param {Date} date - La fecha a mostrar
     */
    function updateCalendarDate(date) {
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        const formattedDate = date.toLocaleDateString('es-ES', options);
        
        document.getElementById('current-date').textContent = formattedDate;
        document.getElementById('current-date').setAttribute('data-date', formatDate(date));
    }
    
    /**
     * Formatea una fecha como YYYY-MM-DD
     * @param {Date} date - Objeto fecha
     * @returns {string} - Fecha formateada
     */
    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        
        return `${year}-${month}-${day}`;
    }
    
    /**
     * Carga los servicios para la fecha especificada
     * @param {string} date - Fecha en formato YYYY-MM-DD
     */
    function loadServices(date) {
        // Mostrar indicador de carga
        showLoadingIndicator(true);
        
        // Limpiar servicios existentes
        clearServices();
        
        // Obtener valores de los filtros
        const technicianId = document.getElementById('technicianFilter').value;
        const status = document.getElementById('statusFilter').value;
        const confirmation = document.getElementById('confirmationFilter').value;
        
        // Construir URL con parámetros de filtrado
        let url = `/admin/api/schedules?start=${date}T00:00:00&end=${date}T23:59:59`;
        if (technicianId) url += `&technician_id=${technicianId}`;
        if (status) url += `&status=${status}`;
        if (confirmation) url += `&confirmation=${confirmation}`;
        
        // Realizar petición AJAX para obtener los servicios
        fetch(url)
            .then(response => response.json())
            .then(events => {
                // Renderizar los servicios
                renderServices(events);
                
                // Ocultar indicador de carga
                showLoadingIndicator(false);
            })
            .catch(error => {
                console.error('Error al cargar los servicios:', error);
                showLoadingIndicator(false);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudieron cargar los servicios. Por favor, intente nuevamente.'
                });
            });
    }
    
    /**
     * Muestra u oculta el indicador de carga
     * @param {boolean} show - Indica si se debe mostrar u ocultar
     */
    function showLoadingIndicator(show) {
        // Por implementar un indicador visual
        document.body.style.cursor = show ? 'wait' : 'default';
    }
    
    /**
     * Elimina todos los servicios del calendario
     */
    function clearServices() {
        document.querySelectorAll('.calendar-service').forEach(service => {
            service.remove();
        });
    }
    
    /**
     * Renderiza los servicios en el calendario
     * @param {Array} events - Array de objetos de evento
     */
    function renderServices(events) {
        events.forEach(event => {
            const startTime = new Date(event.start);
            const hour = startTime.getHours();
            const minute = startTime.getMinutes();
            const technicianId = event.resourceId;
            
            // Determinar si es hora exacta o media hora (redondeamos al bloque más cercano)
            const minuteBlock = minute < 15 ? 0 : minute < 45 ? 30 : 0;
            const hourAdjusted = minuteBlock === 0 && minute >= 45 ? (hour + 1) % 24 : hour;
            
            // Encontrar la celda correspondiente
            const cell = document.querySelector(`.calendar-service-cell[data-hour="${hourAdjusted}"][data-minute="${minuteBlock}"][data-technician-id="${technicianId}"]`);
            
            if (cell) {
                // Determinar tipo de servicio
                let serviceType = 'appointment';
                const eventTitle = (event.title || '').toLowerCase();
                if (eventTitle.includes('reunión') || eventTitle.includes('meeting')) {
                    serviceType = 'meeting';
                } else if (eventTitle.includes('descanso') || eventTitle.includes('almuerzo') || eventTitle.includes('break') || eventTitle.includes('lunch')) {
                    serviceType = 'break';
                } else if (eventTitle.includes('llamada') || eventTitle.includes('conferencia') || eventTitle.includes('call') || eventTitle.includes('conference')) {
                    serviceType = 'conference';
                }
                
                // Crear el elemento para el servicio
                const serviceElement = document.createElement('div');
                serviceElement.className = `calendar-service service-${serviceType}`;
                serviceElement.setAttribute('data-service-id', event.id);
                
                // Agregar clases según estado y confirmación
                if (event.extendedProps && event.extendedProps.status) {
                    serviceElement.classList.add(`status-${event.extendedProps.status.replace(' ', '')}`);
                }
                
                if (event.extendedProps && event.extendedProps.confirmation_status) {
                    serviceElement.classList.add(`confirmation-${event.extendedProps.confirmation_status}`);
                }
                
                // Extraer información del título
                let displayTitle = event.title;
                let clientName = '';
                if (event.extendedProps) {
                    displayTitle = event.extendedProps.serviceName || displayTitle.split(' - ')[0];
                    clientName = event.extendedProps.clientName || (displayTitle.includes(' - ') ? displayTitle.split(' - ')[1] : '');
                }
                
                // Calcular hora de inicio y fin en formato 24 horas
                const startTimeStr = startTime.getHours().toString().padStart(2, '0') + ':' + 
                                     startTime.getMinutes().toString().padStart(2, '0');
                
                const endTime = new Date(event.end);
                const endTimeStr = endTime.getHours().toString().padStart(2, '0') + ':' + 
                                   endTime.getMinutes().toString().padStart(2, '0');
                
                // Contenido HTML del servicio
                serviceElement.innerHTML = `
                    <div class="service-title">${displayTitle}</div>
                    ${clientName ? `<div class="service-client">${clientName}</div>` : ''}
                    <div class="service-time">${startTimeStr} - ${endTimeStr}</div>
                `;
                
                // Añadir evento clic
                serviceElement.addEventListener('click', function() {
                    showServiceDetails(event.id);
                });
                
                // Añadir al calendario
                cell.appendChild(serviceElement);
            }
        });
    }
    
    /**
     * Abre el modal para crear un nuevo servicio
     * @param {string} hour - Hora seleccionada
     * @param {string|number} technicianId - ID del técnico seleccionado
     */
    function openNewServiceModal(hour, technicianId) {
        // Configurar fecha y hora en el modal
        const currentDate = document.getElementById('current-date').getAttribute('data-date');
        if (!currentDate) return;
        
        // Obtener el modal y configurar la fecha/hora
        const modal = document.getElementById('newScheduleModal');
        if (modal) {
            // Seleccionar el técnico en el modal
            const techSelect = document.getElementById('technician_id');
            if (techSelect) techSelect.value = technicianId;
            
            // Configurar fecha y hora
            const dateInput = document.getElementById('scheduled_date');
            if (dateInput) {
                const hourInt = parseInt(hour);
                const timeStr = hourInt < 10 ? `0${hourInt}:00` : `${hourInt}:00`;
                dateInput.value = `${currentDate}T${timeStr}`;
                console.log(`Configurando fecha de inicio: ${currentDate}T${timeStr}`);
                
                // Limpiar listeners previos para evitar duplicación
                const endTimeInput = document.getElementById('end_time');
                const durationInput = document.getElementById('duration');
                
                if (endTimeInput && durationInput) {
                    // Eliminar event listeners previos si existen
                    const newEndTimeInput = endTimeInput.cloneNode(true);
                    endTimeInput.parentNode.replaceChild(newEndTimeInput, endTimeInput);
                    
                    const newDurationInput = durationInput.cloneNode(true);
                    durationInput.parentNode.replaceChild(newDurationInput, durationInput);
                    
                    // Usar referencias actualizadas
                    const updatedEndTimeInput = document.getElementById('end_time');
                    const updatedDurationInput = document.getElementById('duration');
                    
                    // Si es una celda de 8:00, asumir que es para un agendamiento de 2 horas (8-10)
                    // Para otros horarios, usar duración de 60 minutos por defecto
                    let durationValue = 60;
                    if (hourInt === 8) {
                        durationValue = 120; // 2 horas si empieza a las 8:00
                        console.log(`Hora de inicio es 8:00, configurando duración de 2 horas (${durationValue} minutos)`);
                    } else {
                        console.log(`Usando duración estándar de 60 minutos`);
                    }
                    
                    // Actualizar el campo de duración
                    updatedDurationInput.value = durationValue;
                    
                    const startDate = new Date(`${currentDate}T${timeStr}`);
                    const endDate = new Date(startDate.getTime() + durationValue * 60000);
                    console.log(`Hora de inicio: ${startDate.toLocaleString()}, Hora de fin calculada: ${endDate.toLocaleString()}`);
                    
                    // Formatear la hora de finalización (HH:MM)
                    const endHours = endDate.getHours().toString().padStart(2, '0');
                    const endMinutes = endDate.getMinutes().toString().padStart(2, '0');
                    updatedEndTimeInput.value = `${endHours}:${endMinutes}`;
                    console.log(`Hora de finalización establecida: ${endHours}:${endMinutes}`);
                    
                    // Mostrar información del horario seleccionado
                    const selectedTimeInfo = document.getElementById('selected-time-info');
                    const selectedTimeText = document.getElementById('selected-time-text');
                    if (selectedTimeInfo && selectedTimeText) {
                        const formattedStartTime = startDate.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                        const formattedEndTime = endDate.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                        
                        const infoText = `${formattedStartTime} - ${formattedEndTime} (${durationValue} minutos)`;
                        selectedTimeText.textContent = infoText;
                        selectedTimeInfo.classList.remove('d-none');
                        console.log(`Información de tiempo mostrada: ${infoText}`);
                    }
                    
                    // Función reutilizable para actualizar el infobox
                    function updateTimeInfoBox(start, end, duration) {
                        const selectedTimeText = document.getElementById('selected-time-text');
                        if (selectedTimeText) {
                            const formattedStart = start.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                            const formattedEnd = end.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                            selectedTimeText.textContent = `${formattedStart} - ${formattedEnd} (${duration} minutos)`;
                            document.getElementById('selected-time-info').classList.remove('d-none');
                        }
                    }
                    
                    // LISTENERS PARA CAMPOS DE TIEMPO
                    
                    // 1. Cuando cambia la duración, actualizar hora de fin
                    updatedDurationInput.addEventListener('input', function() {
                        if (dateInput.value) {
                            const startDate = new Date(dateInput.value);
                            const durationValue = parseInt(this.value) || 60;
                            const endDate = new Date(startDate.getTime() + durationValue * 60000);
                            
                            // Formatear la hora de finalización (HH:MM)
                            const endHours = endDate.getHours().toString().padStart(2, '0');
                            const endMinutes = endDate.getMinutes().toString().padStart(2, '0');
                            updatedEndTimeInput.value = `${endHours}:${endMinutes}`;
                            
                            // Actualizar el infobox
                            updateTimeInfoBox(startDate, endDate, durationValue);
                            
                            // Resaltar el campo actualizado
                            updatedEndTimeInput.classList.add('field-highlight');
                            setTimeout(() => updatedEndTimeInput.classList.remove('field-highlight'), 1000);
                        }
                    });
                    
                    // 2. Cuando cambia la hora de fin, calcular duración
                    updatedEndTimeInput.addEventListener('input', function() {
                        if (dateInput.value) {
                            const startDate = new Date(dateInput.value);
                            const endTimeArr = this.value.split(':');
                            const endDate = new Date(startDate);
                            
                            // Asegurar que los valores son números
                            const endHours = parseInt(endTimeArr[0]) || 0;
                            const endMinutes = parseInt(endTimeArr[1]) || 0;
                            
                            endDate.setHours(endHours, endMinutes);
                            
                            // Si la hora de fin es anterior a la hora de inicio, asumimos que es para el día siguiente
                            if (endDate < startDate) {
                                endDate.setDate(endDate.getDate() + 1);
                            }
                            
                            // Calcular duración en minutos
                            const durationMinutes = Math.round((endDate - startDate) / 60000);
                            if (durationMinutes > 0) {
                                updatedDurationInput.value = durationMinutes;
                                
                                // Actualizar infobox
                                updateTimeInfoBox(startDate, endDate, durationMinutes);
                                
                                // Resaltar el campo actualizado
                                updatedDurationInput.classList.add('field-highlight');
                                setTimeout(() => updatedDurationInput.classList.remove('field-highlight'), 1000);
                            }
                        }
                    });
                    
                    // 3. Cuando cambia fecha/hora de inicio, actualizar fin
                    dateInput.addEventListener('input', function() {
                        if (this.value) {
                            const durationValue = parseInt(updatedDurationInput.value) || 60;
                            const startDate = new Date(this.value);
                            const endDate = new Date(startDate.getTime() + durationValue * 60000);
                            
                            // Formatear la hora de finalización (HH:MM)
                            const endHours = endDate.getHours().toString().padStart(2, '0');
                            const endMinutes = endDate.getMinutes().toString().padStart(2, '0');
                            updatedEndTimeInput.value = `${endHours}:${endMinutes}`;
                            
                            // Actualizar infobox
                            updateTimeInfoBox(startDate, endDate, durationValue);
                            
                            // Resaltar el campo actualizado
                            updatedEndTimeInput.classList.add('field-highlight');
                            setTimeout(() => updatedEndTimeInput.classList.remove('field-highlight'), 1000);
                        }
                    });
                }
                
                // Actualizar título del modal con la hora
                document.getElementById('newScheduleModalLabel').innerHTML = 
                    `<span class="me-2" style="font-size: 2rem;"><i class="fas fa-calendar-plus"></i></span> Nuevo Agendamiento - ${hour}:00 hrs`;
                
                // Abrir el modal
                const bsModal = new bootstrap.Modal(modal);
                bsModal.show();
            }
        }
    }    /**
     * Muestra los detalles de un servicio
     * @param {string|number} serviceId - ID del servicio
     */
    function showServiceDetails(serviceId) {
        // Usar la función existente para mostrar detalles
        if (typeof showScheduleDetails === 'function') {
            showScheduleDetails(serviceId);
        } else {
            // Abrir el modal de detalles
            fetch(`/admin/schedules/${serviceId}`)
                .then(response => response.json())
                .then(data => {
                    // Aquí iría el código para mostrar los detalles en un modal
                    console.log('Detalles del servicio:', data);
                })
                .catch(error => {
                    console.error('Error al obtener detalles del servicio:', error);
                });
        }
    }
    
    /**
     * Actualiza la posición del indicador de hora actual y hace scroll a la hora actual en el calendario
     */
    function scrollToCurrentHour() {
        const now = new Date();
        const hours = now.getHours();
        const minutes = now.getMinutes();
        
        // Determinar si estamos en la primera o segunda mitad de la hora
        const isFirstHalf = minutes < 30;
        const minuteBlock = isFirstHalf ? '0' : '30';
        
        // Buscar la celda de la hora/media hora actual
        const hourCell = document.querySelector(`.calendar-hour-cell[data-hour="${hours}"][data-minute="${minuteBlock}"]`);
        
        if (hourCell) {
            // Obtener el contenedor del calendario con scroll
            const calendarContainer = document.querySelector('.technician-calendar-container');
            
            // Calcular la posición para el scroll
            const hourCellRect = hourCell.getBoundingClientRect();
            const calendarContainerRect = calendarContainer.getBoundingClientRect();
            
            // Calcular posición y aplicar scroll dentro del contenedor del calendario
            const scrollPosition = hourCellRect.top + calendarContainer.scrollTop - calendarContainerRect.top - 100;
            
            // Scroll con animación suave en el contenedor del calendario
            calendarContainer.scrollTo({
                top: scrollPosition, // 100px arriba para dar contexto
                behavior: 'smooth'
            });
            
            // Crear un resaltado temporal para el indicador
            const indicator = document.querySelector('.current-time-indicator');
            if (indicator) {
                indicator.classList.add('highlight-indicator');
                // Asegurar que el indicador es visible dentro del contenedor con scroll
                const indicatorTop = parseInt(indicator.style.top, 10);
                const containerHeight = calendarContainer.clientHeight;
                const containerScrollTop = calendarContainer.scrollTop;
                const containerScrollBottom = containerScrollTop + containerHeight;
                
                // Si el indicador no está visible en la ventana actual, ajustar el scroll
                if (indicatorTop < containerScrollTop || indicatorTop > containerScrollBottom) {
                    calendarContainer.scrollTop = indicatorTop - (containerHeight / 2); // Centrar el indicador
                }
                
                setTimeout(() => {
                    indicator.classList.remove('highlight-indicator');
                }, 2500);
            }
            
            // Efecto de resaltado temporal para la hora/media hora
            const allCells = document.querySelectorAll(`.calendar-service-cell[data-hour="${hours}"][data-minute="${minuteBlock}"]`);
            allCells.forEach(cell => {
                cell.classList.add('highlight-cell');
                setTimeout(() => {
                    cell.classList.remove('highlight-cell');
                }, 2500);
            });
            
            // Agregar mensaje de notificación
            const timeLabel = document.createElement('div');
            timeLabel.className = 'time-jump-notification';
            timeLabel.textContent = `Hora actual: ${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
            document.body.appendChild(timeLabel);
            
            setTimeout(() => {
                timeLabel.classList.add('show');
                
                setTimeout(() => {
                    timeLabel.classList.remove('show');
                    setTimeout(() => {
                        timeLabel.remove();
                    }, 500);
                }, 2000);
            }, 100);
        }
    }
    
    function updateCurrentTimeIndicator() {
        const now = new Date();
        const hours = now.getHours();
        const minutes = now.getMinutes();
        const startHour = 0; // Hora de inicio del calendario (00:00)
        const endHour = 23; // Hora de fin del calendario (23:00)
        const hourHeight = 28; // Altura en píxeles de cada fila de hora completa (actualizada)
        const halfHourHeight = 22; // Altura en píxeles de media hora (actualizada)
        const calendarContainer = document.querySelector('.technician-calendar-container');
        const calendarHeaderHeight = document.querySelector('.calendar-header-hour') ? 
            document.querySelector('.calendar-header-hour').offsetHeight : 0;
        
        // Determinar si estamos en la primera o segunda mitad de la hora
        const isFirstHalf = minutes < 30;
        const currentMinuteBlock = isFirstHalf ? 0 : 30;
        
        // Marcar la hora actual y media hora en el calendario
        document.querySelectorAll('.calendar-row').forEach(row => {
            const hourCell = row.querySelector('.calendar-hour-cell');
            const rowHour = parseInt(hourCell.getAttribute('data-hour'));
            const rowMinute = parseInt(hourCell.getAttribute('data-minute') || '0');
            
            // Marcar horas laborales/no laborales (8:00 - 18:00)
            const isWorkHour = (rowHour >= 8 && rowHour < 18) || 
                              (rowHour === 18 && rowMinute === 0);
            row.setAttribute('data-work-hours', isWorkHour.toString());
            
            // Marcar hora actual (hora exacta o media hora)
            const isCurrentHour = (rowHour === hours && rowMinute === 0);
            const isCurrentHalfHour = (rowHour === hours && rowMinute === 30);
            
            row.setAttribute('data-current-hour', isCurrentHour.toString());
            row.setAttribute('data-current-half-hour', isCurrentHalfHour.toString());
            
            // Marcar la media hora activa (donde estamos actualmente)
            const isActiveTimeBlock = (rowHour === hours && rowMinute === currentMinuteBlock);
            row.setAttribute('data-active-time-block', isActiveTimeBlock.toString());
        });
        
        // Mostrar y posicionar el indicador
        const indicator = document.querySelector('.current-time-indicator');
        if (indicator) {
            indicator.classList.remove('d-none');
            
            // Formatear la hora actual para mostrarla en el indicador
            const formattedTime = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
            indicator.setAttribute('data-time', formattedTime);
            
            // Determinar la celda de la hora/media hora actual
            const minuteSelector = isFirstHalf ? '0' : '30';
            const hourCell = document.querySelector(`.calendar-hour-cell[data-hour="${hours}"][data-minute="${minuteSelector}"]`);
            
            if (hourCell) {
                // Posicionar relativo a la celda de la hora o media hora
                const hourTop = hourCell.getBoundingClientRect().top;
                const calendarContainerTop = document.querySelector('.technician-calendar-container').getBoundingClientRect().top;
                const relativeTop = hourTop - calendarContainerTop + calendarContainer.scrollTop;
                
                // Calcular la posición de los minutos dentro del bloque actual (0-30 o 30-60)
                const blockMinutes = isFirstHalf ? minutes : minutes - 30;
                const cellHeight = isFirstHalf ? hourHeight : halfHourHeight;
                const minutePosition = (blockMinutes / 30) * cellHeight;
                
                // Reposicionar el indicador exactamente en la hora actual
                indicator.style.top = `${Math.round(relativeTop + minutePosition)}px`;
            } else {
                // Cálculo fallback si no se encuentra la celda
                const headerHeight = document.querySelector('.calendar-header-row') ? 
                    document.querySelector('.calendar-header-row').offsetHeight : 0;
                let position = headerHeight;
                
                // Sumar la altura de todas las horas anteriores
                for (let h = 0; h < hours; h++) {
                    position += hourHeight + halfHourHeight; // Sumar hora completa y media hora
                }
                
                // Agregar la posición dentro de la hora actual
                if (isFirstHalf) {
                    position += (minutes / 30) * hourHeight;
                } else {
                    position += hourHeight + (minutes - 30) / 30 * halfHourHeight;
                }
                
                indicator.style.top = `${Math.round(position)}px`;
            }
            
            // Función para comprobar si una posición del tiempo está visible dentro del contenedor
            function isTimePositionVisible(position, container) {
                const containerTop = container.scrollTop;
                const containerBottom = containerTop + container.clientHeight;
                return position >= containerTop && position <= containerBottom;
            }
            
            // Si el indicador de hora actual no está en el área visible, ajustar el scroll
            // pero solo si no estamos respondiendo a un clic explícito en el botón "Hora Actual"
            if (calendarContainer && !document.activeElement?.id === 'currentHour') {
                const indicatorTop = parseInt(indicator.style.top, 10);
                if (!isTimePositionVisible(indicatorTop, calendarContainer)) {
                    calendarContainer.scrollTop = indicatorTop - (calendarContainer.clientHeight / 2);
                }
            }
        }
    }
</script>

<!-- Script de corrección URGENTE para asegurar que las líneas horizontales abarcan todo el calendario -->
<script>
    // Esta función se ejecuta al final para asegurar que los cambios se apliquen
    document.addEventListener('DOMContentLoaded', function() {
        // Esperar a que todo esté cargado
        setTimeout(function() {
            console.log("¡APLICANDO CORRECCIONES CRÍTICAS!");
            
            // MEJORA 1: NOMBRES DE TÉCNICOS LEGIBLES
            // Forzar estilo en celdas de técnicos
            var techCells = document.querySelectorAll('.fc-datagrid-cell');
            techCells.forEach(function(cell) {
                cell.style.minWidth = '150px';
                cell.style.width = '150px';
                cell.style.backgroundColor = '#f0f8ff';
                cell.style.border = '2px solid #004122';
            });
            
            // Forzar estilo en nombres de técnicos
            var techNames = document.querySelectorAll('.fc-datagrid-cell-cushion');
            techNames.forEach(function(name) {
                name.style.whiteSpace = 'normal';
                name.style.overflow = 'visible';
                name.style.wordBreak = 'break-word';
                name.style.fontWeight = 'bold';
                name.style.fontSize = '14px';
                name.style.color = '#000';
                name.style.padding = '5px';
            });
            
            // MEJORA 2: LÍNEAS HORIZONTALES COMPLETAS
            // Hacer líneas muy visibles
            var rows = document.querySelectorAll('.fc-timeline-slots tr');
            rows.forEach(function(row) {
                row.style.borderTop = '2px solid #87c947';
            });
            
            var lanes = document.querySelectorAll('.fc-timeline-slot-lane');
            lanes.forEach(function(lane) {
                lane.style.borderTop = '2px solid #87c947';
            });
            
            // Aplicar a todas las celdas
            var timeSlotCells = document.querySelectorAll('.fc-timeline-slots td');
            timeSlotCells.forEach(function(cell) {
                cell.style.borderTop = '2px solid #87c947';
            });
            
            // Hacer que las tablas ocupen todo el ancho
            var tables = document.querySelectorAll('.fc-timeline-body table');
            tables.forEach(function(table) {
                table.style.width = '100%';
            });
            
            console.log("¡Correcciones aplicadas con éxito!");
        }, 2000); // Retrasar para asegurar que FullCalendar ha terminado de renderizar
    });
</script>

<!-- Script estabilizador del calendario (debe ir antes de otros scripts) -->
<script src="{{ asset('js/calendar-stabilizer.js') }}"></script>

<!-- Script para selección por arrastre en el calendario (base) -->
<script src="{{ asset('js/calendar-drag-selection.js') }}"></script>

<!-- Solución unificada y simplificada para la selección -->
<script src="{{ asset('js/calendar-simple-selection.js') }}"></script>

<!-- Corrección final de posicionamiento -->
<script src="{{ asset('js/calendar-position-fix.js') }}"></script>

<!-- Optimizaciones para movimiento fluido -->
<script src="{{ asset('js/calendar-smooth-movement.js') }}"></script>

<!-- Corrección para el indicador de hora -->
<script src="{{ asset('js/calendar-indicator-fix.js') }}"></script>

<!-- Corrección crítica para el indicador de hora (definitiva) -->
<script src="{{ asset('js/calendar-critical-indicator-fix.js') }}"></script>

<!-- Diagnóstico para el modal directo -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('=== DIAGNÓSTICO DEL MODAL DIRECTO ===');
        
        // Verificar si el modal existe
        const directModal = document.getElementById('newDirectScheduleModal');
        if (directModal) {
            console.log('✓ Modal de agendamiento directo encontrado en el DOM');
        } else {
            console.error('✗ ERROR: Modal de agendamiento directo NO encontrado en el DOM');
        }
        
        // Verificar el botón auxiliar
        const btnOpen = document.getElementById('btnOpenDirectModal');
        if (btnOpen) {
            console.log('✓ Botón auxiliar para abrir modal encontrado');
            
            // Añadir listener explícito
            btnOpen.addEventListener('click', function() {
                console.log('Botón auxiliar clickeado');
                const modal = document.getElementById('newDirectScheduleModal');
                if (modal) {
                    console.log('Intentando mostrar modal desde botón auxiliar');
                    const bsModal = new bootstrap.Modal(modal);
                    bsModal.show();
                }
            });
        }
        
        // Función global para abrir el modal desde cualquier parte
        window.abrirModalDirecto = function() {
            console.log('Ejecutando función global abrirModalDirecto()');
            const modal = document.getElementById('newDirectScheduleModal');
            if (modal) {
                const directModal = new bootstrap.Modal(modal);
                directModal.show();
                return true;
            } else {
                console.error('Modal no encontrado en abrirModalDirecto()');
                return false;
            }
        };
    });
</script>

<!-- Script para sincronización de campos de tiempo (mejorado) -->
<script src="{{ asset('js/calendar-time-sync.js') }}"></script>

<!-- Manejador especial para selección de calendario -->
<script src="{{ asset('js/calendar-selection-handler.js') }}"></script>

<!-- Script para sincronización de duración basado en el servicio -->
<script src="{{ asset('js/service-duration-sync.js') }}"></script>

<!-- Script para arreglar problemas de cálculo de tiempo -->
<script src="{{ asset('js/direct-time-fixer.js') }}"></script>

<!-- Script para selector de tipo de agendamiento -->
<script src="{{ asset('js/schedule-type-selector.js') }}"></script>

<!-- Script para manejo del formulario de agendamiento directo -->
<script src="{{ asset('js/direct-schedule-form.js') }}"></script>

<!-- Script integrador para los servicios -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Configurando integración de servicios...');

        // 1. Verificar que ambos modales existen
        const modalSolicitud = document.getElementById('newScheduleModal');
        const modalDirecto = document.getElementById('newDirectScheduleModal');
        
        if (modalDirecto) {
            console.log('Modal directo encontrado ✓');
            
            // Añadir listener específico para cuando se abra el modal directo
            modalDirecto.addEventListener('shown.bs.modal', function() {
                console.log('MODAL DIRECTO ABIERTO Y VISIBLE');
                
                // Enfatizar el selector de servicios
                const serviceSelect = document.getElementById('direct_service_id');
                if (serviceSelect) {
                    serviceSelect.focus();
                    serviceSelect.classList.add('border-highlight');
                    setTimeout(() => {
                        serviceSelect.classList.remove('border-highlight');
                    }, 1500);
                }
            });
        } else {
            console.error('Modal directo NO encontrado ✗');
        }
        
        // 2. Crear un botón físico en el DOM para abrir el modal directo
        const btnOpener = document.getElementById('btnOpenDirectModal');
        if (!btnOpener) {
            console.log('Creando botón auxiliar para modal directo');
            const btn = document.createElement('button');
            btn.id = 'btnOpenDirectModal';
            btn.className = 'd-none';
            btn.setAttribute('data-bs-toggle', 'modal');
            btn.setAttribute('data-bs-target', '#newDirectScheduleModal');
            btn.textContent = 'Abrir Modal Directo';
            document.body.appendChild(btn);
        }
    });
</script>
@endpush
