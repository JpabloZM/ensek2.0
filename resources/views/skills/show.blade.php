@extends('layouts.admin')

@push('styles')
<style>
    /* Clases predefinidas para niveles de habilidad */
    .proficiency-bar-1 { width: 20%; }
    .proficiency-bar-2 { width: 40%; }
    .proficiency-bar-3 { width: 60%; }
    .proficiency-bar-4 { width: 80%; }
    .proficiency-bar-5 { width: 100%; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Detalle de la Habilidad</h6>
                    <div>
                        <a href="{{ route('admin.skills.edit', $skill->id) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('admin.skills.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold text-primary">Información de la Habilidad</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>ID</th>
                                            <td>{{ $skill->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nombre</th>
                                            <td>{{ $skill->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Categoría</th>
                                            <td>
                                                @switch($skill->category)
                                                    @case('technical')
                                                        Técnica
                                                        @break
                                                    @case('software')
                                                        Software
                                                        @break
                                                    @case('hardware')
                                                        Hardware
                                                        @break
                                                    @case('networking')
                                                        Redes
                                                        @break
                                                    @case('security')
                                                        Seguridad
                                                        @break
                                                    @case('cloud')
                                                        Cloud
                                                        @break
                                                    @case('certification')
                                                        Certificaciones
                                                        @break
                                                    @case('soft_skills')
                                                        Habilidades Blandas
                                                        @break
                                                    @case('other')
                                                        Otra
                                                        @break
                                                    @default
                                                        {{ $skill->category ?? 'No especificada' }}
                                                @endswitch
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Descripción</th>
                                            <td>{{ $skill->description ?? 'No hay descripción disponible' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Fecha de Creación</th>
                                            <td>{{ $skill->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Última Actualización</th>
                                            <td>{{ $skill->updated_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold text-primary">Técnicos con esta Habilidad</h6>
                                </div>
                                <div class="card-body">
                                    @if($skill->technicians->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Nombre</th>
                                                        <th>Nivel</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($skill->technicians as $technician)
                                                        <tr>
                                                            <td>{{ $technician->user->name }}</td>
                                                            <td>
                                                                <div class="progress">
                                                                    <div class="progress-bar bg-success proficiency-bar-{{ $technician->pivot->proficiency_level }}" 
                                                                         role="progressbar" 
                                                                         aria-valuenow="{{ $technician->pivot->proficiency_level }}" 
                                                                         aria-valuemin="0" 
                                                                         aria-valuemax="5">
                                                                        {{ $technician->pivot->proficiency_level }}/5
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('admin.technicians.show', $technician->id) }}" class="btn btn-sm btn-info">
                                                                    <i class="fas fa-eye"></i> Ver
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-center">No hay técnicos con esta habilidad.</p>
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
