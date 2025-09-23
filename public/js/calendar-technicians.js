/**
 * Funciones para el calendario de técnicos ENSEK
 * Versión: 1.0.0
 *
 * Este archivo contiene las funciones principales para la gestión del
 * calendario de técnicos con estructura de horas en filas y técnicos en columnas.
 */

document.addEventListener("DOMContentLoaded", function () {
    // Inicializar el calendario
    initCalendar();

    // Configurar controladores de eventos
    setupEventHandlers();

    // Actualizar indicador de hora actual
    updateCurrentTimeIndicator();
    setInterval(updateCurrentTimeIndicator, 60000); // Actualizar cada minuto
});

/**
 * Inicializa el calendario con la fecha actual
 */
function initCalendar() {
    // Configurar la fecha actual
    const today = new Date();
    updateCalendarDate(today);

    // Cargar datos de servicios para la fecha actual
    loadServices(formatDate(today));
}

/**
 * Configura los controladores de eventos para los botones del calendario
 */
function setupEventHandlers() {
    // Botones de navegación
    document.getElementById("prev-day").addEventListener("click", function () {
        navigateCalendar(-1);
    });

    document.getElementById("next-day").addEventListener("click", function () {
        navigateCalendar(1);
    });

    document.getElementById("today").addEventListener("click", function () {
        const today = new Date();
        updateCalendarDate(today);
        loadServices(formatDate(today));
    });

    // Delegar eventos para servicios (se aplica a servicios añadidos dinámicamente)
    document.addEventListener("click", function (event) {
        const serviceElement = event.target.closest(".calendar-service");
        if (serviceElement) {
            const serviceId = serviceElement.getAttribute("data-service-id");
            if (serviceId) {
                showServiceDetails(serviceId);
            }
        }
    });

    // Eventos para celdas de servicio (para crear nuevos servicios)
    document.querySelectorAll(".calendar-service-cell").forEach((cell) => {
        cell.addEventListener("dblclick", function (event) {
            if (!event.target.closest(".calendar-service")) {
                const hour = cell.parentElement.querySelector(
                    ".calendar-hour-cell"
                ).textContent;
                const technicianId = cell.getAttribute("data-technician-id");
                openNewServiceModal(hour, technicianId);
            }
        });
    });
}

/**
 * Navega el calendario un día hacia adelante o hacia atrás
 * @param {number} direction - Dirección de navegación (-1 para atrás, 1 para adelante)
 */
function navigateCalendar(direction) {
    const currentDateEl = document.querySelector(".current-date");
    const currentDateStr = currentDateEl.getAttribute("data-date");
    const currentDate = new Date(currentDateStr);

    // Avanzar o retroceder un día
    currentDate.setDate(currentDate.getDate() + direction);

    // Actualizar fecha y cargar servicios
    updateCalendarDate(currentDate);
    loadServices(formatDate(currentDate));
}

/**
 * Actualiza la visualización de la fecha del calendario
 * @param {Date} date - La fecha a mostrar
 */
function updateCalendarDate(date) {
    const options = {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
    };
    const formattedDate = date.toLocaleDateString("es-ES", options);
    const capitalizedDate =
        formattedDate.charAt(0).toUpperCase() + formattedDate.slice(1);

    const currentDateEl = document.querySelector(".current-date");
    currentDateEl.textContent = capitalizedDate;
    currentDateEl.setAttribute("data-date", date.toISOString().split("T")[0]);
}

/**
 * Carga los servicios para la fecha especificada
 * @param {string} date - Fecha en formato YYYY-MM-DD
 */
function loadServices(date) {
    // Mostrar indicador de carga
    showLoadingIndicator(true);

    // Realizar petición AJAX para obtener los servicios
    fetch(`/api/calendar-services?date=${date}`)
        .then((response) => response.json())
        .then((data) => {
            // Limpiar servicios existentes
            clearServices();

            // Renderizar los nuevos servicios
            renderServices(data.services);

            // Ocultar indicador de carga
            showLoadingIndicator(false);
        })
        .catch((error) => {
            console.error("Error al cargar los servicios:", error);
            showLoadingIndicator(false);
            showError(
                "No se pudieron cargar los servicios. Intente nuevamente."
            );
        });
}

/**
 * Elimina todos los servicios del calendario
 */
function clearServices() {
    document.querySelectorAll(".calendar-service").forEach((service) => {
        service.remove();
    });
}

/**
 * Muestra u oculta el indicador de carga
 * @param {boolean} show - Indica si se debe mostrar u ocultar
 */
function showLoadingIndicator(show) {
    // Implementar según la interfaz de usuario
    // Por ejemplo, mostrar/ocultar un spinner o un overlay
}

/**
 * Muestra un mensaje de error
 * @param {string} message - Mensaje de error
 */
function showError(message) {
    // Implementar según la interfaz de usuario
    alert(message);
}

/**
 * Renderiza los servicios en el calendario
 * @param {Array} services - Array de objetos de servicio
 */
function renderServices(services) {
    services.forEach((service) => {
        // Encontrar la celda correspondiente
        const hourCell = getHourCell(service.start_time);
        const technicianCell = getTechnicianCell(
            hourCell,
            service.technician_id
        );

        if (technicianCell) {
            // Crear elemento de servicio
            const serviceElement = createServiceElement(service);

            // Añadir al calendario
            technicianCell.appendChild(serviceElement);
        }
    });
}

/**
 * Obtiene la celda de hora correspondiente a la hora dada
 * @param {string} time - Hora en formato HH:MM:SS
 * @returns {Element} - La celda de hora
 */
