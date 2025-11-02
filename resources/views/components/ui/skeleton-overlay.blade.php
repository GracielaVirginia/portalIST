<div id="pageSkeletonOverlay"
     class="fixed inset-0 z-[9999] hidden items-start justify-center
            bg-white/80 dark:bg-gray-950/80 backdrop-blur-sm p-4">

  <div class="w-full max-w-6xl mx-auto mt-6 animate-pulse">
    {{-- Header / Panel --}}
    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
      <div class="flex items-center gap-4">
        <div class="h-12 w-12 skeleton rounded-full"></div>
        <div class="flex-1 space-y-2">
          <div class="h-4 w-40 skeleton"></div>
          <div class="h-3 w-24 skeleton"></div>
        </div>
        <div class="h-6 w-24 skeleton"></div>
      </div>
    </div>

    {{-- KPIs --}}
    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
      @for($i=0;$i<4;$i++)
        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
          <div class="h-3 w-20 skeleton"></div>
          <div class="mt-3 h-7 w-24 skeleton"></div>
          <div class="mt-2 h-3 w-16 skeleton"></div>
        </div>
      @endfor
    </div>

    {{-- Sidebar + Cards --}}
    <div class="mt-4 grid grid-cols-1 lg:grid-cols-12 gap-4">
      <div class="lg:col-span-3 space-y-3">
        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
          <div class="h-4 w-40 skeleton"></div>
          <div class="mt-3 space-y-2">
            @for($i=0;$i<4;$i++)
              <div class="h-8 w-full skeleton"></div>
            @endfor
          </div>
        </div>
        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
          <div class="h-4 w-48 skeleton"></div>
          <div class="mt-3 h-24 w-full skeleton"></div>
        </div>
      </div>

      <div class="lg:col-span-9 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
        @for($i=0;$i<6;$i++)
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
    </div>
  </div>
</div>
