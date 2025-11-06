<!-- Botón flotante fijo, centrado arriba -->
<button id="btnTourHome"
  class="fixed z-50 left-1/2 -translate-x-1/2"
  style="top: env(safe-area-inset-top, 1rem);"
  onclick="if (typeof window.startTourHome==='function') startTourHome();"
>
  <span class="rounded-2xl shadow-lg bg-purple-700 hover:bg-purple-800 text-white px-5 py-3 text-sm font-semibold focus:outline-none focus:ring-4 focus:ring-purple-300">
    ¿Cómo usar esta página?
  </span>
</button>
