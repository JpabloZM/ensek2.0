@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Movimientos de Inventario</h1>
    <p class="mb-4">Administre y visualice los movimientos del inventario.</p>
    
    <!-- Tarjetas de Estadísticas -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Entradas Totales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_entries'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-circle-down fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Salidas Totales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_exits'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-circle-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Ajustes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_adjustments'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Valor Total Inventario</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($stats['total_value'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de búsqueda -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('inventory-movements.index') }}">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Tipo de Movimiento</label>
                            <select class="form-control" name="movement_type">
                                <option value="" {{ $filters['movement_type'] == '' ? 'selected' : '' }}>Todos</option>
                                <option value="entry" {{ $filters['movement_type'] == 'entry' ? 'selected' : '' }}>Entradas</option>
                                <option value="exit" {{ $filters['movement_type'] == 'exit' ? 'selected' : '' }}>Salidas</option>
                                <option value="adjustment" {{ $filters['movement_type'] == 'adjustment' ? 'selected' : '' }}>Ajustes</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Fecha Desde</label>
                            <input type="date" class="form-control" name="date_from" value="{{ $filters['date_from'] }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Fecha Hasta</label>
                            <input type="date" class="form-control" name="date_to" value="{{ $filters['date_to'] }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Artículo</label>
                            <select class="form-control" name="item_id">
                                <option value="">Todos los artículos</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}" {{ $filters['item_id'] == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Referencia</label>
                            <select class="form-control" name="reference_type">
                                <option value="" {{ $filters['reference_type'] == '' ? 'selected' : '' }}>Todas</option>
                                <option value="manual" {{ $filters['reference_type'] == 'manual' ? 'selected' : '' }}>Manual</option>
                                <option value="provider" {{ $filters['reference_type'] == 'provider' ? 'selected' : '' }}>Proveedor</option>
                                <option value="service" {{ $filters['reference_type'] == 'service' ? 'selected' : '' }}>Servicio</option>
                                <option value="purchase" {{ $filters['reference_type'] == 'purchase' ? 'selected' : '' }}>Compra</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-block">Filtrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Tabla de Movimientos -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Lista de Movimientos</h6>
                    <div>
                        <a href="{{ route('admin.inventory-movements.create') }}" class="btn btn-sm btn-primary shadow-sm">
                            <i class="fas fa-plus fa-sm text-white-50"></i> Nuevo Movimiento
                        </a>
                        <a href="{{ route('admin.inventory-movements.report.form') }}" class="btn btn-sm btn-info shadow-sm ml-2">
                            <i class="fas fa-file-alt fa-sm text-white-50"></i> Generar Reporte
                        </a>
                        <a href="{{ route('admin.inventory-movements.valuation') }}" class="btn btn-sm btn-success shadow-sm ml-2">
                            <i class="fas fa-chart-bar fa-sm text-white-50"></i> Valoración de Inventario
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Artículo</th>
                                    <th>Tipo</th>
                                    <th>Cantidad</th>
                                    <th>Unidad</th>
                                    <th>Valor</th>
                                    <th>Usuario</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($movements as $movement)
                                <tr>
                                    <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $movement->item->name }}</td>
                                    <td>
                                        @if($movement->movement_type == 'entry')
                                            <span class="badge badge-success">{{ $movement->formatted_type }}</span>
                                        @elseif($movement->movement_type == 'exit')
                                            <span class="badge badge-danger">{{ $movement->formatted_type }}</span>
                                        @else
                                            <span class="badge badge-warning">{{ $movement->formatted_type }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $movement->quantity }}</td>
                                    <td>{{ $movement->item->unit_of_measure }}</td>
                                    <td>{{ $movement->value }}</td>
                                    <td>{{ $movement->user->name }}</td>
                                    <td>
                                        <a href="{{ route('inventory-movements.show', $movement->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $movements->links() }}
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Movimientos Recientes -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Movimientos Recientes</h6>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($stats['recent_movements'] as $movement)
                            <a href="{{ route('inventory-movements.show', $movement->id) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $movement->item->name }}</h6>
                                    <small>{{ $movement->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">
                                    <span class="font-weight-bold">
                                        @if($movement->movement_type == 'entry')
                                            <span class="text-success">+{{ $movement->quantity }}</span>
                                        @elseif($movement->movement_type == 'exit')
                                            <span class="text-danger">-{{ $movement->quantity }}</span>
                                        @else
                                            <span class="text-warning">={{ $movement->quantity }}</span>
                                        @endif
                                    </span> 
                                    {{ $movement->item->unit_of_measure }}
                                </p>
                                <small>Valor: {{ $movement->value }}</small>
                            </a>
                        @endforeach
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
        // Inicializar select2 para selectores con muchas opciones
        $('select[name="item_id"]').select2({
            placeholder: "Seleccione un artículo",
            allowClear: true
        });
    });
</script>
@endsection
