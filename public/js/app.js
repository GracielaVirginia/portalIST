(function () {
    // ===== Helpers =====
    const $ = (sel, root = document) => root.querySelector(sel);
    const on = (el, ev, fn) => el && el.addEventListener(ev, fn);

    document.addEventListener('DOMContentLoaded', () => {
        // Nodos
        const $cfg = $('#verifConfig');   // <div id="verifConfig" data-url="..." data-csrf="...">
        const $rut = $('#rut');
        const $password = $('#password');
        const $togglePass = $('#togglePassword'); // <-- ojo: ID, no clase
        const $feedback = $('#rutFeedback');
        const $btn = $('#btnLogin');

        let rutVerified = false;
        let debounceId = null;

        if (!$cfg || !$rut || !$password || !$btn) {
            console.warn('[login] Falta algún nodo', {
                hasCfg: !!$cfg, hasRut: !!$rut, hasPass: !!$password, hasBtn: !!$btn
            });
            return;
        }

        // ----- Formateo RUT -----
        function formatRutStr(str) {
            const clean = (str || '').toUpperCase().replace(/[^0-9K]/g, '');
            return clean.length > 1 ? clean.slice(0, -1) + '-' + clean.slice(-1) : clean;
        }
        function formatRutInput() {
            const before = $rut.selectionStart;
            $rut.value = formatRutStr($rut.value || '');
            try { $rut.setSelectionRange(before, before); } catch (_) { }
        }

        // ----- Submit habilitado? -----
        function updateSubmitState() {
            const canSubmit = rutVerified && ($password.value || '').trim().length > 0;
            $btn.disabled = !canSubmit;
            $btn.className = canSubmit
                ? 'px-3 py-1.5 text-sm rounded-lg bg-violet-900 dark:bg-gray-700 text-white transition hover:bg-violet-800 dark:hover:bg-gray-600 border border-ring'
                : 'px-3 py-1.5 text-sm rounded-lg bg-gray-200 text-gray-500 cursor-not-allowed border border-ring transition';
        }

        // ----- Ver/Ocultar password 3s -----
        function wirePasswordToggle() {
            if (!$togglePass || !$password) return;
            // evita registrar múltiples listeners
            $togglePass.replaceWith($togglePass.cloneNode(true));
            const btn = $('#togglePassword');
            on(btn, 'click', () => {
                const wasDisabled = $password.disabled;
                if (wasDisabled) $password.disabled = false;
                const prevType = $password.type;
                $password.type = 'text';
                btn.textContent = '🙈';
                setTimeout(() => {
                    $password.type = prevType;
                    btn.textContent = '👁️';
                    if (wasDisabled) $password.disabled = true;
                }, 3000);
            });
        }

        // ----- Peek body para debug cuando no es JSON -----
        async function peekResponseBody(res) {
            try { return (await res.clone().text()).slice(0, 200); }
            catch { return '<no-readable-body>'; }
        }

        // ----- Verificar RUT -----
        async function verifyRut() {
            const raw = ($rut.value || '').trim();
            if (raw.length < 3) {
                rutVerified = false;
                $feedback.textContent = '';
                $password.disabled = true;
                $('#togglePassword').disabled = true;
                updateSubmitState();
                return;
            }

            const url = $cfg.dataset.url;
            const csrf = $cfg.dataset.csrf;
            if (!url) {
                console.warn('[verifyRut] URL inexistente en #verifConfig');
                return;
            }

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ rut: raw })
                });

                if (!res.ok) {
                    const peek = await peekResponseBody(res);
                    console.warn('[verifyRut] no OK', res.status, res.statusText, peek);
                    if (res.status === 419) {
                        $feedback.textContent = 'Sesión/CSRF inválido (419). Recarga la página.';
                    } else if (res.status === 302) {
                        $feedback.textContent = 'Redirigido (302). ¿La ruta está bajo auth?';
                    } else {
                        $feedback.textContent = 'Error verificando RUT.';
                    }
                    $feedback.className = 'text-xs mt-1 text-amber-600';
                    rutVerified = false;
                    $password.disabled = true;
                    $('#togglePassword').disabled = true;
                    updateSubmitState();
                    return;
                }

                const data = await res.json();
                rutVerified = !!data?.exists;

                if (rutVerified) {
                    $feedback.textContent = data.name ? `Paciente: ${data.name}` : 'Paciente encontrado.';
                    $feedback.className = 'text-xs mt-1 text-emerald-600';
                    $password.disabled = false;
                    $('#togglePassword').disabled = false;
                    wirePasswordToggle();
                    $password.focus();
                } else {
                    $feedback.textContent = data?.message || 'Paciente no registrado.';
                    $feedback.className = 'text-xs mt-1 text-red-600';
                    $password.value = '';
                    $password.disabled = true;
                    $('#togglePassword').disabled = true;
                }
                updateSubmitState();
            } catch (e) {
                console.error('[verifyRut] fetch error', e);
                $feedback.textContent = 'No se pudo verificar el RUT. Intenta nuevamente.';
                $feedback.className = 'text-xs mt-1 text-amber-600';
                rutVerified = false;
                $password.disabled = true;
                $('#togglePassword').disabled = true;
                updateSubmitState();
            }
        }

        // ----- Eventos -----
        on($rut, 'input', () => {
            formatRutInput();
            clearTimeout(debounceId);
            debounceId = setTimeout(verifyRut, 350); // debounce
        });
        on($rut, 'blur', verifyRut);
        on($password, 'input', updateSubmitState);

        // Estado inicial
        formatRutInput();
        updateSubmitState();

        // Log de arranque
        console.log('[verifyRut:init]', {
            url: $cfg.dataset.url,
            hasCsrf: !!$cfg.dataset.csrf,
            nodes: { rut: !!$rut, password: !!$password, btn: !!$btn }
        });
    });
})();
