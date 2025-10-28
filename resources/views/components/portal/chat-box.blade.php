<div x-data="{ open:false }" class="fixed right-4 bottom-28 z-50">
  {{-- Botón flotante del chat --}}
  <button
    @click="open = true"
    class="bg-purple-900 text-white rounded-full w-12 h-12 flex items-center justify-center shadow-lg hover:scale-110 transition-transform"
    title="Centro de ayuda">
    💬
  </button>

  {{-- Modal del chatbox --}}
  <div
    x-show="open"
    x-cloak
    class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-[60]"
    @keydown.escape.window="open=false">

    <div class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-900 shadow-2xl overflow-hidden border border-gray-200 dark:border-gray-700">
      {{-- Header --}}
      <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-semibold text-purple-900 dark:text-gray-100">Centro de Ayuda</h3>
        <button @click="open=false" class="text-gray-600 dark:text-gray-300 hover:text-purple-700 dark:hover:text-gray-100">✕</button>
      </div>

      {{-- Contenido FAQ dinámico --}}
      <div
        x-data="{
          q: '', items: [],
          async load() {
            const res = await fetch('{{ route('portal.faqs.list') }}?q=' + encodeURIComponent(this.q));
            const json = await res.json();
            if (json.ok) this.items = json.items;
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

        {{-- Listado dinámico --}}
        <template x-for="it in items" :key="it.id">
          <details class="rounded-lg border border-gray-200 dark:border-gray-700 p-3">
            <summary class="font-medium cursor-pointer text-purple-900 dark:text-gray-100" x-text="it.question"></summary>
            <div class="mt-2 text-sm text-gray-700 dark:text-gray-300" x-text="it.answer"></div>
          </details>
        </template>

        {{-- Mensaje si no hay resultados --}}
        <div x-show="items.length===0" class="text-sm text-gray-500 text-center">Sin resultados</div>
      </div>
    </div>
  </div>
</div>
