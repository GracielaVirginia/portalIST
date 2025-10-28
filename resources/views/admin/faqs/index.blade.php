@extends('layouts.app')
@section('title','FAQs — Admin')

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

  {{-- Botón Nueva FAQ (abajo a la derecha, con el mismo margen que usas) --}}
  <div class="flex justify-end mb-3">
    <a href="{{ route('admin.faqs.create') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-purple-900 text-white text-sm font-semibold px-4 py-2 shadow hover:shadow-md">
      ➕ Nueva
    </a>
  </div>

  {{-- Tabla FAQs --}}
  <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-4">
    <table id="tablaFaqs" class="display w-full">
      <thead>
        <tr>
          <th>Pregunta</th>
          <th>Categoría</th>
          <th>Activa</th>
          <th class="text-right">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($faqs as $f)
          <tr data-id="{{ $f->id }}">
            <td class="font-semibold text-purple-900 dark:text-purple-200">
              {{ $f->question }}
            </td>
            <td class="text-sm text-gray-600 dark:text-gray-300">
              {{ $f->category ?: '—' }}
            </td>
            <td>
              @if($f->is_active)
                <span class="inline-flex items-center gap-1 text-green-700 bg-green-100 px-2 py-1 rounded text-xs font-bold">
                  Sí
                </span>
              @else
                <span class="text-xs text-gray-500">No</span>
              @endif
            </td>
            <td class="text-right">
              <div class="inline-flex items-center gap-2">
                <a href="{{ route('admin.faqs.edit', $f) }}" class="btn-action cursor-pointer">✏️ Editar</a>

                {{-- Eliminar con SweetAlert2 --}}
                <form action="{{ route('admin.faqs.destroy', $f) }}" method="POST" class="form-eliminar inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn-action btn-eliminar cursor-pointer">🗑️ Eliminar</button>
                </form>

                {{-- Toggle activa/inactiva --}}
                <form action="{{ route('admin.faqs.toggle', $f) }}" method="POST" class="inline">
                  @csrf @method('PATCH')
                  <button type="submit" class="btn-action cursor-pointer">
                    ↔️ Toggle
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="py-4 text-center text-gray-500">Sin FAQs</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- CSRF meta si lo necesitas en JS --}}
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@push('scripts')
  {{-- SweetAlert2 --}}
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Inicializa DataTable (usa tu skin morado #tablaFaqs del CSS ya agregado)
      new DataTable('#tablaFaqs', {
        pageLength: 10,
        order: [[0, 'asc']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' }
      });

      // SweetAlert2 para confirmar eliminación
      document.querySelectorAll('.btn-eliminar').forEach(btn => {
        btn.addEventListener('click', e => {
          e.preventDefault();
          const form = btn.closest('.form-eliminar');

          Swal.fire({
            title: '¿Eliminar FAQ?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#7e22ce',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
          }).then(result => {
            if (result.isConfirmed) {
              form.submit();
            }
          });
        });
      });
    });
  </script>
@endpush
