@props([
  'userName'     => null,
  'searchUrl'    => route('admin.users.search'), // endpoint AJAX (gestiones)
  'placeholder'  => 'Buscar paciente por RUT o nombre…',
])

<header class="backdrop-blur-md bg-white/10 dark:bg-black/20 border-b border-white/10">
  <div class="max-w-7xl mx-auto px-4 py-3 flex items-center gap-3">

    {{-- Toggle sidebar en móvil (el offcanvas lo manejas aparte) --}}
    <button type="button" id="admin-mobile-menu"
            class="lg:hidden inline-flex items-center justify-center rounded-lg p-2
                   text-purple-100 hover:bg-white/10 focus:outline-none"
            aria-label="Abrir menú">
      ☰
    </button>

    {{-- Branding --}}
    <div class="hidden sm:block text-sm font-semibold text-purple-100">
      Admin Panel
    </div>

    {{-- ================== BUSCADOR (AJAX) ================== --}}
    <div id="admin-topbar-search"
         class="relative flex-1 max-w-xl"
         data-url="{{ $searchUrl }}"
         data-csrf="{{ csrf_token() }}">
      <input type="text"
             id="topbarSearchInput"
             placeholder="{{ $placeholder }}"
             autocomplete="off"
             class="w-full rounded-xl pl-10 pr-3 py-2 text-sm
                    bg-white/20 placeholder-purple-200/70 text-white
                    border border-white/10 focus:outline-none focus:ring-2 focus:ring-purple-400" />
      <span class="absolute left-3 top-1/2 -translate-y-1/2 text-purple-100">🔎</span>

      {{-- Dropdown de resultados (AJAX llena este UL) --}}
      <div id="topbarSearchDropdown"
           class="absolute mt-1 w-full bg-white dark:bg-gray-800 rounded-xl shadow-lg z-50 hidden">
        <ul id="topbarSearchResults" class="max-h-72 overflow-auto divide-y divide-gray-100 dark:divide-gray-700"></ul>
      </div>
    </div>
    {{-- ================== /BUSCADOR ================== --}}



    {{-- Avatar + menú --}}
    <div class="relative">
      <button type="button" id="admin-user-menu-btn"
              class="ml-2 inline-flex items-center gap-2 rounded-full px-2 py-1
                     bg-white/10 hover:bg-white/20 text-white transition">
        <span class="h-8 w-8 grid place-items-center rounded-full bg-purple-700">👤</span>
        @if($userName)
          <span class="hidden md:inline text-sm">{{ $userName }}</span>
        @endif
      </button>

      <div id="admin-user-menu"
           class="hidden absolute right-0 mt-2 w-44 rounded-xl bg-white/95 dark:bg-gray-900/95
                  shadow-lg ring-1 ring-black/5 overflow-hidden z-50">
        <a href="{{ route('admin.dashboard') }}"
           class="block px-4 py-2 text-sm text-gray-800 dark:text-gray-100 hover:bg-purple-50 dark:hover:bg-gray-800">
          📊 Dashboard
        </a>
        <form method="POST" action="{{ route('admin.logout') }}">
          @csrf
          <button type="submit"
                  class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
            🔐 Cerrar sesión
          </button>
        </form>
      </div>
    </div>
  </div>
</header>
