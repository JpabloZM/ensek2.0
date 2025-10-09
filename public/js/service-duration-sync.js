/**
 * Service Duration Synchronization
 * Script para sincronizar la duración y costo cuando se selecciona un servicio
 * Versión 2: Implementación directa con debugger
 */
document.addEventListener("DOMContentLoaded", function () {
    console.log(
        "🔄 Inicializando sincronización de duración de servicios (v2)"
    );

    // Configurar manejo directo para el modal directo
    setupDirectServiceHandler();

    // Configurar sincronización para modal de solicitud
    setupServiceSync(
        "service_id",
        "duration",
        "estimated_cost",
        null,
        "end_time",
        "scheduled_date",
        "selected-time-info"
    );

    /**
     * Configura manejador directo para el selector de servicios en el modal directo
     */
    function setupDirectServiceHandler() {
        console.log("🔧 Configurando manejador directo para servicios");

        // Esperar a que el DOM esté completamente cargado
        setTimeout(() => {
            const serviceSelect = document.getElementById("direct_service_id");
            if (!serviceSelect) {
                console.warn(
                    "⚠️ Selector de servicio directo no encontrado en este momento"
                );
                return;
            }

            console.log("✅ Selector de servicio directo encontrado");

            // Agregar listener al selector de servicios
            serviceSelect.addEventListener("change", function () {
                console.log("🔄 Cambio detectado en servicio directo");

                // Obtener la opción seleccionada
                const selectedOption = this.options[this.selectedIndex];
                if (!selectedOption || !selectedOption.value) {
                    console.log("⚠️ No hay opción seleccionada");
                    return;
                }

                // Obtener los datos del servicio
                const serviceDuration =
                    selectedOption.getAttribute("data-duration") || "60";
                const servicePrice =
                    selectedOption.getAttribute("data-price") || "0";

                console.log(
                    `📊 Servicio: ${selectedOption.text}, Duración: ${serviceDuration}, Precio: ${servicePrice}`
                );

                // Actualizar campos
                const durationInput =
                    document.getElementById("direct_duration");
                const costInput = document.getElementById(
                    "direct_estimated_cost"
                );

                if (durationInput) {
                    durationInput.value = serviceDuration;
                    console.log(`⏱️ Duración actualizada: ${serviceDuration}`);

                    // Disparar actualización manual
                    updateDirectEndTime(serviceDuration);
                }

                if (costInput) {
                    costInput.value = servicePrice;
                    console.log(`💰 Costo actualizado: ${servicePrice}`);
                }

                // Efectos visuales
                if (durationInput) {
                    durationInput.classList.add("field-highlight");
                    setTimeout(
                        () => durationInput.classList.remove("field-highlight"),
                        1000
                    );
                }

                if (costInput) {
                    costInput.classList.add("field-highlight");
                    setTimeout(
                        () => costInput.classList.remove("field-highlight"),
                        1000
                    );
                }
            });
        }, 500);
    }

    // Configurar sincronización para modal de solicitud
    setupServiceSync(
        "service_id",
        "duration",
        "estimated_cost",
        null,
        "end_time",
        "scheduled_date",
        "selected-time-info"
    );

    /**
     * Configura la sincronización entre servicio seleccionado y duración/precio
     */
    function setupServiceSync(
        serviceSelectId,
        durationInputId,
        costInputId,
        startTimeInputId,
        endTimeInputId,
        dateInputId,
        infoBoxId
    ) {
        const serviceSelect = document.getElementById(serviceSelectId);

        if (!serviceSelect) {
            console.log(
                `⚠️ Selector de servicio ${serviceSelectId} no encontrado`
            );
            return;
        }

        console.log(
            `✅ Configurando sincronización para selector de servicio: ${serviceSelectId}`
        );

        // Agregar listener para el cambio de servicio
        serviceSelect.addEventListener("change", function () {
            const selectedOption = this.options[this.selectedIndex];

            if (selectedOption && selectedOption.value) {
                // Obtener datos del servicio desde el data-attribute
                const duration =
                    selectedOption.getAttribute("data-duration") || "60";
                const price = selectedOption.getAttribute("data-price") || "0";

                console.log(
                    `🔄 Servicio seleccionado: ${selectedOption.text} - Duración: ${duration}min - Precio: $${price}`
                );

                // Actualizar campo de duración
                const durationInput = document.getElementById(durationInputId);
                if (durationInput) {
                    durationInput.value = duration;
                    console.log(
                        `⏱️ Duración actualizada a ${duration} minutos`
                    );

                    // Disparar evento de input para activar los cálculos
                    durationInput.dispatchEvent(
                        new Event("input", { bubbles: true })
                    );
                }

                // Actualizar campo de costo
                const costInput = document.getElementById(costInputId);
                if (costInput) {
                    costInput.value = price;
                    console.log(`💰 Costo actualizado a $${price}`);
                }

                // Actualizar el infoBox
                updateInfoBox(
                    dateInputId,
                    startTimeInputId,
                    endTimeInputId,
                    durationInputId,
                    infoBoxId
                );

                // Efecto visual para ambos campos
                if (durationInput)
                    durationInput.classList.add("field-highlight");
                if (costInput) costInput.classList.add("field-highlight");

                setTimeout(() => {
                    if (durationInput)
                        durationInput.classList.remove("field-highlight");
                    if (costInput)
                        costInput.classList.remove("field-highlight");
                }, 1000);
            }
        });

        console.log(
            `✅ Sincronización configurada para selector ${serviceSelectId}`
        );
    }

    /**
     * Actualiza directamente la hora de fin basada en la duración
     * Esta función es más directa y simple para resolver el problema
     */
    function updateDirectEndTime(duration) {
        console.log("🔄 Actualizando hora de fin directamente");

        const dateInput = document.getElementById("direct_scheduled_date");
        const startTimeInput = document.getElementById("direct_start_time");
        const endTimeInput = document.getElementById("direct_end_time");

        if (
            !dateInput ||
            !startTimeInput ||
            !endTimeInput ||
            !dateInput.value ||
            !startTimeInput.value
        ) {
            console.warn(
                "⚠️ Faltan campos necesarios para calcular hora de fin"
            );
            return;
        }

        try {
            // Construir fecha/hora de inicio
            const startDateTime = new Date(
                `${dateInput.value}T${startTimeInput.value}`
            );
            console.log(
                `📅 Fecha/hora inicio: ${startDateTime.toLocaleString()}`
            );

            // Calcular hora de fin
            const durationMins = parseInt(duration);
            const endDateTime = new Date(
                startDateTime.getTime() + durationMins * 60000
            );
            console.log(
                `📅 Fecha/hora fin calculada: ${endDateTime.toLocaleString()}`
            );

            // Formatear para el campo de hora
            const endHours = endDateTime.getHours().toString().padStart(2, "0");
            const endMinutes = endDateTime
                .getMinutes()
                .toString()
                .padStart(2, "0");
            const formattedEndTime = `${endHours}:${endMinutes}`;

            // Actualizar campo
            endTimeInput.value = formattedEndTime;
            console.log(`⏱️ Hora de fin establecida a: ${formattedEndTime}`);

            // Aplicar efecto visual
            endTimeInput.classList.add("field-highlight");
            setTimeout(
                () => endTimeInput.classList.remove("field-highlight"),
                1000
            );

            // Actualizar infoBox
            updateInfoBoxDirecto(startDateTime, endDateTime, durationMins);
        } catch (error) {
            console.error("❌ Error al calcular hora de fin:", error);
        }
    }

    /**
     * Actualiza el infoBox del modal directo con la información de horario
     */
    function updateInfoBoxDirecto(startDateTime, endDateTime, duration) {
        console.log("ℹ️ Actualizando infoBox directo");

        const infoBox = document.getElementById("direct-selected-time-info");
        if (!infoBox) {
            console.warn("⚠️ InfoBox directo no encontrado");
            return;
        }

        const infoText = infoBox.querySelector(".selected-time-text");
        if (!infoText) {
            console.warn("⚠️ Texto de información no encontrado en infoBox");
            return;
        }

        // Formatear horas para mostrar
        const formattedStart = startDateTime.toLocaleTimeString("es-ES", {
            hour: "2-digit",
            minute: "2-digit",
        });

        const formattedEnd = endDateTime.toLocaleTimeString("es-ES", {
            hour: "2-digit",
            minute: "2-digit",
        });

        // Actualizar texto
        infoText.textContent = `${formattedStart} - ${formattedEnd} (${duration} minutos)`;
        infoBox.classList.remove("d-none");

        console.log(
            `ℹ️ InfoBox actualizado: ${formattedStart} - ${formattedEnd} (${duration} min)`
        );
    }

    /**
     * Actualiza la caja de información del tiempo seleccionado
     */
    function updateInfoBox(
        dateInputId,
        startTimeInputId,
        endTimeInputId,
        durationInputId,
        infoBoxId
    ) {
        const dateInput = document.getElementById(dateInputId);
        const startTimeInput = startTimeInputId
            ? document.getElementById(startTimeInputId)
            : null;
        const endTimeInput = document.getElementById(endTimeInputId);
        const durationInput = document.getElementById(durationInputId);
        const infoBox = document.getElementById(infoBoxId);

        if (!dateInput || !endTimeInput || !durationInput || !infoBox) {
            console.warn(
                "⚠️ No se encontraron todos los elementos para actualizar el infoBox"
            );
            return;
        }

        // Obtener valores actuales
        let startDateTime;
        const dateValue = dateInput.value;

        if (!dateValue) {
            console.warn("⚠️ No hay fecha seleccionada");
            return;
        }

        // Si hay campos separados o combinados
        if (startTimeInput) {
            const startTimeValue = startTimeInput.value;
            if (!startTimeValue) {
                console.warn("⚠️ No hay hora de inicio seleccionada");
                return;
            }
            startDateTime = new Date(`${dateValue}T${startTimeValue}`);
        } else {
            startDateTime = new Date(dateValue);
        }

        // Obtener hora fin
        const endTimeValue = endTimeInput.value;
        if (!endTimeValue) {
            console.warn("⚠️ No hay hora de fin seleccionada");
            return;
        }

        const endTimeArr = endTimeValue.split(":");
        const endDateTime = new Date(startDateTime);
        endDateTime.setHours(parseInt(endTimeArr[0]), parseInt(endTimeArr[1]));

        // Obtener duración
        const duration = durationInput.value;

        // Actualizar el texto en el infoBox
        const infoText = infoBox.querySelector(".selected-time-text");
        if (infoText) {
            const formattedStart = startDateTime.toLocaleTimeString("es-ES", {
                hour: "2-digit",
                minute: "2-digit",
            });
            const formattedEnd = endDateTime.toLocaleTimeString("es-ES", {
                hour: "2-digit",
                minute: "2-digit",
            });

            infoText.textContent = `${formattedStart} - ${formattedEnd} (${duration} minutos)`;
            infoBox.classList.remove("d-none");

            console.log(
                `ℹ️ InfoBox actualizado: ${formattedStart} - ${formattedEnd} (${duration} min)`
            );
        }
    }
});
