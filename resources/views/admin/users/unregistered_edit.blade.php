@extends('layouts.app')

@section('title', 'Editar paciente no registrado')

@section('content')
<div class="px-6 py-6">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-purple-700 dark:text-purple-200">
      ✏️ Editar paciente — {{ $rut }}
    </h1>

    <a href="{{ route('admin.users.unregistered') }}"
       class="inline-flex items-center gap-2 bg-purple-900 text-white text-sm font-semibold px-4 py-2 rounded-full shadow-sm hover:bg-purple-800 hover:shadow-md transition">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
      </svg>
      Volver
    </a>
  </div>

  @if(session('ok'))
    <div class="mb-4 rounded-lg bg-green-100 text-green-900 px-4 py-2 font-semibold">
      {{ session('ok') }}
    </div>
  @endif

  @if($errors->any())
    <div class="mb-4 rounded-lg bg-red-100 text-red-900 px-4 py-2">
      <ul class="list-disc ms-5">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6 shadow-sm">
    <form action="{{ route('admin.users.unregistered.update', $rut) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      @csrf
      @method('PUT')

      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Nombre</label>
        <input type="text" name="nombre_paciente" value="{{ old('nombre_paciente', $paciente->nombre_paciente) }}"
               class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
      </div>

      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email', $paciente->email) }}"
               class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
      </div>

      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Teléfono</label>
        <input type="text" name="telefono" value="{{ old('telefono', $paciente->telefono) }}"
               class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
      </div>

      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Dirección</label>
        <input type="text" name="direccion" value="{{ old('direccion', $paciente->direccion) }}"
               class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
      </div>

      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Fecha de nacimiento</label>
        <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', optional($paciente->fecha_nacimiento)->format('Y-m-d')) }}"
               class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
      </div>

      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Sexo</label>
        <input type="text" name="sexo" value="{{ old('sexo', $paciente->sexo) }}"
               class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
      </div>

      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Género</label>
        <input type="text" name="genero" value="{{ old('genero', $paciente->genero) }}"
               class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
      </div>

      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Sede (lugar de cita)</label>
        <select name="lugar_cita" class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
          <option value="">—</option>
          @foreach($sedes as $s)
            <option value="{{ $s }}" @selected(old('lugar_cita', $paciente->lugar_cita) === $s)>{{ $s }}</option>
          @endforeach
        </select>
      </div>

      <div class="md:col-span-2 mt-4">
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-purple-900 text-white text-sm font-semibold px-5 py-2.5 shadow hover:shadow-md hover:bg-purple-800 transition">
          💾 Guardar cambios
        </button>
              <button id="btnEnviarEmail" type="button"
              class="px-3 py-1 rounded bg-purple-700 text-white text-sm hover:bg-purple-800 transition">
        Enviar email
      </button>
        <a href="{{ route('admin.users.unregistered') }}"
           class="inline-flex items-center gap-2 ms-2 rounded-xl bg-gray-200 text-gray-800 text-sm font-semibold px-4 py-2 hover:bg-gray-300 transition">
          Cancelar
        </a>
      </div>
    </form>
  </div>
</div>
@endsection