{{-- resources/views/components/portal/widget-calendario-salud.blade.php --}}
@props([
  /**
   * series: [
   *   'tension' => [['fecha'=>'2025-10-20','sistolica'=>130,'diastolica'=>85], ...],
   *   'glucosa' => [['fecha'=>'2025-10-20','valor'=>110], ...],
   *   'peso'    => [['fecha'=>'2025-10-20','valor'=>78.4], ...],
   * ]
   */
  'series' => ['tension'=>[], 'glucosa'=>[], 'peso'=>[]],
  // URL (web) para POST de una nueva medición. Ej: route('portal.controles.store')
  'storeUrl' => '#',
  // Título del widget
  'titulo' => 'Controles de salud',
])

@php
  // Encabezados de semana (Lunes a Domingo)
  $weekdays = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
@endphp

<section
  x-data="calendarioSalud(@js($series))"
  x-init="init()"
  class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm"
>
  {{-- Header --}}
  <header class="flex items-center justify-between px-4 sm:px-5 py-3 border-b border-gray-200 dark:border-gray-700">
    <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100">
      {{ $titulo }}
    </h3>

    <div class="flex items-center gap-2">
      <button type="button" @click="prevMonth()"
              class="rounded-xl border border-gray-200 dark:border-gray-700 px-2 py-1 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800">&larr;</button>
      <div class="min-w-[140px] text-center text-sm font-semibold text-gray-800 dark:text-gray-100" x-text="monthLabel"></div>
      <button type="button" @click="nextMonth()"
              class="rounded-xl border border-gray-200 dark:border-gray-700 px-2 py-1 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800">&rarr;</button>
      <button type="button" @click="goToday()"
              class="ml-2 rounded-xl border border-purple-900/20 dark:border-purple-300/20 bg-purple-900 text-white px-3 py-1.5 text-xs font-semibold hover:opacity-90">
        Hoy
      </button>
    </div>
  </header>

  {{-- Leyenda --}}
  <div class="px-4 sm:px-5 py-3 flex flex-wrap items-center gap-3 text-xs">
    <span class="inline-flex items-center gap-2">
      <span class="h-2 w-2 rounded-full bg-purple-900"></span>
      <span class="text-gray-700 dark:text-gray-200">Tensión</span>
    </span>
    <span class="inline-flex items-center gap-2">
      <span class="h-2 w-2 rounded-full bg-emerald-600"></span>
      <span class="text-gray-700 dark:text-gray-200">Glucosa</span>
    </span>
    <span class="inline-flex items-center gap-2">
      <span class="h-2 w-2 rounded-full bg-sky-600"></span>
      <span class="text-gray-700 dark:text-gray-200">Peso</span>
    </span>
  </div>

  {{-- Cabecera de semana --}}
  <div class="grid grid-cols-7 gap-px border-t border-b border-gray-200 dark:border-gray-700 bg-gray-200 dark:bg-gray-700">
    @foreach ($weekdays as $wd)
      <div class="bg-white dark:bg-gray-900 text-center py-2 text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $wd }}</div>
    @endforeach
  </div>

  {{-- Grilla de días --}}
  <div class="grid grid-cols-7 gap-px border-b border-gray-200 dark:border-gray-700 bg-gray-200 dark:bg-gray-700">
    <template x-for="day in days" :key="day.key">
      <div class="relative min-h-[92px] bg-white dark:bg-gray-900 p-2">
        {{-- Número de día --}}
        <div class="flex items-center justify-between">
          <div class="text-xs font-semibold" :class="day.inMonth ? 'text-gray-800 dark:text-gray-100' : 'text-gray-400 dark:text-gray-500'">
            <span x-text="day.date.getDate()"></span>
          </div>
          <div x-show="isToday(day.date)" x-cloak class="text-[10px] rounded-md px-1.5 py-0.5 bg-purple-900 text-white">
            Hoy
          </div>
        </div>

        {{-- Badges de mediciones (UNA sola class, sin style inline) --}}
        <div class="mt-2 space-y-1">
          <template x-if="day.data.tension">
            <div class="flex items-center justify-between text-[11px] rounded-md px-1.5 py-0.5 border border-purple-900/30
                        bg-purple-50 dark:bg-purple-950/30 text-purple-900 dark:text-purple-100">
              <span>TA</span>
              <span x-text="`${day.data.tension.sistolica}/${day.data.tension.diastolica}`"></span>
            </div>
          </template>

          <template x-if="day.data.glucosa">
            <div class="flex items-center justify-between text-[11px] rounded-md px-1.5 py-0.5 border border-emerald-600/30
                        bg-emerald-50 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-100">
              <span>GLU</span>
              <span x-text="day.data.glucosa.valor"></span>
            </div>
          </template>

          <template x-if="day.data.peso">
            <div class="flex items-center justify-between text-[11px] rounded-md px-1.5 py-0.5 border border-sky-600/30
                        bg-sky-50 dark:bg-sky-900/30 text-sky-800 dark:text-sky-100">
              <span>Peso</span>
              <span x-text="day.data.peso.valor"></span>
            </div>
          </template>
        </div>

        {{-- Botón agregar --}}
        <div class="absolute bottom-1 right-1">
          <button type="button" @click="openModal(day.date)"
                  class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-950 hover:bg-gray-50 dark:hover:bg-gray-900
                         text-xs px-2 py-1 text-gray-700 dark:text-gray-200">
            Agregar
          </button>
        </div>
      </div>
    </template>
  </div>

  {{-- Modal para registrar medición --}}
  <div x-show="modal.open" x-transition x-cloak
       class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div @click.outside="closeModal()"
         class="w-full max-w-md rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-5 shadow-xl">
      <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">
        Registrar control — <span x-text="modal.dateLabel"></span>
      </h4>

      <form method="POST" action="{{ $storeUrl }}" class="mt-4 space-y-3">
        @csrf
        <input type="hidden" name="fecha" :value="modal.dateIso">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          {{-- Tensión arterial --}}
          <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Tensión (mmHg)</label>
            <div class="flex items-center gap-2">
              <input type="number" name="tension_sistolica" min="50" max="250" placeholder="Sist."
                     class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 text-sm"
                     x-model.number="form.tension_sis">
              <input type="number" name="tension_diastolica" min="30" max="150" placeholder="Diast."
                     class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 text-sm"
                     x-model.number="form.tension_dia">
            </div>
          </div>

          {{-- Glucosa --}}
          <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Glucosa (mg/dL)</label>
            <input type="number" name="glucosa" min="40" max="600" placeholder="Ej: 110"
                   class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 text-sm"
                   x-model.number="form.glucosa">
          </div>

          {{-- Peso --}}
          <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Peso (kg)</label>
            <input type="number" step="0.1" min="20" max="400" name="peso" placeholder="Ej: 78.4"
                   class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 text-sm"
                   x-model.number="form.peso">
          </div>

          {{-- (Opcional) Observaciones --}}
          <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Notas</label>
            <input type="text" name="nota" maxlength="120" placeholder="Opcional"
                   class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 text-sm"
                   x-model="form.nota">
          </div>
        </div>

        <div class="mt-4 flex items-center justify-end gap-2">
          <button type="button" @click="closeModal()"
                  class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-950 hover:bg-gray-50 dark:hover:bg-gray-900
                         px-3 py-1.5 text-sm text-gray-700 dark:text-gray-200">Cancelar</button>
          <button type="submit"
                  class="rounded-xl border border-purple-900/20 dark:border-purple-300/20 bg-purple-900 text-white px-4 py-2 text-sm font-semibold hover:opacity-90">
            Guardar
          </button>
        </div>
      </form>
    </div>
  </div>
