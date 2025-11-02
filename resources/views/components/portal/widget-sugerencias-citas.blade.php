{{-- resources/views/components/portal/widget-sugerencias-citas.blade.php --}}
@props([
  'sugerencias' => [],
  'titulo' => 'Sugerencias para tu cuidado',
  // Rutas para datos y PDF
  'seriesUrl' => route('controles.series'),
  'pdfUrl'    => route('controles.pdf'),
])

@php
  $list = collect($sugerencias);

  $iconSvg = function ($name) {
    switch ($name) {
      case 'lab': return '<path d="M6 2h2v6l4 8v6H8v-4H6v4H2v-6l4-8V2zM14 2h8v2h-8V2zm0 4h8v2h-8V6z"/>';
      case 'med': return '<path d="M3 3h18v6H3V3zm2 2v2h14V5H5zm-2 8h18v8H3v-8zm2 2v4h14v-4H5z"/>';
      case 'vac': return '<path d="M7 2h2v3h6V2h2v3h2v2h-2v3h-2V7H9v3H7V7H5V5h2V2zM5 13h14v2H5v-2zm0 4h14v3H5v-3z"/>';
      case 'heart': return '<path d="M12 21s-7.5-4.35-9.5-8A5.5 5.5 0 0 1 12 6a5.5 5.5 0 0 1 9.5 7c-2 3.65-9.5 8-9.5 8z"/>';
      default: return '<path d="M7 2h2v2h6V2h2v2h3v18H4V4h3V2zm13 6H4v12h16V8z"/>';
    }
  };

  $pillClass = fn($critico) =>
    $critico
      ? 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-100 border-rose-300/60 dark:border-rose-700/50'
      : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-100 border-emerald-300/60 dark:border-emerald-700/50';
@endphp

<section
  x-data="{ showCalc:false }"
  class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm"
