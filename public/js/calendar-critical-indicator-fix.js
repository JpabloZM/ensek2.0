/**
 * Solución crítica para el indicador de hora en FullCalendar
 * Este script asegura que la línea del indicador nunca se muestre por encima de los encabezados
 * con un enfoque más agresivo y múltiples estrategias redundantes
 */

document.addEventListener("DOMContentLoaded", function () {
    // Función principal para corregir el indicador
    function fixTimeIndicator() {
        // 1. Modificar directamente todos los indicadores existentes
        const indicators = document.querySelectorAll(
            ".fc-timeline-now-indicator-line"
        );
        indicators.forEach((indicator) => {
            // Forzar z-index bajo y posicionamiento
            indicator.style.cssText +=
                "z-index: 1 !important; position: absolute !important;";

            // Mover el indicador dentro del DOM para que sea el primer hijo de su contenedor
            // esto asegura que esté detrás de todos los demás elementos
            const parent = indicator.parentElement;
            if (parent) {
                parent.insertBefore(indicator, parent.firstChild);
            }
        });

        // 2. Asegurarse que los encabezados estén por encima
        const headers = document.querySelectorAll(
            ".fc-datagrid-header, .fc-timeline-header"
        );
        headers.forEach((header) => {
            header.style.cssText +=
                "z-index: 9999 !important; position: relative !important; background: #fff !important;";
        });

        // 3. Crear una capa de superposición para los encabezados si no existe
        if (!document.getElementById("header-overlay")) {
            const headerOverlay = document.createElement("div");
            headerOverlay.id = "header-overlay";
            headerOverlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 9000;
                pointer-events: none;
                background: transparent;
            `;

            // Determinar la altura correcta basada en los encabezados
            const headerHeight =
                document.querySelector(".fc-datagrid-header")?.offsetHeight ||
                50;
            headerOverlay.style.height = headerHeight + 5 + "px";

            document.body.appendChild(headerOverlay);

            // Actualizar la altura cuando la ventana cambie de tamaño
            window.addEventListener("resize", () => {
                const newHeaderHeight =
                    document.querySelector(".fc-datagrid-header")
                        ?.offsetHeight || 50;
                headerOverlay.style.height = newHeaderHeight + 5 + "px";
            });
        }
    }

    // Ejecutar inmediatamente
    fixTimeIndicator();

    // Ejecutar después de un breve retraso para asegurarse que FullCalendar ha terminado de renderizar
    setTimeout(fixTimeIndicator, 100);
    setTimeout(fixTimeIndicator, 500);
    setTimeout(fixTimeIndicator, 1000);

    // Crear un observador de mutaciones para detectar cuando FullCalendar actualiza el indicador
    const observer = new MutationObserver((mutations) => {
        let needsFix = false;

        // Verificar si alguna mutación afecta a elementos del calendario
        mutations.forEach((mutation) => {
            if (
                mutation.target.classList &&
                (mutation.target.classList.contains("fc") ||
                    mutation.target.closest(".fc"))
            ) {
                needsFix = true;
            }

            // Verificar si se ha añadido un indicador nuevo
            mutation.addedNodes.forEach((node) => {
                if (
                    node.classList &&
                    node.classList.contains("fc-timeline-now-indicator-line")
                ) {
                    needsFix = true;
                }
            });
        });

        if (needsFix) {
            fixTimeIndicator();
        }
    });

    // Observar cambios en todo el calendario
    const calendar = document.querySelector(".fc");
    if (calendar) {
        observer.observe(calendar, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ["style", "class"],
        });
    }

    // Volver a aplicar en eventos de scroll
    document.addEventListener("scroll", fixTimeIndicator, { passive: true });

    // Aplicar también cuando la ventana cambia de tamaño
    window.addEventListener("resize", fixTimeIndicator);
});
