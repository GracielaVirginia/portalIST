import Swal from "sweetalert2";

(() => {
    const LIFETIME_MIN = window.LaravelSessionLifetime || 20; // fallback
    const WARN_BEFORE = 60; // segundos antes de expirar
    const KEEPALIVE_URL = window.keepaliveUrl;
    const LOGOUT_URL = window.logoutUrl;
    const CSRF_TOKEN = window.csrfToken;

    let timerId, warnTimerId;
    const ms = m => m * 60 * 1000;

    function forceLogout() {
        const form = document.createElement("form");
        form.method = "POST";
        form.action = LOGOUT_URL;
        form.innerHTML = `<input type="hidden" name="_token" value="${CSRF_TOKEN}">`;
        document.body.appendChild(form);
        form.submit();
    }

    async function keepAlive() {
        await fetch(KEEPALIVE_URL, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": CSRF_TOKEN,
                "Accept": "application/json",
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ ping: Date.now() }),
        });
        Swal.close();
        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "success",
            title: "Sesión renovada",
            showConfirmButton: false,
            timer: 2000,
        });
        schedule();
    }

    function warn() {
        let remaining = WARN_BEFORE;
        Swal.fire({
            title: "Tu sesión está por expirar",
            html: `Se cerrará en <b><span id="timeout">${remaining}</span></b> segundos por inactividad.`,
            icon: "warning",
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                const interval = setInterval(() => {
                    remaining--;
                    const span = document.getElementById("timeout");
                    if (span) span.textContent = remaining;
                    if (remaining <= 0) {
                        clearInterval(interval);
                        Swal.close();
                        forceLogout();
                    }
                }, 1000);
            },
            footer: `
        <button class="swal2-confirm swal2-styled" id="stayBtn" style="background:#7c3aed;">Seguir conectado</button>
        <button class="swal2-cancel swal2-styled" id="logoutBtn">Salir ahora</button>
      `,
            didRender: () => {
                document.getElementById("stayBtn").onclick = keepAlive;
                document.getElementById("logoutBtn").onclick = forceLogout;
            },
        });
    }

    function schedule() {
        clearTimeout(timerId);
        clearTimeout(warnTimerId);
        warnTimerId = setTimeout(warn, ms(LIFETIME_MIN) - WARN_BEFORE * 1000);
        timerId = setTimeout(forceLogout, ms(LIFETIME_MIN));
    }

    schedule();
})();
