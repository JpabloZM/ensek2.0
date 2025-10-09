/**
 * Direct Time Calculation Fixer
 * Script para corregir los problemas de cálculo de tiempo en el modal directo
 */
console.log("🛠️ Inicializando arreglo para cálculos de tiempo");

document.addEventListener("DOMContentLoaded", function () {
    // Configurar listeners directos para los campos del modal directo
    console.log("🛠️ Configurando listeners directos para modal directo");

    // Asegurar inicialización después de carga completa
    setTimeout(setupDirectListeners, 1000);

    // Escuchar a eventos de apertura de modal
    document.addEventListener("shown.bs.modal", function (event) {
        if (event.target.id === "newDirectScheduleModal") {
            console.log("🛠️ Modal directo detectado - inicializando listeners");
            setupDirectListeners();

            // Forzar actualización de infoBox si hay valores
            setTimeout(updateInfoBoxIfNeeded, 500);
        }
    });

    /**
     * Configura listeners directos para campos en el modal directo
     */
    function setupDirectListeners() {
        console.log("🔧 Configurando listeners directos");

        // 1. Listener para duración
        const durationInput = document.getElementById("direct_duration");
        if (durationInput) {
            // Eliminar listeners previos
            const newDuration = durationInput.cloneNode(true);
            durationInput.parentNode.replaceChild(newDuration, durationInput);

            // Agregar nuevo listener
            newDuration.addEventListener("input", function () {
                console.log("🕒 Cambio manual en duración:", this.value);
                updateEndTimeFromDuration(this.value);
            });

            console.log("✅ Listener de duración configurado");
        }

        // 2. Listener para inicio de tiempo
        const startTimeInput = document.getElementById("direct_start_time");
        if (startTimeInput) {
            // Eliminar listeners previos
            const newStartTime = startTimeInput.cloneNode(true);
            startTimeInput.parentNode.replaceChild(
                newStartTime,
                startTimeInput
            );

            // Agregar nuevo listener
            newStartTime.addEventListener("input", function () {
                console.log("🕒 Cambio manual en hora inicio:", this.value);
                const duration =
                    document.getElementById("direct_duration").value || "60";
                updateEndTimeFromDuration(duration);
            });

            console.log("✅ Listener de hora inicio configurado");
        }

        // 3. Listener para fin de tiempo
        const endTimeInput = document.getElementById("direct_end_time");
        if (endTimeInput) {
            // Eliminar listeners previos
            const newEndTime = endTimeInput.cloneNode(true);
            endTimeInput.parentNode.replaceChild(newEndTime, endTimeInput);

            // Agregar nuevo listener
            newEndTime.addEventListener("input", function () {
                console.log("🕒 Cambio manual en hora fin:", this.value);
                updateDurationFromEndTime(this.value);
            });

            console.log("✅ Listener de hora fin configurado");
        }

        // 4. Listener para fecha
        const dateInput = document.getElementById("direct_scheduled_date");
        if (dateInput) {
            // Eliminar listeners previos
            const newDate = dateInput.cloneNode(true);
            dateInput.parentNode.replaceChild(newDate, dateInput);

            // Agregar nuevo listener
            newDate.addEventListener("input", function () {
                console.log("📅 Cambio manual en fecha:", this.value);
                const duration =
                    document.getElementById("direct_duration").value || "60";
                updateEndTimeFromDuration(duration);
            });

            console.log("✅ Listener de fecha configurado");
        }
    }

    /**
     * Actualiza hora de fin basado en la duración
     */
    function updateEndTimeFromDuration(duration) {
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

            // Calcular hora de fin
            const durationMins = parseInt(duration);
            const endDateTime = new Date(
                startDateTime.getTime() + durationMins * 60000
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
            console.log(
                `⏱️ Hora fin actualizada a ${formattedEndTime} (duración: ${durationMins} min)`
            );

            // Actualizar infoBox
            updateInfoBoxDirecto(startDateTime, endDateTime, durationMins);
        } catch (error) {
            console.error("❌ Error al calcular hora de fin:", error);
        }
    }

    /**
     * Actualiza duración basado en la hora de fin
     */
    function updateDurationFromEndTime(endTimeValue) {
        const dateInput = document.getElementById("direct_scheduled_date");
        const startTimeInput = document.getElementById("direct_start_time");
        const durationInput = document.getElementById("direct_duration");

        if (
            !dateInput ||
            !startTimeInput ||
            !durationInput ||
            !dateInput.value ||
            !startTimeInput.value
        ) {
            console.warn("⚠️ Faltan campos necesarios para calcular duración");
            return;
        }

        try {
            // Construir fecha/hora de inicio
            const startDateTime = new Date(
                `${dateInput.value}T${startTimeInput.value}`
            );

            // Construir fecha/hora de fin
            const [endHours, endMinutes] = endTimeValue.split(":").map(Number);
            const endDateTime = new Date(startDateTime);
            endDateTime.setHours(endHours, endMinutes);

            // Si la hora fin es anterior a inicio, asumimos día siguiente
            if (endDateTime < startDateTime) {
                endDateTime.setDate(endDateTime.getDate() + 1);
            }

            // Calcular duración en minutos
            const durationMins = Math.round(
                (endDateTime - startDateTime) / 60000
            );

            // Actualizar campo
            durationInput.value = durationMins;
            console.log(`⏱️ Duración actualizada a ${durationMins} min`);

            // Actualizar infoBox
            updateInfoBoxDirecto(startDateTime, endDateTime, durationMins);
        } catch (error) {
            console.error("❌ Error al calcular duración:", error);
        }
    }

    /**
     * Actualiza el infoBox si hay datos suficientes
     */
    function updateInfoBoxIfNeeded() {
        const dateInput = document.getElementById("direct_scheduled_date");
        const startTimeInput = document.getElementById("direct_start_time");
        const endTimeInput = document.getElementById("direct_end_time");
        const durationInput = document.getElementById("direct_duration");

        if (
            dateInput &&
            dateInput.value &&
            startTimeInput &&
            startTimeInput.value &&
            endTimeInput &&
            endTimeInput.value &&
            durationInput &&
            durationInput.value
        ) {
            try {
                const startDateTime = new Date(
                    `${dateInput.value}T${startTimeInput.value}`
                );

                const [endHours, endMinutes] = endTimeInput.value
                    .split(":")
                    .map(Number);
                const endDateTime = new Date(startDateTime);
                endDateTime.setHours(endHours, endMinutes);

                updateInfoBoxDirecto(
                    startDateTime,
                    endDateTime,
                    durationInput.value
                );
            } catch (error) {
                console.error("❌ Error al actualizar infoBox:", error);
            }
        }
    }

    /**
     * Actualiza el infoBox del modal directo
     */
    function updateInfoBoxDirecto(startDateTime, endDateTime, duration) {
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
});
