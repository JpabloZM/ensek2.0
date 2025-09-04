<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\Schedule;
use App\Models\ServiceRequest;
use App\Models\Technician;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Crear una nueva instancia de controlador.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Mostrar el dashboard con estadísticas.
     */
    public function index()
    {
        // Estadísticas para el dashboard
        $stats = [
            'pendingRequests' => ServiceRequest::where('status', 'pendiente')->count(),
            'scheduledServices' => Schedule::where('status', 'pendiente')->count(),
            'completedServices' => Schedule::where('status', 'completado')->count(),
            'technicians' => Technician::where('active', true)->count(),
            'lowStockItems' => InventoryItem::whereRaw('quantity <= minimum_stock')->count(),
        ];
        
        // Solicitudes de servicio recientes
        $recentRequests = ServiceRequest::with('service')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Próximos servicios agendados
        $upcomingSchedules = Schedule::with(['serviceRequest', 'technician.user'])
            ->where('status', 'pendiente')
            ->where('scheduled_date', '>=', now())
            ->orderBy('scheduled_date', 'asc')
            ->take(5)
            ->get();
        
        return view('dashboard', compact('stats', 'recentRequests', 'upcomingSchedules'));
    }
}
