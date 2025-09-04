@extends('layouts.admin')

@section('page-title', 'Editar Agendamiento')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Editar Agendamiento #{{ $schedule->id }}</h5>
                        <a href="{{ route('admin.schedules.show', $schedule->id) }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.schedules.update', $schedule->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="service_request_id" class="form-label">Solicitud de Servicio</label>
                                    <select class="form-select" id="service_request_id" name="service_request_id" required {{ $schedule->status == 'completado' ? 'disabled' : '' }}>
                                        @foreach($serviceRequests as $request)
                                            <option value="{{ $request->id }}" {{ $schedule->service_request_id == $request->id ? 'selected' : '' }}>
                                                {{ $request->service->name }} - {{ $request->client_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="technician_id" class="form-label">Técnico</label>
                                    <select class="form-select" id="technician_id" name="technician_id" required {{ $schedule->status == 'completado' ? 'disabled' : '' }}>
                                        @foreach($technicians as $technician)
                                            <option value="{{ $technician->id }}" {{ $schedule->technician_id == $technician->id ? 'selected' : '' }}>
                                                {{ $technician->user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="scheduled_date" class="form-label">Fecha y Hora</label>
                                    <input type="datetime-local" class="form-control" id="scheduled_date" name="scheduled_date" 
                                           value="{{ $schedule->scheduled_date->format('Y-m-d\TH:i') }}" required {{ $schedule->status == 'completado' ? 'disabled' : '' }}>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Estado</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="pendiente" {{ $schedule->status == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="en proceso" {{ $schedule->status == 'en proceso' ? 'selected' : '' }}>En proceso</option>
                                        <option value="completado" {{ $schedule->status == 'completado' ? 'selected' : '' }}>Completado</option>
                                        <option value="cancelado" {{ $schedule->status == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notas</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="6">{{ $schedule->notes }}</textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
