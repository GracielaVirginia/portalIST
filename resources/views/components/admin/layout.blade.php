@props([
  'title' => 'Admin Panel',
  // ancho fijo del sidebar (coherente con tu UI anterior)
  'sidebarWidth' => 'w-64',
])

{{-- Fondo general (degradado purple) --}}
<div class="min-h-screen w-full bg-gradient-to-br from-purple-900 via-purple-700 to-purple-200
            text-gray-100 antialiased">

  {{-- Capa para permitir contraste (contenedor) --}}
  <div class="min-h-screen bg-white/5 dark:bg-black/20 backdrop-blur-sm">

    {{-- TOPBAR (slot opcional) --}}
    <header class="sticky top-0 z-30">
      {{ $topbar ?? '' }}
    </header>

    {{-- LAYOUT con sidebar fijo + área de contenido --}}
    <div class="flex">

      {{-- SIDEBAR (slot obligatorio) --}}
      <aside class="hidden lg:block {{ $sidebarWidth }} shrink-0 p-4">
        {{ $sidebar ?? '' }}
      </aside>

      {{-- En móviles, el sidebar puede renderizarse como offcanvas desde $mobileSidebar si lo deseas --}}
      {{ $mobileSidebar ?? '' }}

      {{-- CONTENIDO --}}
      <main class="flex-1 p-4 lg:p-6">
        {{-- título de página (opcional) --}}
        @isset($title)
          <h1 class="sr-only">{{ $title }}</h1>
        @endisset

        {{-- Tarjetas en contenedor responsive (grid base) --}}
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

{{-- Pila para scripts por página/componente --}}
@stack('scripts')
