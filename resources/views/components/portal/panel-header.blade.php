{{-- resources/views/components/portal/panel-header.blade.php --}}
@props([
  'paciente' => [
    'nombre' => 'Paciente',
    'rut' => null,
    'sexo' => null,
    'edad' => null,
    'idioma' => 'es',
    'cronico' => false,
    'condiciones' => [],
  ],
])

@php
  $nombre = $paciente['nombre'] ?? 'Paciente';
  $ini    = mb_substr($nombre, 0, 1, 'UTF-8');
  $rut    = $paciente['rut'] ?? null;
  $sexo   = strtoupper((string)($paciente['sexo'] ?? ''));
  $edad   = $paciente['edad'] ?? null;

  $cronico     = (bool)($paciente['cronico'] ?? false);
  $condiciones = $paciente['condiciones'] ?? [];
  $condLabel   = $cronico
                  ? ('Paciente crónico — ' . implode(', ', $condiciones ?: []))
                  : 'No crónico';
@endphp

<div
  x-data="{ showEdit:false }"
  class="w-auto rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-5 py-4 shadow-sm"
>
  {{-- Grid invertido: izquierda paciente (2 filas), derecha branding (2 filas) --}}
  <div class="grid grid-cols-1 md:grid-cols-12 md:grid-rows-2 gap-4 items-center">

    {{-- IZQUIERDA / FILA 1: Avatar + ¡Hola, Nombre! --}}
    <div class="md:col-span-6 md:row-start-1 flex items-center gap-4">
      <div class="grid h-12 w-12 place-items-center rounded-full bg-purple-900 text-white text-lg font-bold">
        {{ $ini }}
      </div>
      <div class="min-w-0">
        <div class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100 truncate">
          ¡Hola, {{ $nombre }}!
        </div>
      </div>
    </div>

    {{-- IZQUIERDA / FILA 2: RUT · Edad · Sexo · Píldora crónico --}}
    <div class="md:col-span-6 md:row-start-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-gray-700 dark:text-gray-300">
      @if($rut)
        <span class="whitespace-nowrap">
          <span class="text-gray-500 dark:text-gray-400">RUT:</span>
          <span class="font-medium">{{ $rut }}</span>
        </span>
        <span class="text-gray-400">•</span>
      @endif

      @if($edad)
        <span class="whitespace-nowrap">{{ $edad }} años</span>
        <span class="text-gray-400">•</span>
      @endif

      @if($sexo === 'M')
        <span class="whitespace-nowrap">Masculino</span>
        <span class="text-gray-400">•</span>
      @elseif($sexo === 'F')
        <span class="whitespace-nowrap">Femenino</span>
        <span class="text-gray-400">•</span>
      @elseif($sexo)
        <span class="whitespace-nowrap">{{ $sexo }}</span>
        <span class="text-gray-400">•</span>
      @endif

      <span class="inline-flex items-center gap-1 whitespace-nowrap">
        <span class="px-2 py-0.5 rounded-md bg-purple-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-purple-900 dark:text-gray-200 text-xs">
          {{ $condLabel }}
        </span>
      </span>
    </div>

    {{-- DERECHA (ocupa 2 filas): Branding alineado a la derecha --}}
    <div class="md:col-span-6 md:row-span-2 flex items-start justify-end gap-3">
      <div class="text-right leading-tight">
        <div class="text-base sm:text-lg font-semibold tracking-tight text-purple-900 dark:text-gray-100">
          Portal Paciente
        </div>
        <div class="text-sm text-purple-900/80 dark:text-gray-300">
          Instituto de Seguridad del Trabajo
        </div>
      </div>
      <img src="{{ asset('favicon.ico') }}" alt="Logo IST" class="h-10 sm:h-12 w-auto shrink-0" />
    </div>
  </div>

  {{-- Acciones: Editar perfil (modal) y Cerrar sesión --}}
  <div class="mt-4 flex items-center justify-end gap-2">
    <button type="button"
            @click="showEdit = true"
            class="inline-flex items-center rounded-xl border border-gray-200 dark:border-gray-700
                   bg-white dark:bg-gray-950 px-3 py-2 text-sm text-gray-700 dark:text-gray-200
                   hover:bg-gray-50 dark:hover:bg-gray-900">
      Editar perfil
    </button>

    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit"
              class="inline-flex items-center rounded-xl bg-purple-900 text-white
                     px-3 py-2 text-sm font-semibold hover:opacity-90">
        Cerrar sesión
      </button>
    </form>
  </div>

  {{-- MODAL: Editar perfil + Eliminar cuenta --}}
  <div x-show="showEdit" x-cloak
       class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
       @keydown.escape.window="showEdit=false">
    <div class="w-full max-w-lg rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-xl overflow-hidden">
      <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Editar perfil</h3>
        <button class="rounded-lg px-2 py-1 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                @click="showEdit=false">Cerrar</button>
      </div>

      <div class="p-4 space-y-6">
        {{-- Form actualizar perfil --}}
        <form method="POST" action="{{ route('profile.update') }}" class="space-y-3">
          @csrf
          @method('PATCH')

          <div>
            <label class="block text-sm text-gray-700 dark:text-gray-300">Nombre</label>
            <input type="text" name="name" value="{{ old('name', auth()->user()->name ?? '') }}"
                   class="mt-1 w-full rounded-md border-gray-300 dark:bg-gray-800 dark:border-gray-700"
                   required>
          </div>

          <div>
            <label class="block text-sm text-gray-700 dark:text-gray-300">Correo electrónico</label>
            <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}"
                   class="mt-1 w-full rounded-md border-gray-300 dark:bg-gray-800 dark:border-gray-700"
                   required>
          </div>

          <div>
            <label class="block text-sm text-gray-700 dark:text-gray-300">Nueva contraseña</label>
            <input type="password" name="password"
                   class="mt-1 w-full rounded-md border-gray-300 dark:bg-gray-800 dark:border-gray-700">
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Déjalo en blanco para no cambiarla.</p>
          </div>

          <div>
            <label class="block text-sm text-gray-700 dark:text-gray-300">Confirmar contraseña</label>
            <input type="password" name="password_confirmation"
                   class="mt-1 w-full rounded-md border-gray-300 dark:bg-gray-800 dark:border-gray-700">
          </div>

          <div class="flex justify-end">
            <button type="submit"
                    class="px-4 py-2 bg-purple-900 text-white rounded-md hover:opacity-90">
              Guardar cambios
            </button>
          </div>
        </form>

        <hr class="border-gray-200 dark:border-gray-700">

        {{-- Form eliminar cuenta --}}
        <form method="POST" action="{{ route('profile.destroy') }}"
              onsubmit="return confirm('¿Seguro que deseas eliminar tu cuenta? Esta acción no se puede deshacer.');">
          @csrf
          @method('DELETE')

          <label class="block text-sm text-gray-700 dark:text-gray-300">Confirma tu contraseña para eliminar la cuenta</label>
          <input type="password" name="password" required
                 class="mt-1 w-full rounded-md border-gray-300 dark:bg-gray-800 dark:border-gray-700">

          <div class="flex justify-end mt-3">
            <button type="submit"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
              Eliminar cuenta
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
