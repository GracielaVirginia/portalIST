<!-- Panel flotante de cambio de tema -->
<div id="themePanel" class="fixed right-4 top-1/2 -translate-y-1/2 z-50">
  <div class="bg-surface-2 text-content rounded-full shadow-lg p-2 border border-ring flex flex-col items-center gap-2">
    
    <!-- ☀️ Solo visible en modo claro -->
    <button
      type="button"
      onclick="toggleDark()"
      class="w-10 h-10 flex items-center justify-center rounded-full bg-yellow-400 text-white text-xl shadow hover:scale-110 transition-transform dark:hidden"
      title="Cambiar a modo oscuro"
      aria-label="Cambiar a modo oscuro">
      ☀️
    </button>

    <!-- 🌙 Solo visible en modo oscuro -->
    <button
      type="button"
      onclick="toggleDark()"
      class="w-10 h-10 flex items-center justify-center rounded-full bg-indigo-800 text-yellow-300 text-xl shadow hover:scale-110 transition-transform hidden dark:flex"
      title="Cambiar a modo claro"
      aria-label="Cambiar a modo claro">
      🌙
    </button>
  </div>
</div>
