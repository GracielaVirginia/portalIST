@extends('layouts.app')
@section('title', 'Administradores — Admin')

@section('content')
<div class="px-6 py-6">

  {{-- Botones superiores --}}
  <div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
      {{-- Volver al dashboard --}}
      <a href="{{ route('admin.dashboard') }}"
         class="inline-flex items-center gap-2 bg-purple-900 text-white text-sm font-semibold px-4 py-2 rounded-full shadow-sm hover:bg-purple-800 hover:shadow-md transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        Volver al dashboard
      </a>

      {{-- Cerrar sesión --}}
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
                class="inline-flex items-center gap-2 bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-full shadow-sm hover:bg-red-500 hover:shadow-md transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
          </svg>
          Cerrar sesión
        </button>
      </form>
    </div>
  </div>

  {{-- Botón agregar administrador --}}
  <div class="flex justify-end mb-3">
    <a href="{{ route('admin.administradores.create') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-purple-900 text-white text-sm font-semibold px-4 py-2 shadow hover:shadow-md">
      ➕ Crear administrador
    </a>
  </div>

  {{-- Tabla de administradores --}}
  <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-4">
    <table id="tablaAdministradores" class="display w-full">
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Usuario</th>
          <th>Email</th>
          <th>RUT</th>
          <th>Rol</th>
          <th>Especialidad</th>
          <th>Activo</th>
          <th class="text-right">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($admins as $a)
          <tr>
            <td class="font-semibold text-purple-900 dark:text-purple-200">{{ $a->nombre_completo }}</td>
            <td class="text-sm text-gray-700 dark:text-gray-300">{{ $a->user }}</td>
            <td class="text-sm text-gray-700 dark:text-gray-300">{{ $a->email }}</td>
            <td class="text-sm text-gray-700 dark:text-gray-300">{{ $a->rut }}</td>
            <td class="text-sm text-gray-700 dark:text-gray-300">{{ $a->rol }}</td>
            <td class="text-sm text-gray-700 dark:text-gray-300">{{ $a->especialidad ?: '—' }}</td>
            <td>
              @if($a->activo)
                <span class="inline-flex items-center gap-1 text-green-700 bg-green-100 px-2 py-1 rounded text-xs font-bold">✔ Activo</span>
              @else
                <span class="inline-flex items-center gap-1 text-gray-600 bg-gray-100 px-2 py-1 rounded text-xs font-bold">✖ Inactivo</span>
              @endif
            </td>
            <td class="text-right">
              <div class="inline-flex items-center gap-2">
                <a href="{{ route('admin.administradores.edit', $a) }}" class="btn-action cursor-pointer">✏️ Editar</a>

                {{-- Eliminar con SweetAlert --}}
                <form action="{{ route('admin.administradores.destroy', $a) }}" method="POST" class="form-eliminar inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn-action btn-eliminar cursor-pointer">🗑️ Eliminar</button>
                </form>

                {{-- Toggle activo/inactivo --}}
                <form action="{{ route('admin.administradores.toggle', $a) }}" method="POST" class="inline">
                  @csrf @method('PATCH')
                  <button type="submit" class="btn-action cursor-pointer">↔️ Toggle</button>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    new DataTable('#tablaAdministradores', {
      pageLength: 10,
      order: [[0, 'asc']],
      language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' }
    });

    // SweetAlert eliminar
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
      btn.addEventListener('click', e => {
        e.preventDefault();
        const form = btn.closest('.form-eliminar');
        Swal.fire({
          title: '¿Eliminar administrador?',
          text: 'Esta acción no se puede deshacer.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#7e22ce',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sí, eliminar',
          cancelButtonText: 'Cancelar'
        }).then(result => {
          if (result.isConfirmed) form.submit();
        });
      });
    });
  });
</script>
@endpush
