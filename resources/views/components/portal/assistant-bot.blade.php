{{-- Componente: Asistente Virtual --}}
<div x-data="{ open:false, sending:false, messages: [] }">

  {{-- BOTÓN FLOTANTE --}}
  <button
    @click="open = true" id="btnAsistenteVirtual"
    class="fixed bottom-12 right-0 w-16 h-16 rounded-full
           border-2 border-purple-700 text-purple-700 bg-transparent
           hover:bg-purple-50 active:scale-95
           dark:border-purple-400 dark:text-purple-300 dark:hover:bg-purple-900/30
           shadow-xl grid place-items-center transition
           z-[999999]" {{-- máximo z-index --}}
    title="Asistente Virtual"
    aria-label="Abrir Asistente Virtual">
    <span class="text-3xl">🤖</span>
  </button>

  {{-- MODAL --}}
{{-- MODAL: Asistente Virtual (anclado abajo-derecha + seguro) --}}
<div x-show="open" x-cloak
     class="fixed inset-0 z-[999998]"  {{-- por encima de todo --}}
     @keydown.escape.window="open=false">

  {{-- fondo (clic fuera cierra) --}}
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="open=false"></div>

  {{-- Panel anclado en esquina inferior derecha (no se corta) --}}
  <section class="fixed right-6 bottom-24   {{-- súbelo lejos del dock/footer --}}
                   w-[420px] max-w-[95vw]
                   max-h-[75vh] flex flex-col
                   rounded-2xl bg-white dark:bg-gray-900
                   border border-gray-200 dark:border-gray-700
                   shadow-2xl overflow-hidden">

    {{-- Header --}}
    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
      <h3 class="text-base font-semibold text-purple-900 dark:text-gray-100">Asistente Virtual</h3>
      <button @click="open=false"
              class="text-gray-600 dark:text-gray-300 hover:text-purple-700 dark:hover:text-gray-100">✕</button>
    </div>

    {{-- Mensajes (scroll interno) --}}
    <div id="botChatScroll" class="p-4 space-y-3 flex-1 overflow-y-auto">
      <template x-for="(m, i) in messages" :key="i">
        <div class="text-sm" :class="m.role==='user' ? 'text-right' : 'text-left'">
          <div class="inline-block px-3 py-2 rounded-xl break-words max-w-[85%]"
               :class="m.role==='user'
                        ? 'bg-purple-600 text-white'
                        : 'bg-gray-100 dark:bg-gray-800 dark:text-gray-100'">
            <span x-html="m.text"></span>
          </div>
        </div>
      </template>
      <div x-show="sending" class="text-xs text-gray-500">Escribiendo…</div>
    </div>

    {{-- Input + Enviar (pegado abajo, siempre visible) --}}
    <form
      @submit.prevent="
        const q = $refs.inp.value.trim();
        if (!q || sending) return;
        messages.push({role:'user', text: q});
        $refs.inp.value = '';
        sending = true;

        fetch('{{ route('portal.assistant.message') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '',
          },
          body: JSON.stringify({ message: q })
        })
        .then(r => r.json())
        .then(j => { messages.push({role:'bot', text: j.reply || 'Lo siento, aún no tengo respuesta para eso 🤖'}); })
        .catch(() => { messages.push({role:'bot', text: 'Hubo un problema al responder. Intenta otra vez.'}); })
        .finally(() => {
          sending = false;
          setTimeout(() => {
            const el = document.getElementById('botChatScroll');
            if (el) el.scrollTop = el.scrollHeight;
          }, 10);
        });
      "
      class="sticky bottom-0 border-t border-gray-200 dark:border-gray-700
             p-3 flex items-center gap-2 bg-white dark:bg-gray-900">
      <input x-ref="inp" type="text" placeholder="Escribe aquí…"
             class="flex-1 rounded-lg border-gray-300 dark:bg-gray-800 dark:border-gray-700 text-sm px-3 py-2">
      <button type="submit"
              class="rounded-xl bg-purple-900 text-white text-sm px-3 py-2 hover:bg-purple-800 disabled:opacity-60"
              :disabled="sending">
        Enviar
      </button>
    </form>
  </section>
</div>
</div>
