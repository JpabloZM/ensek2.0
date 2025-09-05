@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Detalles de Solicitud #{{ $serviceRequest->id }}</h6>
                    <div>
                        @if($serviceRequest->status == 'pendiente')
                            <a href="{{ route('admin.schedules.create', ['service_request_id' => $serviceRequest->id]) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-calendar-plus"></i> Agendar
                            </a>
                        @endif
                        <a href="{{ route('admin.service-requests.edit', $serviceRequest->id) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('admin.service-requests.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold text-primary">Información del Cliente</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Nombre:</strong> {{ $serviceRequest->client_name }}
                                    </div>
                                    <div class="mb-3">
                                        <strong>Teléfono:</strong> {{ $serviceRequest->client_phone }}
                                    </div>
                                    <div class="mb-3">
                                        <strong>Email:</strong> {{ $serviceRequest->client_email ?? 'No especificado' }}
                                    </div>
                                    <div class="mb-3">
                                        <strong>Dirección:</strong> {{ $serviceRequest->address }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold text-primary">Información del Servicio</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Servicio:</strong> {{ $serviceRequest->service->name }}
                                    </div>
                                    <div class="mb-3">
                                        <strong>Precio:</strong> ${{ number_format($serviceRequest->service->price, 2) }}
                                    </div>
                                    <div class="mb-3">
                                        <strong>Duración Estimada:</strong> {{ $serviceRequest->service->duration ?? 'No especificada' }}
                                    </div>
                                    <div class="mb-3">
                                        <strong>Estado:</strong>
                                        @php
                                            $statusClasses = [
                                                'pendiente' => 'warning',
                                                'agendado' => 'info',
                                                'completado' => 'success',
                                                'cancelado' => 'danger'
                                            ];
                                            $statusClass = $statusClasses[$serviceRequest->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge badge-{{ $statusClass }} badge-lg">
                                            {{ ucfirst($serviceRequest->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold text-primary">Descripción del Problema</h6>
                                </div>
                                <div class="card-body">
                                    <p>{{ $serviceRequest->description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($serviceRequest->notes)
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold text-primary">Notas Adicionales</h6>
                                </div>
                                <div class="card-body">
                                    <p>{{ $serviceRequest->notes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($serviceRequest->schedule)
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold text-primary">Información del Agendamiento</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <strong>Fecha y Hora:</strong> {{ $serviceRequest->schedule->scheduled_at->format('d/m/Y H:i') }}
                                            </div>
                                            <div class="mb-3">
                                                <strong>Técnico Asignado:</strong> {{ $serviceRequest->schedule->technician->user->name }}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <strong>Estado:</strong>
                                                @php
                                                    $scheduleStatusClasses = [
                                                        'pendiente' => 'warning',
                                                        'en proceso' => 'info',
                                                        'completado' => 'success',
                                                        'cancelado' => 'danger'
                                                    ];
                                                    $scheduleStatusClass = $scheduleStatusClasses[$serviceRequest->schedule->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge badge-{{ $scheduleStatusClass }} badge-lg">
                                                    {{ ucfirst($serviceRequest->schedule->status) }}
                                                </span>
                                            </div>
                                            <div class="mb-3">
                                                <a href="{{ route('admin.schedules.show', $serviceRequest->schedule->id) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> Ver Agendamiento
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="row mt-4">
                        <div class="col-md-12 text-right">
                            <div class="small text-muted">
                                Creado el: {{ $serviceRequest->created_at->format('d/m/Y H:i') }}
                                @if($serviceRequest->created_at != $serviceRequest->updated_at)
                                <br>Última actualización: {{ $serviceRequest->updated_at->format('d/m/Y H:i') }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
