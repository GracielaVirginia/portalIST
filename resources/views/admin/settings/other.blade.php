@extends('layouts.admin')
@section('title', 'Configuración General')

@section('admin')
<div class="max-w-md mx-auto bg-white dark:bg-gray-900 rounded-2xl shadow p-6">

  @if(session('success'))
    <div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/40 text-green-800 dark:text-green-200 px-4 py-2 text-sm">
      ✅ {{ session('success') }}
    </div>
  @endif

  <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">
    Configuración General del Portal
  </h2>

  <form action="{{ url('/other-settings') }}" method="POST" class="space-y-5">
    @csrf

    {{-- 🕒 Tiempo de inactividad --}}
    <div>
      <label for="session_timeout" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
        Tiempo de inactividad (minutos)
      </label>
      <select name="session_timeout" id="session_timeout"
              class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2 text-sm focus:ring-2 focus:ring-violet-500">
        @foreach($allowedTimeouts as $min)
          <option value="{{ $min }}" @selected($settings->session_timeout == $min)>
            {{ $min }} minutos
          </option>
        @endforeach
      </select>
      <p class="text-xs text-gray-500 mt-1">Define cuánto tiempo puede estar inactivo el usuario antes de cerrar su sesión.</p>
    </div>

    {{-- 🔠 Fuente --}}
    <div>
      <label for="font_family" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
        Tipo de fuente
      </label>
      <select name="font_family" id="font_family"
              class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2 text-sm focus:ring-2 focus:ring-violet-500">
        @foreach($availableFonts as $font)
          <option value="{{ $font }}" @selected($settings->font_family == $font)>
            {{ $font }}
          </option>
        @endforeach
      </select>
      <p class="text-xs text-gray-500 mt-1">Cambia el estilo de texto general del portal.</p>
    </div>

    {{-- Botón --}}
    <div class="pt-3">
      <button type="submit"
              class="w-full bg-violet-700 hover:bg-violet-600 text-white font-semibold py-2 rounded-lg shadow">
        Guardar cambios
      </button>
    </div>

  </form>
</div>
@endsection
