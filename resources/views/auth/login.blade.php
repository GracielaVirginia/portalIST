@extends('layouts.app')

@section('content')

<div class="min-h-screen w-full flex items-center justify-center bg-purple-100 dark:bg-gray-100">
<x-portal.info-bloques />

  <div class="relative w-full max-w-6xl mx-auto px-4">
    <div class="relative w-full aspect-[16/10] md:aspect-[4/3] lg:aspect-[3/2]">

      {{-- ========== CAPA 1: IMÁGENES (Tailwind alterna por modo) ========== --}}
      <img src="{{ asset('images/bg-purple.png') }}"
           alt="Tema claro"
           class="bg-swap bg-img--purple absolute inset-0 m-auto w-full h-full object-contain pointer-events-none select-none opacity-95
                  block dark:hidden" />

      <img src="{{ asset('images/bg-dark.png') }}"
           alt="Tema oscuro"
           class="bg-swap bg-img--dark absolute inset-0 m-auto w-full h-full object-contain pointer-events-none select-none opacity-90
                  hidden dark:block" />
      {{-- ========== CAPA 2: FORMULARIO (card translúcida) ========== --}}
      <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="w-[320px] sm:w-[360px]
           rounded-2xl shadow-xl border border-white/20 dark:border-white/10
           bg-white/30 dark:bg-gray-500/30 backdrop-blur-md
           text-sm leading-tight text-gray-900 dark:text-gray-100 p-4"
">

          <h1 class="text-2xl font-bold text-center mb-3 text-purple-900 dark:text-white">
            Iniciar Sesión
          </h1>

          <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4 p-3" novalidate>
            @csrf

            {{-- URL para verificar RUT vía fetch --}}
            <div id="verifConfig"
                 data-url="{{ route('verificar-rut') }}"
                 data-csrf="{{ csrf_token() }}"></div>

            {{-- RUT --}}
            <div class="space-y-1">
              <label for="rut" class="block text-sm font-semibold text-purple-900 dark:text-gray-600">RUT</label>
              
                <input
  id="rut"
  name="rut"
  type="text"
  value="{{ old('rut') }}"
  placeholder="11111111-1"
  maxlength="12"
  inputmode="text"
  autocomplete="username"
  required
  class="w-full rounded-lg border border-gray-300 dark:border-gray-600
         bg-gray-50 dark:bg-gray-300
         text-gray-900 dark:text-gray-600
         placeholder:text-gray-400 dark:placeholder:text-gray-600
         px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500
         shadow-sm transition"
/>

              <p id="rutFeedback" class="text-xs mt-1 text-gray-600 dark:text-gray-300"></p>
            </div>

            {{-- Password --}}
            <div class="space-y-1">
              <label for="password" class="block text-sm font-semibold text-purple-900 dark:text-gray-600">Password</label>
              <div class="relative">
                <input
                  id="password"
                  name="password"
                  type="password"
                  placeholder="********"
                  autocomplete="current-password"
                  disabled
                  required
  class="w-full rounded-lg border border-gray-300 dark:border-gray-600
         bg-gray-50 dark:bg-gray-300
         text-gray-900 dark:text-gray-600
         placeholder:text-gray-400 dark:placeholder:text-gray-600
         px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500
         shadow-sm transition"
                />
                <button type="button"
                        id="togglePassword"
                        class="absolute inset-y-0 right-2.5 my-auto h-7 w-7 grid place-items-center rounded-md
                               text-gray-500 hover:text-gray-700
                               dark:text-gray-400 dark:hover:text-gray-200
                               transition"
                        disabled
                        aria-label="Mostrar/Ocultar contraseña">
                  👁️
                </button>
              </div>
              @error('password')
                <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
              @enderror
            </div>

            {{-- Submit compacto --}}
            <div class="flex justify-center pt-2">
              <button id="btnLogin" type="submit" disabled
                      class="btn-neutral cursor-not-allowed opacity-70">
                Iniciar Sesión
              </button>
            </div>
          </form>

          {{-- Olvidé mi contraseña --}}
          <div class="text-center mt-3">
            <button type="button"
                    onclick="openModalRut()"
                    class="text-sm underline text-purple-900 hover:text-purple-800 dark:text-gray-600 dark:hover:text-gray-500">
              ¿Olvidaste tu contraseña?
            </button>
          </div>
        </div>
      </div>
      {{-- /FORM --}}
    </div>
  </div>
</div>

{{-- ========== MODAL: Olvidé mi contraseña ========== --}}
<div id="modalRut"
     class="fixed inset-0 hidden items-center justify-center bg-black/60 z-50 backdrop-blur-sm">
  <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-2xl w-96
              text-gray-900 dark:text-gray-100 border border-purple-200 dark:border-purple-800
              transition-all duration-300 ease-out">
    <h2 class="text-lg font-bold text-center text-purple-900 dark:text-purple-200">
      Olvidé mi Contraseña
    </h2>
    <p class="text-sm mt-3 text-center leading-relaxed text-gray-700 dark:text-gray-300">
      Dirígete a la <span class="font-semibold text-purple-800 dark:text-purple-300">Clínica IST</span>
      o comunícate con el Administrador para recuperar tus credenciales.
    </p>
    <div class="mt-6 flex justify-center">
      <button onclick="closeModalRut()"
              class="px-5 py-2 rounded-lg font-semibold text-white
                     bg-purple-900 hover:bg-purple-800
                     dark:bg-purple-700 dark:hover:bg-purple-600
                     focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-1
                     transition-all duration-200">
        Cerrar
      </button>
    </div>
  </div>
</div>


{{-- ========== MODAL: Ayuda (si lo usas) ========== --}}
<div id="helpModal" class="fixed inset-0 hidden items-center justify-center bg-black/60 z-50">
  <div class="bg-white dark:bg-slate-800 p-6 rounded-xl shadow-xl w-[22rem] text-gray-900 dark:text-gray-100">
    <h2 class="text-lg font-bold text-center">Ayuda</h2>
    <p class="text-sm mt-3 text-justify">
      Si tienes problemas para ingresar, verifica tu RUT y solicita soporte al administrador del sistema.
    </p>
    <div class="mt-5 flex justify-center">
      <button onclick="closeHelp()" class="btn-neutral">Cerrar</button>
    </div>
  </div>
</div>

{{-- Scripts mínimos para abrir/cerrar modales (no cambio nombres) --}}
<script>
  function openModalRut(){ const m = document.getElementById('modalRut'); m.classList.remove('hidden'); m.classList.add('flex'); }
  function closeModalRut(){ const m = document.getElementById('modalRut'); m.classList.add('hidden'); m.classList.remove('flex'); }

  function openHelp(){ const m = document.getElementById('helpModal'); m.classList.remove('hidden'); m.classList.add('flex'); }
  function closeHelp(){ const m = document.getElementById('helpModal'); m.classList.add('hidden'); m.classList.remove('flex'); }
</script>

@endsection
