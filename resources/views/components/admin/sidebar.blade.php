@props([
    // Totales para badges (llegan del controlador)
    'stats' => [
        'dashboard' => 0, // si quieres mostrar algo aquí (opcional)
        'usuarios' => 0,
        'noticias' => 0,
        'administradores' => 0,
        'validaciones' => 0,
    ],
])

@php
    $dash = (int) ($stats['dashboard'] ?? 0);
    $users = (int) ($stats['usuarios'] ?? 0);
    $news = (int) ($stats['noticias'] ?? 0);
    $admins = (int) ($stats['administradores'] ?? 0);
    $valids = (int) ($stats['validaciones'] ?? 0);

    $is = fn($name) => request()->routeIs($name);

    $linkBase =
        'group cursor-pointer flex items-center justify-between rounded-lg px-3 py-2 text-xs sm:text-sm font-medium transition';
    $linkText = 'text-purple-900 dark:text-purple-100';
    $linkHover = 'hover:bg-purple-900 dark:hover:bg-purple-800 hover:text-white';
    $dotBase = 'h-2 w-2 rounded-full bg-purple-700 group-hover:bg-white';
    $badgeBase =
        'rounded-lg bg-purple-900 text-white text-[11px] px-2 py-0.5 group-hover:ring-1 group-hover:ring-white/40';
@endphp

