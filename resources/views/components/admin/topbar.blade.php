@props([
    'userName' => null,
    'searchUrl' => route('admin.users.search'),
    'placeholder' => 'Buscar paciente por RUT o nombre…',
    // nuevos
    'alertStats' => ['login' => 0, 'validacion' => 0, 'bloqueados' => 0],
    'auditUrl' => route('admin.auth_attempts.index'),
])

@php
    $totalAlerts =
        (int) ($alertStats['login'] ?? 0) +
        (int) ($alertStats['validacion'] ?? 0) +
        (int) ($alertStats['bloqueados'] ?? 0);
@endphp

<header class="backdrop-blur-md bg-white/10 dark:bg-black/20 border-b border-white/10">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center gap-3">

        {{-- Toggle sidebar en móvil --}}
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
        <div id="admin-topbar-search" class="relative flex-1 max-w-xl" data-url="{{ $searchUrl }}"
            data-csrf="{{ csrf_token() }}">
            <input type="text" id="topbarSearchInput" placeholder="{{ $placeholder }}" autocomplete="off"
                class="w-full rounded-xl pl-10 pr-3 py-2 text-sm
                    bg-white/20 placeholder-purple-200/70 text-white
                    border border-white/10 focus:outline-none focus:ring-2 focus:ring-purple-400 p-4" />
            {{-- <span class="absolute left-3 top-1/2 -translate-y-1/2 text-purple-100 mr-4">🔎</span> --}}

            <div id="topbarSearchDropdown"
                class="absolute mt-1 w-full bg-white dark:bg-gray-800 rounded-xl shadow-lg z-50 hidden">
                <ul id="topbarSearchResults"
                    class="max-h-72 overflow-auto divide-y divide-gray-100 dark:divide-gray-700"></ul>
            </div>
        </div>
        {{-- ================== /BUSCADOR ================== --}}

        {{-- ===== Campana de alertas (entre buscador y avatar) ===== --}}
        <div class="relative group">
            <button type="button" aria-label="Alertas"
                class="relative ml-2 inline-flex items-center justify-center rounded-full p-2
                     bg-white/10 hover:bg-white/20 text-white transition">
                🔔
                @if ($totalAlerts > 0)
                    <span
                        class="absolute -bottom-1 -right-1 min-w-[18px] h-[18px] px-1
                       text-[10px] leading-[18px] text-white bg-red-600
                       rounded-full text-center font-bold shadow">
                        {{ $totalAlerts }}
                    </span>
                @endif
            </button>

            {{-- HOVER BRIDGE: zona invisible que mantiene el :hover al cruzar --}}
            <span class="absolute left-0 right-0 top-full h-3"></span>

            {{-- Panel: pegado a la campana, anclado al mismo contenedor --}}
            <div
                class="absolute right-0 top-[calc(100%+0.25rem)] w-64 rounded-xl
              bg-white/95 dark:bg-gray-900/95 shadow-lg ring-1 ring-black/5
              overflow-hidden z-50
              opacity-0 translate-y-1 pointer-events-none
              group-hover:opacity-100 group-hover:translate-y-0 group-hover:pointer-events-auto
              transition duration-150 ease-out">
                <div class="px-3 py-2 text-xs font-semibold text-gray-600 dark:text-gray-300">
                    Resumen de alertas
                </div>


                <ul class="text-sm divide-y divide-gray-100 dark:divide-gray-800">
                    <li class="flex items-center justify-between px-3 py-2">
                        <span class="flex items-center gap-2">
                            <span>🚫</span>
                            <span class="text-purple-700 dark:text-purple-400">Usuarios bloqueados</span> </span>
                        <span class="font-semibold text-gray-900 dark:text-gray-100">
                            {{ (int) ($alertStats['bloqueados'] ?? 0) }}
                        </span>
                    </li>
                    <li class="flex items-center justify-between px-3 py-2">
                        <span class="flex items-center gap-2">
                            <span>🔑</span>
                            <span class="text-purple-700 dark:text-purple-400">Fallaron login</span>
                        </span>
                        <span class="font-semibold text-gray-900 dark:text-gray-100">
                            {{ (int) ($alertStats['login'] ?? 0) }}
                        </span>
                    </li>
                    <li class="flex items-center justify-between px-3 py-2">
                        <span class="flex items-center gap-2">
                            <span>🧩</span>
                            <span class="text-purple-700 dark:text-purple-400">Fallaron validación</span>
                        </span>
                        <span class="font-semibold text-gray-900 dark:text-gray-100">
                            {{ (int) ($alertStats['validacion'] ?? 0) }}
                        </span>
                    </li>
                </ul>

                <div class="border-t border-gray-100 dark:border-gray-800 px-3 py-2 text-right">
                    <a href="{{ $auditUrl }}"
                        class="inline-flex items-center gap-1 text-xs text-fuchsia-700 dark:text-fuchsia-300 hover:underline">
                        Ver más en Historial de accesos →
                    </a>
                </div>
            </div>
        </div>
        {{-- ===== /Campana de alertas ===== --}}

        {{-- Avatar + menú (se mantiene igual) --}}
        <div class="relative">
            <button type="button" id="admin-user-menu-btn"
                class="ml-2 inline-flex items-center gap-2 rounded-full px-2 py-1
                     bg-white/10 hover:bg-white/20 text-white transition">
                <span class="h-8 w-8 grid place-items-center rounded-full bg-purple-700">👤</span>
                @if ($userName)
                    <span class="md:inline text-sm">{{ $userName ?: 'Administrador' }}</span>
                @else
                    <span class="md:inline text-sm">Administrador</span>
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
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                        🔐 Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
