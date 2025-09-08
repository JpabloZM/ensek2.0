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
                    <h6 class="m-0 font-weight-bold text-primary">Detalle del Técnico</h6>
                    <div>
                        <a href="{{ route('admin.technicians.edit', $technician->id) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('admin.technicians.availability', $technician->id) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-calendar-alt"></i> Disponibilidad
                        </a>
                        <a href="{{ route('admin.technicians.skills', $technician->id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-tools"></i> Habilidades
                        </a>
                        <a href="{{ route('admin.technicians.time-off', $technician->id) }}" class="btn btn-sm btn-success">
                            <i class="fas fa-calendar-minus"></i> Tiempo Libre
                        </a>
                        <a href="{{ route('admin.technicians.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center mb-4">
                            <div class="card border-left-primary shadow py-2">
                                <div class="card-body">
                                    <div class="text-center mb-2">
                                        @if($technician->profile_image)
                                            <img src="{{ asset('storage/' . $technician->profile_image) }}" alt="{{ $technician->user->name }}" class="img-profile rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                                        @else
                                            <img src="{{ asset('img/undraw_profile.svg') }}" alt="{{ $technician->user->name }}" class="img-profile rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                                        @endif
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $technician->user->name }}</div>
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mt-1 mb-2">
                                        {{ $technician->title ?? 'Técnico' }}
                                    </div>
                                    <div class="text-gray-600 mb-1">{{ $technician->user->email }}</div>
                                    <div class="text-gray-600 mb-1">{{ $technician->user->phone ?? 'No registrado' }}</div>
                                    <div class="mt-2">
                                        @if($technician->active)
                                            <span class="badge badge-success badge-lg">Activo</span>
                                        @else
                                            <span class="badge badge-danger badge-lg">Inactivo</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-9">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Información del Técnico</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th>Especialidad</th>
                                                    <td>{{ $technician->specialtyService ? $technician->specialtyService->name : 'Sin especialidad' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Tipo de Empleo</th>
                                                    <td>
                                                        @switch($technician->employment_type)
                                                            @case('full_time')
                                                                Tiempo completo
                                                                @break
                                                            @case('part_time')
                                                                Medio tiempo
                                                                @break
                                                            @case('contractor')
                                                                Contratista
                                                                @break
                                                            @case('on_call')
                                                                Por llamado
                                                                @break
                                                            @default
                                                                {{ $technician->employment_type ?? 'No definido' }}
                                                        @endswitch
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Fecha de Contratación</th>
                                                    <td>{{ $technician->hire_date ? $technician->hire_date->format('d/m/Y') : 'No registrada' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Años de Experiencia</th>
                                                    <td>{{ $technician->years_experience ?? '0' }} años</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            @if($technician->bio)
                                                <div class="card border-left-info mb-3">
                                                    <div class="card-body">
                                                        <h6 class="font-weight-bold text-primary">Biografía</h6>
                                                        <p class="mb-0">{{ $technician->bio }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if($technician->certifications)
                                                <div class="card border-left-success">
                                                    <div class="card-body">
                                                        <h6 class="font-weight-bold text-primary">Certificaciones</h6>
                                                        <ul class="mb-0">
                                                            @foreach($technician->certifications as $cert)
                                                                <li>{{ $cert }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold text-primary">Habilidades</h6>
                                    <a href="{{ route('admin.technicians.skills', $technician->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Gestionar
                                    </a>
                                </div>
                                <div class="card-body">
                                    @if($technician->skills->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Habilidad</th>
                                                        <th>Categoría</th>
                                                        <th>Nivel</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($technician->skills as $skill)
                                                        <tr>
                                                            <td>{{ $skill->name }}</td>
                                                            <td>{{ $skill->category ?? 'N/A' }}</td>
                                                            <td>
                                                                <div class="progress">
                                                                    <div class="progress-bar bg-success proficiency-bar-{{ $skill->pivot->proficiency_level }}" 
                                                                         role="progressbar" 
                                                                         aria-valuenow="{{ $skill->pivot->proficiency_level }}" 
                                                                         aria-valuemin="0" 
                                                                         aria-valuemax="5">
                                                                        {{ $skill->pivot->proficiency_level }}/5
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-center">No hay habilidades registradas para este técnico.</p>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold text-primary">Disponibilidad</h6>
                                    <a href="{{ route('admin.technicians.availability', $technician->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i> Gestionar
                                    </a>
                                </div>
                                <div class="card-body">
                                    @if($technician->availabilities->count() > 0)
                                        @php
                                            $daysOfWeek = [
                                                0 => 'Domingo',
                                                1 => 'Lunes',
                                                2 => 'Martes',
                                                3 => 'Miércoles',
                                                4 => 'Jueves',
                                                5 => 'Viernes',
                                                6 => 'Sábado'
                                            ];
                                            
                                            // Organize availabilities by day
                                            $availabilitiesByDay = [];
                                            foreach($technician->availabilities as $availability) {
                                                $availabilitiesByDay[$availability->day_of_week][] = $availability;
                                            }
                                        @endphp
                                        
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Día</th>
                                                        <th>Horarios</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($daysOfWeek as $dayNum => $dayName)
                                                        <tr>
                                                            <td>{{ $dayName }}</td>
                                                            <td>
                                                                @if(isset($availabilitiesByDay[$dayNum]) && count($availabilitiesByDay[$dayNum]) > 0)
                                                                    @foreach($availabilitiesByDay[$dayNum] as $availability)
                                                                        <div class="mb-1">
                                                                            @if($availability->is_available)
                                                                                <span class="badge badge-success">
                                                                                    {{ substr($availability->start_time, 0, 5) }} - {{ substr($availability->end_time, 0, 5) }}
                                                                                </span>
                                                                            @else
                                                                                <span class="badge badge-danger">
                                                                                    {{ substr($availability->start_time, 0, 5) }} - {{ substr($availability->end_time, 0, 5) }}
                                                                                    (No disponible)
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    @endforeach
                                                                @else
                                                                    <span class="badge badge-secondary">No definido</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-center">No hay disponibilidad registrada para este técnico.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold text-primary">Tiempo Libre</h6>
                                    <a href="{{ route('admin.technicians.time-off', $technician->id) }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-edit"></i> Gestionar
                                    </a>
                                </div>
                                <div class="card-body">
                                    @if($technician->timeOffRequests->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Desde</th>
                                                        <th>Hasta</th>
                                                        <th>Motivo</th>
                                                        <th>Estado</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($technician->timeOffRequests->sortByDesc('start_date') as $timeOff)
                                                        <tr>
                                                            <td>{{ $timeOff->start_date->format('d/m/Y') }}</td>
                                                            <td>{{ $timeOff->end_date->format('d/m/Y') }}</td>
                                                            <td>
                                                                @switch($timeOff->reason)
                                                                    @case('vacation')
                                                                        Vacaciones
                                                                        @break
                                                                    @case('sick_leave')
                                                                        Baja por enfermedad
                                                                        @break
                                                                    @case('personal')
                                                                        Asunto personal
                                                                        @break
                                                                    @case('training')
                                                                        Capacitación/Formación
                                                                        @break
                                                                    @default
                                                                        {{ $timeOff->reason }}
                                                                @endswitch
                                                            </td>
                                                            <td>
                                                                @switch($timeOff->status)
                                                                    @case('requested')
                                                                        <span class="badge badge-warning">Solicitado</span>
                                                                        @break
                                                                    @case('approved')
                                                                        <span class="badge badge-success">Aprobado</span>
                                                                        @break
                                                                    @case('denied')
                                                                        <span class="badge badge-danger">Denegado</span>
                                                                        @break
                                                                    @default
                                                                        <span class="badge badge-secondary">{{ $timeOff->status }}</span>
                                                                @endswitch
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-center">No hay registros de tiempo libre para este técnico.</p>
                                    @endif
                                </div>
                            </div>
                            
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
                                                    @foreach($technician->schedules->sortByDesc('scheduled_at') as $schedule)
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
