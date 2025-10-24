{{-- resources/views/components/portal/card-resultado.blade.php --}}
@props([
  'item' => [
    // 'id' => null,
    // 'especialidad' => 'RX',
    // 'examen_nombre' => 'Rx Abdomen complementario',
    // 'examen_codigo' => '0401014-01',
    // 'fecha' => '2025-10-18 11:20:00',
    // 'estado' => 'DISPONIBLE', // BORRADOR | EN_PROCESO | FINAL/DICTADO | PENDIENTE
    // 'url_pdf_informe' => null,
    // 'viewer' => false,
  ],
  'compact' => false,
])

@php
  $id     = $item['id'] ?? null;
  $esp    = strtoupper((string)($item['especialidad'] ?? ''));
  $name   = $item['examen_nombre'] ?? 'Examen';
  $code   = $item['examen_codigo'] ?? '—';
  $fecha  = $item['fecha'] ?? '—';
  $estado = strtoupper((string)($item['estado'] ?? '—'));
  $pdf    = $item['url_pdf_informe'] ?? null;
  $hasViewer = (bool)($item['viewer'] ?? false);

  $estadoBadgeClass = match (true) {
    str_contains($estado, 'FINAL') || str_contains($estado, 'DICTADO') || str_contains($estado, 'DISPONIBLE')
      => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-100 border-green-300/60 dark:border-green-700/50',
    str_contains($estado, 'BORRADOR') || str_contains($estado, 'EN_PROCESO')
      => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-100 border-amber-300/60 dark:border-amber-700/50',
    default
      => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200 border-gray-300/60 dark:border-gray-700/50',
  };

  $pad = $compact ? 'p-3' : 'p-4';
  $titleClass = $compact ? 'text-sm' : 'text-base';
@endphp

<article class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm {{ $pad }} flex flex-col">
  {{-- header: especialidad + estado --}}
  <div class="flex items-start justify-between gap-3">
    <div class="inline-flex items-center gap-2">
      <span class="h-2.5 w-2.5 rounded-full bg-purple-900"></span>
      <span class="text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200">
        {{ $esp ?: '—' }}
      </span>
    </div>

    <span class="inline-flex items-center gap-1 rounded-lg border px-2 py-0.5 text-[11px] font-semibold {{ $estadoBadgeClass }}">
      {{ $estado ?: '—' }}
    </span>
  </div>

  {{-- cuerpo --}}
  <div class="mt-3">
    <h4 class="font-semibold text-gray-900 dark:text-gray-100 leading-snug {{ $titleClass }}">
      {{ $name }}
    </h4>
    <div class="mt-1 text-xs text-gray-600 dark:text-gray-300">
      Código: <span class="font-mono">{{ $code }}</span>
    </div>
    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
      Fecha: {{ $fecha }}
    </div>
  </div>

  {{-- acciones --}}
  <div class="mt-4 flex flex-wrap items-center gap-2">
    @if($id)
      <a href="{{ route('portal.resultados.show', $id) }}"
         class="inline-flex items-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700
                bg-white dark:bg-gray-950 hover:bg-gray-50 dark:hover:bg-gray-900
                px-3 py-1.5 text-xs font-semibold text-gray-800 dark:text-gray-100">
        Detalle
      </a>
    @endif

    @if($pdf)
      <a href="{{ route('portal.resultados.pdf', $id) }}"
         class="inline-flex items-center gap-2 rounded-xl border border-purple-900/20 dark:border-purple-300/20
                bg-purple-900 text-white hover:opacity-90 px-3 py-1.5 text-xs font-semibold">
        Ver PDF
      </a>
    @else
      <button type="button" disabled
              class="inline-flex items-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700
                     bg-gray-100 dark:bg-gray-800 px-3 py-1.5 text-xs font-semibold text-gray-500 dark:text-gray-400 cursor-not-allowed">
        PDF no disponible
      </button>
    @endif

    @if($hasViewer)
      <a href="{{ route('portal.resultados.viewer', $id) }}"
         class="inline-flex items-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700
                bg-white dark:bg-gray-950 hover:bg-gray-50 dark:hover:bg-gray-900
                px-3 py-1.5 text-xs font-semibold text-gray-800 dark:text-gray-100">
        Viewer
      </a>
    @endif
  </div>
</article>
{{-- En una grilla de resultados por especialidad --}}
{{-- <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4">
  @foreach ($items as $it)
    <x-portal.card-resultado :item="$it" />
  @endforeach
</div> --}}

{{-- Versión compacta (por ejemplo dentro de widget de “recientes”) --}}
{{-- <x-portal.card-resultado :item="$it" compact="true" /> --}}
