@extends('layouts.admin')

@section('admin')
<div class="max-w-6xl mx-auto mt-8">
            <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
  <x-admin.nav-actions
    backHref="{{ route('admin.dashboard') }}"
    logoutRoute="admin.logout"
    variant="inline"   {{-- o "sticky" si la tabla es larga --}}
  />            </div>
        </div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-purple-700 dark:text-purple-300">Promociones</h1>
        <a href="{{ route('admin.promociones.create') }}"
           class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-semibold shadow">
            + Nueva promoción
        </a>
    </div>

    <div class="overflow-hidden bg-white dark:bg-gray-900 rounded-2xl shadow">
        <table id="tablaPromociones" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-purple-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Título</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Subtítulo</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold">Activa</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold">Destacada</th>
                    <th class="px-4 py-3 text-right text-sm font-semibold">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach($promos as $promo)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-200">
                        {{ $promo->titulo }}
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                        {{ Str::limit($promo->subtitulo, 60) }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="{{ route('admin.promociones.toggle', $promo) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="cursor-pointer text-xs px-3 py-1 rounded-full font-semibold
                                {{ $promo->activo ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                                {{ $promo->activo ? 'Activa' : 'Inactiva' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="{{ route('admin.promociones.destacar', $promo) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="cursor-pointer text-xs px-3 py-1 rounded-full font-semibold
                                {{ $promo->destacada ? 'bg-purple-100 text-purple-700' : 'bg-gray-200 text-gray-600' }}">
                                {{ $promo->destacada ? 'Sí' : 'No' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.promociones.edit', $promo) }}"
                           class="text-purple-600 hover:underline font-semibold text-sm">Editar</a>
<form method="POST" action="{{ route('admin.promociones.destroy', $promo) }}" class="inline eliminar-form">
    @csrf
    @method('DELETE')
    <button type="button"
        class="js-delete text-red-500 hover:text-red-700 font-semibold text-sm"
        data-titulo="{{ $promo->titulo }}">
        Eliminar
    </button>
</form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($promos->isEmpty())
        <p class="text-center text-gray-500 dark:text-gray-400 py-8">No hay promociones registradas.</p>
        @endif
    </div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- Confirmación con SweetAlert2 ---
    document.querySelectorAll('.js-delete').forEach(btn => {
        btn.addEventListener('click', e => {
            const form = btn.closest('form');
            const titulo = btn.dataset.titulo || 'la promoción';

            Swal.fire({
                title: `¿Eliminar ${titulo}?`,
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#e02424',
                cancelButtonColor: '#6b7280',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'bg-red-600 hover:bg-red-700 focus:ring-2 focus:ring-red-400 text-white px-4 py-2 rounded-lg font-semibold',
                    cancelButton: 'bg-gray-200 hover:bg-gray-300 focus:ring-2 focus:ring-gray-300 text-gray-800 px-4 py-2 rounded-lg font-semibold'
                }
            }).then(result => {
                if (result.isConfirmed) form.submit();
            });
        });
    });
});
</script>
@endpush
