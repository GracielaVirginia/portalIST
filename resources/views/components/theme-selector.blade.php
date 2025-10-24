<!-- Panel flotante de temas (solo en modo claro) -->
<div id="themePanel" class="fixed right-4 top-1/2 -translate-y-1/2 z-50 dark:hidden">
  <div class="bg-surface-2 text-content rounded-lg shadow-lg p-3 border border-ring">
    <h3 class="text-xs font-semibold mb-2 text-center">Temas</h3>

    <div class="flex flex-col gap-2">
      <!-- Purple -->
      <button
        type="button"
        data-color="purple"
        onclick="changeTheme('purple')"
        class="theme-option w-8 h-8 rounded border-2 shadow hover:scale-110 transition-transform
               bg-purple-500 border-white"
        title="Purple"
        aria-label="Tema Purple">
      </button>

      <!-- Teal -->
      <button
        type="button"
        data-color="teal"
        onclick="changeTheme('teal')"
        class="theme-option w-8 h-8 rounded border-2 shadow hover:scale-110 transition-transform
               bg-teal-500 border-white"
        title="Teal"
        aria-label="Tema Teal">
      </button>

      <!-- Sky -->
      <button
        type="button"
        data-color="sky"
        onclick="changeTheme('sky')"
        class="theme-option w-8 h-8 rounded border-2 shadow hover:scale-110 transition-transform
               bg-sky-500 border-white"
        title="Sky"
        aria-label="Tema Sky">
      </button>
    </div>

    <div class="mt-2 pt-2 border-t border-ring/60">
      <button type="button" onclick="toggleThemePanel()"
              class="w-full text-xs text-muted hover:text-content transition-colors text-center">
        👁️
      </button>
    </div>
  </div>
</div>
