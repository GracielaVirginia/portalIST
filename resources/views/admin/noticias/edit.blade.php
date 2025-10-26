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
      @error('titulo')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>

    {{-- Bajada --}}
    <div>
      <label class="block font-semibold mb-1">Bajada</label>
      <textarea name="bajada" rows="2"
                class="w-full rounded-xl border p-3">{{ old('bajada', $noticia->bajada) }}</textarea>
    </div>

    {{-- Contenido --}}
    <div>
      <label class="block font-semibold mb-1">Contenido</label>
      <textarea name="contenido" rows="6"
                class="w-full rounded-xl border p-3">{{ old('contenido', $noticia->contenido) }}</textarea>
    </div>

    {{-- Imagen actual --}}
    @if($noticia->imagen_url)
      <div class="mb-2">
        <p class="font-semibold mb-2">Imagen actual:</p>
        <img src="{{ $noticia->imagen_url }}" alt="Imagen actual"
             class="w-64 h-36 object-cover rounded-lg shadow">
      </div>
    @endif

    {{-- Subir nueva imagen --}}
    <div>
      <label class="block font-semibold mb-1">Cambiar imagen (opcional)</label>
      <input type="file" name="imagen" accept="image/*" class="block w-full">
      @error('imagen')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
      @enderror
      <p class="text-xs text-gray-500 mt-1">Si seleccionas una nueva imagen, reemplazará la actual.</p>
    </div>

    {{-- Checkbox destacada --}}
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
