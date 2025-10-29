@extends('layouts.app')

@section('title', 'Validación — Número de caso')

@section('content')
<div class="min-h-[88vh] bg-gradient-to-b from-violet-100 to-white dark:from-gray-900 dark:to-gray-900">
  <div class="max-w-5xl mx-auto px-6 py-10">

    {{-- HERO --}}
    <div class="flex items-center gap-4 mb-6">
      <div class="flex items-center justify-center w-12 h-12 rounded-2xl bg-violet-900 text-white shadow">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 1a5 5 0 00-5 5v3H6a2 2 0 00-2 2v9a2 2 0 002 2h12a2 2 0 002-2V11a2 2 0 00-2-2h-1V6a5 5 0 00-5-5zm-3 8V6a3 3 0 016 0v3H9z"/>
        </svg>
      </div>
      <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-violet-900 dark:text-violet-200">
          Validar con Nº de caso
        </h1>
        <p class="text-sm text-violet-900/80 dark:text-gray-300">
          Ingresa el número de caso asignado para verificar tu identidad.
        </p>
      </div>
    </div>

    {{-- STEPPER --}}
    <div class="flex items-center mb-8 select-none">
      <div class="flex-1 text-center">
        <div class="w-9 h-9 mx-auto rounded-full flex items-center justify-center bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold">1</div>
        <p class="mt-1 text-xs font-semibold text-gray-500 dark:text-gray-400">Registrarse</p>
      </div>
      <div class="w-10 md:w-24 h-1 bg-violet-300/70 dark:bg-gray-700 mx-2"></div>
      <div class="flex-1 text-center">
        <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-violet-900 text-white font-bold ring-2 ring-violet-300">2</div>
        <p class="mt-1 text-xs font-semibold text-violet-900 dark:text-violet-100">Nº de caso</p>
      </div>
      <div class="w-10 md:w-24 h-1 bg-violet-300/70 dark:bg-gray-700 mx-2"></div>
      <div class="flex-1 text-center">
        <div class="w-9 h-9 mx-auto rounded-full flex items-center justify-center bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold">3</div>
        <p class="mt-1 text-xs font-semibold text-gray-500 dark:text-gray-400">Ver resultados</p>
      </div>
    </div>

    {{-- GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">

      {{-- PANEL IZQUIERDO --}}
      <aside class="md:col-span-2 bg-violet-50 dark:bg-gray-800/60 p-5 rounded-2xl ring-1 ring-violet-200/60 dark:ring-gray-700">
        <h2 class="text-base font-semibold text-violet-900 dark:text-white flex items-center gap-2">
          <span class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-violet-900 text-white text-xs">i</span>
          Ayuda rápida
        </h2>
        <ul class="mt-3 space-y-2 text-sm text-violet-900/90 dark:text-gray-200 list-disc list-inside">
          <li>El <strong>Nº de caso</strong> está en tu comprobante o correo de atención.</li>
          <li>Si no lo recuerdas, pídelo en el centro de atención.</li>
        </ul>

        <div class="mt-5 p-3 rounded-xl bg-white/60 dark:bg-gray-900/50 ring-1 ring-violet-200/50 dark:ring-gray-700">
          <p class="text-xs text-gray-700 dark:text-gray-300">
            <span class="font-semibold">Seguridad:</span> Validamos tu identidad para proteger tu información clínica.
          </p>
        </div>
      </aside>

      {{-- FORMULARIO --}}
      <section class="md:col-span-3 bg-white/80 dark:bg-gray-900/60 p-6 rounded-2xl ring-1 ring-black/5 shadow-sm">

        {{-- Flash / Estados --}}
        @if(!empty($bloqueado) && $bloqueado)
          <div class="mb-4 p-3 rounded-lg bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-200">
            Tu cuenta fue bloqueada temporalmente por intentos fallidos. Inténtalo más tarde o contacta soporte.
          </div>
        @elseif(session('success'))
          <div class="mb-4 p-3 rounded-lg bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200">
            {{ session('success') }}
          </div>
        @endif

        <form method="POST" action="{{ route('validacion.procesar') }}" class="space-y-5" x-data="{submitting:false}">
          @csrf

          <label for="numero_caso" class="block text-sm font-semibold text-violet-900 dark:text-violet-100">Número de caso</label>

          <div class="relative">
            <input
              id="numero_caso"
              name="numero_caso"
              type="text"
              inputmode="numeric"
              placeholder="Ej: 123456-AB"
              class="w-full pl-11 pr-11 py-3 rounded-xl border border-violet-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:outline-none focus:ring-2 focus:ring-violet-500"
              {{ (!empty($bloqueado) && $bloqueado) ? 'disabled' : '' }}
              required
            />
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-violet-700 dark:text-violet-300">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M9 2a7 7 0 105.292 12.01l4.349 4.35 1.414-1.415-4.35-4.348A7 7 0 009 2zm0 2a5 5 0 110 10A5 5 0 019 4z"/>
              </svg>
            </span>
            <span x-show="submitting" class="absolute right-3 top-1/2 -translate-y-1/2 animate-spin" style="display:none">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-violet-700 dark:text-violet-300" viewBox="0 0 24 24" fill="none">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path>
              </svg>
            </span>
          </div>

          <p class="text-xs text-gray-600 dark:text-gray-300">
            Intentos fallidos: <strong>{{ (int)($intentosFallidos ?? 0) }}</strong> / 3
          </p>

          @error('numero_caso')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
          @enderror
          @error('validacion')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
          @enderror
          @if (session('error_message'))
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ session('error_message') }}</p>
          @endif

          <div class="flex items-center gap-3 pt-2">
            {{-- VALIDAR (submit) --}}
            <button type="submit"
              @click="submitting=true"
              class="inline-flex items-center justify-center gap-2 px-6 py-3 text-base font-semibold rounded-xl
                     bg-violet-900 text-white hover:bg-violet-800 transition
                     disabled:opacity-60 disabled:cursor-not-allowed"
              {{ (!empty($bloqueado) && $bloqueado) ? 'disabled' : '' }}
            >
              Validar
            </button>

            {{-- SALIR (solo botón, NO submit) --}}
            <button type="button" id="btnSalirCaso"
              onclick="window.__salirCaso()"
              class="ml-auto px-4 py-3 rounded-xl bg-white/70 dark:bg-gray-800/70 ring-1 ring-violet-200/60 dark:ring-gray-700 text-sm text-gray-700 dark:text-gray-200 hover:bg-white/90 dark:hover:bg-gray-800/90 transition">
              Salir
            </button>
          </div>
        </form>

        <div class="mt-6 border-t border-violet-200/60 dark:border-gray-800"></div>

        <div class="mt-4 text-xs text-gray-600 dark:text-gray-400">
          ¿Problemas con tu número de caso? <a href="{{ url('soporte.create') }}" class="underline text-violet-900 dark:text-violet-300">Contacta soporte</a>.
        </div>

      </section>
    </div>
  </div>
</div>

{{-- Form oculto para logout (FUERA del form principal) --}}
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
  @csrf
</form>
@endsection

{{-- SweetAlert2 + función global (robusta) --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  window.__salirCaso = function() {
    // Si SweetAlert está disponible, úsalo; si no, fallback a confirm()
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: '¿Seguro que quiere salir?',
        text: 'Aún no ha terminado su validación.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, salir',
        cancelButtonText: 'Continuar aquí',
        reverseButtons: true,
        focusCancel: true
      }).then((r) => {
        if (r.isConfirmed) {
          const formLogout = document.getElementById('logout-form');
          if (formLogout) formLogout.submit();
        }
      });
    } else {
      if (confirm('¿Seguro que quiere salir? Aún no ha terminado su validación.')) {
        const formLogout = document.getElementById('logout-form');
        if (formLogout) formLogout.submit();
      }
    }
  };
</script>
