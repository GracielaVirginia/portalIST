@extends('layouts.admin')

@section('title', 'Usuarios no registrados')

@section('admin')
  <div class="px-6 py-6">
<div class="px-6 py-6">
  <x-admin.nav-actions
    backHref="{{ route('admin.dashboard') }}"
    logoutRoute="admin.logout"
    variant="inline"   {{-- o "sticky" si la tabla es larga --}}
  />
    <h1 class="text-2xl font-bold text-purple-700 dark:text-purple-200">👤 Usuarios no registrados</h1>
  </div>

  {{-- Botón alineado a la derecha --}}
  <div class="flex justify-end mb-3">
    <a href="{{ route('admin.users.create') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-purple-900 text-white text-sm font-semibold px-4 py-2 shadow hover:shadow-md hover:bg-purple-800 transition">
       <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
           <path d="M11 11V5h2v6h6v2h-6v6h-2v-6H5v-2h6z" />
       </svg>
       Agregar usuario al portal
    </a>
  </div>

  {{-- ⚙️ Config para inline-edit (patrones de URL usados por los JS) --}}
  <div id="dtConfig"
       data-email-update-pattern="{{ route('admin.users.unregistered.email', '__RUT__') }}"
       data-rut-update-pattern="{{ route('admin.users.unregistered.rut', '__RUT__') }}">
  </div>

  {{-- Fallbacks por si el JS carga antes del div --}}
  <script>
    document.body.dataset.emailUpdatePattern = "{{ route('admin.users.unregistered.email', '__RUT__') }}";
    document.body.dataset.rutUpdatePattern   = "{{ route('admin.users.unregistered.rut', '__RUT__') }}";
  </script>

  <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
    <table id="tablaNoRegistrados" class="min-w-full">
      <thead>
        <tr class="text-left text-xs sm:text-sm text-purple-900 dark:text-purple-200">
          <th class="py-2">Documento</th>
          <th class="py-2">Paciente</th>
          <th class="py-2">Email</th>
          <th class="py-2">Teléfono</th>
          <th class="py-2">Día</th>
          <th class="py-2">Número de caso</th>
          <th class="py-2">Detalles</th>
          <th class="py-2">Acciones</th>
        </tr>
      </thead>
      <tbody class="text-sm"></tbody>
    </table>
  </div>
@endsection

@push('styles')
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
@endpush

@push('scripts')
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const table = $('#tablaNoRegistrados').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('admin.users.unregistered.data') }}',
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        order: [[4, 'desc'], [0, 'asc']], // Día desc, luego Documento
        columns: [
          // 👇 Documento (RUT) editable inline
          {
            data: 'numero_documento',
            name: 'numero_documento',
            render: function (data, type, row) {
              const rut = data ?? '';
              return `
                <div class="flex items-center gap-2">
                  <span class="rut-text">${rut}</span>
                  <button type="button"
                          class="js-rut-edit inline-flex items-center gap-1 text-purple-700 dark:text-purple-300 hover:underline"
                          title="Editar RUT"
                          data-rut="${rut}">
                    ✏️ <span class="hidden sm:inline">Editar</span>
                  </button>
                </div>
              `;
            }
          },

          { data: 'nombre_paciente',  name: 'nombre_paciente' },

          // 👇 Email editable inline
          {
            data: 'email',
            name: 'email',
            render: function (data, type, row) {
              const email = data ?? '';
              const rut   = row.numero_documento;
              return `
                <div class="flex items-center gap-2">
                  <span class="email-text">${email}</span>
                  <button type="button"
                          class="js-email-edit inline-flex items-center gap-1 text-purple-700 dark:text-purple-300 hover:underline"
                          title="Editar email"
                          data-rut="${rut}"
                          data-email="${email}">
                    ✏️ <span class="hidden sm:inline">Editar</span>
                  </button>
                </div>
              `;
            }
          },

          { data: 'telefono', name: 'telefono' },
          { data: 'dia',      name: 'dia' },
          { data: 'gestiones',name: 'gestiones' },
          {
            data: 'detalles',
            name: 'detalles',
            orderable: false,
            searchable: true,
            render: function(data) {
              if (!Array.isArray(data) || !data.length) return '';
              return '<ul class="list-disc ms-4">' + data.map(d => `<li>${d}</li>`).join('') + '</ul>';
            }
          },
          {
            data: null,
            orderable: false,
            searchable: false,
            render: function(row) {
              const editUrl = '{{ route('admin.users.unregistered.edit', '__RUT__') }}'
                .replace('__RUT__', encodeURIComponent(row.numero_documento));

              return `
                <div class="flex items-center gap-2">
                  <a href="${editUrl}" class="btn-action btn-action--edit" title="Editar usuario">
                    ✏️ <span>Editar</span>
                  </a>
                  <span class="btn-action" title="Email">✉️</span>
                </div>
              `;
            }
          },
        ],
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json' },
        createdRow: function(row) {
          row.classList.add('border-b', 'border-gray-100', 'dark:border-gray-800');
        },
        initComplete: function() {
          const $filter = $('#tablaNoRegistrados_filter');
          const $input = $filter.find('input');
          $input.attr('placeholder', 'Buscar…');
          $input.on('keypress', function(e) {
            if (e.which === 13) {
              table.search($input.val()).draw();
            }
          });
        }
      });
    });
  </script>
@endpush
