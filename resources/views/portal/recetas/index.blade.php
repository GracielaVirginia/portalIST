{{-- resources/views/recetas/index.blade.php --}}
@extends('layouts.app', ['topbar' => true, 'navbar' => true])

@section('title', 'Mis recetas')

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

  /** Estructura esperada en $grupos (igual a ver-resultados/licencias):
   *  [
   *    [
   *      'code'  => 'MED',      // clave/normalizada del grupo
   *      'label' => 'Recetas',  // etiqueta visible
   *      'items' => collect([
   *          [
   *            'id'         => 1,
   *            'titulo'     => 'Receta médica Nº 001',
   *            'fecha'      => '2025-10-22 09:30',
   *            'codigo'     => 'RC-001',
   *            'estado'     => 'DISPONIBLE' | 'PENDIENTE',
   *            'resumen'    => 'Tratamiento indicado...',
   *            'lugar'      => 'Clínica Central',
   *            'profesional'=> 'Dr. Juan Pérez',
   *          ],
   *      ])
   *    ],
   *    ...
   *  ]
   */
  $grupos = collect($grupos ?? []);
@endphp

<div
  x-data="{ modalPdf:false, modalDetalle:false, pdfUrl:'', detalle:{} }"
  class="container mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-6"
>
  {{-- Header paciente --}}
  <x-portal.panel-header :paciente="$paciente" onOrganizar="{{ route('portal.home') }}#organizar" />

  {{-- Título principal --}}
  <div class="mt-6 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
    <div class="flex items-center justify-between">
      <h2 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100">
        Mis recetas
      </h2>
      <a href="{{ route('portal.home') }}"
         class="inline-flex items-center rounded-xl border border-gray-200 dark:border-gray-700 px-3 py-1.5 text-sm
                text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-900">
        Volver
      </a>
    </div>
  </div>

  {{-- ================= SECCIONES POR GRUPO ================= --}}
  @forelse($grupos as $grupo)
    @php
      $items  = collect($grupo['items'] ?? []);
      $code   = $grupo['code']  ?? 'OTRO';
      $label  = $grupo['label'] ?? 'Recetas';
      $pages  = $items->chunk(4);
      $totalP = $pages->count();
      $uid    = 'grp_'.$code;
    @endphp

    <div x-data="{ page: 0, total: {{ $totalP }}, id: '{{ $uid }}' }" class="mt-6">
      {{-- Encabezado --}}
      <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-3">
          <h3 class="text-sm sm:text-base font-semibold text-gray-900 dark:text-gray-100">
            {{ $label }}
          </h3>
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

      {{-- Grilla (4 ítems por página) --}}
      @foreach($pages as $i => $chunk)
        <div x-show="page === {{ $i }}" x-cloak class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          @foreach($chunk as $it)
            @php
              $isDisponible = ($it['estado'] ?? null) === 'DISPONIBLE';
              // ✅ PDF estático en /public/recetas/receta-prueba.pdf
              $recetaUrl = asset('recetas/receta-prueba.pdf');
              $detalle = [
                'titulo'      => $it['titulo'] ?? 'Receta',
                'especialidad'=> $label,
                'fecha'       => $it['fecha'] ?? '—',
                'codigo'      => $it['codigo'] ?? '—',
                'estado'      => $it['estado'] ?? '—',
                'resumen'     => $it['resumen'] ?? '—',
                'lugar'       => $it['lugar'] ?? '—',
                'profesional' => $it['profesional'] ?? '—',
                'pdf'         => $recetaUrl,
              ];
            @endphp

            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <div class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                    {{ $it['titulo'] ?? 'Receta' }}
                  </div>
                  <div class="mt-1 text-xs text-gray-600 dark:text-gray-300">
                    {{ $it['fecha'] ?? '—' }}
                  </div>
                </div>

                <span class="rounded-lg {{ $isDisponible ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-100' : 'bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-200' }} px-2 py-0.5 text-[11px] shrink-0">
                  {{ $isDisponible ? 'Disponible' : 'Pendiente' }}
                </span>
              </div>

              <div class="mt-4 flex items-center gap-3">
                <div class="h-12 w-12 rounded-lg bg-purple-100 dark:bg-purple-900/30 grid place-items-center shrink-0">
                  {{-- Ícono receta --}}
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-purple-900 dark:text-purple-200" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20 2H8a2 2 0 0 0-2 2v9.09l-4.42 4.41a1 1 0 1 0 1.41 1.42L7 14.91V20a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zm-7 13h-2v-2h2v2zm0-4h-2V7h2v4zm4 4h-2v-2h2v2zm0-4h-2V7h2v4z"/>
                  </svg>
                </div>
                <div class="text-sm text-teal-700 dark:text-teal-300 font-medium">
                  Ver receta
                </div>
              </div>

              <div class="mt-4 flex items-center gap-2">
                <button type="button"
                        @click="modalPdf=true; pdfUrl='{{ $recetaUrl }}'"
                        class="inline-flex items-center gap-2 rounded-xl bg-purple-900 text-white px-3 py-1.5 text-xs font-semibold hover:opacity-90">
                  Ver receta (PDF)
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M5 20h14v-2H5v2zM11 4h2v8h3l-4 4-4-4h3V4z"/>
                  </svg>
                </button>

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
      No hay recetas disponibles.
    </div>
  @endforelse
  {{-- ================= /SECCIONES ================= --}}

  {{-- ================= MODALES ================= --}}
  {{-- Modal PDF --}}
  <div x-show="modalPdf" x-cloak
       class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
       @keydown.escape.window="modalPdf=false">
    <div class="w-full max-w-6xl h-[80vh] rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-xl overflow-hidden">
      <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Receta médica</h3>
        <button class="rounded-lg px-2 py-1 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                @click="modalPdf=false">Cerrar</button>
      </div>
      <div class="h-full">
        <iframe :src="pdfUrl" class="w-full h-[calc(80vh-48px)]" frameborder="0"></iframe>
      </div>
    </div>
  </div>

  {{-- Modal Detalle --}}
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
          <button type="button"
                  @click="modalPdf=true; pdfUrl=detalle.pdf"
                  class="inline-flex items-center rounded-xl bg-purple-900 text-white px-4 py-2 text-sm font-semibold hover:opacity-90">
            Abrir receta (PDF)
          </button>
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
