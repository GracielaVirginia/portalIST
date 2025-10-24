{{-- resources/views/components/portal/card-cita.blade.php --}}
@props([
  'cita' => [
    // 'id' => 1001,
    // 'fecha_cita_programada' => '2025-11-03 09:30:00',
    // 'lugar_cita' => 'Centro Médico Central',
    // 'especialidad' => 'RX',
    // 'tipo_atencion' => 'CONTROL',
    // 'modalidad_atencion' => 'PRESENCIAL',
    // 'estado_solicitud' => 'CONFIRMADA',  // PENDIENTE|CONFIRMADA|RECHAZADA
    // 'estado_asistencia' => 'PENDIENTE',  // REALIZADA|NO_REALIZADA|PENDIENTE
    // 'profesional' => 'Dra. González',
  ],
  'compact' => false,
  'ctaUrl' => null,
])

@php
  $id        = $cita['id'] ?? null;
  $fechaRaw  = $cita['fecha_cita_programada'] ?? null;
  try {
    $fechaFmt = $fechaRaw ? \Carbon\Carbon::parse($fechaRaw)->locale('es')->isoFormat('ddd D [de] MMM, HH:mm') : '—';
  } catch (\Throwable $e) {
    $fechaFmt = $fechaRaw ?: '—';
  }

  $lugar     = $cita['lugar_cita'] ?? '—';
  $esp       = strtoupper((string)($cita['especialidad'] ?? ''));
  $tipo      = strtoupper((string)($cita['tipo_atencion'] ?? ''));
  $mod       = strtoupper((string)($cita['modalidad_atencion'] ?? ''));
  $estSol    = strtoupper((string)($cita['estado_solicitud'] ?? ''));
  $estAsist  = strtoupper((string)($cita['estado_asistencia'] ?? ''));
  $prof      = $cita['profesional'] ?? null;

  $pad = $compact ? 'p-3' : 'p-4';

  $badgeSolicitud = match ($estSol) {
    'CONFIRMADA' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-100 border-emerald-300/60 dark:border-emerald-700/50',
    'RECHAZADA'  => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-100 border-rose-300/60 dark:border-rose-700/50',
    'PENDIENTE'  => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-100 border-amber-300/60 dark:border-amber-700/50',
    default      => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200 border-gray-300/60 dark:border-gray-700/50',
  };

  $badgeAsistencia = match ($estAsist) {
    'REALIZADA'     => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-100 border-emerald-300/60 dark:border-emerald-700/50',
    'NO_REALIZADA'  => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-100 border-rose-300/60 dark:border-rose-700/50',
    'PENDIENTE'     => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-100 border-amber-300/60 dark:border-amber-700/50',
    default         => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200 border-gray-300/60 dark:border-gray-700/50',
  };
@endphp

<article class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm {{ $pad }} flex flex-col">
  {{-- header: especialidad + estado(s) --}}
  <div class="flex items-start justify-between gap-3">
    <div class="inline-flex items-center gap-2">
      <span class="h-2.5 w-2.5 rounded-full bg-purple-900"></span>
      <span class="text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200">
        {{ $esp ?: '—' }}
      </span>
    </div>

    <div class="flex items-center gap-2">
      @if($estSol)
        <span class="inline-flex items-center gap-1 rounded-lg border px-2 py-0.5 text-[11px] font-semibold {{ $badgeSolicitud }}">
          {{ $estSol }}
        </span>
      @endif
      @if($estAsist)
        <span class="inline-flex items-center gap-1 rounded-lg border px-2 py-0.5 text-[11px] font-semibold {{ $badgeAsistencia }}">
          {{ $estAsist }}
        </span>
      @endif
    </div>
  </div>

  {{-- cuerpo --}}
  <div class="mt-3 space-y-1">
    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
      {{ $fechaFmt }}
    </div>
    <div class="text-xs text-gray-600 dark:text-gray-300">
      Lugar: <span class="font-medium">{{ $lugar }}</span>
    </div>
    <div class="text-xs text-gray-600 dark:text-gray-300">
      Tipo: <span class="font-medium">{{ $tipo ?: '—' }}</span>
      <span class="mx-1">•</span>
      Modalidad: <span class="font-medium">{{ $mod ?: '—' }}</span>
    </div>
    @if($prof)
      <div class="text-xs text-gray-600 dark:text-gray-300">
        Profesional: <span class="font-medium">{{ $prof }}</span>
      </div>
    @endif
  </div>

  {{-- acciones --}}
  <div class="mt-4 flex flex-wrap items-center gap-2">
    @if($ctaUrl)
      <a href="{{ $ctaUrl }}"
         class="inline-flex items-center gap-2 rounded-xl border border-purple-900/20 dark:border-purple-300/20
                bg-purple-900 text-white hover:opacity-90 px-3 py-1.5 text-xs font-semibold">
        Ver / gestionar
      </a>
    @endif

    {{-- botón “Agregar a calendario” (descarga .ics simple más adelante si quieres) --}}
    @if($fechaRaw && $lugar !== '—')
      <button type="button"
              class="inline-flex items-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700
                     bg-white dark:bg-gray-950 hover:bg-gray-50 dark:hover:bg-gray-900
                     px-3 py-1.5 text-xs font-semibold text-gray-800 dark:text-gray-100">
        Agendar (.ics)
      </button>
    @endif
  </div>
</article>
{{-- <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4">
  @foreach ($citas as $cita)
    <x-portal.card-cita :cita="$cita" :cta-url="route('portal.citas.index')" />
  @endforeach
</div> --}}