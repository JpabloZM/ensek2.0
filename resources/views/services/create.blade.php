@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Crear Nuevo Servicio</h1>
        <a href="{{ route('admin.services.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Volver
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Información del Servicio</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.services.store') }}" method="POST">
                @csrf

                <div class="form-group row">
                    <label for="name" class="col-sm-2 col-form-label">Nombre <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="description" class="col-sm-2 col-form-label">Descripción <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="price" class="col-sm-2 col-form-label">Precio (COP) <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" step="1" min="0" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text text-muted">Precio en pesos colombianos (sin decimales)</small>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="tax_rate" class="col-sm-2 col-form-label">Tasa de impuesto <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <div class="input-group">
                            <input type="number" class="form-control @error('tax_rate') is-invalid @enderror" id="tax_rate" name="tax_rate" value="{{ old('tax_rate', 16) }}" step="0.01" min="0" max="100" required>
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                            @error('tax_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text text-muted">Porcentaje de impuesto aplicable a este servicio.</small>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="duration" class="col-sm-2 col-form-label">Duración (min) <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control @error('duration') is-invalid @enderror" id="duration" name="duration" value="{{ old('duration', 30) }}" min="5" required>
                        @error('duration')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Duración estimada del servicio en minutos.</small>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="special_requirements" class="col-sm-2 col-form-label">Requisitos especiales</label>
                    <div class="col-sm-10">
                        <textarea class="form-control @error('special_requirements') is-invalid @enderror" id="special_requirements" name="special_requirements" rows="3">{{ old('special_requirements') }}</textarea>
                        @error('special_requirements')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Requisitos especiales para realizar este servicio (opcional).</small>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="materials_included" class="col-sm-2 col-form-label">Materiales incluidos</label>
                    <div class="col-sm-10">
                        <textarea class="form-control @error('materials_included') is-invalid @enderror" id="materials_included" name="materials_included" rows="3">{{ old('materials_included') }}</textarea>
                        @error('materials_included')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Materiales incluidos en el precio del servicio (opcional).</small>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-2">Requiere técnico especializado</div>
                    <div class="col-sm-10">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="requires_technician_approval" name="requires_technician_approval" {{ old('requires_technician_approval') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="requires_technician_approval">Sí</label>
                        </div>
                        <small class="form-text text-muted">Marque si este servicio requiere un técnico con aprobación especial.</small>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-2">Estado</div>
                    <div class="col-sm-10">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="active" name="active" {{ old('active') ? 'checked' : '' }} checked>
                            <label class="custom-control-label" for="active">Activo</label>
                        </div>
                        <small class="form-text text-muted">Los servicios inactivos no aparecerán en las opciones disponibles para crear solicitudes.</small>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Servicio
                        </button>
                        <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
