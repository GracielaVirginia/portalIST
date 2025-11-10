(function () {
  const $ = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));
  const on = (el, ev, fn) => el && el.addEventListener(ev, fn);

  document.addEventListener('DOMContentLoaded', () => {
    const $btnRut = $('#btnModoRut');
    const $btnPpt = $('#btnModoPasaporte');

    const $formRut = $('#formLogin');
    const $formPpt = $('#formLoginPasaporte');

    // Helpers para habilitar/deshabilitar controles visibles (sin afectar _token)
    function setFormEnabled($form, enabled) {
      if (!$form) return;
      const controls = $$('button, input:not([type="hidden"]):not([name="_token"]), select, textarea', $form);
      controls.forEach(el => {
        if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
          if (enabled) el.removeAttribute('readonly');
          else el.setAttribute('readonly', 'readonly');
        }
        if (!enabled) el.setAttribute('disabled', 'disabled');
        else el.removeAttribute('disabled');
      });
    }

    function showRut() {
      $btnRut.setAttribute('aria-selected', 'true');
      $btnPpt.setAttribute('aria-selected', 'false');

      $formRut.classList.remove('hidden');
      $formRut.removeAttribute('aria-hidden');
      $formPpt.classList.add('hidden');
      $formPpt.setAttribute('aria-hidden', 'true');

      setFormEnabled($formRut, true);
      setFormEnabled($formPpt, false);

      $btnRut.className = 'px-3 py-1.5 text-sm font-semibold rounded-l-xl border border-purple-300/70 dark:border-purple-700/70 bg-purple-900 text-white hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-500';
      $btnPpt.className = 'px-3 py-1.5 text-sm font-semibold rounded-r-xl border-t border-b border-r border-purple-300/70 dark:border-purple-700/70 bg-white text-purple-900 hover:bg-purple-50 dark:bg-gray-900 dark:text-purple-200 focus:outline-none focus:ring-2 focus:ring-purple-500';

      const first = $('#rut');
      if (first) { first.focus(); first.select?.(); }

      try { localStorage.setItem('login_mode', 'rut'); } catch { }
    }

    function showPasaporte() {
      $btnRut.setAttribute('aria-selected', 'false');
      $btnPpt.setAttribute('aria-selected', 'true');

      $formRut.classList.add('hidden');
      $formRut.setAttribute('aria-hidden', 'true');
      $formPpt.classList.remove('hidden');
      $formPpt.removeAttribute('aria-hidden');

      setFormEnabled($formRut, false);
      setFormEnabled($formPpt, true);

      $btnRut.className = 'px-3 py-1.5 text-sm font-semibold rounded-l-xl border border-purple-300/70 dark:border-purple-700/70 bg-white text-purple-900 hover:bg-purple-50 dark:bg-gray-900 dark:text-purple-200 focus:outline-none focus:ring-2 focus:ring-purple-500';
      $btnPpt.className = 'px-3 py-1.5 text-sm font-semibold rounded-r-xl border-t border-b border-r border-purple-300/70 dark:border-purple-700/70 bg-purple-900 text-white hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-500';

      const first = $('#pasaporte');
      if (first) { first.focus(); first.select?.(); }

      try { localStorage.setItem('login_mode', 'pasaporte'); } catch { }
    }

    // Wire (clics de los botones)
    on($btnRut, 'click', showRut);
    on($btnPpt, 'click', showPasaporte);

    // === Sincronizar el localStorage con el backend ===
    $formRut?.addEventListener('submit', () => {
      try {
        localStorage.setItem('login_mode', 'rut');
        const hidden = $formRut.querySelector('input[name="_login_mode"]');
        if (hidden) hidden.value = 'rut';
      } catch { }
    });

    $formPpt?.addEventListener('submit', () => {
      try {
        localStorage.setItem('login_mode', 'pasaporte');
        const hidden = $formPpt.querySelector('input[name="_login_mode"]');
        if (hidden) hidden.value = 'pasaporte';
      } catch { }
    });

    // === Estado inicial: UNA sola declaración ===
    const initialMode = (() => {
      try {
        const saved = localStorage.getItem('login_mode');
        return (saved === 'pasaporte' || saved === 'rut') ? saved : 'rut';
      } catch { return 'rut'; }
    })();

    if (initialMode === 'pasaporte') showPasaporte();
    else showRut();
  });
})();
