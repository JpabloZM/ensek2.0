@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Técnicos</h6>
            <a href="{{ route('admin.technicians.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Agregar Técnico
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered datatable-table" id="dt-table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Especialidad</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($technicians as $technician)
                            <tr>
                                <td>{{ $technician->id }}</td>
                                <td>{{ $technician->user->name }}</td>
                                <td>{{ $technician->user->email }}</td>
                                <td>{{ $technician->specialtyService ? $technician->specialtyService->name : 'Sin especialidad' }}</td>
                                <td>
                                    @if($technician->active)
                                        <span class="badge badge-success badge-lg">Activo</span>
                                    @else
                                        <span class="badge badge-danger badge-lg">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.technicians.edit', $technician->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.technicians.show', $technician->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.technicians.destroy', $technician->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro que desea eliminar este técnico?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
            }
        });
    });
</script>
@endpush

@endsection
