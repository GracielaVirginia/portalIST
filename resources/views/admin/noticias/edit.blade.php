@extends('layouts.app', ['topbar' => true, 'navbar' => true])

@section('title', 'Editar noticia')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-6">
  <h1 class="text-2xl font-bold text-purple-700 dark:text-purple-200 mb-4">
    ✏️ Editar noticia
  </h1>

  <form method="POST"
        action="{{ route('admin.noticias.update', $noticia) }}"
        enctype="multipart/form-data"
        class="space-y-6">
    @csrf
    @method('PUT')

    {{-- Título --}}
    <div>
      <label class="block font-semibold mb-1">Título</label>
      <input name="titulo"
             value="{{ old('titulo', $noticia->titulo) }}"
             class="w-full rounded-xl border p-3"
             required>
      @error('titulo')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- Bajada --}}
    <div>
      <label class="block font-semibold mb-1">Bajada</label>
      <textarea name="bajada" rows="2"
                class="w-full rounded-xl border p-3">{{ old('bajada', $noticia->bajada) }}</textarea>
    </div>

    {{-- Contenido con editor reutilizable (sin cambiar el name) --}}
    <x-admin.richtext
      name="contenido"
      label="Contenido"
      :value="old('contenido', $noticia->contenido)"
      placeholder="Escribe el contenido completo de la noticia…"
      toolbar="basic"
    />

    {{-- Imagen actual --}}
    @if($noticia->imagen_url)
      <div>
        <p class="font-semibold mb-2">Imagen actual:</p>
        <img src="{{ $noticia->imagen_url }}" alt="Imagen actual"
             class="w-64 h-36 object-cover rounded-lg shadow ring-1 ring-purple-200 dark:ring-purple-700">
      </div>
    @endif

    {{-- Cambiar imagen (opcional) con zona de carga y preview --}}
    <div>
      <label class="block font-semibold mb-2">Cambiar imagen (opcional)</label>

      <input id="imagen" type="file" name="imagen" accept="image/*" class="hidden">

      <label for="imagen"
             class="flex flex-col items-center justify-center w-full border-2 border-dashed border-purple-300 dark:border-purple-600 rounded-xl cursor-pointer bg-purple-50/30 dark:bg-gray-800 hover:bg-purple-100/40 dark:hover:bg-gray-700 transition">
        <div id="label-texto" class="flex flex-col items-center justify-center py-10">
          <i class="fa-solid fa-cloud-arrow-up text-4xl text-purple-500 mb-2"></i>
          <p class="text-sm text-gray-700 dark:text-gray-300">
            <span class="font-semibold">Haz clic para subir una imagen</span> o arrástrala aquí
          </p>
          <p class="text-xs text-gray-500 mt-1">Formatos: JPG, PNG, WEBP. Máx. 2 MB.</p>
        </div>
      </label>

      <div id="preview-wrapper" class="mt-3 hidden">
        <p id="preview-name" class="text-sm text-purple-700 dark:text-purple-300 font-semibold mb-2"></p>
        <img id="preview-img" class="w-64 h-36 object-cover rounded-lg shadow ring-1 ring-purple-200 dark:ring-purple-700" alt="Vista previa">
      </div>

      @error('imagen')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
      <p class="text-xs text-gray-500 mt-1">Si seleccionas una nueva imagen, reemplazará la actual.</p>
    </div>

    {{-- Destacada --}}
    <label class="inline-flex items-center gap-2">
      <input type="checkbox" name="destacada" value="1"
             @checked(old('destacada', $noticia->destacada))
             class="rounded">
      <span>¿Poner esta noticia en el home?</span>
    </label>

    {{-- Botones --}}
    <div class="flex gap-3">
      <button class="rounded-xl bg-purple-900 text-white px-4 py-2 font-semibold hover:bg-purple-800">
        💾 Guardar cambios
      </button>
      <a href="{{ route('admin.noticias.index') }}"
         class="rounded-xl border px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-800">
        Cancelar
      </a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const input = document.getElementById('imagen');
  const labelTexto = document.getElementById('label-texto');
  const wrap = document.getElementById('preview-wrapper');
  const nameEl = document.getElementById('preview-name');
  const img = document.getElementById('preview-img');

  if (!input) return;

  input.addEventListener('change', (e) => {
    const file = e.target.files?.[0];
    if (!file) { wrap.classList.add('hidden'); nameEl.textContent=''; img.src=''; return; }
    labelTexto.innerHTML = `<p class="text-center text-purple-700 dark:text-purple-300 font-semibold text-sm">📸 Imagen seleccionada: ${file.name}</p>`;
    nameEl.textContent = `Imagen seleccionada: ${file.name}`;
    const reader = new FileReader();
    reader.onload = ev => { img.src = ev.target.result; wrap.classList.remove('hidden'); };
    reader.readAsDataURL(file);
  });
});
</script>
@endpush
