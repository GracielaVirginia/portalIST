{{-- resources/views/portal/promocion-show.blade.php --}}
@extends('layouts.app')

@section('content')
<section class="max-w-5xl mx-auto px-6 md:px-0 py-8">
    {{-- Breadcrumb --}}
    <nav class="text-sm mb-6 text-gray-500 dark:text-gray-400">
        <a href="{{ route('portal.promociones') }}" class="hover:text-purple-600">Promociones</a>
        <span class="mx-2">/</span>
        <span class="text-gray-700 dark:text-gray-200">{{ $promocion->titulo }}</span>
    </nav>

    {{-- Header con imagen --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow overflow-hidden">
        <div class="relative">
            <img
                src="{{ $promocion->imagen_path ? asset($promocion->imagen_path) : asset('images/default-promo.jpg') }}"
                alt="{{ $promocion->titulo }}"
                class="w-full h-64 md:h-80 object-cover">
            @if($promocion->destacada)
                <span class="absolute top-4 right-4 bg-purple-600 text-white text-xs font-semibold px-3 py-1 rounded-full shadow">
                    Destacada
                </span>
            @endif
        </div>

        <div class="p-6 md:p-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
                {{ $promocion->titulo }}
            </h1>

            @if($promocion->subtitulo)
                <p class="mt-2 text-gray-600 dark:text-gray-300">
                    {{ $promocion->subtitulo }}
                </p>
            @endif

            <hr class="my-6 border-gray-200 dark:border-gray-800">

            {{-- Contenido enriquecido desde el editor --}}
            <article class="prose prose-purple max-w-none dark:prose-invert">
                {!! $promocion->contenido_html !!}
            </article>

            <div class="mt-8 flex items-center justify-between">
                <a href="{{ route('portal.promociones') }}"
                   class="inline-flex items-center gap-2 text-purple-700 dark:text-purple-300 font-semibold hover:underline">
                    ← Volver a promociones
                </a>

                {{-- Si quisieras un CTA adicional a otra ruta, ponlo aquí --}}
                {{-- <a href="{{ url('/agenda') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2 rounded-lg font-semibold">
                    Agendar →
                </a> --}}
            </div>
        </div>
    </div>
</section>
@endsection
