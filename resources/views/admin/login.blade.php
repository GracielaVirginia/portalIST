@extends('layouts.app')

@section('content')

<div class="min-h-screen w-full flex items-center justify-center bg-purple-100 dark:bg-gray-100">

  <div class="relative w-full max-w-6xl mx-auto px-4">
    <div class="relative w-full aspect-[16/10] md:aspect-[4/3] lg:aspect-[3/2]">

      {{-- ========== CAPA 1: IMÁGENES DE FONDO (solo una imagen para admin) ========== --}}
      <img src="{{ asset('images/bg-admin-light.jpg') }}"
           alt="Fondo administrador"
           class="absolute inset-0 m-auto w-full h-full object-cover pointer-events-none select-none opacity-95" />

      {{-- ========== CAPA 2: FORMULARIO (card translúcida) ========== --}}
      <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="w-[320px] sm:w-[360px]
           rounded-2xl shadow-xl border border-white/20 dark:border-white/10
           bg-white/40 dark:bg-gray-700/40 backdrop-blur-md
           text-sm leading-tight text-gray-900 dark:text-gray-100 p-4">

          <h1 class="text-2xl font-bold text-center mb-3 text-purple-900 dark:text-white">
            Acceso Administrador
          </h1>

          <form method="POST" action="{{ route('admin.login.attemp') }}" class="space-y-4 p-3">
            @csrf

            {{-- Usuario / Email --}}
            <div class="space-y-1">
              <label for="username" class="block text-sm font-semibold text-purple-900 dark:text-gray-300">
                Usuario o Email
              </label>
              <input
                id="username"
                name="username"
                type="text"
                placeholder="admin@ist.cl"
                autocomplete="username"
                required
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                       bg-gray-50 dark:bg-gray-300
                       text-gray-900 dark:text-gray-700
                       placeholder:text-gray-400 dark:placeholder:text-gray-600
                       px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500
                       shadow-sm transition"
              />
                            @error('username')
                <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
              @enderror
            </div>

            {{-- Password --}}
            <div class="space-y-1">
              <label for="password" class="block text-sm font-semibold text-purple-900 dark:text-gray-300">
                Contraseña
              </label>
              <div class="relative">
                <input
                  id="password"
                  name="password"
                  type="password"
                  placeholder="********"
                  autocomplete="current-password"
                  required
                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                         bg-gray-50 dark:bg-gray-300
                         text-gray-900 dark:text-gray-700
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
                        aria-label="Mostrar/Ocultar contraseña"
                        onclick="togglePasswordVisibility()">
                  👁️
                </button>
              </div>
                            @error('password')
                <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
              @enderror
            </div>

            {{-- Botón de ingreso --}}
            <div class="flex justify-center pt-2">
              <button type="submit"
                      class="inline-flex items-center justify-center gap-2 rounded-lg
                             bg-purple-900 hover:bg-purple-800 text-white font-semibold
                             px-5 py-2.5 text-sm transition shadow-sm hover:shadow-md">
                Ingresar
              </button>
            </div>
          </form>

        </div>
      </div>
      {{-- /FORM --}}
    </div>
  </div>
</div>

{{-- Pequeño script para mostrar/ocultar contraseña --}}
<script>
  function togglePasswordVisibility() {
    const input = document.getElementById('password');
    input.type = input.type === 'password' ? 'text' : 'password';
  }
</script>

@endsection
