@extends('layouts.app')

@section('title', 'Usuarios registrados')

@section('content')
<div class="px-6 py-6">
  {{-- Botones volver / logout --}}
  <x-admin.nav-actions
    backHref="{{ route('admin.dashboard') }}"
    logoutRoute="admin.logout"
    variant="inline"
  />

  <h1 class="text-2xl font-bold text-purple-700 dark:text-purple-200 mb-4">
    👤 Usuarios registrados
  </h1>

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

        // === Activo ===
        {
          data: 'activo', orderable: false, searchable: false,
          render: function (value, type, row) {
            const isOn = value == 1;
            const badge = isOn
              ? 'bg-emerald-50 text-emerald-700 border-emerald-300 dark:bg-emerald-900/30 dark:text-emerald-200 dark:border-emerald-700'
              : 'bg-gray-50 text-gray-700 border-gray-300 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700';
            const text = isOn ? 'Activo' : 'Inactivo';

            const btn = isOn
              ? 'border-emerald-400 text-emerald-700 hover:bg-emerald-50 dark:text-emerald-300 dark:border-emerald-600 dark:hover:bg-emerald-900/30'
              : 'border-gray-400 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-800';

            return `
              <div class="flex items-center gap-2">
                <span class="inline-flex items-center border rounded-full px-2.5 py-0.5 text-xs font-semibold ${badge}">${text}</span>
                <button type="button" class="border rounded-full px-3 py-1 text-xs font-semibold transition btn-toggle-active ${btn}"
                        data-id="${row.id}">
                  ${isOn ? 'Desactivar' : 'Activar'}
                </button>
              </div>
            `;
          }
        },

        // === Bloqueado ===
        {
          data: 'is_blocked', orderable: false, searchable: false,
          render: function (value, type, row) {
            const blocked = value == 1;
            const badge = blocked
              ? 'bg-red-50 text-red-700 border-red-300 dark:bg-red-900/30 dark:text-red-200 dark:border-red-700'
              : 'bg-emerald-50 text-emerald-700 border-emerald-300 dark:bg-emerald-900/30 dark:text-emerald-200 dark:border-emerald-700';
            const text = blocked ? 'Bloqueado' : 'OK';

            const btn = blocked
              ? 'border-red-400 text-red-700 hover:bg-red-50 dark:text-red-300 dark:border-red-600 dark:hover:bg-red-900/30'
              : 'border-amber-400 text-amber-700 hover:bg-amber-50 dark:text-amber-300 dark:border-amber-600 dark:hover:bg-amber-900/30';

            return `
              <div class="flex items-center gap-2">
                <span class="inline-flex items-center border rounded-full px-2.5 py-0.5 text-xs font-semibold ${badge}">${text}</span>
                <button type="button" class="border rounded-full px-3 py-1 text-xs font-semibold transition btn-toggle-block ${btn}"
                        data-id="${row.id}">
                  ${blocked ? 'Desbloquear' : 'Bloquear'}
                </button>
              </div>
            `;
          }
        },

        // === Acciones (Eliminar con SweetAlert) ===
        {
          data: null, orderable: false, searchable: false,
          render: function (_v, _t, row) {
            return `
              <div class="flex justify-center">
                <button type="button"
                        class="inline-flex items-center gap-1 border border-red-400 text-red-700 rounded-full px-3 py-1 text-xs font-semibold
                               hover:bg-red-50 dark:text-red-300 dark:border-red-600 dark:hover:bg-red-900/30 btn-delete"
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

    // === Eventos ===
    $('#tablaRegistrados').on('click', '.btn-toggle-active', function () {
      const id = this.dataset.id;
      $.ajax({
        url: '{{ route('admin.users.toggle.active', '__ID__') }}'.replace('__ID__', id),
        type: 'PATCH',
        success: () => table.ajax.reload(null, false),
        error: () => alert('Error al cambiar estado activo')
      });
    });

    $('#tablaRegistrados').on('click', '.btn-toggle-block', function () {
      const id = this.dataset.id;
      $.ajax({
        url: '{{ route('admin.users.toggle.block', '__ID__') }}'.replace('__ID__', id),
        type: 'PATCH',
        success: () => table.ajax.reload(null, false),
        error: () => alert('Error al cambiar bloqueo')
      });
    });

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
              Swal.fire({
                icon: 'success',
                title: 'Eliminado',
                text: 'El usuario fue eliminado correctamente.',
                timer: 1800,
                showConfirmButton: false
              });
              table.ajax.reload(null, false);
            },
            error: () => {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo eliminar el usuario.'
              });
            }
          });
        }
      });
    });
  });
</script>
@endpush