</section>

{{-- Alpine store / helpers --}}
<script>
  function calendarioSalud(initialSeries) {
    return {
      // Estado base
      today: new Date(),
      cursor: new Date(), // mes visible
      days: [],
      dataByDate: {}, // { 'YYYY-MM-DD': { tension:{...}, glucosa:{...}, peso:{...} } }
      modal: { open: false, dateIso: '', dateLabel: '' },
      form: { tension_sis: null, tension_dia: null, glucosa: null, peso: null, nota: '' },

      get monthLabel() {
        return this.cursor.toLocaleDateString('es-CL', { month: 'long', year: 'numeric' });
      },

      init() {
        // Normaliza series por fecha (último valor del día)
        const map = {};
        const iso = d => (new Date(d)).toISOString().slice(0,10);
        (initialSeries.tension || []).forEach(r => {
          const k = iso(r.fecha);
          map[k] = map[k] || {};
          map[k].tension = { sistolica: Number(r.sistolica), diastolica: Number(r.diastolica) };
        });
        (initialSeries.glucosa || []).forEach(r => {
          const k = iso(r.fecha);
          map[k] = map[k] || {};
          map[k].glucosa = { valor: Number(r.valor) };
        });
        (initialSeries.peso || []).forEach(r => {
          const k = iso(r.fecha);
          map[k] = map[k] || {};
          map[k].peso = { valor: Number(r.valor) };
        });
        this.dataByDate = map;
        this.build();
      },

      build() {
        const year = this.cursor.getFullYear();
        const month = this.cursor.getMonth(); // 0..11
        const first = new Date(year, month, 1);
        // lunes=0 ... domingo=6
        const startIdx = (first.getDay() + 6) % 7;
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        const cells = [];
        for (let i = 0; i < startIdx; i++) {
          const d = new Date(year, month, -startIdx + i + 1);
          cells.push(this.makeCell(d, false));
        }
        for (let d = 1; d <= daysInMonth; d++) {
          cells.push(this.makeCell(new Date(year, month, d), true));
        }
        while (cells.length % 7 !== 0) {
          const last = cells[cells.length - 1].date;
          const next = new Date(last.getFullYear(), last.getMonth(), last.getDate() + 1);
          cells.push(this.makeCell(next, false));
        }
        this.days = cells;
      },

      makeCell(date, inMonth) {
        const key = date.toISOString().slice(0,10);
        return { key: key + (inMonth ? '' : '-o'), date, inMonth, data: this.dataByDate[key] || {} };
      },

      isToday(d) {
        const t = this.today;
        return d.getFullYear() === t.getFullYear()
          && d.getMonth() === t.getMonth()
          && d.getDate() === t.getDate();
      },

      prevMonth() { this.cursor = new Date(this.cursor.getFullYear(), this.cursor.getMonth() - 1, 1); this.build(); },
      nextMonth() { this.cursor = new Date(this.cursor.getFullYear(), this.cursor.getMonth() + 1, 1); this.build(); },
      goToday()   { this.cursor = new Date(); this.build(); },

      openModal(date) {
        const iso = date.toISOString().slice(0,10);
        this.modal.open = true;
        this.modal.dateIso = iso;
        this.modal.dateLabel = date.toLocaleDateString('es-CL', { day: '2-digit', month: 'short', year: 'numeric' });
        const d = this.dataByDate[iso] || {};
        this.form.tension_sis = d.tension?.sistolica ?? null;
        this.form.tension_dia = d.tension?.diastolica ?? null;
        this.form.glucosa     = d.glucosa?.valor ?? null;
        this.form.peso        = d.peso?.valor ?? null;
        this.form.nota        = '';
      },
      closeModal() { this.modal.open = false; },
    }
  }
</script>
