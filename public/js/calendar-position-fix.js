/**
 * Script para corregir y verificar posicionamiento del calendario
 * Este script se carga después de todos los demás para asegurar la correcta visualización
 */

document.addEventListener("DOMContentLoaded", function () {
    // Esperar a que todo esté cargado
    setTimeout(function () {
        console.log("Verificando posicionamiento del marcador de selección");

        // Asegurarse que el contenedor del calendario tenga posición relativa
        const calendarContainer = document.querySelector(
            ".technician-calendar-container"
        );
        if (calendarContainer) {
            calendarContainer.style.position = "relative";

            // Observar cambios en el DOM para detectar selecciones
            const observer = new MutationObserver(function (mutations) {
                // Buscar el marcador de selección
                const marker = document.querySelector(
                    "#simple-selection-marker"
                );
                if (marker && marker.style.display !== "none") {
                    // Asegurarse que está posicionado correctamente
                    const selectedCells = document.querySelectorAll(
                        ".calendar-cell-selected"
                    );
                    if (selectedCells.length > 0) {
                        const firstCell = selectedCells[0];
                        const lastCell =
                            selectedCells[selectedCells.length - 1];

                        if (firstCell && lastCell) {
                            const firstRect = firstCell.getBoundingClientRect();
                            const lastRect = lastCell.getBoundingClientRect();
                            const containerRect =
                                calendarContainer.getBoundingClientRect();

                            // Calcular posición correcta
                            const top =
                                firstRect.top -
                                containerRect.top +
                                calendarContainer.scrollTop;
                            const left =
                                firstRect.left -
                                containerRect.left +
                                calendarContainer.scrollLeft;
                            const width = firstRect.width;
                            const height = lastRect.bottom - firstRect.top;

                            // Si la posición es diferente, corregirla
                            if (
                                Math.abs(parseInt(marker.style.top) - top) >
                                    5 ||
                                Math.abs(parseInt(marker.style.left) - left) > 5
                            ) {
                                console.log(
                                    "Corrigiendo posición del marcador"
                                );
                                marker.style.top = `${top}px`;
                                marker.style.left = `${left}px`;
                                marker.style.width = `${width}px`;
                                marker.style.height = `${height}px`;
                            }
                        }
                    }
                }
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ["style", "class"],
            });
        }
    }, 1000); // Esperar 1 segundo para que todo esté cargado
});
