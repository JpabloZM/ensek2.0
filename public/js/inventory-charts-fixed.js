/**
 * Inventory Dashboard Charts - VERSIÓN ARREGLADA
 * Este archivo se encarga de la inicialización y configuración de los gráficos en el dashboard de inventario
 */

// Función para generar colores para gráficos
function generateColors(count) {
    const baseColors = [
        "#4e73df",
        "#1cc88a",
        "#36b9cc",
        "#f6c23e",
        "#e74a3b",
        "#858796",
        "#5a5c69",
        "#2e59d9",
        "#17a673",
        "#2c9faf",
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

// Inicialización segura de gráficos
document.addEventListener('DOMContentLoaded', function() {
    console.log("%c[INVENTORY CHARTS] Inicializando sistema de gráficos...", "background:blue; color:white");
    
    // Verificar si Chart.js está disponible
    if (typeof Chart === "undefined") {
        console.error("ERROR CRÍTICO: Chart.js no está disponible");
        showChartError("inventorySummaryChart", "Chart.js no está disponible");
        return;
    }
    
    // Función para mostrar error en el contenedor del gráfico
    function showChartError(containerId, message) {
        const container = document.querySelector(containerId ? `#${containerId}` : '.chart-container');
        if (container) {
            container.innerHTML = `
                <div class="alert alert-danger text-center p-3">
                    <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                    <h5>Error al cargar el gráfico</h5>
                    <p>${message}</p>
                </div>
            `;
        }
    }
    
    // Carga segura de datos
    try {
        // Intentamos obtener los datos del DOM primero
        let inventoryData = {};
        const dataScript = document.getElementById('inventory-data');
        
        if (dataScript) {
            try {
                inventoryData = JSON.parse(dataScript.textContent);
                console.log("✓ Datos cargados desde elemento script", inventoryData);
            } catch (parseError) {
                console.error("Error al parsear datos desde script:", parseError);
            }
        }
        
        // Si no hay datos en el DOM, intentamos usar los datos globales
        if (!inventoryData.categoryLabels && window.inventoryData) {
            inventoryData = window.inventoryData;
            console.log("✓ Datos cargados desde variable global", inventoryData);
        }
        
        // Verificar que tenemos datos válidos
        if (!inventoryData.categoryLabels || !Array.isArray(inventoryData.categoryLabels)) {
            console.error("No hay datos de categorías válidos");
            showChartError("inventorySummaryChart", "No hay datos disponibles para mostrar");
            return;
        }
        
        // Inicializar el gráfico de resumen solo cuando tenemos datos válidos
        setTimeout(function() {
            initSummaryChart(
                inventoryData.categoryLabels || [],
                inventoryData.categoryQuantities || []
            );
        }, 300);
        
    } catch (error) {
        console.error("Error al inicializar los gráficos:", error);
        showChartError("inventorySummaryChart", error.message);
    }
    
    // Inicializar el gráfico de resumen
    function initSummaryChart(labels, data) {
        if (!labels.length || !data.length) {
            console.warn("No hay datos para mostrar en el gráfico de resumen");
            return;
        }
        
        const canvas = document.getElementById("inventorySummaryChart");
        if (!canvas) {
            console.error("No se encontró el elemento canvas para el gráfico de resumen");
            return;
        }
        
        // Asegurarse de que no exista una instancia previa
        let existingChart;
        try {
            existingChart = Chart.getChart(canvas);
            if (existingChart) {
                console.log("Destruyendo instancia previa del gráfico");
                existingChart.destroy();
            }
        } catch (error) {
            console.warn("No se pudo obtener/destruir gráfico existente:", error);
        }
        
        try {
            // Crear nueva instancia de gráfico
            const colors = generateColors(labels.length);
            
            const chartOptions = {
                type: "doughnut",
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: "bottom"
                        }
                    },
                    cutout: "60%"
                }
            };
            
            const newChart = new Chart(canvas, chartOptions);
            console.log("✓ Gráfico creado exitosamente");
        } catch (chartError) {
            console.error("Error al crear el gráfico:", chartError);
            showChartError("inventorySummaryChart", chartError.message);
        }
    }
});

// Función para ajustar alturas de las tarjetas del dashboard
function fixCardHeights() {
    try {
        console.log('Ajustando alturas de tarjetas...');
        
        // Aplicar la misma altura a las tarjetas en la misma fila
        if (window.innerWidth >= 992) {
            // Resetear alturas previas
            $('#dashboardRow .card').css('height', '');
            
            setTimeout(function() {
                // Encontrar la altura máxima
                let maxHeight = 0;
                $('#dashboardRow .card').each(function() {
                    const height = $(this).outerHeight();
                    if (height > maxHeight) {
                        maxHeight = height;
                    }
                });
                
                if (maxHeight > 0) {
                    // Aplicar altura máxima a todas las tarjetas
                    $('#dashboardRow .card').css('height', maxHeight + 'px');
                    
                    // Ajustar también los contenedores internos
                    const contentHeight = maxHeight - 48; // 48px para el card-header
                    $('.chart-container, .stock-list').css('height', contentHeight + 'px');
                }
            }, 100);
        }
    } catch (error) {
        console.error("Error al ajustar alturas:", error);
    }
}

// Ejecutar el ajuste de altura cuando la página esté completamente cargada
$(window).on('load', function() {
    setTimeout(fixCardHeights, 300);
});

// Manejar eventos de redimensionamiento con debounce
let resizeTimeout;
$(window).on('resize', function() {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(fixCardHeights, 250);
});

// Función para mostrar toast notifications
function showToast(title, message, type = 'success') {
    const toastContainer = document.querySelector('.toast-container');
    
    if (!toastContainer) return;
    
    // Crear elemento toast
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                ${title ? `<strong>${title}:</strong> ` : ''}${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    
    // Añadir al container
    toastContainer.appendChild(toast);
    
    // Animación de entrada
    setTimeout(() => {
        toast.classList.add('showing');
        setTimeout(() => {
            toast.classList.add('show');
        }, 100);
        
        // Auto-ocultar después de 5 segundos
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                toast.remove();
            }, 500);
        }, 5000);
    }, 100);
    
    // Manejar botón cerrar
    const closeBtn = toast.querySelector('.btn-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            toast.classList.remove('show');
            setTimeout(() => {
                toast.remove();
            }, 500);
        });
    }
}

// Exportar funciones para uso global
window.initInventoryCharts = {
    fixCardHeights,
    showToast,
    generateColors
};
