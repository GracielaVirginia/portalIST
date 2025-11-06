@extends('layouts.admin')

@section('title', 'Citas — Admin')

@section('admin')
<div class="px-6 py-6">
  {{-- Acciones top / breadcrumb --}}
  <div class="flex items-center justify-between mb-4">
    <x-admin.nav-actions
      backHref="{{ route('admin.dashboard') }}"
      logoutRoute="admin.logout"
      variant="inline"
    />
  </div>

  <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-4">
    <table id="tablaCitas" class="display w-full">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Hora</th>
          <th>Profesional</th>
          <th>Paciente</th>
          <th>Tipo</th>
          <th>Estado</th>
          <th class="text-right">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($citas as $c)
          <tr data-id="{{ $c->id }}">
            <td class="whitespace-nowrap">{{ \Illuminate\Support\Carbon::parse($c->fecha)->format('d-m-Y') }}</td>
            <td class="whitespace-nowrap font-medium">{{ $c->hora_inicio }}–{{ $c->hora_fin }}</td>

            <td>
              @if($c->profesional)
                <div class="font-semibold text-purple-900 dark:text-purple-200">
                  {{ trim(($c->profesional->nombres ?? '').' '.($c->profesional->apellidos ?? '')) }}
                </div>
              @else
                <span class="text-gray-400 text-sm">—</span>
              @endif
            </td>

            <td>
              @if(method_exists($c,'paciente') && $c->paciente)
                <div class="text-sm">{{ $c->paciente->name ?? ($c->paciente->email ?? '—') }}</div>
              @else
                <span class="text-gray-400 text-sm">—</span>
              @endif
            </td>

            <td class="whitespace-nowrap">
              <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold
                {{ $c->tipo_atencion === 'remota'
                    ? 'bg-cyan-100 text-cyan-800'
                    : 'bg-violet-100 text-violet-800' }}">
                {{ ucfirst($c->tipo_atencion) }}
              </span>
            </td>

            {{-- ====== BADGE ESTADO (reservada <-> confirmada) ====== --}}
            <td class="whitespace-nowrap">
              @php
                $estado = $c->estado; // 'reservada' | 'confirmada' | 'cancelada' | 'atendida'
                $isToggleable = in_array($estado, ['reservada','confirmada']);
                $badgeClass = $estado === 'confirmada'
                  ? 'bg-yellow-100 text-yellow-800'
                  : 'bg-rose-100 text-rose-700'; // 'reservada' => rosa
                $label = $estado === 'reservada' ? 'Reservada' : ucfirst($estado);
              @endphp

              <span
                class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-bold select-none
                       {{ $badgeClass }} {{ $isToggleable ? 'cursor-pointer hover:opacity-90' : 'cursor-default' }}"
                title="{{ $isToggleable ? 'Click para cambiar estado' : '' }}"
                data-role="estado-badge"
                data-id="{{ $c->id }}"
                data-state="{{ $estado }}"
                data-confirm-url="{{ route('admin.citas.confirmar', $c) }}"
                data-reservada-url="{{ route('admin.citas.reservada', $c) }}" {{-- <- ver rutas en el paso 2 --}}
                data-clickable="{{ $isToggleable ? '1' : '0' }}"
              >
                {{ $label }}
              </span>
            </td>

            {{-- ====== ACCIONES ====== --}}
            <td class="text-right">
              <div class="inline-flex items-center gap-2">
                <a href="{{ route('admin.citas.edit', $c) }}" class="btn-action">✏️ Editar</a>

                <form action="{{ route('admin.citas.destroy', $c) }}" method="POST" class="form-eliminar inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn-action btn-eliminar">🗑️ Eliminar</button>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    // DataTable
    new DataTable('#tablaCitas', {
      pageLength: 10,
      order: [[0, 'desc'], [1, 'asc']],
      language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' }
    });

    // SWEET ALERT eliminar
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
      btn.addEventListener('click', e => {
        const form = btn.closest('.form-eliminar');
        e.preventDefault();
        Swal.fire({
          title: '¿Eliminar cita?',
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

{{-- ====== BADGE: cambiar estado con SweetAlert + fetch ====== --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  function setBadge(el, newState) {
    el.dataset.state = newState;
    el.classList.remove('bg-rose-100','text-rose-700','bg-yellow-100','text-yellow-800');

    if (newState === 'reservada') {
      el.textContent = 'Reservada';
      el.classList.add('bg-rose-100','text-rose-700');
      el.title = 'Click para cambiar estado';
      el.dataset.clickable = '1';
      el.classList.add('cursor-pointer');
    } else if (newState === 'confirmada') {
      el.textContent = 'Confirmada';
      el.classList.add('bg-yellow-100','text-yellow-800');
      el.title = 'Click para cambiar estado';
      el.dataset.clickable = '1';
      el.classList.add('cursor-pointer');
    } else {
      // otros estados: cancelar/atendida --> no toggleables desde badge
      el.textContent = newState.charAt(0).toUpperCase() + newState.slice(1);
      el.dataset.clickable = '0';
      el.title = '';
      el.classList.remove('cursor-pointer');
    }
  }

  document.querySelectorAll('[data-role="estado-badge"]').forEach(el => {
    if (el.dataset.clickable !== '1') return;

    el.addEventListener('click', async () => {
      const current = el.dataset.state; // 'reservada' | 'confirmada'
      const isToConfirm = current === 'reservada';
      const url = isToConfirm ? el.dataset.confirmUrl : el.dataset.reservadaUrl;
      const actionText = isToConfirm ? 'confirmar' : 'marcar como reservada';

      const res = await Swal.fire({
        title: `¿Deseas ${actionText} esta cita?`,
        text: isToConfirm
          ? 'La cita quedará confirmada.'
          : 'La cita volverá a estado “Reservada”.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#7e22ce',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar',
      });
      if (!res.isConfirmed) return;

      try {
        // POST + _method PATCH, JSON/AJAX headers
        const r = await fetch(url, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ _method: 'PATCH' })
        });

        if (!r.ok) {
          const txt = await r.text();
          console.error('Fallo al cambiar estado', r.status, txt);
          throw new Error('HTTP ' + r.status);
        }

        // ok → actualiza badge
        setBadge(el, isToConfirm ? 'confirmada' : 'reservada');

        Swal.fire({
          title: 'Listo',
          text: isToConfirm ? 'Cita confirmada.' : 'Cita marcada como Reservada.',
          icon: 'success',
          timer: 1400,
          showConfirmButton: false
        });
      } catch (e) {
        Swal.fire('Error', 'No se pudo actualizar el estado.', 'error');
      }
    });
  });
});
</script>
@endpush
