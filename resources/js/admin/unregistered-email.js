// resources/js/admin/unregistered-email.js
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery;
    if (!$) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const cfgEl = document.getElementById('dtConfig');
    const updateEmailUrlTpl =
        cfgEl?.dataset.emailUpdatePattern ||
        document.body.dataset.emailUpdatePattern || '';

    $('#tablaNoRegistrados tbody').on('click', '.js-email-edit', function () {
        const $btn = $(this);
        const $td = $btn.closest('td');
        const rut = $btn.data('rut');
        const emailActual = ($btn.data('email') || '').trim();

        if ($td.data('editing')) return;
        $td.data('editing', true);
        const htmlOriginal = $td.html();
        $td.data('original', htmlOriginal);

        $td.html(`
      <div class="flex items-center gap-2">
        <input type="email"
               class="js-email-input w-48 sm:w-64 rounded border px-2 py-1 text-sm dark:bg-gray-800 dark:text-gray-100"
               value="${emailActual}"
               placeholder="correo@dominio.com">
        <button type="button"
                class="js-email-save inline-flex items-center gap-1 rounded bg-emerald-600 text-white text-xs font-semibold px-3 py-1 hover:bg-emerald-700">
          Guardar
        </button>
        <button type="button"
                class="js-email-cancel inline-flex items-center gap-1 rounded bg-gray-200 text-gray-800 text-xs font-semibold px-2 py-1 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-100">
          Cancelar
        </button>
      </div>
    `);

        const $input = $td.find('.js-email-input').trigger('focus');

        $td.on('click', '.js-email-save', async function () {
            const nuevoEmail = ($input.val() || '').trim();
            $input.removeClass('border-red-500');

            const emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(nuevoEmail);
            if (!emailOk) { $input.addClass('border-red-500').focus(); return; }

            const url = updateEmailUrlTpl?.replace('__RUT__', encodeURIComponent(rut || ''));
            if (!url || !rut) { alert('No se pudo construir la URL de actualización.'); return; }

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
                    body: JSON.stringify({ email: nuevoEmail })
                });

                const data = await res.json().catch(async () => ({ raw: await res.text() }));
                if (!res.ok || !data.ok) {
                    alert(data.error || 'No se pudo actualizar el email.');
                    $btnSave.prop('disabled', false).text('Guardar');
                    return;
                }

                $td.html(`
          <div class="flex items-center gap-2">
            <span class="email-text">${nuevoEmail}</span>
            <button type="button"
                    class="js-email-edit inline-flex items-center gap-1 text-purple-700 dark:text-purple-300 hover:underline"
                    title="Editar email"
                    data-rut="${rut}"
                    data-email="${nuevoEmail}">
              ✏️ <span class="hidden sm:inline">Editar</span>
            </button>
          </div>
        `);
                $td.data('editing', false).removeData('original');
            } catch {
                alert('Error de red al actualizar.');
            }
        });

        $td.on('click', '.js-email-cancel', function () {
            $td.html(htmlOriginal);
            $td.data('editing', false).removeData('original');
        });

        $td.on('keydown', '.js-email-input', function (e) {
            if (e.key === 'Enter') $td.find('.js-email-save').click();
            if (e.key === 'Escape') $td.find('.js-email-cancel').click();
        });
    });
});
