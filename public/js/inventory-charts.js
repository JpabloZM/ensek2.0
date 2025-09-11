/**
 * Inventory Dashboard Charts
 * Este archivo se encarga de la inicialización y configuración de los gráficos en el dashboard de inventario
 */

// Variables para las instancias de los gráficos (NO SE UTILIZAN MÁS)
// NOTA: Los gráficos ahora se inicializan directamente en la vista
// Se mantiene el código por compatibilidad con versiones anteriores
var summaryChart = null;
var lowStockChart = null;

// Inicializar los gráficos
function initializeCharts(
    categoryLabels,
    categoryQuantities,
    lowStockLabels,
    lowStockQuantities,
    lowStockThresholds
) {
    console.log(
        "%c[CHARTS DEBUG] INICIANDO GRÁFICOS",
        "background:yellow; color:black; font-size: 14px"
    );
    console.log("Datos recibidos:", {
        categoryLabels,
        categoryQuantities,
        lowStockLabels,
        lowStockQuantities,
        lowStockThresholds,
    });

    // Comprobar si tenemos Chart.js disponible
    if (typeof Chart === "undefined") {
        console.error("ERROR CRÍTICO: Chart.js no está disponible");
        return;
    } else {
        console.log("Chart.js disponible: versión", Chart.version);
    }

    // Validar que tenemos datos válidos
    if (!Array.isArray(categoryLabels) || !Array.isArray(categoryQuantities)) {
        console.error("ERROR: Los datos de categorías no son arrays válidos");
        console.log("Tipo categoryLabels:", typeof categoryLabels);
        console.log("Tipo categoryQuantities:", typeof categoryQuantities);
        return;
    }

    if (categoryLabels.length === 0 || categoryQuantities.length === 0) {
        console.warn("ADVERTENCIA: No hay datos de categorías para mostrar");
    } else {
        console.log(
            "Datos de categorías válidos:",
            categoryLabels.length,
            "categorías"
        );
    }

    destroyCharts();

    // Gráfico de resumen de inventario
    if ($("#inventorySummaryChart").length > 0) {
        try {
            console.log("Encontrado elemento #inventorySummaryChart");
            const chartElement = document.getElementById(
                "inventorySummaryChart"
            );

            // Verificar que el elemento es válido
            if (!chartElement) {
                console.error(
                    "ERROR: El elemento #inventorySummaryChart no es válido"
                );
                return;
            }

            console.log("Validando contexto del canvas...");
            const ctx = chartElement.getContext("2d");
            if (!ctx) {
                console.error(
                    "ERROR: No se pudo obtener el contexto 2D del canvas"
                );
                return;
            }

            const colors = generateColors(categoryLabels.length);
            console.log("Colores generados:", colors);

            if (
                !categoryLabels ||
                !categoryQuantities ||
                categoryLabels.length === 0
            ) {
                console.warn("No hay datos para el gráfico de resumen");

                // Mostrar mensaje en el canvas
                ctx.font = "16px Arial";
                ctx.textAlign = "center";
                ctx.fillStyle = "#888";
                ctx.fillText(
                    "No hay datos para mostrar",
                    chartElement.width / 2,
                    chartElement.height / 2
                );
                return;
            }

            console.log("Creando instancia de gráfico de resumen...");
            summaryChart = new Chart(ctx, {
                type: "doughnut",
                data: {
                    labels: categoryLabels,
                    datasets: [
                        {
                            data: categoryQuantities,
                            backgroundColor: colors,
                            hoverBackgroundColor: colors,
                            hoverBorderColor: "rgba(234, 236, 244, 1)",
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: "bottom",
                        },
                        tooltip: {
                            backgroundColor: "rgb(255,255,255)",
                            bodyColor: "#858796",
                            borderColor: "#dddfeb",
                            borderWidth: 1,
                            usePointStyle: true,
                        },
                    },
                    cutout: "70%",
                },
            });
            console.log(
                "%c✓ Gráfico de resumen creado correctamente",
                "color: green; font-weight: bold;"
            );
        } catch (error) {
            console.error(
                "ERROR CRÍTICO al crear el gráfico de resumen:",
                error
            );
            // Mostrar el error en la página
            const chartContainer = document.querySelector(".chart-container");
            if (chartContainer) {
                chartContainer.innerHTML = `
                    <div class="alert alert-danger">
                        <strong>Error al cargar el gráfico:</strong> ${error.message}
                    </div>
                `;
            }
        }
    } else {
        console.error(
            "ERROR: No se encontró el elemento #inventorySummaryChart en el DOM"
        );
    }

    // Gráfico de stock bajo
    if ($("#lowStockChart").length > 0) {
        try {
            const ctx = document.getElementById("lowStockChart");

            if (
                !lowStockLabels ||
                !lowStockQuantities ||
                lowStockLabels.length === 0
            ) {
                console.warn("No hay datos para el gráfico de stock bajo");
                return;
            }

            lowStockChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: lowStockLabels,
                    datasets: [
                        {
                            label: "Cantidad actual",
                            backgroundColor: "#e74a3b",
                            data: lowStockQuantities,
                        },
                        {
                            label: "Nivel mínimo",
                            backgroundColor: "#4e73df",
                            data: lowStockThresholds,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45,
                            },
                        },
                        y: {
                            beginAtZero: true,
                        },
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: "bottom",
                        },
                    },
                    barPercentage: 0.6,
                },
            });
            console.log("Gráfico de stock bajo creado correctamente");
        } catch (error) {
            console.error("Error al crear el gráfico de stock bajo:", error);
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

// Optimizar altura de tarjetas
function fixCardHeights() {
    $(".dashboard-card").css("height", "");

    setTimeout(function () {
        try {
            let maxHeight = 0;
            $(".dashboard-card").each(function () {
                maxHeight = Math.max(maxHeight, $(this).outerHeight());
            });

            if (maxHeight > 0) {
                maxHeight = Math.max(maxHeight, 400);
                $(".dashboard-card").css("height", maxHeight + "px");
                console.log("Altura de tarjetas ajustada a:", maxHeight, "px");
            }
        } catch (error) {
            console.error("Error al ajustar alturas:", error);
            $(".dashboard-card").css("height", "400px");
        }
    }, 200);
}
