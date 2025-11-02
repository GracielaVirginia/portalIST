@extends('layouts.app', ['topbar' => true, 'navbar' => true])

@section('title', $noticia['titulo'] ?? 'Noticia')

@section('content')
  {{-- 
    Notas:
    - pb-[88px]: reserva espacio para el footer fijo (ajusta según su altura real).
    - min-h-[80vh]: asegura algo de alto para que exista scroll si el contenido es corto.
  --}}
  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-[88px] min-h-[80vh]">
    
    {{-- Imagen principal (más pequeña y contenida) --}}
    @if(!empty($noticia['imagen']))
      <figure class="mb-4">
        <div class="w-full overflow-hidden rounded-xl shadow-md">
          {{-- Altura reducida y responsive; object-cover mantiene el recorte limpio --}}
          <img
            src="{{ $noticia['imagen'] }}"
            alt="{{ $noticia['titulo'] }}"
            loading="lazy"
            class="w-full h-48 md:h-56 lg:h-64 object-cover object-center"
          >
        </div>
      </figure>
    @endif

    {{-- Título --}}
    <h1 class="text-2xl md:text-3xl font-bold text-purple-900 dark:text-gray-100 mb-2">
      {{ $noticia['titulo'] }}
    </h1>

    {{-- Bajada / descripción corta --}}
    <p class="text-gray-700 dark:text-gray-300 text-base md:text-lg mb-5">
      {{ $noticia['bajada'] }}
    </p>

    {{-- Contenido ampliado (simulado) --}}
    <div class="prose dark:prose-invert max-w-none text-justify leading-relaxed">
      <p>
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi tempor, risus eget tincidunt volutpat,
        felis nisl porta elit, a volutpat justo libero a erat. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae.
      </p>
      <p>
        Suspendisse potenti. Nulla facilisi. In feugiat metus in mi pretium, nec luctus sem ultrices. Aenean tincidunt velit at velit faucibus, nec elementum est fermentum.
      </p>
    </div>

    {{-- Botón volver (más arriba) --}}
    <div class="mt-6">
      <a href="{{ route('portal.home') }}"
         class="relative z-10 inline-flex items-center rounded-xl border border-purple-900/20 bg-purple-900 text-white
                hover:bg-purple-800 px-5 py-2 font-semibold text-sm transition">
        ← Volver al Home
      </a>
    </div>

    {{-- Espaciador opcional extra por si el footer tiene sombras/z-index alto --}}
    <div aria-hidden="true" class="h-6"></div>
  </div>
@endsection