function getHourCell(time) {
    const hour = parseInt(time.split(":")[0]);
    const hourCells = document.querySelectorAll(".calendar-hour-cell");

    // Encontrar la celda de hora correspondiente
    for (const cell of hourCells) {
        const cellHour = parseHour(cell.textContent);
        if (cellHour === hour) {
            return cell.parentElement;
        }
    }

    return null;
}

/**
 * Extrae la hora de un texto con formato "HH:MM AM/PM"
 * @param {string} timeString - Cadena de texto con la hora
 * @returns {number} - Hora en formato 24h
 */
function parseHour(timeString) {
    // Extraer la parte de la hora
    const match = timeString.match(/(\d+)(?::(\d+))?\s*(am|pm)?/i);
    if (!match) return 0;

    let hour = parseInt(match[1]);
    const isPM = match[3] && match[3].toLowerCase() === "pm";

    // Convertir a formato 24h
    if (isPM && hour < 12) {
        hour += 12;
    } else if (!isPM && hour === 12) {
        hour = 0;
    }

    return hour;
}

/**
 * Obtiene la celda de técnico correspondiente
 * @param {Element} hourRow - Fila de hora
 * @param {string|number} technicianId - ID del técnico
 * @returns {Element} - La celda del técnico
 */
function getTechnicianCell(hourRow, technicianId) {
    if (!hourRow) return null;

    const serviceCells = hourRow.querySelectorAll(".calendar-service-cell");
    for (let i = 0; i < serviceCells.length; i++) {
        if (
            serviceCells[i].getAttribute("data-technician-id") == technicianId
        ) {
            return serviceCells[i];
        }
    }

    return null;
}

/**
 * Crea un elemento DOM para un servicio
 * @param {Object} service - Datos del servicio
 * @returns {Element} - Elemento DOM del servicio
 */
function createServiceElement(service) {
    const serviceElement = document.createElement("div");
    serviceElement.className = "calendar-service";
    serviceElement.setAttribute("data-service-id", service.id);

    // Añadir clases según el tipo y estado del servicio
    if (service.type) {
        serviceElement.classList.add("service-" + service.type.toLowerCase());
    }

    if (service.status) {
        serviceElement.classList.add("status-" + service.status.toLowerCase());
    }

    if (service.confirmation_status) {
        serviceElement.classList.add(
            "confirmation-" + service.confirmation_status
        );
    }

    // Contenido del servicio
    serviceElement.innerHTML = `
        <div class="service-title">${service.title}</div>
        ${
            service.client_name
                ? `<div class="service-client">${service.client_name}</div>`
                : ""
        }
        <div class="service-time">${formatServiceTime(
            service.start_time,
            service.end_time
        )}</div>
    `;

    return serviceElement;
}

/**
 * Formatea la hora de inicio y fin de un servicio
 * @param {string} startTime - Hora de inicio en formato HH:MM:SS
 * @param {string} endTime - Hora de fin en formato HH:MM:SS
 * @returns {string} - Horas formateadas
 */
function formatServiceTime(startTime, endTime) {
    const formatTime = (time) => {
        if (!time) return "";

        const [hours, minutes] = time.split(":");
        const hour = parseInt(hours);
        const period = hour >= 12 ? "PM" : "AM";
        const displayHour = hour > 12 ? hour - 12 : hour === 0 ? 12 : hour;

        return `${displayHour}:${minutes} ${period}`;
    };

    return `${formatTime(startTime)}${
        endTime ? " - " + formatTime(endTime) : ""
    }`;
}

/**
 * Formatea una fecha como YYYY-MM-DD
 * @param {Date} date - Objeto fecha
 * @returns {string} - Fecha formateada
 */
function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const day = String(date.getDate()).padStart(2, "0");

    return `${year}-${month}-${day}`;
}

/**
 * Abre el modal para crear un nuevo servicio
 * @param {string} hour - Hora seleccionada
 * @param {string|number} technicianId - ID del técnico seleccionado
 */
function openNewServiceModal(hour, technicianId) {
    // Implementar según la interfaz de usuario
    // Por ejemplo, abrir un modal Bootstrap con los datos preseleccionados
    console.log(
        `Abrir modal para nuevo servicio: Hora=${hour}, Técnico=${technicianId}`
    );
}

/**
 * Muestra los detalles de un servicio
 * @param {string|number} serviceId - ID del servicio
 */
function showServiceDetails(serviceId) {
    // Implementar según la interfaz de usuario
    // Por ejemplo, abrir un modal con los detalles del servicio
    console.log(`Mostrar detalles del servicio: ID=${serviceId}`);
}

/**
 * Actualiza la posición del indicador de hora actual
 */
function updateCurrentTimeIndicator() {
    const now = new Date();
    const hours = now.getHours();
    const minutes = now.getMinutes();
    const startHour = 8; // Hora de inicio del calendario (8 AM)
    const hourHeight = 60; // Altura en píxeles de cada hora en el calendario

    // Verificar si estamos dentro del horario visible
    if (hours < startHour || hours >= startHour + 12) {
        // Ocultar indicador si estamos fuera del horario
        document
            .querySelector(".current-time-indicator")
            ?.classList.add("d-none");
        return;
    }

    // Mostrar y posicionar el indicador
    const indicator = document.querySelector(".current-time-indicator");
    if (indicator) {
        indicator.classList.remove("d-none");

        // Calcular posición
        const minutesSinceStart = (hours - startHour) * 60 + minutes;
        const topPosition = (minutesSinceStart / 60) * hourHeight;

        // Aplicar posición
        indicator.style.top = `${topPosition}px`;
    }
}
