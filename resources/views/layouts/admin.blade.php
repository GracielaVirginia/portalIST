<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}" />

  <title>@yield('title', 'Admin · IST')</title>

  {{-- Tailwind / Vite --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  {{-- Modo oscuro temprano --}}
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

<body class="min-h-screen transition-colors duration-300 bg-purple-100/50 dark:bg-gray-900" data-color="purple">
  {{-- ===== Overlay Skeleton (admin) ===== --}}
  <x-ui.skeleton-admin-overlay id="pageSkeletonOverlay" class="hidden" />

  <div class="min-h-screen flex flex-col" x-data="{ helpOpen:false, openManual:false, openVideo:false, openFaq:false }">
    {{-- Toggle dark flotante, si lo usas --}}
    @include('components.dark-toggle')

    {{-- ===== Flash messages ===== --}}
    <div class="fixed top-4 left-1/2 -translate-x-1/2 z-50 w-full max-w-4xl px-4 pointer-events-none" aria-live="polite" aria-atomic="true">
      @foreach (['success' => ['green','✅'], 'error' => ['red','❌'], 'warning' => ['yellow','⚠️'], 'info' => ['sky','ℹ️']] as $key => [$color, $icon])
        @if (session($key))
          <div class="mb-3 pointer-events-auto" x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)">
            <div x-show="show" x-transition.opacity.duration.300ms role="alert"
                 class="rounded-xl border border-{{ $color }}-300 dark:border-{{ $color }}-700
                        bg-{{ $color }}-50 dark:bg-{{ $color }}-900/30
                        text-{{ $color }}-800 dark:text-{{ $color }}-200
                        px-4 py-3 font-semibold shadow-sm">
              {{ $icon }} {{ session($key) }}
            </div>
          </div>
        @endif
      @endforeach
    </div>

    {{-- ===== Contenido de vistas admin ===== --}}
    <main id="content" class="flex-grow">
      @yield('admin')
    </main>

    {{-- ===== Footer compacto admin ===== 
    <footer class="fixed bottom-0 left-0 w-full z-40">
      <div class="mx-auto max-w-7xl px-4 py-3
                  flex flex-col sm:flex-row items-center justify-center sm:justify-between gap-2
                  bg-purple-100/90 dark:bg-gray-900/90 backdrop-blur
                  border-t border-purple-200/60 dark:border-gray-800
                  text-purple-900 dark:text-gray-200">
        <p class="text-sm">Versión 3.0.0 · &copy; {{ date('Y') }} Todos los derechos reservados.</p>
        <div class="flex items-center gap-3">
          <a href="#" @click.prevent="helpOpen = true"
             class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-sm font-semibold
                    bg-white text-purple-900 border border-purple-200
                    hover:bg-purple-50 hover:border-purple-300
                    dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700 dark:hover:bg-gray-700">
            🛈 <span>Ayuda</span>
          </a>
        </div>
      </div>
    </footer>--}}

    {{-- Bot / otros --}}
    <x-portal.assistant-bot />

    {{-- ======= Modales de ayuda (opcional, igual que tenías) ======= --}}
    {{-- … (deja aquí tus modales Manual/Video/FAQ si los usas) … --}}
  </div>

  <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

  {{-- ===== Núcleo del tema (oscuro/tema) ===== --}}
  <script>
    function getCurrentDark(){ if('theme' in localStorage) return localStorage.theme==='dark'; return window.matchMedia('(prefers-color-scheme: dark)').matches; }
    function applyDarkMode(isDark){ document.documentElement.classList.toggle('dark', isDark); localStorage.theme = isDark ? 'dark' : 'light'; }
    document.addEventListener('DOMContentLoaded', () => {
      applyDarkMode(getCurrentDark());
      document.getElementById('floatingDarkToggle')?.addEventListener('click', () => applyDarkMode(!getCurrentDark()));
    });
  </script>

  {{-- ===== Skeleton controller: clicks internos + submit de formularios ===== --}}
  <script>
    (function(){
      const overlay = document.getElementById('pageSkeletonOverlay');
      if(!overlay) return;
      let locked = false; // evita doble-show

      function show(){ if(locked) return; locked = true; overlay.classList.remove('hidden'); overlay.classList.add('flex'); }
      function hide(){ locked = false; overlay.classList.add('hidden'); overlay.classList.remove('flex'); }

      // 1) Click en enlaces internos
      document.addEventListener('click', (e) => {
        const link = e.target.closest('a');
        if (!link) return;

        const url = link.getAttribute('href') || '';
        if (!url || url.startsWith('#') || link.target === '_blank' || link.hasAttribute('download') || link.dataset.noSkeleton !== undefined) return;

        const dest = new URL(url, location.href);
        if (dest.origin !== location.origin) return; // externos no

        show();
      }, { capture:true });

      // 2) Submit de formularios (no-AJAX)
      document.addEventListener('submit', (e) => {
        const form = e.target;
        if (form.matches('[data-no-skeleton]')) return;
        // si el form define target _blank o tiene preventDefault en línea, no mostramos
        if (form.getAttribute('target') === '_blank') return;
        // Solo mostrar si realmente va a navegar (caso estándar POST/GET)
        show();
      }, { capture:true });

      // 3) Ocultar si la página vuelve desde bfcache (atrás/adelante)
      window.addEventListener('pageshow', () => hide());

      // 4) Exponer helpers por si quieres usar: window.AdminSkeleton.show/hide
      window.AdminSkeleton = { show, hide };
    })();
  </script>
<script>
  window.LaravelSessionLifetime = @json(config('session.lifetime', 20));
  window.keepaliveUrl = @json(route('session.keepalive'));
  window.logoutUrl = @json(route('logout'));
  window.csrfToken = @json(csrf_token());
</script>

@vite(['resources/js/app.js'])

  {{-- Tu app.js si lo necesitas aparte --}}
  <script src="{{ asset('js/app.js') }}"></script>
  @stack('scripts')
</body>
</html>
