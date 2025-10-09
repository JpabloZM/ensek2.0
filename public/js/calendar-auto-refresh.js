/**
 * Calendar Auto Refresh
 * Este script permite refrescar el calendario automáticamente después de guardar un agendamiento
 */
document.addEventListener("DOMContentLoaded", function () {
    console.log("🔄 Inicializando refresco automático del calendario");

    // Función para refrescar el calendario
    window.refreshCalendar = function () {
        console.log("🔄 Refrescando calendario...");

        // Si hay un objeto calendar global, refrescar eventos
        if (window.calendar) {
            window.calendar.refetchEvents();
            console.log("✅ Eventos del calendario refrescados");
            return true;
        }
        // Si no hay objeto calendar, recargar la página
        else {
            console.log(
                "⚠️ Objeto calendar no encontrado, recargando página..."
            );
            window.location.reload();
            return false;
        }
    };

    // Refrescar al cerrar cualquier modal
    document.querySelectorAll(".modal").forEach((modal) => {
        modal.addEventListener("hidden.bs.modal", function (e) {
            console.log("📢 Modal cerrado, refrescando datos...");
            setTimeout(window.refreshCalendar, 300); // Pequeño retraso para asegurar que todo está listo
        });
    });

    // Botón de refrescar calendario
    const refreshBtn = document.getElementById("refreshCalendar");
    if (refreshBtn) {
        refreshBtn.addEventListener("click", function (e) {
            e.preventDefault();
            window.refreshCalendar();
        });
    }

    // Auto-recargar calendario cada 5 minutos
    setInterval(window.refreshCalendar, 300000); // 300000 ms = 5 minutos
});
