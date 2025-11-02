{{-- resources/views/portal/conoce-mas.blade.php --}}
{{-- Espera variables (arrays) opcionales:
    $hero = [
      'titulo' => string,
      'subtitulo' => string,
      'cta_texto' => string,
      'cta_url' => string,            // ej: route('login')
      'fondo_url' => string|null,     // ej: asset('images/...')
      'overlay_opacity' => '0.35'     // string "0..1"
    ]

    $beneficios = [
      ['icon' => '💬', 'titulo' => '...', 'texto' => '...', 'url' => '#'],
      ...
    ]

    $comoFunciona = [
      ['icon' => '①', 'titulo' => 'Paso 1', 'texto' => '...', 'url' => null],
      ...
    ]

    $novedades = [
      ['titulo' => '...', 'extracto' => '...', 'url' => '#', 'imagen' => null, 'fecha' => '2025-10-31'],
      ...
    ]

    $testimonios = [
      ['nombre' => '...', 'cargo' => '...', 'texto' => '...', 'avatar' => null],
      ...
    ]

    $kpis = [
      ['valor' => '24/7', 'label' => 'Disponibilidad', 'nota' => null],
      ...
    ]

    $seguridad = [
      'titulo' => 'Seguridad y Privacidad',
      'items' => [
        ['titulo' => 'Cifrado', 'texto' => '...'],
        ...
      ],
      'bases_legales' => [
        ['titulo' => 'Base Legal 1', 'texto' => '...'],
        ...
      ]
    ]

    $branding = ['logo_url' => asset('favicon.ico')]

    $opciones = [
      'mostrar_beneficios'    => bool,
      'mostrar_como_funciona' => bool,
      'mostrar_novedades'     => bool,
      'mostrar_testimonios'   => bool,
      'mostrar_kpis'          => bool,
      'mostrar_seguridad'     => bool,
    ]
--}}

@php
  $overlay = $hero['overlay_opacity'] ?? '0.35';
  $heroFondo = $hero['fondo_url'] ?? null;
  $ctaTexto = $hero['cta_texto'] ?? null;
  $ctaUrl = $hero['cta_url'] ?? null;
@endphp

@extends('layouts.app')

@section('title', 'Conoce Mas')

