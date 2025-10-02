/**
 * Script para optimizar la fluidez del movimiento en la selección del calendario
 * Aplica técnicas avanzadas de rendimiento para un arrastre suave
 */

document.addEventListener("DOMContentLoaded", function () {
    // Esperar a que todo esté cargado completamente
    setTimeout(function () {
        console.log("Aplicando optimizaciones para movimiento fluido");

        // Referencia al marcador de selección
        const marker = document.getElementById("simple-selection-marker");
        if (!marker) return;

        // Aplicar optimizaciones de rendimiento al marcador
        applyPerformanceOptimizations(marker);

        // Funciones para optimizar rendimiento
        function applyPerformanceOptimizations(element) {
            // 1. Forzar composición de hardware para animaciones más fluidas
            element.style.transform = "translateZ(0)";
            element.style.backfaceVisibility = "hidden";
            element.style.perspective = "1000px";

            // 2. Asegurar que las transiciones sean suaves
            const timeDisplay = element.querySelector(".time-display");
            if (timeDisplay) {
                timeDisplay.style.transition = "transform 0.15s ease-out";
                timeDisplay.style.transform = "translateZ(0) translateX(-50%)";
            }

            // 3. Reducir trabajo de pintura durante el arrastre
            const calendarContainer = document.querySelector(
                ".technician-calendar-container"
            );
            if (calendarContainer) {
                calendarContainer.addEventListener("mousedown", function () {
                    document.body.classList.add("dragging-optimization");
                });

                document.addEventListener("mouseup", function () {
                    document.body.classList.remove("dragging-optimization");
                });
            }
        }

        // Agregar estilos para optimización
        const optimizationStyles = document.createElement("style");
        optimizationStyles.textContent = `
            /* Optimizaciones para movimiento fluido */
            #simple-selection-marker {
                transform: translateZ(0);
                will-change: transform, top, left, width, height;
                transition: top 0.08s cubic-bezier(0.1, 0.7, 0.1, 1), 
                            left 0.08s cubic-bezier(0.1, 0.7, 0.1, 1), 
                            width 0.12s ease-out, 
                            height 0.12s ease-out;
            }
            
            /* Reducir trabajo de pintura durante arrastre */
            body.dragging-optimization * {
                animation-play-state: paused !important;
                transition: none !important;
            }
            
            body.dragging-optimization #simple-selection-marker {
                transition: top 0.08s cubic-bezier(0.1, 0.7, 0.1, 1), 
                           left 0.08s cubic-bezier(0.1, 0.7, 0.1, 1), 
                           width 0.12s ease-out, 
                           height 0.12s ease-out !important;
            }
            
            /* Animación suave para el indicador de tiempo */
            #simple-selection-marker .time-display {
                transform: translateZ(0) translateX(-50%);
                will-change: transform;
                transition: transform 0.1s ease-out;
            }
        `;
        document.head.appendChild(optimizationStyles);

        // Monitorear el rendimiento y ajustar dinámicamente
        let lastFrameTime = 0;
        let frameCount = 0;
        let fpsMonitoring = false;

        // Función para activar monitoreo temporal de rendimiento durante arrastres
        document.addEventListener("mousedown", function (e) {
            const cell = e.target.closest(".calendar-service-cell");
            if (cell) {
                startPerformanceMonitoring();
            }
        });

        document.addEventListener("mouseup", function () {
            stopPerformanceMonitoring();
        });

        function startPerformanceMonitoring() {
            if (fpsMonitoring) return;

            fpsMonitoring = true;
            frameCount = 0;
            lastFrameTime = performance.now();
            requestAnimationFrame(monitorFrameRate);
        }

        function stopPerformanceMonitoring() {
            fpsMonitoring = false;
        }

        function monitorFrameRate() {
            if (!fpsMonitoring) return;

            frameCount++;
            const now = performance.now();
            const elapsed = now - lastFrameTime;

            // Cada segundo, verificar rendimiento
            if (elapsed >= 1000) {
                const fps = Math.round((frameCount * 1000) / elapsed);
                console.log(`Rendimiento: ${fps} FPS`);

                // Si el rendimiento es bajo, aplicar optimizaciones adicionales
                if (fps < 30 && marker) {
                    console.log(
                        "Aplicando optimizaciones adicionales para rendimiento bajo"
                    );
                    marker.style.transition = "none"; // Desactivar transiciones si el rendimiento es bajo
                    document.body.classList.add("low-performance-mode");
                } else if (fps >= 40) {
                    document.body.classList.remove("low-performance-mode");
                }

                // Reiniciar contador
                frameCount = 0;
                lastFrameTime = now;
            }

            // Continuar monitoreando
            requestAnimationFrame(monitorFrameRate);
        }
    }, 1500);
});
