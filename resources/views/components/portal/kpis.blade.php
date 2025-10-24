{{-- resources/views/components/portal/kpis.blade.php --}}
@props([
  'kpis' => [
    'proximas_citas' => 0,
    'resultados_disponibles' => 0,
    'ordenes' => 0,
    'alertas' => 0,
  ],
])

@php
  $pc   = (int)($kpis['proximas_citas'] ?? 0);
  $res  = (int)($kpis['resultados_disponibles'] ?? 0);
  $ord  = (int)($kpis['ordenes'] ?? 0);
  $alr  = (int)($kpis['alertas'] ?? 0);

  // Texto accesible
  $items = [
    ['label' => 'Próximas citas', 'value' => $pc,  'icon' => 'calendar'],
    ['label' => 'Resultados',     'value' => $res, 'icon' => 'doc'],
    ['label' => 'Licencias',        'value' => $ord, 'icon' => 'form'],
    ['label' => 'Alertas',        'value' => $alr, 'icon' => 'warn'],
  ];
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mt-4">
  @foreach ($items as $it)
    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-3 shadow-sm">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          {{-- Icono --}}
          @switch($it['icon'])
            @case('calendar')
              <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-purple-900 text-white">
                {{-- calendar icon --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M7 2h2v2h6V2h2v2h3v18H4V4h3V2zm13 6H4v12h16V8z"/></svg>
              </span>
            @break
            @case('doc')
              <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-purple-900 text-white">
                {{-- document icon --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6v20h12V8l-4-6zm0 2.5L17.5 8H14V4.5z"/></svg>
              </span>
            @break
            @case('form')
              <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-purple-900 text-white">
                {{-- form icon --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M4 3h16v18H4V3zm3 4v2h10V7H7zm0 4v2h10v-2H7zm0 4v2h7v-2H7z"/></svg>
              </span>
            @break
            @default
              <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-purple-900 text-white">
                {{-- warning/alert icon --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M1 21h22L12 2 1 21zm12-3h-2v2h2v-2zm0-8h-2v6h2V10z"/></svg>
              </span>
          @endswitch

          <div class="text-sm text-gray-600 dark:text-gray-300">
            {{ $it['label'] }}
          </div>
        </div>

        <div class="text-xl font-bold text-gray-900 dark:text-gray-100">
          {{ $it['value'] }}
        </div>
      </div>
    </div>
  @endforeach
</div>
