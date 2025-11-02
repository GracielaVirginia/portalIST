@extends('layouts.app')

@section('content')
    @php
        $banner = $destacada ?? null;
    @endphp

    {{-- ===== HERO DESTACADO ===== --}}
    @if ($banner)
        <section class="relative overflow-hidden rounded-2xl mb-12">
            <div class="absolute inset-0 bg-black/50"></div>
            <img src="{{ asset($banner->imagen_path ?? 'images/banner-promociones.jpg') }}" alt="{{ $banner->titulo }}"
                class="w-full h-96 object-cover">
            <div class="absolute inset-0 flex flex-col justify-center items-center text-center text-white px-6">
                <h1 class="text-3xl md:text-5xl font-bold drop-shadow-lg">{{ $banner->titulo }}</h1>
                <p class="mt-3 text-lg opacity-90 max-w-2xl">{{ $banner->subtitulo }}</p>
                @if ($banner->cta_url)
                    <a href="{{ $banner->cta_url }}"
                        class="mt-6 inline-block bg-white text-purple-700 font-semibold px-6 py-3 rounded-full shadow hover:bg-purple-100 transition-all">
                        {{ $banner->cta_texto }} →
                    </a>
                @endif
            </div>
        </section>
    @endif
    {{-- Botón volver (más arriba) --}}
    <div class="mt-6 flex justify-end mr-2">
        <x-ui.back-button :href="route('portal.home')" label="Volver" variant="outline" size="sm" class="mr-4" />
    </div>

    {{-- ===== LISTADO DE PROMOCIONES ===== --}}
    <section class="max-w-6xl mx-auto px-6 md:px-0">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6 text-center">
            Promociones disponibles
        </h2>

        @if ($promos->isEmpty())
            <p class="text-center text-gray-500 dark:text-gray-400">No hay promociones vigentes por ahora.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($promos as $promo)
                    <div
                        class="group bg-white dark:bg-gray-800 rounded-2xl shadow-md overflow-hidden transition-all hover:-translate-y-1 hover:shadow-xl">
                        <a href="{{ route('portal.promociones.show', $promo) }}">
                            <div class="relative">
                                <img src="{{ asset($promo->imagen_path ?? 'images/default-promo.jpg') }}"
                                    alt="{{ $promo->titulo }}" class="w-full h-56 object-cover">
                                @if ($promo->destacada)
                                    <span
                                        class="absolute top-3 right-3 bg-purple-600 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                        Destacada
                                    </span>
                                @endif
                            </div>
                            <div class="p-5">
                                <h3
                                    class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-purple-600 transition">
                                    {{ $promo->titulo }}
                                </h3>
                                @if ($promo->subtitulo)
                                    <p class="text-gray-600 dark:text-gray-300 text-sm mt-1">{{ $promo->subtitulo }}</p>
                                @endif
                                <div class="mt-4 flex justify-end">
                                    <span class="text-purple-600 font-semibold group-hover:underline">
                                        {{ $promo->cta_texto }} →
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
@endsection
