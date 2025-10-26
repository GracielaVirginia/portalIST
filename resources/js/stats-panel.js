
(function () {
    function attach() {
        const panel = document.getElementById('statsPanel');
        if (!panel) return;

        const ENDPOINT = panel.dataset.endpoint; // ← viene del data-endpoint
        const fechaEl = document.getElementById('filtroFecha');
        const sedeEl = document.getElementById('filtroSede');
        const fechaTxt = document.getElementById('statsFechaTxt');
        const msgEl = document.getElementById('statsMsg');

        const cUsuarios = document.getElementById('cardUsuariosRegistrados');
        const cExamenes = document.getElementById('cardExamenesRealizados');
        const cBloqs = document.getElementById('cardUsuariosBloqueados');

        if (!ENDPOINT) {
            console.error('[stats] Falta data-endpoint en #statsPanel');
            return;
        }

        async function cargarStats() {
            msgEl.textContent = 'Cargando…';
            const params = new URLSearchParams();
            if (fechaEl.value) params.set('fecha', fechaEl.value);
            if (sedeEl.value) params.set('sede', sedeEl.value);

            console.log('[stats] fetch', ENDPOINT, params.toString());

            try {
                const res = await fetch(`${ENDPOINT}?${params.toString()}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                console.log('[stats] status', res.status);
                const data = await res.json();
                console.log('[stats] data', data);

                if (!data.ok) throw new Error(data.error || 'Error en respuesta');

                // Llenar sedes solo una vez (o cuando el select está vacío)
                if (sedeEl.options.length <= 1 && Array.isArray(data.sedes)) {
                    data.sedes.forEach(s => {
                        const opt = document.createElement('option');
                        opt.value = s;
                        opt.textContent = s;
                        sedeEl.appendChild(opt);
                    });
                }

                cUsuarios.textContent = data.usuariosRegistrados;
                cExamenes.textContent = data.examenesRealizados;
                cBloqs.textContent = data.usuariosBloqueados;

                fechaTxt.textContent = data.fecha;
                msgEl.textContent = data.sede ? `Filtrado por sede: ${data.sede}` : 'Todas las sedes';
            } catch (e) {
                msgEl.textContent = 'No se pudieron cargar estadísticas.';
                console.error('[stats] error', e);
            }
        }

        // Escuchar el evento del calendario (global)
        window.addEventListener('calendar:select', (e) => {
            console.log('[stats] recibido calendar:select', e.detail);
            fechaEl.value = e.detail.date; // YYYY-MM-DD
            cargarStats();
        });

        // Cambio de sede
        sedeEl.addEventListener('change', cargarStats);

        // Carga inicial (si el calendario aún no ha emitido nada)
        const d = new Date();
        const pad = (n) => String(n).padStart(2, '0');
        if (!fechaEl.value) {
            fechaEl.value = `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
        }
        cargarStats();
    }

    // Garantiza que el listener se registre
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', attach);
    } else {
        attach();
    }
})();
