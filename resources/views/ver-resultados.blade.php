{{-- resources/views/ver-resultados.blade.php --}}
@extends('layouts.app', ['topbar' => true, 'navbar' => true])

@section('title', 'Mis resultados')

@section('content')
@php
  use Illuminate\Support\Str;

  $paciente = $paciente ?? [
    'nombre' => 'Paciente',
    'rut' => null,
    'sexo' => null,
    'edad' => null,
    'idioma' => 'es',
    'cronico' => false,
    'condiciones' => [],
  ];

  /** @var \Illuminate\Support\Collection|array $grupos
   *  Estructura esperada:
   *  [
   *    ['code' => 'RX', 'label' => 'Radiografía', 'items' => collect([
   *        ['id'=>..., 'titulo'=>..., 'fecha'=>..., 'codigo'=>..., 'estado'=>..., 'pdf'=>..., 'lugar'=>..., 'profesional'=>...]
   *    ])],
   *    ...
   *  ]
   */
  $grupos = collect($grupos ?? []);
@endphp

<div
  x-data="{ modalPdf:false, modalDetalle:false, pdfUrl:'', detalle:{} }"
  class="container mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-6"
>
  {{-- Header paciente (lo mantengo igual que venías usando) --}}
  <x-portal.panel-header :paciente="$paciente" onOrganizar="{{ route('portal.home') }}#organizar" />

  {{-- Título principal --}}
  <div class="mt-6 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
    <div class="flex items-center justify-between">
      <h2 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100">
        Mis resultados
      </h2>
        <x-ui.back-button :href="route('portal.home')" label="Volver" variant="outline" size="sm" class="mr-4" />

    </div>
  </div>

  {{-- ================= SECCIONES POR ESPECIALIDAD ================= --}}
  @forelse($grupos as $grupo)
    @php
      $items  = collect($grupo['items'] ?? []);
      $code   = $grupo['code']  ?? 'OTRO';
      $label  = $grupo['label'] ?? 'Otro';
      $pages  = $items->chunk(4);
      $totalP = $pages->count();
      $uid    = 'grp_'.$code; // id único por grupo
    @endphp

    <div x-data="{ page: 0, total: {{ $totalP }}, id: '{{ $uid }}' }" class="mt-6">
      {{-- Encabezado de la especialidad + controles --}}
      <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-3">
          <h3 class="text-sm sm:text-base font-semibold text-gray-900 dark:text-gray-100">
            {{ $label }}
          </h3>
          <a
            href="{{ route('portal.resultados.especialidad', $code) }}"
            class="text-[11px] sm:text-xs inline-flex items-center rounded-lg border border-purple-900/20 dark:border-purple-300/20
                   px-2 py-1 text-purple-900 dark:text-purple-100 hover:bg-purple-900 hover:text-white transition"
            title="Ver todos en {{ $label }}"
          >
            Ver todos
          </a>
        </div>

        @if($totalP > 1)
          <div class="flex items-center gap-2">
            <button type="button"
                    class="rounded-lg px-3 py-1.5 text-xs border border-gray-200 dark:border-gray-700
                           text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-950
                           disabled:opacity-40"
                    :disabled="page === 0"
                    @click="page = Math.max(0, page - 1)">
              Previous
            </button>
            <span class="text-xs text-gray-600 dark:text-gray-300"
                  x-text="(page+1) + ' / ' + total"></span>
            <button type="button"
                    class="rounded-lg px-3 py-1.5 text-xs border border-gray-200 dark:border-gray-700
                           text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-950
                           disabled:opacity-40"
                    :disabled="page >= total - 1"
                    @click="page = Math.min(total - 1, page + 1)">
              Next
            </button>
          </div>
        @endif
      </div>

      {{-- Grilla (paginada por 4 ítems) --}}
      @foreach($pages as $i => $chunk)
        <div x-show="page === {{ $i }}" x-cloak class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          @foreach($chunk as $it)
            @php
              // Armamos el payload de detalle para el modal
              $detalle = [
                'titulo'      => $it['titulo'] ?? $label,
                'especialidad'=> $label,
                'fecha'       => $it['fecha'] ?? '—',
                'codigo'      => $it['codigo'] ?? '—',
                'estado'      => $it['estado'] ?? '—',
                'resumen'     => $it['resumen'] ?? '—',
                'lugar'       => $it['lugar'] ?? '—',
                'profesional' => $it['profesional'] ?? '—',
                'pdf'         => $it['pdf'] ?? null,
              ];
              $isDisponible = ($it['estado'] ?? null) === 'DISPONIBLE';
            @endphp

            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <div class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                    {{ $it['titulo'] ?? $label }}
                  </div>
                  <div class="mt-1 text-xs text-gray-600 dark:text-gray-300">
                    {{ $it['fecha'] ?? '—' }}
                  </div>
                </div>

                <span class="rounded-lg {{ $isDisponible ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-100' : 'bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-200' }} px-2 py-0.5 text-[11px] shrink-0">
                  {{ $isDisponible ? 'Disponible' : 'Pendiente' }}
                </span>
              </div>

      @if(!empty($it['viewer_url']))
        <div class="mt-4 flex items-center gap-3">
          <div class="h-12 w-12 rounded-lg bg-purple-100 dark:bg-purple-900/30 grid place-items-center shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-purple-900 dark:text-purple-200" viewBox="0 0 24 24" fill="currentColor">
              <path d="M21 19V5a2 2 0 0 0-2-2H5C3.89 3 3 3.9 3 5v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2zM7 17l3-4 2 3 3-4 2 5H7z"/>
            </svg>
          </div>
          <a href="{{ $it['viewer_url'] }}" target="_blank" rel="noopener"
             class="text-sm font-medium text-teal-700 dark:text-teal-300 hover:underline inline-flex items-center gap-1">
            Ver resultado
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 opacity-70" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M14 3h7v7h-2V6.41l-9.29 9.3-1.42-1.42 9.3-9.29H14V3z"/>
              <path d="M5 5h5V3H5a2 2 0 0 0-2 2v14c0 1.11.89 2 2 2h14a2 2 0 0 0 2-2v-5h-2v5H5V5z"/>
            </svg>
          </a>
        </div>
      @endif

              <div class="mt-4 flex items-center gap-2">
                @if(!empty($it['pdf']))
                  <button type="button"
                          @click="modalPdf=true; pdfUrl='{{ $it['pdf'] }}'"
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
              </div>
            </div>
          @endforeach
        </div>
      @endforeach
    </div>
  @empty
    <div class="mt-6 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 p-6 text-center text-sm text-gray-600 dark:text-gray-300">
      No hay resultados disponibles.
    </div>
  @endforelse
  {{-- ================= /SECCIONES POR ESPECIALIDAD ================= --}}

  {{-- ================= MODALES ================= --}}
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
          <div>
            <dt class="text-gray-500 dark:text-gray-400">Especialidad</dt>
            <dd class="font-medium text-gray-900 dark:text-gray-100" x-text="detalle.especialidad"></dd>
          </div>
          <div>
            <dt class="text-gray-500 dark:text-gray-400">Fecha</dt>
            <dd class="font-medium text-gray-900 dark:text-gray-100" x-text="detalle.fecha"></dd>
          </div>
          <div>
            <dt class="text-gray-500 dark:text-gray-400">Código</dt>
            <dd class="font-medium text-gray-900 dark:text-gray-100" x-text="detalle.codigo"></dd>
          </div>
          <div>
            <dt class="text-gray-500 dark:text-gray-400">Estado</dt>
            <dd class="font-medium text-gray-900 dark:text-gray-100" x-text="detalle.estado"></dd>
          </div>
          <div>
            <dt class="text-gray-500 dark:text-gray-400">Lugar</dt>
            <dd class="font-medium text-gray-900 dark:text-gray-100" x-text="detalle.lugar"></dd>
          </div>
          <div>
            <dt class="text-gray-500 dark:text-gray-400">Profesional</dt>
            <dd class="font-medium text-gray-900 dark:text-gray-100" x-text="detalle.profesional"></dd>
          </div>
          <div class="sm:col-span-2">
            <dt class="text-gray-500 dark:text-gray-400">Resumen</dt>
            <dd class="mt-1 whitespace-pre-wrap text-gray-800 dark:text-gray-200" x-text="detalle.resumen"></dd>
          </div>
        </dl>

        <div class="mt-5 flex items-center justify-end gap-2">
          <template x-if="detalle.pdf">
            <button type="button"
                    @click="modalPdf=true; pdfUrl=detalle.pdf"
                    class="inline-flex items-center rounded-xl bg-purple-900 text-white px-4 py-2 text-sm font-semibold hover:opacity-90">
              Abrir PDF
            </button>
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
  {{-- ================= /MODALES ================= --}}
</div>
@endsection
