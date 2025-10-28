@extends('layouts.app')

@section('title', 'Crear paciente')

@section('content')
<div class="px-6 py-6">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-purple-700 dark:text-purple-200">➕ Crear paciente</h1>

    <a href="{{ route('admin.users.unregistered') }}"
       class="inline-flex items-center gap-2 bg-purple-900 text-white text-sm font-semibold px-4 py-2 rounded-full shadow-sm hover:bg-purple-800 hover:shadow-md transition">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
      </svg>
      Volver
    </a>
  </div>

  @if ($errors->any())
    <div class="mb-4 rounded-lg bg-red-100 text-red-900 px-4 py-2">
      <ul class="list-disc ms-5">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6 shadow-sm">
    <form method="POST" action="{{ route('admin.users.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      @csrf

      {{-- Paciente --}}
      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Tipo documento</label>
        <input type="text" name="tipo_documento" value="{{ old('tipo_documento','RUT') }}"
               class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
      </div>

      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Número documento</label>
        <input type="text" name="numero_documento" value="{{ old('numero_documento') }}"
               class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300" required>
      </div>

      <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Nombre del paciente</label>
        <input type="text" name="nombre_paciente" value="{{ old('nombre_paciente') }}"
               class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300" required>
      </div>

      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Fecha de nacimiento</label>
        <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}"
               class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
      </div>

      {{-- Select sexo desde BD --}}
      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Sexo</label>
        <select name="sexo" class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
          <option value="">—</option>
          @foreach ($sexos as $s)
            <option value="{{ $s }}" @selected(old('sexo')===$s)>{{ $s }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Género</label>
        <input type="text" name="genero" value="{{ old('genero') }}"
               class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
      </div>

      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Teléfono</label>
        <input type="text" name="telefono" value="{{ old('telefono') }}"
               class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
      </div>

      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email') }}"
               class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
      </div>

      <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Dirección</label>
        <input type="text" name="direccion" value="{{ old('direccion') }}"
               class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
      </div>

      {{-- Select grupo sanguíneo desde BD --}}
      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Grupo sanguíneo</label>
        <select name="grupo_sanguineo" class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
          <option value="">—</option>
          @foreach ($gruposSang as $g)
            <option value="{{ $g }}" @selected(old('grupo_sanguineo')===$g)>{{ $g }}</option>
          @endforeach
        </select>
      </div>

      {{-- Preferencias --}}
      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Idioma preferido</label>
        <select name="idioma_preferido" class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
          <option value="">—</option>
          @foreach ($idiomas as $i)
            <option value="{{ $i }}" @selected(old('idioma_preferido')===$i)>{{ $i }}</option>
          @endforeach
        </select>
      </div>

      <div class="md:col-span-2 flex items-center gap-6 mt-2">
        <label class="inline-flex items-center gap-2">
          <input type="checkbox" name="notificaciones_email" value="1" @checked(old('notificaciones_email'))>
          <span class="text-sm">Notif. Email</span>
        </label>
        <label class="inline-flex items-center gap-2">
          <input type="checkbox" name="notificaciones_sms" value="1" @checked(old('notificaciones_sms'))>
          <span class="text-sm">Notif. SMS</span>
        </label>
        <label class="inline-flex items-center gap-2">
          <input type="checkbox" name="notificaciones_app" value="1" @checked(old('notificaciones_app'))>
          <span class="text-sm">Notif. App</span>
        </label>
      </div>

      {{-- Gestión / Solicitud (selects desde BD) --}}
      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Origen solicitud</label>
        <select name="origen_solicitud" class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
          <option value="">—</option>
          @foreach ($origenesSolicitud as $o)
            <option value="{{ $o }}" @selected(old('origen_solicitud')===$o)>{{ $o }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Tipo gestión</label>
        <select name="tipo_gestion" class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
          <option value="">—</option>
          @foreach ($tiposGestion as $tg)
            <option value="{{ $tg }}" @selected(old('tipo_gestion')===$tg)>{{ $tg }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Especialidad</label>
        <select name="especialidad" class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
          <option value="">—</option>
          @foreach ($especialidades as $esp)
            <option value="{{ $esp }}" @selected(old('especialidad')===$esp)>{{ $esp }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Tipo examen</label>
        <select name="tipo_examen" class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
          <option value="">—</option>
          @foreach ($tiposExamen as $te)
            <option value="{{ $te }}" @selected(old('tipo_examen')===$te)>{{ $te }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Examen código</label>
        <select name="examen_codigo" class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
          <option value="">—</option>
          @foreach ($examenCodigos as $c)
            <option value="{{ $c }}" @selected(old('examen_codigo')===$c)>{{ $c }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Examen nombre</label>
        <select name="examen_nombre" class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
          <option value="">—</option>
          @foreach ($examenNombres as $n)
            <option value="{{ $n }}" @selected(old('examen_nombre')===$n)>{{ $n }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Fecha solicitud</label>
        <input type="date" name="fecha_solicitud" value="{{ old('fecha_solicitud') }}"
               class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
      </div>

      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Fecha cita programada</label>
        <input type="date" name="fecha_cita_programada" value="{{ old('fecha_cita_programada') }}"
               class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
      </div>

      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Sede (lugar de cita)</label>
        <select name="lugar_cita" class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
          <option value="">—</option>
          @foreach ($sedes as $s)
            <option value="{{ $s }}" @selected(old('lugar_cita')===$s)>{{ $s }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Estado solicitud</label>
        <select name="estado_solicitud" class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
          <option value="">—</option>
          @foreach ($estadosSolicitud as $es)
            <option value="{{ $es }}" @selected(old('estado_solicitud')===$es)>{{ $es }}</option>
          @endforeach
        </select>
      </div>

      {{-- Atención --}}
      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Tipo atención</label>
        <select name="tipo_atencion" class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
          <option value="">—</option>
          @foreach ($tiposAtencion as $ta)
            <option value="{{ $ta }}" @selected(old('tipo_atencion')===$ta)>{{ $ta }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Modalidad atención</label>
        <select name="modalidad_atencion" class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
          <option value="">—</option>
          @foreach ($modalidadesAtencion as $ma)
            <option value="{{ $ma }}" @selected(old('modalidad_atencion')===$ma)>{{ $ma }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Fecha atención</label>
        <input type="date" name="fecha_atencion" value="{{ old('fecha_atencion') }}"
               class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
      </div>

      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Estado asistencia</label>
        <select name="estado_asistencia" class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
          <option value="">—</option>
          @foreach ($estadosAsistencia as $ea)
            <option value="{{ $ea }}" @selected(old('estado_asistencia')===$ea)>{{ $ea }}</option>
          @endforeach
        </select>
      </div>

      {{-- Seguridad --}}
      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Nivel urgencia</label>
        <select name="nivel_urgencia" class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-purple-300">
          <option value="">—</option>
          @foreach ($nivelesUrgencia as $nu)
            <option value="{{ $nu }}" @selected(old('nivel_urgencia')===$nu)>{{ $nu }}</option>
          @endforeach
        </select>
      </div>

      <div class="md:col-span-2 mt-4">
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-purple-900 text-white text-sm font-semibold px-5 py-2.5 shadow hover:shadow-md hover:bg-purple-800 transition">
          💾 Guardar
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
