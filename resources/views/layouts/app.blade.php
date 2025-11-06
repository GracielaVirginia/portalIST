<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}" />

  <title>@yield('title', 'Portal Pacientes')</title>
{{-- Overlay del skeleton de carga entre páginas --}}
<x-ui.skeleton-overlay />
  {{-- Tailwind v4 --}}
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
<div class="min-h-screen flex flex-col" x-data="{ helpOpen:false, openManual:false, openVideo:false, openFaq:false }">
  @include('components.dark-toggle')

  {{-- ===== Flash messages ===== --}}
  <div
    class="fixed top-4 left-1/2 -translate-x-1/2 z-50 w-full max-w-4xl px-4 pointer-events-none"
    aria-live="polite" aria-atomic="true">
    @foreach (['success' => ['green','✅'], 'error' => ['red','❌'], 'warning' => ['yellow','⚠️'], 'info' => ['sky','ℹ️']] as $key => [$color, $icon])
      @if (session($key))
        <div class="mb-3 pointer-events-auto"
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
  </div>

  <main id="content" class="flex-grow">
<button id="modoFacil"
  type="button"
  aria-pressed="false"
  class="w-full sm:w-auto px-4 py-3 text-base font-semibold rounded-full
         bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white
         shadow-md hover:shadow-lg hover:from-violet-700 hover:to-fuchsia-700
         focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-400
         dark:from-violet-600 dark:to-fuchsia-600 dark:hover:from-violet-700 dark:hover:to-fuchsia-700
         transition-all duration-200 ease-in-out">
  <span class="block leading-tight">
    <span class="block" data-label>Activar modo fácil</span>
    <span class="block text-white/90 text-xs font-normal">
      <strong data-state>Desactivado</strong>
    </span>
  </span>
</button>

<script>
  (function () {
    const btn = document.getElementById('modoFacil');
    const KEY = 'modoFacilOn';

    function apply(on) {
      btn.setAttribute('aria-pressed', String(on));
      btn.querySelector('[data-label]').textContent = on ? 'Desactivar modo fácil' : 'Activar modo fácil';
      btn.querySelector('[data-state]').textContent = on ? 'Activado' : 'Desactivado';
      document.documentElement.classList.toggle('modo-facil', on);
      localStorage.setItem(KEY, on ? '1' : '0');
    }

    // Restaurar preferencia
    apply(localStorage.getItem(KEY) === '1');

    // Alternar
    btn.addEventListener('click', () => {
      const on = btn.getAttribute('aria-pressed') !== 'true';
      apply(on);
    });
  })();
</script>

<script>
document.getElementById('modoFacil').addEventListener('click', () => {
  document.body.classList.toggle('modo-facil');
});
</script>

<style>
.modo-facil * { font-size: 1.25rem !important; }
.modo-facil button, .modo-facil input { border-width: 3px; }
.modo-facil { background-color: #fefefe; color: #111; }
</style>
    @yield('content')
  </main>

  {{-- ===== Footer ===== --}}
  <footer class="fixed bottom-0 left-0 w-full z-40">
    <div class="mx-auto max-w-7xl px-4 py-3
                flex flex-col sm:flex-row items-center justify-center sm:justify-between gap-2
                bg-purple-100/90 dark:bg-gray-900/90 backdrop-blur
                border-t border-purple-200/60 dark:border-gray-800
                text-purple-900 dark:text-gray-200">

      <p class="text-sm">
        Versión 3.0.0 · &copy; {{ date('Y') }} Todos los derechos reservados.
      </p>

      <div class="flex items-center gap-3">
        {{-- Ayuda (abre modal) --}}
        <a href="#" id="btnAyuda"
           @click.prevent="helpOpen = true"
           class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-sm font-semibold
                  bg-white text-purple-900 border border-purple-200
                  hover:bg-purple-50 hover:border-purple-300
                  dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700 dark:hover:bg-gray-700">
          <span aria-hidden="true">🛈</span>
          <span>Ayuda</span>
        </a>
      </div>
    </div>
  </footer>

  {{-- Mantengo tus componentes --}}
  {{-- @include('components.portal.chat-box') --}}

  {{-- ===== Modal: Centro de ayuda ===== --}}
  <div
    x-show="helpOpen"
    x-transition.opacity
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center"
    @keydown.escape.window="helpOpen=false"
  >
    <div class="absolute inset-0 bg-black/50" @click="helpOpen=false"></div>
    <div
      class="relative bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
             rounded-2xl shadow-xl p-8 w-full max-w-sm text-center"
    >
      <h2 class="text-xl font-bold mb-6">Centro de ayuda</h2>

      <ul class="space-y-3 text-sm">
        <li>
          <a href="#"
             @click.prevent="openManual = true; helpOpen = false"
             class="block rounded-lg border border-purple-200 dark:border-gray-700 p-3 hover:bg-purple-50 dark:hover:bg-gray-800 font-medium">
            📘 Manual de ayuda
          </a>
        </li>
        <li>
          <a href="#"
             @click.prevent="openVideo = true; helpOpen = false"
             class="block rounded-lg border border-purple-200 dark:border-gray-700 p-3 hover:bg-purple-50 dark:hover:bg-gray-800 font-medium">
            🎥 Video de ayuda
          </a>
        </li>
        <li>
          <a href="{{ route('soporte.create') }}"
             class="block rounded-lg border border-purple-200 dark:border-gray-700 p-3 hover:bg-purple-50 dark:hover:bg-gray-800 font-medium">
            📨 Enviar ticket
          </a>
        </li>
        <li>
          <a href="#"
             @click.prevent="openFaq = true; helpOpen = false"
             class="block rounded-lg border border-purple-200 dark:border-gray-700 p-3 hover:bg-purple-50 dark:hover:bg-gray-800 font-medium">
            ❓ Preguntas frecuentes
          </a>
        </li>
      </ul>
    </div>
  </div>

  <!-- ===== Modal MANUAL ===== -->
  <div x-show="openManual" x-cloak
       class="fixed inset-0 z-[60] bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
       @keydown.escape.window="openManual=false"
       x-transition.opacity>
    <div class="w-full max-w-5xl rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-xl overflow-hidden">
      <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Manual del usuario</h3>
        <button class="rounded-lg px-2 py-1 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                @click="openManual=false">Cerrar</button>
      </div>
      <div class="p-0">
        <iframe src="{{ asset('manual/manual-usuario.pdf') }}"
                class="w-full h-[70vh]" title="Manual del usuario"></iframe>
      </div>
    </div>
  </div>

  <!-- ===== Modal VIDEO ===== -->
  <div x-show="openVideo" x-cloak
       class="fixed inset-0 z-[60] bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
       @keydown.escape.window="openVideo=false"
       x-transition.opacity>
    <div class="w-full max-w-4xl rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-xl overflow-hidden">
      <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Video tutorial</h3>
        <button class="rounded-lg px-2 py-1 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                @click="openVideo=false">Cerrar</button>
      </div>
      <div class="p-0">
        <video controls class="w-full h-[60vh] object-contain bg-black">
          <source src="{{ asset('videos/tutorial.mp4') }}" type="video/mp4">
          Tu navegador no soporta video HTML5.
        </video>
      </div>
    </div>
  </div>

  <!-- ===== Modal FAQ ===== -->
  <div
    x-show="openFaq"
    x-cloak
    class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-[60]"
    @keydown.escape.window="openFaq=false"
    x-transition.opacity
  >
    <div class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-900 shadow-2xl overflow-hidden border border-gray-200 dark:border-gray-700">
      {{-- Header --}}
      <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-semibold text-purple-900 dark:text-gray-100">Preguntas Frecuentes</h3>
        <button @click="openFaq=false" class="text-gray-600 dark:text-gray-300 hover:text-purple-700 dark:hover:text-gray-100">✕</button>
      </div>

      {{-- Contenido dinámico --}}
      <div
        x-data="{
          q: '', items: [],
          async load() {
            try {
              const res = await fetch('{{ route('portal.faqs.list') }}?q=' + encodeURIComponent(this.q));
              const json = await res.json();
              this.items = json.ok ? (json.items || []) : [];
            } catch (_) { this.items = []; }
          }
        }"
        x-init="load()"
        class="p-4 space-y-3 h-[70vh] overflow-y-auto">

        {{-- Campo de búsqueda --}}
        <input type="search"
              x-model="q"
              @input.debounce.300ms="load()"
              placeholder="Buscar preguntas..."
              class="w-full rounded-lg border-gray-300 dark:bg-gray-800 dark:border-gray-700 text-sm px-3 py-2">

        {{-- Listado --}}
