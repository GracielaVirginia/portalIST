{{-- resources/views/components/portal/widget-resultados-recientes.blade.php --}}
@props([
  'items' => [
    // [
    //   'id' => 152,
    //   'especialidad' => 'RX',
    //   'examen_nombre' => 'Rx Abdomen complementario',
    //   'examen_codigo' => '0401014-01',
    //   'fecha' => '2025-10-18 11:20:00',
    //   'estado' => 'DISPONIBLE',
    //   'url_pdf_informe' => 'archivo.pdf' | '/ruta/absoluta.pdf' | 'https://...'
    // ],
  ],
  'titulo' => 'Resultados recientes',
])

@php
  use Illuminate\Support\Str;

  $list = collect($items);

  $badgeColor = function ($estado) {
    $e = strtoupper((string) $estado);
    return match ($e) {
      'DISPONIBLE', 'DICTADO', 'FINAL' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200 border-green-300/60 dark:border-green-700/50',
      'EN_PROCESO', 'BORRADOR'        => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200 border-amber-300/60 dark:border-amber-700/50',
      default                         => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200 border-gray-300/60 dark:border-gray-700/50',
    };
  };

  // Helper para normalizar URL del PDF
  $pdfUrlFn = function ($raw, $id) {
    if ($raw) {
      return Str::startsWith($raw, ['http://','https://','/'])
        ? $raw
        : asset('informes/'.$raw); // busca en public/informes
    }
    // Fallback opcional: viewer/route si no hay archivo físico
    return $id ? route('portal.resultados.pdf', $id) : null;
  };
@endphp

<section class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm">
  <header class="flex items-center justify-between px-4 sm:px-5 py-3 border-b border-gray-200 dark:border-gray-700">
    <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100">
      {{ $titulo }}
    </h3>
    <a href="{{ route('portal.resultados.index') }}"
       class="inline-flex items-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700
              px-3 py-1.5 text-xs sm:text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800">
      Ver todos
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 4l1.41 1.41L8.83 10H20v2H8.83l4.58 4.59L12 18l-8-8 8-8z"/>
      </svg>
    </a>
  </header>

  @if($list->isEmpty())
    <div class="px-4 sm:px-5 py-6">
      <div class="rounded-xl border border-dashed border-purple-900/30 dark:border-purple-300/30
                  bg-purple-50 dark:bg-purple-950/30 p-4 text-sm text-purple-900 dark:text-purple-100">
        Aún no tienes resultados recientes. Cuando estén disponibles, aparecerán aquí con acceso directo al PDF.
      </div>
    </div>
  @else
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4 p-4 sm:p-5">
      @foreach ($list as $it)
        @php
          $id     = $it['id'] ?? null;
          $esp    = strtoupper((string)($it['especialidad'] ?? ''));
          $name   = $it['examen_nombre'] ?? '';
          $code   = $it['examen_codigo'] ?? '';
          $fecha  = $it['fecha'] ?? '';
          $estado = $it['estado'] ?? '';
          $rawPdf = $it['url_pdf_informe'] ?? null;

          $pdfUrl = $pdfUrlFn($rawPdf, $id);
        @endphp

        <article class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm flex flex-col">
          {{-- encabezado: especialidad + estado --}}
          <div class="flex items-start justify-between gap-3">
            <div class="inline-flex items-center gap-2">
              <span class="h-2.5 w-2.5 rounded-full bg-purple-900"></span>
              <span class="text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200">
                {{ $esp ?: '—' }}
              </span>
            </div>

            <span class="inline-flex items-center gap-1 rounded-lg border px-2 py-0.5 text-xs font-semibold {{ $badgeColor($estado) }}">
              {{ $estado ?: '—' }}
            </span>
          </div>

          {{-- contenido principal --}}
          <div class="mt-3">
            <h4 class="text-sm sm:text-base font-semibold text-gray-900 dark:text-gray-100 leading-snug">
              {{ $name ?: 'Examen' }}
            </h4>
            <div class="mt-1 text-xs text-gray-600 dark:text-gray-300">
              Código: <span class="font-mono">{{ $code ?: '—' }}</span>
            </div>
            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              Fecha: {{ $fecha ?: '—' }}
            </div>
          </div>

          {{-- acciones --}}
          <div class="mt-4 flex flex-wrap items-center gap-2">
            {{-- Detalle --}}
            @if($id)
              <a href="{{ route('portal.resultados.show', $id) }}"
                 class="inline-flex items-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700
                        bg-white dark:bg-gray-950 hover:bg-gray-50 dark:hover:bg-gray-900
                        px-3 py-1.5 text-xs font-semibold text-gray-800 dark:text-gray-100">
                Detalle
              </a>
            @endif

            {{-- Abrir PDF (como en los modales) --}}
            @if($pdfUrl)
              <a href="{{ $pdfUrl }}" target="_blank" rel="noopener"
                 class="inline-flex items-center rounded-xl bg-purple-900 text-white px-4 py-2 text-sm font-semibold hover:opacity-90">
                Abrir PDF
              </a>
            @else
              <button type="button" disabled
                      class="inline-flex items-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700
                             bg-gray-100 dark:bg-gray-800 px-3 py-1.5 text-xs font-semibold text-gray-500 dark:text-gray-400 cursor-not-allowed">
                PDF no disponible
              </button>
            @endif

            {{-- Viewer (opcional) --}}
            @if(isset($it['viewer']) && $it['viewer'] && $id)
              <a href="{{ route('portal.resultados.viewer', $id) }}"
                 class="inline-flex items-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700
                        bg-white dark:bg-gray-950 hover:bg-gray-50 dark:hover:bg-gray-900
                        px-3 py-1.5 text-xs font-semibold text-gray-800 dark:text-gray-100">
                Viewer
              </a>
            @endif
          </div>
        </article>
      @endforeach
    </div>
  @endif
</section>
