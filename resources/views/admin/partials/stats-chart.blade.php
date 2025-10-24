{{-- resources/views/admin/partials/stats-chart.blade.php --}}
<div id="statsChartPanel"
     class="mt-8"
     data-endpoint="{{ route('dashboard.stats.bySede') }}">

  <div class="flex items-start gap-6 flex-col md:flex-row">
    {{-- Checkboxes de sedes --}}
    <div class="min-w-[220px] bg-white dark:bg-slate-800 rounded-lg p-3 shadow">
      <h4 class="font-semibold mb-2 text-slate-700 dark:text-slate-100">Sedes</h4>
      <div id="chartSedes" class="flex flex-col gap-2 max-h-72 overflow-auto pr-2"></div>
    </div>

    {{-- Canvas del gráfico --}}
    <div class="flex-1 bg-white dark:bg-slate-800 rounded-lg p-3 shadow w-full">
      <h4 class="font-semibold mb-2 text-slate-700 dark:text-slate-100">
        Estudios (verde) vs Usuarios (rojo)
      </h4>
      <canvas id="chartSedesCanvas" height="320"></canvas>
    </div>
  </div>

  <p id="chartMsg" class="text-sm text-slate-600 dark:text-slate-300 mt-2"></p>

  {{-- estado compartido con el calendario --}}
  <input type="hidden" id="chartFecha" value="">
</div>
