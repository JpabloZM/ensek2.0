/**
 * Solución definitiva para visualizar la selección del calendario
 * Este script asegura que siempre haya un borde verde visible alrededor de la selección
 */

document.addEventListener('DOMContentLoaded', function() {
    // Esperar a que todo el DOM esté cargado para inicializar
    setTimeout(function() {
        console.log("Inicializando marcador de selección definitivo");
        
        // Elementos clave
        const calendarContainer = document.querySelector('.technician-calendar-container');
        if (!calendarContainer) return;
        
        // Crear un nuevo elemento para mostrar la selección con alta visibilidad
        const definitiveBorder = document.createElement('div');
        definitiveBorder.id = 'definitive-selection-border';
        definitiveBorder.style.position = 'absolute';
        definitiveBorder.style.display = 'none';
        definitiveBorder.style.pointerEvents = 'none';
        definitiveBorder.style.zIndex = '10000';
        definitiveBorder.style.border = '3px solid #00a651';
        definitiveBorder.style.boxShadow = '0 0 0 1px white, 0 0 8px rgba(0,0,0,0.5)';
        definitiveBorder.style.backgroundColor = 'rgba(135, 201, 71, 0.15)';
        definitiveBorder.innerHTML = `
            <div class="border-time-indicator"></div>
            <div class="border-corner top-left"></div>
            <div class="border-corner top-right"></div>
            <div class="border-corner bottom-left"></div>
            <div class="border-corner bottom-right"></div>
        `;
        document.body.appendChild(definitiveBorder);
        
        // Estilos para el borde y sus elementos
        const borderStyles = document.createElement('style');
        borderStyles.textContent = `
            #definitive-selection-border {
                transition: all 0.05s ease-out;
            }
            
            #definitive-selection-border.visible {
                display: block !important;
            }
            
            .border-time-indicator {
                position: absolute;
                top: -30px;
                left: 50%;
                transform: translateX(-50%);
                background-color: #004122;
                color: white;
                padding: 4px 10px;
                font-size: 14px;
                font-weight: bold;
                border-radius: 4px;
                box-shadow: 0 2px 6px rgba(0,0,0,0.3);
                white-space: nowrap;
            }
            
            .border-corner {
                position: absolute;
                width: 8px;
                height: 8px;
                background-color: #00a651;
                border: 2px solid white;
                box-shadow: 0 0 4px rgba(0,0,0,0.5);
            }
            
            .top-left {
                top: -4px;
                left: -4px;
                border-radius: 50%;
            }
            
            .top-right {
                top: -4px;
                right: -4px;
                border-radius: 50%;
            }
            
            .bottom-left {
                bottom: -4px;
                left: -4px;
                border-radius: 50%;
            }
            
            .bottom-right {
                bottom: -4px;
                right: -4px;
                border-radius: 50%;
            }
            
            /* Puntero personalizado al arrastrar */
            .calendar-selectable.dragging-active {
                cursor: grabbing !important;
            }
            
            /* Mejorar visibilidad de celdas seleccionadas */
            .calendar-cell-selected {
                background-color: transparent !important;
                outline: none !important;
                box-shadow: none !important;
                border: none !important;
            }
        `;
        document.head.appendChild(borderStyles);
        
        // Observar cambios en el overlay original
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && 
                    mutation.attributeName === 'style' && 
                    mutation.target.classList.contains('calendar-selection-overlay')) {
                    
                    const overlay = mutation.target;
                    
                    if (overlay.style.display !== 'none') {
                        // La selección está visible, actualizar nuestro borde definitivo
                        const rect = overlay.getBoundingClientRect();
                        updateDefinitiveBorder(rect, overlay);
                    } else {
                        // La selección está oculta, ocultar nuestro borde
                        definitiveBorder.classList.remove('visible');
                        definitiveBorder.style.display = 'none';
                    }
                }
            });
        });
        
        // Conectar el observador al overlay original
        const originalOverlay = document.querySelector('.calendar-selection-overlay');
        if (originalOverlay) {
            observer.observe(originalOverlay, { 
                attributes: true,
                attributeFilter: ['style', 'class']
            });
        }
        
        // Función para actualizar el borde definitivo
        function updateDefinitiveBorder(rect, originalOverlay) {
            // Posicionar el borde exactamente sobre la selección
            definitiveBorder.style.top = `${rect.top}px`;
            definitiveBorder.style.left = `${rect.left}px`;
            definitiveBorder.style.width = `${rect.width}px`;
            definitiveBorder.style.height = `${rect.height}px`;
            
            // Actualizar el indicador de tiempo
            const timeElement = originalOverlay.querySelector('.selection-time');
            if (timeElement) {
                const timeText = timeElement.textContent.trim();
                definitiveBorder.querySelector('.border-time-indicator').textContent = timeText;
            }
            
            // Hacer visible el borde
            definitiveBorder.style.display = 'block';
            definitiveBorder.classList.add('visible');
        }
        
        // También conectar con eventos del mouse para mayor confiabilidad
        document.addEventListener('mousedown', function(e) {
            const cell = e.target.closest('.calendar-service-cell');
            if (cell && !cell.querySelector('.calendar-service')) {
                // Estamos iniciando una selección, prepararnos
                document.body.classList.add('calendar-selection-active');
            }
        });
        
        document.addEventListener('mouseup', function() {
            // La selección ha terminado
            document.body.classList.remove('calendar-selection-active');
            
            // Si el overlay original está oculto, ocultar nuestro borde también
            const originalOverlay = document.querySelector('.calendar-selection-overlay');
            if (originalOverlay && originalOverlay.style.display === 'none') {
                definitiveBorder.style.display = 'none';
                definitiveBorder.classList.remove('visible');
            }
        });
        
        // También monitorear el final de la creación del servicio
        document.addEventListener('hidden.bs.modal', function(e) {
            if (e.target.id === 'newScheduleModal') {
                // Se cerró el modal de servicio, ocultar cualquier selección
                definitiveBorder.style.display = 'none';
                definitiveBorder.classList.remove('visible');
            }
        });
        
    }, 500); // Dar tiempo para que otros scripts se carguen primero
});