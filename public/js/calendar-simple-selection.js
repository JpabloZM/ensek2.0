/**
 * Solución definitiva para la selección en el calendario
 * Este script corrige los problemas de posicionamiento del marcador de selección
 */

document.addEventListener("DOMContentLoaded", function () {
    // Esperar a que el DOM esté completamente cargado
    setTimeout(function () {
        console.log("Inicializando marcador de selección corregido");

        // Referencias a elementos clave
        const calendarContainer = document.querySelector(
            ".technician-calendar-container"
        );
        if (!calendarContainer) return;

        // Crear el marcador de selección
        const selectionMarker = document.createElement("div");
        selectionMarker.id = "simple-selection-marker";
        selectionMarker.style.position = "absolute";
        selectionMarker.style.display = "none";
        selectionMarker.style.pointerEvents = "none";
        selectionMarker.style.zIndex = "10000";
        selectionMarker.innerHTML = `<div class="time-display"></div>`;
        // Añadir el marcador directamente al contenedor del calendario para posicionamiento relativo correcto
        calendarContainer.appendChild(selectionMarker);

        // Estilos para el marcador
        const markerStyles = document.createElement("style");
        markerStyles.textContent = `
            #simple-selection-marker {
                border: 2px solid #00a651;
                background-color: rgba(135, 201, 71, 0.15);
                box-shadow: 0 0 0 1px white;
                pointer-events: none;
            }
            
            #simple-selection-marker .time-display {
                position: absolute;
                top: -25px;
                left: 50%;
                transform: translateX(-50%);
                background-color: #004122;
                color: white;
                padding: 3px 8px;
                font-size: 12px;
                font-weight: bold;
                border-radius: 3px;
                white-space: nowrap;
            }
            
            /* Ocultar el marcador original */
            .calendar-selection-overlay {
                display: none !important;
                opacity: 0 !important;
                visibility: hidden !important;
            }
            
            /* Limpiar estilos de celdas seleccionadas */
            .calendar-cell-selected {
                background-color: transparent !important;
                border: none !important;
                outline: none !important;
            }
            
            /* Estilo para el cursor durante la selección */
            .calendar-selectable {
                cursor: pointer;
            }
            
            .calendar-selectable.dragging-active {
                cursor: grabbing !important;
            }
        `;
        document.head.appendChild(markerStyles);

        // Modificar el comportamiento de selección original
        // Primero, verificar si existe la función para detectar la selección
        let isDragging = false;
        let startCell = null;
        let endCell = null;
        let selectedCells = [];

        // 1. Reemplazar el overlay original
        const originalOverlay = document.querySelector(
            ".calendar-selection-overlay"
        );
        if (originalOverlay) {
            // Ocultar el overlay original
            originalOverlay.style.opacity = "0";
            originalOverlay.style.visibility = "hidden";

            // Observar cambios en el overlay original para sincronizar nuestro marcador simple
            const observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    if (
                        mutation.type === "attributes" &&
                        mutation.attributeName === "style"
                    ) {
                        if (originalOverlay.style.display !== "none") {
                            // El overlay original está visible, obtener sus datos
                            const timeDisplay =
                                originalOverlay.querySelector(".selection-time")
                                    ?.textContent || "";
                            const rect =
                                originalOverlay.getBoundingClientRect();

                            // Mostrar nuestro marcador simple
                            showSimpleMarker(rect, timeDisplay);

                            // Ocultar todos los demás marcadores
                            hideAllOtherMarkers();
                        } else {
                            // El overlay original está oculto
                            selectionMarker.style.display = "none";
                        }
                    }
                });
            });

            observer.observe(originalOverlay, {
                attributes: true,
                attributeFilter: ["style"],
            });
        }

        // 2. Capturar eventos de selección directamente
        document.addEventListener("mousedown", function (e) {
            const cell = e.target.closest(".calendar-service-cell");
            if (cell && !cell.querySelector(".calendar-service")) {
                // Estamos iniciando una nueva selección
                isDragging = true;
                startCell = cell;
                endCell = cell;

                // Limpiar cualquier selección previa
                selectedCells.forEach((c) =>
                    c.classList.remove("calendar-cell-selected")
                );
                selectedCells = [cell];
                cell.classList.add("calendar-cell-selected");

                // Marcar el estado de arrastre
                document.body.classList.add("is-selecting");
                calendarContainer.classList.add("dragging-active");
            }
        });

        // Variable para controlar la limitación de velocidad de actualización
        let rafId = null;
        let lastUpdateTime = 0;
        const updateThrottleMs = 16; // Aproximadamente 60fps

        document.addEventListener("mousemove", function (e) {
            if (!isDragging || !startCell) return;

            // Prevenir selección de texto
            e.preventDefault();

            // Usar requestAnimationFrame para optimizar rendimiento
            if (rafId) return; // Si ya hay una actualización pendiente, no programar otra

            const now = Date.now();
            const timeSinceLastUpdate = now - lastUpdateTime;

            if (timeSinceLastUpdate < updateThrottleMs) {
                // Si ha pasado muy poco tiempo desde la última actualización, programar una actualización diferida
                rafId = requestAnimationFrame(function () {
                    updateSelectionFromMouseEvent(e);
                    rafId = null;
                });
            } else {
                // Actualizar inmediatamente
                updateSelectionFromMouseEvent(e);
                lastUpdateTime = now;
                rafId = null;
            }
        });

        // Función separada para actualizar la selección (mejora rendimiento)
        function updateSelectionFromMouseEvent(e) {
            // Encontrar celda actual
            const elementUnderCursor = document.elementFromPoint(
                e.clientX,
                e.clientY
            );
            if (!elementUnderCursor) return;

            const targetCell = elementUnderCursor.closest(
                ".calendar-service-cell"
            );
            if (!targetCell) return;

            // Verificar mismo técnico
            if (
                targetCell.getAttribute("data-technician-id") !==
                startCell.getAttribute("data-technician-id")
            )
                return;

            // Actualizar celda final
            endCell = targetCell;

            // Recalcular selección
            updateCellsSelection();

            // Actualizar marcador visual con RAF para suavidad
            requestAnimationFrame(updateMarkerFromCells);

            lastUpdateTime = Date.now();
        }

        document.addEventListener("mouseup", function () {
            if (isDragging) {
                isDragging = false;
                document.body.classList.remove("is-selecting");
                calendarContainer.classList.remove("dragging-active");

                // Solo abrir modal si realmente hay una selección
                if (selectedCells.length > 0) {
                    // La funcionalidad de apertura del modal sigue siendo manejada
                    // por el script original, no necesitamos duplicarla aquí
                }
            }
        });

        // Funciones auxiliares
        function updateCellsSelection() {
            // Limpiar selección previa
            selectedCells.forEach((c) =>
                c.classList.remove("calendar-cell-selected")
            );
            selectedCells = [];

            // Obtener todas las celdas del mismo técnico
            const technicianId = startCell.getAttribute("data-technician-id");
            const allCells = Array.from(
                document.querySelectorAll(
                    `.calendar-service-cell[data-technician-id="${technicianId}"]`
                )
            );

            // Encontrar índices
            const startIndex = allCells.indexOf(startCell);
            const endIndex = allCells.indexOf(endCell);

            if (startIndex === -1 || endIndex === -1) return;

            // Seleccionar rango
            const minIndex = Math.min(startIndex, endIndex);
            const maxIndex = Math.max(startIndex, endIndex);

            for (let i = minIndex; i <= maxIndex; i++) {
                const cell = allCells[i];
                if (!cell.querySelector(".calendar-service")) {
                    selectedCells.push(cell);
                    cell.classList.add("calendar-cell-selected");
                } else {
                    // Encontramos un servicio existente
                    break;
                }
            }
        }

        // Cache de valores para evitar recálculos innecesarios
        let lastUpdateHash = "";
        let lastTimeText = "";

        function updateMarkerFromCells() {
            if (selectedCells.length === 0) {
                selectionMarker.style.display = "none";
                return;
            }

            const firstCell = selectedCells[0];
            const lastCell = selectedCells[selectedCells.length - 1];

            if (!firstCell || !lastCell) {
                selectionMarker.style.display = "none";
                return;
            }

            // Crear un hash simple de la selección actual para detectar cambios
            const selectionHash = `${firstCell.id}-${lastCell.id}`;

            // Si la selección no ha cambiado, no recalcular todo
            if (
                selectionHash === lastUpdateHash &&
                selectionMarker.style.display === "block"
            ) {
                return;
            }

            lastUpdateHash = selectionHash;

            // Calcular las posiciones absolutas respecto al contenedor
            const firstRect = firstCell.getBoundingClientRect();
            const lastRect = lastCell.getBoundingClientRect();
            const containerRect = calendarContainer.getBoundingClientRect();

            // Calcular posición y dimensiones relativas al contenedor
            const top =
                firstRect.top - containerRect.top + calendarContainer.scrollTop;
            const left =
                firstRect.left -
                containerRect.left +
                calendarContainer.scrollLeft;
            const width = firstRect.width;
            const height = lastRect.bottom - firstRect.top;

            // Obtener información de horario
            const startHour = firstCell.getAttribute("data-hour");
            const startMinute = firstCell.getAttribute("data-minute");
            const endHour = lastCell.getAttribute("data-hour");
            const endMinute = lastCell.getAttribute("data-minute");

            // Calcular hora final correctamente (30 minutos después de la última celda)
            let displayEndHour = endHour;
            let displayEndMinute = endMinute;

            if (endMinute === "30") {
                displayEndHour = parseInt(endHour) + 1;
                displayEndMinute = "00";
            } else {
                displayEndMinute = "30";
            }

            const startTimeStr = `${startHour.padStart(
                2,
                "0"
            )}:${startMinute.padStart(2, "0")}`;
            const endTimeStr = `${displayEndHour
                .toString()
                .padStart(2, "0")}:${displayEndMinute}`;
            const timeText = `${startTimeStr} - ${endTimeStr}`;

            // Usar transform en lugar de top/left para mejor rendimiento
            selectionMarker.style.transform = `translate3d(0,0,0)`;

            // Aplicar hardware acceleration para movimiento más suave
            selectionMarker.style.backfaceVisibility = "hidden";

            // Posicionar el marcador
            selectionMarker.style.top = `${top}px`;
            selectionMarker.style.left = `${left}px`;
            selectionMarker.style.width = `${width}px`;
            selectionMarker.style.height = `${height}px`;
            selectionMarker.style.display = "block";

            // Actualizar texto de hora solo si cambia
            if (timeText !== lastTimeText) {
                selectionMarker.querySelector(".time-display").textContent =
                    timeText;
                lastTimeText = timeText;
            }
        }

        function showSimpleMarker(rect, timeText) {
            // Calcular posición relativa al contenedor del calendario
            const containerRect = calendarContainer.getBoundingClientRect();
            const top =
                rect.top - containerRect.top + calendarContainer.scrollTop;
            const left =
                rect.left - containerRect.left + calendarContainer.scrollLeft;

            selectionMarker.style.top = `${top}px`;
            selectionMarker.style.left = `${left}px`;
            selectionMarker.style.width = `${rect.width}px`;
            selectionMarker.style.height = `${rect.height}px`;
            selectionMarker.style.display = "block";

            selectionMarker.querySelector(".time-display").textContent =
                timeText;
        }

        function hideAllOtherMarkers() {
            // Ocultar cualquier otro marcador que pueda estar visible
            const otherMarkers = document.querySelectorAll(
                "#advanced-selection-marker, #definitive-selection-border"
            );

            otherMarkers.forEach((marker) => {
                marker.style.display = "none";
            });
        }
    }, 500);
});
