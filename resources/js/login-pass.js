    (function () {
  // ===== Helpers =====
  const $  = (sel, root = document) => root.querySelector(sel);
  const on = (el, ev, fn) => el && el.addEventListener(ev, fn);

  document.addEventListener('DOMContentLoaded', () => {
    // Nodos
    const $cfg        = $('#verifConfig'); // tiene data-url-pasaporte y data-csrf
    const $ppt        = $('#pasaporte');
    const $passPpt    = $('#passworPasaporte');          // (sin "d", tal como pediste)
    const $togglePpt  = $('#togglePasswordPasaporte');
    const $fbPpt      = $('#pasaporteFeedback');
    const $btnPpt     = $('#btnLoginPasaporte');

    let pptVerified = false;
    let debounceId  = null;

    if (!$cfg || !$ppt || !$passPpt || !$btnPpt) {
        console.warn('[login:ppt] Falta algún nodo', {
            hasCfg: !!$cfg, hasPasaporte: !!$ppt, hasPass: !!$passPpt, hasBtn: !!$btnPpt
        });
    return;
    }

    // ----- Normaliza entrada (solo visual, NO formatea con guiones/puntos) -----
    function normalizePasaporteInput() {
      // Mantén visible lo que el usuario escribe pero forzamos MAYÚSCULA
      const pos = $ppt.selectionStart;
    $ppt.value = ($ppt.value || '').toUpperCase();
    try {$ppt.setSelectionRange(pos, pos); } catch (_) { }
    }

    // ----- Habilitar submit? -----
    function updateSubmitStatePasaporte() {
      const canSubmit = pptVerified && ($passPpt.value || '').trim().length > 0;
    $btnPpt.disabled = !canSubmit;
    // (mismos estilos que usas en el botón)
    $btnPpt.className = canSubmit
            ? 'px-3 py-1.5 text-sm rounded-lg bg-violet-900 dark:bg-gray-700 text-white transition hover:bg-violet-800 dark:hover:bg-gray-600 border border-ring'
            : 'px-3 py-1.5 text-sm rounded-lg bg-gray-200 text-gray-500 cursor-not-allowed border border-ring transition';
    }

    // ----- Toggle "ver contraseña" por 3s -----
    function wirePasswordTogglePasaporte() {
      if (!$togglePpt || !$passPpt) return;
    // evita duplicar listeners
    $togglePpt.replaceWith($togglePpt.cloneNode(true));
    const btn = $('#togglePasswordPasaporte');
      on(btn, 'click', () => {
        const wasDisabled = $passPpt.disabled;
    if (wasDisabled) $passPpt.disabled = false;
    const prevType = $passPpt.type;
    $passPpt.type = 'text';
    btn.textContent = '🙈';
        setTimeout(() => {
        $passPpt.type = prevType;
    btn.textContent = '👁️';
    if (wasDisabled) $passPpt.disabled = true;
        }, 3000);
      });
    }

    // ----- Peek body para diagnósticos cuando no hay JSON -----
    async function peekResponseBody(res) {
      try { return (await res.clone().text()).slice(0, 200); }
    catch { return '<no-readable-body>'; }
    }

        // ----- Verificar PASAPORTE -----
        async function verifyPasaporte() {
      const raw = ($ppt.value || '').trim();
        if (raw.length < 3) {
            pptVerified = false;
        $fbPpt.textContent = '';
        $passPpt.disabled = true;
        $('#togglePasswordPasaporte').disabled = true;
        updateSubmitStatePasaporte();
        return;
      }

        const url  = $cfg.dataset.urlPasaporte || $cfg.dataset.urlPasaporte || $cfg.dataset.urlpasaporte;
        const csrf = $cfg.dataset.csrf;
        if (!url) {
            console.warn('[verifyPasaporte] URL inexistente (data-url-pasaporte) en #verifConfig');
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
        body: JSON.stringify({pasaporte: raw })
        });

        if (!res.ok) {
          const peek = await peekResponseBody(res);
        console.warn('[verifyPasaporte] no OK', res.status, res.statusText, peek);
        if (res.status === 419) {
            $fbPpt.textContent = 'Sesión/CSRF inválido (419). Recarga la página.';
          } else if (res.status === 302) {
            $fbPpt.textContent = 'Redirigido (302). ¿La ruta está bajo auth?';
          } else {
            $fbPpt.textContent = 'Error verificando Pasaporte.';
          }
        $fbPpt.className = 'text-xs mt-1 text-amber-600';
        pptVerified = false;
        $passPpt.disabled = true;
        $('#togglePasswordPasaporte').disabled = true;
        updateSubmitStatePasaporte();
        return;
        }

        const data = await res.json();
        // Manejo de bloqueo inmediato (además de tus interceptores globales)
        if (data && (data.bloqueado === true || data.blocked === true)) {
          // Si ya tienes fireAlert() global, úsalo:
          if (typeof fireAlert === 'function') {
            fireAlert(data.message);
          } else if (window.Swal) {
            Swal.fire({
                icon: 'error',
                title: 'Cuenta bloqueada',
                text: data.message || 'Paciente Bloqueado. Comunícate con el Administrador',
                confirmButtonText: 'Contactar soporte',
                confirmButtonColor: '#7c3aed',
                allowOutsideClick: false,
                backdrop: 'rgba(0,0,0,0.6)'
            }).then(r => {
                if (r.isConfirmed) window.location.href = "{{ route('soporte.create') }}";
            });
          }
        // Estado UI
        pptVerified = false;
        $fbPpt.textContent = data?.message || 'Cuenta bloqueada.';
        $fbPpt.className = 'text-xs mt-1 text-red-600';
        $passPpt.value = '';
        $passPpt.disabled = true;
        $('#togglePasswordPasaporte').disabled = true;
        updateSubmitStatePasaporte();
        return;
        }

        pptVerified = !!data?.exists;

        if (pptVerified) {
            $fbPpt.textContent = data.name ? `Paciente: ${data.name}` : 'Paciente encontrado.';
        $fbPpt.className = 'text-xs mt-1 text-emerald-600';
        $passPpt.disabled = false;
        $('#togglePasswordPasaporte').disabled = false;
        wirePasswordTogglePasaporte();
        $passPpt.focus();
        } else {
            $fbPpt.textContent = data?.message || 'Paciente no registrado.';
        $fbPpt.className = 'text-xs mt-1 text-red-600';
        $passPpt.value = '';
        $passPpt.disabled = true;
        $('#togglePasswordPasaporte').disabled = true;
        }
        updateSubmitStatePasaporte();
      } catch (e) {
            console.error('[verifyPasaporte] fetch error', e);
        $fbPpt.textContent = 'No se pudo verificar el Pasaporte. Intenta nuevamente.';
        $fbPpt.className = 'text-xs mt-1 text-amber-600';
        pptVerified = false;
        $passPpt.disabled = true;
        $('#togglePasswordPasaporte').disabled = true;
        updateSubmitStatePasaporte();
      }
    }

    // ----- Eventos -----
    on($ppt, 'input', () => {
            normalizePasaporteInput();
        clearTimeout(debounceId);
        debounceId = setTimeout(verifyPasaporte, 350); // debounce
    });
        on($ppt, 'blur', verifyPasaporte);
        on($passPpt, 'input', updateSubmitStatePasaporte);

        // Estado inicial
        normalizePasaporteInput();
        updateSubmitStatePasaporte();

        console.log('[verifyPasaporte:init]', {
            url: $cfg.dataset.urlPasaporte,
        hasCsrf: !!$cfg.dataset.csrf,
        nodes: {pasaporte: !!$ppt, password: !!$passPpt, btn: !!$btnPpt }
    });
  });
})();
