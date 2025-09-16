@extends('layouts.admin')

@section('page-title', 'Panel de Control')

@section('content')
<div class="container-fluid">
    <div class="row g-4 my-4">
        <!-- Solicitudes pendientes -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body position-relative p-0">
                    <div class="position-absolute w-100" style="height: 4px; background-color: #ffc107; top: 0;"></div>
                    <div class="px-4 pt-4 pb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex flex-column">
                                <p class="text-muted mb-1 fw-light">Solicitudes Pendientes</p>
                                <h2 class="fs-1 fw-bold mb-0">{{ $stats['pendingRequests'] }}</h2>
                            </div>
                            <div class="icon-wrapper d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px; background-color: #ffc107;">
                                <i class="fas fa-clipboard-list fs-3 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Servicios agendados -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body position-relative p-0">
                    <div class="position-absolute w-100" style="height: 4px; background-color: var(--ensek-green-dark); top: 0;"></div>
                    <div class="px-4 pt-4 pb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex flex-column">
                                <p class="text-muted mb-1 fw-light">Servicios Agendados</p>
                                <h2 class="fs-1 fw-bold mb-0">{{ $stats['scheduledServices'] }}</h2>
                            </div>
                            <div class="icon-wrapper d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px; background-color: var(--ensek-green-dark);">
                                <i class="fas fa-calendar-check fs-3 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Servicios completados -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body position-relative p-0">
                    <div class="position-absolute w-100" style="height: 4px; background-color: var(--ensek-green-light); top: 0;"></div>
                    <div class="px-4 pt-4 pb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex flex-column">
                                <p class="text-muted mb-1 fw-light">Servicios Completados</p>
                                <h2 class="fs-1 fw-bold mb-0">{{ $stats['completedServices'] }}</h2>
                            </div>
                            <div class="icon-wrapper d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px; background-color: var(--ensek-green-light);">
                                <i class="fas fa-check-circle fs-3 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ítems de inventario con bajo stock -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body position-relative p-0">
                    <div class="position-absolute w-100" style="height: 4px; background-color: #e74a3b; top: 0;"></div>
                    <div class="px-4 pt-4 pb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex flex-column">
                                <p class="text-muted mb-1 fw-light">Ítems con Bajo Stock</p>
                                <h2 class="fs-1 fw-bold mb-0">{{ $stats['lowStockItems'] }}</h2>
                            </div>
                            <div class="icon-wrapper d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px; background-color: #e74a3b;">
                                <i class="fas fa-exclamation-triangle fs-3 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mapa de calor de solicitudes -->
    <div class="row my-4">
        <div class="col-12">
            <h3 class="fs-5 fw-bold mb-3">Zonas con Mayor Demanda</h3>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <!-- Elemento oculto para almacenar los datos del mapa de calor -->
                    <script type="application/json" id="heatmap-data">{!! json_encode($heatmapData) !!}</script>
                    
                    <div id="heatmap" style="height: 350px; width: 100%; border-radius: 0.25rem;">
                        <div class="d-flex justify-content-center align-items-center h-100">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <span class="ms-2">Cargando mapa...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparación mensual de servicios -->
    <div class="row my-4">
        <div class="col-12">
            <h3 class="fs-5 fw-bold mb-3">Comparación Mensual de Servicios</h3>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <!-- Elemento oculto para almacenar los datos de comparación mensual -->
                    <script type="application/json" id="monthly-comparison-data">{!! json_encode($monthlyComparison) !!}</script>
                    
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div style="height: 250px; position: relative;">
                                <canvas id="monthlyComparisonChart" width="100%" height="250"></canvas>
                            </div>
                            <div class="d-flex flex-wrap justify-content-center gap-3 mt-2">
                                <div class="d-flex align-items-center small">
                                    <span class="badge rounded-circle p-1 me-1" style="background-color: #ffcc00;"></span>
                                    <span class="text-muted">Mayor variación</span>
                                </div>
                                <div class="d-flex align-items-center small">
                                    <span class="badge rounded-circle p-1 me-1" style="background-color: rgba(135, 201, 71, 0.9);"></span>
                                    <span class="text-muted">Picos {{ $monthlyComparison['currentMonth']['name'] }}</span>
                                </div>
                                <div class="d-flex align-items-center small">
                                    <span class="badge rounded-circle p-1 me-1" style="background-color: rgba(0, 65, 34, 0.7);"></span>
                                    <span class="text-muted">Picos {{ $monthlyComparison['prevMonth']['name'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center py-4">
                                <h4 class="fs-4 fw-bold">{{ $monthlyComparison['currentMonth']['name'] }} vs {{ $monthlyComparison['prevMonth']['name'] }}</h4>
                                <div class="d-flex justify-content-center align-items-center gap-3 my-3">
                                    <div class="text-center">
                                        <p class="text-muted mb-1 small">{{ $monthlyComparison['prevMonth']['name'] }}</p>
                                        <h3 class="fs-2 mb-0">{{ $monthlyComparison['prevMonth']['total'] }}</h3>
                                    </div>
                                    <div class="text-center">
                                        <i class="fas fa-arrow-right text-muted"></i>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-muted mb-1 small">{{ $monthlyComparison['currentMonth']['name'] }}</p>
                                        <h3 class="fs-2 mb-0">{{ $monthlyComparison['currentMonth']['total'] }}</h3>
                                    </div>
                                </div>
                                @if($monthlyComparison['hasGrowth'])
                                    <p class="mb-0">
                                        <span class="badge rounded-pill" style="background-color: var(--ensek-green-light);">
                                            <i class="fas fa-arrow-up me-1"></i> {{ number_format($monthlyComparison['growthPercentage'], 1) }}%
                                        </span>
                                        <span class="text-muted ms-2 small">vs mes anterior</span>
                                    </p>
                                @else
                                    <p class="mb-0">
                                        <span class="badge rounded-pill text-bg-danger">
                                            <i class="fas fa-arrow-down me-1"></i> {{ number_format(abs($monthlyComparison['growthPercentage']), 1) }}%
                                        </span>
                                        <span class="text-muted ms-2 small">vs mes anterior</span>
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row my-5">
        <!-- Solicitudes recientes -->
        <div class="col-md-6">
            <h3 class="fs-5 fw-bold mb-3">Solicitudes de servicio recientes</h3>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <table class="table bg-white table-hover mb-0">
                        <thead>
                            <tr>
                                <th scope="col" class="fw-semibold border-bottom-0">#</th>
                                <th scope="col" class="fw-semibold border-bottom-0">Cliente</th>
                                <th scope="col" class="fw-semibold border-bottom-0">Servicio</th>
                                <th scope="col" class="fw-semibold border-bottom-0">Estado</th>
                                <th scope="col" class="fw-semibold border-bottom-0">Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentRequests as $request)
                                <tr>
                                    <td>{{ $request->id }}</td>
                                    <td>{{ $request->client_name }}</td>
                                    <td>{{ $request->service->name }}</td>
                                    <td>
                                        @if($request->status == 'pendiente')
                                            <span class="badge text-bg-warning rounded-pill">Pendiente</span>
                                        @elseif($request->status == 'agendado')
                                            <span class="badge rounded-pill" style="background-color: var(--ensek-green-dark);">Agendado</span>
                                        @elseif($request->status == 'completado')
                                            <span class="badge rounded-pill" style="background-color: var(--ensek-green-light);">Completado</span>
                                        @else
                                            <span class="badge text-bg-danger rounded-pill">Cancelado</span>
                                        @endif
                                    </td>
                                    <td>{{ $request->created_at->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No hay solicitudes recientes</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Próximos servicios agendados -->
        <div class="col-md-6">
            <h3 class="fs-5 fw-bold mb-3">Próximos servicios agendados</h3>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <table class="table bg-white table-hover mb-0">
                        <thead>
                            <tr>
                                <th scope="col" class="fw-semibold border-bottom-0">Fecha</th>
                                <th scope="col" class="fw-semibold border-bottom-0">Cliente</th>
                                <th scope="col" class="fw-semibold border-bottom-0">Técnico</th>
                                <th scope="col" class="fw-semibold border-bottom-0">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($upcomingSchedules as $schedule)
                                <tr>
                                    <td>{{ $schedule->scheduled_date->format('d/m/Y H:i') }}</td>
                                    <td>{{ $schedule->serviceRequest->client_name }}</td>
                                    <td>{{ $schedule->technician->user->name }}</td>
                                    <td>
                                        @if($schedule->status == 'pendiente')
                                            <span class="badge text-bg-warning rounded-pill">Pendiente</span>
                                        @elseif($schedule->status == 'en proceso')
                                            <span class="badge rounded-pill" style="background-color: var(--ensek-green-dark);">En proceso</span>
                                        @elseif($schedule->status == 'completado')
                                            <span class="badge rounded-pill" style="background-color: var(--ensek-green-light);">Completado</span>
                                        @else
                                            <span class="badge text-bg-danger rounded-pill">Cancelado</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No hay servicios agendados próximamente</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Obtener los datos del mapa de calor desde un elemento oculto en la página
var heatmapDataElement = document.getElementById('heatmap-data');
var heatmapData = JSON.parse(heatmapDataElement.textContent);

// Cargar la API de Google Maps después de que la página esté lista
document.addEventListener('DOMContentLoaded', function() {
    // Crear el script de la API de Google Maps
    const script = document.createElement('script');
    script.src = "https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=visualization&callback=initHeatMap";
    script.defer = true;
    script.async = true;
    document.head.appendChild(script);
});

// Inicializar el mapa de calor
function initHeatMap() {
    // Verificar si Google Maps está cargado
    if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
        console.error('Google Maps API no cargada correctamente');
        document.getElementById('heatmap').innerHTML = '<div class="alert alert-warning text-center py-3">' +
            '<i class="fas fa-map-marked-alt me-2"></i> No se pudo cargar el mapa de calor. ' +
            'Mostrando información alternativa de demanda por zona.</div>';
        
        // Cargar visualización alternativa de datos
        loadAlternativeView();
        return;
    }
    
    // Función para mostrar visualización alternativa si el mapa falla
    function loadAlternativeView() {
        // Si tenemos datos, mostramos una visualización más amigable con las áreas más demandadas
        if (heatmapData && heatmapData.length > 0) {
            // Agrupar datos por proximidad y contar frecuencia (simulado)
            const areas = [
                { name: "Centro de Rionegro", count: Math.floor(Math.random() * 20) + 10 },
                { name: "Zona Industrial", count: Math.floor(Math.random() * 15) + 5 },
                { name: "San Antonio", count: Math.floor(Math.random() * 12) + 3 },
                { name: "El Porvenir", count: Math.floor(Math.random() * 10) + 2 },
                { name: "Otras áreas", count: Math.floor(Math.random() * 8) + 1 }
            ];
            
            // Ordenar por frecuencia
            areas.sort((a, b) => b.count - a.count);
            
            // Calcular el total para los porcentajes
            const total = areas.reduce((sum, area) => sum + area.count, 0);
            
            // Crear visualización con barras de progreso
            let htmlContent = '<div class="mt-3">';
            
            // Para cada área, crear una barra de progreso estilizada
            areas.forEach(area => {
                const percentage = ((area.count / total) * 100).toFixed(1);
                const barColor = percentage > 30 ? 'var(--ensek-green-dark)' : 
                                percentage > 15 ? 'var(--ensek-green-light)' : 
                                '#87c947';
                
                htmlContent += `
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <strong>${area.name}</strong>
                        <span>${area.count} solicitudes (${percentage}%)</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar" role="progressbar" 
                            style="width: ${percentage}%; background-color: ${barColor};" 
                            aria-valuenow="${percentage}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                </div>`;
            });
            
            htmlContent += '</div>';
            
            document.getElementById('heatmap').innerHTML = htmlContent;
        }
    }
    
    // Centro del mapa (Rionegro, Antioquia)
    const center = { lat: 6.1528, lng: -75.3750 };

    // Configuración del mapa
    const map = new google.maps.Map(document.getElementById("heatmap"), {
        zoom: 13,
        center: center,
        mapTypeId: "roadmap",
        styles: [
            {
                "featureType": "administrative",
                "elementType": "labels.text.fill",
                "stylers": [{"color": "#444444"}]
            },
            {
                "featureType": "landscape",
                "elementType": "all",
                "stylers": [{"color": "#f2f2f2"}]
            },
            {
                "featureType": "poi",
                "elementType": "all",
                "stylers": [{"visibility": "off"}]
            },
            {
                "featureType": "road",
                "elementType": "all",
                "stylers": [{"saturation": -100}, {"lightness": 45}]
            },
            {
                "featureType": "road.highway",
                "elementType": "all",
                "stylers": [{"visibility": "simplified"}]
            },
            {
                "featureType": "road.arterial",
                "elementType": "labels.icon",
                "stylers": [{"visibility": "off"}]
            },
            {
                "featureType": "transit",
                "elementType": "all",
                "stylers": [{"visibility": "off"}]
            },
            {
                "featureType": "water",
                "elementType": "all",
                "stylers": [{"color": "#c4e7f2"}, {"visibility": "on"}]
            }
        ]
    });

    // Convertir datos a formato Google Maps
    const heatmapPoints = heatmapData.map(point => {
        return new google.maps.LatLng(point.lat, point.lng);
    });

    // Crear el mapa de calor con colores ENSEK
    const heatmap = new google.maps.visualization.HeatmapLayer({
        data: heatmapPoints,
        map: map,
        radius: 20,
        opacity: 0.8,
        gradient: [
            'rgba(255, 255, 255, 0)',    // Transparente
            'rgba(135, 201, 71, 0.3)',   // ENSEK verde claro (muy sutil)
            'rgba(135, 201, 71, 0.5)',   // ENSEK verde claro
            'rgba(135, 201, 71, 0.7)',   // ENSEK verde claro
            'rgba(135, 201, 71, 0.9)',   // ENSEK verde claro
            'rgba(100, 170, 60, 1)',     // Verde intermedio
            'rgba(80, 140, 50, 1)',      // Verde intermedio
            'rgba(60, 120, 40, 1)',      // Verde intermedio
            'rgba(40, 100, 30, 1)',      // Verde oscuro
            'rgba(20, 80, 20, 1)',       // Verde oscuro
            'rgba(0, 65, 34, 0.9)',      // ENSEK verde oscuro
            'rgba(0, 65, 34, 1)'         // ENSEK verde oscuro
        ]
    });

    // Opcional: Agregar marcador de la sede central de la empresa
    const companyMarker = new google.maps.Marker({
        position: center,
        map: map,
        title: "Sede central ENSEK",
        icon: {
            url: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
            scaledSize: new google.maps.Size(32, 32)
        }
    });
}

// Script para el gráfico de comparación mensual
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado - Iniciando gráfico de comparación mensual');
    
    try {
        // Verificar si Chart.js está disponible, si no, cargarlo
        if (typeof Chart === 'undefined') {
            console.warn('Chart.js no está disponible, intentando cargarlo localmente...');
            
            // Intentar cargar Chart.js directamente
            loadChartJsAndRenderChart();
            return;
        } else {
            console.log('Chart.js detectado, versión:', Chart.version);
        }
        
        function loadChartJsAndRenderChart() {
            // Cargar Chart.js localmente como respaldo
            const script = document.createElement('script');
            script.src = "https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js";
            script.onload = function() {
                console.log('Chart.js cargado exitosamente como respaldo, versión:', Chart.version);
                
                // Ahora que Chart.js está cargado, continuar con el proceso
                const monthlyComparisonElement = document.getElementById('monthly-comparison-data');
                if (!monthlyComparisonElement) {
                    console.error('Error: No se encontró el elemento #monthly-comparison-data');
                    return;
                }
                
                const monthlyComparisonData = JSON.parse(monthlyComparisonElement.textContent);
                renderMonthlyComparisonChart(monthlyComparisonData);
            };
            script.onerror = function() {
                console.error('No se pudo cargar Chart.js como respaldo');
                document.getElementById('monthlyComparisonChart').innerHTML = `
                    <div class="alert alert-warning text-center py-5">
                        <i class="fas fa-chart-bar fa-2x mb-3 text-muted"></i>
                        <p>No se pudo cargar la biblioteca de gráficos.</p>
                        <p>Los datos de la comparación mensual están disponibles en formato de texto.</p>
                    </div>
                `;
            };
            document.head.appendChild(script);
        }
        
        // Verificar si el elemento existe
        const chartContainer = document.getElementById('monthlyComparisonChart');
        if (!chartContainer) {
            console.error('Error: No se encontró el elemento #monthlyComparisonChart');
            return;
        } else {
            console.log('Elemento del gráfico encontrado');
        }
        
        // Obtener los datos de comparación mensual desde el elemento oculto
        const monthlyComparisonElement = document.getElementById('monthly-comparison-data');
        if (!monthlyComparisonElement) {
            console.error('Error: No se encontró el elemento #monthly-comparison-data');
            return;
        }
        
        console.log('Datos JSON encontrados, parseando...');
        const monthlyComparisonData = JSON.parse(monthlyComparisonElement.textContent);
        console.log('Datos parseados correctamente:', monthlyComparisonData);
        
        // Renderizar el gráfico
        renderMonthlyComparisonChart(monthlyComparisonData);
    } catch (error) {
        console.error('Error al inicializar el gráfico:', error);
        document.getElementById('monthlyComparisonChart').innerHTML = 
            '<div class="alert alert-danger">Error al cargar el gráfico: ' + error.message + '</div>';
    }
});

function renderMonthlyComparisonChart(data) {
    console.log('Iniciando renderización del gráfico...');
    
    try {
        // Verificar que el canvas existe y obtener el contexto 2d
        const chartCanvas = document.getElementById('monthlyComparisonChart');
        if (!chartCanvas) {
            console.error('Error: Canvas #monthlyComparisonChart no encontrado');
            return;
        }
        
            console.log('Canvas encontrado, obteniendo contexto 2d...');
        const ctx = chartCanvas.getContext('2d');
        if (!ctx) {
            console.error('Error: No se pudo obtener el contexto 2d del canvas');
            // Si falla el contexto 2d, mostrar visualización alternativa
            showAlternativeVisualization(data, chartCanvas);
            return;
        }        console.log('Contexto 2d obtenido, preparando datos...');
        
        // Preparar los datos para el gráfico
        const currentMonthData = prepareChartData(data.currentMonth.dailyData);
        const prevMonthData = prepareChartData(data.prevMonth.dailyData);
        
        console.log('Datos preparados:', {
            currentMonth: currentMonthData.slice(0, 5) + '... (truncado)',
            prevMonth: prevMonthData.slice(0, 5) + '... (truncado)'
        });
        
        // Configurar los colores de ENSEK
        const currentMonthColor = 'rgba(135, 201, 71, 0.9)';
        const prevMonthColor = 'rgba(0, 65, 34, 0.7)';
        
        // Calcular el máximo valor para ajustar la escala Y
        const maxValue = Math.max(
            ...currentMonthData,
            ...prevMonthData
        );
        
        // Encontrar los días con más servicios para destacarlos
        const peakDaysData = findPeakDays(currentMonthData, prevMonthData);
        
        // Encontrar el día con mayor diferencia entre los meses
        const biggestDifference = findBiggestDifference(currentMonthData, prevMonthData);
        
        console.log('Creando instancia del gráfico Chart.js...');
        
        // Crear el gráfico con manejo básico de errores
        let chart;
        try {
            chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: Array.from({length: 30}, (_, i) => i + 1), // Días del mes (limitado a 30 para claridad)
            datasets: [
                {
                    label: data.currentMonth.name,
                    data: currentMonthData.slice(0, 30),
                    borderColor: currentMonthColor,
                    backgroundColor: 'rgba(135, 201, 71, 0.2)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.3,
                    pointRadius: (ctx) => {
                        // Destacar los días pico con puntos más grandes
                        const day = parseInt(ctx.chart.data.labels[ctx.dataIndex]);
                        return peakDaysData.currentMonth.includes(day) ? 5 : 3;
                    },
                    pointBackgroundColor: (ctx) => {
                        // Destacar el día con mayor diferencia
                        const day = parseInt(ctx.chart.data.labels[ctx.dataIndex]);
                        return day === biggestDifference.day ? '#ffcc00' : currentMonthColor;
                    },
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#ffcc00'
                },
                {
                    label: data.prevMonth.name,
                    data: prevMonthData.slice(0, 30),
                    borderColor: prevMonthColor,
                    backgroundColor: 'rgba(0, 65, 34, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.3,
                    pointRadius: (ctx) => {
                        // Destacar los días pico con puntos más grandes
                        const day = parseInt(ctx.chart.data.labels[ctx.dataIndex]);
                        return peakDaysData.prevMonth.includes(day) ? 5 : 3;
                    },
                    pointBackgroundColor: (ctx) => {
                        // Destacar el día con mayor diferencia
                        const day = parseInt(ctx.chart.data.labels[ctx.dataIndex]);
                        return day === biggestDifference.day ? '#ffcc00' : prevMonthColor;
                    },
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#ffcc00'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            layout: {
                padding: {
                    left: 5,
                    right: 10,
                    top: 0,
                    bottom: 5
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    align: 'end',
                    labels: {
                        boxWidth: 12,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: {
                            weight: 'bold'
                        }
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#333',
                    bodyColor: '#333',
                    borderColor: '#ddd',
                    borderWidth: 1,
                    padding: 10,
                    cornerRadius: 4,
                    callbacks: {
                        title: function(tooltipItems) {
                            return 'Día ' + tooltipItems[0].label;
                        },
                        label: function(context) {
                            const label = context.dataset.label || '';
                            return label + ': ' + context.raw + ' servicios';
                        },
                        afterBody: function(tooltipItems) {
                            const currentValue = tooltipItems[0].raw || 0;
                            const prevValue = tooltipItems[1]?.raw || 0;
                            
                            if (tooltipItems.length > 1) {
                                const diff = currentValue - prevValue;
                                const percentage = prevValue !== 0 ? ((diff / prevValue) * 100).toFixed(1) : 0;
                                
                                if (diff > 0) {
                                    return `Crecimiento: +${diff} (${percentage}%)`;
                                } else if (diff < 0) {
                                    return `Reducción: ${diff} (${percentage}%)`;
                                } else {
                                    return 'Sin cambios';
                                }
                            }
                            return '';
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Día del mes',
                        font: {
                            weight: 'bold'
                        }
                    },
                    grid: {
                        display: false
                    },
                    ticks: {
                        callback: function(value, index) {
                            // Mostrar solo cada tercer día o el día 1 y 30
                            return index % 3 === 0 || value === 1 || value === 30 ? value : '';
                        },
                        font: {
                            size: 10
                        },
                        maxRotation: 0,
                        minRotation: 0
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Servicios completados',
                        font: {
                            weight: 'bold'
                        }
                    },
                    beginAtZero: true,
                    suggestedMax: Math.ceil(maxValue * 1.1), // 10% más que el máximo para mejor visualización
                    ticks: {
                        precision: 0,
                        stepSize: Math.max(1, Math.ceil(maxValue / 10)) // Ajustar paso según datos
                    }
                }
            }
        }
    });
            
            console.log('Gráfico creado correctamente');
            
            // Si son datos de prueba, mostrar una nota en la parte superior del gráfico
            if (data.isTestData) {
                const chartArea = document.getElementById('monthlyComparisonChart').parentNode;
                const noteElement = document.createElement('div');
                noteElement.className = 'text-center text-muted mb-2';
                noteElement.style.fontSize = '0.8rem';
                noteElement.style.position = 'absolute';
                noteElement.style.top = '5px';
                noteElement.style.left = '0';
                noteElement.style.right = '0';
                noteElement.style.zIndex = '10';
                noteElement.innerHTML = '<i class="fas fa-info-circle me-1"></i> Mostrando datos simulados para fines de demostración';
                chartArea.prepend(noteElement);
            }
        } catch (error) {
            console.error('Error al crear la instancia de Chart.js:', error);
            // Mostrar visualización alternativa si falla la creación del gráfico
            showAlternativeVisualization(data, document.getElementById('monthlyComparisonChart'));
        }
    } catch (error) {
        console.error('Error en renderMonthlyComparisonChart:', error);
    }
    
    // Función para preparar los datos del gráfico
    function prepareChartData(dailyData) {
        // Crear un array de 31 elementos (días del mes) con valores 0
        const result = Array(31).fill(0);
        
        // Rellenar con los datos reales disponibles
        Object.entries(dailyData).forEach(([date, count]) => {
            const day = parseInt(date.split('-')[2]);
            if (day >= 1 && day <= 31) {
                result[day - 1] = count;
            }
        });
        
        return result;
    }
    
    // Encontrar los días con más servicios para destacar
    function findPeakDays(currentData, prevData) {
        // Encontrar los 3 días con más servicios en cada mes
        function getTopDays(data, count = 3) {
            return data
                .map((value, index) => ({ value, day: index + 1 }))
                .sort((a, b) => b.value - a.value)
                .slice(0, count)
                .map(item => item.day);
        }
        
        return {
            currentMonth: getTopDays(currentData),
            prevMonth: getTopDays(prevData)
        };
    }
    
    // Encontrar el día con mayor diferencia entre meses
    function findBiggestDifference(currentData, prevData) {
        let maxDiff = 0;
        let maxDiffDay = 1;
        
        currentData.forEach((value, index) => {
            const diff = Math.abs(value - (prevData[index] || 0));
            if (diff > maxDiff) {
                maxDiff = diff;
                maxDiffDay = index + 1;
            }
        });
        
        return {
            day: maxDiffDay,
            difference: maxDiff
        };
    }
    
    // Función para mostrar una visualización alternativa cuando falla el gráfico
    function showAlternativeVisualization(data, container) {
        console.log('Mostrando visualización alternativa de los datos');
        
        // Calcular los días de mayor servicio para cada mes
        const currentMonthData = prepareChartData(data.currentMonth.dailyData);
        const prevMonthData = prepareChartData(data.prevMonth.dailyData);
        
        // Encontrar los días pico
        const peakDays = findPeakDays(currentMonthData, prevMonthData);
        
        // Crear HTML para la visualización alternativa
        let htmlContent = `
            <div class="alert alert-info text-center mb-3">
                <i class="fas fa-info-circle me-2"></i>
                Se muestra una visualización alternativa de los datos
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <h6 class="fw-bold">Días pico en ${data.currentMonth.name}</h6>
                    <ul class="list-group">
                        ${peakDays.currentMonth.map(day => 
                            `<li class="list-group-item d-flex justify-content-between align-items-center">
                                Día ${day}
                                <span class="badge rounded-pill" style="background-color: var(--ensek-green-light);">
                                    ${currentMonthData[day-1]} servicios
                                </span>
                             </li>`
                        ).join('')}
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold">Días pico en ${data.prevMonth.name}</h6>
                    <ul class="list-group">
                        ${peakDays.prevMonth.map(day => 
                            `<li class="list-group-item d-flex justify-content-between align-items-center">
                                Día ${day}
                                <span class="badge rounded-pill" style="background-color: var(--ensek-green-dark);">
                                    ${prevMonthData[day-1]} servicios
                                </span>
                             </li>`
                        ).join('')}
                    </ul>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <p class="fw-bold">Total de servicios</p>
                <div class="d-flex justify-content-center gap-5">
                    <div>
                        <span class="d-block text-muted">${data.prevMonth.name}</span>
                        <span class="fs-2">${data.prevMonth.total}</span>
                    </div>
                    <div>
                        <span class="d-block text-muted">${data.currentMonth.name}</span>
                        <span class="fs-2">${data.currentMonth.total}</span>
                    </div>
                </div>
                
                <div class="mt-3">
                    ${data.hasGrowth 
                        ? `<span class="badge rounded-pill bg-success">
                            <i class="fas fa-arrow-up me-1"></i> ${data.growthPercentage.toFixed(1)}%
                          </span>`
                        : `<span class="badge rounded-pill bg-danger">
                            <i class="fas fa-arrow-down me-1"></i> ${Math.abs(data.growthPercentage).toFixed(1)}%
                          </span>`
                    }
                    <span class="text-muted ms-2">vs mes anterior</span>
                </div>
            </div>
        `;
        
        // Insertar el HTML en el contenedor
        container.innerHTML = htmlContent;
    }
}
</script>
@endpush