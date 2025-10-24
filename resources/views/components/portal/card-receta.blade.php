{{-- resources/views/components/portal/card-receta.blade.php --}}
@props([
  'receta' => [
    // 'id' => 9001,
    // 'fecha_atencion' => '2025-10-18 10:45:00',
    // 'created_at' => '2025-10-18 10:50:00',
    // 'especialidad' => 'MED_INT',
    // 'tiene_receta' => true,
    // 'detalle_receta' => 'Paracetamol 500mg cada 8h por 3 días...',
    // 'url_pdf_receta' => 'recetas/9001.pdf',
    // 'medicamentos_activos' => 'Metformina; Losartán',
  ],
  'compact' => false,
  'ctaUrl' => null,
])

@php
  $id       = $receta['id'] ?? null;
  $esp      = strtoupper((string)($receta['especialidad'] ?? ''));
  $tiene    = (bool)($receta['tiene_receta'] ?? false);
  $detalle  = $receta['detalle_receta'] ?? null;
  $pdf      = $receta['url_pdf_receta'] ?? null;
  $meds     = $receta['medicamentos_activos'] ?? null;

  $faRaw    = $receta['fecha_atencion'] ?? null;
  $fcRaw    = $receta['created_at'] ?? null;

  try { $fa = $faRaw ? \Carbon\Carbon::parse($faRaw)->locale('es')->isoFormat('DD MMM YYYY, HH:mm') : null; }
  catch (\Throwable $e) { $fa = $faRaw; }
  try { $fc = $fcRaw ? \Carbon\Carbon::parse($fcRaw)->locale('es')->isoFormat('DD MMM YYYY, HH:mm') : null; }
  catch (\Throwable $e) { $fc = $fcRaw; }

  $pad = $compact ? 'p-3' : 'p-4';

  $badgeEstado = $tiene
    ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-100 border-emerald-300/60 dark:border-emerald-700/50'
    : 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-100 border-amber-300/60 dark:border-amber-700/50';
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

    <span class="inline-flex items-center gap-1 rounded-lg border px-2 py-0.5 text-[11px] font-semibold {{ $badgeEstado }}">
      {{ $tiene ? 'Con receta' : 'Sin receta' }}
    </span>
  </div>

  {{-- cuerpo --}}
  <div class="mt-3 space-y-2">
    @if($fa || $fc)
      <div class="text-xs text-gray-600 dark:text-gray-300 space-y-0.5">
        @if($fa)
          <div>Atención: <span class="font-medium">{{ $fa }}</span></div>
        @endif
        @if($fc)
          <div>Emitida: <span class="font-medium">{{ $fc }}</span></div>
        @endif
      </div>
    @endif

    @if($meds)
      <div class="text-xs text-gray-600 dark:text-gray-300">
        <span class="font-semibold">Medicamentos:</span>
        <span>{{ $meds }}</span>
      </div>
    @endif

    @if($detalle)
      <div class="text-xs text-gray-600 dark:text-gray-300">
        <span class="font-semibold">Indicaciones:</span>
        <span>{{ $detalle }}</span>
      </div>
    @endif
  </div>

  {{-- acciones --}}
  <div class="mt-4 flex flex-wrap items-center gap-2">
    @if($ctaUrl)
      <a href="{{ $ctaUrl }}"
         class="inline-flex items-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700
                bg-white dark:bg-gray-950 hover:bg-gray-50 dark:hover:bg-gray-900
                px-3 py-1.5 text-xs font-semibold text-gray-800 dark:text-gray-100">
        Ver / gestionar
      </a>
    @endif

    @if($pdf)
      {{-- Si guardas el archivo en storage local/public y tienes una ruta dedicada, usa esa en lugar de $pdf --}}
      <a href="{{ $pdf }}"
         class="inline-flex items-center gap-2 rounded-xl border border-purple-900/20 dark:border-purple-300/20
                bg-purple-900 text-white hover:opacity-90 px-3 py-1.5 text-xs font-semibold">
        Descargar PDF
      </a>
    @else
      <button type="button" disabled
              class="inline-flex items-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700
                     bg-gray-100 dark:bg-gray-800 px-3 py-1.5 text-xs font-semibold text-gray-500 dark:text-gray-400 cursor-not-allowed">
        PDF no disponible
      </button>
    @endif
  </div>
</article>
{{-- <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4">
  @foreach ($recetas as $rx)
    <x-portal.card-receta :receta="$rx" :cta-url="route('portal.recetas.index')" />
  @endforeach
</div> --}}
