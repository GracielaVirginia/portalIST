@props([
  'href' => null,
  'label' => 'Volver',
  'variant' => 'primary',
  'size' => 'md',
  'useHistory' => true,
])

@php
  $computedHref = $href ?? url()->previous();

  $base = 'inline-flex items-center justify-center font-semibold rounded-xl transition-all duration-300
           focus:outline-none focus:ring-2 focus:ring-purple-300 relative overflow-hidden group';

  $variants = [
      'primary' => 'bg-gradient-to-r from-purple-600 via-fuchsia-600 to-purple-700 text-white shadow-md 
                    hover:shadow-lg hover:scale-[1.02]',
      'outline' => 'border border-purple-400 text-purple-800 bg-white hover:bg-purple-50 hover:text-purple-900',
      'ghost'   => 'text-purple-700 hover:bg-purple-100 hover:text-purple-900',
  ];

  $sizes = [
      'sm' => 'text-xs px-3 py-1.5',
      'md' => 'text-sm px-5 py-2.5',
  ];

  $cls = implode(' ', [$base, $variants[$variant] ?? $variants['primary'], $sizes[$size] ?? $sizes['md'], $attributes->get('class')]);
  $useButton = (!$computedHref && $useHistory);
@endphp

@if($useButton)
  <button type="button" @click="history.back()" class="{{ $cls }}" aria-label="{{ $label }}">
    {{-- Ícono izquierda --}}
    <svg xmlns="http://www.w3.org/2000/svg"
         class="-ml-0.5 mr-2 h-5 w-5 transition-transform group-hover:-translate-x-1 duration-300"
         viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
      <path d="M10 19l-7-7 7-7 1.41 1.41L6.83 10H21v2H6.83l4.58 4.59L10 19z"/>
    </svg>
    <span class="relative z-10">{{ $label }}</span>

    {{-- Brillo animado --}}
    <span class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 blur-sm transition duration-300"></span>
  </button>
@else
  <a href="{{ $computedHref }}" class="{{ $cls }}" aria-label="{{ $label }}">
    <svg xmlns="http://www.w3.org/2000/svg"
         class="-ml-0.5 mr-2 h-5 w-5 transition-transform group-hover:-translate-x-1 duration-300"
         viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
      <path d="M10 19l-7-7 7-7 1.41 1.41L6.83 10H21v2H6.83l4.58 4.59L10 19z"/>
    </svg>
    <span class="relative z-10">{{ $label }}</span>
    <span class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 blur-sm transition duration-300"></span>
  </a>
@endif
