{{-- resources/views/components/portal/voice-search-gestiones.blade.php --}}
@props([
  /**
   * Colección de gestiones del paciente (Illuminate\Support\Collection|array).
   * Sugerencia de columnas en gestiones:
   * id, titulo, codigo, fecha, lugar, profesional, pdf_url, informe
   * (si tus nombres son distintos, el mapper de abajo intenta resolver alias comunes)
   */
  'gestiones' => [],
])

@php
  // Normaliza cada fila a la forma que usa el widget
  $mapped = collect($gestiones)->map(function ($g) {
      // Soporte tanto objeto Eloquent como array
      $arr = is_array($g) ? $g : $g->toArray();
      $get = fn($keys, $default = null) =>
          collect((array)$keys)->map(fn($k) => $arr[$k] ?? null)->first(fn($v) => filled($v)) ?? $default;

      // Título (examen)
      $titulo = $get(['titulo','nombre','examen','descripcion','detalle',"titulo_examen"], 'Gestión #' . ($arr['id'] ?? ''));
      // Código del examen
      $codigo = $get(['codigo','code','cod_examen'], '');
      // Fecha (ISO YYYY-MM-DD)
      $fechaIso = \Illuminate\Support\Str::of($get(['fecha','fecha_resultado','fecha_informe','created_at'], now()->toDateString()))
                    ->substr(0,10);
      // Lugar
      $lugar = $get(['lugar','centro','sede','establecimiento'], '—');
      // Profesional
      $profesional = $get(['profesional','medico','doctor','quimico','tecnologo'], '—');
      // PDF
      $pdf = $get(['pdf_url','archivo_pdf','pdf','url_pdf'], null);
      // Informe
      $informe = $get(['informe','observaciones','resultado_texto','comentarios','resultado'], 'Sin informe disponible.');

      // Keywords: título tokenizado + heurísticos
      $base = \Illuminate\Support\Str::of($titulo)->lower()->ascii()->replaceMatches('/[^a-z0-9\s]/', ' ')->squish()->__toString();
      $kw = collect(explode(' ', $base))->filter()->values()->all();

      // Sinónimos frecuentes
      if (preg_match('/gluco|glice|gluce|glicemia|glucosa/i', $titulo)) {
        array_push($kw, 'glucosa','glicemia','glucemia');
      }
      if (preg_match('/hemograma|cbc/i', $titulo)) {
        array_push($kw, 'hemograma','cbc');
      }
      if (preg_match('/lipid|hdl|ldl|trigli|colesterol/i', $titulo)) {
        array_push($kw, 'perfil lipidico','colesterol','hdl','ldl','trigliceridos');
      }

      return [
        'id'          => $arr['id'] ?? null,
        'titulo'      => $titulo,
        'codigo'      => $codigo,
        'fecha'       => (string)$fechaIso,
        'lugar'       => $lugar,
        'profesional' => $profesional,
        'pdf'         => $pdf ? (filter_var($pdf, FILTER_VALIDATE_URL) ? $pdf : asset($pdf)) : null,
        'keywords'    => array_values(array_unique($kw)),
        'informe'     => $informe,
      ];
  })->values();
@endphp

