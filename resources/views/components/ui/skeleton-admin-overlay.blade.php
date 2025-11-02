@props([
  'id' => 'pageSkeletonOverlay',
  'class' => '',
])

<div id="{{ $id }}"
     {{ $attributes->merge(['class' => "fixed inset-0 z-[100] hidden items-center justify-center bg-gradient-to-br from-purple-200/80 via-fuchsia-200/70 to-purple-100/80 dark:from-gray-900/90 dark:via-gray-900/85 dark:to-gray-900/90 backdrop-blur-md $class"]) }}>

  {{-- Glow/spotlights sutiles --}}
  <div class="pointer-events-none absolute -top-24 -left-24 h-[420px] w-[420px] rounded-full blur-3xl bg-fuchsia-400/25 animate-pulse"></div>
  <div class="pointer-events-none absolute -bottom-28 -right-28 h-[520px] w-[520px] rounded-full blur-3xl bg-purple-400/25 animate-[pulse_4s_ease-in-out_infinite]"></div>

  <div class="w-full max-w-[1200px] mx-auto px-4">
    <div class="grid grid-cols-12 gap-5 animate-pulse select-none">
      {{-- ===== Sidebar ===== --}}
      <div class="col-span-12 md:col-span-3">
        <div class="rounded-2xl border border-white/30 dark:border-white/10 bg-white/50 dark:bg-gray-800/40 backdrop-blur p-4 shadow-xl space-y-3">
          <div class="h-5 w-40 rounded bg-white/60 dark:bg-gray-700/60"></div>
          <div class="space-y-2 pt-1">
            @for($i=0; $i<5; $i++)
              <div class="flex items-center gap-3">
                <div class="h-2.5 w-2.5 rounded-full bg-purple-400/80"></div>
                <div class="h-3 w-40 rounded bg-white/60 dark:bg-gray-700/60"></div>
                <div class="ml-auto text-[10px] h-5 w-7 rounded-full bg-white/50 dark:bg-gray-700/50"></div>
              </div>
            @endfor
          </div>
          <div class="pt-2 h-9 w-28 rounded-full bg-white/60 dark:bg-gray-700/60"></div>
        </div>
      </div>

      {{-- ===== Centro (chart/panel) ===== --}}
      <div class="col-span-12 md:col-span-6">
        <div class="rounded-2xl border border-white/30 dark:border-white/10 bg-white/60 dark:bg-gray-800/40 backdrop-blur p-4 shadow-xl">
          <div class="h-5 w-56 rounded bg-white/70 dark:bg-gray-700/70 mb-3"></div>
          <div class="h-[260px] rounded-xl bg-gradient-to-br from-white/70 to-white/40 dark:from-gray-700/60 dark:to-gray-700/30 border border-white/40 dark:border-gray-700/40"></div>
          <div class="mt-3 flex items-center gap-3">
            <div class="h-3 w-32 rounded bg-white/60 dark:bg-gray-700/60"></div>
            <div class="h-3 w-20 rounded bg-white/50 dark:bg-gray-700/50"></div>
          </div>
        </div>

        <div class="mt-4 rounded-2xl border border-white/30 dark:border-white/10 bg-white/50 dark:bg-gray-800/40 backdrop-blur p-4 shadow-xl">
          <div class="h-5 w-48 rounded bg-white/60 dark:bg-gray-700/60 mb-2"></div>
          <div class="h-16 rounded-xl bg-white/50 dark:bg-gray-700/50"></div>
        </div>
      </div>

      {{-- ===== Columna derecha (calendario + KPIs) ===== --}}
      <div class="col-span-12 md:col-span-3 space-y-4">
        <div class="rounded-2xl border border-white/30 dark:border-white/10 bg-white/60 dark:bg-gray-800/40 backdrop-blur p-4 shadow-xl">
          <div class="h-5 w-40 rounded bg-white/70 dark:bg-gray-700/70 mb-3"></div>
          <div class="grid grid-cols-7 gap-1">
            @for ($i=0; $i<35; $i++)
              <div class="h-7 rounded-lg bg-white/50 dark:bg-gray-700/50"></div>
            @endfor
          </div>
        </div>

        <div class="grid grid-cols-3 gap-3">
          @for ($i=0; $i<3; $i++)
            <div class="rounded-2xl border border-white/30 dark:border-white/10 bg-white/60 dark:bg-gray-800/40 backdrop-blur p-3 shadow-xl">
              <div class="h-3 w-24 rounded bg-white/60 dark:bg-gray-700/60 mb-2"></div>
              <div class="h-6 w-8 rounded bg-purple-400/70"></div>
            </div>
          @endfor
        </div>
      </div>
    </div>

    {{-- Mini leyenda inferior --}}
    <div class="mt-5 text-center text-xs text-purple-900/80 dark:text-gray-300/80">
      Cargando panel de administración…
    </div>
  </div>
</div>
