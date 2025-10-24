<!-- Panel flotante de AYUDA (video + manual) -->
<div id="helpPanel" class="fixed right-4 bottom-24 z-50 flex flex-col gap-2">
  <!-- Botón: Video de ayuda -->
  <button
    onclick="openHelpVideo()"
    class="w-11 h-11 rounded-full shadow-lg flex items-center justify-center 
           bg-custom-primary text-white hover:opacity-90 transition
           border-2"
    style="border-color: var(--secondary);"
    title="Video de ayuda"
    aria-label="Video de ayuda">
    <!-- Ícono Play -->
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
      <path d="M8 5v14l11-7z"/>
    </svg>
  </button>

  <!-- Botón: Manual / PDF -->
  <button
    onclick="openHelpManual()"
    class="w-11 h-11 rounded-full shadow-lg flex items-center justify-center 
           bg-white dark:bg-gray-800 text-custom-primary hover:opacity-90 transition
           border-2"
    style="border-color: var(--primary);"
    title="Manual del usuario"
    aria-label="Manual del usuario">
    <!-- Ícono Libro -->
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
      <path d="M19 2H8a3 3 0 00-3 3v14a3 3 0 013 3h11a1 1 0 001-1V3a1 1 0 00-1-1zm-1 18H9a1 1 0 010-2h9v2zm0-4H9a3 3 0 00-1 .18V5a1 1 0 011-1h10v12z"/>
    </svg>
  </button>
</div>
