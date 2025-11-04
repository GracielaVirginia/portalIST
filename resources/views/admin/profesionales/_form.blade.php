@csrf
<div class="space-y-4">
  {{-- Sucursal --}}
  <div>
    <label class="text-sm">Sucursal*</label>
    <select id="idsucursal" name="idsucursal" required
            class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-600">
      <option value="">— Selecciona sucursal —</option>
      @foreach(($sucursales ?? []) as $s)
        <option value="{{ $s->id }}" {{ (old('idsucursal', $profesional->idsucursal ?? null) == $s->id) ? 'selected' : '' }}>
          {{ $s->nombre }}
        </option>
      @endforeach
    </select>
  </div>

  {{-- Tipo (filtrado por sucursal) --}}
  <div>
    <label class="text-sm">Tipo de profesional*</label>
    <select id="tipo_profesional_id" name="tipo_profesional_id" required
            class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-600">
      <option value="">— Selecciona tipo —</option>
      @foreach(($tipos ?? []) as $t)
        <option value="{{ $t->id }}"
                data-idsucursal="{{ $t->idsucursal }}"
                {{ (old('tipo_profesional_id', $profesional->tipo_profesional_id ?? null) == $t->id) ? 'selected' : '' }}>
          {{ $t->nombre }}
        </option>
      @endforeach
    </select>
    <p id="tipos-empty" class="hidden mt-1 text-xs text-amber-600">No hay tipos para la sucursal seleccionada.</p>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="text-sm">Nombres*</label>
      <input name="nombres" required value="{{ old('nombres', $profesional->nombres ?? '') }}"
             class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-600">
    </div>
    <div>
      <label class="text-sm">Apellidos</label>
      <input name="apellidos" value="{{ old('apellidos', $profesional->apellidos ?? '') }}"
             class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-600">
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="text-sm">RUT</label>
      <input name="rut" value="{{ old('rut', $profesional->rut ?? '') }}"
             class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-600">
    </div>
    <div>
      <label class="text-sm">Teléfono</label>
      <input name="telefono" value="{{ old('telefono', $profesional->telefono ?? '') }}"
             class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-600">
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="text-sm">Email</label>
      <input type="email" name="email" value="{{ old('email', $profesional->email ?? '') }}"
             class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-600">
    </div>
    <div>
      <label class="text-sm">Notas</label>
      <input name="notas" value="{{ old('notas', $profesional->notas ?? '') }}"
             class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-600" placeholder="Opcional">
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const selSucursal = document.getElementById('idsucursal');
  const selTipo = document.getElementById('tipo_profesional_id');
  const msgEmpty = document.getElementById('tipos-empty');

  function filtrarTipos() {
    const suc = selSucursal.value || '';
    let visibles = 0;
    [...selTipo.options].forEach((opt, idx) => {
      if (idx === 0) return; // placeholder
      const ok = String(opt.dataset.idsucursal || '') === String(suc);
      opt.hidden = !ok;
      if (ok) visibles++;
    });
    // reset selección inválida
    const selected = selTipo.options[selTipo.selectedIndex];
    if (!selected || selected.hidden) selTipo.value = '';
    msgEmpty.classList.toggle('hidden', visibles > 0);
  }

  selSucursal.addEventListener('change', filtrarTipos);
  filtrarTipos();
});
</script>
@endpush
