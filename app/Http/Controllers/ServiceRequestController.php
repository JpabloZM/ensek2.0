<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Mail\NewServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ServiceRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $serviceRequests = ServiceRequest::with(['service', 'user', 'schedule'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('service-requests.index', compact('serviceRequests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $services = Service::where('active', true)->get();
        return view('service-requests.create', compact('services'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'client_email' => 'nullable|email|max:255',
            'description' => 'required|string',
            'address' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);
        
        $serviceRequest = new ServiceRequest($validated);
        $serviceRequest->user_id = Auth::id();
        $serviceRequest->status = 'pendiente';
        $serviceRequest->save();
        
        // Notificar a los administradores si la solicitud es creada desde el panel admin
        $this->notifyAdminsOfNewRequest($serviceRequest);
        
        return redirect()->route('admin.service-requests.index')
            ->with('success', 'Solicitud de servicio creada correctamente.');
    }
    
    /**
     * Notify administrators of new service request
     */
    private function notifyAdminsOfNewRequest($serviceRequest)
    {
        // Si estamos en modo de desarrollo, no enviar correos
        if (app()->environment('local')) {
            Log::info('Nueva solicitud de servicio creada: ' . $serviceRequest->id);
            return;
        }
        
        // Obtener todos los usuarios administradores
        $admins = User::whereHas('role', function ($query) {
            $query->where('name', 'Administrador');
        })->get();
        
        // Enviar correo a cada administrador
        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new NewServiceRequest($serviceRequest));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $serviceRequest = ServiceRequest::with(['service', 'user', 'schedule.technician.user'])
            ->findOrFail($id);
        
        return view('service-requests.show', compact('serviceRequest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $serviceRequest = ServiceRequest::findOrFail($id);
        $services = Service::where('active', true)->get();
        
        return view('service-requests.edit', compact('serviceRequest', 'services'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $serviceRequest = ServiceRequest::findOrFail($id);
        
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'client_email' => 'nullable|email|max:255',
            'description' => 'required|string',
            'address' => 'required|string|max:255',
            'status' => 'required|in:pendiente,agendado,completado,cancelado',
            'notes' => 'nullable|string',
        ]);
        
        $serviceRequest->update($validated);
        
        return redirect()->route('admin.service-requests.show', $serviceRequest->id)
            ->with('success', 'Solicitud de servicio actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $serviceRequest = ServiceRequest::findOrFail($id);
        
        // Si la solicitud está agendada o completada, no se puede eliminar
        if ($serviceRequest->status == 'agendado' || $serviceRequest->status == 'completado') {
            return back()->with('error', 'No se puede eliminar una solicitud que ya está agendada o completada.');
        }
        
        // Si tiene un agendamiento, lo cancelamos
        if ($serviceRequest->schedule) {
            $serviceRequest->schedule->status = 'cancelado';
            $serviceRequest->schedule->save();
        }
        
        // Cancelamos la solicitud en lugar de eliminarla
        $serviceRequest->status = 'cancelado';
        $serviceRequest->save();
        
        return redirect()->route('admin.service-requests.index')
            ->with('success', 'Solicitud de servicio cancelada correctamente.');
    }
    
    /**
     * Filter requests by status
     */
    public function filter(Request $request)
    {
        $status = $request->status;
        $query = ServiceRequest::with(['service', 'user', 'schedule']);
        
        if ($status && $status != 'todos') {
            $query->where('status', $status);
        }
        
        $serviceRequests = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('service-requests.index', compact('serviceRequests'));
    }
}
