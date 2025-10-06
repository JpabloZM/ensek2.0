<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\ServiceRequest;
use App\Models\Service;
use App\Models\Technician;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DirectScheduleController extends Controller
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
     * Almacena un nuevo agendamiento directo sin solicitud previa.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validar datos
        $validated = $request->validate([
            'client_name' => 'required|string|max:100',
            'client_phone' => 'required|string|max:20',
            'client_email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:255',
            'service_id' => 'required|exists:services,id',
            'description' => 'required|string|max:1000',
            'technician_id' => 'required|exists:technicians,id',
            'scheduled_date' => 'required|date',
            'end_time' => 'required|date_format:H:i',
            'duration' => 'nullable|integer|min:15',
            'notes' => 'nullable|string|max:500',
            'status' => 'nullable|string|in:pendiente,en proceso,completado,cancelado',
            'estimated_cost' => 'nullable|numeric|min:0',
        ]);

        // Usar transacción para garantizar la integridad de los datos
        DB::beginTransaction();
        
        try {
            // 1. Crear primero la solicitud de servicio
            $serviceRequest = new ServiceRequest();
            $serviceRequest->service_id = $validated['service_id'];
            $serviceRequest->user_id = Auth::id(); // Usuario que crea la solicitud
            $serviceRequest->client_name = $validated['client_name'];
            $serviceRequest->client_phone = $validated['client_phone'];
            $serviceRequest->client_email = $validated['client_email'] ?? null;
            $serviceRequest->description = $validated['description'];
            $serviceRequest->address = $validated['address'] ?? null;
            $serviceRequest->status = 'agendado'; // Directamente agendado
            $serviceRequest->save();

            // 2. Convertir scheduled_date y end_time a objetos DateTime
            $scheduledDate = new \DateTime($validated['scheduled_date']);
            
            // Extraer hora y minutos de end_time
            [$endHour, $endMinute] = explode(':', $validated['end_time']);
            
            // Crear objeto DateTime para la hora de finalización
            $endDate = clone $scheduledDate;
            $endDate->setTime((int)$endHour, (int)$endMinute);
            
            // Calcular duración en minutos
            $durationInMinutes = ($endDate->getTimestamp() - $scheduledDate->getTimestamp()) / 60;
            
            // 3. Crear el agendamiento
            $schedule = new Schedule();
            $schedule->service_request_id = $serviceRequest->id;
            $schedule->technician_id = $validated['technician_id'];
            $schedule->scheduled_date = $validated['scheduled_date'];
            $schedule->duration = $durationInMinutes;
            $schedule->status = $validated['status'] ?? 'pendiente';
            $schedule->notes = $validated['notes'] ?? null;
            
            if (isset($validated['estimated_cost']) && $validated['estimated_cost'] > 0) {
                $schedule->estimated_cost = $validated['estimated_cost'];
            }
            
            // Establecer confirmación inicial como pendiente
            $schedule->confirmation_status = 'pending';
            
            $schedule->save();

            DB::commit();
            
            return redirect()->route('admin.schedules.index')
                ->with('success', 'Agendamiento creado correctamente desde cero.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el agendamiento: ' . $e->getMessage());
        }
    }
}