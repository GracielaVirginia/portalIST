{{-- resources/views/portal/home.blade.php --}}
@extends('layouts.app', ['topbar' => true, 'navbar' => true])

@section('title', 'Portal de Salud — Inicio')

@section('content')
  @php
    // Fallbacks por si el controlador aún trae placeholders
    $paciente         = $paciente         ?? ['nombre' => 'Paciente', 'rut' => null, 'sexo' => null, 'edad' => null, 'idioma' => 'es', 'cronico' => false, 'condiciones' => []];
    $kpis             = $kpis             ?? ['proximas_citas' => 0, 'resultados_disponibles' => 0, 'ordenes' => 0, 'alertas' => 0];
    $sidebar          = $sidebar          ?? ['resultados' => ['total' => 0, 'por_especialidad' => []]];
    $itemsRecientes   = $itemsRecientes   ?? [];   // para modal de "Resultados recientes"
    $seriesControles  = $seriesControles  ?? ['tension'=>[], 'glucosa'=>[], 'peso'=>[]]; // para modal de calendario
    $sugerencias      = $sugerencias      ?? [];   // para modal de sugerencias
    $noticia          = $noticia          ?? null; // si es null, el componente usa su default
  @endphp

  <div
    x-data="{ modalResultados:false, modalSalud:false }"
    class="container mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-6 overflow-x-hidden"
  >
@if (Auth::check() && Auth::user()->password_needs_change)
    <x-security.force-password-modal />
@endif
{{-- Popup de valoración --}}
{{-- @include('components.review-popup') --}}
@include('components.calificacion')

{{-- Header paciente --}}

    <x-portal.panel-header :paciente="$paciente" onOrganizar="{{ route('portal.home') }}#organizar" />


    {{-- KPIs (informativos) --}}
    <div class="mt-6">
      <x-portal.kpis :kpis="$kpis" />
    </div>

    {{-- Layout con sidebar + contenido --}}
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-12 gap-4">
      {{-- Sidebar (izquierda) --}}
      <div class="lg:col-span-3 space-y-3">
        <x-portal.sidebar :resultados="$sidebar['resultados']" />

        {{-- CTAs morados (abren modales) --}}
        {{-- <div class="rounded-2xl bg-purple-100 dark:bg-purple-900/30 border border-purple-200/60 dark:border-purple-800/50 p-3">
          <button type="button"
                  @click="modalResultados = true"
                  class="w-full text-left group cursor-pointer rounded-xl px-3 py-2
                         text-sm font-semibold text-purple-900 dark:text-purple-100
                         hover:bg-purple-900 hover:text-white transition inline-flex items-center justify-between">
            <span class="inline-flex items-center gap-2">
              <span class="h-2 w-2 rounded-full bg-purple-700 group-hover:bg-white"></span>
              Ver resultados recientes
            </span>
            <span class="rounded-lg bg-purple-900 text-white text-[11px] px-2 py-0.5">Abrir</span>
          </button>
        </div>--}}

        {{-- <div class="rounded-2xl bg-purple-100 dark:bg-purple-900/30 border border-purple-200/60 dark:border-purple-800/50 p-3">
          <button type="button"
                  @click="modalSalud = true"
                  class="w-full text-left group cursor-pointer rounded-xl px-3 py-2
                         text-sm font-semibold text-purple-900 dark:text-purple-100
                         hover:bg-purple-900 hover:text-white transition inline-flex items-center justify-between">
            <span class="inline-flex items-center gap-2">
              <span class="h-2 w-2 rounded-full bg-purple-700 group-hover:bg-white"></span>
              Mis Controles (Glucosa-Tensión-Peso)
            </span>
            <span class="rounded-lg bg-purple-900 text-white text-[11px] px-2 py-0.5">Abrir</span>
          </button>
        </div>  --}}
      </div>

      {{-- Contenido principal (derecha) --}}
      <div class="lg:col-span-9 space-y-4">
        {{-- Noticia destacada (único widget visible en la página) --}}
        @if($noticia)
          <x-portal.widget-noticia :noticia="$noticia" />
        @else
          <x-portal.widget-noticia />
        @endif

        {{-- IMPORTANTE: ocultamos del flujo normal los otros widgets para evitar scroll --}}
        {{-- <x-portal.widget-resultados-recientes :items="$itemsRecientes" /> --}}
        {{-- <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">--}}
             {{-- <x-portal.widget-calendario-salud :series="$seriesControles" :store-url="route('portal.controles.store')" /> --}}
             {{-- <x-portal.widget-sugerencias-citas :sugerencias="$sugerencias" /> --}}
           </div> 
      </div>
    </div>

    {{-- ================= MODALES ================= --}}
    {{-- Modal: Resultados recientes --}}
    <div x-show="modalResultados" x-cloak
         class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4"
         @keydown.escape.window="modalResultados=false">
      <div class="w-full max-w-4xl rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-xl overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
          <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Resultados recientes</h3>
          <button class="rounded-lg px-2 py-1 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                  @click="modalResultados=false">Cerrar</button>
        </div>
        <div class="p-4">
          <x-portal.widget-resultados-recientes :items="$itemsRecientes" />
          <div class="mt-4 text-right">
            <a href="{{ route('portal.resultados.index') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-purple-900/20 dark:border-purple-300/20
                      bg-purple-900 text-white hover:opacity-90 px-4 py-2 text-sm font-semibold">
              Ver todos
            </a>
          </div>
        </div>
      </div>
    </div>

    {{-- Modal: Calendario + Sugerencias --}}
    <div x-show="modalSalud" x-cloak
         class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4"
         @keydown.escape.window="modalSalud=false">
      <div class="w-full max-w-5xl rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-xl overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
          <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Calendario y sugerencias</h3>
          <button class="rounded-lg px-2 py-1 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                  @click="modalSalud=false">Cerrar</button>
        </div>
        {{-- <div class="p-4 grid grid-cols-1 xl:grid-cols-2 gap-4">
          <x-portal.widget-calendario-salud :series="$seriesControles" :store-url="route('portal.controles.store')" />
          <x-portal.widget-sugerencias-citas :sugerencias="$sugerencias" />
        </div> 
      </div>
    </div>
    {{-- =============== /MODALES =============== --}}
    {{-- ... tu contenido de portal.home ... --}}


  </div>

@endsection
