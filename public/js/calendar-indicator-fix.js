/**
 * Script para corregir el comportamiento del indicador de hora durante el scroll
 * Evita que se monte encima de los nombres de técnicos
 */

document.addEventListener("DOMContentLoaded", function () {
    // Esperar a que el DOM esté completamente cargado
    setTimeout(function () {
        console.log("Inicializando corrección de indicador de hora");

        // Referencias a elementos clave
        const calendarContainer = document.querySelector(
            ".technician-calendar-container"
        );
        if (!calendarContainer) return;

        // Observar el indicador de hora que crea FullCalendar
        const observer = new MutationObserver(function (mutations) {
            // Buscar el indicador de hora
            const indicators = document.querySelectorAll(
                ".fc-timeline-now-indicator-line, .fc-timeline-now-indicator-arrow"
            );

            indicators.forEach(function (indicator) {
                // Comprobar si es la flecha (etiqueta) del indicador
                if (
                    indicator.classList.contains(
                        "fc-timeline-now-indicator-arrow"
                    )
                ) {
                    // Agregar la hora actual como atributo para mostrarla en CSS
                    const now = new Date();
                    const hours = now.getHours().toString().padStart(2, "0");
                    const minutes = now
                        .getMinutes()
                        .toString()
                        .padStart(2, "0");
                    indicator.setAttribute("data-time", `${hours}:${minutes}`);
                }

                // Asegurar que el indicador tenga el z-index correcto
                indicator.style.zIndex = "1000";
            });
        });

        // Comenzar a observar el DOM para detectar cuando FullCalendar agrega el indicador
        observer.observe(document.body, {
            childList: true,
            subtree: true,
            attributes: false,
        });

        // También monitorear el evento de scroll para ajustar z-index dinámicamente
        const datagridHeader = document.querySelector(".fc-datagrid-header");
        const timelineHeader = document.querySelector(".fc-timeline-header");

        if (datagridHeader && timelineHeader) {
            calendarContainer.addEventListener("scroll", function () {
                // Asegurar que los encabezados estén por encima durante el scroll
                datagridHeader.style.zIndex = "2000";
                timelineHeader.style.zIndex = "2000";
            });
        }
    }, 1000);
});
