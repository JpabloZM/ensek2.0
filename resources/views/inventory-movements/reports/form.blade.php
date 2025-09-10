@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Generar Reporte de Movimientos</h1>
    <p class="mb-4">Configure los parámetros para generar el reporte de movimientos de inventario.</p>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Parámetros del Reporte</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.inventory-movements.report') }}">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="date_from">Fecha Desde *</label>
                            <input type="date" class="form-control @error('date_from') is-invalid @enderror" 
                                   id="date_from" 
                                   name="date_from" 
                                   value="{{ old('date_from', date('Y-m-01')) }}" 
                                   required>
                            @error('date_from')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="date_to">Fecha Hasta *</label>
                            <input type="date" class="form-control @error('date_to') is-invalid @enderror" 
                                   id="date_to" 
                                   name="date_to" 
                                   value="{{ old('date_to', date('Y-m-d')) }}" 
                                   required>
                            @error('date_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="report_type">Tipo de Reporte *</label>
                            <select class="form-control @error('report_type') is-invalid @enderror" 
                                    id="report_type" 
                                    name="report_type" 
                                    required>
                                <option value="summary">Reporte Resumido</option>
                                <option value="detail">Reporte Detallado</option>
                            </select>
                            @error('report_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="movement_type">Tipo de Movimiento</label>
                            <select class="form-control @error('movement_type') is-invalid @enderror" 
                                    id="movement_type" 
                                    name="movement_type">
                                <option value="all">Todos los movimientos</option>
                                <option value="entry">Entradas</option>
                                <option value="exit">Salidas</option>
                                <option value="adjustment">Ajustes</option>
                            </select>
                            @error('movement_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="item_id">Artículo (opcional)</label>
                            <select class="form-control @error('item_id') is-invalid @enderror" 
                                    id="item_id" 
                                    name="item_id">
                                <option value="">Todos los artículos</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                            @error('item_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="card bg-light mt-4">
                            <div class="card-body">
                                <h5 class="card-title">Información</h5>
                                <p class="card-text">Los reportes pueden ser visualizados en pantalla o exportados a PDF/Excel desde la vista del reporte.</p>
                                <p class="card-text">El reporte resumido muestra totales agrupados por tipo de movimiento, mientras que el detallado lista todos los movimientos individuales.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-file-alt"></i> Generar Reporte
                        </button>
                        <a href="{{ route('admin.inventory-movements.index') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Acceso Rápido a Reportes Predefinidos -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Reportes Predefinidos</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.inventory-movements.report', ['date_from' => date('Y-m-01'), 'date_to' => date('Y-m-d'), 'movement_type' => 'entry', 'report_type' => 'detail']) }}" class="btn btn-success btn-block">
                                <i class="fas fa-arrow-circle-down"></i> Entradas del Mes Actual
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.inventory-movements.report', ['date_from' => date('Y-m-01'), 'date_to' => date('Y-m-d'), 'movement_type' => 'exit', 'report_type' => 'detail']) }}" class="btn btn-danger btn-block">
                                <i class="fas fa-arrow-circle-up"></i> Salidas del Mes Actual
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.inventory-movements.report', ['date_from' => date('Y-01-01'), 'date_to' => date('Y-12-31'), 'movement_type' => 'all', 'report_type' => 'summary']) }}" class="btn btn-info btn-block">
                                <i class="fas fa-chart-pie"></i> Resumen Anual
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.inventory-movements.valuation') }}" class="btn btn-warning btn-block">
                                <i class="fas fa-dollar-sign"></i> Valoración de Inventario
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Inicializar select2 para selector de artículos
        $('#item_id').select2({
            placeholder: "Seleccione un artículo (opcional)",
            allowClear: true
        });
        
        // Establecer fechas por defecto (principio de mes hasta hoy)
        if (!$('#date_from').val()) {
            const now = new Date();
            const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
            $('#date_from').val(firstDay.toISOString().split('T')[0]);
            $('#date_to').val(new Date().toISOString().split('T')[0]);
        }
    });
</script>
@endsection
