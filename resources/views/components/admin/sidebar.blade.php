@props([
  // Totales para badges (llegan del controlador)
  'stats' => [
    'dashboard'      => 0,  // si quieres mostrar algo aquí (opcional)
    'usuarios'       => 0,
    'noticias'       => 0,
    'administradores'=> 0,
    'validaciones'   => 0,
  ],
])

@php
  $dash   = (int) ($stats['dashboard'] ?? 0);
  $users  = (int) ($stats['usuarios'] ?? 0);
  $news   = (int) ($stats['noticias'] ?? 0);
  $admins = (int) ($stats['administradores'] ?? 0);
  $valids = (int) ($stats['validaciones'] ?? 0);

  $is = fn ($name) => request()->routeIs($name);

  $linkBase  = "group cursor-pointer flex items-center justify-between rounded-lg px-3 py-2 text-xs sm:text-sm font-medium transition";
  $linkText  = "text-purple-900 dark:text-purple-100";
  $linkHover = "hover:bg-purple-900 dark:hover:bg-purple-800 hover:text-white";
  $dotBase   = "h-2 w-2 rounded-full bg-purple-700 group-hover:bg-white";
  $badgeBase = "rounded-lg bg-purple-900 text-white text-[11px] px-2 py-0.5 group-hover:ring-1 group-hover:ring-white/40";
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
      @if($dash > 0)
        <span class="rounded-lg bg-purple-900 text-white text-[11px] px-2 py-0.5">{{ $dash }}</span>
      @endif
    </a>

    {{-- ================= BLOQUE MORADO (como “Resultados” del portal) ================= --}}
    <div class="mt-2">
      {{-- Encabezado fijo del bloque --}}
      <div class="flex items-center justify-between rounded-xl px-3 py-2 text-sm font-semibold
                  text-gray-900 dark:text-gray-100">
        <span class="inline-flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-purple-900" viewBox="0 0 24 24" fill="currentColor">
            <path d="M4 4h16v16H4zM7 7h10v2H7V7zm0 4h10v2H7v-2zm0 4h7v2H7v-2z"/>
          </svg>
          Gestión
        </span>
        {{-- total combinado opcional: usuarios + noticias + admins + valids --}}
        @php $totalBlock = $users + $news + $admins + $valids; @endphp
        <span class="rounded-lg bg-purple-900 text-white text-xs px-2 py-0.5">{{ $totalBlock }}</span>
      </div>

      {{-- Contenedor lila como en el portal --}}
      <div class="ml-3 mt-1 rounded-xl bg-purple-100 dark:bg-purple-900/30 p-1.5 space-y-1">

        {{-- Usuarios --}}
        <a href="{{ route('admin.users.index') }}"
           class="{{ $linkBase }} {{ $linkText }} {{ $linkHover }}">
          <span class="inline-flex items-center gap-2">
            <span class="{{ $dotBase }}"></span>
            <span class="pl-1">👤 Usuarios</span>
          </span>
          @if($users > 0)
            <span class="{{ $badgeBase }}">{{ $users }}</span>
          @endif
        </a>

        {{-- Noticias --}}
        <a href="{{ route('admin.news.index') }}"
           class="{{ $linkBase }} {{ $linkText }} {{ $linkHover }}">
          <span class="inline-flex items-center gap-2">
            <span class="{{ $dotBase }}"></span>
            <span class="pl-1">📰 Noticias</span>
          </span>
          @if($news > 0)
            <span class="{{ $badgeBase }}">{{ $news }}</span>
          @endif
        </a>

        {{-- Administradores --}}
        <a href="{{ route('admin.admins.index') }}"
           class="{{ $linkBase }} {{ $linkText }} {{ $linkHover }}">
          <span class="inline-flex items-center gap-2">
            <span class="{{ $dotBase }}"></span>
            <span class="pl-1">🧑‍💼 Administradores</span>
          </span>
          @if($admins > 0)
            <span class="{{ $badgeBase }}">{{ $admins }}</span>
          @endif
        </a>

        {{-- Validaciones --}}
        <a href="{{ route('admin.validations.index') }}"
           class="{{ $linkBase }} {{ $linkText }} {{ $linkHover }}">
          <span class="inline-flex items-center gap-2">
            <span class="{{ $dotBase }}"></span>
            <span class="pl-1">✅ Validaciones</span>
          </span>
          @if($valids > 0)
            <span class="{{ $badgeBase }}">{{ $valids }}</span>
          @endif
        </a>

      </div>
    </div>
    {{-- ================= /BLOQUE MORADO ================= --}}

    {{-- Cerrar sesión (fuera del bloque, como en tu portal) --}}
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
