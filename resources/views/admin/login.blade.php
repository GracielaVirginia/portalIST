@extends('layouts.admin')

@section('admin')
<div class="min-h-screen w-full relative overflow-hidden">
  {{-- Capa base: color de fondo --}}
  <div class="absolute inset-0 bg-purple-100 dark:bg-gray-900"></div>

  {{-- Capa 1: imagen de fondo (una para light, opcional otra para dark) --}}
  <img
    src="{{ asset('images/bg-admin-light.jpg') }}"
    alt="Fondo administrador"
    class="absolute inset-0 w-full h-full object-cover opacity-90 dark:hidden pointer-events-none select-none" />
  <img
    src="{{ asset('images/bg-admin-dark.jpg') }}"
    alt="Fondo administrador oscuro"
    class="absolute inset-0 w-full h-full object-cover opacity-80 hidden dark:block pointer-events-none select-none" />

  {{-- Capa 2: degradado de color para legibilidad --}}
  <div class="absolute inset-0 bg-gradient-to-br from-purple-900/50 via-purple-800/35 to-fuchsia-700/30 mix-blend-multiply"></div>

  {{-- Capa 3: patrón SVG sutil (retícula) --}}
  <svg class="absolute inset-0 w-full h-full opacity-25 dark:opacity-20 mix-blend-overlay pointer-events-none"
       aria-hidden="true">
    <defs>
      <pattern id="gridPattern" width="40" height="40" patternUnits="userSpaceOnUse">
        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-opacity="0.15"/>
      </pattern>
    </defs>
    <rect width="100%" height="100%" fill="url(#gridPattern)" />
  </svg>

  {{-- Capa 4: spotlight animado suave --}}
  <div class="absolute -top-40 -left-40 h-[420px] w-[420px] rounded-full blur-3xl bg-fuchsia-400/25 animate-pulse"></div>
  <div class="absolute -bottom-48 -right-48 h-[520px] w-[520px] rounded-full blur-3xl bg-purple-400/25 animate-[pulse_4s_ease-in-out_infinite]"></div>

  {{-- Contenido centrado --}}
  <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">

      {{-- Banner / Mensaje lateral --}}
      <section class="lg:col-span-7 space-y-6 text-white bg-purple-900 p-4 rounded-xl">
        {{-- Logo + entorno --}}
        <div class="flex items-center gap-3">
          <div class="flex items-center gap-2">
            <span class="text-lg font-bold tracking-tight">Panel Administrador</span>
            {{-- Etiqueta de entorno --}}
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/15 border border-white/20">
              {{ strtoupper(app()->environment()) }}
            </span>
          </div>
        </div>

        <h1 class="text-3xl sm:text-4xl font-extrabold leading-tight">
          Configura el Portal del <span class="text-teal-200">IST</span> con
          <span class="text-teal-200">seguridad</span> y <span class="text-teal-200">precisión</span>.
        </h1>

        {{-- Píldoras de valor --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 max-w-2xl">
          <div class="inline-flex items-center gap-2 rounded-2xl bg-white/10 backdrop-blur px-3 py-2 text-sm">
            <span class="grid place-items-center h-6 w-6 rounded-full bg-white/20">🔒</span>
            <span class="font-medium">Seguridad reforzada</span>
          </div>
          <div class="inline-flex items-center gap-2 rounded-2xl bg-white/10 backdrop-blur px-3 py-2 text-sm">
            <span class="grid place-items-center h-6 w-6 rounded-full bg-white/20">📊</span>
            <span class="font-medium">Auditoría & logs</span>
          </div>
          <div class="inline-flex items-center gap-2 rounded-2xl bg-white/10 backdrop-blur px-3 py-2 text-sm">
            <span class="grid place-items-center h-6 w-6 rounded-full bg-white/20">⚙️</span>
            <span class="font-medium">Gestión ágil</span>
          </div>
        </div>

        {{-- Insignias de confianza --}}
        <div class="flex flex-wrap items-center gap-2 text-xs">
          <span class="px-2 py-1 rounded-lg bg-white/10 border border-white/20">2FA Ready</span>
          <span class="px-2 py-1 rounded-lg bg-white/10 border border-white/20">SSL Activo</span>
          <span class="px-2 py-1 rounded-lg bg-white/10 border border-white/20">Backups diarios</span>
        </div>
      </section>

      {{-- Card de Login (glass) --}}
      <section class="lg:col-span-5">
        <div class="w-full max-w-md ml-auto rounded-3xl border border-white/20 dark:border-white/10 bg-white/40 dark:bg-gray-700/40 backdrop-blur-md shadow-2xl">
          <div class="p-6 sm:p-8 text-gray-900 dark:text-gray-100">
            <h2 class="text-2xl font-bold text-center mb-1 text-purple-900 dark:text-white">
              Acceso Administrador
            </h2>
            <p class="text-xs text-center text-gray-700/90 dark:text-gray-200/90">
              Área restringida. Solo personal autorizado.
            </p>

            <form method="POST" action="{{ route('admin.login.attemp') }}" class="mt-6 space-y-4" novalidate>
              @csrf

              {{-- Usuario / Email --}}
              <div>
                <label for="username" class="block text-sm font-semibold text-purple-900 dark:text-gray-200">
                  Usuario o Email
                </label>
                <input id="username" name="username" type="text" placeholder="admin@ist.cl" autocomplete="username" required
                       class="mt-1 w-full rounded-xl border border-purple-300/70 dark:border-purple-700/70
                              bg-white/80 dark:bg-gray-900 text-gray-900 dark:text-gray-100
                              placeholder:text-gray-400 dark:placeholder:text-gray-400
                              px-4 py-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                @error('username')
                  <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
              </div>

              {{-- Password --}}
              <div>
                <label for="password" class="block text-sm font-semibold text-purple-900 dark:text-gray-200">
                  Contraseña
                </label>
                <div class="mt-1 relative">
                  <input id="password" name="password" type="password" placeholder="********" autocomplete="current-password" required
                         class="w-full rounded-xl border border-purple-300/70 dark:border-purple-700/70
                                bg-white/80 dark:bg-gray-900 text-gray-900 dark:text-gray-100
                                placeholder:text-gray-400 dark:placeholder:text-gray-400
                                px-4 py-3 pr-12 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                  <button type="button" id="togglePassword"
                          class="absolute inset-y-0 right-2 my-auto h-9 w-9 grid place-items-center rounded-lg
                                 text-purple-700 dark:text-purple-300 hover:bg-purple-100/60 dark:hover:bg-purple-900/40"
                          aria-label="Mostrar/Ocultar contraseña"
                          onclick="(function(){const i=document.getElementById('password'); i.type=(i.type==='password')?'text':'password';})();">
                    👁️
                  </button>
                </div>
                @error('password')
                  <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
              </div>

              {{-- CTA --}}
              <div class="pt-2">
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-2xl px-5 py-3 font-semibold
                               bg-purple-900 text-white shadow-sm hover:bg-purple-800">
                  Ingresar
                </button>
              </div>
            </form>

            {{-- Links secundarios --}}
            <div class="mt-4 text-center">
              <a href="{{ route('soporte.create') }}"
                 class="text-sm font-semibold text-purple-900 hover:text-purple-700 dark:text-purple-200 dark:hover:text-purple-100 underline underline-offset-4">
                ¿Necesitas ayuda?
              </a>
            </div>
          </div>

          {{-- Footer mini dentro de la card --}}
          <div class="px-6 pb-6">
            <div class="mt-4 rounded-2xl border border-purple-200/60 dark:border-purple-800/60 bg-purple-50/70 dark:bg-purple-950/30 p-4">
              <p class="text-xs leading-relaxed text-purple-900/90 dark:text-purple-100">
                Para mayor seguridad, activa 2FA en tu perfil y mantén tus credenciales en un gestor de contraseñas.
              </p>
            </div>
          </div>
        </div>
      </section>

    </div>
  </div>
</div>
@endsection
