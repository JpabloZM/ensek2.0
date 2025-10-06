<!-- Modal para crear nuevo agendamiento directo (sin solicitud previa) -->
<div class="modal fade" id="newDirectScheduleModal" tabindex="-1" aria-labelledby="newDirectScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="newDirectScheduleModalLabel">Crear Agendamiento Directo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="directScheduleForm" action="{{ route('admin.schedules.store-direct') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Creación rápida de un nuevo agendamiento sin una solicitud previa.
                    </div>
                    
                    <!-- Datos del cliente -->
                    <div class="card direct-schedule-card client-card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-user card-header-icon"></i>Datos del Cliente</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="direct_client_name" class="form-label">Nombre del Cliente <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control required" id="direct_client_name" name="client_name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="direct_client_phone" class="form-label">Teléfono <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control required" id="direct_client_phone" name="client_phone" placeholder="Ej: (123) 456-7890" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="direct_client_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="direct_client_email" name="client_email">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="direct_client_address" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="direct_client_address" name="address">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Datos del servicio -->
                    <div class="card direct-schedule-card service-card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-tools card-header-icon"></i>Datos del Servicio</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="direct_service_id" class="form-label">Tipo de Servicio <span class="text-danger">*</span></label>
                                    <select class="form-select required" id="direct_service_id" name="service_id" required>
                                        <option value="">Seleccione un servicio...</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}" 
                                                data-duration="{{ $service->duration ?? 60 }}" 
                                                data-price="{{ $service->price ?? 0 }}">
                                                {{ $service->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Seleccione uno de los servicios que ofrece la empresa</small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="direct_description" class="form-label">Descripción del Servicio <span class="text-danger">*</span></label>
                                    <textarea class="form-control required" id="direct_description" name="description" rows="3" required placeholder="Describa el servicio que necesita el cliente..."></textarea>
                                    <small class="form-text text-muted">Se completará automáticamente al seleccionar un servicio, pero puede modificarlo</small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="direct_estimated_cost" class="form-label">Costo Estimado ($)</label>
                                    <input type="number" class="form-control" id="direct_estimated_cost" name="estimated_cost" step="0.01">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="direct_duration" class="form-label">Duración (minutos)</label>
                                    <input type="number" class="form-control" id="direct_duration" name="duration" min="15" step="15" value="60">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Datos del agendamiento -->
                    <div class="card direct-schedule-card schedule-card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-calendar-alt card-header-icon"></i>Datos del Agendamiento</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="direct_technician_id" class="form-label">Técnico <span class="text-danger">*</span></label>
                                    <select class="form-select" id="direct_technician_id" name="technician_id" required>
                                        <option value="">Seleccione un técnico...</option>
                                        @foreach($technicians as $technician)
                                            <option value="{{ $technician->id }}">
                                                {{ $technician->user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="direct_status" class="form-label">Estado</label>
                                    <select class="form-select" id="direct_status" name="status">
                                        <option value="pendiente">Pendiente</option>
                                        <option value="en proceso">En proceso</option>
                                        <option value="completado">Completado</option>
                                        <option value="cancelado">Cancelado</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="direct_scheduled_date" class="form-label">Fecha <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control required" id="direct_scheduled_date" name="scheduled_date" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="direct_start_time" class="form-label">Hora de Inicio <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control required" id="direct_start_time" name="start_time" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="direct_end_time" class="form-label">Hora de Finalización <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control required" id="direct_end_time" name="end_time" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="direct_notes" class="form-label">Notas Adicionales</label>
                                    <textarea class="form-control" id="direct_notes" name="notes" rows="2" placeholder="Información adicional importante para el agendamiento..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success btn-save-direct-schedule">
                        <i class="fas fa-save me-1"></i>Guardar Agendamiento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>