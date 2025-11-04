@extends('layouts.admin')
@section('title','Profesionales')

@section('admin')
<div class="px-6 py-6">
  <div class="flex items-center justify-between mb-4">
          <x-admin.nav-actions
    backHref="{{ route('admin.dashboard') }}"
    logoutRoute="admin.logout"
    variant="inline"
  />

    <h1 class="text-2xl font-bold text-purple-700 dark:text-purple-200">👩‍⚕️ Profesionales</h1>
    <a href="{{ route('profesionales.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">Nuevo profesional</a>
  </div>

  <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
    <table id="tablaProfesionales" class="min-w-full">
      <thead>
        <tr class="text-left text-xs sm:text-sm text-purple-900 dark:text-purple-200">
          <th class="py-2">ID</th>
          <th class="py-2">Nombre</th>
          <th class="py-2">RUT</th>
          <th class="py-2">Email</th>
          <th class="py-2">Teléfono</th>
          <th class="py-2">Sucursal</th>
          <th class="py-2">Tipo</th>
          <th class="py-2 text-center">Acciones</th>
        </tr>
      </thead>
      <tbody class="text-sm">
        @foreach($profesionales as $row)
          <tr class="border-b border-gray-100 dark:border-gray-800">
            <td class="py-2">{{ $row->id }}</td>
            <td class="py-2">{{ trim(($row->nombres ?? '').' '.($row->apellidos ?? '')) }}</td>
            <td class="py-2">{{ $row->rut }}</td>
            <td class="py-2">{{ $row->email }}</td>
            <td class="py-2">{{ $row->telefono }}</td>
            <td class="py-2">{{ $row->sucursal->nombre ?? '-' }}</td>
            <td class="py-2">{{ $row->tipoProfesional->nombre ?? '-' }}</td>
            <td class="py-2">
              <div class="flex justify-center gap-2">
                <a href="{{ route('profesionales.edit', $row) }}"
                   class="border border-purple-400 text-purple-700 rounded-full px-3 py-1 text-xs font-semibold hover:bg-purple-50">Editar</a>

                <button type="button"
                        class="btn-del border border-red-400 text-red-700 rounded-full px-3 py-1 text-xs font-semibold hover:bg-red-50"
                        data-id="{{ $row->id }}">
                  Eliminar
                </button>
                <form id="del-prof-{{ $row->id }}" action="{{ route('profesionales.destroy', $row) }}" method="POST" class="hidden">
                  @csrf @method('DELETE')
                </form>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
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
  $('#tablaProfesionales').DataTable({
    pageLength: 10,
    order: [[0, 'asc']],
    language: { url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json' },
  });

  $(document).on('click', '.btn-del', function () {
    const id = this.dataset.id;
    Swal.fire({
      icon: 'warning',
      title: '¿Eliminar profesional?',
      text: 'Esta acción no se puede deshacer.',
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#dc2626',
    }).then(res => {
      if (res.isConfirmed) {
        document.getElementById(`del-prof-${id}`).submit();
      }
    });
  });
});
</script>
@endpush
