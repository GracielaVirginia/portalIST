import Swal from 'sweetalert2';

document.addEventListener('DOMContentLoaded', () => {
    console.log('[tickets] DOM listo — Vite');

    // Inicializar DataTable (si ya está inicializado, DataTables v2 ignora duplicado)
    try {
        new window.DataTable('#tablaTickets', {
            pageLength: 10,
            order: [[0, 'desc']],
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' }
        });
        console.log('[tickets] DataTable inicializado');
    } catch (e) {
        console.error('[tickets] Error iniciando DataTable:', e);
    }

    const csrfEl = document.querySelector('meta[name="csrf-token"]');
    const CSRF = csrfEl ? csrfEl.getAttribute('content') : '';
    if (!CSRF) console.warn('[tickets] Falta meta csrf-token, PATCH puede fallar (419).');

    const tbody = document.querySelector('#tablaTickets tbody');
    if (!tbody) {
        console.error('[tickets] No se encontró #tablaTickets tbody');
        return;
    }

    const isPendiente = (el) => (el.dataset.estado || '').toLowerCase() === 'pendiente';

    async function resolverBadge(badge) {
        if (!isPendiente(badge)) return;
        const url = badge.dataset.url;
        if (!url) {
            console.warn('[tickets] Badge pendiente sin data-url');
            return;
        }

        const result = await Swal.fire({
            title: '¿Marcar como resuelto?',
            text: '¿Ya resolviste este ticket?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#7e22ce',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, resolver',
            cancelButtonText: 'Cancelar'
        });
        if (!result.isConfirmed) return;

        try {
            const resp = await fetch(url, {
                method: 'PATCH',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
            });
            if (!resp.ok) throw new Error('HTTP ' + resp.status);
            const data = await resp.json();
            if (!data.ok) throw new Error('Respuesta inválida del servidor');

            // Actualiza visual del badge
            badge.classList.remove(
                'bg-yellow-100', 'text-yellow-800', 'dark:bg-yellow-900', 'dark:text-yellow-200', 'cursor-pointer'
            );
            badge.classList.add('bg-green-100', 'text-green-800', 'dark:bg-green-900', 'dark:text-green-200');
            badge.dataset.estado = 'resuelto';
            badge.removeAttribute('data-url');
            badge.setAttribute('tabindex', '-1');
            badge.textContent = '● Resuelto';

            await Swal.fire({
                icon: 'success',
                title: 'Actualizado',
                text: 'El ticket fue marcado como resuelto.',
                timer: 1400,
                showConfirmButton: false
            });
        } catch (err) {
            console.error('[tickets] Error resolviendo:', err);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No pudimos actualizar el estado. Revisa consola y vuelve a intentar.'
            });
        }
    }

    // Delegación: click y teclado en <tbody>
    tbody.addEventListener('click', (ev) => {
        const badge = ev.target.closest('.estado-badge');
        if (!badge) return;
        console.log('[tickets] Click en badge', badge.dataset);
        resolverBadge(badge);
    });

    tbody.addEventListener('keydown', (ev) => {
        if (ev.key !== 'Enter' && ev.key !== ' ') return;
        const badge = ev.target.closest('.estado-badge');
        if (!badge) return;
        ev.preventDefault();
        console.log('[tickets] Keydown en badge', badge.dataset);
        resolverBadge(badge);
    });
});
