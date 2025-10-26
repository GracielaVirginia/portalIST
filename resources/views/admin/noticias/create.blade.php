@extends('layouts.app', ['topbar' => true, 'navbar' => true])

@section('title','Agregar noticia')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-6">
  <h1 class="text-2xl font-bold text-purple-700 dark:text-purple-200 mb-4">Nueva noticia</h1>

  <form method="POST" action="{{ route('admin.noticias.store') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf

    <div>
      <label class="block font-semibold mb-1">Título</label>
      <input name="titulo" value="{{ old('titulo') }}" class="w-full rounded-xl border p-3" required>
      @error('titulo')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
      <label class="block font-semibold mb-1">Bajada</label>
      <textarea name="bajada" rows="2" class="w-full rounded-xl border p-3">{{ old('bajada') }}</textarea>
    </div>

    <div>
      <label class="block font-semibold mb-1">Contenido</label>
      <textarea name="contenido" rows="6" class="w-full rounded-xl border p-3">{{ old('contenido') }}</textarea>
    </div>

    <div>
      <label class="block font-semibold mb-1">Imagen (opcional)</label>
      <input type="file" name="imagen" accept="image/*" class="block w-full">
      @error('imagen')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
    </div>

    <label class="inline-flex items-center gap-2">
      <input type="checkbox" name="destacada" value="1" class="rounded">
      <span>¿Poner esta noticia en el home?</span>
    </label>

    <div class="flex gap-3">
      <button class="rounded-xl bg-purple-900 text-white px-4 py-2 font-semibold">Guardar</button>
      <a href="{{ route('admin.noticias.index') }}" class="rounded-xl border px-4 py-2">Cancelar</a>
    </div>
  </form>
</div>
@endsection
