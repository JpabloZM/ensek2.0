<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\Service;
use App\Mail\AppointmentCreated;
use App\Mail\AppointmentConfirmation;
use App\Mail\AppointmentReminder;
use App\Mail\AppointmentRescheduled;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['technician', 'serviceRequest.service', 'serviceRequest.client'])
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        $technicians = User::whereHas('role', function($query) {
            $query->where('name', 'Técnico');
        })->get();

        $pendingRequests = ServiceRequest::whereDoesntHave('appointment')
            ->where('status', 'pending')
            ->with(['client', 'service'])
            ->get();

        return view('appointments.index', compact('appointments', 'technicians', 'pendingRequests'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_request_id' => 'required|exists:service_requests,id',
            'technician_id' => 'required|exists:users,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Convertir a objetos Carbon para manipulación de fechas
        $appointmentDate = Carbon::parse($request->date);
        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);
        
        // Combinar fecha con hora para comparaciones
        $startDateTime = Carbon::parse($request->date . ' ' . $request->start_time);
        $endDateTime = Carbon::parse($request->date . ' ' . $request->end_time);

        // Verificar conflictos de agenda para el técnico
        $conflictingAppointments = $this->checkForConflicts(
            $request->technician_id, 
            $appointmentDate->toDateString(), 
            $startTime->format('H:i:s'), 
            $endTime->format('H:i:s')
        );

        if ($conflictingAppointments->count() > 0) {
            return redirect()->back()
                ->with('error', 'El técnico ya tiene una cita programada en ese horario.')
                ->withInput();
        }

        // Obtener la solicitud de servicio
        $serviceRequest = ServiceRequest::find($request->service_request_id);
        
        // Crear la cita
        $appointment = new Appointment();
        $appointment->service_request_id = $request->service_request_id;
        $appointment->technician_id = $request->technician_id;
        $appointment->date = $appointmentDate;
        $appointment->start_time = $startTime;
        $appointment->end_time = $endTime;
        $appointment->notes = $request->notes;
        $appointment->status = 'scheduled';
        $appointment->save();

        // Actualizar el estado de la solicitud de servicio
        $serviceRequest->status = 'scheduled';
        $serviceRequest->save();

        // Enviar notificaciones por email
        $this->sendAppointmentNotifications($appointment);

        return redirect()->route('appointments.index')
            ->with('success', 'La cita ha sido programada exitosamente y se han enviado las notificaciones correspondientes.');
    }

    public function edit(Appointment $appointment)
    {
        $technicians = User::whereHas('role', function($query) {
            $query->where('name', 'Técnico');
        })->get();

        $serviceRequest = $appointment->serviceRequest()->with('client', 'service')->first();

        return view('appointments.edit', compact('appointment', 'technicians', 'serviceRequest'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validator = Validator::make($request->all(), [
            'technician_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'notes' => 'nullable|string',
            'status' => 'required|in:scheduled,completed,cancelled,rescheduled',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error' => 'Datos inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Convertir a objetos Carbon para manipulación de fechas
        $appointmentDate = Carbon::parse($request->date);
        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);

        // Verificar conflictos solo si cambió la fecha/hora o el técnico
        if ($appointment->technician_id != $request->technician_id || 
            $appointment->date->format('Y-m-d') != $appointmentDate->format('Y-m-d') || 
            $appointment->start_time->format('H:i:s') != $startTime->format('H:i:s') || 
            $appointment->end_time->format('H:i:s') != $endTime->format('H:i:s')) {
            
            $conflictingAppointments = $this->checkForConflicts(
                $request->technician_id, 
                $appointmentDate->toDateString(), 
                $startTime->format('H:i:s'), 
                $endTime->format('H:i:s'),
                $appointment->id
            );

            if ($conflictingAppointments->count() > 0) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'error' => 'El técnico ya tiene una cita programada en ese horario.',
                        'conflicts' => $conflictingAppointments
                    ], 409); // Código 409 Conflict
                }
                
                return redirect()->back()
                    ->with('error', 'El técnico ya tiene una cita programada en ese horario.')
                    ->withInput();
            }
        }

        // Verificar si cambió el estado para enviar notificaciones adecuadas
        $statusChanged = $appointment->status !== $request->status;
        $wasRescheduled = $statusChanged && $request->status === 'rescheduled';
        
        // Actualizar la cita
        $appointment->technician_id = $request->technician_id;
        $appointment->date = $appointmentDate;
        $appointment->start_time = $startTime;
        $appointment->end_time = $endTime;
        $appointment->notes = isset($request->notes) ? $request->notes : $appointment->notes; // Mantener las notas si no se proporcionan
        $appointment->status = $request->status;
        $appointment->save();

        // Actualizar el estado de la solicitud de servicio según el estado de la cita
        $serviceRequest = $appointment->serviceRequest;
        if ($request->status === 'completed') {
            $serviceRequest->status = 'completed';
        } elseif ($request->status === 'cancelled') {
            $serviceRequest->status = 'pending'; // Volver a pendiente para reprogramar
        } elseif ($request->status === 'scheduled' || $request->status === 'rescheduled') {
            $serviceRequest->status = 'scheduled';
        }
        $serviceRequest->save();

        // Enviar notificaciones si la cita fue reprogramada
        if ($wasRescheduled) {
            $this->sendRescheduledNotifications($appointment);
        }
        
        // Responder en formato JSON si es una petición AJAX
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'La cita ha sido actualizada exitosamente.',
                'appointment' => $appointment
            ]);
        }

        return redirect()->route('appointments.index')
            ->with('success', 'La cita ha sido actualizada exitosamente.');
    }

    public function destroy(Appointment $appointment)
    {
        // Actualizar el estado de la solicitud de servicio a pendiente
        $serviceRequest = $appointment->serviceRequest;
        $serviceRequest->status = 'pending';
        $serviceRequest->save();

        // Eliminar la cita
        $appointment->delete();

        return redirect()->route('appointments.index')
            ->with('success', 'La cita ha sido eliminada exitosamente.');
    }

    public function getCalendarData(Request $request)
    {
        $query = Appointment::with(['technician', 'serviceRequest.service', 'serviceRequest.client']);
        
        // Aplicar filtros si existen
        if ($request->has('technician_id') && $request->technician_id) {
            $query->where('technician_id', $request->technician_id);
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('confirmation') && $request->confirmation) {
            $query->where('confirmation_status', $request->confirmation);
        }
        
        $appointments = $query->get();

        $events = [];

        foreach ($appointments as $appointment) {
            $startDateTime = Carbon::parse($appointment->date->format('Y-m-d') . ' ' . $appointment->start_time->format('H:i:s'));
            $endDateTime = Carbon::parse($appointment->date->format('Y-m-d') . ' ' . $appointment->end_time->format('H:i:s'));
            
            // Determinar el color según el estado
            $color = '#3498db'; // Azul por defecto
            if ($appointment->status === 'completed') {
                $color = '#2ecc71'; // Verde
            } elseif ($appointment->status === 'cancelled') {
                $color = '#e74c3c'; // Rojo
            } elseif ($appointment->status === 'rescheduled') {
                $color = '#f39c12'; // Naranja
            }

            $events[] = [
                'id' => $appointment->id,
                'title' => $appointment->serviceRequest->service->name . ' - ' . $appointment->serviceRequest->client->name,
                'start' => $startDateTime->toIso8601String(),
                'end' => $endDateTime->toIso8601String(),
                'color' => $color,
                'extendedProps' => [
                    'technician' => $appointment->technician->name,
                    'technician_id' => $appointment->technician_id, // Añadir ID del técnico para la vista de recursos
                    'client' => $appointment->serviceRequest->client->name,
                    'service' => $appointment->serviceRequest->service->name,
                    'address' => $appointment->serviceRequest->address,
                    'status' => $appointment->status,
                    'confirmation_status' => $appointment->confirmation_status,
                    'notes' => $appointment->notes,
                ]
            ];
        }

        return response()->json($events);
    }

    public function calendar()
    {
        $technicians = User::whereHas('role', function($query) {
            $query->where('name', 'Técnico');
        })->get();

        return view('appointments.calendar', compact('technicians'));
    }

    /**
     * Sugiere técnicos disponibles y adecuados para una solicitud de servicio específica
     */
    public function suggestTechnicians(Request $request)
    {
        $serviceRequestId = $request->input('service_request_id');
        $date = $request->input('date');
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');

        if (!$serviceRequestId || !$date || !$startTime || !$endTime) {
            return response()->json(['error' => 'Datos incompletos'], 400);
        }

        // Obtener la solicitud de servicio
        $serviceRequest = ServiceRequest::with('service')->find($serviceRequestId);
        if (!$serviceRequest) {
            return response()->json(['error' => 'Solicitud de servicio no encontrada'], 404);
        }

        // Buscar técnicos disponibles en ese horario y que tengan la especialidad adecuada
        $availableTechnicians = $this->findAvailableTechnicians(
            $serviceRequest->service->id,
            $date,
            $startTime,
            $endTime
        );

        return response()->json([
            'technicians' => $availableTechnicians,
            'recommended' => count($availableTechnicians) > 0 ? $availableTechnicians[0]->id : null
        ]);
    }

    /**
     * Encuentra técnicos disponibles para un servicio en un horario específico
     */
    private function findAvailableTechnicians($serviceId, $date, $startTime, $endTime)
    {
        // 1. Obtener todos los técnicos
        $technicians = User::whereHas('role', function($query) {
            $query->where('name', 'Técnico');
        })->get();

        // 2. Filtrar por especialidad para este servicio
        $service = Service::find($serviceId);
        $specializedTechnicians = [];
        $generalTechnicians = [];

        foreach ($technicians as $technician) {
            // Verificar si tiene la especialidad adecuada (esto requiere una tabla de especialidades por técnico)
            // En este caso estoy simulando la lógica - deberías adaptarla a tu estructura de datos
            $hasSpeciality = DB::table('technician_specialities')
                ->where('user_id', $technician->id)
                ->where('service_id', $serviceId)
                ->exists();
            
            if ($hasSpeciality) {
                $specializedTechnicians[] = $technician;
            } else {
                $generalTechnicians[] = $technician;
            }
        }

        // 3. Verificar disponibilidad en el horario indicado
        $availableSpecializedTechnicians = [];
        $availableGeneralTechnicians = [];

        // Primero verificamos los especializados
        foreach ($specializedTechnicians as $technician) {
            $conflicts = $this->checkForConflicts(
                $technician->id,
                $date,
                $startTime,
                $endTime
            );

            if ($conflicts->count() === 0) {
                // También verificamos la carga de trabajo del técnico
                $workload = Appointment::where('technician_id', $technician->id)
                    ->whereDate('date', '=', $date)
                    ->count();
                
                $technician->workload = $workload;
                $availableSpecializedTechnicians[] = $technician;
            }
        }

        // Luego verificamos los generales si es necesario
        if (count($availableSpecializedTechnicians) < 3) {
            foreach ($generalTechnicians as $technician) {
                $conflicts = $this->checkForConflicts(
                    $technician->id,
                    $date,
                    $startTime,
                    $endTime
                );

                if ($conflicts->count() === 0) {
                    // También verificamos la carga de trabajo del técnico
                    $workload = Appointment::where('technician_id', $technician->id)
                        ->whereDate('date', '=', $date)
                        ->count();
                    
                    $technician->workload = $workload;
                    $availableGeneralTechnicians[] = $technician;
                }
            }
        }

        // 4. Ordenar por especialización y carga de trabajo
        $availableSpecializedTechnicians = collect($availableSpecializedTechnicians)
            ->sortBy('workload')
            ->values()
            ->all();
            
        $availableGeneralTechnicians = collect($availableGeneralTechnicians)
            ->sortBy('workload')
            ->values()
            ->all();

        // 5. Combinar los resultados (primero los especializados)
        return array_merge($availableSpecializedTechnicians, $availableGeneralTechnicians);
    }

    /**
     * Verifica si hay conflictos de horario para un técnico específico
     */
    private function checkForConflicts($technicianId, $date, $startTime, $endTime, $excludeAppointmentId = null)
    {
        $query = Appointment::where('technician_id', $technicianId)
            ->whereDate('date', '=', $date)
            ->where(function($query) use ($startTime, $endTime) {
                // Citas que comienzan durante la cita propuesta
                $query->whereBetween('start_time', [$startTime, $endTime])
                    // Citas que terminan durante la cita propuesta
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    // Citas que abarcan completamente la cita propuesta
                    ->orWhere(function($query) use ($startTime, $endTime) {
                        $query->where('start_time', '<=', $startTime)
                              ->where('end_time', '>=', $endTime);
                    });
            })
            ->where('status', '!=', 'cancelled');

        // Excluir la cita actual en caso de edición
        if ($excludeAppointmentId) {
            $query->where('id', '!=', $excludeAppointmentId);
        }

        return $query->get();
    }

    /**
     * Enviar notificaciones por email cuando se crea una cita
     */
    private function sendAppointmentNotifications(Appointment $appointment)
    {
        $serviceRequest = $appointment->serviceRequest;
        $client = $serviceRequest->client;
        $technician = $appointment->technician;
        
        // Notificación para el cliente
        if ($client->email) {
            Mail::to($client->email)->send(new AppointmentCreated($appointment));
        }
        
        // Notificación para el técnico
        if ($technician->email) {
            Mail::to($technician->email)->send(new AppointmentCreated($appointment, true));
        }
    }

    /**
     * Enviar notificaciones por email cuando se reprograma una cita
     */
    private function sendRescheduledNotifications(Appointment $appointment)
    {
        $serviceRequest = $appointment->serviceRequest;
        $client = $serviceRequest->client;
        $technician = $appointment->technician;
        
        // Notificación para el cliente
        if ($client->email) {
            Mail::to($client->email)->send(new AppointmentRescheduled($appointment));
        }
        
        // Notificación para el técnico
        if ($technician->email) {
            Mail::to($technician->email)->send(new AppointmentRescheduled($appointment, true));
        }
    }
    
    /**
     * Enviar recordatorio de cita
     */
    public function sendReminders()
    {
        // Obtener citas para mañana
        $tomorrow = Carbon::tomorrow();
        $appointments = Appointment::with(['serviceRequest.client', 'technician'])
            ->whereDate('date', $tomorrow)
            ->where('status', 'scheduled')
            ->get();
            
        foreach ($appointments as $appointment) {
            $client = $appointment->serviceRequest->client;
            $technician = $appointment->technician;
            
            // Enviar recordatorio al cliente
            if ($client->email) {
                Mail::to($client->email)->send(new AppointmentReminder($appointment));
            }
            
            // Enviar recordatorio al técnico
            if ($technician->email) {
                Mail::to($technician->email)->send(new AppointmentReminder($appointment, true));
            }
        }
        
        return response()->json(['message' => 'Recordatorios enviados: ' . $appointments->count()]);
    }
    
    /**
     * Permite a un cliente confirmar su cita
     */
    public function confirmAppointment($id, $token)
    {
        $appointment = Appointment::find($id);
        
        if (!$appointment) {
            return redirect()->route('public.landing')
                ->with('error', 'La cita no existe.');
        }
        
        // Verificar el token (deberías implementar una tabla de tokens o usar hash para esto)
        $validToken = hash('sha256', $appointment->id . $appointment->created_at);
        
        if ($token !== $validToken) {
            return redirect()->route('public.landing')
                ->with('error', 'El enlace de confirmación no es válido.');
        }
        
        // Actualizar el estado de la cita a confirmada
        $appointment->confirmation_status = 'confirmed';
        $appointment->save();
        
        return redirect()->route('public.landing')
            ->with('success', 'Su cita ha sido confirmada. Gracias!');
    }
    
    /**
     * Verifica si hay conflictos de horario para un técnico
     */
    public function checkConflicts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'technician_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Datos inválidos', 'errors' => $validator->errors()], 422);
        }

        // Obtener conflictos
        $conflicts = $this->checkForConflicts(
            $request->technician_id,
            $request->date,
            $request->start_time,
            $request->end_time,
            $request->appointment_id ?? null
        );

        // Preparar respuesta con detalles de los conflictos
        $conflictDetails = [];
        foreach ($conflicts as $conflict) {
            $conflictDetails[] = [
                'id' => $conflict->id,
                'service_name' => $conflict->serviceRequest->service->name,
                'client_name' => $conflict->serviceRequest->client->name,
                'date' => $conflict->date->format('Y-m-d'),
                'start_time' => $conflict->start_time->format('h:i A'),
                'end_time' => $conflict->end_time->format('h:i A'),
            ];
        }

        return response()->json([
            'has_conflicts' => $conflicts->count() > 0,
            'conflicts' => $conflictDetails
        ]);
    }
    
    /**
     * Obtiene todos los técnicos para la vista del calendario
     * Incluye información sobre especialidades
     */
    public function getTechnicians()
    {
        $technicians = User::whereHas('role', function($query) {
            $query->where('name', 'Técnico');
        })
        ->with(['technician.specialtyService', 'technician.skills'])
        ->get();
        
        $formattedTechnicians = $technicians->map(function($tech) {
            $specialtyName = $tech->technician && $tech->technician->specialtyService 
                ? $tech->technician->specialtyService->name 
                : 'General';
                
            $skills = $tech->technician && $tech->technician->skills 
                ? $tech->technician->skills->pluck('name')->implode(', ') 
                : '';
                
            return [
                'id' => $tech->id,
                'name' => $tech->name,
                'title' => $specialtyName, // Para mostrar la especialidad como título
                'specialty' => $specialtyName,
                'skills' => $skills,
                'eventColor' => $this->getTechnicianColor($specialtyName) // Color basado en la especialidad
            ];
        });
        
        return response()->json($formattedTechnicians);
    }
    
    /**
     * Obtiene un color basado en la especialidad del técnico
     * para diferenciar visualmente en el calendario
     */
    private function getTechnicianColor($specialty)
    {
        // Mapeo de especialidades a colores
        $specialtyColors = [
            'Electricidad' => '#1E88E5', // Azul
            'Plomería' => '#43A047',     // Verde
            'Climatización' => '#E53935', // Rojo
            'Carpintería' => '#8D6E63',  // Marrón
            'Albañilería' => '#757575',  // Gris
            'Pintura' => '#FB8C00',      // Naranja
            'Jardinería' => '#7CB342',   // Verde claro
            'Cerrajería' => '#5E35B1',   // Púrpura
            'Informática' => '#00ACC1',  // Cian
            'Electrónica' => '#F9A825',  // Amarillo
        ];
        
        return $specialtyColors[$specialty] ?? '#546E7A'; // Color por defecto para especialidades no mapeadas
    }
}