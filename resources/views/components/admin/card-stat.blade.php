@props([
  // Contenido
  'title'        => 'Título',
  'value'        => 0,               // número mostrado
  'suffix'       => '',              // ej: '%', 'usuarios'
  'subtitle'     => null,            // texto pequeño bajo el valor

  // Progreso
  'percent'      => null,            // 0..100 (para barra/círculo)
  'trend'        => null,            // 'up' | 'down' | null
  'trendLabel'   => null,            // ej: '+12% vs ayer'

  // Apariencia
  'variant'      => 'circle',        // 'circle' | 'bar'
  'compact'      => false,           // tamaño más compacto
])

@php
  $isCircle = $variant === 'circle';
  $p = max(0, min(100, (int) ($percent ?? 0)));

  // Clases base
  $card  = 'rounded-2xl bg-white/10 backdrop-blur-md shadow ring-1 ring-white/10 p-4';
  $titleClass = 'text-xs font-semibold uppercase tracking-wide text-purple-100/80';
  $valueClass = 'text-2xl font-bold text-white';
  $subClass   = 'text-xs text-purple-100/70';
  $trendUp    = 'text-green-300';
  $trendDown  = 'text-red-300';

  // Círculo (SVG)
  $size = $compact ? 72 : 96;
  $stroke = 8;
  $r = ($size/2) - ($stroke/2);
  $circumference = 2 * M_PI * $r;
  $dash = $circumference * $p / 100;

  // Barra
  $barH = $compact ? 'h-1.5' : 'h-2.5';
@endphp

<div {{ $attributes->merge(['class' => $card]) }}>
  <div class="flex items-start justify-between">
    <div>
      <div class="{{ $titleClass }}">{{ $title }}</div>
      <div class="mt-1 flex items-center gap-2">
        <div class="{{ $valueClass }}">
          {{ $value }}@if($suffix)<span class="ml-1 text-purple-100/70 text-base">{{ $suffix }}</span>@endif
        </div>

        @if($trend)
          <div class="text-xs inline-flex items-center gap-1 rounded-full px-2 py-0.5 ring-1 ring-white/10
                      {{ $trend === 'up' ? 'bg-green-500/10 '.$trendUp : 'bg-red-500/10 '.$trendDown }}">
            @if($trend === 'up') ↑ @else ↓ @endif
            <span>{{ $trendLabel ?? '' }}</span>
          </div>
        @endif
      </div>

      @if($subtitle)
        <div class="mt-1 {{ $subClass }}">{{ $subtitle }}</div>
      @endif
    </div>

    {{-- Slot para icono opcional en la esquina --}}
    @if(trim($slot))
      <div class="shrink-0">
        {{ $slot }}
      </div>
    @endif
  </div>

  {{-- Visualización del progreso --}}
  @if(!is_null($percent))
    @if($isCircle)
      <div class="mt-4 grid place-items-center">
        <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 {{ $size }} {{ $size }}"
             class="drop-shadow-sm -rotate-90">
          <circle
            cx="{{ $size/2 }}" cy="{{ $size/2 }}" r="{{ $r }}"
            stroke="rgba(255,255,255,0.15)" stroke-width="{{ $stroke }}" fill="none"
          />
          <circle
            cx="{{ $size/2 }}" cy="{{ $size/2 }}" r="{{ $r }}"
            stroke="rgb(147, 51, 234)" {{-- purple-600 --}}
            stroke-width="{{ $stroke }}" fill="none"
            stroke-linecap="round"
            stroke-dasharray="{{ $circumference }}"
            stroke-dashoffset="{{ $circumference - $dash }}"
          />
        </svg>
        <div class="-mt-12 text-center">
          <div class="text-xl font-semibold text-white">{{ $p }}%</div>
          <div class="text-[11px] text-purple-100/70">completado</div>
        </div>
      </div>
    @else
      <div class="mt-4">
        <div class="w-full bg-white/10 rounded-full {{ $barH }} overflow-hidden ring-1 ring-white/10">
          <div class="bg-purple-600 {{ $barH }}" style="width: {{ $p }}%"></div>
        </div>
        <div class="mt-1 text-[11px] text-purple-100/70">{{ $p }}% completado</div>
      </div>
    @endif
  @endif
</div>
