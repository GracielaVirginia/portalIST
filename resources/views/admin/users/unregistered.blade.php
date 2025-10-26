@extends('layouts.app')

@section('title', 'Usuarios no registrados')

@section('content')
    <div class="px-6 py-6">
        <a href="{{ route('admin.dashboard') }}"
            class="inline-flex items-center gap-2 bg-purple-900 text-white text-sm font-semibold px-4 py-2 rounded-full shadow-sm hover:bg-purple-800 hover:shadow-md transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Volver al dashboard
        </a>
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
    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
        <table id="tablaNoRegistrados" class="min-w-full">

            <thead>
                <tr class="text-left text-xs sm:text-sm text-purple-900 dark:text-purple-200">
                    <th class="py-2">Documento</th>
                    <th class="py-2">Paciente</th>
                    <th class="py-2">Email</th>
                    <th class="py-2">Teléfono</th>
                    <th class="py-2">Día</th>
                    <th class="py-2"># Gestiones</th>
                    <th class="py-2">Detalles</th>
                    <th class="py-2">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-sm"></tbody>
        </table>
    </div>
    </div>
    </div>
@endsection

@push('styles')
    {{-- CSS base de DataTables (opcional si no lo importas por NPM) --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const table = $('#tablaNoRegistrados').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.users.unregistered.data') }}',
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [
                    [4, 'desc'],
                    [0, 'asc']
                ], // Día desc, luego Documento
                columns: [{
                        data: 'numero_documento',
                        name: 'numero_documento'
                    },
                    {
                        data: 'nombre_paciente',
                        name: 'nombre_paciente'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'telefono',
                        name: 'telefono'
                    },
                    {
                        data: 'dia',
                        name: 'dia'
                    },
                    {
                        data: 'gestiones',
                        name: 'gestiones'
                    },
                    {
                        data: 'detalles',
                        name: 'detalles',
                        orderable: false,
                        searchable: true,
                        render: function(data) {
                            if (!Array.isArray(data) || !data.length) return '';
                            return '<ul class="list-disc ms-4">' + data.map(d => `<li>${d}</li>`)
                                .join('') + '</ul>';
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
                  <span class="btn-action" title="Email">
                    ✉️
                  </span>
                </div>
              `;
                        }
                    },
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
                },
                createdRow: function(row) {
                    row.classList.add('border-b', 'border-gray-100', 'dark:border-gray-800');
                },
                initComplete: function() {
                    // Placeholder + botón Buscar
                    const $filter = $('#tablaNoRegistrados_filter');
                    const $input = $filter.find('input');
                    $input.attr('placeholder', 'Buscar…');

                    //   const $btn = $('<button class="dt-search-btn">Buscar</button>');
                    //   $btn.on('click', function () {
                    //     table.search($input.val()).draw();
                    //   });
                    //   $filter.append($btn);

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
