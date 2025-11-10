{{-- resources/views/components/portal/widget-calendario-salud.blade.php --}}
@props([
    /**
     * series: [
     *   'tension' => [['fecha'=>'2025-10-20','sistolica'=>130,'diastolica'=>85], ...],
     *   'glucosa' => [['fecha'=>'2025-10-20','valor'=>110], ...],
     *   'peso'    => [['fecha'=>'2025-10-20','valor'=>78.4], ...],
     * ]
     */
    'series' => ['tension' => [], 'glucosa' => [], 'peso' => []],
    // (Ya no se usa para guardar — se mantiene por compatibilidad)
    'storeUrl' => '#',
    // Título del widget
    'titulo' => 'Controles de salud',
])

@php
    $weekdays = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
@endphp

<section x-data="calendarioSalud(
    @js($series), {
        routes: {
            tension: '{{ route('controles.tension.store') }}',
            glucosa: '{{ route('controles.glucosa.store') }}',
            peso: '{{ route('controles.peso.store') }}',
        }
    }
)" x-init="init()"
    class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm">
    {{-- Header --}}
    <header class="flex items-center justify-between px-4 sm:px-5 py-3 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100">
            {{ $titulo }}
        </h3>

        <div class="flex items-center gap-2">
            <button type="button" @click="prevMonth()"
                class="rounded-xl border border-gray-200 dark:border-gray-700 px-2 py-1 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800">&larr;</button>
            <div class="min-w-[140px] text-center text-sm font-semibold text-gray-800 dark:text-gray-100"
                x-text="monthLabel"></div>
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
    <div
        class="grid grid-cols-7 gap-px border-t border-b border-gray-200 dark:border-gray-700 bg-gray-200 dark:bg-gray-700">
        @foreach ($weekdays as $wd)
            <div
                class="bg-white dark:bg-gray-900 text-center py-2 text-xs font-semibold text-gray-500 dark:text-gray-400">
                {{ $wd }}</div>
        @endforeach
    </div>

    {{-- Grilla de días --}}
    <div class="grid grid-cols-7 gap-px border-b border-gray-200 dark:border-gray-700 bg-gray-200 dark:bg-gray-700">
        <template x-for="day in days" :key="day.key">
            <div class="relative min-h-[92px] bg-white dark:bg-gray-900 p-2" :class="day.isPast ? 'opacity-60' : ''">
                {{-- Número de día --}}
                <div class="flex items-center justify-between">
                    <div class="text-xs font-semibold"
                        :class="day.inMonth ? 'text-gray-800 dark:text-gray-100' : 'text-gray-400 dark:text-gray-500'">
                        <span x-text="day.date.getDate()"></span>
                    </div>
                    <div x-show="isToday(day.date)" x-cloak
                        class="text-[10px] rounded-md px-1.5 py-0.5 bg-purple-900 text-white">
                        Hoy
                    </div>
                </div>

                {{-- Badges de mediciones --}}
                <div class="mt-2 space-y-1">
                    <template x-if="day.data.tension">
                        <div
                            class="flex items-center justify-between text-[11px] rounded-md px-1.5 py-0.5 border border-purple-900/30
                        bg-purple-50 dark:bg-purple-950/30 text-purple-900 dark:text-purple-100">
                            <span>TA</span>
                            <span x-text="`${day.data.tension.sistolica}/${day.data.tension.diastolica}`"></span>
                        </div>
                    </template>

                    <template x-if="day.data.glucosa">
                        <div
                            class="flex items-center justify-between text-[11px] rounded-md px-1.5 py-0.5 border border-emerald-600/30
                        bg-emerald-50 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-100">
                            <span>GLU</span>
                            <span x-text="day.data.glucosa.valor"></span>
                        </div>
                    </template>

                    <template x-if="day.data.peso">
                        <div
                            class="flex items-center justify-between text-[11px] rounded-md px-1.5 py-0.5 border border-sky-600/30
                        bg-sky-50 dark:bg-sky-900/30 text-sky-800 dark:text-sky-100">
                            <span>Peso</span>
                            <span x-text="day.data.peso.valor"></span>
                        </div>
                    </template>
                </div>

                {{-- Botón agregar --}}
                <div class="absolute bottom-1 right-1">
                    <button type="button" @click="!day.isPast && openModal(day.date)"
                        :class="day.isPast ?
                            'opacity-40 pointer-events-none cursor-not-allowed' :
                            'hover:bg-gray-50 dark:hover:bg-gray-900'"
                        class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-950
                         text-xs px-2 py-1 text-gray-700 dark:text-gray-200">
                        Agregar
                    </button>
                </div>
            </div>
        </template>
    </div>

    {{-- Modal para registrar medición (autosave por campo) --}}
    <div x-show="modal.open" x-transition x-cloak
        class="fixed inset-0 z-[60] bg-black/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.outside="closeModal()"
            class="w-full max-w-md rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-5 shadow-2xl ring-1 ring-black/5">
            <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                Registrar control — <span x-text="modal.dateLabel"></span>
            </h4>

            <form x-ref="form" class="mt-4 space-y-3" @keydown.window.ctrl.s.prevent="saveAll()">
                @csrf
                <input type="hidden" name="fecha" :value="modal.dateIso">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    {{-- Tensión arterial --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Tensión
                            (mmHg)</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="tension_sistolica" min="50" max="250"
                                placeholder="Sist."
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600
       bg-white dark:bg-gray-950 text-sm text-gray-800 dark:text-gray-100
       focus:outline-none focus:ring-2 focus:ring-purple-400/60 focus:border-purple-500"
                                x-model.number="form.tension_sis" @input.debounce.600ms="maybeSaveTension()"
                                @blur="maybeSaveTension(true)">
                            <input type="number" name="tension_diastolica" min="30" max="150"
                                placeholder="Diast."
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600
       bg-white dark:bg-gray-950 text-sm text-gray-800 dark:text-gray-100
       focus:outline-none focus:ring-2 focus:ring-purple-400/60 focus:border-purple-500"
                                x-model.number="form.tension_dia" @input.debounce.600ms="maybeSaveTension()"
                                @blur="maybeSaveTension(true)">
                        </div>
                        <div class="mt-1 text-[11px]"
                            :class="{
                                'text-gray-500': states.tension==='idle',
                                'text-amber-600': states.tension==='pending',
                                'text-emerald-600': states.tension==='saved',
                                'text-rose-600': states.tension==='error'
                            }">
                            <template x-if="states.tension==='pending'">Guardando tensión…</template>
                            <template x-if="states.tension==='saved'">Tensión guardada ✓</template>
                            <template x-if="states.tension==='error'">Error al guardar tensión</template>
                            <template x-if="states.tension==='idle'">Escribe ambos valores para guardar</template>
                        </div>
                    </div>

                    {{-- Glucosa --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Glucosa
                            (mg/dL)</label>
                        <input type="number" name="glucosa" min="40" max="600" placeholder="Ej: 110"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600
       bg-white dark:bg-gray-950 text-sm text-gray-800 dark:text-gray-100
       focus:outline-none focus:ring-2 focus:ring-purple-400/60 focus:border-purple-500"
                            x-model.number="form.glucosa" @input.debounce.600ms="maybeSaveGlucosa()"
                            @blur="maybeSaveGlucosa(true)">
                        <div class="mt-1 text-[11px]"
                            :class="{
                                'text-gray-500': states.glucosa==='idle',
                                'text-amber-600': states.glucosa==='pending',
                                'text-emerald-600': states.glucosa==='saved',
                                'text-rose-600': states.glucosa==='error'
                            }">
                            <template x-if="states.glucosa==='pending'">Guardando glucosa…</template>
                            <template x-if="states.glucosa==='saved'">Glucosa guardada ✓</template>
                            <template x-if="states.glucosa==='error'">Error al guardar glucosa</template>
                            <template x-if="states.glucosa==='idle'">Se guarda automáticamente</template>
                        </div>
                    </div>

                    {{-- Peso --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Peso
                            (kg)</label>
                        <input type="number" step="0.1" min="20" max="400" name="peso"
                            placeholder="Ej: 78.4"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600
       bg-white dark:bg-gray-950 text-sm text-gray-800 dark:text-gray-100
       focus:outline-none focus:ring-2 focus:ring-purple-400/60 focus:border-purple-500"
                            x-model.number="form.peso" @input.debounce.600ms="maybeSavePeso()"
                            @blur="maybeSavePeso(true)">
                        <div class="mt-1 text-[11px]"
                            :class="{
                                'text-gray-500': states.peso==='idle',
                                'text-amber-600': states.peso==='pending',
                                'text-emerald-600': states.peso==='saved',
                                'text-rose-600': states.peso==='error'
                            }">
                            <template x-if="states.peso==='pending'">Guardando peso…</template>
                            <template x-if="states.peso==='saved'">Peso guardado ✓</template>
                            <template x-if="states.peso==='error'">Error al guardar peso</template>
                            <template x-if="states.peso==='idle'">Se guarda automáticamente</template>
                        </div>
                    </div>

                    {{-- (Opcional) Observaciones --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Notas</label>
                        <input type="text" name="nota" maxlength="120" placeholder="Opcional"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600
       bg-white dark:bg-gray-950 text-sm text-gray-800 dark:text-gray-100
       focus:outline-none focus:ring-2 focus:ring-purple-400/60 focus:border-purple-500"
                            x-model="form.nota">
                        <p class="mt-1 text-[11px] text-gray-500">La nota se envía junto con la métrica que estés
                            guardando.</p>
                    </div>
                </div>

                {{-- (Se quitaron los tres botones Guardar) --}}
                <div class="mt-2 text-right">

                    <button type="button" @click="closeModal()"
                  class="px-4 py-2 bg-purple-900 hover:bg-purple-800 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400/60 transition duration-150 ease-in-out">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

{{-- Alpine store / helpers --}}
<script>
    function calendarioSalud(initialSeries, config = {}) {
        return {
            // Estado base
            today: new Date(),
            cursor: new Date(),
            days: [],
            dataByDate: {}, // { 'YYYY-MM-DD': { tension:{...}, glucosa:{...}, peso:{...} } }
            modal: {
                open: false,
                dateIso: '',
                dateLabel: ''
            },
            form: {
                tension_sis: null,
                tension_dia: null,
                glucosa: null,
                peso: null,
                nota: ''
            },
            states: {
                tension: 'idle',
                glucosa: 'idle',
                peso: 'idle'
            }, // idle|pending|saved|error
            routes: config.routes || {},

            // etiqueta de mes
            get monthLabel() {
                return this.cursor.toLocaleDateString('es-CL', {
                    month: 'long',
                    year: 'numeric'
                });
            },

            // normalizar a YYYY-MM-DD
            toIso(d) {
                const d0 = new Date(d.getFullYear(), d.getMonth(), d.getDate());
                return d0.toISOString().slice(0, 10);
            },

            init() {
                // Normaliza series por fecha (último valor del día)
                const map = {};
                const iso = (d) => (new Date(d)).toISOString().slice(0, 10);

                (initialSeries.tension || []).forEach(r => {
                    const k = iso(r.fecha);
                    map[k] = map[k] || {};
                    map[k].tension = {
                        sistolica: Number(r.sistolica),
                        diastolica: Number(r.diastolica)
                    };
                });
                (initialSeries.glucosa || []).forEach(r => {
                    const k = iso(r.fecha);
                    map[k] = map[k] || {};
                    map[k].glucosa = {
                        valor: Number(r.valor)
                    };
                });
                (initialSeries.peso || []).forEach(r => {
                    const k = iso(r.fecha);
                    map[k] = map[k] || {};
                    map[k].peso = {
                        valor: Number(r.valor)
                    };
                });

                this.dataByDate = map;
                this.build();
            },

            build() {
                const year = this.cursor.getFullYear();
                const month = this.cursor.getMonth();
                const first = new Date(year, month, 1);
                const startIdx = (first.getDay() + 6) % 7; // lunes=0 ... domingo=6
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                const todayIso = this.toIso(this.today);

                const cells = [];
                for (let i = 0; i < startIdx; i++) {
                    const d = new Date(year, month, -startIdx + i + 1);
                    cells.push(this.makeCell(d, false, todayIso));
                }
                for (let d = 1; d <= daysInMonth; d++) {
                    cells.push(this.makeCell(new Date(year, month, d), true, todayIso));
                }
                while (cells.length % 7 !== 0) {
                    const last = cells[cells.length - 1].date;
                    const next = new Date(last.getFullYear(), last.getMonth(), last.getDate() + 1);
                    cells.push(this.makeCell(next, false, todayIso));
                }
                this.days = cells;
            },

            makeCell(date, inMonth, todayIso) {
                const key = date.toISOString().slice(0, 10);
                const iso = this.toIso(date);
                const isPast = iso < todayIso;
                return {
                    key: key + (inMonth ? '' : '-o'),
                    date,
                    inMonth,
                    isPast,
                    data: this.dataByDate[key] || {}
                };
            },

            isToday(d) {
                const t = this.today;
                return d.getFullYear() === t.getFullYear() &&
                    d.getMonth() === t.getMonth() &&
                    d.getDate() === t.getDate();
            },

            prevMonth() {
                this.cursor = new Date(this.cursor.getFullYear(), this.cursor.getMonth() - 1, 1);
                this.build();
            },
            nextMonth() {
                this.cursor = new Date(this.cursor.getFullYear(), this.cursor.getMonth() + 1, 1);
                this.build();
            },
            goToday() {
                this.cursor = new Date();
                this.build();
            },

            openModal(date) {
                const iso = date.toISOString().slice(0, 10);
                if (this.toIso(date) < this.toIso(this.today)) return;

                this.modal.open = true;
                this.modal.dateIso = iso;
                this.modal.dateLabel = date.toLocaleDateString('es-CL', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });

                const d = this.dataByDate[iso] || {};
                this.form.tension_sis = d.tension?.sistolica ?? null;
                this.form.tension_dia = d.tension?.diastolica ?? null;
                this.form.glucosa = d.glucosa?.valor ?? null;
                this.form.peso = d.peso?.valor ?? null;
                this.form.nota = '';

                this.states.tension = 'idle';
                this.states.glucosa = 'idle';
                this.states.peso = 'idle';
            },

            closeModal() {
                this.modal.open = false;
            },

            // ===== Autosave helpers =====
            csrf() {
                const fromMeta = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (fromMeta) return fromMeta;
                // fallback: buscar input _token dentro del form del modal
                const tokenInput = this.$refs?.form?.querySelector('input[name="_token"]');
                return tokenInput ? tokenInput.value : '';
            },

            async postJSON(url, payload, stateKey) {
                this.states[stateKey] = 'pending';
                try {
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrf(),
                        },
                        body: JSON.stringify(payload),
                    });
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    this.states[stateKey] = 'saved';
                    setTimeout(() => {
                        if (this.states[stateKey] === 'saved') this.states[stateKey] = 'idle';
                    }, 2500);
                    return await res.json().catch(() => ({}));
                } catch (e) {
                    console.error(e);
                    this.states[stateKey] = 'error';
                    return null;
                }
            },

            validTension() {
                const s = Number(this.form.tension_sis),
                    d = Number(this.form.tension_dia);
                const sOK = Number.isFinite(s) && s >= 50 && s <= 250;
                const dOK = Number.isFinite(d) && d >= 30 && d <= 150;
                return sOK && dOK;
            },
            validGlucosa() {
                const g = Number(this.form.glucosa);
                return Number.isFinite(g) && g >= 40 && g <= 600;
            },
            validPeso() {
                const p = Number(this.form.peso);
                return Number.isFinite(p) && p >= 20 && p <= 400;
            },

            // ===== Autosave actions =====
            async maybeSaveTension(force = false) {
                if (this.validTension()) {
                    const payload = {
                        fecha: this.modal.dateIso,
                        tension_sistolica: Number(this.form.tension_sis),
                        tension_diastolica: Number(this.form.tension_dia),
                        nota: this.form.nota || null,
                    };
                    const ok = await this.postJSON(this.routes.tension, payload, 'tension');
                    if (ok) {
                        // Actualiza badges del día
                        const iso = this.modal.dateIso;
                        this.dataByDate[iso] = this.dataByDate[iso] || {};
                        this.dataByDate[iso].tension = {
                            sistolica: payload.tension_sistolica,
                            diastolica: payload.tension_diastolica
                        };
                        this.build();
                    }
                } else if (force) {
                    // opcional: feedback si salen del input con datos inválidos
                }
            },

            async maybeSaveGlucosa(force = false) {
                if (this.validGlucosa()) {
                    const payload = {
                        fecha: this.modal.dateIso,
                        glucosa: Number(this.form.glucosa),
                        nota: this.form.nota || null,
                    };
                    const ok = await this.postJSON(this.routes.glucosa, payload, 'glucosa');
                    if (ok) {
                        const iso = this.modal.dateIso;
                        this.dataByDate[iso] = this.dataByDate[iso] || {};
                        this.dataByDate[iso].glucosa = {
                            valor: payload.glucosa
                        };
                        this.build();
                    }
                } else if (force) {
                    // opcional
                }
            },

            async maybeSavePeso(force = false) {
                if (this.validPeso()) {
                    const payload = {
                        fecha: this.modal.dateIso,
                        peso: Number(this.form.peso),
                        nota: this.form.nota || null,
                    };
                    const ok = await this.postJSON(this.routes.peso, payload, 'peso');
                    if (ok) {
                        const iso = this.modal.dateIso;
                        this.dataByDate[iso] = this.dataByDate[iso] || {};
                        this.dataByDate[iso].peso = {
                            valor: payload.peso
                        };
                        this.build();
                    }
                } else if (force) {
                    // opcional
                }
            },

            // Atajo: Ctrl+S guarda todo lo válido en ese momento
            saveAll() {
                this.maybeSaveTension(true);
                this.maybeSaveGlucosa(true);
                this.maybeSavePeso(true);
            },
        }
    }
</script>
