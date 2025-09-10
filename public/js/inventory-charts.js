/**
 * Inventory Dashboard Charts
 * Este archivo se encarga de la inicialización y configuración de los gráficos en el dashboard de inventario
 */

// Variables para las instancias de los gráficos
var summaryChart = null;
var lowStockChart = null;

// Inicializar los gráficos
function initializeCharts(categoryLabels, categoryQuantities, lowStockLabels, lowStockQuantities, lowStockThresholds) {
    console.log('Inicializando gráficos del dashboard...');
    destroyCharts();
    
    // Gráfico de resumen de inventario
    if ($('#inventorySummaryChart').length > 0) {
        try {
            const ctx = document.getElementById('inventorySummaryChart');
            const colors = generateColors(categoryLabels.length);
            
            if (!categoryLabels || !categoryQuantities || categoryLabels.length === 0) {
                console.warn('No hay datos para el gráfico de resumen');
                return;
            }
            
            summaryChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: categoryLabels,
                    datasets: [{
                        data: categoryQuantities,
                        backgroundColor: colors,
                        hoverBackgroundColor: colors,
                        hoverBorderColor: "rgba(234, 236, 244, 1)"
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    cutoutPercentage: 80,
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        displayColors: false
                    }
                }
            });
            console.log('Gráfico de resumen creado correctamente');
        } catch (error) {
            console.error('Error al crear el gráfico de resumen:', error);
        }
    }
    
    // Gráfico de stock bajo
    if ($('#lowStockChart').length > 0) {
        try {
            const ctx = document.getElementById('lowStockChart');
            
            if (!lowStockLabels || !lowStockQuantities || lowStockLabels.length === 0) {
                console.warn('No hay datos para el gráfico de stock bajo');
                return;
            }
            
            lowStockChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: lowStockLabels,
                    datasets: [
                        {
                            label: "Cantidad actual",
                            backgroundColor: "#e74a3b",
                            data: lowStockQuantities
                        },
                        {
                            label: "Nivel mínimo",
                            backgroundColor: "#4e73df",
                            data: lowStockThresholds
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        xAxes: [{
                            gridLines: { display: false },
                            maxBarThickness: 25
                        }],
                        yAxes: [{
                            ticks: { min: 0 }
                        }]
                    },
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            });
            console.log('Gráfico de stock bajo creado correctamente');
        } catch (error) {
            console.error('Error al crear el gráfico de stock bajo:', error);
        }
    }
}

// Eliminar instancias anteriores de gráficos
function destroyCharts() {
    if (summaryChart) {
        summaryChart.destroy();
        summaryChart = null;
    }
    if (lowStockChart) {
        lowStockChart.destroy();
        lowStockChart = null;
    }
}

// Generar colores para gráficos
function generateColors(count) {
    const baseColors = [
        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', 
        '#858796', '#5a5c69', '#2e59d9', '#17a673', '#2c9faf'
    ];
    
    if (count <= baseColors.length) {
        return baseColors.slice(0, count);
    }
    
    const colors = [...baseColors];
    for (let i = baseColors.length; i < count; i++) {
        const r = Math.floor(Math.random() * 200) + 55;
        const g = Math.floor(Math.random() * 200) + 55;
        const b = Math.floor(Math.random() * 200) + 55;
        colors.push(`rgb(${r}, ${g}, ${b})`);
    }
    return colors;
}

// Optimizar altura de tarjetas
function fixCardHeights() {
    $('.dashboard-card').css('height', '');
    
    setTimeout(function() {
        try {
            let maxHeight = 0;
            $('.dashboard-card').each(function() {
                maxHeight = Math.max(maxHeight, $(this).outerHeight());
            });
            
            if (maxHeight > 0) {
                maxHeight = Math.max(maxHeight, 400);
                $('.dashboard-card').css('height', maxHeight + 'px');
                console.log('Altura de tarjetas ajustada a:', maxHeight, 'px');
            }
        } catch (error) {
            console.error('Error al ajustar alturas:', error);
            $('.dashboard-card').css('height', '400px');
        }
    }, 200);
}
