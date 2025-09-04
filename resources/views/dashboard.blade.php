@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row g-3 my-2">
        <!-- Solicitudes pendientes -->
        <div class="col-md-3">
            <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                <div>
                    <h3 class="fs-2">{{ $stats['pendingRequests'] }}</h3>
                    <p class="fs-5">Solicitudes Pendientes</p>
                </div>
                <i class="fas fa-clipboard-list fs-1 primary-text border rounded-full secondary-bg p-3"></i>
            </div>
        </div>

        <!-- Servicios agendados -->
        <div class="col-md-3">
            <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                <div>
                    <h3 class="fs-2">{{ $stats['scheduledServices'] }}</h3>
                    <p class="fs-5">Servicios Agendados</p>
                </div>
                <i class="fas fa-calendar-check fs-1 primary-text border rounded-full secondary-bg p-3"></i>
            </div>
        </div>

        <!-- Servicios completados -->
        <div class="col-md-3">
            <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                <div>
                    <h3 class="fs-2">{{ $stats['completedServices'] }}</h3>
                    <p class="fs-5">Servicios Completados</p>
                </div>
                <i class="fas fa-check-circle fs-1 primary-text border rounded-full secondary-bg p-3"></i>
            </div>
        </div>

        <!-- Ítems de inventario con bajo stock -->
        <div class="col-md-3">
            <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                <div>
                    <h3 class="fs-2">{{ $stats['lowStockItems'] }}</h3>
                    <p class="fs-5">Ítems con Bajo Stock</p>
                </div>
                <i class="fas fa-exclamation-triangle fs-1 primary-text border rounded-full secondary-bg p-3"></i>
            </div>
        </div>
    </div>

    <div class="row my-5">
        <!-- Solicitudes recientes -->
        <div class="col-md-6">
            <h3 class="fs-4 mb-3">Solicitudes de servicio recientes</h3>
            <div class="card">
                <div class="card-body">
                    <table class="table bg-white rounded shadow-sm table-hover">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Cliente</th>
                                <th scope="col">Servicio</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentRequests as $request)
                                <tr>
                                    <td>{{ $request->id }}</td>
                                    <td>{{ $request->client_name }}</td>
                                    <td>{{ $request->service->name }}</td>
                                    <td>
                                        @if($request->status == 'pendiente')
                                            <span class="badge bg-warning">Pendiente</span>
                                        @elseif($request->status == 'agendado')
                                            <span class="badge bg-primary">Agendado</span>
                                        @elseif($request->status == 'completado')
                                            <span class="badge bg-success">Completado</span>
                                        @else
                                            <span class="badge bg-danger">Cancelado</span>
                                        @endif
                                    </td>
                                    <td>{{ $request->created_at->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No hay solicitudes recientes</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Próximos servicios agendados -->
        <div class="col-md-6">
            <h3 class="fs-4 mb-3">Próximos servicios agendados</h3>
            <div class="card">
                <div class="card-body">
                    <table class="table bg-white rounded shadow-sm table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Fecha</th>
                                <th scope="col">Cliente</th>
                                <th scope="col">Técnico</th>
                                <th scope="col">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($upcomingSchedules as $schedule)
                                <tr>
                                    <td>{{ $schedule->scheduled_date->format('d/m/Y H:i') }}</td>
                                    <td>{{ $schedule->serviceRequest->client_name }}</td>
                                    <td>{{ $schedule->technician->user->name }}</td>
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No hay servicios agendados próximamente</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
