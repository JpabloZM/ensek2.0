@extends('layouts.admin')

@section('page-title', 'Detalle de Agendamiento')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Detalles del Agendamiento #{{ $schedule->id }}</h5>
                        <div>
                            <a href="{{ route('admin.schedules.index') }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                            <a href="{{ route('admin.schedules.edit', $schedule->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Información del Cliente</h6>
                            <div class="mb-3">
                                <strong>Cliente:</strong> {{ $schedule->serviceRequest->client_name }}
                            </div>
                            <div class="mb-3">
                                <strong>Teléfono:</strong> {{ $schedule->serviceRequest->client_phone }}
                            </div>
                            <div class="mb-3">
                                <strong>Dirección:</strong> {{ $schedule->serviceRequest->client_address }}
                            </div>
                            <div class="mb-3">
                                <strong>Email:</strong> {{ $schedule->serviceRequest->client_email }}
                            </div>
                            <h6 class="text-muted mb-2 mt-4">Información del Servicio</h6>
                            <div class="mb-3">
                                <strong>Servicio:</strong> {{ $schedule->serviceRequest->service->name }}
                            </div>
                            <div class="mb-3">
                                <strong>Descripción del problema:</strong> 
                                <p>{{ $schedule->serviceRequest->problem_description }}</p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Información del Agendamiento</h6>
                            <div class="mb-3">
                                <strong>Fecha y hora programada:</strong> 
                                <span>{{ $schedule->scheduled_date->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="mb-3">
                                <strong>Técnico asignado:</strong> {{ $schedule->technician->user->name }}
                            </div>
                            <div class="mb-3">
                                <strong>Estado:</strong> 
                                @if($schedule->status == 'pendiente')
                                    <span class="badge bg-warning">Pendiente</span>
                                @elseif($schedule->status == 'en proceso')
                                    <span class="badge bg-info">En proceso</span>
                                @elseif($schedule->status == 'completado')
                                    <span class="badge bg-success">Completado</span>
                                @else
                                    <span class="badge bg-danger">Cancelado</span>
                                @endif
                            </div>
                            
                            @if($schedule->completed_at)
                            <div class="mb-3">
                                <strong>Completado el:</strong> 
                                <span>{{ $schedule->completed_at->format('d/m/Y H:i') }}</span>
                            </div>
                            @endif
                            
                            @if($schedule->notes)
                            <div class="mb-3">
                                <strong>Notas:</strong>
                                <p>{{ $schedule->notes }}</p>
                            </div>
                            @endif
                            
                            <h6 class="text-muted mb-2 mt-4">Actualizaciones</h6>
                            @if($schedule->status != 'completado' && $schedule->status != 'cancelado')
                            <form action="{{ route('admin.schedules.update', $schedule->id) }}" method="POST" class="mb-3">
                                @csrf
                                @method('PATCH')
                                <div class="input-group">
                                    <select name="status" class="form-select">
                                        <option value="pendiente" {{ $schedule->status == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="en proceso" {{ $schedule->status == 'en proceso' ? 'selected' : '' }}>En proceso</option>
                                        <option value="completado">Completar</option>
                                        <option value="cancelado">Cancelar</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary">Actualizar Estado</button>
                                </div>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
