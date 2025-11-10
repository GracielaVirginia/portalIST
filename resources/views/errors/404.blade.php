<!doctype html>
<html lang="es" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Página no encontrada - 404</title>
  <link rel="icon" href="{{ asset('favicon.ico') }}">
  <link rel="stylesheet" href="{{ asset('css/errors.css') }}">
  @vite('resources/css/app.css')

  <script>
    // ----- Modo oscuro -----
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

    // ----- Mostrar/ocultar "Volver atrás" según referrer -----
    document.addEventListener('DOMContentLoaded', () => {
      const backBtn = document.getElementById('goBack');
      try {
        const ref = document.referrer || '';
        const sameOrigin = ref.startsWith(location.origin);
        if (sameOrigin && history.length > 1) {
          backBtn.classList.remove('hidden');
          backBtn.addEventListener('click', () => history.back());
        }
      } catch (_) { /* no-op */ }
    });
  </script>
</head>

<body class="min-h-screen flex items-center justify-center bg-purple-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100 transition-colors duration-500">

  <main role="main" aria-labelledby="title404"
        class="w-full max-w-lg p-8 rounded-2xl bg-white/80 dark:bg-gray-800/70 backdrop-blur-md shadow-xl ring-1 ring-black/10 dark:ring-white/10 text-center">
    <div class="flex flex-col items-center gap-3">
      <span aria-hidden="true" class="text-5xl">🔍</span>
      <h1 id="title404" tabindex="-1"
          class="text-2xl sm:text-3xl font-bold text-purple-900 dark:text-purple-300">
        No pudimos encontrar esa página
      </h1>

      <button id="themeToggle" onclick="toggleDark()"
              class="mt-1 text-xs sm:text-sm px-3 py-1 rounded-lg bg-purple-800 text-white hover:bg-purple-700 dark:bg-gray-700 dark:hover:bg-gray-600 transition">
        🌙 Modo oscuro
      </button>
    </div>

    <p class="mt-6 text-gray-700 dark:text-gray-300 leading-relaxed">
      Es posible que el enlace haya cambiado o que la dirección esté mal escrita.
    </p>
    <p class="mt-2 text-gray-700 dark:text-gray-300 leading-relaxed">
      Puedes volver a la pantalla principal o regresar a la página anterior.
    </p>

    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-3">
      {{-- Botón principal: Portal Pacientes (mantengo tu ruta) --}}
      <a href="{{ route('login') }}"
         class="inline-flex items-center justify-center px-6 py-3 rounded-lg font-semibold text-white bg-purple-900 hover:bg-purple-800 shadow-md transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-600">
        Ir al Portal Pacientes
      </a>

      {{-- Portal Administrativo (mantengo tu URL) --}}
      <a href="{{ url('/login-admin') }}"
         class="inline-flex items-center justify-center px-6 py-3 rounded-lg font-semibold border-2 border-purple-900 text-purple-900 hover:bg-purple-900 hover:text-white dark:border-purple-400 dark:text-purple-300 dark:hover:bg-purple-500 dark:hover:text-white transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-600">
        Ir al Portal Administrativo
      </a>
    </div>

    {{-- Botón volver atrás: solo visible si hay referrer del mismo dominio --}}
    <div class="mt-4">
      <button id="goBack"
              class="hidden inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400">
        ⬅️ Volver a la página anterior
      </button>
    </div>

    {{-- Sugerencia corta para usuarios mayores --}}
    <p class="mt-6 text-[13px] text-gray-500 dark:text-gray-400">
      Consejo: si escribiste la dirección a mano, revisa que no falten letras o puntos.
    </p>
  </main>

</body>
</html>
