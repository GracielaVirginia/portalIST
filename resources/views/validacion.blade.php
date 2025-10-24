@extends('layouts.app')

@section('title', 'Validación')

@section('content')
<div class="flex min-h-screen bg-violet-100 dark:bg-gray-900 justify-center items-center">
  <div class="w-full max-w-4xl p-6 rounded-2xl bg-white/30 dark:bg-gray-800/30 backdrop-blur-md ring-1 ring-black/5 shadow-md">

    {{-- Botón salir --}}
    <div class="flex justify-end mb-6">
      <form id="logout-form" action="{{ route('logout') }}" method="POST">@csrf</form>
      <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
         class="px-3 py-1 bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm font-semibold rounded-md shadow-sm hover:bg-gray-300 dark:hover:bg-gray-600 transition">
        Salir
      </a>
    </div>

    {{-- Pasos --}}
    <div class="flex justify-between items-center mb-6 select-none">
      <div class="w-1/3 text-center">
        <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-white font-bold">1</div>
        <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Registrarse</p>
      </div>
      <div class="flex-1 h-1 bg-violet-300 dark:bg-gray-700 mx-2"></div>
      <div class="w-1/3 text-center">
        <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-violet-900 text-white font-bold ring-2 ring-violet-300">2</div>
        <p class="text-sm font-semibold text-violet-900 dark:text-white">Ingresar Nº de Caso</p>
      </div>
      <div class="flex-1 h-1 bg-violet-300 dark:bg-gray-700 mx-2"></div>
      <div class="w-1/3 text-center">
        <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-white font-bold">3</div>
        <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Ver Resultados</p>
      </div>
    </div>

    {{-- Cuerpo --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
      {{-- Lado izquierdo --}}
      <div class="md:col-span-2 bg-violet-50 dark:bg-gray-700 p-5 rounded-xl">
        <h2 class="text-lg font-semibold text-violet-900 dark:text-white">Valide su identidad</h2>
        <p class="text-sm text-violet-900/80 dark:text-gray-200 mt-2">
          Para continuar, ingrese su <span class="font-semibold">Nº de caso</span>.
        </p>
        <ul class="mt-4 space-y-2 text-sm text-violet-900/80 dark:text-gray-200 list-disc list-inside">
          <li>Si no lo recuerdas, solicítalo en el centro de atención.</li>
          <li><span class="font-semibold">Si no sabes cuál, revisa tu correo</span> de confirmación (aparece como “Nº de caso”).</li>
        </ul>
      </div>

      {{-- Formulario --}}
      <div class="md:col-span-3 p-5 rounded-xl">
        @if($bloqueado)
          <div class="p-3 rounded-lg bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-200 mb-4">
            Tu cuenta ha sido bloqueada por intentos fallidos. Contacta al administrador.
          </div>
        @elseif(session('success'))
          <div class="p-3 rounded-lg bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200 mb-4">
            {{ session('success') }}
          </div>
        @endif

        <form method="POST" action="{{ route('validacion.procesar') }}" class="space-y-4">
          @csrf

          <label for="numero_caso" class="block text-violet-900 dark:text-white font-medium">Número de caso</label>
          <input
            id="numero_caso"
            name="numero_caso"
            type="text"
            inputmode="numeric"
            placeholder="Ej: 1234..."
            class="w-full p-3 pr-10 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:outline-none focus:ring-2 focus:ring-violet-500"
            {{ $bloqueado ? 'disabled' : '' }}
            required
          />

          <p class="text-xs text-gray-600 dark:text-gray-300">
            Intentos fallidos: <strong>{{ (int) ($intentosFallidos ?? 0) }}</strong> / 3
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
            <button type="submit"
              class="inline-flex items-center justify-center gap-2 px-6 py-3 text-base font-semibold rounded-lg
                     bg-violet-900 text-white hover:bg-violet-800 transition
                     disabled:opacity-60 disabled:cursor-not-allowed"
              {{ $bloqueado ? 'disabled' : '' }}
            >
              Validar
            </button>

            <a href="{{ url()->previous() }}"
               class="px-4 py-3 rounded-lg bg-gray-200 dark:bg-gray-700 dark:text-gray-100 hover:bg-gray-300 dark:hover:bg-gray-600 transition">
              Volver
            </a>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>
@endsection
