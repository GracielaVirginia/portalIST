@extends('layouts.admin')
@section('title','Bloqueos')

@section('admin')
<div class="px-6 py-6">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-purple-700 dark:text-purple-200">🚫 Bloqueos</h1>
    <a href="{{ route('bloqueos.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">Nuevo bloqueo</a>
  </div>

  <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
    <table id="tablaBloqueos" class="min-w-full">
      <thead>
        <tr class="text-left text-xs sm:text-sm text-purple-900 dark:text-purple-200">
          <th class="py-2">ID</th>
          <th class="py-2">Profesional</th>
          <th class="py-2">Sucursal</th>
          <th class="py-2">Tipo</th>
          <th class="py-2">Fecha/Día</th>
          <th class="py-2">Inicio</th>
          <th class="py-2">Duración</th>
          <th class="py-2">Horario</th>
          <th class="py-2">Motivo</th>
          <th class="py-2 text-center">Acciones</th>
        </tr>
      </thead>
      <tbody class="text-sm">
        @foreach($bloqueos as $b)
          <tr class="border-b border-gray-100 dark:border-gray-800">
            <td class="py-2">{{ $b->id }}</td>
            <td class="py-2">{{ $b->profesional->nombres }} {{ $b->profesional->apellidos }}</td>
            <td class="py-2">{{ $b->sucursal->nombre ?? '-' }}</td>
            <td class="py-2">
              @if($b->fecha) Puntual @elseif($b->dia_semana) Recurrente @else - @endif
            </td>
            <td class="py-2">
              {{ $b->fecha ? $b->fecha->format('Y-m-d') : ucfirst($b->dia_semana ?? '-') }}
            </td>
            <td class="py-2">{{ \Illuminate\Support\Str::of($b->inicio)->limit(5,'') }}</td>
            <td class="py-2">{{ $b->duracion }} min</td>
            <td class="py-2">
              @if($b->horario)
                {{ ucfirst($b->horario->dia_semana) }} {{ \Illuminate\Support\Str::of($b->horario->hora_inicio)->limit(5,'') }}-{{ \Illuminate\Support\Str::of($b->horario->hora_fin)->limit(5,'') }}
              @else
                —
              @endif
            </td>
            <td class="py-2">{{ $b->motivo ?? '—' }}</td>
            <td class="py-2">
              <div class="flex justify-center gap-2">
                <a href="{{ route('bloqueos.edit', $b) }}"
                   class="border border-purple-400 text-purple-700 rounded-full px-3 py-1 text-xs font-semibold hover:bg-purple-50">Editar</a>

                <button type="button"
                        class="btn-del border border-red-400 text-red-700 rounded-full px-3 py-1 text-xs font-semibold hover:bg-red-50"
                        data-id="{{ $b->id }}">
                  Eliminar
                </button>
                <form id="del-b-{{ $b->id }}" action="{{ route('bloqueos.destroy', $b) }}" method="POST" class="hidden">
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
  $('#tablaBloqueos').DataTable({
    pageLength: 10,
    order: [[0, 'asc']],
    language: { url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json' },
  });

  $(document).on('click', '.btn-del', function () {
    const id = this.dataset.id;
    Swal.fire({
      icon: 'warning',
      title: '¿Eliminar bloqueo?',
      text: 'Esta acción no se puede deshacer.',
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#dc2626',
    }).then(res => {
      if (res.isConfirmed) {
        document.getElementById(`del-b-${id}`).submit();
      }
    });
  });
});
</script>
@endpush
