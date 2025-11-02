@props([
  'backHref' => route('admin.dashboard'),
  'logoutRoute' => 'admin.logout',
  // posición opcional: left, right, center
  'align' => 'left',
])

@php
  $alignClass = match($align) {
    'center' => 'justify-center',
    'right'  => 'justify-end',
    default  => 'justify-start',
  };
@endphp

<div class="mb-5">
  <div class="flex flex-wrap {{ $alignClass }} items-center gap-3">

    {{-- Botón Volver --}}
    <a href="{{ $backHref }}"
       class="group inline-flex items-center gap-2 border border-purple-400 text-purple-800 dark:text-purple-200
              rounded-full px-4 py-2 text-sm font-semibold transition-all duration-200
              hover:bg-purple-100/50 dark:hover:bg-purple-900/30 hover:border-purple-700 dark:hover:border-purple-500
              focus:ring-2 focus:ring-purple-400 focus:outline-none">
      <svg xmlns="http://www.w3.org/2000/svg"
           class="h-4 w-4 transition-transform duration-200 group-hover:-translate-x-0.5"
           fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
      </svg>
      <span>Volver al dashboard</span>
    </a>

    {{-- Botón Cerrar sesión --}}
    <form method="POST" action="{{ route($logoutRoute) }}">
      @csrf
      <button type="submit"
              class="group inline-flex items-center gap-2 border border-red-400 text-red-700 dark:text-red-300
                     rounded-full px-4 py-2 text-sm font-semibold transition-all duration-200
                     hover:bg-red-50 dark:hover:bg-red-900/30 hover:border-red-600 dark:hover:border-red-500
                     focus:ring-2 focus:ring-red-400 focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg"
             class="h-4 w-4 transition-transform duration-200 group-hover:rotate-12"
             viewBox="0 0 24 24" fill="currentColor">
          <path d="M16 13v-2H7V8l-5 4 5 4v-3h9zM20 3h-8a2 2 0 00-2 2v4h2V5h8v14h-8v-4h-2v4a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2z"/>
        </svg>
        <span>Cerrar sesión</span>
      </button>
    </form>

  </div>
</div>
