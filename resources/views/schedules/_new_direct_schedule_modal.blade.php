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
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>