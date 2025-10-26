@props([
  'reason' => null,
])

  <!-- Modal de cambio de contraseña forzado -->
  <div id="modalPassword"
       role="dialog"
       aria-modal="true"
       aria-labelledby="pw-title"
       class="fixed inset-0 z-[1000] bg-black/60 hidden flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 w-full max-w-md p-6 rounded-lg shadow-lg">
      <div class="flex justify-between items-center border-b pb-3">
        <h2 id="pw-title" class="text-xl font-bold text-teal-700 dark:text-white">
          Cambiar Contraseña
        </h2>

        <!-- Botón de cerrar se neutraliza por JS, queda “inerte” -->
        <button id="closePasswordModal"
                type="button"
                class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"
                aria-label="Cerrar">
          &times;
        </button>
      </div>

      @if ($reason)
        <p class="mt-2 text-red-600 dark:text-red-400 text-sm font-semibold">
          {{ $reason }}
        </p>
      @endif

      <form id="passwordChangeForm" action="{{ route('password.update') }}" method="POST" class="mt-4 space-y-4">
        @csrf

        <div>
          <label for="current_password" class="block text-sm font-medium text-teal-700 dark:text-gray-300">
            Contraseña Actual
          </label>
          <div class="relative mt-1">
            <input type="password" id="current_password" name="current_password" required
                   autocomplete="current-password"
                   class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <button type="button" id="togglePassword"
                    class="absolute right-3 top-1/2 -translate-y-1/2 select-none">
              <span>👁️</span>
            </button>
          </div>
        </div>

        <div>
          <label for="new_password" class="block text-sm font-medium text-teal-700 dark:text-gray-300">
            Nueva Contraseña
          </label>
          <div class="relative mt-1">
            <input type="password" id="new_password" name="new_password" required
                   autocomplete="new-password"
                   class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <button type="button" id="togglePasswordNew"
                    class="absolute right-3 top-1/2 -translate-y-1/2 select-none">
              <span>👁️</span>
            </button>
          </div>
        </div>

        <div>
          <label for="new_password_confirmation" class="block text-sm font-medium text-teal-700 dark:text-gray-300">
            Confirmar Nueva Contraseña
          </label>
          <div class="relative mt-1">
            <input type="password" id="new_password_confirmation" name="new_password_confirmation" required
                   autocomplete="new-password"
                   class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <button type="button" id="togglePasswordConfirm"
                    class="absolute right-3 top-1/2 -translate-y-1/2 select-none">
              <span>👁️</span>
            </button>
          </div>
        </div>

        <!-- Reglas -->
        <div>
          <label class="block text-sm font-medium text-teal-700 dark:text-gray-300">
            Requisitos de la contraseña
          </label>
          <ul class="pl-1 mt-2 text-sm text-gray-700 dark:text-gray-300 space-y-1">
            <li class="flex items-center gap-2">
              <input type="checkbox" id="check-length" disabled class="h-4 w-4"> Al menos 8 caracteres
            </li>
            <li class="flex items-center gap-2">
              <input type="checkbox" id="check-uppercase" disabled class="h-4 w-4"> Al menos una letra mayúscula
            </li>
            <li class="flex items-center gap-2">
              <input type="checkbox" id="check-number" disabled class="h-4 w-4"> Al menos un número
            </li>
          </ul>
        </div>

        <p id="password-error-message" class="text-red-500 hidden text-sm">
          Las contraseñas no coinciden o no cumplen los requisitos.
        </p>

        <div class="flex justify-end pt-2">
          <button type="submit"
                  class="w-auto text-base py-2 px-4 rounded bg-purple-900 text-white hover:bg-purple-800
                         dark:bg-gray-700 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
            <i class="fas fa-save mr-2"></i>
            Guardar Cambios
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- Forzado visual del modal cuando está bloqueado --}}
  <style>
    #modalPassword[data-locked="1"]{
      display:flex !important; visibility:visible !important; opacity:1 !important; pointer-events:auto !important;
    }
  </style>
