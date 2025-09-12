<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\ServiceRequest;
use App\Models\Technician;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ScheduleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Mostrar los agendamientos asignados a un técnico.
     */
    public function assigned()
    {
        // Obtener el técnico actual
        $technician = Technician::where('user_id', Auth::id())->firstOrFail();
        
        // Obtener sus agendamientos
        $schedules = Schedule::with(['serviceRequest', 'serviceRequest.service'])
            ->where('technician_id', $technician->id)
            ->orderBy('scheduled_date')
            ->paginate(10);
            
        return view('technician.schedules', compact('schedules'));
    }
    
    /**
     * Actualizar el estado de un agendamiento.
     */
    public function updateStatus(Request $request, Schedule $schedule)
    {
        $request->validate([
            'status' => 'required|in:pendiente,en proceso,completado,cancelado',
        ]);
        
        // Verificar que el técnico es el asignado al agendamiento
        $technician = Technician::where('user_id', Auth::id())->firstOrFail();
        
        if ($schedule->technician_id != $technician->id) {
            return back()->with('error', 'No tienes permisos para actualizar este agendamiento.');
        }
        
        $schedule->status = $request->status;
        
        // Si se marca como completado, guardar la fecha de finalización
        if ($request->status == 'completado') {
            $schedule->completed_at = now();
        }
        
        $schedule->save();
        
        return back()->with('success', 'Estado del agendamiento actualizado correctamente.');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener todos los técnicos activos
        $technicians = Technician::where('active', true)
            ->with('user')
            ->get();
            
        // Obtener solicitudes pendientes para nuevo agendamiento
        $pendingRequests = ServiceRequest::where('status', 'pendiente')
            ->with('service')
            ->get();
            
        // Obtener todos los servicios para el modal de agregar técnicos
        $services = \App\Models\Service::where('active', true)->get();
            
        // Definir colores para especialidades
        $specialtyColors = [
            1 => '#2C3E50', // Electricidad
            2 => '#3498DB', // Fontanería
            3 => '#E74C3C', // Carpintería
            4 => '#F39C12', // Pintura
            5 => '#27AE60', // Jardinería
            6 => '#8E44AD', // Albañilería
            7 => '#D35400', // Cerrajería
        ];
        
        // Preparar datos de recursos para el calendario con colores por especialidad
        $resourcesJson = $technicians->map(function ($technician) use ($specialtyColors) {
            $specialty = $technician->specialty ?? 0;
            $color = $specialtyColors[$specialty] ?? '#95A5A6'; // Color por defecto
            
            return [
                'id' => $technician->id,
                'title' => $technician->user->name,
                'extendedProps' => [
                    'color' => $color,
                    'specialty' => $specialty,
                    'phone' => $technician->user->phone,
                    'email' => $technician->user->email,
                ]
            ];
        });
        
        // Obtener todos los agendamientos
        $schedules = Schedule::with(['serviceRequest.service', 'technician.user'])
            ->get();
            
        // Preparar datos de eventos para el calendario
        $eventsJson = $schedules->map(function ($schedule) {
            $start = $schedule->scheduled_date->format('Y-m-d\TH:i:s');
            // Asignar un tiempo de duración estimado según el servicio (por defecto 1 hora)
            $end = $schedule->scheduled_date->addHours(1)->format('Y-m-d\TH:i:s');
            
            return [
                'id' => $schedule->id,
                'resourceId' => $schedule->technician_id,
                'title' => $schedule->serviceRequest->service->name . ' - ' . $schedule->serviceRequest->client_name,
                'start' => $start,
                'end' => $end,
                'status' => $schedule->status,
                'extendedProps' => [
                    'status' => $schedule->status,
                    'clientName' => $schedule->serviceRequest->client_name,
                    'serviceId' => $schedule->serviceRequest->service_id,
                    'serviceName' => $schedule->serviceRequest->service->name
                ]
            ];
        });
        
        return view('schedules.index', compact('technicians', 'pendingRequests', 'resourcesJson', 'eventsJson', 'services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Obtener técnicos activos
        $technicians = Technician::where('active', true)
            ->with('user')
            ->get();
            
        // Obtener solicitudes pendientes
        $pendingRequests = ServiceRequest::where('status', 'pendiente')
            ->with('service')
            ->get();
        
        // Si se especifica una solicitud específica
        $selectedRequest = null;
        if ($request->has('service_request_id')) {
            $selectedRequest = ServiceRequest::with('service')->findOrFail($request->service_request_id);
        }
            
        return view('schedules.create', compact('technicians', 'pendingRequests', 'selectedRequest'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_request_id' => 'required|exists:service_requests,id',
            'technician_id' => 'required|exists:technicians,id',
            'scheduled_date' => 'required|date',
            'status' => 'required|in:pendiente,en proceso',
            'notes' => 'nullable|string|max:500'
        ]);
        
        // Crear el agendamiento
        $schedule = new Schedule();
        $schedule->service_request_id = $validated['service_request_id'];
        $schedule->technician_id = $validated['technician_id'];
        $schedule->scheduled_date = $validated['scheduled_date'];
        $schedule->status = $validated['status'];
        $schedule->notes = $validated['notes'];
        $schedule->save();
        
        // Actualizar el estado de la solicitud a "agendado"
        $serviceRequest = ServiceRequest::find($validated['service_request_id']);
        $serviceRequest->status = 'agendado';
        $serviceRequest->save();
        
        return redirect()->route('admin.schedules.index')
            ->with('success', 'Agendamiento creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $schedule = Schedule::with(['serviceRequest.service', 'technician.user'])
            ->findOrFail($id);
            
        // Si la solicitud es AJAX, devolver JSON
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'schedule' => $schedule
            ]);
        }
        
        return view('schedules.show', compact('schedule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $schedule = Schedule::with(['serviceRequest.service', 'technician.user'])
            ->findOrFail($id);
            
        // Obtener técnicos activos
        $technicians = Technician::where('active', true)
            ->with('user')
            ->get();
            
        // Obtener todas las solicitudes de servicio (incluida la que ya está asignada)
        $serviceRequests = ServiceRequest::with('service')->get();
        
        return view('schedules.edit', compact('schedule', 'technicians', 'serviceRequests'));
    }

    /**
     * Update schedule duration.
     */
    public function updateDuration(Request $request, string $id)
    {
        $schedule = Schedule::findOrFail($id);
        
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);
        
        try {
            // Actualizar la fecha de inicio y calcular la duración
            $startDate = new \DateTime($validated['start_date']);
            $endDate = new \DateTime($validated['end_date']);
            
            // Calcular la duración en minutos
            $duration = $startDate->diff($endDate);
            $durationMinutes = ($duration->h * 60) + $duration->i;
            
            // Actualizar el agendamiento
            $schedule->scheduled_date = $startDate;
            $schedule->duration = $durationMinutes;
            $schedule->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Duración actualizada correctamente.',
                'schedule' => $schedule
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la duración: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $schedule = Schedule::findOrFail($id);
        
        // Si la solicitud es AJAX (drag & drop en calendario)
        if ($request->ajax()) {
            $schedule->technician_id = $request->technician_id;
            $schedule->scheduled_date = $request->scheduled_date;
            $schedule->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Agendamiento actualizado correctamente.'
            ]);
        }
        
        // Validación para formulario normal
        $validated = $request->validate([
            'service_request_id' => 'sometimes|required|exists:service_requests,id',
            'technician_id' => 'sometimes|required|exists:technicians,id',
            'scheduled_date' => 'sometimes|required|date',
            'status' => 'required|in:pendiente,en proceso,completado,cancelado',
            'notes' => 'nullable|string|max:500'
        ]);
        
        // Actualizar campos solo si no está completado o si no se envían
        if ($schedule->status != 'completado' || !isset($validated['service_request_id'])) {
            if (isset($validated['service_request_id'])) {
                $schedule->service_request_id = $validated['service_request_id'];
            }
            
            if (isset($validated['technician_id'])) {
                $schedule->technician_id = $validated['technician_id'];
            }
            
            if (isset($validated['scheduled_date'])) {
                $schedule->scheduled_date = $validated['scheduled_date'];
            }
        }
        
        // Siempre se puede actualizar el estado y las notas
        $schedule->status = $validated['status'];
        $schedule->notes = $validated['notes'] ?? $schedule->notes;
        
        // Si se cambia a completado, registrar la fecha de finalización
        if ($validated['status'] == 'completado' && $schedule->status != 'completado') {
            $schedule->completed_at = now();
            
            // Actualizar el estado de la solicitud a "completado"
            $serviceRequest = ServiceRequest::find($schedule->service_request_id);
            $serviceRequest->status = 'completado';
            $serviceRequest->save();
        }
        
        // Si se cancela, actualizar el estado de la solicitud
        if ($validated['status'] == 'cancelado' && $schedule->status != 'cancelado') {
            $serviceRequest = ServiceRequest::find($schedule->service_request_id);
            $serviceRequest->status = 'pendiente'; // Vuelve a estar pendiente para reagendar
            $serviceRequest->save();
        }
        
        $schedule->save();
        
        return redirect()->route('schedules.show', $schedule->id)
            ->with('success', 'Agendamiento actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $schedule = Schedule::findOrFail($id);
        
        // Actualizar el estado de la solicitud a "pendiente"
        $serviceRequest = ServiceRequest::find($schedule->service_request_id);
        if ($serviceRequest && $serviceRequest->status == 'agendado') {
            $serviceRequest->status = 'pendiente';
            $serviceRequest->save();
        }
        
        $schedule->delete();
        
        return redirect()->route('schedules.index')
            ->with('success', 'Agendamiento eliminado correctamente.');
    }
    
    /**
     * Obtener datos para el calendario con soporte para filtros
     */
    public function getCalendarData(Request $request)
    {
        $query = Schedule::with(['serviceRequest.service', 'technician.user']);
        
        // Aplicar filtros si existen
        if ($request->has('technician_id') && $request->technician_id) {
            $query->where('technician_id', $request->technician_id);
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Solo aplicar filtro de confirmación si la columna existe
        if ($request->has('confirmation') && $request->confirmation && Schema::hasColumn('schedules', 'confirmation_status')) {
            $query->where('confirmation_status', $request->confirmation);
        }
        
        // Si se proporcionan fechas de inicio y fin, filtrar por rango
        if ($request->has('start') && $request->has('end')) {
            $query->whereBetween('scheduled_date', [$request->start, $request->end]);
        }
        
        $schedules = $query->get();
        
        // Preparar datos para el calendario
        $events = $schedules->map(function ($schedule) {
            $start = $schedule->scheduled_date->format('Y-m-d\TH:i:s');
            
            // Calcular la hora de finalización (por defecto 1 hora si no hay duración)
            $duration = $schedule->duration ?? 60; // minutos
            $end = $schedule->scheduled_date->addMinutes($duration)->format('Y-m-d\TH:i:s');
            
            // Determinar el color según el estado
            $color = '#3498db'; // Azul por defecto (pendiente)
            switch ($schedule->status) {
                case 'en proceso':
                    $color = '#f39c12'; // Naranja
                    break;
                case 'completado':
                    $color = '#2ecc71'; // Verde
                    break;
                case 'cancelado':
                    $color = '#e74c3c'; // Rojo
                    break;
            }
            
            return [
                'id' => $schedule->id,
                'resourceId' => $schedule->technician_id,
                'title' => $schedule->serviceRequest->service->name . ' - ' . $schedule->serviceRequest->client_name,
                'start' => $start,
                'end' => $end,
                'color' => $color,
                'extendedProps' => [
                    'status' => $schedule->status,
                    'confirmation_status' => Schema::hasColumn('schedules', 'confirmation_status') ? ($schedule->confirmation_status ?? 'pending') : 'pending',
                    'clientName' => $schedule->serviceRequest->client_name,
                    'serviceId' => $schedule->serviceRequest->service_id,
                    'serviceName' => $schedule->serviceRequest->service->name,
                    'address' => $schedule->serviceRequest->address,
                    'notes' => $schedule->notes
                ]
            ];
        });
        
        return response()->json($events);
    }
}
