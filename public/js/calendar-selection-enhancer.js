/**
 * Función para integrarse con el sistema de selección existente
 * y asegurar que siempre sea visible el marcador
 */

document.addEventListener("DOMContentLoaded", function () {
    // Esperar un momento para que todos los scripts se hayan cargado
    setTimeout(function () {
        const calendarContainer = document.querySelector(
            ".technician-calendar-container"
        );
        if (!calendarContainer) return;

        // Buscar la función original de actualización del overlay
        const originalScript = document.querySelector(
            'script[src*="calendar-drag-selection.js"]'
        );
        if (originalScript) {
            // Modificar la función updateSelectionOverlay original
            // Este enfoque observa la ejecución de la función original y mejora el marcador
            const observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    if (
                        mutation.type === "attributes" &&
                        mutation.attributeName === "style" &&
                        mutation.target.classList.contains(
                            "calendar-selection-overlay"
                        )
                    ) {
                        const overlay = mutation.target;

                        if (overlay.style.display !== "none") {
                            // Asegurarse que el marcador tenga estilos claramente visibles
                            overlay.style.border = "3px solid #004122";
                            overlay.style.backgroundColor =
                                "rgba(135, 201, 71, 0.2)";
                            overlay.style.zIndex = "9999";
                            overlay.style.boxShadow =
                                "0 0 0 1px white, 0 0 10px rgba(0,0,0,0.5)";

                            // Añadir/reforzar clases para asegurar visibilidad
                            overlay.classList.add("selection-visible");
                            document.body.classList.add("selection-active");
                        }
                    }
                });
            });

            // Observar cambios en el overlay existente
            const originalOverlay = document.querySelector(
                ".calendar-selection-overlay"
            );
            if (originalOverlay) {
                observer.observe(originalOverlay, {
                    attributes: true,
                    attributeFilter: ["style", "class"],
                });

                // También aplicar estilos iniciales
                originalOverlay.style.border = "3px solid #004122";
                originalOverlay.style.backgroundColor =
                    "rgba(135, 201, 71, 0.2)";
                originalOverlay.style.zIndex = "9999";
                originalOverlay.style.boxShadow =
                    "0 0 0 1px white, 0 0 10px rgba(0,0,0,0.5)";
            }
        }

        // Añadir estilos adicionales para el overlay
        const extraStyles = document.createElement("style");
        extraStyles.textContent = `
            .calendar-selection-overlay {
                border: 3px solid #004122 !important;
                background-color: rgba(135, 201, 71, 0.2) !important;
                z-index: 9999 !important;
                box-shadow: 0 0 0 1px white, 0 0 10px rgba(0,0,0,0.5) !important;
                visibility: visible !important;
                display: flex !important;
                opacity: 1 !important;
                pointer-events: none;
            }
            
            /* Cuando la selección está oculta, ocultarla realmente */
            .calendar-selection-overlay[style*="display: none"] {
                display: none !important;
            }
            
            /* Estilos para el indicador de tiempo */
            .selection-time {
                background-color: #004122 !important;
                color: white !important;
                padding: 4px 8px !important;
                font-size: 14px !important;
                font-weight: bold !important;
                border-radius: 2px !important;
                margin-top: 0 !important;
                box-shadow: 0 2px 4px rgba(0,0,0,0.3) !important;
            }
            
            /* Asegurar que las celdas no tengan estilos que compitan */
            .calendar-cell-selected {
                background-color: transparent !important;
                border: none !important;
                outline: none !important;
            }
        `;
        document.head.appendChild(extraStyles);
    }, 200);
});