<aside class="w-full lg:w-64 shrink-0">
    <nav class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-3 shadow-sm">

        {{-- Inicio (similar a tu portal) --}}
        <a href="{{ route('admin.dashboard') }}"
            class="flex items-center justify-between rounded-xl px-3 py-2 text-sm font-medium
              text-gray-800 dark:text-gray-100 bg-gray-50 dark:hover:bg-gray-800">
            <span class="inline-flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-purple-900"></span>
                Panel de administración
            </span>
            @if ($dash > 0)
                <span class="rounded-lg bg-purple-900 text-white text-[11px] px-2 py-0.5">{{ $dash }}</span>
            @endif
        </a>

        {{-- ================= BLOQUE MORADO ================= --}}
        <div class="mt-2">
            {{-- Encabezado fijo del bloque --}}
            <div
                class="flex items-center justify-between rounded-xl px-3 py-2 text-sm font-semibold
                  text-gray-900 dark:text-gray-100">
                <span class="inline-flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-purple-900" viewBox="0 0 24 24"
                        fill="currentColor">
                        <path d="M4 4h16v16H4zM7 7h10v2H7V7zm0 4h10v2H7v-2zm0 4h7v2H7v-2z" />
                    </svg>
                    Gestión
                </span>
                @php $totalBlock = $users + $news + $admins + $valids; @endphp
                <span class="rounded-lg bg-purple-900 text-white text-xs px-2 py-0.5">{{ $totalBlock }}</span>
            </div>

            {{-- Contenedor lila --}}
            <div class="ml-3 mt-1 rounded-xl bg-purple-100 dark:bg-purple-900/30 p-1.5 space-y-1">

                {{-- Usuarios (con submenú) --}}
                <div class="relative group/usuarios">
                    <a href="" class="{{ $linkBase }} {{ $linkText }} {{ $linkHover }}">
                        <span class="inline-flex items-center gap-2">
                            <span class="{{ $dotBase }}"></span>
                            <span class="pl-1">👤 Usuarios</span>
                        </span>
                        @if ($users > 0)
                            <span class="{{ $badgeBase }}">{{ $users }}</span>
                        @endif
                    </a>

                    {{-- Popout derecha (sin gap + puente de hover) --}}
                    <div
                        class="absolute top-0 left-full z-10 hidden w-64 rounded-xl border border-gray-200 bg-white p-2 shadow-xl
                   dark:border-gray-700 dark:bg-gray-900
                   lg:group-hover/usuarios:block group-focus-within/usuarios:block
                   opacity-0 scale-95 transition
                   lg:group-hover/usuarios:opacity-100 lg:group-hover/usuarios:scale-100
                   group-focus-within/usuarios:opacity-100 group-focus-within/usuarios:scale-100
                   pointer-events-none lg:group-hover/usuarios:pointer-events-auto group-focus-within/usuarios:pointer-events-auto
                   before:content-[''] before:absolute before:top-0 before:-left-2 before:w-2 before:h-full before:bg-transparent">
                        <div class="pl-2 space-y-1">
                            <a href="{{ route('admin.users.registered') }}"
                                class="{{ $linkBase }} {{ $linkText }} hover:bg-purple-900 dark:hover:bg-purple-800 hover:text-white">
                                <span class="inline-flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-purple-700 group-hover:bg-white"></span>
                                    Registrados en el portal
                                </span>
                            </a>
                            <a href="{{ route('admin.users.unregistered') }}"
                                class="{{ $linkBase }} {{ $linkText }} hover:bg-purple-900 dark:hover:bg-purple-800 hover:text-white">
                                <span class="inline-flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-purple-700 group-hover:bg-white"></span>
                                    No registrados en el portal
                                </span>
                            </a>
                            <a href="{{ route('admin.login_logs.index') }}"
                                class="{{ $linkBase }} {{ $linkText }} hover:bg-purple-900 dark:hover:bg-purple-800 hover:text-white">
                                <span class="inline-flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-purple-700 group-hover:bg-white"></span>
                                    Sesiones usuarios registrados
                                </span>
                            </a>

                        </div>
                    </div>
                </div>

                {{-- Noticias --}}
                <a href="{{ route('admin.auditoria-logins') }}"
                    class="{{ $linkBase }} {{ $linkText }} {{ $linkHover }}">
                    <span class="inline-flex items-center gap-2">
                        <span class="{{ $dotBase }}"></span>
                        <span class="pl-1">📰 Noticias</span>
                    </span>
                    @if ($news > 0)
                        <span class="{{ $badgeBase }}">{{ $news }}</span>
                    @endif
                </a>

                {{-- Administradores (con submenú) --}}
                <div class="relative group/admins">
                    <a href="" class="{{ $linkBase }} {{ $linkText }} {{ $linkHover }}">
                        <span class="inline-flex items-center gap-2">
                            <span class="{{ $dotBase }}"></span>
                            <span class="pl-1">🧑‍💼 Administradores</span>
                        </span>
                        @if ($admins > 0)
                            <span class="{{ $badgeBase }}">{{ $admins }}</span>
                        @endif
                    </a>
                    
                    {{-- Popout derecha (sin gap + puente de hover) --}}
                    <div
                        class="absolute top-0 left-full z-10 hidden w-72 rounded-xl border border-gray-200 bg-white p-2 shadow-xl
                   dark:border-gray-700 dark:bg-gray-900
                   lg:group-hover/admins:block group-focus-within/admins:block
                   opacity-0 scale-95 transition
                   lg:group-hover/admins:opacity-100 lg:group-hover/admins:scale-100
                   group-focus-within/admins:opacity-100 group-focus-within/admins:scale-100
                   pointer-events-none lg:group-hover/admins:pointer-events-auto group-focus-within/admins:pointer-events-auto
                   before:content-[''] before:absolute before:top-0 before:-left-2 before:w-2 before:h-full before:bg-transparent">
                        <div class="pl-2 space-y-1">
                            <a href="{{ route('admin.administradores.index') }}"
                                class="{{ $linkBase }} {{ $linkText }} hover:bg-purple-900 dark:hover:bg-purple-800 hover:text-white">
                                <span class="inline-flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-purple-700 group-hover:bg-white"></span>
                                    Crear administrador
                                </span>
                            </a>
                            <a href="{{ route('admin.faqs.index') }}"
                                class="{{ $linkBase }} {{ $linkText }} hover:bg-purple-900 dark:hover:bg-purple-800 hover:text-white">
                                <span class="inline-flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-purple-700 group-hover:bg-white"></span>
                                    Configurar preguntas frecuentes
                                </span>
                            </a>
                            <a href="{{ route('admin.tickets.index') }}"
                                class="{{ $linkBase }} {{ $linkText }} hover:bg-purple-900 dark:hover:bg-purple-800 hover:text-white">
                                <span class="inline-flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-purple-700 group-hover:bg-white"></span>
                                    Tickets-Soporte
                                </span>
                            </a>
                            <a href="{{ route('admin.assistant_rules.index') }}"
                                class="{{ $linkBase }} {{ $linkText }} hover:bg-purple-900 dark:hover:bg-purple-800 hover:text-white">
                                <span class="inline-flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-purple-700 group-hover:bg-white"></span>
                                    Configurar Asistente Virtual
                                </span>
                            </a>
                            <a href="{{ route('admin.reviews.index') }}"
                                class="{{ $linkBase }} {{ $linkText }} hover:bg-purple-900 dark:hover:bg-purple-800 hover:text-white">
                                <span class="inline-flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-purple-700 group-hover:bg-white"></span>
                                    Ver Reviews
                                </span>
                            </a>
                            <a href="{{ route('admin.config.home') }}"
                                class="{{ $linkBase }} {{ $linkText }} hover:bg-purple-900 dark:hover:bg-purple-800 hover:text-white">
                                <span class="inline-flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-purple-700 group-hover:bg-white"></span>
                                    Configuraciones en el Login (imagen y seccion(tres cards o banner))
                                </span>
                            </a>
                            <a href="{{ route('admin.promociones.index') }}"
                                class="{{ $linkBase }} {{ $linkText }} hover:bg-purple-900 dark:hover:bg-purple-800 hover:text-white">
                                <span class="inline-flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-purple-700 group-hover:bg-white"></span>
                                    Promociones en el banner
                                </span>
                            </a>
                            {{-- <a href=""
                 class="{{ $linkBase }} {{ $linkText }} hover:bg-purple-900 dark:hover:bg-purple-800 hover:text-white">
                <span class="inline-flex items-center gap-2">
                  <span class="h-2 w-2 rounded-full bg-purple-700 group-hover:bg-white"></span>
                  Especialidades
                </span>
              </a>
              <a href=""
                 class="{{ $linkBase }} {{ $linkText }} hover:bg-purple-900 dark:hover:bg-purple-800 hover:text-white">
                <span class="inline-flex items-center gap-2">
                  <span class="h-2 w-2 rounded-full bg-purple-700 group-hover:bg-white"></span>
                  Tipos de exámenes
                </span>
              </a>
              <a href=""
                 class="{{ $linkBase }} {{ $linkText }} hover:bg-purple-900 dark:hover:bg-purple-800 hover:text-white">
                <span class="inline-flex items-center gap-2">
                  <span class="h-2 w-2 rounded-full bg-purple-700 group-hover:bg-white"></span>
                  Lugar de la cita
                </span>
              </a> --}}
                        </div>
                    </div>
                </div>

                {{-- Validaciones --}}
                <a href="{{ route('admin.validacion.modos') }}"
                    class="{{ $linkBase }} {{ $linkText }} {{ $linkHover }}">
                    <span class="inline-flex items-center gap-2">
                        <span class="{{ $dotBase }}"></span>
                        <span class="pl-1">✅ Validaciones</span>
                    </span>
                    @if ($valids > 0)
                        <span class="{{ $badgeBase }}">{{ $valids }}</span>
                    @endif
                </a>
{{-- <a href="{{ route('other-settings.edit') }}"
   class="{{ $linkBase }} {{ $linkText }} {{ $linkHover }}">
  <span class="inline-flex items-center gap-2">
    <span class="{{ $dotBase }}"></span>
    <span class="pl-1">⚙️ Configuración del Portal</span>
  </span>
</a> --}}
            </div>
        </div>
        {{-- ================= /BLOQUE MORADO ================= --}}
        {{-- Citas (dropdown independiente) --}}
        <div class="relative group/citas mt-2">
            <a href="#" class="{{ $linkBase }} {{ $linkText }} {{ $linkHover }}">
                <span class="inline-flex items-center gap-2">
                    <span class="{{ $dotBase }}"></span>
                    <span class="pl-1">📅 Citas</span>
                </span>
            </a>

            {{-- Dropdown a la derecha --}}
            <div
                class="absolute top-0 left-full z-10 hidden w-64 rounded-xl border border-gray-200 bg-white p-2 shadow-xl
           dark:border-gray-700 dark:bg-gray-900
           lg:group-hover/citas:block group-focus-within/citas:block
           opacity-0 scale-95 transition
           lg:group-hover/citas:opacity-100 lg:group-hover/citas:scale-100
           group-focus-within/citas:opacity-100 group-focus-within/citas:scale-100
           pointer-events-none lg:group-hover/citas:pointer-events-auto group-focus-within/citas:pointer-events-auto
           before:content-[''] before:absolute before:top-0 before:-left-2 before:w-2 before:h-full before:bg-transparent">
                <div class="pl-2 space-y-1">
                    <a href="{{ route('admin.citas.index') }}"
                        class="{{ $linkBase }} {{ $linkText }} hover:bg-purple-900 dark:hover:bg-purple-800 hover:text-white">
                        <span class="inline-flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-purple-700 group-hover:bg-white"></span>
                            Citas Reservadas
                        </span>
                    </a>
                    {{-- ✅ EXISTE --}}
                    <a href="{{ route('sucursales.index') }}"
                        class="{{ $linkBase }} {{ $linkText }} hover:bg-purple-900 dark:hover:bg-purple-800 hover:text-white">
                        <span class="inline-flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-purple-700 group-hover:bg-white"></span>
                            Sucursales
                        </span>
                    </a>

                    {{-- ✅ EXISTE (renombrado en el menú) --}}
                    <a href="{{ route('tipos.index') }}"
                        class="{{ $linkBase }} {{ $linkText }} hover:bg-purple-900 dark:hover:bg-purple-800 hover:text-white">
                        <span class="inline-flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-purple-700 group-hover:bg-white"></span>
                            Tipos de profesionales
                        </span>
                    </a>
                    <a href="{{ route('profesionales.index') }}"
                        class="{{ $linkBase }} {{ $linkText }} hover:bg-purple-900 dark:hover:bg-purple-800 hover:text-white">
                        <span class="inline-flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-purple-700 group-hover:bg-white"></span>
                            Profesionales
                        </span>
                    </a>
                    <a href="{{ route('horarios.index') }}"
                        class="{{ $linkBase }} {{ $linkText }} hover:bg-purple-900 dark:hover:bg-purple-800 hover:text-white">
                        <span class="inline-flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-purple-700 group-hover:bg-white"></span>
                            Horarios Profesionales
                        </span>
                    </a>
                    {{-- ❌ AÚN NO EXISTE. Descomenta cuando definas las rutas profesionales.* 
      <a href="{{ route('profesionales.index') }}"
         class="{{ $linkBase }} {{ $linkText }} hover:bg-purple-900 dark:hover:bg-purple-800 hover:text-white">
        <span class="inline-flex items-center gap-2">
          <span class="h-2 w-2 rounded-full bg-purple-700 group-hover:bg-white"></span>
          Profesionales
        </span>
      </a>
      --}}

                    {{-- ❌ AÚN NO EXISTE. Ajusta si defines bloqueos.index
      <a href="{{ route('bloqueos.index') }}"
         class="{{ $linkBase }} {{ $linkText }} hover:bg-purple-900 dark:hover:bg-purple-800 hover:text-white">
        <span class="inline-flex items-center gap-2">
          <span class="h-2 w-2 rounded-full bg-purple-700 group-hover:bg-white"></span>
          Bloqueos
        </span>
      </a>
      --}}
                </div>
            </div>
        </div>

        {{-- Cerrar sesión --}}
        <form method="POST" action="{{ route('admin.logout') }}" class="mt-3">
            @csrf
            <button type="submit"
                class="flex items-center justify-between rounded-xl px-3 py-2 text-sm font-medium
                     text-gray-800 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800 w-full text-left">
                <span class="inline-flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-purple-900"></span>
                    🔐 Cerrar sesión
                </span>
            </button>
        </form>

    </nav>
</aside>
