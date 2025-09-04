@extends('layouts.admin')

@section('page-title', 'Nuevo Agendamiento')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Crear Nuevo Agendamiento</h5>
                        <a href="{{ route('admin.schedules.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al Calendario
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.schedules.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="service_request_id" class="form-label">Solicitud de Servicio</label>
                                    <select class="form-select @error('service_request_id') is-invalid @enderror" id="service_request_id" name="service_request_id" required>
                                        <option value="">Seleccione una solicitud...</option>
                                        @foreach($pendingRequests as $request)
                                            <option value="{{ $request->id }}" {{ old('service_request_id') == $request->id ? 'selected' : '' }}>
                                                {{ $request->service->name }} - {{ $request->client_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('service_request_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="technician_id" class="form-label">Técnico</label>
                                    <select class="form-select @error('technician_id') is-invalid @enderror" id="technician_id" name="technician_id" required>
                                        <option value="">Seleccione un técnico...</option>
                                        @foreach($technicians as $technician)
                                            <option value="{{ $technician->id }}" {{ old('technician_id') == $technician->id ? 'selected' : '' }}>
                                                {{ $technician->user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('technician_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="scheduled_date" class="form-label">Fecha y Hora</label>
                                    <input type="datetime-local" class="form-control @error('scheduled_date') is-invalid @enderror" 
                                           id="scheduled_date" name="scheduled_date" value="{{ old('scheduled_date') }}" required>
                                    @error('scheduled_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Estado</label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="pendiente" {{ old('status', 'pendiente') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="en proceso" {{ old('status') == 'en proceso' ? 'selected' : '' }}>En proceso</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notas</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="6">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Agendamiento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
