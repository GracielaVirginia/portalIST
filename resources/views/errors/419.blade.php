<!doctype html>
<html lang="es" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Sesión expirada - 419</title>
  <link rel="icon" href="{{ asset('favicon.ico') }}">
  <link rel="stylesheet" href="{{ asset('css/errors.css') }}">
  @vite('resources/css/app.css')

  <script>
    // ----- Tema oscuro -----
    function toggleDark() {
      document.documentElement.classList.toggle('dark');
      localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
    }
    (function () {
      const saved = localStorage.getItem('theme');
      if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
      }
    })();

    // ----- Redirección automática -----
    document.addEventListener('DOMContentLoaded', () => {
      const countdownEl = document.getElementById('countdown');
      const redirectUrl = "{{ route('/') }}"; // cambia a donde quieras dirigir
      let seconds = 10;

      const updateCountdown = () => {
        countdownEl.textContent = seconds;
        if (seconds <= 0) {
          window.location.href = redirectUrl;
        } else {
          seconds--;
          setTimeout(updateCountdown, 1000);
        }
      };
      updateCountdown();

      // Botón “volver ahora”
      document.getElementById('goNow').addEventListener('click', () => {
        window.location.href = redirectUrl;
      });
    });
  </script>
</head>

<body class="min-h-screen flex items-center justify-center bg-purple-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100 transition-colors duration-500">

  <main class="w-full max-w-lg p-8 rounded-2xl bg-white/70 dark:bg-gray-800/60 backdrop-blur-md shadow-xl ring-1 ring-black/10 dark:ring-white/10 text-center">
    <div class="flex flex-col items-center gap-3">
      <span class="text-5xl">🔒</span>
      <h1 class="text-2xl font-bold text-purple-900 dark:text-purple-300">Sesión cerrada por seguridad</h1>

      <button id="themeToggle" onclick="toggleDark()"
              class="mt-2 text-sm px-3 py-1 rounded-lg bg-purple-800 text-white hover:bg-purple-700 dark:bg-gray-700 dark:hover:bg-gray-600 transition">
        🌙 Modo Oscuro
      </button>
    </div>

    <p class="mt-6 text-gray-700 dark:text-gray-300 text-sm leading-relaxed">
      Tu sesión ha expirado por seguridad o el tiempo de inactividad fue prolongado.
    </p>
    <p class="mt-2 text-gray-700 dark:text-gray-300 text-sm leading-relaxed">
      Serás redirigido automáticamente al inicio de sesión en <strong><span id="countdown">10</span> segundos</strong>.
    </p>
    <p class="mt-2 text-gray-700 dark:text-gray-300 text-sm leading-relaxed">
      También puedes hacerlo manualmente presionando el siguiente botón:
    </p>

    <div class="mt-8 flex flex-col sm:flex-row justify-center gap-4">
      <button id="goNow"
              class="px-6 py-3 rounded-lg font-semibold text-white bg-purple-900 hover:bg-purple-800 shadow-md transition">
        Volver ahora al sistema
      </button>
      <a href="{{ url('/login-admin') }}"
         class="px-6 py-3 rounded-lg font-semibold text-white bg-purple-900 hover:bg-purple-800 shadow-md transition">
        Ir al panel del Administrador
      </a>
    </div>
  </main>

</body>
</html>
