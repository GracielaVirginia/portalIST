{{-- resources/views/ver-resultados-especialidad.blade.php --}}
@extends('layouts.app', ['topbar' => true, 'navbar' => true])

@section('title', 'Resultados por especialidad')

@section('content')
@php
  use Illuminate\Support\Str;

  // Fallbacks por si el controlador aún no pasa todos los datos
  $paciente         = $paciente         ?? ['nombre' => 'Paciente', 'rut' => null, 'sexo' => null, 'edad' => null, 'idioma' => 'es', 'cronico' => false, 'condiciones' => []];
  $kpis             = $kpis             ?? ['proximas_citas' => 0, 'resultados_disponibles' => 0, 'ordenes' => 0, 'alertas' => 0];
  $sidebar          = $sidebar          ?? ['resultados' => ['total' => 0, 'por_especialidad' => []]];
  $itemsRecientes   = $itemsRecientes   ?? [];   // para modal "Resultados recientes"
  $seriesControles  = $seriesControles  ?? ['tension'=>[], 'glucosa'=>[], 'peso'=>[]]; // para modal calendario
  $sugerencias      = $sugerencias      ?? [];   // para modal sugerencias

  $gestiones        = $gestiones        ?? collect();
  $esp              = strtoupper($esp ?? '');

  $labelEsp = function($raw) {
      $u = Str::upper(Str::ascii((string)$raw));
      return Str::contains($u,'RADIO')||Str::contains($u,'RX') ? 'Radiografía' :
             (Str::contains($u,'ECOG')||Str::contains($u,'ECO') ? 'Ecografía' :
             (Str::contains($u,'LAB') ? 'Laboratorio' :
             (Str::contains($u,'ENDO') ? 'Endocrinología' :
             (Str::contains($u,'INTERNA')||Str::contains($u,'MED') ? 'Medicina Interna' : ($raw ?: 'Otro')))));
  };
  $titulo = $labelEsp($esp);
@endphp

<div
  x-data="{ modalResultados:false, modalSalud:false, modalPdf:false, modalDetalle:false, pdfUrl:'', detalle:{} }"
  class="container mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-6"
>
  {{-- Header paciente --}}
  <x-portal.panel-header :paciente="$paciente" onOrganizar="{{ route('portal.home') }}#organizar" />

  {{-- KPIs --}}
  <div class="mt-6">
    <x-portal.kpis :kpis="$kpis" />
  </div>

  {{-- Layout con sidebar + contenido --}}
  <div class="mt-6 grid grid-cols-1 lg:grid-cols-12 gap-4">
    {{-- Sidebar (izquierda) con CTAs moradas --}}
    <div class="lg:col-span-3 space-y-3">
      <x-portal.sidebar :resultados="$sidebar['resultados']" />

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
      </div> --}}

      {{-- <div class="rounded-2xl bg-purple-100 dark:bg-purple-900/30 border border-purple-200/60 dark:border-purple-800/50 p-3">
        <button type="button"
                @click="modalSalud = true"
                class="w-full text-left group cursor-pointer rounded-xl px-3 py-2
                       text-sm font-semibold text-purple-900 dark:text-purple-100
                       hover:bg-purple-900 hover:text-white transition inline-flex items-center justify-between">
          <span class="inline-flex items-center gap-2">
            <span class="h-2 w-2 rounded-full bg-purple-700 group-hover:bg-white"></span>
            Ver calendario y sugerencias
          </span>
          <span class="rounded-lg bg-purple-900 text-white text-[11px] px-2 py-0.5">Abrir</span>
        </button>
      </div> --}}
    </div>

    {{-- Contenido principal (derecha): AQUÍ INYECTAMOS EN LUGAR DE LA NOTICIA --}}
    <div class="lg:col-span-9 space-y-4">
      {{-- Título de la sección --}}
      <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
        <div class="flex items-center justify-between">
          <h2 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100">
            Resultados — {{ $titulo }}
          </h2>
        <x-ui.back-button :href="route('portal.home')" label="Volver" variant="outline" size="sm" class="mr-4" />

        </div>
      </div>

      {{-- Grilla de resultados --}}
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-4">
        @forelse($gestiones as $g)
          @php
            // URL PDF (public/informes o absoluta)
            $pdf = $g->url_pdf_informe;
            $pdfUrl = $pdf
              ? (Str::startsWith($pdf, ['http://','https://','/']) ? $pdf : asset('informes/'.$pdf))
              : null;

            $detalle = [
              'titulo'        => $g->examen_nombre ?: $titulo,
              'especialidad'  => $titulo,
              'fecha'         => optional($g->fecha_atencion)->format('Y-m-d H:i') ?: optional($g->created_at)->format('Y-m-d H:i'),
              'codigo'        => $g->examen_codigo ?: '—',
              'estado'        => $g->tiene_informe ? 'DISPONIBLE' : ($g->estado_solicitud ?: '—'),
              'resumen'       => $g->resumen_atencion ?: ($g->impresion_diagnostica ?: '—'),
              'lugar'         => $g->lugar_cita ?: '—',
              'profesional'   => $g->id_profesional ?: '—',
              'pdf'           => $pdfUrl,
            ];
          @endphp

          <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
            <div class="flex items-start justify-between gap-3">
              <div class="min-w-0">
                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                  {{ $g->examen_nombre ?: $titulo }}
                </div>
                <div class="mt-1 text-xs text-gray-600 dark:text-gray-300">
                  {{ optional($g->fecha_atencion)->format('Y-m-d H:i') ?: optional($g->created_at)->format('Y-m-d H:i') }}
                </div>
              </div>

              <span class="rounded-lg {{ $g->tiene_informe ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-100' : 'bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-200' }} px-2 py-0.5 text-[11px] shrink-0">
                {{ $g->tiene_informe ? 'Disponible' : 'Pendiente' }}
              </span>
            </div>

