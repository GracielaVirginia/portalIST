{{-- resources/views/components/portal/sidebar.blade.php --}}
@props([
  'resultados' => [
    'total' => 0,
    'por_especialidad' => [],
  ],
])

@php
  $total = (int) ($resultados['total'] ?? 0);
  $esp   = $resultados['por_especialidad'] ?? [];
@endphp

<aside class="w-full lg:w-64 shrink-0">
  <nav class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-3 shadow-sm">

    {{-- Inicio --}}
    <a href="{{ route('portal.home') }}"
       class="flex items-center justify-between rounded-xl px-3 py-2 text-sm font-medium
              text-gray-800 dark:text-gray-100 bg-gray-50 dark:hover:bg-gray-800">
      <span class="inline-flex items-center gap-2">
        <span class="h-2 w-2 rounded-full bg-purple-900"></span>
        Inicio
      </span>
    </a>

    {{-- ================= RESULTADOS (FIJO) ================= --}}
    <div class="mt-2">
      {{-- Encabezado fijo --}}
      <div class="flex items-center justify-between rounded-xl px-3 py-2 text-sm font-semibold
                  text-gray-900 dark:text-gray-100">
        <span class="inline-flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-purple-900" viewBox="0 0 24 24" fill="currentColor">
            <path d="M14 2H6v20h12V8l-4-6zm0 2.5L17.5 8H14V4.5z"/>
          </svg>
          Todos mis resultados
        </span>
        <span class="rounded-lg bg-purple-900 text-white text-xs px-2 py-0.5">{{ $total }}</span>
      </div>

      {{-- Contenedor lila --}}
      <div class="ml-3 mt-1 rounded-xl bg-purple-100 dark:bg-purple-900/30 p-1.5 space-y-1">

        {{-- Ver todos --}}
        <a href="{{ route('ver-resultados') }}"
           class="group cursor-pointer flex items-center justify-between rounded-lg
                  px-3 py-2 text-xs font-medium
                  text-purple-900 dark:text-purple-100
                  hover:bg-purple-900 dark:hover:bg-purple-800 hover:text-white
                  transition">
          <span class="inline-flex items-center gap-2">
            <span class="h-2 w-2 rounded-full bg-purple-700 group-hover:bg-white"></span>
            <span class="pl-1">Ver todos</span>
          </span>
          <span class="rounded-lg bg-purple-900 text-white text-[11px] px-2 py-0.5
                       group-hover:ring-1 group-hover:ring-white/40">→</span>
        </a>

        {{-- Por especialidad --}}
        @forelse ($esp as $row)
          @php
            $code = strtoupper($row['especialidad'] ?? '');
            $label = $row['label'] ?? $code;
            $count = (int) ($row['count'] ?? 0);
          @endphp

          <a href="{{ route('portal.resultados.especialidad', ['esp' => $code]) }}"
             class="group cursor-pointer flex items-center justify-between rounded-lg
                    px-3 py-2 text-xs font-medium
                    text-purple-900 dark:text-purple-100
                    hover:bg-purple-900 dark:hover:bg-purple-800 hover:text-white
                    transition">
            <span class="inline-flex items-center gap-2">
              <span class="h-2 w-2 rounded-full bg-purple-700 group-hover:bg-white"></span>
              <span class="pl-1">{{ $label }}</span>
            </span>

            <span class="rounded-lg bg-purple-900 text-white text-[11px] px-2 py-0.5
                         group-hover:ring-1 group-hover:ring-white/40">
              {{ $count }}
            </span>
          </a>
        @empty
          <div class="rounded-lg px-3 py-2 text-xs text-purple-800 dark:text-purple-200">
            Sin especialidades disponibles
          </div>
        @endforelse
      </div>
    </div>
    {{-- ================= /RESULTADOS ================= --}}

    {{-- Licencias --}}
    <a href="{{ route('portal.licencias.index') }}"
       class="mt-3 flex items-center justify-between rounded-xl px-3 py-2 text-sm font-medium
              text-gray-800 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800">
      <span class="inline-flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-purple-900" viewBox="0 0 24 24" fill="currentColor">
          <path d="M4 3h16v18H4V3zm3 4v2h10V7H7zm0 4v2h10v-2H7zm0 4v2h7v-2H7z"/>
        </svg>
        Licencias
      </span>
    </a>

    {{-- Recetas --}}
    <a href="{{ route('portal.recetas.index') }}"
       class="mt-1 flex items-center justify-between rounded-xl px-3 py-2 text-sm font-medium
              text-gray-800 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800">
      <span class="inline-flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-purple-900" viewBox="0 0 24 24" fill="currentColor">
          <path d="M19 3H5v18h14V3zm-4 4v2H9V7h6zm0 4v2H9v-2h6zm-3 4v2H9v-2h3z"/>
        </svg>
        Recetas
      </span>
    </a>
  </nav>
</aside>
