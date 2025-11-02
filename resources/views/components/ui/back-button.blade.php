@props([
  // URL de destino. Si no se pasa, usa la anterior (url()->previous()).
  'href' => null,

  // Texto del botón
  'label' => 'Volver',

  // Variantes visuales: primary | outline | ghost
  'variant' => 'primary',

  // Tamaño: sm | md
  'size' => 'md',

  // Si no hay href, usar history.back() como fallback JS
  'useHistory' => true,
])

@php
  $computedHref = $href ?? url()->previous();

  // Estilos por variante
  $base = 'inline-flex items-center font-semibold rounded-xl transition focus:outline-none focus:ring-2 focus:ring-purple-400/60';
  $variants = [
    'primary' => 'bg-purple-900 text-white border border-purple-900/20 hover:bg-purple-800',
    'outline' => 'border border-gray-300 dark:border-gray-700 text-gray-800 dark:text-gray-100 bg-white dark:bg-gray-950 hover:bg-gray-50 dark:hover:bg-gray-900',
    'ghost'   => 'text-purple-900 dark:text-purple-100 hover:bg-purple-50/80 dark:hover:bg-purple-900/30',
  ];
  $sizes = [
    'sm' => 'text-xs px-3 py-1.5',
    'md' => 'text-sm px-5 py-2',
  ];
  $cls = implode(' ', [$base, $variants[$variant] ?? $variants['primary'], $sizes[$size] ?? $sizes['md'], $attributes->get('class')]);

  // ¿Usamos <a> o <button>?
  $useButton = (!$computedHref && $useHistory);
@endphp

@if($useButton)
  <button type="button"
          @click="history.back()"
          class="{{ $cls }}"
          aria-label="{{ $label }}">
    {{-- Icono --}}
    <svg xmlns="http://www.w3.org/2000/svg" class="-ml-0.5 mr-2 h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
      <path d="M10 19l-7-7 7-7 1.41 1.41L6.83 10H21v2H6.83l4.58 4.59L10 19z"/>
    </svg>
    <span>{{ $label }}</span>
  </button>
@else
  <a href="{{ $computedHref }}"
     class="{{ $cls }}"
     aria-label="{{ $label }}">
    <svg xmlns="http://www.w3.org/2000/svg" class="-ml-0.5 mr-2 h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
      <path d="M10 19l-7-7 7-7 1.41 1.41L6.83 10H21v2H6.83l4.58 4.59L10 19z"/>
    </svg>
    <span>{{ $label }}</span>
  </a>
@endif
