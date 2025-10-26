document.addEventListener('DOMContentLoaded', () => {
    const box = document.getElementById('admin-topbar-search');
    if (!box) return;

    const url = box.dataset.url;
    const csrf = box.dataset.csrf;
    const input = document.getElementById('topbarSearchInput');
    const dropdown = document.getElementById('topbarSearchDropdown');
    const resultsUl = document.getElementById('topbarSearchResults');

    let lastController = null;
    let debounceId = null;

    const hideDropdown = () => dropdown.classList.add('hidden');
    const showDropdown = () => dropdown.classList.remove('hidden');

    document.addEventListener('click', (e) => {
        if (!box.contains(e.target)) hideDropdown();
    });

    function debouncedSearch(q) {
        clearTimeout(debounceId);
        debounceId = setTimeout(() => doSearch(q), 250);
    }

    async function doSearch(query) {
        if (!query || query.length < 2) {
            resultsUl.innerHTML = '';
            hideDropdown();
            return;
        }

        if (lastController) lastController.abort();
        lastController = new AbortController();

        try {
            const resp = await fetch(`${url}?query=${encodeURIComponent(query)}`, {
                headers: { 'Accept': 'application/json' },
                signal: lastController.signal,
                credentials: 'same-origin',
            });

            const data = await resp.json();
            resultsUl.innerHTML = '';

            if (!Array.isArray(data) || data.length === 0) {
                resultsUl.innerHTML = `<li class="px-4 py-2 text-gray-500 dark:text-gray-300">Sin resultados</li>`;
                showDropdown();
                return;
            }

            // dentro de data.forEach(u => { ... })
            data.forEach(u => {
                const estado = u.portal_registrado ? 'Registrado en el portal' : 'No registrado en el portal';
                const estadoClass = u.portal_registrado
                    ? 'bg-green-200 dark:bg-green-900 text-green-900 dark:text-green-200'
                    : 'bg-yellow-200 dark:bg-yellow-900 text-yellow-900 dark:text-yellow-200';

                const email = u.email ?? '—';

                const li = document.createElement('li');
                // 👇 guardamos el RUT en data-*
                li.dataset.patientRut = u.rut || '';
                li.className = 'cursor-pointer'; // por si acaso

                li.innerHTML = `
      <div class="px-4 py-2 hover:bg-purple-100 dark:hover:bg-gray-700 flex justify-between items-center">
        <div>
          <div class="font-semibold text-purple-800 dark:text-purple-200">${u.nombre ?? ''}</div>
          <div class="text-sm text-gray-600 dark:text-gray-400">${u.rut ?? ''}</div>
        </div>

        <div class="flex items-center gap-2">
          <span class="text-xs bg-purple-200 dark:bg-purple-900 text-purple-900 dark:text-purple-200 px-2 py-1 rounded-full">
            ${email}
          </span>
          <span class="text-[11px] px-2 py-1 rounded-full ${estadoClass}">
            ${estado}
          </span>
        </div>
      </div>
    `;
                resultsUl.appendChild(li);
            });

            showDropdown();
        } catch (err) {
            if (err.name !== 'AbortError') {
                resultsUl.innerHTML = `<li class="px-4 py-2 text-red-500">Error al buscar</li>`;
                showDropdown();
            }
        }
    }

    input.addEventListener('input', (e) => debouncedSearch(e.target.value.trim()));
});
