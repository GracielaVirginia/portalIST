@props([
  // Opcionales
  'id' => 'cal-' . Str::random(5),
  'value' => null,          // 'YYYY-MM-DD' para preseleccionar
  'min' => null,            // 'YYYY-MM-DD' (opcional)
  'max' => null,            // 'YYYY-MM-DD' (opcional)
  'firstDay' => 1,          // 1 = Lunes, 0 = Domingo
  'locale' => 'es-CL',
])

<div
  id="{{ $id }}"
  class="cal-root"
  data-calendar
  data-locale="{{ $locale }}"
  data-first-day="{{ $firstDay }}"
  @if($value) data-value="{{ $value }}" @endif
  @if($min) data-min="{{ $min }}" @endif
  @if($max) data-max="{{ $max }}" @endif
>
  <div class="cal-header">
    <button class="cal-nav" data-action="prev" type="button" aria-label="Mes anterior">‹</button>
    <div class="cal-title" data-title></div>
    <button class="cal-nav" data-action="next" type="button" aria-label="Mes siguiente">›</button>
  </div>

  <div class="cal-weekdays" data-weekdays></div>
  <div class="cal-grid" role="grid" aria-labelledby="{{ $id }}-title" data-grid></div>
</div>
