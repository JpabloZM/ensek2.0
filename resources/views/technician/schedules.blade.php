@extends('layouts.technician')

@section('page-title', 'Mis Agendamientos')

@section('content')
<div class="container-fluid">
    <div class="row my-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Mis Servicios Agendados</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Servicio</th>
                                    <th>Dirección</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($schedules as $schedule)
                                    <tr>
                                        <td>{{ $schedule->scheduled_date->format('d/m/Y H:i') }}</td>
                                        <td>{{ $schedule->serviceRequest->client_name }}</td>
                                        <td>{{ $schedule->serviceRequest->service->name }}</td>
                                        <td>{{ $schedule->serviceRequest->address }}</td>
                                        <td>
                                            @if($schedule->status == 'pendiente')
                                                <span class="badge bg-warning">Pendiente</span>
                                            @elseif($schedule->status == 'en proceso')
                                                <span class="badge bg-info">En proceso</span>
                                            @elseif($schedule->status == 'completado')
                                                <span class="badge bg-success">Completado</span>
                                            @else
                                                <span class="badge bg-danger">Cancelado</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailsModal{{ $schedule->id }}">
                                                <i class="fas fa-eye"></i> Detalles
                                            </button>
                                            
                                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#statusModal{{ $schedule->id }}">
                                                <i class="fas fa-edit"></i> Estado
                                            </button>
                                        </td>
                                    </tr>
                                    
                                    <!-- Modal de detalles -->
                                    <div class="modal fade" id="detailsModal{{ $schedule->id }}" tabindex="-1" aria-labelledby="detailsModalLabel{{ $schedule->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="detailsModalLabel{{ $schedule->id }}">Detalles del servicio</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <h6 class="mb-3">Información del cliente:</h6>
                                                    <p><strong>Nombre:</strong> {{ $schedule->serviceRequest->client_name }}</p>
                                                    <p><strong>Teléfono:</strong> {{ $schedule->serviceRequest->client_phone }}</p>
                                                    <p><strong>Email:</strong> {{ $schedule->serviceRequest->client_email ?? 'No especificado' }}</p>
                                                    <p><strong>Dirección:</strong> {{ $schedule->serviceRequest->address }}</p>
                                                    
                                                    <hr>
                                                    
                                                    <h6 class="mb-3">Detalles del servicio:</h6>
                                                    <p><strong>Servicio:</strong> {{ $schedule->serviceRequest->service->name }}</p>
                                                    <p><strong>Descripción:</strong> {{ $schedule->serviceRequest->description }}</p>
                                                    <p><strong>Notas adicionales:</strong> {{ $schedule->serviceRequest->notes ?? 'Ninguna' }}</p>
                                                    
                                                    <hr>
                                                    
                                                    <h6 class="mb-3">Detalles del agendamiento:</h6>
                                                    <p><strong>Fecha:</strong> {{ $schedule->scheduled_date->format('d/m/Y H:i') }}</p>
                                                    <p><strong>Duración estimada:</strong> {{ $schedule->serviceRequest->service->duration }} minutos</p>
                                                    <p><strong>Estado actual:</strong> 
                                                        @if($schedule->status == 'pendiente')
                                                            <span class="badge bg-warning">Pendiente</span>
                                                        @elseif($schedule->status == 'en proceso')
                                                            <span class="badge bg-info">En proceso</span>
                                                        @elseif($schedule->status == 'completado')
                                                            <span class="badge bg-success">Completado</span>
                                                        @else
                                                            <span class="badge bg-danger">Cancelado</span>
                                                        @endif
                                                    </p>
                                                    @if($schedule->completed_at)
                                                        <p><strong>Completado el:</strong> {{ $schedule->completed_at->format('d/m/Y H:i') }}</p>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Modal para cambiar estado -->
                                    <div class="modal fade" id="statusModal{{ $schedule->id }}" tabindex="-1" aria-labelledby="statusModalLabel{{ $schedule->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="statusModalLabel{{ $schedule->id }}">Actualizar estado</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ route('technician.schedules.update-status', $schedule) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        
                                                        <div class="mb-3">
                                                            <label for="status" class="form-label">Estado del servicio</label>
                                                            <select class="form-select" id="status" name="status" required>
                                                                <option value="pendiente" {{ $schedule->status == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                                                <option value="en proceso" {{ $schedule->status == 'en proceso' ? 'selected' : '' }}>En proceso</option>
                                                                <option value="completado" {{ $schedule->status == 'completado' ? 'selected' : '' }}>Completado</option>
                                                                <option value="cancelado" {{ $schedule->status == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                                                            </select>
                                                        </div>
                                                        
                                                        <div class="d-grid">
                                                            <button type="submit" class="btn btn-primary">Actualizar estado</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No tienes servicios agendados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $schedules->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