>
  <header class="flex items-center justify-between px-4 sm:px-5 py-3 border-b border-gray-200 dark:border-gray-700">
    <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100">
      {{ $titulo }}
    </h3>

    <div class="flex items-center gap-2">
      <button type="button"
              @click="showCalc = true; $nextTick(()=>window.dispatchEvent(new Event('renderChart')))"
              class="inline-flex items-center gap-2 rounded-xl bg-purple-900 text-white px-3 py-1.5 text-xs sm:text-sm font-semibold hover:opacity-90">
        Calcular resultados
      </button>

      <a href="{{ route('portal.citas.index') }}"
         class="inline-flex items-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700
                px-3 py-1.5 text-xs sm:text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800">
        Ver citas
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 4l1.41 1.41L8.83 10H20v2H8.83l4.58 4.59L12 18l-8-8 8-8z"/>
        </svg>
      </a>
    </div>
  </header>

  @if($list->isEmpty())
    <div class="px-4 sm:px-5 py-6">
      <div class="rounded-xl border border-dashed border-purple-900/30 dark:border-purple-300/30
                  bg-purple-50 dark:bg-purple-950/30 p-4 text-sm text-purple-900 dark:text-purple-100">
        Por ahora no hay sugerencias. Cuando detectemos controles pendientes o chequeos preventivos, aparecerán aquí.
      </div>
    </div>
  @else
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4 p-4 sm:p-5">
      @foreach ($list as $sg)
        @php
          $motivo  = $sg['motivo'] ?? 'Control';
          $cuando  = $sg['cuando'] ?? '';
          $esp     = strtoupper((string)($sg['especialidad'] ?? ''));
          $url     = $sg['cta_url'] ?? '#';
          $critico = (bool)($sg['critico'] ?? false);
          $detalle = $sg['detalle'] ?? null;
          $icon    = $sg['icon'] ?? 'calendar';
        @endphp

        <article class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm flex flex-col">
          <div class="flex items-start justify-between gap-3">
            <div class="inline-flex items-center gap-2">
              <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-purple-900 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">{!! $iconSvg($icon) !!}</svg>
              </span>
              <div>
                <h4 class="text-sm sm:text-base font-semibold text-gray-900 dark:text-gray-100 leading-snug">
                  {{ $motivo }}
                </h4>
                <div class="text-xs text-gray-600 dark:text-gray-300">
                  {{ $cuando }}
                </div>
              </div>
            </div>

            @if($esp)
              <span class="inline-flex items-center gap-1 rounded-lg border px-2 py-0.5 text-xs font-semibold
                           bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200 border-gray-300/60 dark:border-gray-700/50">
                {{ $esp }}
              </span>
            @endif
          </div>

          @if($detalle)
            <p class="mt-3 text-xs text-gray-600 dark:text-gray-300">{{ $detalle }}</p>
          @endif

          <div class="mt-4 flex items-center justify-between">
            <span class="inline-flex items-center gap-1 rounded-md border px-2 py-0.5 text-[11px] font-semibold {{ $pillClass($critico) }}">
              {{ $critico ? 'Prioridad alta' : 'Recomendado' }}
            </span>

            <a href="{{ $url }}"
               class="inline-flex items-center gap-2 rounded-xl border border-purple-900/20 dark:border-purple-300/20
                      bg-purple-900 text-white hover:opacity-90 px-3 py-1.5 text-xs font-semibold">
              Reservar
            </a>
          </div>
        </article>
      @endforeach
    </div>
  @endif

  {{-- ===================== MODAL: Gráfico + PDF ===================== --}}
  <div x-show="showCalc" x-cloak
       class="fixed inset-0 z-[60] bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
       @keydown.escape.window="showCalc=false">
    <div class="w-full max-w-3xl rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-2xl overflow-hidden">
      <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Evolución de controles</h3>
        <button class="rounded-lg px-2 py-1 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                @click="showCalc=false">Cerrar</button>
      </div>

      <div class="p-4 space-y-4"
           x-data="chartRenderer('{{ $seriesUrl }}', '{{ $pdfUrl }}')"
           x-init="init()">

        {{-- Rango de fechas --}}
        <form x-ref="pdfForm" :action="pdfUrl" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
          @csrf
          <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Desde</label>
            <input type="date" name="start_date" x-model="range.start"
                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-950
                          text-sm text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-400/60">
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Hasta</label>
            <input type="date" name="end_date" x-model="range.end"
                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-950
                          text-sm text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-400/60">
          </div>

          <div class="flex gap-2">
            <button type="button"
                    @click="loadSeries().then(()=>window.dispatchEvent(new Event('renderChart')))"
                    class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-950
                           px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-900">
              Aplicar rango
            </button>
          </div>

          {{-- Campo oculto para el gráfico --}}
          <input type="hidden" name="chart_base64" x-ref="chart64">
        </form>

        {{-- Gráfico --}}
        <canvas id="healthChart" height="180" class="w-full"></canvas>

        {{-- Generar PDF --}}
        <div class="flex justify-end">
          <button type="button"
                  @click.prevent="exportChart().then(img => { $refs.chart64.value = img; $refs.pdfForm.submit(); })"
                  class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 text-white px-3 py-2 text-sm font-semibold hover:opacity-90">
            Generar PDF
          </button>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Chart.js (si no está en tu layout) --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  function chartRenderer(seriesUrl, pdfUrl) {
    return {
      chart: null,
      seriesUrl,
      pdfUrl,
      series: { tension: [], glucosa: [], peso: [] },
      range: {
        start: (() => { const d = new Date(); d.setDate(d.getDate()-30); return d.toISOString().slice(0,10); })(),
        end:   (new Date()).toISOString().slice(0,10),
      },

      async init() {
        await this.loadSeries();
        const ctx = document.getElementById('healthChart').getContext('2d');
        const draw = () => {
          const { labels, dataGlu, dataPeso, dataTAS, dataTAD } = this.seriesToDatasets(this.series);
          if (this.chart) this.chart.destroy();
          this.chart = new Chart(ctx, {
            type: 'line',
            data: {
              labels,
              datasets: [
                { label: 'Glucosa (mg/dL)', data: dataGlu },
                { label: 'Peso (kg)',       data: dataPeso },
                { label: 'Tensión Sistólica',  data: dataTAS },
                { label: 'Tensión Diastólica', data: dataTAD },
              ]
            },
            options: {
              responsive: true,
              plugins: { legend: { position: 'bottom' } },
              scales: { x: { ticks: { autoSkip: true } } }
            }
          });
        };
        window.addEventListener('renderChart', draw);
        window.dispatchEvent(new Event('renderChart'));
      },

      async loadSeries() {
        try {
          const qs = new URLSearchParams({
            start_date: this.range.start || '',
            end_date:   this.range.end   || '',
          }).toString();
          const res = await fetch(`${this.seriesUrl}?${qs}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
          if (!res.ok) throw new Error('Error cargando series');
          this.series = await res.json();
        } catch (e) {
          console.error(e);
          this.series = { tension: [], glucosa: [], peso: [] };
        }
      },

      seriesToDatasets(s) {
        const fechas = Array.from(new Set([
          ...(s.glucosa||[]).map(r=>r.fecha),
          ...(s.peso||[]).map(r=>r.fecha),
          ...(s.tension||[]).map(r=>r.fecha),
        ])).sort();

        return {
          labels: fechas,
          dataGlu: fechas.map(f => { const r=(s.glucosa||[]).find(x=>x.fecha===f); return r?Number(r.valor):null; }),
          dataPeso: fechas.map(f => { const r=(s.peso||[]).find(x=>x.fecha===f); return r?Number(r.valor):null; }),
          dataTAS:  fechas.map(f => { const r=(s.tension||[]).find(x=>x.fecha===f); return r?Number(r.sistolica):null; }),
          dataTAD:  fechas.map(f => { const r=(s.tension||[]).find(x=>x.fecha===f); return r?Number(r.diastolica):null; }),
        };
      },

      async exportChart() {
        if (!this.chart) return '';
        return this.chart.toBase64Image();
      }
    }
  }
</script>
