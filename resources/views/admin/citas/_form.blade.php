@php
  $isEdit = isset($cita);
@endphp

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
  <div>
    <label class="text-sm">Profesional</label>
    <select name="profesional_id" class="mt-1 w-full rounded-lg border px-3 py-2" required>
      @foreach($profesionales as $p)
        <option value="{{ $p->id }}" @selected(old('profesional_id', $cita->profesional_id ?? null) == $p->id)>
          {{ trim(($p->nombres ?? '').' '.($p->apellidos ?? '')) }}
        </option>
      @endforeach
    </select>
    @error('profesional_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
  </div>

  <div>
    <label class="text-sm">Fecha</label>
    <input type="date" name="fecha" value="{{ old('fecha', $cita->fecha->format('Y-m-d') ?? '') }}"
           class="mt-1 w-full rounded-lg border px-3 py-2" required>
    @error('fecha') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
  </div>

  <div>
    <label class="text-sm">Tipo de atención</label>
    <select name="tipo_atencion" class="mt-1 w-full rounded-lg border px-3 py-2" required>
      @foreach (['presencial' => 'Presencial', 'remota' => 'Remota'] as $val => $lbl)
        <option value="{{ $val }}" @selected(old('tipo_atencion', $cita->tipo_atencion ?? 'presencial') === $val)>{{ $lbl }}</option>
      @endforeach
    </select>
    @error('tipo_atencion') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
  </div>

  <div>
    <label class="text-sm">Hora inicio</label>
    <input type="time" name="hora_inicio" value="{{ old('hora_inicio', $cita->hora_inicio ?? '') }}"
           class="mt-1 w-full rounded-lg border px-3 py-2" required>
    @error('hora_inicio') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
  </div>

  <div>
    <label class="text-sm">Hora fin</label>
    <input type="time" name="hora_fin" value="{{ old('hora_fin', $cita->hora_fin ?? '') }}"
           class="mt-1 w-full rounded-lg border px-3 py-2" required>
    @error('hora_fin') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
  </div>

  <div>
    <label class="text-sm">Estado</label>
    <select name="estado" class="mt-1 w-full rounded-lg border px-3 py-2" required>
      @foreach (['reservada'=>'Reservada','confirmada'=>'Confirmada','cancelada'=>'Cancelada','atendida'=>'Atendida'] as $val=>$lbl)
        <option value="{{ $val }}" @selected(old('estado', $cita->estado ?? 'reservada') === $val)>{{ $lbl }}</option>
      @endforeach
    </select>
    @error('estado') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
  </div>
</div>

<div class="mt-4">
  <label class="text-sm">Motivo</label>
  <textarea name="motivo" rows="3" class="mt-1 w-full rounded-lg border px-3 py-2">{{ old('motivo', $cita->motivo ?? '') }}</textarea>
  @error('motivo') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
</div>

<div class="pt-4 text-right">
  <a href="{{ route('admin.citas.index') }}" class="px-4 py-2 rounded border mr-2">Cancelar</a>
  <button class="px-4 py-2 rounded bg-purple-600 hover:bg-purple-700 text-white">
    {{ $isEdit ? 'Actualizar' : 'Guardar' }}
  </button>
</div>
