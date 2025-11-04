@csrf
@php
  $dias = ['lunes','martes','miércoles','jueves','viernes','sábado','domingo'];
@endphp

<div class="space-y-4">
  {{-- Profesional (obligatorio) --}}
  <div>
    <label class="text-sm">Profesional*</label>
    <select id="profesional_id" name="profesional_id" required
            class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-600">
      <option value="">— Selecciona profesional —</option>
      @foreach(($profesionales ?? []) as $p)
        <option value="{{ $p->id }}"
                data-idsucursal="{{ $p->idsucursal }}"
                {{ (old('profesional_id', $bloqueo->profesional_id ?? null) == $p->id) ? 'selected' : '' }}>
          {{ $p->nombres }} {{ $p->apellidos }} — ({{ $p->tipoProfesional->nombre ?? 'Tipo' }}) — {{ $p->sucursal->nombre ?? 'Sucursal' }}
        </option>
      @endforeach
    </select>
  </div>

  {{-- Sucursal (auto) --}}
  <div>
    <label class="text-sm">Sucursal</label>
    <input id="sucursal_nombre" type="text" readonly
           class="w-full border rounded px-3 py-2 bg-gray-100 dark:bg-gray-800 dark:border-gray-600">
    <small class="text-xs text-gray-500">Se asigna automáticamente según el profesional.</small>
  </div>

  {{-- Alcance del bloqueo --}}
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div>
      <label class="text-sm">Bloqueo por fecha (puntual)</label>
      <input type="date" name="fecha"
             value="{{ old('fecha', optional($bloqueo->fecha ?? null)->format('Y-m-d')) }}"
             class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-600">
      <small class="text-xs text-gray-500">Déjalo vacío si será recurrente por día.</small>
    </div>

    <div>
      <label class="text-sm">Bloqueo recurrente por día</label>
      <select name="dia_semana" class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-600">
        <option value="">— Ninguno —</option>
        @foreach($dias as $d)
          <option value="{{ $d }}" {{ old('dia_semana', $bloqueo->dia_semana ?? '') === $d ? 'selected' : '' }}>
            {{ ucfirst($d) }}
          </option>
        @endforeach
      </select>
      <small class="text-xs text-gray-500">Usa esto si es un descanso fijo semanal.</small>
    </div>

    <div>
      <label class="text-sm">Horario (opcional)</label>
      <select id="horario_id" name="horario_id"
              class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-600">
        <option value="">— Sin horario específico —</option>
        {{-- se llena dinámicamente según profesional --}}
      </select>
      <small class="text-xs text-gray-500">Si lo eliges, validará que el bloqueo esté dentro de esa franja.</small>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div>
      <label class="text-sm">Inicio*</label>
      <input type="time" name="inicio" required
             value="{{ old('inicio', \Illuminate\Support\Str::of($bloqueo->inicio ?? '')->limit(5,'')) }}"
             class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-600">
    </div>
    <div>
      <label class="text-sm">Duración (min)*</label>
      <input type="number" name="duracion" min="5" max="600" required
             value="{{ old('duracion', $bloqueo->duracion ?? 60) }}"
             class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-600">
    </div>
    <div>
      <label class="text-sm">Motivo</label>
      <input name="motivo" value="{{ old('motivo', $bloqueo->motivo ?? '') }}"
             class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-600" placeholder="Colación, reunión, etc.">
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const selProf = document.getElementById('profesional_id');
  const selHorario = document.getElementById('horario_id');
  const inputSucursal = document.getElementById('sucursal_nombre');

  // mapa sucursal y horarios por profesional (inyectado del servidor)
  const mapaSuc = {};
  const mapaHor = {};
  @foreach(($profesionales ?? []) as $p)
    mapaSuc['{{ $p->id }}'] = @json($p->sucursal->nombre ?? '');
    mapaHor['{{ $p->id }}'] = [
      @foreach(($p->horarios ?? []) as $h)
        { id: {{ $h->id }}, label: '{{ ucfirst($h->dia_semana) }} {{ \Illuminate\Support\Str::of($h->hora_inicio)->limit(5,"") }}-{{ \Illuminate\Support\Str::of($h->hora_fin)->limit(5,"") }}' },
      @endforeach
    ];
  @endforeach

  function syncSucursalYHorarios() {
    const idp = selProf.value || '';
    inputSucursal.value = mapaSuc[idp] || '';
    // reset horarios
    selHorario.innerHTML = '<option value="">— Sin horario específico —</option>';
    (mapaHor[idp] || []).forEach(o => {
      const opt = document.createElement('option');
      opt.value = o.id; opt.textContent = o.label;
      selHorario.appendChild(opt);
    });

    // seleccionar el horario si viene de edición
    const pre = '{{ old('horario_id', $bloqueo->horario_id ?? '') }}';
    if (pre) selHorario.value = pre;
  }

  selProf.addEventListener('change', syncSucursalYHorarios);
  syncSucursalYHorarios();
});
</script>
@endpush
