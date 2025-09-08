@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Solicitudes de Servicio</h6>
            <div>
                <a href="{{ route('admin.service-requests.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Nueva Solicitud
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Filtros -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <form action="{{ route('admin.service-requests.filter') }}" method="GET" class="form-inline">
                        <div class="input-group">
                            <select name="status" class="form-control">
                                <option value="todos" {{ request('status') == 'todos' ? 'selected' : '' }}>Todos los estados</option>
                                <option value="pendiente" {{ request('status') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="agendado" {{ request('status') == 'agendado' ? 'selected' : '' }}>Agendado</option>
                                <option value="completado" {{ request('status') == 'completado' ? 'selected' : '' }}>Completado</option>
                                <option value="cancelado" {{ request('status') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">Filtrar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Tabla de Solicitudes (usando nuestra configuración robusta de DataTables) -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover datatable-table" id="dt-table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Servicio</th>
                            <th>Contacto</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($serviceRequests as $request)
                            <tr>
                                <td>{{ $request->id }}</td>
                                <td>{{ $request->client_name ?? 'N/A' }}</td>
                                <td>{{ $request->service->name ?? 'N/A' }}</td>
                                <td>
                                    {{ $request->client_phone ?? 'N/A' }}<br>
                                    {{ $request->client_email ?? 'N/A' }}
                                </td>
                                <td>
                                    @php
                                        $statusClasses = [
                                            'pendiente' => 'warning',
                                            'agendado' => 'info',
                                            'completado' => 'success',
                                            'cancelado' => 'danger'
                                        ];
                                        $statusClass = $statusClasses[$request->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge badge-{{ $statusClass }} badge-lg">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td>{{ $request->created_at ? $request->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.service-requests.show', $request->id) }}" class="btn btn-sm btn-info" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <a href="{{ route('admin.service-requests.edit', $request->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        @if($request->status == 'pendiente')
                                            <a href="{{ route('admin.schedules.create', ['service_request_id' => $request->id]) }}" class="btn btn-sm btn-primary" title="Agendar">
                                                <i class="fas fa-calendar-plus"></i>
                                            </a>
                                        @endif
                                        
                                        @if($request->status != 'agendado' && $request->status != 'completado')
                                            <form action="{{ route('admin.service-requests.destroy', $request->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Cancelar" onclick="return confirm('¿Está seguro que desea cancelar esta solicitud?')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No hay solicitudes de servicio disponibles</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación nativa de Laravel -->
            <div class="mt-4">
                {{ $serviceRequests->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    console.log('La funcionalidad de DataTables se maneja globalmente desde datatables-custom.js');
</script>
@endpush
