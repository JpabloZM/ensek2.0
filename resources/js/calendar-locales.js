// Custom file to handle FullCalendar locales
document.addEventListener("DOMContentLoaded", function () {
    // Set Spanish locale for FullCalendar if it's being used
    if (typeof FullCalendar !== "undefined") {
        FullCalendar.globalLocales.push({
            code: "es",
            buttonText: {
                prev: "Ant",
                next: "Sig",
                today: "Hoy",
                month: "Mes",
                week: "Semana",
                day: "Día",
                list: "Agenda",
            },
            weekText: "Sm",
            allDayText: "Todo el día",
            moreLinkText: "más",
            noEventsText: "No hay eventos para mostrar",
        });
    }
});
