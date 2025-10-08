<!-- Modal para crear nuevo agendamiento -->
<div class="modal fade" id="newScheduleModal" tabindex="-1" aria-labelledby="newScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(to right, #004122, #045a30);">
                <h5 class="modal-title" id="newScheduleModalLabel">
                    <i class="fas fa-calendar-plus me-2"></i>Nuevo Agendamiento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.schedules.store-direct') }}" method="POST">
                @csrf
                <input type="hidden" name="direct_scheduling" value="1">
                <div class="modal-body p-4">
                    <!-- Alerta para horario seleccionado -->
                    <div class="alert alert-info d-none mb-4" id="selected-time-info" style="border-left: 4px solid #0dcaf0;">
                    </div>
                    
                    <div class="row">
                        <!-- Columna izquierda: Servicio y Cliente -->
                        <div class="col-lg-6 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white py-3 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle bg-primary text-white me-3">
                                            <i class="fas fa-tools"></i>
                                        </div>
                                        <h6 class="mb-0 fw-bold text-primary">Servicio y Cliente</h6>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <div class="mb-4">
                                        <label for="service_id" class="form-label fw-semibold">
                                            <i class="fas fa-tools me-1 text-primary"></i>
                                            Servicio a Programar
                                        </label>
                                        <select class="form-select border-0 shadow-sm" id="service_id" name="service_id" required style="background-color: #f8f9fa;">
                                            <option value="">Seleccione un servicio...</option>
                                            @foreach($services as $service)
                                                <option value="{{ $service->id }}">
                                                    {{ $service->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted mt-1 d-block">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Seleccione el servicio que desea programar
                                        </small>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="client_name" class="form-label fw-semibold">
                                            <i class="fas fa-user me-1 text-primary"></i>
                                            Nombre del Cliente
                                        </label>
                                        <input type="text" class="form-control border-0 shadow-sm" 
                                            id="client_name" name="client_name" required style="background-color: #f8f9fa;" 
                                            placeholder="Ingrese el nombre del cliente">
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="client_phone" class="form-label fw-semibold">
                                            <i class="fas fa-phone-alt me-1 text-primary"></i>
                                            Teléfono de Contacto
                                        </label>
                                        <input type="tel" class="form-control border-0 shadow-sm" 
                                            id="client_phone" name="client_phone" style="background-color: #f8f9fa;" 
                                            placeholder="Ingrese el teléfono de contacto">
                                    </div>

                                    <div class="mb-0">
                                        <label for="technician_id" class="form-label fw-semibold">
                                            <i class="fas fa-user-hard-hat me-1 text-primary"></i>
                                            Técnico Asignado
                                        </label>
                                        <select class="form-select border-0 shadow-sm" id="technician_id" name="technician_id" required style="background-color: #f8f9fa;">
                                            <option value="">Seleccione un técnico...</option>
                                            @foreach($technicians as $technician)
                                                <option value="{{ $technician->id }}">
                                                    {{ $technician->user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Columna derecha: Programación y Notas -->
                        <div class="col-lg-6 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white py-3 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle bg-info text-white me-3">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                        <h6 class="mb-0 fw-bold text-info">Programación y Detalles</h6>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <div class="mb-4">
                                        <label for="scheduled_date" class="form-label fw-semibold">
                                            <i class="fas fa-calendar-day me-1 text-info"></i>
                                            Fecha y Hora de Inicio
                                        </label>
                                        <input type="datetime-local" class="form-control border-0 shadow-sm" 
                                            id="scheduled_date" name="scheduled_date" required style="background-color: #f8f9fa;">
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="end_time" class="form-label fw-semibold">
                                            <i class="fas fa-hourglass-end me-1 text-info"></i>
                                            Hora de Finalización
                                        </label>
                                        <input type="time" class="form-control border-0 shadow-sm" 
                                            id="end_time" name="end_time" required style="background-color: #f8f9fa;">
                                        <small class="text-muted mt-1 d-block">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Hora estimada de finalización del servicio
                                        </small>
                                    </div>
                                    
                                    <div class="row mb-4">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <label for="duration" class="form-label fw-semibold">
                                                <i class="fas fa-clock me-1 text-info"></i>
                                                Duración (min)
                                            </label>
                                            <div class="input-group">
                                                <input type="number" class="form-control border-0 shadow-sm" 
                                                    id="duration" name="duration" min="15" step="15" value="60" style="background-color: #f8f9fa;">
                                                <span class="input-group-text bg-info text-white border-0">
                                                    <i class="fas fa-stopwatch"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="estimated_cost" class="form-label fw-semibold">
                                                <i class="fas fa-hand-holding-usd me-1 text-info"></i>
                                                Costo ($)
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-info text-white border-0">
                                                    <i class="fas fa-dollar-sign"></i>
                                                </span>
                                                <input type="number" class="form-control border-0 shadow-sm" 
                                                    id="estimated_cost" name="estimated_cost" step="0.01" style="background-color: #f8f9fa;">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-check form-switch ps-0 mb-4">
                                        <div class="d-flex align-items-center">
                                            <input class="form-check-input me-2" type="checkbox" role="switch" id="send_notification" name="send_notification" checked style="width: 2.5rem; height: 1.25rem;">
                                            <label class="form-check-label" for="send_notification">
                                                <i class="fas fa-envelope me-1 text-info"></i>
                                                Enviar notificación al cliente
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label for="notes" class="form-label fw-semibold">
                                            <i class="fas fa-sticky-note me-1 text-info"></i>
                                            Notas Adicionales
                                        </label>
                                        <textarea class="form-control border-0 shadow-sm" id="notes" name="notes" rows="4" 
                                            placeholder="Instrucciones especiales, requerimientos o detalles adicionales..." 
                                            style="background-color: #f8f9fa; resize: none;"></textarea>
                                        <small class="text-muted mt-1 d-block">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Estas notas son visibles para el técnico asignado
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary px-4" style="background-color: #004122; border: none;">
                        <i class="fas fa-calendar-check me-2"></i>Programar Servicio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Estilos para el ícono circular */
    .icon-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }
</style>