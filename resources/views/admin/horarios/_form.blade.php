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
          {{ (old('profesional_id', $horario->profesional_id ?? null) == $p->id) ? 'selected' : '' }}>
          {{ $p->nombres }} {{ $p->apellidos }} — ({{ $p->tipoProfesional->nombre ?? 'Tipo' }}) — {{ $p->sucursal->nombre ?? 'Sucursal' }}
        </option>
      @endforeach
    </select>
  </div>

  {{-- Sucursal (auto) --}}
  <div>
    <label class="text-sm">Sucursal</label>
    <input id="sucursal_nombre" type="text" readonly
           class="w-full border rounded px-3 py-2 bg-gray-100 dark:bg-gray-800 dark:border-gray-600"
           value="">
    <small class="text-xs text-gray-500">Se asigna automáticamente según el profesional.</small>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="md:col-span-1">
      <label class="text-sm">Día*</label>
      <select name="dia_semana" required class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-600">
        <option value="">— Día —</option>
        @foreach($dias as $d)
          <option value="{{ $d }}" {{ old('dia_semana', $horario->dia_semana ?? '') === $d ? 'selected' : '' }}>
            {{ ucfirst($d) }}
          </option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="text-sm">Inicio*</label>
      <input type="time" name="hora_inicio" required
             value="{{ old('hora_inicio', \Illuminate\Support\Str::of($horario->hora_inicio ?? '')->limit(5,'')) }}"
             class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-600">
    </div>

    <div>
      <label class="text-sm">Fin*</label>
      <input type="time" name="hora_fin" required
             value="{{ old('hora_fin', \Illuminate\Support\Str::of($horario->hora_fin ?? '')->limit(5,'')) }}"
             class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-600">
    </div>

    <div>
      <label class="text-sm">Bloque (min)*</label>
      <input type="number" name="duracion_bloque" min="5" max="180" required
             value="{{ old('duracion_bloque', $horario->duracion_bloque ?? 30) }}"
             class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-600">
    </div>
  </div>

  <div>
    <label class="text-sm">Tipo de turno (opcional)</label>
    <input name="tipo" value="{{ old('tipo', $horario->tipo ?? '') }}"
           class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-600" placeholder="Mañana / Tarde / Consulta">
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const selProf = document.getElementById('profesional_id');
  const inputSucursal = document.getElementById('sucursal_nombre');

  const mapaSuc = {};
  @foreach(($profesionales ?? []) as $p)
    mapaSuc['{{ $p->id }}'] = @json($p->sucursal->nombre ?? '');
  @endforeach

  function syncSucursal() {
    const idp = selProf.value || '';
    inputSucursal.value = mapaSuc[idp] || '';
  }

  selProf.addEventListener('change', syncSucursal);
  syncSucursal();
});
</script>
@endpush
