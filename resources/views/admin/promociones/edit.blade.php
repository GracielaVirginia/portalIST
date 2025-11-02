@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white dark:bg-gray-900 rounded-2xl shadow p-8 mt-8">
    <h1 class="text-2xl font-bold mb-6 text-purple-700 dark:text-purple-300">
        Editar Promoción
    </h1>

    <form method="POST" action="{{ route('admin.promociones.update', $promocion) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Título --}}
        <div class="mb-5">
            <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Título</label>
            <input type="text" name="titulo" value="{{ old('titulo', $promocion->titulo) }}"
                   class="w-full border rounded-lg px-3 py-2 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
        </div>

        {{-- Subtítulo --}}
        <div class="mb-5">
            <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Subtítulo</label>
            <input type="text" name="subtitulo" value="{{ old('subtitulo', $promocion->subtitulo) }}"
                   class="w-full border rounded-lg px-3 py-2 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
        </div>

        {{-- Contenido (editor WYSIWYG) --}}
        <x-admin.richtext
            name="contenido_html"
            label="Contenido"
            :value="old('contenido_html', $promocion->contenido_html)"
            placeholder="Describe la promoción, beneficios, condiciones, vigencia…"
            toolbar="basic"
        />

        {{-- Imagen (preview + input file estilizado) --}}
        <div class="mb-8">
            <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-2">Imagen de la promoción</label>

            @if(!empty($promocion->imagen_path))
                <div class="mb-3">
                    <img src="{{ asset($promocion->imagen_path) }}"
                         class="w-full max-w-sm rounded-xl shadow-md mb-2 ring-1 ring-purple-200 dark:ring-purple-700"
                         alt="Imagen actual">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Imagen actual</p>
                </div>
            @endif

            <label for="imagen"
                   class="flex flex-col items-center justify-center w-full border-2 border-dashed border-purple-300 dark:border-purple-600 rounded-xl cursor-pointer bg-purple-50/30 dark:bg-gray-800 hover:bg-purple-100/40 dark:hover:bg-gray-700 transition">
                <div class="flex flex-col items-center justify-center py-10">
                    <i class="fa-solid fa-cloud-arrow-up text-4xl text-purple-500 mb-2"></i>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        <span class="font-semibold">Haz clic para subir una imagen</span> o arrástrala aquí
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Formatos: JPG, PNG, WEBP. Tamaño máximo 2MB.</p>
                </div>
                <input id="imagen" type="file" name="imagen" class="hidden" accept="image/*">
            </label>
        </div>

        {{-- Estado y destacada --}}
        <div class="flex items-center justify-start gap-6 mb-8">
            <label class="inline-flex items-center">
                <input type="checkbox" name="activo" value="1"
                       {{ old('activo', $promocion->activo) ? 'checked' : '' }}
                       class="rounded text-purple-600 focus:ring-purple-500">
                <span class="ml-2 text-gray-700 dark:text-gray-300 font-medium">Activa</span>
            </label>

            <label class="inline-flex items-center">
                <input type="checkbox" name="destacada" value="1"
                       {{ old('destacada', $promocion->destacada) ? 'checked' : '' }}
                       class="rounded text-purple-600 focus:ring-purple-500">
                <span class="ml-2 text-gray-700 dark:text-gray-300 font-medium">Marcar como destacada</span>
            </label>
        </div>

        {{-- Botones --}}
        <div class="flex justify-between">
            <a href="{{ route('admin.promociones.index') }}"
               class="text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-white">
                ← Volver
            </a>

            <button type="submit"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-semibold shadow transition">
                Actualizar
            </button>
        </div>
    </form>
</div>
@endsection
