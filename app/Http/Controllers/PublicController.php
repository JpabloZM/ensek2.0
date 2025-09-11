<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Mail\NewServiceRequest;
use App\Models\User;

class PublicController extends Controller
{
    /**
     * Display the landing page with service request form
     */
    public function index()
    {
        $services = Service::where('active', true)->get();
        return view('public.landing', compact('services'));
    }

    /**
     * Store a new service request from public form
     */
    public function storeServiceRequest(Request $request)
    {
        // Registrar todos los datos recibidos
        Log::info('Solicitud recibida', [
            'método' => $request->method(),
            'todos los datos' => $request->all()
        ]);
        
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Debe iniciar sesión para solicitar un servicio.');
        }
        
        // Validar datos de manera explícita
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
            'description' => 'required|string|min:10',
            'address' => 'required|string|max:255',
        ], [
            'service_id.required' => 'Por favor seleccione un servicio.',
            'service_id.exists' => 'El servicio seleccionado no es válido.',
            'description.required' => 'La descripción del problema es obligatoria.',
            'description.min' => 'La descripción debe tener al menos 10 caracteres.',
            'address.required' => 'La dirección es obligatoria.',
        ]);
        
        // Si la validación falla, redirigir con los errores
        if ($validator->fails()) {
            Log::warning('Validación fallida', [
                'errores' => $validator->errors()->toArray()
            ]);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            // Obtener usuario autenticado
            $user = Auth::user();
            
            // Crear la solicitud de servicio
            $serviceRequest = new ServiceRequest();
            $serviceRequest->service_id = $request->service_id;
            $serviceRequest->user_id = $user->id;
            $serviceRequest->description = $request->description;
            $serviceRequest->address = $request->address;
            $serviceRequest->client_name = $user->name;
            $serviceRequest->client_email = $user->email;
            $serviceRequest->client_phone = $user->phone ?? '(Sin teléfono)';
            $serviceRequest->status = 'pendiente';
            $serviceRequest->save();
            
            Log::info('Solicitud de servicio creada', ['id' => $serviceRequest->id]);
            
            // Intentar enviar notificaciones
            try {
                $this->notifyAdmins($serviceRequest);
            } catch (\Exception $e) {
                Log::error('Error en notificaciones: ' . $e->getMessage());
                // Continuar con el flujo normal
            }
            
            // Redirigir a página de agradecimiento
            return redirect()->route('public.thank-you');
            
        } catch (\Exception $e) {
            Log::error('Error guardando solicitud: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error_general' => 'Ocurrió un error al procesar su solicitud: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Display thank you page after successful service request
     */
    public function thankYou()
    {
        Log::info('Usuario redirigido a la página de agradecimiento');
        return view('public.thank-you');
    }
    
    /**
     * Send notification emails to all admin users
     */
    private function notifyAdmins($serviceRequest)
    {
        // Obtener todos los usuarios administradores
        $admins = User::whereHas('role', function ($query) {
            $query->where('name', 'Administrador');
        })->get();
        
        // Enviar correo a cada administrador
        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new NewServiceRequest($serviceRequest));
        }
    }
}
