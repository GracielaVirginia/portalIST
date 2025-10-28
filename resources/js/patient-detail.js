// resources/js/patient-detail.js  (versión integrada con CTA editar por RUT)

console.log('[patient] módulo cargado');

(function () {
    function attach() {
        console.log('[patient] attach() inicializando…');

        const panel = document.getElementById('patientDetailPanel');
        if (!panel) {
            console.warn('[patient] NO existe #patientDetailPanel en el DOM');
            return;
        }

        // Endpoints / patrones desde data-atributos
        const LOOKUP_URL = panel.dataset.lookup;
        const UNBLOCK_TPL = panel.dataset.unblockPattern;
        const DELETE_TPL = panel.dataset.deletePattern;
        const EDIT_TPL = panel.dataset.editPattern; // 👈 NUEVO: patrón para editar por RUT

        console.log('[patient] endpoints:', { LOOKUP_URL, UNBLOCK_TPL, DELETE_TPL, EDIT_TPL });

        // Referencias de UI
        const msgEl = document.getElementById('patientMsg');
        const tableWrap = document.getElementById('patientTableWrap');
        const tbody = document.getElementById('patientTableBody');
        const ctaRegister = document.getElementById('patientCtaRegister');
        const linkEditar = document.getElementById('linkEditarNoReg'); // 👈 NUEVO: <a> del CTA

        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        if (!csrf) console.warn('[patient] WARNING: no se encontró <meta name="csrf-token">. POST/DELETE fallarán en producción.');

        function showMsg(t) { if (msgEl) msgEl.textContent = t; }

        async function lookupByRut(rut) {
            if (!rut) { console.error('[patient] lookupByRut sin rut'); return; }

            showMsg('Buscando…');
            tableWrap?.classList.add('hidden');
            ctaRegister?.classList.add('hidden');
            if (tbody) tbody.innerHTML = '';

            const url = `${LOOKUP_URL}?rut=${encodeURIComponent(rut)}`;
            console.log('[patient] LOOKUP fetch', url);

            try {
                const res = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                console.log('[patient] LOOKUP status', res.status);

                let data;
                try {
                    data = await res.json();
                } catch {
                    const raw = await res.text();
                    console.error('[patient] LOOKUP respuesta no-JSON (HTML):\n', raw);
                    throw new Error('Respuesta no JSON en lookup');
                }
                console.log('[patient] LOOKUP data', data);

                if (!res.ok || !data.ok) {
                    const msg = (data && data.error) ? data.error : `HTTP ${res.status}`;
                    throw new Error(msg);
                }

                // === Usuario NO registrado → mostrar CTA con link a editar por RUT ===
                if (!data.exists) {
                    const rutNorm = (data.rut || rut || '').toUpperCase();
                    showMsg(`RUT ${rutNorm}: usuario NO registrado`);

                    // Armar el href al edit usando el patrón de ruta
                    if (linkEditar && EDIT_TPL) {
                        linkEditar.href = EDIT_TPL.replace('__RUT__', encodeURIComponent(rutNorm));
                    } else {
                        console.warn('[patient] No hay EDIT_TPL o linkEditar para armar el enlace de edición.');
                    }

                    ctaRegister?.classList.remove('hidden');
                    return;
                }

                // === Usuario EXISTE → render de la fila ===
                const u = data.user;
                const estadoHtml = u.blocked
                    ? `<span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-200">Bloqueado</span>`
                    : `<span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200">Activo</span>`;

                const unblockBtn = u.blocked
                    ? `<button data-action="unblock" data-id="${u.id}" class="px-3 py-1 rounded bg-blue-600 text-white text-xs hover:bg-blue-700">Desbloquear</button>`
                    : '';

                const deleteBtn = `<button data-action="delete" data-id="${u.id}" class="px-3 py-1 rounded bg-red-600 text-white text-xs hover:bg-red-700">Eliminar</button>`;

                if (tbody) {
                    tbody.innerHTML = `
                        <tr class="border-t border-slate-100 dark:border-slate-700">
                          <td class="px-4 py-3">${u.name ?? ''}</td>
                          <td class="px-4 py-3">${u.apellido ?? ''}</td>
                          <td class="px-4 py-3">${u.rut ?? ''}</td>
                          <td class="px-4 py-3">${estadoHtml}</td>
                          <td class="px-4 py-3 flex gap-2">${unblockBtn} ${deleteBtn}</td>
                        </tr>
                    `;
                }

                tableWrap?.classList.remove('hidden');
                showMsg(`Resultado para RUT ${u.rut}`);
            } catch (e) {
                console.error('[patient] LOOKUP error', e);
                showMsg('No se pudo obtener información del paciente.');
            }
        }

        async function doUnblock(id) {
            const url = UNBLOCK_TPL?.replace('USER_ID', id);
            console.log('[patient] UNBLOCK →', url);
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                console.log('[patient] UNBLOCK status', res.status);
                const data = await res.json().catch(async () => ({ raw: await res.text() }));
                console.log('[patient] UNBLOCK data', data);
                if (!res.ok || !data.ok) throw new Error(data.error || `HTTP ${res.status}`);

                showMsg('Usuario desbloqueado.');
                const rut = tbody?.querySelector('tr td:nth-child(3)')?.textContent?.trim();
                if (rut) lookupByRut(rut);
            } catch (e) {
                console.error('[patient] UNBLOCK error', e);
                showMsg('No se pudo desbloquear.');
            }
        }

        async function doDelete(id) {
            const url = DELETE_TPL?.replace('USER_ID', id);
            console.log('[patient] DELETE →', url);
            try {
                const res = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                console.log('[patient] DELETE status', res.status);
                const data = await res.json().catch(async () => ({ raw: await res.text() }));
                console.log('[patient] DELETE data', data);
                if (!res.ok || !data.ok) throw new Error(data.error || `HTTP ${res.status}`);

                showMsg('Cuenta eliminada.');
                tableWrap?.classList.add('hidden');
                ctaRegister?.classList.add('hidden');
            } catch (e) {
                console.error('[patient] DELETE error', e);
                showMsg('No se pudo eliminar la cuenta.');
            }
        }

        // Delegación de eventos para botones (unblock/delete)
        panel.addEventListener('click', (ev) => {
            const btn = ev.target.closest('button[data-action]');
            if (!btn) return;
            const id = btn.dataset.id;
            const action = btn.dataset.action;
            console.log('[patient] botón', action, 'id=', id);
            if (action === 'unblock') doUnblock(id);
            if (action === 'delete') doDelete(id);
        });

        // 1) Evento oficial desde tu buscador:
        window.addEventListener('patient:select', (e) => {
            console.log('[patient] recibido patient:select', e.detail);
            const rut = e.detail?.rut;
            if (rut) lookupByRut(rut);
        });

        // 2) Fallback: si tus resultados tienen atributo data-patient-rut, lo “capturamos”
        document.addEventListener('click', (ev) => {
            const el = ev.target.closest('[data-patient-rut]');
            if (!el) return;
            const rut = el.getAttribute('data-patient-rut');
            console.log('[patient] click en data-patient-rut=', rut);
            if (rut) lookupByRut(rut);
        });

        // 3) Helper para probar desde consola
        window.__patientSelect = (rut) => {
            console.log('[patient] __patientSelect()', rut);
            lookupByRut(rut);
        };
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', attach);
    } else {
        attach();
    }
})();
