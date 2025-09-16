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
        
        // Datos para el mapa de calor (solicitudes de los últimos 3 meses)
        $heatmapData = $this->getHeatmapData();
        
        // Datos para la comparación mensual de servicios
        $monthlyComparison = $this->getMonthlyServicesComparison();
        
        return view('dashboard', compact('stats', 'recentRequests', 'upcomingSchedules', 'heatmapData', 'monthlyComparison'));
    }
    
    /**
     * Obtener datos comparativos de servicios del mes actual vs mes anterior
     * Incluye datos de prueba para visualizar mejor la comparación
     *
     * @return array
     */
    private function getMonthlyServicesComparison()
    {
        // Mes actual
        $currentMonthStart = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();
        
        // Mes anterior
        $prevMonthStart = now()->subMonth()->startOfMonth();
        $prevMonthEnd = now()->subMonth()->endOfMonth();
        
        try {
            // Conteo de servicios completados por día para el mes actual
            $currentMonthServices = Schedule::where('status', 'completado')
                ->whereBetween('scheduled_date', [$currentMonthStart, $currentMonthEnd])
                ->selectRaw('DATE(scheduled_date) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->pluck('count', 'date')
                ->toArray();
                
            // Conteo de servicios completados por día para el mes anterior
            $prevMonthServices = Schedule::where('status', 'completado')
                ->whereBetween('scheduled_date', [$prevMonthStart, $prevMonthEnd])
                ->selectRaw('DATE(scheduled_date) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->pluck('count', 'date')
                ->toArray();
                
            // Si no hay suficientes datos reales, generar datos de prueba
            if (count($currentMonthServices) < 10 || count($prevMonthServices) < 10) {
                $useTestData = true;
            } else {
                $useTestData = false;
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al obtener datos de servicios mensuales: ' . $e->getMessage());
            $useTestData = true;
        }
        
        // Si no hay suficientes datos o hubo un error, usar datos de prueba
        if ($useTestData) {
            // Generar datos de prueba para el mes actual
            $currentMonthServices = $this->generateTestMonthlyData(true);
            
            // Generar datos de prueba para el mes anterior (con un patrón ligeramente diferente)
            $prevMonthServices = $this->generateTestMonthlyData(false);
        }
        
        // Total de servicios para cada mes
        $currentMonthTotal = array_sum($currentMonthServices);
        $prevMonthTotal = array_sum($prevMonthServices);
        
        // Crecimiento porcentual
        $growthPercentage = 0;
        if ($prevMonthTotal > 0) {
            $growthPercentage = (($currentMonthTotal - $prevMonthTotal) / $prevMonthTotal) * 100;
        }
        
        // Nombres de los meses en español
        $mesesEspañol = [
            'January' => 'Enero',
            'February' => 'Febrero',
            'March' => 'Marzo',
            'April' => 'Abril',
            'May' => 'Mayo',
            'June' => 'Junio',
            'July' => 'Julio',
            'August' => 'Agosto',
            'September' => 'Septiembre',
            'October' => 'Octubre',
            'November' => 'Noviembre',
            'December' => 'Diciembre'
        ];
        
        $currentMonthName = $mesesEspañol[now()->format('F')] ?? now()->format('F');
        $prevMonthName = $mesesEspañol[now()->subMonth()->format('F')] ?? now()->subMonth()->format('F');
        
        return [
            'currentMonth' => [
                'name' => $currentMonthName,
                'total' => $currentMonthTotal,
                'dailyData' => $currentMonthServices
            ],
            'prevMonth' => [
                'name' => $prevMonthName,
                'total' => $prevMonthTotal,
                'dailyData' => $prevMonthServices
            ],
            'growthPercentage' => $growthPercentage,
            'hasGrowth' => $growthPercentage >= 0,
            'isTestData' => $useTestData
        ];
    }
    
    /**
     * Genera datos de prueba para la visualización del gráfico de comparación mensual
     *
     * @param bool $isCurrentMonth Si es true, genera datos para el mes actual, si es false, para el mes anterior
     * @return array
     */
    private function generateTestMonthlyData($isCurrentMonth = true)
    {
        $data = [];
        $daysInMonth = 30; // Asumimos un mes de 30 días para simplificar
        
        // Definir una tendencia base basada en días de la semana
        // Con más servicios los días de semana y menos los fines de semana
        $weekdayTrend = [5, 6, 7, 8, 7, 3, 2]; // Lun, Mar, Mié, Jue, Vie, Sáb, Dom
        
        // Definir factores de variación para los meses
        $currentMonthFactor = 1.2; // 20% más servicios en el mes actual
        $prevMonthFactor = 0.9;    // 10% menos servicios en el mes anterior
        
        // Definir una tendencia creciente a lo largo del mes
        $growthTrend = $isCurrentMonth ? 0.05 : 0.02; // 5% de crecimiento por día en el mes actual, 2% en el anterior
        
        // Generar datos para cada día del mes
        for ($day = 1; $day <= $daysInMonth; $day++) {
            // Simular el día de la semana (0-6, donde 0 es lunes)
            $weekday = ($day % 7);
            
            // Usar la tendencia base según el día de la semana
            $baseValue = $weekdayTrend[$weekday];
            
            // Aplicar el factor del mes
            $monthFactor = $isCurrentMonth ? $currentMonthFactor : $prevMonthFactor;
            
            // Aplicar tendencia creciente a lo largo del mes
            $dayGrowthFactor = 1 + ($growthTrend * $day);
            
            // Añadir algo de variación aleatoria (±20%)
            $randomVariation = mt_rand(80, 120) / 100;
            
            // Calcular el valor final para este día
            $value = round($baseValue * $monthFactor * $dayGrowthFactor * $randomVariation);
            
            // Para que sea más realista, algunos días pueden no tener servicios
            if (mt_rand(1, 100) <= 5) { // 5% de probabilidad de día sin servicios
                $value = 0;
            }
            
            // Crear la fecha en formato YYYY-MM-DD
            $month = $isCurrentMonth ? now()->format('m') : now()->subMonth()->format('m');
            $year = $isCurrentMonth ? now()->format('Y') : now()->subMonth()->format('Y');
            $date = sprintf('%s-%s-%02d', $year, $month, $day);
            
            $data[$date] = max(0, $value); // Asegurar que no hay valores negativos
        }
        
        return $data;
    }
    
    /**
     * Obtiene datos geográficos para el mapa de calor
     * 
     * @return array
     */
    private function getHeatmapData()
    {
        // En un entorno real, estas coordenadas vendrían de la geocodificación de las direcciones
        // de las solicitudes de servicio. Para este ejemplo, usamos datos ficticios alrededor de Rionegro, Antioquia.
        
        // Coordenadas base para Rionegro, Antioquia
        $baseLatitude = 6.1528; // Latitud aproximada de Rionegro
        $baseLongitude = -75.3750; // Longitud aproximada de Rionegro
        
        $data = [];
        
        // Obtenemos todas las solicitudes de los últimos 3 meses
        $requests = ServiceRequest::where('created_at', '>=', now()->subMonths(3))
            ->get();
            
        try {
            // Si no hay suficientes datos reales, generamos algunos puntos para demostración
            if ($requests->count() < 5) {
                // Generamos 20 puntos aleatorios cercanos a la ubicación base
                for ($i = 0; $i < 20; $i++) {
                    // Generamos variaciones aleatorias pequeñas para crear puntos cercanos
                    $latVariation = (mt_rand(-100, 100) / 1000); // Variación de ±0.1
                    $lngVariation = (mt_rand(-100, 100) / 1000); // Variación de ±0.1
                    
                    // Más peso (intensidad) para algunos puntos para simular áreas con más demanda
                    $weight = mt_rand(1, 10);
                    
                    // Usamos floatval para asegurar que son números, no strings
                    $data[] = [
                        'lat' => floatval($baseLatitude + $latVariation),
                        'lng' => floatval($baseLongitude + $lngVariation),
                        'weight' => floatval($weight),
                    ];
                }
            } else {
                // Aquí procesaríamos datos reales si los tuviéramos
                foreach ($requests as $request) {
                    // Nota: En un sistema real, las coordenadas vendrían de una tabla de geocodificación
                    // o se calcularían en tiempo real a partir de la dirección almacenada
                    
                    // Por ahora, simplemente generamos coordenadas aleatorias cerca de la ubicación base
                    $latVariation = (mt_rand(-100, 100) / 1000);
                    $lngVariation = (mt_rand(-100, 100) / 1000);
                    
                    // Usamos floatval para asegurar que son números, no strings
                    $data[] = [
                        'lat' => floatval($baseLatitude + $latVariation),
                        'lng' => floatval($baseLongitude + $lngVariation),
                        'weight' => 1, // En un sistema real, este peso podría depender de la frecuencia
                    ];
                }
            }
        } catch (\Exception $e) {
            // Si hay algún error, registrarlo y devolver un array vacío
            // Usar el facade de Log con el import correcto
            \Illuminate\Support\Facades\Log::error('Error al generar datos para el mapa de calor: ' . $e->getMessage());
            $data = [];
        }
        
        return $data;
    }
}
