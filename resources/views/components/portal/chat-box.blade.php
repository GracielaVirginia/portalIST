<div x-data="{ open:false }" class="relative z-50">
  {{-- FOOTER --}}
  <footer class="fixed bottom-0 left-0 w-full z-40">
    <div class="mx-auto max-w-7xl px-4 py-3
                flex flex-col sm:flex-row items-center justify-center sm:justify-between gap-2
                bg-purple-100/90 dark:bg-gray-900/90 backdrop-blur
                border-t border-purple-200/60 dark:border-gray-800
                text-purple-900 dark:text-gray-200">

      <p class="text-sm">
        Versión 3.0.0 · &copy; {{ date('Y') }} Todos los derechos reservados.
      </p>

      <div class="flex items-center gap-3">
        {{-- Ayuda --}}
        <a href="{{ route('soporte.create') }}"
           class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-sm font-semibold
                  bg-white text-purple-900 border border-purple-200
                  hover:bg-purple-50 hover:border-purple-300
                  dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700 dark:hover:bg-gray-700">
          <span aria-hidden="true">🛈</span>
          <span>Ayuda</span>
        </a>

        {{-- FAQ botón (dentro del footer) --}}
        <button
          @click="open = true"
          class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-sm font-semibold
                 bg-purple-900 text-white hover:bg-purple-800
                 dark:bg-purple-700 dark:hover:bg-purple-600
                 shadow transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
               viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M8 10a4 4 0 118 0c0 1.5-1 2.5-2 3s-2 1.5-2 3m0 4h.01M12 4a8 8 0 100 16 8 8 0 000-16z"/>
          </svg>
          <span>FAQ</span>
        </button>
      </div>
    </div>
  </footer>

  {{-- MODAL FAQ --}}
  <div
    x-show="open"
    x-cloak
    class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-[60]"
    @keydown.escape.window="open=false">

    <div class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-900 shadow-2xl overflow-hidden border border-gray-200 dark:border-gray-700">
      {{-- Header --}}
      <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-semibold text-purple-900 dark:text-gray-100">Preguntas Frecuentes</h3>
        <button @click="open=false" class="text-gray-600 dark:text-gray-300 hover:text-purple-700 dark:hover:text-gray-100">✕</button>
      </div>

      {{-- Contenido dinámico --}}
      <div
        x-data="{
          q: '', items: [],
          async load() {
            try {
              const res = await fetch('{{ route('portal.faqs.list') }}?q=' + encodeURIComponent(this.q));
              const json = await res.json();
              this.items = json.ok ? (json.items || []) : [];
            } catch (_) { this.items = []; }
          }
        }"
        x-init="load()"
        class="p-4 space-y-3 h-[70vh] overflow-y-auto">

        {{-- Campo de búsqueda --}}
        <input type="search"
              x-model="q"
              @input.debounce.300ms="load()"
              placeholder="Buscar preguntas..."
              class="w-full rounded-lg border-gray-300 dark:bg-gray-800 dark:border-gray-700 text-sm px-3 py-2">

        {{-- Listado --}}
        <template x-for="it in items" :key="it.id">
          <details class="rounded-lg border border-gray-200 dark:border-gray-700 p-3">
            <summary class="font-medium cursor-pointer text-purple-900 dark:text-gray-100" x-text="it.question"></summary>
            <div class="mt-2 text-sm text-gray-700 dark:text-gray-300" x-text="it.answer"></div>
          </details>
        </template>

        {{-- Sin resultados --}}
        <div x-show="items.length===0" class="text-sm text-gray-500 text-center">Sin resultados</div>
      </div>
    </div>
  </div>
</div>
