<div id="pageSkeletonOverlay"
     class="fixed inset-0 z-[9999] hidden items-start justify-center
            bg-white/80 dark:bg-gray-950/80 backdrop-blur-sm p-4">

  <div class="w-full max-w-6xl mx-auto mt-6 animate-pulse">
    {{-- ===== Header / Panel del paciente ===== --}}
    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 lg:p-5 shadow-sm">
      <div class="flex items-start justify-between gap-4">
        {{-- Izquierda: avatar + datos --}}
        <div class="flex items-center gap-4 min-w-0">
          <div class="h-12 w-12 skeleton rounded-full shrink-0"></div>
          <div class="space-y-2 min-w-0">
            <div class="h-4 w-56 skeleton"></div>
            <div class="flex flex-wrap gap-2">
              <div class="h-3 w-28 skeleton rounded-full"></div>
              <div class="h-3 w-16 skeleton rounded-full"></div>
              <div class="h-3 w-14 skeleton rounded-full"></div>
              <div class="h-3 w-24 skeleton rounded-full"></div>
            </div>
          </div>
        </div>

        {{-- Derecha: botones + logo --}}
        <div class="flex items-center gap-3">
          <div class="hidden md:flex items-center gap-2">
            <div class="h-6 w-24 skeleton rounded-full"></div>
            <div class="h-6 w-24 skeleton rounded-full"></div>
            <div class="h-6 w-20 skeleton rounded-full"></div>
          </div>
          <div class="h-8 w-28 sm:w-36 skeleton rounded-xl"></div>
        </div>
      </div>
    </div>

    {{-- ===== KPIs (Próximas citas / Resultados / Licencias / Alertas) ===== --}}
    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
      @foreach (['Próximas citas','Resultados','Licencias','Alertas'] as $i)
        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
          <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
              <div class="h-8 w-8 skeleton rounded-xl shrink-0"></div>
              <div class="space-y-2 min-w-0">
                <div class="h-3 w-28 skeleton"></div>
                <div class="h-3 w-16 skeleton"></div>
              </div>
            </div>
            <div class="h-6 w-8 skeleton rounded-full"></div>
          </div>
        </div>
      @endforeach
    </div>

    {{-- ===== Sidebar + Main ===== --}}
    <div class="mt-4 grid grid-cols-1 lg:grid-cols-12 gap-4">
      {{-- --- Sidebar --- --}}
      <aside class="lg:col-span-3 space-y-3">
        {{-- Inicio + Todos mis resultados --}}
        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
          {{-- "Inicio" --}}
          <div class="flex items-center gap-2">
            <div class="h-3 w-16 skeleton"></div>
          </div>

          {{-- "Todos mis resultados" --}}
          <div class="mt-4 flex items-center justify-between">
            <div class="h-3 w-40 skeleton"></div>
            <div class="h-5 w-6 skeleton rounded-full"></div>
          </div>

          {{-- Sub-items --}}
          <div class="mt-3 space-y-2">
            @foreach (['Ver todos','Endocrinología','Laboratorio','Radiografía'] as $item)
              <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2 min-w-0">
                  <div class="h-2.5 w-2.5 skeleton rounded-full shrink-0"></div>
                  <div class="h-3 w-36 skeleton"></div>
                </div>
                <div class="h-5 w-5 skeleton rounded-full shrink-0"></div>
              </div>
            @endforeach
          </div>
        </div>

        {{-- Licencias --}}
        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <div class="h-3 w-20 skeleton"></div>
            <div class="h-5 w-6 skeleton rounded-full"></div>
          </div>
          <div class="mt-3 h-16 w-full skeleton rounded-xl"></div>
        </div>

        {{-- Recetas --}}
        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <div class="h-3 w-16 skeleton"></div>
            <div class="h-5 w-6 skeleton rounded-full"></div>
          </div>
          <div class="mt-3 h-12 w-full skeleton rounded-xl"></div>
        </div>
      </aside>

      {{-- --- Main --- --}}
      <main class="lg:col-span-9 space-y-4">
        {{-- Banner / noticia grande --}}
        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">
          <div class="w-full aspect-[21/9] skeleton"></div>
          <div class="p-4 sm:p-5">
            <div class="h-4 w-3/5 skeleton"></div>
            <div class="mt-3 space-y-2">
              <div class="h-3 w-11/12 skeleton"></div>
              <div class="h-3 w-10/12 skeleton"></div>
              <div class="h-3 w-9/12 skeleton"></div>
            </div>
            <div class="mt-4 h-8 w-24 skeleton rounded-xl"></div>
          </div>
        </div>

        {{-- (Opcional) tarjetas secundarias para completar grilla --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
          @for($i=0;$i<3;$i++)
            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
              <div class="flex items-start justify-between gap-3">
                <div class="space-y-2 w-full">
                  <div class="h-4 w-3/5 skeleton"></div>
                  <div class="h-3 w-24 skeleton"></div>
                </div>
                <div class="h-5 w-16 skeleton"></div>
              </div>
              <div class="mt-4 space-y-2">
                <div class="h-3 w-11/12 skeleton"></div>
                <div class="h-3 w-4/5 skeleton"></div>
                <div class="h-8 w-24 skeleton"></div>
              </div>
            </div>
          @endfor
        </div>
      </main>
    </div>
  </div>
</div>
