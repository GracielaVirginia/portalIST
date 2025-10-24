<!doctype html>
<html lang="es" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Error interno - 500</title>
  <link rel="icon" href="{{ asset('favicon.ico') }}">
  <link rel="stylesheet" href="{{ asset('css/errors.css') }}">
  @vite('resources/css/app.css')
  <script>
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
  </script>
</head>
<body class="min-h-screen flex items-center justify-center bg-purple-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100 transition-colors duration-500">
  <main class="w-full max-w-lg p-8 rounded-2xl bg-white/70 dark:bg-gray-800/60 backdrop-blur-md shadow-xl ring-1 ring-black/10 dark:ring-white/10 text-center">
    <div class="flex flex-col items-center gap-3">
      <span class="text-5xl">💥</span>
      <h1 class="text-2xl font-bold text-purple-900 dark:text-purple-300">Error interno</h1>
      <button onclick="toggleDark()" class="mt-2 text-sm px-3 py-1 rounded-lg bg-purple-800 text-white hover:bg-purple-700 dark:bg-gray-700 dark:hover:bg-gray-600 transition">
        🌙 Modo Oscuro
      </button>
    </div>
    <p class="mt-6 text-gray-600 dark:text-gray-300">
      Algo salió mal en el servidor.
    </p>
    <p class="mt-2 text-gray-600 dark:text-gray-300">
      El equipo técnico ha sido notificado. Por favor, inténtalo más tarde.
    </p>
    <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4">
      <a href="{{ url('/') }}" class="px-6 py-3 rounded-lg font-semibold text-white bg-purple-900 hover:bg-purple-800 shadow-md transition">
        Volver al inicio
      </a>
      <a href="mailto:soporte@tudominio.cl" class="px-6 py-3 rounded-lg font-semibold border-2 border-purple-900 text-purple-900 hover:bg-purple-900 hover:text-white dark:border-purple-400 dark:text-purple-300 dark:hover:bg-purple-500 dark:hover:text-white transition">
        Contactar soporte
      </a>
    </div>
  </main>
</body>
</html>