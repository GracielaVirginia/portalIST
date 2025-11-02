{{-- resources/views/admin/partials/stats-panel.blade.php --}}

<div id="statsPanel" class="mt-6"
     data-endpoint="{{ route('dashboard.stats') }}">
  {{-- filtros --}}
  <div class="flex flex-col md:flex-row gap-3 items-start md:items-center">
    <div class="text-sm text-gray-600 dark:text-gray-300">
      <span class="font-semibold">Fecha seleccionada:</span>
      <span id="statsFechaTxt" class="ml-1">—</span>
    </div>

    <div class="flex items-center gap-2">
      <label for="filtroSede" class="text-sm font-semibold text-gray-700 dark:text-gray-200">Sede</label>
      <select id="filtroSede"
              class="border rounded-md px-3 py-2 text-sm
                     dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100">
        <option value="">Todas</option>
      </select>
    </div>
  </div>

  {{-- tarjetas --}}
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
      {{-- Usuarios registrados --}}
      <div onclick="mostrarModal && mostrarModal('usuariosModal')"
           class="bg-white dark:bg-slate-800 cursor-pointer shadow-lg p-6 rounded-lg">
          <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
              Usuarios registrados
          </h3>
          <p id="cardUsuariosRegistrados"
             class="text-2xl font-bold text-teal-800 dark:text-teal-300">0</p>
      </div>

      {{-- Exámenes realizados --}}
      <div class="bg-white dark:bg-slate-800 shadow-lg p-6 rounded-lg">
          <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
              Pacientes con Examen
          </h3>
          <p id="cardExamenesRealizados"
             class="text-2xl font-bold text-indigo-700 dark:text-indigo-300">0</p>
      </div>

      {{-- Usuarios bloqueados --}}
      <div onclick="mostrarModal && mostrarModal('bloqueadosModal')"
           class="bg-white dark:bg-slate-800 cursor-pointer shadow-lg p-6 rounded-lg">
          <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
              Usuarios bloqueados
          </h3>
          <p id="cardUsuariosBloqueados"
             class="text-2xl font-bold text-red-600 dark:text-red-300">0</p>
      </div>
  </div>

  {{-- estado / errores --}}
  <p id="statsMsg"
     class="mt-3 text-sm text-slate-600 dark:text-slate-300"></p>

  {{-- inputs ocultos para estado --}}
  <input type="hidden" id="filtroFecha" value="">
</div>