<div x-data="voiceSearchGestiones(@js($mapped))" x-init="init()" class="w-full">
  {{-- Barra de búsqueda + botón micrófono --}}
  <div class="mt-4 flex items-center gap-2">
    <div class="relative flex-1">
      <input
        x-model="query"
        @keydown.enter.prevent="runSearch(query)"
        type="text"
        class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 pr-20 text-sm text-gray-800 dark:text-gray-100"
        placeholder="Di o escribe: “búscame el informe de glucosa en sangre…”"
        aria-label="Buscar informe por texto"
      />
      <div class="absolute inset-y-0 right-2 flex items-center gap-1">
        <button type="button"
          @click="runSearch(query)"
          class="rounded-lg px-3 py-1 text-sm bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700"
          aria-label="Buscar">
          Buscar
        </button>
      </div>
    </div>

    <button type="button"
      @click="toggleListen()"
      :class="listening ? 'bg-green-600 text-white' : 'bg-white dark:bg-gray-950 text-gray-800 dark:text-gray-100 border border-gray-200 dark:border-gray-700'"
      class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-400/60"
      :aria-pressed="listening.toString()"
      :aria-label="listening ? 'Detener búsqueda por voz' : 'Iniciar búsqueda por voz'">
      <template x-if="!listening"><span>🎤</span></template>
      <template x-if="listening"><span>🟢</span></template>
      <span x-text="listening ? 'Escuchando…' : 'Buscar por voz'"></span>
    </button>
  </div>

  {{-- Estado / Mensajes --}}
  <p class="mt-2 text-xs text-gray-600 dark:text-gray-400" x-show="hint" x-text="hint"></p>
  <p class="mt-1 text-xs text-red-600" x-show="error" x-text="error"></p>

  {{-- MODAL: Resultado encontrado / no encontrado --}}
  <div x-show="showModal" x-cloak class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
       @keydown.escape.window="closeModal()">
    <div class="w-full max-w-xl rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-xl overflow-hidden">
      <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
          <span x-text="modalTitle"></span>
        </h3>
        <button class="rounded-lg px-2 py-1 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                @click="closeModal()">Cerrar</button>
      </div>

      <template x-if="result">
        <div class="p-4 space-y-4">
          <div class="text-sm text-gray-800 dark:text-gray-100">
            <div class="flex items-center justify-between">
              <div class="font-semibold" x-text="result.titulo"></div>
              <span class="text-xs px-2 py-0.5 rounded-md bg-purple-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-purple-900 dark:text-gray-200" x-text="result.codigo || '—'"></span>
            </div>
            <div class="mt-1 text-xs text-gray-600 dark:text-gray-400">
              <span>Fecha: </span><span x-text="formatDate(result.fecha)"></span> •
              <span>Lugar: </span><span x-text="result.lugar || '—'"></span> •
              <span>Profesional: </span><span x-text="result.profesional || '—'"></span>
            </div>
            <hr class="my-3 border-gray-200 dark:border-gray-700">
            <div class="prose prose-sm dark:prose-invert max-w-none">
              <p class="whitespace-pre-line" x-text="result.informe"></p>
            </div>
          </div>

          <div class="flex items-center justify-end gap-2">
            <button type="button"
                    @click="speakReport(result)"
                    class="inline-flex items-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-950 text-gray-800 dark:text-gray-100 px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-900">
              🔊 Escuchar informe
            </button>
            <template x-if="result.pdf">
              <a :href="result.pdf" target="_blank"
                 class="inline-flex items-center gap-2 rounded-xl bg-purple-900 text-white px-3 py-2 text-sm font-semibold hover:opacity-90">
                Ver PDF
              </a>
            </template>
          </div>
        </div>
      </template>

      <template x-if="!result">
        <div class="p-6 text-center text-sm text-gray-700 dark:text-gray-200">
          No encontré un informe que coincida con tu búsqueda.
          <div class="mt-3">
            <button @click="closeModal()"
                    class="rounded-xl bg-gray-100 dark:bg-gray-800 px-3 py-2 text-sm hover:bg-gray-200 dark:hover:bg-gray-700">
              Entendido
            </button>
          </div>
        </div>
      </template>
    </div>
  </div>
</div>

