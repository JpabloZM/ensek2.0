/**
 * Service Request Scheduler
 * Script para agendar solicitudes de servicio desde la lista de pendientes
 * Versión 1.1 - Con soporte para calendario
 */
document.addEventListener("DOMContentLoaded", function () {
    console.log("🔄 Inicializando Service Request Scheduler v1.1");

    // Manejar clic en botones de agendar desde solicitud
    const scheduleButtons = document.querySelectorAll(".schedule-from-request");

    scheduleButtons.forEach((button) => {
        button.addEventListener("click", function (e) {
            e.preventDefault();

            // Recoger datos de la solicitud de servicio
            const requestData = {
                id: this.dataset.serviceRequestId,
                clientName: this.dataset.clientName,
                clientPhone: this.dataset.clientPhone,
                clientEmail: this.dataset.clientEmail,
                serviceId: this.dataset.serviceId,
                serviceName: this.dataset.serviceName,
                description: this.dataset.description,
                address: this.dataset.address,
            };

            console.log("📋 Datos de solicitud para agendar:", requestData);

            // Función para mostrar diálogo con opciones de agendamiento
            showSchedulingDialog(requestData);
        });
    });

    /**
     * Muestra el diálogo para agendar una solicitud
     */
    function showSchedulingDialog(requestData) {
        // Obtener la fecha actual para valores predeterminados
        const now = new Date();
        now.setMinutes(Math.ceil(now.getMinutes() / 15) * 15); // Redondear a los próximos 15 min

        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, "0");
        const day = String(now.getDate()).padStart(2, "0");
        const hours = String(now.getHours()).padStart(2, "0");
        const minutes = String(now.getMinutes()).padStart(2, "0");

        const defaultDate = `${year}-${month}-${day}T${hours}:${minutes}`;

        // Abrir modal con sweetalert2
        Swal.fire({
            title: "Agendar Solicitud",
            html: `
                <div class="text-start">
                    <div class="mb-3">
                        <span class="badge bg-success">Cliente</span>
                        <h6>${requestData.clientName}</h6>
                    </div>
                    <div class="mb-3">
                        <span class="badge bg-primary">Servicio</span>
                        <h6>${requestData.serviceName}</h6>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="form-label">Seleccione un técnico</label>
                        <select id="swal-technician-select" class="form-select"></select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha y hora</label>
                        <input type="datetime-local" id="swal-datetime-select" class="form-control" value="${defaultDate}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Duración (minutos)</label>
                        <input type="number" id="swal-duration-select" class="form-control" value="60" min="15" step="15">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notas adicionales</label>
                        <textarea id="swal-notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            `,
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: "Agendar",
            confirmButtonColor: "#87c947",
            cancelButtonText: "Cancelar",
            didOpen: () => {
                // Cargar lista de técnicos
                const technicianSelect = document.getElementById(
                    "swal-technician-select"
                );
                const technicians = JSON.parse(
                    document.getElementById("technicians-data").textContent
                );

                technicians.forEach((tech) => {
                    const option = document.createElement("option");
                    option.value = tech.id;
                    option.textContent = tech.user.name;
                    technicianSelect.appendChild(option);
                });
            },
        }).then((result) => {
            if (result.isConfirmed) {
                // Recoger datos del formulario
                const technicianId = document.getElementById(
                    "swal-technician-select"
                ).value;
                const scheduledDate = document.getElementById(
                    "swal-datetime-select"
                ).value;
                const duration = document.getElementById(
                    "swal-duration-select"
                ).value;
                const notes = document.getElementById("swal-notes").value;

                if (!technicianId || !scheduledDate) {
                    Swal.fire(
                        "Error",
                        "Debe seleccionar un técnico y una fecha/hora",
                        "error"
                    );
                    return;
                }

                // Crear objeto con datos para enviar
                const scheduleData = {
                    service_request_id: requestData.id,
                    technician_id: technicianId,
                    scheduled_date: scheduledDate,
                    duration: duration,
                    notes: notes,
                };

                console.log("📅 Enviando datos para agendar:", scheduleData);

                // Mostrar indicador de carga
                Swal.fire({
                    title: "Agendando servicio...",
                    text: "Por favor espere mientras se procesa la solicitud",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });

                // Enviar datos mediante fetch API
                fetch("/admin/schedules/schedule-request", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                    },
                    body: JSON.stringify(scheduleData),
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            Swal.fire({
                                title: "¡Agendado!",
                                text: "La solicitud ha sido agendada correctamente.",
                                icon: "success",
                                confirmButtonText: "OK",
                            }).then(() => {
                                // Refrescar el calendario si existe
                                if (
                                    typeof window.refreshCalendar === "function"
                                ) {
                                    window.refreshCalendar();
                                } else {
                                    // Si no hay función de refresco, recargar la página
                                    window.location.reload();
                                }
                            });
                        } else {
                            Swal.fire(
                                "Error",
                                data.message ||
                                    "Hubo un error al agendar la solicitud",
                                "error"
                            );
                        }
                    })
                    .catch((error) => {
                        console.error("Error agendando solicitud:", error);
                        Swal.fire(
                            "Error",
                            "Hubo un error en la comunicación con el servidor",
                            "error"
                        );
                    });
            }
        });
    }
});
