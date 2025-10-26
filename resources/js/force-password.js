(() => {
    const $ = (sel, root = document) => root.querySelector(sel);
    const on = (el, ev, fn, opts) => el && el.addEventListener(ev, fn, opts);

    const modal = document.getElementById('modalPassword');
    if (!modal) return;
    const panel = modal.firstElementChild || modal;

    // Lock flag + estilos forzados
    modal.setAttribute('data-locked', '1');

    // Bloquear scroll detrás
    document.documentElement.style.overflow = 'hidden';
    document.body.style.overflow = 'hidden';

    // Mantener visible aunque intenten ocultarlo
    const ensureVisible = () => {
        const cs = getComputedStyle(modal);
        if (cs.display === 'none' || modal.classList.contains('hidden') ||
            cs.visibility === 'hidden' || cs.opacity === '0') {
            modal.style.setProperty('display', 'flex', 'important');
            modal.classList.remove('hidden', 'invisible', 'opacity-0', 'pointer-events-none');
            modal.style.setProperty('visibility', 'visible', 'important');
            modal.style.setProperty('opacity', '1', 'important');
            modal.style.setProperty('pointer-events', 'auto', 'important');
        }
    };
    new MutationObserver(ensureVisible).observe(modal, { attributes: true, attributeFilter: ['class', 'style'] });
    ensureVisible();

    // Desactivar ESC SOLO en este modal
    const stop = (e) => { e.preventDefault(); e.stopImmediatePropagation(); };
    on(modal, 'keydown', (e) => { if (e.key === 'Escape') stop(e); }, true);

    // Neutralizar cualquier botón/atributo de cerrar dentro del modal
    modal.querySelectorAll('[data-modal-hide],[data-dismiss],.modal-close,[aria-label="Close"],#closePasswordModal')
        .forEach(el => on(el, 'click', stop, true));

    // Evitar clic fuera para cerrar
    on(modal, 'click', (e) => {
        if (!panel.contains(e.target)) stop(e);
    }, true);

    // OJITOS: mostrar 3s
    const autoHideTimers = new WeakMap();
    function showFor3s(input, setIcon) {
        if (!input) return;
        const prev = autoHideTimers.get(input);
        if (prev) clearTimeout(prev);
        input.type = 'text';
        setIcon && setIcon('🙈');
        const tid = setTimeout(() => {
            input.type = 'password';
            setIcon && setIcon('👁️');
            autoHideTimers.delete(input);
        }, 3000);
        autoHideTimers.set(input, tid);
    }

    document.addEventListener('click', (e) => {
        const btn = e.target.closest('#togglePassword, #togglePasswordNew, #togglePasswordConfirm');
        if (!btn) return;
        e.preventDefault();
        const map = {
            togglePassword: 'current_password',
            togglePasswordNew: 'new_password',
            togglePasswordConfirm: 'new_password_confirmation'
        };
        const input = document.getElementById(map[btn.id]);
        if (!(input instanceof HTMLInputElement)) return;
        showFor3s(input, (txt) => { btn.innerHTML = txt; });
    }, true);

    // Validación en tiempo real
    const pwd = $('#new_password');
    const pwd2 = $('#new_password_confirmation');
    const err = $('#password-error-message');
    const form = $('#passwordChangeForm');
    const submitBtn = form?.querySelector('button[type="submit"]');

    const ckLen = $('#check-length');
    const ckUp = $('#check-uppercase');
    const ckNum = $('#check-number');

    function validatePassword() {
        const v = (pwd?.value ?? '');
        const okLen = v.length >= 8;
        const okUp = /[A-Z]/.test(v);
        const okNum = /[0-9]/.test(v);
        if (ckLen) ckLen.checked = okLen;
        if (ckUp) ckUp.checked = okUp;
        if (ckNum) ckNum.checked = okNum;
        return okLen && okUp && okNum;
    }

    function syncState() {
        const valid = validatePassword() && (pwd2?.value ?? '') === (pwd?.value ?? '');
        if (err) err.classList.toggle('hidden', valid);
        if (submitBtn) submitBtn.disabled = !valid;
    }

    on(pwd, 'input', syncState);
    on(pwd2, 'input', syncState);
    syncState();

    on(form, 'submit', (e) => {
        const valid = validatePassword() && (pwd2?.value ?? '') === (pwd?.value ?? '');
        if (!valid) { e.preventDefault(); if (err) err.classList.remove('hidden'); }
    });

    // ===== PATCH: evitar diálogo "¿Deseas abandonar el sitio?" al guardar =====
    let isSubmitting = false;

    // Nuestro handler: solo muestra diálogo si hay lock y NO se está enviando
    const beforeUnloadHandler = (e) => {
        if (modal.getAttribute('data-locked') === '1' && !isSubmitting) {
            e.preventDefault();
            e.returnValue = '';
        }
    };
    window.addEventListener('beforeunload', beforeUnloadHandler);

    // Función que desarma todos los beforeunload al iniciar un envío real
    function armSafeSubmit() {
        if (isSubmitting) return;
        isSubmitting = true;

        // bajar lock y reactivar scroll
        modal.setAttribute('data-locked', '0');
        document.documentElement.style.overflow = '';
        document.body.style.overflow = '';

        // quitar nuestro listener y anular cualquier onbeforeunload global
        window.removeEventListener('beforeunload', beforeUnloadHandler);
        window.onbeforeunload = null;

        // mini-guard por si otro script intenta enganchar beforeunload en ese tick
        setTimeout(() => {
            const guard = () => { if (isSubmitting) window.onbeforeunload = null; };
            window.addEventListener('beforeunload', guard, { once: true, capture: true });
        }, 0);
    }

    // Marcar envío lo más temprano posible (click, touch, Enter y submit)
    submitBtn?.addEventListener('mousedown', armSafeSubmit, { capture: true });
    submitBtn?.addEventListener('touchstart', armSafeSubmit, { capture: true });
    form?.addEventListener('keydown', (e) => { if (e.key === 'Enter') armSafeSubmit(); }, { capture: true });
    form?.addEventListener('submit', () => { armSafeSubmit(); }, { capture: true });
})();