<script>
function voiceSearchGestiones(dataset) {
  return {
    listening: false,
    recognizer: null,
    synth: null,
    query: '',
    hint: 'Ejemplos: “búscame el informe de glucosa en sangre”, “hemograma del 20 de agosto”.',
    error: '',
    showModal: false,
    modalTitle: 'Informe encontrado',
    result: null,
    resultados: Array.isArray(dataset) ? dataset : [],

    init() {
      this.synth = window.speechSynthesis || null;
      const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
      if (!SR) {
        this.error = 'Tu navegador no soporta reconocimiento de voz. Usa la caja de texto.';
        return;
      }
      this.recognizer = new SR();
      this.recognizer.lang = 'es-ES'; // si prefieres: 'es-CL'
      this.recognizer.interimResults = false;
      this.recognizer.maxAlternatives = 1;

      this.recognizer.onresult = (e) => {
        const transcript = (e.results?.[0]?.[0]?.transcript || '').trim();
        if (transcript) {
          this.query = transcript;
          this.runSearch(transcript);
        }
      };
      this.recognizer.onerror = (e) => {
        this.error = 'Error de micrófono: ' + (e.error || 'desconocido');
        this.listening = false;
      };
      this.recognizer.onend = () => {
        this.listening = false;
      };
    },

    toggleListen() {
      if (!this.recognizer) return;
      if (this.listening) { try { this.recognizer.stop(); } catch {} this.listening = false; return; }
      this.error = '';
      this.hint = 'Escuchando… habla ahora.';
      try { this.recognizer.start(); this.listening = true; } catch (e) { this.error = 'No se pudo iniciar el micrófono.'; }
    },

    runSearch(raw) {
      this.error = '';
      const q = this.normalize(raw || this.query || '');
      if (!q) { this.error = 'Escribe o di qué informe necesitas.'; return; }

      const fechaFiltro = this.extractDate(q);     // 'YYYY-MM-DD' o null
      const term = this.extractExamTerm(q);        // término pivote (glucosa, hemograma, etc.)

      // 1) por keywords
      let candidatos = this.resultados.filter(r =>
        Array.isArray(r.keywords) &&
        r.keywords.some(k => this.normalize(k).includes(term) || term.includes(this.normalize(k)))
      );

      // 2) si no hay por keywords, intenta por título
      if (candidatos.length === 0 && term) {
        candidatos = this.resultados.filter(r => this.normalize(r.titulo).includes(term));
      }

      // 3) si hay fecha, filtra exacto
      if (fechaFiltro) {
        candidatos = candidatos.filter(r => (r.fecha || '').slice(0,10) === fechaFiltro);
      }

      // Orden: más reciente primero
      candidatos.sort((a,b) => ((a.fecha||'') < (b.fecha||'') ? 1 : -1));

      this.result = candidatos[0] || null;
      this.modalTitle = this.result ? 'Informe encontrado' : 'Sin coincidencias';
      this.showModal = true;

      this.hint = this.result ? '' : 'Prueba: “glucosa en sangre”, “hemograma 20 de agosto”, “perfil lipídico 2/7/2025”.';
    },

    speakReport(item) {
      if (!this.synth || !item) return;
      const texto = `${item.titulo}. Fecha ${this.formatDate(item.fecha)}. ${item.informe}`;
      const u = new SpeechSynthesisUtterance(texto);
      u.lang = 'es-ES';
      this.synth.cancel();
      this.synth.speak(u);
    },

    closeModal() { this.showModal = false; this.result = null; if (this.synth) this.synth.cancel(); },

    // ===== Utilidades =====
    normalize(s) {
      return (s || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '')
               .replace(/[^a-z0-9\s\/\-]/g, ' ').replace(/\s+/g, ' ').trim();
    },
    extractExamTerm(q) {
      let s = q.replace(/\b(busca(?:me)?|buscar|quiero|necesito|informe|resultado|examen|de|del|la|el|mi|mostrar|dime|ver)\b/g, ' ')
               .replace(/\s+/g, ' ').trim();

      const map = [
        [/glucosa|glicemia|glucemia/g, 'glucosa'],
        [/perfil lipidico|lipidos|colesterol|trigliceridos|hdl|ldl/g, 'perfil lipidico'],
        [/hemograma|sangre completa|cbc/g, 'hemograma'],
      ];
      for (const [re, rep] of map) s = s.replace(re, rep);

      if (!s) {
        if (/gluco|glice|gluce|glicemia|glucosa/.test(q)) return 'glucosa';
        if (/hemograma|cbc/.test(q)) return 'hemograma';
        if (/colesterol|lipidico|hdl|ldl|trigli/.test(q)) return 'perfil lipidico';
      }
      return this.normalize(s);
    },
    extractDate(q) {
      const num = q.match(/\b(\d{1,2})[\/\-](\d{1,2})(?:[\/\-](\d{2,4}))?\b/);
      if (num) {
        let [_, d, m, y] = num; d = d.padStart(2,'0'); m = m.padStart(2,'0');
        y = y ? (y.length === 2 ? ('20' + y) : y) : String(new Date().getFullYear());
        return `${y}-${m}-${d}`;
      }
      const meses = {'enero':'01','febrero':'02','marzo':'03','abril':'04','mayo':'05','junio':'06','julio':'07','agosto':'08','septiembre':'09','setiembre':'09','octubre':'10','noviembre':'11','diciembre':'12'};
      const mtx = q.match(/\b(\d{1,2})\s+de\s+([a-záéíóú]+)(?:\s+de\s+(\d{4}))?\b/i);
      if (mtx) {
        let d = String(mtx[1]).padStart(2,'0'); let mesTxt = this.normalize(mtx[2]); let y = mtx[3] ? mtx[3] : String(new Date().getFullYear());
        if (meses[mesTxt]) return `${y}-${meses[mesTxt]}-${d}`;
      }
      return null;
    },
    formatDate(iso) { try { const d = new Date((iso||'') + 'T00:00:00'); return d.toLocaleDateString('es-CL', { year:'numeric', month:'long', day:'numeric' }); } catch { return iso; } },
  }
}
</script>
