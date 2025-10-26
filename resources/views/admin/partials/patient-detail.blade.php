{{-- resources/views/admin/partials/patient-detail.blade.php --}}
<div id="patientDetailPanel"
     class="mt-6"
     data-lookup="{{ route('admin.patients.lookup') }}"
     data-unblock-pattern="{{ route('admin.users.unblock', ['user' => 'USER_ID']) }}"
     data-delete-pattern="{{ route('admin.users.destroy', ['user' => 'USER_ID']) }}">

  <h3 class="text-lg font-semibold text-white dark:text-slate-100 mb-3">Detalle de paciente</h3>

  <div id="patientMsg" class="text-sm text-white dark:text-slate-300">
    Selecciona un paciente en el buscador para ver detalles aquí.
  </div>

  <div id="patientTableWrap" class="mt-3 hidden">
    <div class="overflow-x-auto rounded-xl shadow">
      <table class="min-w-full bg-white dark:bg-slate-800">
        <thead>
          <tr class="text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-300">
            <th class="px-4 py-3">Nombre</th>
            <th class="px-4 py-3">Apellido</th>
            <th class="px-4 py-3">RUT</th>
            <th class="px-4 py-3">Estado</th>
            <th class="px-4 py-3">Acciones</th>
          </tr>
        </thead>
        <tbody id="patientTableBody" class="text-sm text-slate-800 dark:text-slate-100"></tbody>
      </table>
    </div>
  </div>

  <div id="patientCtaRegister" class="mt-4 hidden">
    <div class="p-4 rounded-lg bg-yellow-50 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200">
      Usuario no registrado. 
      <a href="/admin/users" class="font-semibold underline">¿Quiere registrarlo?</a>
    </div>
  </div>
</div>
