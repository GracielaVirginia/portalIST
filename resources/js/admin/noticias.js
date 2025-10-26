// resources/js/admin/noticias.js
import Swal from 'sweetalert2';

// Si usas DataTables v2 por CDN, DataTable está en window. Si lo tienes por npm, importa aquí.
// import DataTable from 'datatables.net'; // <-- solo si lo instalaste por npm

document.addEventListener('DOMContentLoaded', () => {
    // Inicializa DataTable
    // Nota: si DataTables viene por CDN, esto funciona tal cual con window.DataTable
    if (window.DataTable) {
        new DataTable('#tablaNoticias', {
            pageLength: 10,
            order: [[1, 'asc']],
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' }
        });
    }

    // Interceptar submit de formularios de eliminar (delegación = robusto)
    document.addEventListener('submit', function (e) {
        const form = e.target.closest('.form-eliminar');
        if (!form) return;

        // Evita doble diálogo
        if (form.dataset.confirmed === 'true') return;

        e.preventDefault();
        Swal.fire({
            title: '¿Eliminar noticia?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#7e22ce',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (result.isConfirmed) {
                form.dataset.confirmed = 'true';
                form.submit();
            }
        });
    });
});
