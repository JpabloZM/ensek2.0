// Si realmente necesitas DataTables en el futuro, puedes restaurar este script
// y cargarlo solo en las páginas donde sea absolutamente necesario

/*
// Esto es solo un ejemplo de cómo usar DataTables si se necesita en el futuro
document.addEventListener('DOMContentLoaded', function() {
    // Solo inicializar si se ha solicitado explícitamente
    if (document.body.classList.contains('use-datatables')) {
        const tables = document.querySelectorAll('.datatable-opt');
        if (tables.length) {
            // Cargar dinamicamente DataTables
            loadScript('https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js', function() {
                loadScript('https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js', function() {
                    // Inicializar tablas
                    tables.forEach(function(table) {
                        try {
                            $(table).DataTable({
                                // Opciones básicas aquí
                            });
                        } catch (e) {
                            console.error('Error al inicializar DataTable:', e);
                        }
                    });
                });
            });
        }
    }
    
    // Función para cargar scripts dinámicamente
    function loadScript(src, callback) {
        const script = document.createElement('script');
        script.src = src;
        script.onload = callback;
        document.head.appendChild(script);
    }
});
*/