{{-- Solo mostrar el link si viene viewer_url (RX/ECO) --}}
@if(!empty($g->viewer_url))
  <div class="mt-4 flex items-center gap-3">
    <div class="h-12 w-12 rounded-lg bg-purple-100 dark:bg-purple-900/30 grid place-items-center shrink-0">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-purple-900 dark:text-purple-200" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
        <path d="M21 19V5a2 2 0 0 0-2-2H5C3.89 3 3 3.9 3 5v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2zM7 17l3-4 2 3 3-4 2 5H7z"/>
      </svg>
    </div>

    {{-- Link limpio, texto al lado del icono, misma ubicación --}}
    <a href="{{ $g->viewer_url }}" target="_blank" rel="noopener"
       class="text-sm font-medium text-teal-700 dark:text-teal-300 hover:underline inline-flex items-center gap-1">
      Ver mis resultados
      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 opacity-70" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
        <path d="M14 3h7v7h-2V6.41l-9.29 9.3-1.42-1.42 9.3-9.29H14V3z"/><path d="M5 5h5V3H5a2 2 0 0 0-2 2v14c0 1.11.89 2 2 2h14a2 2 0 0 0 2-2v-5h-2v5H5V5z"/>
      </svg>
    </a>
  </div>
@endif


            <div class="mt-4 flex items-center gap-2">
              @if($pdfUrl)
                <button type="button"
                        @click="modalPdf=true; pdfUrl='{{ $pdfUrl }}'"
                        class="inline-flex items-center gap-2 rounded-xl bg-purple-900 text-white px-3 py-1.5 text-xs font-semibold hover:opacity-90">
                  Ver PDF
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M5 20h14v-2H5v2zM11 4h2v8h3l-4 4-4-4h3V4z"/>
                  </svg>
                </button>
              @endif

              <button type="button"
                      @click="modalDetalle=true; detalle=@js($detalle)"
                      class="inline-flex items-center rounded-xl border border-gray-200 dark:border-gray-700
                             bg-white dark:bg-gray-950 px-3 py-1.5 text-xs text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-900">
                Ver detalle
              </button>

              <a href="{{ route('portal.resultados.show', $g->id) }}"
                 class="ml-auto inline-flex items-center rounded-xl border border-gray-200 dark:border-gray-700
                        bg-white dark:bg-gray-950 px-3 py-1.5 text-xs text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-900">
                Abrir vista
              </a>
            </div>
          </div>
        @empty
          <div class="col-span-full rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 p-6 text-center text-sm text-gray-600 dark:text-gray-300">
            No hay resultados en {{ $titulo }}.
          </div>
        @endforelse
      </div>
    </div>
  </div>

  {{-- ================= MODALES REUTILIZADOS ================= --}}

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
      <div class="p-4 grid grid-cols-1 xl:grid-cols-2 gap-4">
        <x-portal.widget-calendario-salud :series="$seriesControles" :store-url="route('portal.controles.store')" />
        <x-portal.widget-sugerencias-citas :sugerencias="$sugerencias" />
      </div>
    </div>
  </div>

  {{-- Modal: PDF --}}
  <div x-show="modalPdf" x-cloak
       class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
       @keydown.escape.window="modalPdf=false">
    <div class="w-full max-w-6xl h-[80vh] rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-xl overflow-hidden">
      <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Informe</h3>
        <button class="rounded-lg px-2 py-1 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                @click="modalPdf=false">Cerrar</button>
      </div>
      <div class="h-full">
        <iframe :src="pdfUrl" class="w-full h-[calc(80vh-48px)]" frameborder="0"></iframe>
      </div>
    </div>
  </div>

  {{-- Modal: Detalle --}}
  <div x-show="modalDetalle" x-cloak
       class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
       @keydown.escape.window="modalDetalle=false">
    <div class="w-full max-w-3xl rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-xl overflow-hidden">
      <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100" x-text="detalle.titulo || 'Detalle'"></h3>
        <button class="rounded-lg px-2 py-1 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                @click="modalDetalle=false">Cerrar</button>
      </div>
      <div class="p-5">
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
          <div><dt class="text-gray-500 dark:text-gray-400">Especialidad</dt><dd class="font-medium text-gray-900 dark:text-gray-100" x-text="detalle.especialidad"></dd></div>
          <div><dt class="text-gray-500 dark:text-gray-400">Fecha</dt><dd class="font-medium text-gray-900 dark:text-gray-100" x-text="detalle.fecha"></dd></div>
          <div><dt class="text-gray-500 dark:text-gray-400">Código</dt><dd class="font-medium text-gray-900 dark:text-gray-100" x-text="detalle.codigo"></dd></div>
          <div><dt class="text-gray-500 dark:text-gray-400">Estado</dt><dd class="font-medium text-gray-900 dark:text-gray-100" x-text="detalle.estado"></dd></div>
          <div><dt class="text-gray-500 dark:text-gray-400">Lugar</dt><dd class="font-medium text-gray-900 dark:text-gray-100" x-text="detalle.lugar"></dd></div>
          <div><dt class="text-gray-500 dark:text-gray-400">Profesional</dt><dd class="font-medium text-gray-900 dark:text-gray-100" x-text="detalle.profesional"></dd></div>
          <div class="sm:col-span-2">
            <dt class="text-gray-500 dark:text-gray-400">Resumen</dt>
            <dd class="mt-1 whitespace-pre-wrap text-gray-800 dark:text-gray-200" x-text="detalle.resumen"></dd>
          </div>
        </dl>

        <div class="mt-5 flex items-center justify-end gap-2">
          <template x-if="detalle.pdf">
            <a :href="detalle.pdf" target="_blank" rel="noopener"
               class="inline-flex items-center rounded-xl bg-purple-900 text-white px-4 py-2 text-sm font-semibold hover:opacity-90">
              Abrir PDF
            </a>
          </template>
          <button type="button"
                  @click="modalDetalle=false"
                  class="inline-flex items-center rounded-xl border border-gray-200 dark:border-gray-700
                         bg-white dark:bg-gray-950 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-900">
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>
  {{-- =============== /MODALES =============== --}}
</div>
@endsection
