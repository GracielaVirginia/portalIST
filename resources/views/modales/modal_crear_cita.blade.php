{{-- resources/views/modales/modal_crear_cita_universal.blade.php --}}
<div id="modalCrearCita" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center">
  <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl w-full max-w-3xl">
    <div class="px-6 py-4 border-b dark:border-gray-700 flex justify-between items-center bg-purple-600 text-white rounded-t-2xl">
      <h2 class="text-lg font-semibold">Confirmar cita</h2>
      <button type="button" class="text-2xl leading-none hover:opacity-90" onclick="cerrarModal()">×</button>
    </div>

    <form id="formCrearCita" method="POST" action="{{ route('agenda.store') }}" class="p-6 space-y-5">
      @csrf
      <input type="hidden" name="profesional_id" id="medico_id">

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="text-sm text-gray-700 dark:text-gray-300">Fecha</label>
          <input readonly id="fechaCita" name="fecha" class="w-full border rounded px-3 py-2 bg-gray-100 dark:bg-gray-800 dark:border-gray-700">
        </div>
        <div>
          <label class="text-sm text-gray-700 dark:text-gray-300">Inicio</label>
          <input readonly id="horaIni" name="hora_inicio" class="w-full border rounded px-3 py-2 bg-gray-100 dark:bg-gray-800 dark:border-gray-700">
        </div>
        <div>
          <label class="text-sm text-gray-700 dark:text-gray-300">Fin</label>
          <input readonly id="horaFin" name="hora_fin" class="w-full border rounded px-3 py-2 bg-gray-100 dark:bg-gray-800 dark:border-gray-700">
        </div>
      </div>

      <div>
        <label class="text-sm text-gray-700 dark:text-gray-300">Tipo de atención</label>
        <div class="mt-2 flex gap-3">
          <label class="inline-flex items-center gap-2">
            <input type="radio" name="tipo_atencion" value="presencial" checked>
            <span>Presencial</span>
          </label>
          <label class="inline-flex items-center gap-2">
            <input type="radio" name="tipo_atencion" value="remota">
            <span>Remota</span>
          </label>
        </div>
      </div>

      <div>
        <label class="text-sm text-gray-700 dark:text-gray-300">Motivo</label>
        <textarea name="motivo" rows="3" class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-700"></textarea>
      </div>

      <div class="pt-2 text-right">
        <button type="button" onclick="cerrarModal()" class="px-4 py-2 rounded border border-gray-300 dark:border-gray-600 mr-2">
          Cancelar
        </button>
        <button class="px-4 py-2 rounded bg-purple-600 hover:bg-purple-700 text-white">
          Reservar
        </button>
      </div>
    </form>
  </div>
</div>
