{{-- resources/views/components/portal/card-orden.blade.php --}}
@props([
  'orden' => [
    // 'id' => 501,
    // 'fecha_solicitud' => '2025-10-20 11:15:00',
    // 'especialidad' => 'RX',
    // 'examen_codigo' => '0401014-01',
    // 'examen_nombre' => 'Rx Abdomen complementario',
    // 'origen_solicitud' => 'WEB',        // WEB|CALLCENTER|PRESENCIAL...
    // 'estado_solicitud' => 'PENDIENTE',  // PENDIENTE|CONFIRMADA|RECHAZADA
    // 'fecha_cita_programada' => null,    // si ya tiene hora asignada
    // 'lugar_cita' => 'Centro Imagenología Norte',
  ],
  'compact' => false,
  'ctaUrl' => null,
])

@php
  $id      = $orden['id'] ?? null;
  $fechaS  = $orden['fecha_solicitud'] ?? null;
  try {
    $fechaSolicitud = $fechaS ? \Carbon\Carbon::parse($fechaS)->locale('es')->isoFormat('DD MMM YYYY, HH:mm') : '—';
  } catch (\Throwable $e) {
    $fechaSolicitud = $fechaS ?: '—';
  }

  $esp     = strtoupper((string)($orden['especialidad'] ?? ''));
  $code    = $orden['examen_codigo'] ?? '—';
  $name    = $orden['examen_nombre'] ?? '—';
  $origen  = strtoupper((string)($orden['origen_solicitud'] ?? '—'));
  $estado  = strtoupper((string)($orden['estado_solicitud'] ?? '—'));
  $fechaC  = $orden['fecha_cita_programada'] ?? null;
  $lugar   = $orden['lugar_cita'] ?? null;

  try {
    $fechaCita = $fechaC ? \Carbon\Carbon::parse($fechaC)->locale('es')->isoFormat('ddd D [de] MMM, HH:mm') : null;
  } catch (\Throwable $e) {
    $fechaCita = $fechaC;
  }

  $pad = $compact ? 'p-3' : 'p-4';

  $estadoBadge = match ($estado) {
    'CONFIRMADA' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-100 border-emerald-300/60 dark:border-emerald-700/50',
    'RECHAZADA'  => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-100 border-rose-300/60 dark:border-rose-700/50',
    'PENDIENTE'  => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-100 border-amber-300/60 dark:border-amber-700/50',
    default      => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200 border-gray-300/60 dark:border-gray-700/50',
  };

  $origenBadge = 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200 border-gray-300/60 dark:border-gray-700/50';
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

    <div class="flex items-center gap-2">
      <span class="inline-flex items-center gap-1 rounded-lg border px-2 py-0.5 text-[11px] font-semibold {{ $estadoBadge }}">
        {{ $estado }}
      </span>
      @if($origen && $origen !== '—')
        <span class="inline-flex items-center gap-1 rounded-lg border px-2 py-0.5 text-[11px] font-semibold {{ $origenBadge }}">
          {{ $origen }}
        </span>
      @endif
    </div>
  </div>

  {{-- cuerpo --}}
  <div class="mt-3 space-y-1">
    <h4 class="text-sm sm:text-base font-semibold text-gray-900 dark:text-gray-100 leading-snug">
      {{ $name }}
    </h4>
    <div class="text-xs text-gray-600 dark:text-gray-300">
      Código: <span class="font-mono">{{ $code }}</span>
    </div>
    <div class="text-xs text-gray-500 dark:text-gray-400">
      Solicitada: {{ $fechaSolicitud }}
    </div>

    @if($fechaCita || $lugar)
      <div class="pt-2 mt-2 border-t border-dashed border-gray-200 dark:border-gray-700">
        @if($fechaCita)
          <div class="text-xs text-gray-600 dark:text-gray-300">
            Cita: <span class="font-medium">{{ $fechaCita }}</span>
          </div>
        @endif
        @if($lugar)
          <div class="text-xs text-gray-600 dark:text-gray-300">
            Lugar: <span class="font-medium">{{ $lugar }}</span>
          </div>
        @endif
      </div>
    @endif
  </div>

  {{-- acciones --}}
  <div class="mt-4 flex flex-wrap items-center gap-2">
    @if($ctaUrl)
      <a href="{{ $ctaUrl }}"
         class="inline-flex items-center gap-2 rounded-xl border border-purple-900/20 dark:border-purple-300/20
                bg-purple-900 text-white hover:opacity-90 px-3 py-1.5 text-xs font-semibold">
        Gestionar orden
      </a>
    @endif

    {{-- (Opcional) botón alterno: confirmar / cancelar, según estado --}}
    @if($estado === 'PENDIENTE')
      <button type="button"
              class="inline-flex items-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700
                     bg-white dark:bg-gray-950 hover:bg-gray-50 dark:hover:bg-gray-900
                     px-3 py-1.5 text-xs font-semibold text-gray-800 dark:text-gray-100">
        Confirmar
      </button>
    @endif
  </div>
</article>
{{-- <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4">
  @foreach ($ordenes as $orden)
    <x-portal.card-orden :orden="$orden" :cta-url="route('portal.ordenes.index')" />
  @endforeach
</div> --}}
