{{-- resources/views/admin/noticias/_form.blade.php --}}
@csrf

<div class="space-y-6">
  {{-- Título --}}
  <div>
    <label class="block font-semibold mb-1">Título</label>
    <input name="titulo"
           value="{{ old('titulo', $noticia->titulo ?? '') }}"
           class="w-full rounded-xl border p-3"
           required>
    @error('titulo')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
  </div>

  {{-- Bajada --}}
  <div>
    <label class="block font-semibold mb-1">Bajada</label>
    <textarea name="bajada" rows="2"
              class="w-full rounded-xl border p-3">{{ old('bajada', $noticia->bajada ?? '') }}</textarea>
  </div>

  {{-- Contenido con editor reutilizable --}}
  <x-admin.richtext
      name="contenido"
      label="Contenido"
      :value="old('contenido', $noticia->contenido ?? '')"
      placeholder="Escribe el contenido completo de la noticia..."
      toolbar="basic"
  />

  {{-- Imagen --}}
  <div>
    <label class="block font-semibold mb-1">Imagen (opcional)</label>
    <input type="file" name="imagen" accept="image/*" class="block w-full">
    @error('imagen')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
  </div>

  {{-- Destacada --}}
  <label class="inline-flex items-center gap-2">
    <input type="checkbox" name="destacada" value="1"
           {{ old('destacada', $noticia->destacada ?? false) ? 'checked' : '' }}
           class="rounded">
    <span>¿Poner esta noticia en el home?</span>
  </label>

  {{-- Botones --}}
  <div class="flex gap-3">
    <button class="rounded-xl bg-purple-900 text-white px-4 py-2 font-semibold">
      {{ isset($noticia) ? 'Actualizar' : 'Guardar' }}
    </button>
    <a href="{{ route('admin.noticias.index') }}" class="rounded-xl border px-4 py-2">Cancelar</a>
  </div>
</div>
