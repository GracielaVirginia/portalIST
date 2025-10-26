@extends('layouts.app')

@section('title', 'Usuarios registrados')

@section('content')
<div class="px-6 py-6">
  <a href="{{ route('admin.dashboard') }}"
   class="inline-flex items-center gap-2 bg-purple-900 text-white text-sm font-semibold px-4 py-2 rounded-full shadow-sm hover:bg-purple-800 hover:shadow-md transition">
  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
  </svg>
  Volver al dashboard
</a>
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
        </tr>
      </thead>
      <tbody class="text-sm"></tbody>
    </table>
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
    document.addEventListener('DOMContentLoaded', function () {
      $('#tablaRegistrados').DataTable({
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
        ],
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json' },
        createdRow: function (row) {
          row.classList.add('border-b','border-gray-100','dark:border-gray-800');
        }
      });
    });
  </script>
@endpush
