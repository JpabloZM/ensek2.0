/**
 * Marcador de selección mejorado para el calendario
 * Este script reemplaza completamente el mecanismo de selección visual
 */

document.addEventListener("DOMContentLoaded", function () {
    // Esperar a que el DOM esté completamente cargado
    setTimeout(function () {
        // Referencias a elementos clave
        const calendarContainer = document.querySelector(
            ".technician-calendar-container"
        );
        if (!calendarContainer) return;

        // Crear un nuevo overlay de selección personalizado
        const selectionMarker = document.createElement("div");
        selectionMarker.id = "advanced-selection-marker";
        selectionMarker.style.display = "none";
        selectionMarker.style.position = "absolute";
        selectionMarker.style.pointerEvents = "none";
        selectionMarker.style.zIndex = "9999";
        selectionMarker.innerHTML = `
            <div class="selection-marker-header"></div>
            <div class="selection-marker-body"></div>
        `;
        document.body.appendChild(selectionMarker);

        // Crear estilos específicos para el marcador
        const markerStyles = document.createElement("style");
        markerStyles.textContent = `
            #advanced-selection-marker {
                border: 3px solid #004122;
                box-shadow: 0 0 0 1px white, 0 0 8px rgba(0,0,0,0.3);
                background-color: rgba(135, 201, 71, 0.2);
                display: flex;
                flex-direction: column;
            }
            
            .selection-marker-header {
                background-color: #004122;
                color: white;
                padding: 4px 8px;
                font-size: 14px;
                font-weight: bold;
                text-align: center;
            }
            
            .selection-marker-body {
                flex-grow: 1;
                border: 1px dashed rgba(255,255,255,0.7);
                margin: 5px;
            }
            
            body.is-selecting #advanced-selection-marker {
                display: block !important;
            }
        `;
        document.head.appendChild(markerStyles);

        // Función para mostrar el marcador de selección
        window.showSelectionMarker = function (startCell, endCell, timeRange) {
            if (!startCell || !endCell) return;

            // Obtener las coordenadas de las celdas
            const startRect = startCell.getBoundingClientRect();
            const endRect = endCell.getBoundingClientRect();
            const containerRect = calendarContainer.getBoundingClientRect();

            // Calcular las dimensiones del marcador
            const top = startRect.top;
            const left = startRect.left;
            const width = startRect.width;
            const height = endRect.top + endRect.height - startRect.top;

            // Posicionar el marcador
            selectionMarker.style.top = `${top}px`;
            selectionMarker.style.left = `${left}px`;
            selectionMarker.style.width = `${width}px`;
            selectionMarker.style.height = `${height}px`;

            // Actualizar el texto del horario
            if (timeRange) {
                selectionMarker.querySelector(
                    ".selection-marker-header"
                ).textContent = timeRange;
            }

            // Asegurar que el marcador sea visible
            selectionMarker.style.display = "block";
            document.body.classList.add("is-selecting");
        };

        // Función para ocultar el marcador
        window.hideSelectionMarker = function () {
            selectionMarker.style.display = "none";
            document.body.classList.remove("is-selecting");
        };

        // Monitorear cambios en el DOM para detectar la selección de celdas
        // Esto permitirá que nuestro script funcione con el mecanismo existente
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
                        // La selección se está mostrando, obtengamos su contenido
                        const timeRange =
                            overlay.querySelector(".selection-time")
                                ?.textContent || "";

                        // Encontrar las celdas que están siendo seleccionadas
                        const selectedCells = document.querySelectorAll(
                            ".calendar-cell-selected"
                        );
                        if (selectedCells.length > 0) {
                            const firstCell = selectedCells[0];
                            const lastCell =
                                selectedCells[selectedCells.length - 1];

                            // Mostrar nuestro marcador mejorado
                            window.showSelectionMarker(
                                firstCell,
                                lastCell,
                                timeRange
                            );
                        }
                    } else {
                        // La selección se está ocultando
                        window.hideSelectionMarker();
                    }
                }
            });
        });

        // Observar cambios en el overlay original
        const originalOverlay = document.querySelector(
            ".calendar-selection-overlay"
        );
        if (originalOverlay) {
            observer.observe(originalOverlay, { attributes: true });
        }

        // También podemos conectar directamente con el sistema de eventos existente
        document.addEventListener("mousedown", function (e) {
            const cell = e.target.closest(".calendar-service-cell");
            if (cell && !cell.querySelector(".calendar-service")) {
                // Se está iniciando una selección
                document.body.classList.add("selection-started");
            }
        });

        document.addEventListener("mouseup", function () {
            document.body.classList.remove("selection-started");
        });
    }, 500); // Pequeño retraso para asegurar que todo esté cargado
});