<template x-for="it in items" :key="it.id">
  <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4">
    <h4 class="text-sm font-semibold text-purple-900 dark:text-gray-100" x-text="it.question"></h4>
    <!-- Usa x-html si tu answer trae HTML; si es texto plano, cambia a x-text -->
    <div class="mt-2 text-sm text-gray-700 dark:text-gray-300" x-html="it.answer"></div>
  </div>
</template>

        {{-- Sin resultados --}}
        <div x-show="items.length===0" class="text-sm text-gray-500 text-center">Sin resultados</div>
      </div>
    </div>
  </div>

</div> {{-- <-- AQUÍ recién cerramos el contenedor con x-data --}}
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <x-portal.assistant-bot />

{{-- ==== Núcleo del Tema ==== --}}
<script>
  function getCurrentDark() {
    if ('theme' in localStorage) return localStorage.theme === 'dark';
    return window.matchMedia('(prefers-color-scheme: dark)').matches;
  }
  function applyDarkMode(isDark) {
    document.documentElement.classList.toggle('dark', isDark);
    localStorage.theme = isDark ? 'dark' : 'light';
  }
  function toggleDark() { applyDarkMode(!getCurrentDark()); }
  function applyColorTheme(name) {
    const enforced = 'purple';
    document.body.setAttribute('data-color', enforced);
    localStorage.setItem('colorTheme', enforced);
  }
  function changeTheme(name) {
    if (!getCurrentDark()) applyColorTheme('purple');
  }
  document.addEventListener('DOMContentLoaded', () => {
    const dark = getCurrentDark();
    applyDarkMode(dark);
    if (!dark) applyColorTheme('purple');
    document.getElementById('floatingDarkToggle')?.addEventListener('click', toggleDark);
  });
  window.changeTheme = changeTheme;
</script>
{{-- Script que activa el skeleton al navegar --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
  const overlay = document.getElementById('pageSkeletonOverlay');
  if (!overlay) return;

  const show = () => {
    overlay.classList.remove('hidden');
    overlay.classList.add('flex');
  };

  // ✅ Mostrar skeleton al hacer clic en cualquier enlace interno
  document.addEventListener('click', (e) => {
    const link = e.target.closest('a');
    if (!link) return;

    const url = link.getAttribute('href') || '';
    if (!url || url.startsWith('#') || link.target === '_blank' || link.hasAttribute('download')) return;

    // Solo para enlaces dentro del mismo sitio (no externos)
    const dest = new URL(url, location.href);
    if (dest.origin !== location.origin) return;

    show();
  });

  // ✅ Si vuelve con el botón “atrás” del navegador, ocultar skeleton
  window.addEventListener('pageshow', () => {
    overlay.classList.add('hidden');
    overlay.classList.remove('flex');
  });
});
</script>

<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
