@extends('layouts.app')

@section('title', 'Editar bloque: ' . ($section->tipo ?? ''))

@section('content')
<div class="max-w-5xl mx-auto p-6">
  <h1 class="text-2xl font-semibold mb-6">
    Editar bloque: {{ $section->tipo }} 
    <span class="text-gray-500 text-sm">({{ $section->page_slug }})</span>
  </h1>

  @if(session('success'))
    <div class="mb-4 p-3 rounded bg-green-100 text-green-800">
      {{ session('success') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
      <ul class="list-disc pl-5 text-sm">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.portal.sections.update', $section) }}" class="space-y-6">
    @csrf
    @method('PUT')

    {{-- Título --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Título</label>
      <input type="text" name="titulo" value="{{ old('titulo', $section->titulo) }}"
             class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
    </div>

    {{-- Subtítulo --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Subtítulo</label>
      <input type="text" name="subtitulo" value="{{ old('subtitulo', $section->subtitulo) }}"
             class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
    </div>

    {{-- Contenido JSON --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Contenido (JSON)</label>
      <textarea name="contenido" rows="10"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 font-mono text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500">{{ old('contenido', json_encode($section->contenido, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) }}</textarea>
      <p class="text-xs text-gray-500 mt-1">Edita el JSON directamente o usa el generador si lo tienes implementado.</p>
    </div>

    {{-- Visible --}}
    <div class="flex items-center space-x-2">
      <input id="visible" type="checkbox" name="visible" value="1"
             @checked(old('visible', $section->visible)) 
             class="rounded border-gray-300 text-sky-600 shadow-sm focus:ring-sky-500">
      <label for="visible" class="text-sm font-medium text-gray-700 dark:text-gray-200">Visible</label>
    </div>

    {{-- Publicar desde / hasta --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Publicar desde</label>
        <input type="datetime-local" name="publicar_desde"
               value="{{ old('publicar_desde', optional($section->publicar_desde)->format('Y-m-d\TH:i')) }}"
               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Publicar hasta</label>
        <input type="datetime-local" name="publicar_hasta"
               value="{{ old('publicar_hasta', optional($section->publicar_hasta)->format('Y-m-d\TH:i')) }}"
               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
      </div>
    </div>

    {{-- Botones --}}
    <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-gray-800">
      <a href="{{ route('admin.portal.sections.index', ['pagina' => $section->page_slug]) }}"
         class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
         ← Volver
      </a>

      <button type="submit"
              class="rounded-md bg-sky-600 text-white px-4 py-2 text-sm font-medium hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
        Guardar cambios
      </button>
    </div>
  </form>
</div>
@endsection
