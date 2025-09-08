/**
 * Custom Search Implementation - Reemplazo total de DataTables
 * Esta implementación prescinde completamente de DataTables y usa búsqueda simple
 * para evitar todos los problemas relacionados con la biblioteca.
 */

(function () {
    // Esperar a que el DOM esté completamente cargado
    document.addEventListener("DOMContentLoaded", function () {
        console.log("Iniciando sistema de búsqueda personalizado...");

        // Encontrar todas las tablas que necesitan búsqueda
        const allTables = document.querySelectorAll(
            ".datatable-table, #dt-table, #dataTable"
        );

        if (!allTables.length) {
            console.log("No se encontraron tablas para aplicar búsqueda");
            return;
        }

        console.log(
            "Se encontraron " +
                allTables.length +
                " tablas para aplicar búsqueda simple"
        );

        // Para cada tabla, añadir la funcionalidad de búsqueda
        allTables.forEach(function (table, index) {
            // Generar ID único si no tiene
            if (!table.id) {
                table.id = "custom-table-" + index;
            }

            // Asegurarse de que tiene la clase correcta
            table.classList.add("custom-searchable-table");

            // Aplicar búsqueda simple
            applyCustomSearchToTable(table);
        });

        /**
         * Aplica sistema de búsqueda personalizado a una tabla
         * @param {HTMLElement} table - La tabla a la que se aplica la búsqueda
         */
        function applyCustomSearchToTable(table) {
            const tableId = table.id;
            const searchId = "search-" + tableId;

            // Si ya existe un buscador para esta tabla, no crear otro
            if (document.getElementById(searchId)) {
                return;
            }

            console.log("Aplicando búsqueda personalizada a tabla: " + tableId);

            // Crear contenedor de búsqueda
            const searchContainer = document.createElement("div");
            searchContainer.className = "custom-search-container mb-3";
            searchContainer.innerHTML = `
                <div class="input-group">
                    <span class="input-group-text bg-primary text-white">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" id="${searchId}" class="form-control" 
                           placeholder="Buscar en esta tabla..." autocomplete="off">
                </div>
            `;

            // Insertar el buscador antes de la tabla
            table.parentNode.insertBefore(searchContainer, table);

            // Obtener el input de búsqueda
            const searchInput = document.getElementById(searchId);

            // Función de búsqueda
            function filterTable() {
                const searchText = searchInput.value.toLowerCase().trim();
                const rows = table.querySelectorAll("tbody tr");
                let hasResults = false;

                // Para cada fila en el cuerpo de la tabla
                rows.forEach(function (row) {
                    // Si es una fila de "no results", siempre ocultarla inicialmente
                    if (row.classList.contains("no-results-row")) {
                        row.style.display = "none";
                        return;
                    }

                    const text = row.textContent.toLowerCase();
                    const shouldShow =
                        searchText === "" || text.includes(searchText);

                    // Mostrar u ocultar la fila
                    row.style.display = shouldShow ? "" : "none";

                    // Actualizar bandera de resultados
                    if (shouldShow) {
                        hasResults = true;
                    }
                });

                // Manejar caso de sin resultados
                const noResultsRow = table.querySelector(".no-results-row");

                if (!hasResults && searchText !== "") {
                    if (!noResultsRow) {
                        // Crear fila de "sin resultados" si no existe
                        const tBody = table.querySelector("tbody");
                        const colCount =
                            table.querySelector("thead tr").children.length;

                        const newRow = document.createElement("tr");
                        newRow.className = "no-results-row";

                        const cell = document.createElement("td");
                        cell.colSpan = colCount;
                        cell.className = "text-center py-3 text-muted";
                        cell.innerHTML = `
                            <i class="fas fa-search fa-fw me-2"></i>
                            No se encontraron resultados para "${searchText}"
                        `;

                        newRow.appendChild(cell);
                        tBody.appendChild(newRow);
                    } else {
                        noResultsRow.style.display = "";
                        const messageCell = noResultsRow.querySelector("td");
                        if (messageCell) {
                            messageCell.innerHTML = `
                                <i class="fas fa-search fa-fw me-2"></i>
                                No se encontraron resultados para "${searchText}"
                            `;
                        }
                    }
                }
            }

            // Asignar evento de búsqueda
            searchInput.addEventListener("input", filterTable);
            searchInput.addEventListener("keyup", filterTable);

            // Aplicar estilos para mejorar la apariencia
            addCustomStyles();

            console.log(
                "Búsqueda personalizada aplicada con éxito a: " + tableId
            );
        }

        /**
         * Agrega estilos CSS personalizados para las tablas y búsquedas
         */
        function addCustomStyles() {
            // Verificar si ya existen los estilos
            if (document.getElementById("custom-search-styles")) {
                return;
            }

            // Crear elemento de estilos
            const styleEl = document.createElement("style");
            styleEl.id = "custom-search-styles";
            styleEl.textContent = `
                .custom-search-container {
                    margin-bottom: 1rem;
                }
                .custom-search-container .input-group-text {
                    border-top-right-radius: 0;
                    border-bottom-right-radius: 0;
                }
                .custom-searchable-table {
                    width: 100% !important;
                    margin-bottom: 1rem;
                }
                .no-results-row td {
                    background-color: #f8f9fa;
                }
                /* Mejoras de accesibilidad */
                .form-control:focus {
                    border-color: #4e73df;
                    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
                }
            `;

            // Añadir estilos al head del documento
            document.head.appendChild(styleEl);
        }
    });
})();
