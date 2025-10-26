@extends('layouts.app', ['topbar' => true, 'navbar' => true])

@section('title', $noticia['titulo'] ?? 'Noticia')

@section('content')
  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    {{-- Imagen principal --}}
    @if(!empty($noticia['imagen']))
      <div class="mb-6">
        <img src="{{ $noticia['imagen'] }}" alt="{{ $noticia['titulo'] }}"
             class="rounded-2xl shadow-md w-full object-cover">
      </div>
    @endif

    {{-- Título --}}
    <h1 class="text-3xl font-bold text-purple-900 dark:text-gray-100 mb-3">
      {{ $noticia['titulo'] }}
    </h1>

    {{-- Bajada o descripción corta --}}
    <p class="text-gray-700 dark:text-gray-300 text-lg mb-6">
      {{ $noticia['bajada'] }}
    </p>

    {{-- Contenido ampliado (simulado por ahora) --}}
    <div class="prose dark:prose-invert max-w-none text-justify leading-relaxed">
      <p>
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi tempor, risus eget tincidunt volutpat,
        felis nisl porta elit, a volutpat justo libero a erat. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae.
      </p>
      <p>
        Suspendisse potenti. Nulla facilisi. In feugiat metus in mi pretium, nec luctus sem ultrices. Aenean tincidunt velit at velit faucibus, nec elementum est fermentum.
      </p>
    </div>

    {{-- Botón volver --}}
    <div class="mt-10">
      <a href="{{ route('portal.home') }}"
         class="inline-flex items-center rounded-xl border border-purple-900/20 bg-purple-900 text-white
                hover:bg-purple-800 px-5 py-2 font-semibold text-sm transition">
        ← Volver al Home
      </a>
    </div>
  </div>
@endsection
