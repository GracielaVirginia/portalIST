@extends('layouts.admin')

@section('title', 'Usuarios registrados')

@section('admin')
<div class="px-6 py-6">
  <x-admin.nav-actions
    backHref="{{ route('admin.dashboard') }}"
    logoutRoute="admin.logout"
    variant="inline"
  />

  <h1 class="text-2xl font-bold text-purple-700 dark:text-purple-200 mb-4">👤 Usuarios registrados</h1>

  <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
    <table id="tablaRegistrados" class="min-w-full">
      <thead>
        <tr class="text-left text-xs sm:text-sm text-purple-900 dark:text-purple-200">
          <th class="py-2">ID</th>
          <th class="py-2">RUT</th>
          <th class="py-2">Nombre</th>
          <th class="py-2">Email</th>
          <th class="py-2">Lugar</th>
          <th class="py-2">Creado</th>
          <th class="py-2">Activo</th>
          <th class="py-2">Bloqueado</th>
          <th class="py-2 text-center">Acciones</th>
        </tr>
      </thead>
      <tbody class="text-sm"></tbody>
    </table>
  </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  $.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
  });

  const table = $('#tablaRegistrados').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{{ route('admin.users.registered.data') }}',
    pageLength: 10,
    lengthMenu: [10, 25, 50, 100],
    order: [[0, 'asc']],
    columns: [
      { data: 'id',         name: 'id' },
      { data: 'rut',        name: 'rut' },
      { data: 'name',       name: 'name' },
      { data: 'email',      name: 'email' },
      { data: 'lugar_cita', name: 'lugar_cita' },
      { data: 'created_at', name: 'created_at' },

      // === Activo: un solo botón estado actual (toggle) ===
      {
        data: 'activo', orderable: false, searchable: false,
        render: function (value, type, row) {
          const isOn = Number(value) === 1;
          // verde si activo, gris/rojo si inactivo
          const classes = isOn
            ? 'border-emerald-500 text-emerald-700 hover:bg-emerald-50 dark:text-emerald-300 dark:border-emerald-600 dark:hover:bg-emerald-900/30'
            : 'border-red-400 text-red-700 hover:bg-red-50 dark:text-red-300 dark:border-red-600 dark:hover:bg-red-900/30';
          const label = isOn ? 'Activo' : 'Inactivo';

          return `
            <button type="button"
                    class="btn-toggle-active inline-flex items-center gap-2 border rounded-full px-3 py-1 text-xs font-semibold transition ${classes}"
                    data-id="${row.id}">
              ${label}
            </button>
          `;
        }
      },

// === Bloqueado: solo mostrar si está bloqueado (clic = desbloquear) ===
{
  data: 'is_blocked', orderable: false, searchable: false,
  render: function (value, type, row) {

    // 🔍 Muestra en consola lo que llega en cada fila
    console.log('Usuario:', row.id, '| is_blocked =', value);

    // Solo muestra el botón si is_blocked = 1
    if (Number(value) === 1) {
      return `
        <button type="button"
                class="btn-toggle-block inline-flex items-center gap-2 border rounded-full px-3 py-1 text-xs font-semibold transition
                       border-amber-500 text-amber-700 hover:bg-amber-50
                       dark:text-amber-300 dark:border-amber-600 dark:hover:bg-amber-900/30"
                data-id="${row.id}">
          Bloqueado
        </button>
      `;
    }

    // Si no está bloqueado, muestra el valor (0 o null) solo para depurar
    return `<span class="text-xs text-gray-400"></span>`;
  }
},


      // === Acciones (Eliminar con SweetAlert) ===
      {
        data: null, orderable: false, searchable: false,
        render: function (_v, _t, row) {
          return `
            <div class="flex justify-center">
              <button type="button"
                      class="btn-delete inline-flex items-center gap-1 border border-red-400 text-red-700 rounded-full px-3 py-1 text-xs font-semibold
                             hover:bg-red-50 dark:text-red-300 dark:border-red-600 dark:hover:bg-red-900/30"
                      data-id="${row.id}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M9 3h6a1 1 0 011 1v1h4v2H4V5h4V4a1 1 0 011-1zm-3 6h12l-1 11a2 2 0 01-2 2H9a2 2 0 01-2-2L6 9z"/>
                </svg>
                Eliminar
              </button>
            </div>
          `;
        }
      },
    ],
    language: { url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json' },
    createdRow: function (row) {
      row.classList.add('border-b','border-gray-100','dark:border-gray-800');
    }
  });

  // --- Delegados de eventos ---

  // Activo/Inactivo (toggle)
  $('#tablaRegistrados').on('click', '.btn-toggle-active', function () {
    const id = this.dataset.id;
    $.ajax({
      url: '{{ route('admin.users.toggle.active', '__ID__') }}'.replace('__ID__', id),
      type: 'PATCH',
      success: () => table.ajax.reload(null, false),
      error: () => alert('Error al cambiar estado activo')
    });
  });

  // Bloqueado -> Desbloquear
  $('#tablaRegistrados').on('click', '.btn-toggle-block', function () {
    const id = this.dataset.id;
    $.ajax({
      url: '{{ route('admin.users.toggle.block', '__ID__') }}'.replace('__ID__', id),
      type: 'PATCH',
      success: () => table.ajax.reload(null, false),
      error: () => alert('Error al cambiar bloqueo')
    });
  });

  // Eliminar (SweetAlert)
  $('#tablaRegistrados').on('click', '.btn-delete', function () {
    const id = this.dataset.id;

    Swal.fire({
      icon: 'warning',
      title: '¿Eliminar cuenta?',
      text: 'Esta acción no se puede deshacer.',
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#dc2626',
      cancelButtonColor: '#6b7280',
      background: document.documentElement.classList.contains('dark') ? '#111827' : '#ffffff',
      color: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#111827'
    }).then(result => {
      if (result.isConfirmed) {
        $.ajax({
          url: '{{ route('admin.users.delete', '__ID__') }}'.replace('__ID__', id),
          type: 'DELETE',
          success: () => {
            Swal.fire({ icon: 'success', title: 'Eliminado', text: 'Usuario eliminado.', timer: 1800, showConfirmButton: false });
            table.ajax.reload(null, false);
          },
          error: () => {
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo eliminar el usuario.' });
          }
        });
      }
    });
  });
});
</script>
@endpush