@section('content')
  {{-- HERO --}}
  <section class="relative overflow-hidden">
    <div
      class="absolute inset-0 bg-center bg-cover"
      @if($heroFondo)
        style="background-image: url('{{ $heroFondo }}')"
      @endif
      aria-hidden="true"></div>
    <div class="absolute inset-0" style="background: rgba(0,0,0,{{ $overlay }});"></div>

    <div class="relative mx-auto max-w-7xl px-6 py-24 text-center text-white">
      <div class="flex justify-center mb-6">
        @if(!empty($branding['logo_url']))
          <img src="{{ $branding['logo_url'] }}" alt="Logo" class="h-12 w-auto opacity-90">
        @endif
      </div>

      <h1 class="text-3xl md:text-5xl font-bold tracking-tight">
        {{ $hero['titulo'] ?? 'Tu salud, más cerca' }}
      </h1>
      @if(!empty($hero['subtitulo']))
        <p class="mt-4 text-base md:text-lg opacity-90">
          {{ $hero['subtitulo'] }}
        </p>
      @endif

      @if($ctaTexto && $ctaUrl)
        <div class="mt-8">
          <a href="{{ $ctaUrl }}"
             class="inline-block rounded-xl bg-white/95 text-gray-900 px-6 py-3 font-semibold hover:bg-white">
            {{ $ctaTexto }}
          </a>
        </div>
      @endif
    </div>
  </section>

  {{-- BENEFICIOS --}}
  @if(($opciones['mostrar_beneficios'] ?? false) && !empty($beneficios))
    <section class="mx-auto max-w-7xl px-6 py-16">
      <h2 class="text-2xl md:text-3xl font-semibold text-gray-900 dark:text-gray-100">
        Beneficios
      </h2>
      <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($beneficios as $b)
          <div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-6 bg-white dark:bg-gray-900">
            <div class="text-3xl" aria-hidden="true">{{ $b['icon'] ?? '✨' }}</div>
            <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-gray-100">
              {{ $b['titulo'] ?? '' }}
            </h3>
            @if(!empty($b['texto']))
              <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ $b['texto'] }}</p>
            @endif
            @if(!empty($b['url']))
              <a href="{{ $b['url'] }}" class="mt-4 inline-block text-sm font-medium text-sky-700 dark:text-sky-400 hover:underline">
                Saber más →
              </a>
            @endif
          </div>
        @endforeach
      </div>
    </section>
  @endif

  {{-- CÓMO FUNCIONA --}}
  @if(($opciones['mostrar_como_funciona'] ?? false) && !empty($comoFunciona))
    <section class="mx-auto max-w-7xl px-6 py-16">
      <h2 class="text-2xl md:text-3xl font-semibold text-gray-900 dark:text-gray-100">
        ¿Cómo funciona?
      </h2>
      <ol class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($comoFunciona as $step)
          <li class="rounded-2xl border border-gray-200 dark:border-gray-800 p-6 bg-white dark:bg-gray-900">
            <div class="text-2xl" aria-hidden="true">{{ $step['icon'] ?? '•' }}</div>
            <h3 class="mt-3 text-lg font-semibold text-gray-900 dark:text-gray-100">
              {{ $step['titulo'] ?? '' }}
            </h3>
            @if(!empty($step['texto']))
              <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ $step['texto'] }}</p>
            @endif
            @if(!empty($step['url']))
              <a href="{{ $step['url'] }}" class="mt-4 inline-block text-sm font-medium text-sky-700 dark:text-sky-400 hover:underline">
                Ver detalle →
              </a>
            @endif
          </li>
        @endforeach
      </ol>
    </section>
  @endif

  {{-- KPIs --}}
  @if(($opciones['mostrar_kpis'] ?? false) && !empty($kpis))
    <section class="mx-auto max-w-7xl px-6 py-16">
      <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($kpis as $k)
          <div class="rounded-2xl bg-gradient-to-br from-slate-50 to-white dark:from-gray-900 dark:to-gray-950 border border-gray-200 dark:border-gray-800 p-6 text-center">
            <div class="text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-gray-100">
              {{ $k['valor'] ?? '—' }}
            </div>
            <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
              {{ $k['label'] ?? '' }}
            </div>
            @if(!empty($k['nota']))
              <div class="mt-1 text-xs text-gray-500 dark:text-gray-500">{{ $k['nota'] }}</div>
            @endif
          </div>
        @endforeach
      </div>
    </section>
  @endif

  {{-- NOVEDADES --}}
  @if(($opciones['mostrar_novedades'] ?? false) && !empty($novedades))
    <section class="mx-auto max-w-7xl px-6 py-16">
      <div class="flex items-baseline justify-between">
        <h2 class="text-2xl md:text-3xl font-semibold text-gray-900 dark:text-gray-100">Novedades</h2>
      </div>
      <div class="mt-8 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
        @foreach($novedades as $n)
          <article class="rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
            @if(!empty($n['imagen']))
              <img src="{{ $n['imagen'] }}" alt="" class="h-44 w-full object-cover">
            @endif
            <div class="p-6">
              @if(!empty($n['fecha']))
                <div class="text-xs text-gray-500 dark:text-gray-500">{{ \Illuminate\Support\Str::of($n['fecha'])->replace('-', '/') }}</div>
              @endif
              <h3 class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">
                {{ $n['titulo'] ?? '' }}
              </h3>
              @if(!empty($n['extracto']))
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ $n['extracto'] }}</p>
              @endif
              @if(!empty($n['url']))
                <a href="{{ $n['url'] }}" class="mt-4 inline-block text-sm font-medium text-sky-700 dark:text-sky-400 hover:underline">
                  Leer más →
                </a>
              @endif
            </div>
          </article>
        @endforeach
      </div>
    </section>
  @endif

  {{-- TESTIMONIOS --}}
  @if(($opciones['mostrar_testimonios'] ?? false) && !empty($testimonios))
    <section class="mx-auto max-w-7xl px-6 py-16">
      <h2 class="text-2xl md:text-3xl font-semibold text-gray-900 dark:text-gray-100">
        Testimonios
      </h2>
      <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @foreach($testimonios as $t)
          <figure class="rounded-2xl border border-gray-200 dark:border-gray-800 p-6 bg-white dark:bg-gray-900">
            <blockquote class="text-sm text-gray-700 dark:text-gray-300">
              “{{ $t['texto'] ?? '' }}”
            </blockquote>
            <figcaption class="mt-4 flex items-center gap-3">
              @if(!empty($t['avatar']))
                <img src="{{ $t['avatar'] }}" class="h-10 w-10 rounded-full object-cover" alt="">
              @else
                <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700"></div>
              @endif
              <div>
                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $t['nombre'] ?? 'Paciente' }}</div>
                @if(!empty($t['cargo']))
                  <div class="text-xs text-gray-500 dark:text-gray-500">{{ $t['cargo'] }}</div>
                @endif
              </div>
            </figcaption>
          </figure>
        @endforeach
      </div>
    </section>
  @endif

  {{-- SEGURIDAD / BASES LEGALES --}}
  @if(($opciones['mostrar_seguridad'] ?? false) && !empty($seguridad))
    <section class="mx-auto max-w-7xl px-6 py-16">
      <h2 class="text-2xl md:text-3xl font-semibold text-gray-900 dark:text-gray-100">
        {{ $seguridad['titulo'] ?? 'Seguridad y Bases Legales' }}
      </h2>

      {{-- Puntos de seguridad --}}
      @if(!empty($seguridad['items']) && is_array($seguridad['items']))
        <div class="mt-8 grid gap-6 md:grid-cols-2">
          @foreach($seguridad['items'] as $s)
            <div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-6 bg-white dark:bg-gray-900">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $s['titulo'] ?? '' }}</h3>
              @if(!empty($s['texto']))
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ $s['texto'] }}</p>
              @endif
            </div>
          @endforeach
        </div>
      @endif

      {{-- Bases legales --}}
      @if(!empty($seguridad['bases_legales']) && is_array($seguridad['bases_legales']))
        <div class="mt-12">
          <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Bases legales</h3>
          <div class="mt-6 space-y-4">
            @foreach($seguridad['bases_legales'] as $b)
              <div class="rounded-xl bg-slate-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 p-5">
                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $b['titulo'] ?? 'Base legal' }}</div>
                @if(!empty($b['texto']))
                  <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $b['texto'] }}</p>
                @endif
              </div>
            @endforeach
          </div>
        </div>
      @endif
    </section>
  @endif

  {{-- CTA Final opcional (reutiliza hero si viene) --}}
  @if($ctaTexto && $ctaUrl)
    <section class="mx-auto max-w-7xl px-6 pb-20">
      <div class="rounded-2xl bg-gradient-to-br from-sky-50 to-white dark:from-gray-900 dark:to-gray-950 border border-gray-200 dark:border-gray-800 p-8 text-center">
        <h3 class="text-xl md:text-2xl font-semibold text-gray-900 dark:text-gray-100">
          {{ $hero['subtitulo'] ?? 'Ingresa para acceder a tus resultados y más.' }}
        </h3>
        <a href="{{ $ctaUrl }}"
           class="mt-6 inline-block rounded-xl bg-sky-600 text-white px-6 py-3 font-semibold hover:bg-sky-700">
          {{ $ctaTexto }}
        </a>
      </div>
    </section>
  @endif
@endsection
