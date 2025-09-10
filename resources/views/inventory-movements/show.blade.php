@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Detalle del Movimiento de Inventario</h1>
    <p class="mb-4">Información detallada del movimiento de inventario.</p>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Información del Movimiento</h6>
                    <a href="{{ route('admin.inventory-movements.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">ID de Movimiento</label>
                                <p>{{ $movement->id }}</p>
                            </div>
                            
                            <div class="form-group">
                                <label class="font-weight-bold">Fecha y Hora</label>
                                <p>{{ $movement->created_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                            
                            <div class="form-group">
                                <label class="font-weight-bold">Artículo</label>
                                <p>{{ $movement->item->name }}</p>
                            </div>
                            
                            <div class="form-group">
                                <label class="font-weight-bold">Código</label>
                                <p>{{ $movement->item->code }}</p>
                            </div>
                            
                            <div class="form-group">
                                <label class="font-weight-bold">Tipo de Movimiento</label>
                                <p>
                                    @if($movement->movement_type == 'entry')
                                        <span class="badge badge-success">{{ $movement->formatted_type }}</span>
                                    @elseif($movement->movement_type == 'exit')
                                        <span class="badge badge-danger">{{ $movement->formatted_type }}</span>
                                    @else
                                        <span class="badge badge-warning">{{ $movement->formatted_type }}</span>
                                    @endif
                                </p>
                            </div>
                            
                            <div class="form-group">
                                <label class="font-weight-bold">Usuario</label>
                                <p>{{ $movement->user->name }}</p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Cantidad</label>
                                <p>{{ $movement->quantity }} {{ $movement->item->unit_of_measure }}</p>
                            </div>
                            
                            <div class="form-group">
                                <label class="font-weight-bold">Precio Unitario</label>
                                <p>${{ number_format($movement->unit_price, 2) }}</p>
                            </div>
                            
                            <div class="form-group">
                                <label class="font-weight-bold">Valor Total</label>
                                <p>{{ $movement->value }}</p>
                            </div>
                            
                            <div class="form-group">
                                <label class="font-weight-bold">Cantidad Anterior</label>
                                <p>{{ $movement->previous_quantity }} {{ $movement->item->unit_of_measure }}</p>
                            </div>
                            
                            <div class="form-group">
                                <label class="font-weight-bold">Cantidad Nueva</label>
                                <p>{{ $movement->new_quantity }} {{ $movement->item->unit_of_measure }}</p>
                            </div>
                            
                            <div class="form-group">
                                <label class="font-weight-bold">Tipo de Referencia</label>
                                <p>
                                    @switch($movement->reference_type)
                                        @case('manual')
                                            Manual
                                            @break
                                        @case('provider')
                                            Proveedor
                                            @break
                                        @case('service')
                                            Servicio
                                            @break
                                        @case('purchase')
                                            Compra
                                            @break
                                        @default
                                            {{ $movement->reference_type }}
                                    @endswitch
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    @if($movement->reference && $movement->reference_type != 'manual')
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-header">
                                    Información de Referencia
                                </div>
                                <div class="card-body">
                                    @switch($movement->reference_type)
                                        @case('provider')
                                            <p><strong>Proveedor:</strong> {{ $movement->reference->name }}</p>
                                            <p><strong>Contacto:</strong> {{ $movement->reference->contact_name }}</p>
                                            <p><strong>Teléfono:</strong> {{ $movement->reference->phone }}</p>
                                            @break
                                        @case('service')
                                            <p><strong>Servicio:</strong> {{ $movement->reference->name }}</p>
                                            <p><strong>Descripción:</strong> {{ $movement->reference->description }}</p>
                                            @break
                                        @case('purchase')
                                            <p><strong>Compra #:</strong> {{ $movement->reference->id }}</p>
                                            <p><strong>Fecha:</strong> {{ $movement->reference->date->format('d/m/Y') }}</p>
                                            <p><strong>Proveedor:</strong> {{ $movement->reference->provider->name }}</p>
                                            @break
                                    @endswitch
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($movement->notes)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    Notas
                                </div>
                                <div class="card-body">
                                    {{ $movement->notes }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Estado Actual</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Artículo</label>
                        <h5>{{ $movement->item->name }}</h5>
                    </div>
                    
                    <div class="form-group">
                        <label class="font-weight-bold">Categoría</label>
                        <p>{{ $movement->item->category->name }}</p>
                    </div>
                    
                    <div class="form-group">
                        <label class="font-weight-bold">Stock Actual</label>
                        <h4>{{ $movement->item->quantity }} {{ $movement->item->unit_of_measure }}</h4>
                    </div>
                    
                    <div class="form-group">
                        <label class="font-weight-bold">Stock Mínimo</label>
                        <p>{{ $movement->item->minimum_stock }} {{ $movement->item->unit_of_measure }}</p>
                    </div>
                    
                    <div class="form-group">
                        <label class="font-weight-bold">Estado</label>
                        @if($movement->item->isLowStock())
                            <p><span class="badge badge-danger">Stock Bajo</span></p>
                        @else
                            <p><span class="badge badge-success">Stock OK</span></p>
                        @endif
                    </div>
                    
                    <div class="form-group">
                        <label class="font-weight-bold">Valor Actual en Inventario</label>
                        <h5>{{ $movement->item->formatted_total_value }}</h5>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.inventory-items.show', $movement->item_id) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-box"></i> Ver Artículo
                        </a>
                        <a href="{{ route('admin.inventory-movements.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Nuevo Movimiento
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
