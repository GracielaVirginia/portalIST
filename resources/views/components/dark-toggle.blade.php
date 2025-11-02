<!-- Botón flotante: modo oscuro (sol/luna) -->
<div class="fixed top-4 right-4 z-50">
  <button id="floatingDarkToggle"
          class="w-11 h-11 rounded-full bg-surface-2 text-content border-2 border-primary shadow-lg
                 flex items-center justify-center hover:opacity-90 transition"
          title="Cambiar tema oscuro/claro"
          aria-label="Cambiar tema oscuro/claro">
    <!-- Sol: visible en oscuro -->
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden dark:block" viewBox="0 0 24 24" fill="currentColor">
      <path d="M6.76 4.84l-1.8-1.79-1.41 1.41 1.79 1.8 1.42-1.42zm10.48 0l1.79-1.79 1.41 1.41-1.79 1.8-1.41-1.42zM12 4V1h0v3zm0 19v-3h0v3zm8-8h3v0h-3zm-19 0h3v0H1zm14.24 7.16l1.41 1.42 1.79-1.8-1.41-1.41-1.79 1.79zM4.84 17.24l-1.8 1.79 1.41 1.41 1.8-1.79-1.41-1.41zM12 7a5 5 0 100 10 5 5 0 000-10z"/>
    </svg>
    <!-- Luna: visible en claro -->
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 block dark:hidden" viewBox="0 0 24 24" fill="currentColor">
      <path d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z"/>
    </svg>
  </button>
  
</div>
