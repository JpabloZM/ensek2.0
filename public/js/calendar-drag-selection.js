/**
 * Funcionalidad de arrastrar para seleccionar rango de tiempo en el calendario
 *
 * Este script implementa:
 * 1. Selección por arrastre de celdas en el calendario
 * 2. Apertura de modal para crear servicio con datos precargados
 * 3. Visualización del rango seleccionado
 */

document.addEventListener("DOMContentLoaded", function () {
    let isDragging = false;
    let startCell = null;
    let endCell = null;
    let selectedCells = [];
    let dragTimeout = null;
    let dragDataReady = false;
    let mouseDownTimestamp = 0; // Para detectar clics vs. arrastres

    const calendarContainer = document.querySelector(
        ".technician-calendar-container"
    );
    if (!calendarContainer) return;

    // Agregar la clase para el cursor personalizado al contenedor
    calendarContainer.classList.add("calendar-selectable");

    // Desactivar doble clic para evitar conflictos
    document.querySelectorAll(".calendar-service-cell").forEach((cell) => {
        cell.ondblclick = (e) => e.preventDefault();
    });

    // Crear elemento para visualizar el rango seleccionado
    const selectionOverlay = document.createElement("div");
    selectionOverlay.classList.add("calendar-selection-overlay");
    selectionOverlay.style.display = "none";
    calendarContainer.appendChild(selectionOverlay);

    // Deshabilitar cualquier evento de clic existente que pudiera interferir
    document.querySelectorAll(".calendar-service-cell").forEach((cell) => {
        // Eliminar eventos de clic existentes (solución para prevenir apertura de formulario)
        const oldCell = cell.cloneNode(true);
        cell.parentNode.replaceChild(oldCell, cell);
    });

    // Eventos para iniciar el arrastre
    document.querySelectorAll(".calendar-service-cell").forEach((cell) => {
        // Prevenir eventos de clic simple que podrían abrir modales
        cell.addEventListener(
            "click",
            function (e) {
                // Solo prevenir si estamos en modo de selección
                if (isDragging) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            },
            true
        );

        cell.addEventListener(
            "mousedown",
            function (e) {
                if (e.button !== 0) return; // Solo botón izquierdo

                // Verificar si la celda ya contiene un servicio
                const hasExistingService =
                    cell.querySelector(".calendar-service");
                if (hasExistingService) return;

                // Marcar el inicio del arrastre y registrar el tiempo para diferenciar clic de arrastre
                isDragging = true;
                mouseDownTimestamp = Date.now();
                startCell = cell;
                endCell = cell;

                // Añadir clase al body para prevenir scroll durante el arrastre
                document.body.classList.add("calendar-dragging");

                // Limpiar selecciones previas
                clearSelection();

                // Agregar la primera celda a la selección
                addCellToSelection(cell);

                // Mostrar overlay de selección
                updateSelectionOverlay();

                // Prevenir eventos de texto seleccionable y cualquier otro evento
                e.preventDefault();
                e.stopPropagation();
            },
            true
        );
    });

    // Evento para actualizar la selección durante el arrastre
    document.addEventListener("mousemove", function (e) {
        if (!isDragging || !startCell) return;

        // Prevenir eventos predeterminados durante el arrastre
        e.preventDefault();
        e.stopPropagation();

        // Evitar la selección de texto durante el arrastre
        if (window.getSelection) {
            if (window.getSelection().empty) {
                // Chrome
                window.getSelection().empty();
            } else if (window.getSelection().removeAllRanges) {
                // Firefox
                window.getSelection().removeAllRanges();
            }
        }

        // Limitar la frecuencia de actualizaciones para mejor rendimiento
        if (dragTimeout) clearTimeout(dragTimeout);
        dragTimeout = setTimeout(() => {
            // Asegurar que el estado de arrastre está activado para mantener el layout estable
            const calendarContainer = document.querySelector(
                ".technician-calendar-container"
            );
            if (calendarContainer) {
                calendarContainer.classList.add("dragging-active");

                // Prevenir cualquier cambio en el tamaño durante el arrastre
                if (!calendarContainer.hasAttribute("data-original-width")) {
                    calendarContainer.setAttribute(
                        "data-original-width",
                        calendarContainer.offsetWidth
                    );
                }
            }

            // Encontrar la celda sobre la que está el cursor usando elementFromPoint
            // Esta técnica previene problemas con eventos propagados
            const elementUnderCursor = document.elementFromPoint(
                e.clientX,
                e.clientY
            );
            if (!elementUnderCursor) return;

            // Verificar si es una celda del calendario o está dentro de una
            let targetCell = elementUnderCursor.closest(
                ".calendar-service-cell"
            );
            if (!targetCell) return;

            // Verificar que la celda esté en la misma columna (mismo técnico)
            if (
                targetCell.getAttribute("data-technician-id") !==
                startCell.getAttribute("data-technician-id")
            ) {
                return;
            }

            // Actualizar la celda final
            endCell = targetCell;

            // Recalcular todas las celdas entre inicio y fin
            clearSelection();
            selectCellsBetween(startCell, endCell);

            // Actualizar visualización de la selección
            updateSelectionOverlay();
        }, 50);
    });

    // Evento para finalizar el arrastre
    document.addEventListener("mouseup", function (e) {
        if (!isDragging) {
            return;
        }

        // Finalizar el arrastre
        isDragging = false;

        // Quitar clase del body
        document.body.classList.remove("calendar-dragging");

        // Si no hay celdas seleccionadas o no hay celda de inicio/fin, salir
        if (selectedCells.length === 0 || !startCell || !endCell) {
            clearSelection();
            return;
        }

        // Verificar que realmente se haya arrastrado (distancia mínima)
        // Esto previene que un clic simple abra el modal
        const firstCell = selectedCells[0];
        const lastCell = selectedCells[selectedCells.length - 1];

        // Si solo hay una celda seleccionada, verificar duración para determinar si es clic o arrastre
        if (
            selectedCells.length === 1 &&
            firstCell === startCell &&
            firstCell === endCell
        ) {
            const mouseUpTimestamp = Date.now();
            const clickDuration = mouseUpTimestamp - mouseDownTimestamp;

            // Si la duración del "clic" es mayor a 400ms, considerarlo un arrastre intencional
            // Aumentamos el tiempo para evitar falsos positivos
            if (clickDuration > 400) {
                // Antes de abrir el modal, restaurar cualquier estilo que pudiera haberse alterado
                document.querySelectorAll(".dragging-active").forEach((el) => {
                    el.classList.remove("dragging-active");
                });

                prepareModalData();
            } else {
                // Si fue un clic rápido, probablemente no era intencional
                clearSelection();
            }
        } else {
            // Si hay múltiples celdas, es un arrastre claro, abrir modal
            // Primero restaurar cualquier estilo que pudiera haberse alterado
            document.querySelectorAll(".dragging-active").forEach((el) => {
                el.classList.remove("dragging-active");
            });

            prepareModalData();
        }

        // Restaurar el ancho del contenedor y limpiar cualquier estilo temporal
        document
            .querySelectorAll(".technician-calendar-container")
            .forEach((container) => {
                container.classList.remove("dragging-active");
            });
    });

    /**
     * Agregar una celda a la selección
     */
    function addCellToSelection(cell) {
        if (!selectedCells.includes(cell)) {
            selectedCells.push(cell);
            cell.classList.add("calendar-cell-selected");
        }
    }

    /**
     * Limpiar toda la selección actual
     */
    function clearSelection() {
        selectedCells.forEach((cell) => {
            cell.classList.remove("calendar-cell-selected");
        });
        selectedCells = [];
        selectionOverlay.style.display = "none";

        // Quitar la clase de selección activa
        document.body.classList.remove("active-selection");
    }

    /**
     * Seleccionar todas las celdas entre la celda inicial y final
     */
    function selectCellsBetween(start, end) {
        // Obtener todas las celdas del calendario del mismo técnico
        const technicianId = start.getAttribute("data-technician-id");
        const allCells = Array.from(
            document.querySelectorAll(
                `.calendar-service-cell[data-technician-id="${technicianId}"]`
            )
        );

        // Encontrar los índices de las celdas de inicio y fin
        const startIndex = allCells.indexOf(start);
        const endIndex = allCells.indexOf(end);

        if (startIndex === -1 || endIndex === -1) return;

        // Determinar el rango correcto independientemente de la dirección del arrastre
        const minIndex = Math.min(startIndex, endIndex);
        const maxIndex = Math.max(startIndex, endIndex);

        // Seleccionar todas las celdas en el rango
        for (let i = minIndex; i <= maxIndex; i++) {
            // Verificar que la celda no tenga un servicio existente
            const cell = allCells[i];
            const hasExistingService = cell.querySelector(".calendar-service");

            if (!hasExistingService) {
                addCellToSelection(cell);
            } else {
                // Si encontramos un servicio existente, detenemos la selección en ese punto
                break;
            }
        }
    }

    /**
     * Actualizar la visualización del overlay de selección
     */
    function updateSelectionOverlay() {
        if (selectedCells.length === 0) {
            selectionOverlay.style.display = "none";
            return;
        }

        // Aplicar clase especial para indicar selección activa
        document.body.classList.add("active-selection");

        // Encontrar las coordenadas de la primera y última celda seleccionada
        const firstCell = selectedCells[0];
        const lastCell = selectedCells[selectedCells.length - 1];

        if (!firstCell || !lastCell) return;

        const firstRect = firstCell.getBoundingClientRect();
        const lastRect = lastCell.getBoundingClientRect();
        const containerRect = calendarContainer.getBoundingClientRect();

        // Asegurar que el ancho del calendario se mantenga durante el arrastre
        document
            .querySelectorAll(".technician-calendar-container")
            .forEach((container) => {
                // Calcular el ancho total necesario basado en el contenido
                const hourCell = container.querySelector(".calendar-hour-cell");
                const serviceCells = container.querySelectorAll(
                    '.calendar-service-cell[data-hour="0"]'
                );
                if (!hourCell) return;

                // Asegurarnos que no haya espacio en blanco en el lateral
                container.style.width = "100%";
                container.style.maxWidth = "100%";
            });

        // Calcular posición relativa al contenedor
        const top =
            firstRect.top - containerRect.top + calendarContainer.scrollTop;
        const left = firstRect.left - containerRect.left;
        const width = firstRect.width;
        const height = lastRect.top + lastRect.height - firstRect.top;

        // Actualizar el overlay con posiciones absolutas
        selectionOverlay.style.display = "block";
        selectionOverlay.style.position = "absolute";
        selectionOverlay.style.top = `${top}px`;
        selectionOverlay.style.left = `${left}px`;
        selectionOverlay.style.width = `${width}px`;
        selectionOverlay.style.height = `${height}px`;
        selectionOverlay.style.pointerEvents = "none";

        // Actualizar el texto dentro del overlay para mostrar el rango horario
        const startHour = firstCell.getAttribute("data-hour");
        const startMinute = firstCell.getAttribute("data-minute");
        const endHour = lastCell.getAttribute("data-hour");
        const endMinute = lastCell.getAttribute("data-minute");

        // Calcular el tiempo final correctamente (30 minutos después de la última celda)
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

        // Mostrar hora en un formato simple centrado en el overlay
        selectionOverlay.innerHTML = `
            <div class="selection-time">
                ${startTimeStr} - ${endTimeStr}
            </div>
        `;
    }

    /**
     * Preparar datos para abrir el modal con información precargada
     */
    function prepareModalData() {
        if (selectedCells.length === 0) return;

        // Obtener datos de la primera y última celda
        const firstCell = selectedCells[0];
        const lastCell = selectedCells[selectedCells.length - 1];

        const startHour = parseInt(firstCell.getAttribute("data-hour"));
        const startMinute = parseInt(firstCell.getAttribute("data-minute"));
        const endHour = parseInt(lastCell.getAttribute("data-hour"));
        const endMinute = parseInt(lastCell.getAttribute("data-minute"));
        const technicianId = firstCell.getAttribute("data-technician-id");

        // Calcular la hora de fin (30 minutos después de la última celda)
        let finalEndHour = endHour;
        let finalEndMinute = endMinute;

        if (endMinute === 30) {
            finalEndHour = endHour + 1;
            finalEndMinute = 0;
        } else {
            finalEndMinute = 30;
        }

        // Formatear las horas para el modal
        const startTimeStr = `${startHour
            .toString()
            .padStart(2, "0")}:${startMinute.toString().padStart(2, "0")}`;
        const endTimeStr = `${finalEndHour
            .toString()
            .padStart(2, "0")}:${finalEndMinute.toString().padStart(2, "0")}`;

        // Obtener la fecha actual del calendario
        const currentDate =
            document
                .getElementById("current-date")
                ?.getAttribute("data-date") ||
            new Date().toISOString().split("T")[0];

        // Abrir el modal con estos datos
        openNewServiceModalWithRange(
            currentDate,
            startTimeStr,
            endTimeStr,
            technicianId
        );

        // Ocultar la selección después de un breve retraso
        setTimeout(() => {
            clearSelection();
        }, 300);
    }

    /**
     * Abrir el modal con los datos del rango horario seleccionado
     */
    function openNewServiceModalWithRange(
        date,
        startTime,
        endTime,
        technicianId
    ) {
        const modal = document.getElementById("newScheduleModal");
        if (!modal) return;

        // Configurar datos en el formulario
        const startDateInput = document.getElementById("scheduled_date");
        const endDateInput = document.getElementById("end_date");
        const techSelect = document.getElementById("technician_id");
        const statusSelect = document.getElementById("status");

        // Formatear fechas con horas para los inputs
        if (startDateInput) startDateInput.value = `${date}T${startTime}`;
        if (endDateInput) endDateInput.value = `${date}T${endTime}`;
        if (techSelect) techSelect.value = technicianId;

        // Establecer estado por defecto como "Pendiente"
        if (statusSelect) {
            // Buscar la opción que contenga "pendiente" (insensible a mayúsculas/minúsculas)
            for (let i = 0; i < statusSelect.options.length; i++) {
                const option = statusSelect.options[i];
                if (option.text.toLowerCase().includes("pendiente")) {
                    statusSelect.selectedIndex = i;
                    break;
                }
            }
        }

        // Actualizar título del modal para reflejar el rango horario
        const modalTitle = modal.querySelector(".modal-title");
        if (modalTitle) {
            modalTitle.textContent = `Nuevo Servicio - ${startTime} a ${endTime}`;
        }

        // Abrir el modal
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    }
});
