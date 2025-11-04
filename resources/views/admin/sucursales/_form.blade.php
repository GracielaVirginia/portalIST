@csrf
<div class="space-y-4">
  <div>
    <label class="text-sm">Nombre*</label>
    <input name="nombre" required value="{{ old('nombre', $sucursal->nombre ?? '') }}"
           class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-600">
  </div>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="text-sm">Ciudad</label>
      <input name="ciudad" value="{{ old('ciudad', $sucursal->ciudad ?? '') }}"
             class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-600">
    </div>
    <div>
      <label class="text-sm">Región</label>
      <input name="region" value="{{ old('region', $sucursal->region ?? '') }}"
             class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-600">
    </div>
  </div>
  <div>
    <label class="text-sm">Dirección</label>
    <input name="direccion" value="{{ old('direccion', $sucursal->direccion ?? '') }}"
           class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-600">
  </div>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="text-sm">Teléfono</label>
      <input name="telefono" value="{{ old('telefono', $sucursal->telefono ?? '') }}"
             class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-600">
    </div>
    <div>
      <label class="text-sm">Email</label>
      <input type="email" name="email" value="{{ old('email', $sucursal->email ?? '') }}"
             class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-600">
    </div>
  </div>
</div>
