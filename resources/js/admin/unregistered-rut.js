// resources/js/admin/unregistered-rut.js  (SIN validación ni formateo)
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery;
    if (!$) { console.warn('[rut-edit] jQuery no está disponible.'); return; }

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // patrones desde #dtConfig o <body>
    const cfgEl = document.getElementById('dtConfig');
    const updateRutUrlTpl =
        cfgEl?.dataset.rutUpdatePattern ||
        document.body.dataset.rutUpdatePattern || '';

    // ÚNICA delegación, namespaciada
    $('#tablaNoRegistrados')
        .off('click.rutEdit', 'tbody .js-rut-edit')
        .on('click.rutEdit', 'tbody .js-rut-edit', function (e) {
            e.stopImmediatePropagation();

            const $btn = $(this);
            const $td = $btn.closest('td,th');
            const rutActual = ($btn.data('rut') || '').trim();

            if (!$td.length) { console.warn('[rut-edit] No se encontró celda TD/TH'); return; }
            if ($td.data('editing')) return;

            $td.data('editing', true);
            const htmlOriginal = $td.html();
            $td.data('original', htmlOriginal);

            $td.html(`
        <div class="flex items-center gap-2">
          <input type="text"
                 class="js-rut-input w-40 sm:w-48 rounded border px-2 py-1 text-sm dark:bg-gray-800 dark:text-gray-100"
                 value="${rutActual}"
                 placeholder="11111111-1" maxlength="20">
          <button type="button"
                  class="js-rut-save inline-flex items-center gap-1 rounded bg-emerald-600 text-white text-xs font-semibold px-3 py-1 hover:bg-emerald-700">
            Guardar
          </button>
          <button type="button"
                  class="js-rut-cancel inline-flex items-center gap-1 rounded bg-gray-200 text-gray-800 text-xs font-semibold px-2 py-1 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-100">
            Cancelar
          </button>
        </div>
      `);

            const $input = $td.find('.js-rut-input').trigger('focus');

            // Guardar (SIN validación ni formateo)
            $td.off('click.rutSave').on('click.rutSave', '.js-rut-save', async function () {
                const nuevoRut = ($input.val() || ''); // ← tal cual, sin tocar

                const url = updateRutUrlTpl?.replace('__RUT__', encodeURIComponent(rutActual || ''));
                if (!url || !rutActual) { alert('No se pudo construir la URL de actualización.'); return; }

                try {
                    const $btnSave = $(this).prop('disabled', true).text('Guardando…');

                    const res = await fetch(url, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ rut: nuevoRut })
                    });

                    const data = await res.json().catch(async () => ({ raw: await res.text() }));
                    if (!res.ok || !data.ok) {
                        alert(data.error || 'No se pudo actualizar el RUT.');
                        $btnSave.prop('disabled', false).text('Guardar');
                        return;
                    }

                    // Re-render de la celda con el nuevo RUT
                    $td.html(`
            <div class="flex items-center gap-2">
              <span class="rut-text">${nuevoRut}</span>
              <button type="button"
                      class="js-rut-edit inline-flex items-center gap-1 text-purple-700 dark:text-purple-300 hover:underline"
                      title="Editar RUT"
                      data-rut="${nuevoRut}">
                ✏️ <span class="hidden sm:inline">Editar</span>
              </button>
            </div>
          `);
                    $td.data('editing', false).removeData('original');
                } catch (err) {
                    console.error('[rut-edit] fetch error', err);
                    alert('Error de red al actualizar.');
                }
            });

            // Cancelar
            $td.off('click.rutCancel').on('click.rutCancel', '.js-rut-cancel', function () {
                $td.html(htmlOriginal);
                $td.data('editing', false).removeData('original');
            });

            // Enter = guardar / Esc = cancelar
            $td.off('keydown.rutKeys').on('keydown.rutKeys', '.js-rut-input', function (ev) {
                if (ev.key === 'Enter') $td.find('.js-rut-save').click();
                if (ev.key === 'Escape') $td.find('.js-rut-cancel').click();
            });
        });

    // (opcional) log de ayuda
    $(document).on('draw.dt', function () {
        const n = $('#tablaNoRegistrados tbody .js-rut-edit').length;
        console.log(`[rut-edit] draw.dt → ${n} botones`);
    });
});
