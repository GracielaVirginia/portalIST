<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}" />

  <title>@yield('title', 'Portal Pacientes')</title>

  {{-- Tailwind v4 (ya contiene tus tokens y temas) --}}
@vite(['resources/css/app.css', 'resources/js/app.js'])

  {{-- Aplicar modo oscuro temprano --}}
  <script>
    (function () {
      try {
        const saved = localStorage.getItem('theme');
        const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        if (saved === 'dark' || (!saved && systemDark)) {
          document.documentElement.classList.add('dark');
        } else {
          document.documentElement.classList.remove('dark');
        }
      } catch (_) {}
    })();
  </script>
  <style>[x-cloak]{ display:none !important; }</style>
</head>

<body class="min-h-screen transition-colors duration-300" data-color="purple">
  <div class="min-h-screen flex flex-col">

    <main id="content" class="flex-grow">
      {{-- ===== Flash messages (autocierra en 3s) ===== --}}
@foreach (['success' => ['green','✅'], 'error' => ['red','❌'], 'warning' => ['yellow','⚠️'], 'info' => ['sky','ℹ️']] as $key => [$color, $icon])
  @if (session($key))
    <div class="max-w-4xl mx-auto mt-4 mb-6 px-4"
         x-data="{ show: true }"
         x-init="setTimeout(() => show = false, 3000)">
      <div x-show="show"
           x-transition.opacity.duration.300ms
           role="alert"
           class="rounded-xl border border-{{ $color }}-300 dark:border-{{ $color }}-700
                  bg-{{ $color }}-50 dark:bg-{{ $color }}-900/30
                  text-{{ $color }}-800 dark:text-{{ $color }}-200
                  px-4 py-3 font-semibold shadow-sm">
        {{ $icon }} {{ session($key) }}
      </div>
    </div>
  @endif
@endforeach

      @yield('content')
    </main>

  {{-- ===== Footer ===== --}}
  <footer class="fixed bottom-0 left-0 w-full text-center py-3 bg-purple-100 dark:bg-gray-800 text-purple-900 dark:text-gray-300">
    <p>Versión 3.0.0 | &copy; {{ date('Y') }} Todos los derechos reservados.</p>
  </footer>
  </div>

  {{-- Componentes flotantes --}}
  {{-- @include('components.theme-selector')  --}} {{-- eliminado: quitamos el componente de temas --}}
  @include('components.dark-toggle')
  @include('components.help-panel')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  {{-- ==== Núcleo del Tema ==== --}}
  <script>
    // --- Oscuro / Claro ---
    function getCurrentDark() {
      if ('theme' in localStorage) return localStorage.theme === 'dark';
      return window.matchMedia('(prefers-color-scheme: dark)').matches;
    }

    function applyDarkMode(isDark) {
      document.documentElement.classList.toggle('dark', isDark);
      localStorage.theme = isDark ? 'dark' : 'light';

      // Si existiera el panel antiguo, lo ocultamos en oscuro (no rompe si no está)
      const panel = document.getElementById('themePanel');
      if (panel) panel.classList.toggle('hidden', isDark);
    }

    function toggleDark() {
      applyDarkMode(!getCurrentDark());
    }

    // --- Colores de Marca ---
    // Mantenemos la firma y el nombre para no romper nada,
    // pero forzamos SIEMPRE 'purple' en claro y no hacemos cambios en oscuro.
    function applyColorTheme(name) {
      const rootEl = document.body;
      const enforced = 'purple';
      rootEl.setAttribute('data-color', enforced);
      localStorage.setItem('colorTheme', enforced);

      // Si quedara algún botón del panel previo, marcamos 'purple' como activo sin error
      const panel = document.getElementById('themePanel');
      if (panel) {
        panel.querySelectorAll('.theme-option').forEach(btn => {
          const active = btn.getAttribute('data-color') === enforced;
          btn.classList.toggle('ring-2', active);
          btn.classList.toggle('ring-offset-2', active);
          btn.classList.toggle('ring-primary', active);
        });
      }
    }

    function changeTheme(name) {
      // Conservamos la API pública, pero no cambiamos el color en oscuro
      if (!getCurrentDark()) applyColorTheme('purple');
    }

    // --- Inicialización ---
    document.addEventListener('DOMContentLoaded', () => {
      const dark = getCurrentDark();
      applyDarkMode(dark);

      // Forzar purple en claro SIEMPRE
      if (!dark) applyColorTheme('purple');

      document.getElementById('floatingDarkToggle')?.addEventListener('click', toggleDark);
    });

    // Exportar globalmente (se mantienen los nombres)
    window.changeTheme = changeTheme;
  </script>

  <script src="{{ asset('js/app.js') }}"></script>
  @stack('scripts')
</body>
</html>
