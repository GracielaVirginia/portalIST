<!-- Panel flotante: Ayuda / Video / Manual -->
<div
  x-data="{ openVideo:false, openManual:false, openHelp:false }"
  class="fixed right-4 bottom-24 z-50"
>
  <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
              rounded-2xl shadow-lg p-2 flex flex-col items-center gap-2">

    <!-- Botón 1: Manual (PDF) — círculo morado -->
    <button type="button"
            @click="openManual = true"
            class="w-12 h-12 rounded-full grid place-items-center
                   text-white bg-purple-900 hover:bg-purple-800
                   dark:bg-purple-700 dark:hover:bg-purple-600
                   border border-transparent shadow-md transition-all"
            title="Abrir Manual" aria-label="Abrir Manual">
      {{-- document-text icon --}}
      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
           viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M7 21h10a2 2 0 002-2V9l-5-5H7a2 2 0 00-2 2v13a2 2 0 002 2z"/>
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M14 3v4a1 1 0 001 1h4"/>
      </svg>
    </button>

    <!-- Botón 2: Video — círculo morado (contenido del modal se mantiene tal cual) -->
    <button type="button"
            @click="openVideo = true"
            class="w-12 h-12 rounded-full grid place-items-center
                   text-white bg-purple-900 hover:bg-purple-800
                   dark:bg-purple-700 dark:hover:bg-purple-600
                   border border-transparent shadow-md transition-all"
            title="Ver Video" aria-label="Ver Video">
      {{-- play-circle icon --}}
      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
           viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M14.752 11.168l-4.596-2.65A1 1 0 009 9.35v5.3a1 1 0 001.156.98l4.596-.918a1 1 0 00.844-.98v-1.585a1 1 0 00-.844-.98z"/>
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
    </button>

    <!-- Botón 3: Preguntas Frecuentes (FAQ) — círculo morado -->
    {{-- <button type="button"
            @click="openHelp = true"
            class="w-12 h-12 rounded-full grid place-items-center
                   text-white bg-purple-900 hover:bg-purple-800
                   dark:bg-purple-700 dark:hover:bg-purple-600
                   border border-transparent shadow-md transition-all"
            title="Abrir Ayuda" aria-label="Abrir Ayuda">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
           viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M8 10h8M8 14h5m7-2a9 9 0 11-18 0 9 9 0 0118 0zm-4 5.5l-3.5-1-2.5 2.5v-3.5"/>
      </svg>
    </button> --}}

  </div>

  <!-- ================ MODALES ================ -->

  <!-- Modal: Ayuda (chat box / FAQ) -->
  <div x-show="openHelp" x-cloak
       class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4"
       @keydown.escape.window="openHelp=false"
       x-transition.opacity>
    <div class="w-full max-w-lg rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-xl overflow-hidden">
      <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-2 text-purple-900 dark:text-gray-100">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
               viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M8 10h8M8 14h5m7-2a9 9 0 11-18 0 9 9 0 0118 0zm-4 5.5l-3.5-1-2.5 2.5v-3.5"/>
          </svg>
          <h3 class="text-base font-semibold">Preguntas Frecuentes (FAQ)</h3>
        </div>
        <button class="rounded-lg px-2 py-1 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                @click="openHelp=false">Cerrar</button>
      </div>
      <div class="p-4 space-y-3">
        <p class="text-sm text-gray-700 dark:text-gray-300">
          ¿En qué podemos ayudarte? Déjanos tu mensaje y te contactaremos.
        </p>
        <textarea class="w-full rounded-lg border-gray-300 dark:bg-gray-800 dark:border-gray-700"
                  rows="4" placeholder="Escribe tu consulta…"></textarea>
        <div class="flex justify-end">
          <button class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-purple-900 text-white hover:opacity-90">
            Enviar
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal: Video -->
  <div x-show="openVideo" x-cloak
       class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
       @keydown.escape.window="openVideo=false"
       x-transition.opacity>
    <div class="w-full max-w-4xl rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-xl overflow-hidden">
      <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Video tutorial</h3>
        <button class="rounded-lg px-2 py-1 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                @click="openVideo=false">Cerrar</button>
      </div>
      <div class="p-0">
        <!-- Cambia la ruta por tu archivo real en /public -->
        <video controls class="w-full h-[60vh] object-contain bg-black">
          <source src="{{ asset('videos/tutorial.mp4') }}" type="video/mp4">
          Tu navegador no soporta video HTML5.
        </video>
      </div>
    </div>
  </div>

  <!-- Modal: Manual (PDF) -->
  <div x-show="openManual" x-cloak
       class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
       @keydown.escape.window="openManual=false"
       x-transition.opacity>
    <div class="w-full max-w-5xl rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-xl overflow-hidden">
      <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Manual del usuario</h3>
        <button class="rounded-lg px-2 py-1 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                @click="openManual=false">Cerrar</button>
      </div>
      <div class="p-0">
        <!-- Cambia la ruta por tu PDF real en /public -->
        <iframe src="{{ asset('manual/manual-usuario.pdf') }}"
                class="w-full h-[70vh]" title="Manual del usuario"></iframe>
      </div>
    </div>
  </div>

  <!-- ============== /MODALES ============== -->
</div>
