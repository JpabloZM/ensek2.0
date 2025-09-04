@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Detalle del Técnico</h6>
                    <div>
                        <a href="{{ route('admin.technicians.edit', $technician->id) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('admin.technicians.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Nombre</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $technician->user->name }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-user fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Email</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $technician->user->email }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Teléfono</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $technician->user->phone ?? 'No registrado' }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-phone fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Información del Técnico</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Especialidad</th>
                                            <td>{{ $technician->specialtyService ? $technician->specialtyService->name : 'Sin especialidad' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Estado</th>
                                            <td>
                                                @if($technician->active)
                                                    <span class="badge badge-success">Activo</span>
                                                @else
                                                    <span class="badge badge-danger">Inactivo</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Disponibilidad</th>
                                            <td>{{ $technician->availability }}</td>
                                        </tr>
                                        <tr>
                                            <th>Habilidades</th>
                                            <td>{{ $technician->skills ?? 'No especificadas' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Agendamientos</h6>
                                </div>
                                <div class="card-body">
                                    @if($technician->schedules->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Fecha</th>
                                                        <th>Servicio</th>
                                                        <th>Estado</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($technician->schedules as $schedule)
                                                        <tr>
                                                            <td>{{ $schedule->scheduled_at->format('d/m/Y H:i') }}</td>
                                                            <td>{{ $schedule->serviceRequest->service->name ?? 'N/A' }}</td>
                                                            <td>
                                                                @switch($schedule->status)
                                                                    @case('pending')
                                                                        <span class="badge badge-warning">Pendiente</span>
                                                                        @break
                                                                    @case('in_progress')
                                                                        <span class="badge badge-info">En Progreso</span>
                                                                        @break
                                                                    @case('completed')
                                                                        <span class="badge badge-success">Completado</span>
                                                                        @break
                                                                    @case('cancelled')
                                                                        <span class="badge badge-danger">Cancelado</span>
                                                                        @break
                                                                    @default
                                                                        <span class="badge badge-secondary">{{ $schedule->status }}</span>
                                                                @endswitch
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-center">No hay agendamientos asignados a este técnico.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
