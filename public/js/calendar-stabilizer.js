/**
 * Estabilizador del calendario - Previene cambios de layout durante interacciones
 * Este script se ejecuta lo antes posible para prevenir saltos en el diseño
 */
(function () {
    // Ejecutar inmediatamente para prevenir FOUC (Flash of Unstyled Content)
    document.addEventListener("DOMContentLoaded", function () {
        // Aplicar fijación de tamaños al contenedor principal
        const calendar = document.querySelector(
            ".technician-calendar-container"
        );
        if (!calendar) return;

        // Guardar las dimensiones iniciales para prevenir cambios
        const setFixedDimensions = function () {
            // Obtener dimensiones
            const containerWidth = calendar.offsetWidth;

            // Fijar el ancho total
            calendar.style.width = containerWidth + "px";

            // Evitar ajustes automáticos
            calendar.style.tableLayout = "fixed";
            calendar.style.maxWidth = "100%";

            // Identificar las celdas de técnicos
            const techCells = document.querySelectorAll(
                ".calendar-header-tech"
            );
            if (techCells.length > 0) {
                // Calcular ancho de celda para cada técnico
                const availableWidth = containerWidth - 80; // Restar el ancho de la columna de hora
                const cellWidth = availableWidth / techCells.length;

                // Aplicar ancho fijo a cada celda
                techCells.forEach((cell) => {
                    cell.style.width = cellWidth + "px";
                    cell.style.minWidth = cellWidth + "px";
                    cell.style.maxWidth = cellWidth + "px";
                });

                // Aplicar el mismo ancho a las celdas de servicio
                const techIds = Array.from(techCells).map((_, index) => index);

                techIds.forEach((techIndex) => {
                    const serviceCells = document.querySelectorAll(
                        `.calendar-service-cell:nth-child(${techIndex + 2})`
                    );
                    serviceCells.forEach((cell) => {
                        cell.style.width = cellWidth + "px";
                        cell.style.minWidth = cellWidth + "px";
                        cell.style.maxWidth = cellWidth + "px";
                    });
                });
            }
        };

        // Primera aplicación inmediata
        setFixedDimensions();

        // Reajustar después de un breve retraso para asegurar carga completa
        setTimeout(setFixedDimensions, 200);

        // Desactivar temporalmente transiciones durante interacciones
        const disableTransitionsDuringInteraction = function () {
            document
                .querySelectorAll(
                    ".calendar-service-cell, .calendar-header-tech"
                )
                .forEach((cell) => {
                    cell.addEventListener("mouseenter", () => {
                        cell.classList.add("interaction-active");
                    });

                    cell.addEventListener("mouseleave", () => {
                        cell.classList.remove("interaction-active");
                    });
                });

            // Detectar cuando se inicia un arrastre y fijar todo el diseño
            document.addEventListener("mousedown", function (e) {
                if (e.button !== 0) return; // Solo con botón izquierdo

                const targetCell = e.target.closest(".calendar-service-cell");
                if (
                    targetCell &&
                    !targetCell.querySelector(".calendar-service")
                ) {
                    // Aplicar clase para fijar todo el diseño
                    calendar.classList.add("dragging-active");

                    // Fijar dimensiones una vez más para asegurar estabilidad
                    setFixedDimensions();
                }
            });

            // Restaurar cuando se suelta el ratón
            document.addEventListener("mouseup", function () {
                calendar.classList.remove("dragging-active");
            });
        };

        disableTransitionsDuringInteraction();

        // Crear estilos dinámicos para garantizar estabilidad
        const styleEl = document.createElement("style");
        styleEl.textContent = `
            .technician-calendar-container {
                table-layout: fixed !important;
            }
            .calendar-service-cell.interaction-active {
                transition: none !important;
                transform: none !important;
            }
            .technician-calendar-container.dragging-active {
                width: 100% !important;
                max-width: 100% !important;
            }
            .dragging-active .calendar-service-cell {
                transition: none !important;
                transform: none !important;
                box-shadow: none !important;
            }
            .dragging-active .calendar-service-cell:hover {
                background-color: inherit !important;
                transform: none !important;
                box-shadow: none !important;
            }
        `;
        document.head.appendChild(styleEl);
    });
})();
