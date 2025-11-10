@props([
  'title' => 'Admin Panel',
  'sidebarWidth' => 'w-64',
])

{{-- Fondo general (degradado purple) --}}
<div class="min-h-screen w-full bg-gradient-to-br from-purple-900 via-purple-700 to-purple-200 text-gray-100 antialiased">

  <div class="min-h-screen bg-white/5 dark:bg-black/20 backdrop-blur-sm">

    {{-- TOPBAR --}}
    <header class="sticky top-0 z-40">
      {{ $topbar ?? '' }}
    </header>

{{-- ===== LAYOUT RESPONSIVE ===== --}}
<div class="flex">

  {{-- ✅ MÓVIL: sidebar visible arriba del contenido --}}
  <div class="lg:hidden p-4">
    {{ $sidebar ?? '' }}
  </div>

  {{-- DESKTOP: sidebar fijo a la izquierda --}}
  <aside class="hidden lg:block {{ $sidebarWidth }} shrink-0 p-4">
    {{ $sidebar ?? '' }}
  </aside>

{{-- CONTENIDO --}}
<main class="flex-1 p-4 lg:p-6 hidden sm:block">
  @isset($title)
    <h1 class="sr-only">{{ $title }}</h1>
  @endisset

  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    {{ $slot }}
  </div>
</main>
</div>

    {{-- FOOTER opcional --}}
    @isset($footer)
      <footer class="p-4 text-xs text-gray-300">
        {{ $footer }}
      </footer>
    @endisset
  </div>
</div>

{{-- ===== Drawer móvil (hamburguesa) ===== --}}
<div x-cloak class="lg:hidden">
  {{-- Overlay --}}
  <div
    class="fixed inset-0 z-[60] bg-black/40 backdrop-blur-[1px]"
    x-show="$store.nav?.open"
    x-transition.opacity
    @click="$store.nav.open=false"
    aria-hidden="true"
  ></div>

  {{-- Panel --}}
  <aside
    class="fixed inset-y-0 left-0 z-[70] w-72 sm:w-80 -translate-x-full bg-white dark:bg-gray-900 shadow-xl will-change-transform"
    :class="{ 'translate-x-0' : $store.nav?.open }"
    x-transition:enter="transform transition ease-in-out duration-200"
    x-transition:enter-start="-translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transform transition ease-in-out duration-200"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full"
    x-trap="$store.nav?.open"
    role="dialog" aria-modal="true" aria-label="Menú"
  >
    <div class="h-full overflow-y-auto p-3">
      {{-- 🔁 MISMO COMPONENTE SIDEBAR --}}
      {{ $sidebar ?? '' }}
    </div>
  </aside>
</div>

@push('scripts')
<script>
  // ✅ Garantiza que el store exista (aunque Alpine se cargue antes/después)
  document.addEventListener('alpine:init', () => {
    if (!Alpine.store('nav')) Alpine.store('nav', { open: false });
  });

  // ✅ Cierra el drawer al navegar por enlaces (solo en móvil)
  document.addEventListener('click', (e) => {
    const a = e.target.closest('a[href]');
    if (!a) return;
    if (window.innerWidth >= 1024) return;
    requestAnimationFrame(() => {
      try { Alpine.store('nav').open = false } catch(_) {}
    });
  });
</script>
@endpush
