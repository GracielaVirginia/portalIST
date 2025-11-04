@csrf
<div class="space-y-4">
  <div>
    <label class="text-sm">Sucursal*</label>
    <select name="idsucursal" required
            class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-600">
      <option value="">— Selecciona sucursal —</option>
      @foreach($sucursales as $s)
        <option value="{{ $s->id }}" {{ old('idsucursal', $tipo->idsucursal ?? null) == $s->id ? 'selected' : '' }}>
          {{ $s->nombre }}
        </option>
      @endforeach
    </select>
  </div>

  <div>
    <label class="text-sm">Nombre*</label>
    <input name="nombre" required
           value="{{ old('nombre', $tipo->nombre ?? '') }}"
           class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-600">
  </div>

  <div>
    <label class="text-sm">Descripción</label>
    <input name="descripcion"
           value="{{ old('descripcion', $tipo->descripcion ?? '') }}"
           class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-600">
  </div>
</div>
