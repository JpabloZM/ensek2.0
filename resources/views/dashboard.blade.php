@extends('layouts.admin')

@section('page-title', 'Panel de Control')

@section('content')
<div class="container-fluid">
    <div class="row g-4 my-4">
        <!-- Solicitudes pendientes -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body position-relative p-0">
                    <div class="position-absolute w-100" style="height: 4px; background-color: #ffc107; top: 0;"></div>
                    <div class="px-4 pt-4 pb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex flex-column">
                                <p class="text-muted mb-1 fw-light">Solicitudes Pendientes</p>
                                <h2 class="fs-1 fw-bold mb-0">{{ $stats['pendingRequests'] }}</h2>
                            </div>
                            <div class="icon-wrapper d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px; background-color: #ffc107;">
                                <i class="fas fa-clipboard-list fs-3 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Servicios agendados -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body position-relative p-0">
                    <div class="position-absolute w-100" style="height: 4px; background-color: var(--ensek-green-dark); top: 0;"></div>
                    <div class="px-4 pt-4 pb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex flex-column">
                                <p class="text-muted mb-1 fw-light">Servicios Agendados</p>
                                <h2 class="fs-1 fw-bold mb-0">{{ $stats['scheduledServices'] }}</h2>
                            </div>
                            <div class="icon-wrapper d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px; background-color: var(--ensek-green-dark);">
                                <i class="fas fa-calendar-check fs-3 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Servicios completados -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body position-relative p-0">
                    <div class="position-absolute w-100" style="height: 4px; background-color: var(--ensek-green-light); top: 0;"></div>
                    <div class="px-4 pt-4 pb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex flex-column">
                                <p class="text-muted mb-1 fw-light">Servicios Completados</p>
                                <h2 class="fs-1 fw-bold mb-0">{{ $stats['completedServices'] }}</h2>
                            </div>
                            <div class="icon-wrapper d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px; background-color: var(--ensek-green-light);">
                                <i class="fas fa-check-circle fs-3 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ítems de inventario con bajo stock -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body position-relative p-0">
                    <div class="position-absolute w-100" style="height: 4px; background-color: #e74a3b; top: 0;"></div>
                    <div class="px-4 pt-4 pb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex flex-column">
                                <p class="text-muted mb-1 fw-light">Ítems con Bajo Stock</p>
                                <h2 class="fs-1 fw-bold mb-0">{{ $stats['lowStockItems'] }}</h2>
                            </div>
                            <div class="icon-wrapper d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px; background-color: #e74a3b;">
                                <i class="fas fa-exclamation-triangle fs-3 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row my-5">
        <!-- Solicitudes recientes -->
        <div class="col-md-6">
            <h3 class="fs-5 fw-bold mb-3">Solicitudes de servicio recientes</h3>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <table class="table bg-white table-hover mb-0">
                        <thead>
                            <tr>
                                <th scope="col" class="fw-semibold border-bottom-0">#</th>
                                <th scope="col" class="fw-semibold border-bottom-0">Cliente</th>
                                <th scope="col" class="fw-semibold border-bottom-0">Servicio</th>
                                <th scope="col" class="fw-semibold border-bottom-0">Estado</th>
                                <th scope="col" class="fw-semibold border-bottom-0">Fecha</th>
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
                                            <span class="badge text-bg-warning rounded-pill">Pendiente</span>
                                        @elseif($request->status == 'agendado')
                                            <span class="badge rounded-pill" style="background-color: var(--ensek-green-dark);">Agendado</span>
                                        @elseif($request->status == 'completado')
                                            <span class="badge rounded-pill" style="background-color: var(--ensek-green-light);">Completado</span>
                                        @else
                                            <span class="badge text-bg-danger rounded-pill">Cancelado</span>
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
            <h3 class="fs-5 fw-bold mb-3">Próximos servicios agendados</h3>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <table class="table bg-white table-hover mb-0">
                        <thead>
                            <tr>
                                <th scope="col" class="fw-semibold border-bottom-0">Fecha</th>
                                <th scope="col" class="fw-semibold border-bottom-0">Cliente</th>
                                <th scope="col" class="fw-semibold border-bottom-0">Técnico</th>
                                <th scope="col" class="fw-semibold border-bottom-0">Estado</th>
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
                                            <span class="badge text-bg-warning rounded-pill">Pendiente</span>
                                        @elseif($schedule->status == 'en proceso')
                                            <span class="badge rounded-pill" style="background-color: var(--ensek-green-dark);">En proceso</span>
                                        @elseif($schedule->status == 'completado')
                                            <span class="badge rounded-pill" style="background-color: var(--ensek-green-light);">Completado</span>
                                        @else
                                            <span class="badge text-bg-danger rounded-pill">Cancelado</span>
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
