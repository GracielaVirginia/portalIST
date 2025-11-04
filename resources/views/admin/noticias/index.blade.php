@extends('layouts.admin')

@section('title', 'Noticias — Admin')

@section('admin')
    <div class="px-6 py-6">
        {{-- Botones superiores --}}
        <div class="flex items-center justify-between mb-4">
  <x-admin.nav-actions
    backHref="{{ route('admin.dashboard') }}"
    logoutRoute="admin.logout"
    variant="inline"   {{-- o "sticky" si la tabla es larga --}}
  />       

</div>
        {{-- Botón agregar noticia --}}
        <div class="flex justify-end mb-3">
            <a href="{{ route('admin.noticias.create') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-purple-900 text-white text-sm font-semibold px-4 py-2 shadow hover:shadow-md">
                ➕ Agregar noticia
            </a>
        </div>

        {{-- Tabla de noticias --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-4">
            <table id="tablaNoticias" class="display w-full">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Título</th>
                        <th>Bajada</th>
                        <th>Destacada</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($noticias as $n)
                        <tr data-id="{{ $n->id }}">
                            <td>
                                @if ($n->imagen_url)
                                    <img src="{{ $n->imagen_url }}" alt="" class="w-20 h-12 object-cover rounded">
                                @else
                                    <span class="text-gray-400 text-sm">Sin imagen</span>
                                @endif
                            </td>
                            <td class="font-semibold text-purple-900 dark:text-purple-200">{{ $n->titulo }}</td>
                            <td class="text-sm text-gray-600 dark:text-gray-300">{{ Str::limit($n->bajada, 80) }}</td>
                            <td>
                                @if ($n->destacada)
                                    <span
                                        class="inline-flex items-center gap-1 text-green-700 bg-green-100 px-2 py-1 rounded text-xs font-bold">
                                        ● En home
                                    </span>
                                @else
                                    <span class="text-xs text-gray-500">—</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.noticias.edit', $n) }}" class="btn-action">✏️ Editar</a>

                                    {{-- SweetAlert para eliminar --}}
                                    <form action="{{ route('admin.noticias.destroy', $n) }}" method="POST"
                                        class="form-eliminar inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-eliminar cursor-pointer">🗑️
                                            Eliminar</button>
                                    </form>
                                    {{-- Toggle: poner en home --}}
                                    <form action="{{ route('admin.noticias.toggle-home', $n) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn-action cursor-pointer"
                                            @disabled($n->destacada)>
                                            🏠 Poner en home
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@push('scripts')
    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Inicializa DataTable
            new DataTable('#tablaNoticias', {
                pageLength: 10,
                order: [
                    [1, 'asc']
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                }
            });

            // SWEET ALERT eliminar
            document.querySelectorAll('.btn-eliminar').forEach(btn => {
                btn.addEventListener('click', e => {
                    const form = btn.closest('.form-eliminar');
                    Swal.fire({
                        title: '¿Eliminar noticia?',
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
