@extends('layouts.admin')

@section('title', 'Tickets de Soporte — Admin')

@section('admin')
  <div class="px-6 py-6">
    {{-- Botones superiores (mismos estilos que Noticias) --}}
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center gap-3">
  <x-admin.nav-actions
    backHref="{{ route('admin.dashboard') }}"
    logoutRoute="admin.logout"
    variant="inline"   {{-- o "sticky" si la tabla es larga --}}
  />
      </div>
    </div>

    {{-- Tabla de tickets --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-4">
      <table id="tablaTickets" class="display w-full">
        <thead>
          <tr>
            <th>ID</th>
            <th>Correo</th>
            <th>RUT</th>
            <th>Teléfono</th>
            <th>Detalle</th>
            <th>Fecha</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
        @foreach ($tickets as $t)
          @php
            $isPend = $t->estado === 'pendiente';
            $badgeClass = $isPend
                ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
                : ($t->estado === 'resuelto'
                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                    : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300');
          @endphp
          <tr data-id="{{ $t->id }}">
            <td class="font-semibold text-purple-900 dark:text-purple-200">{{ $t->id }}</td>
            <td class="text-gray-700 dark:text-gray-200">{{ $t->email }}</td>
            <td class="text-gray-700 dark:text-gray-200">{{ $t->rut }}</td>
            <td class="text-gray-700 dark:text-gray-200">{{ $t->telefono ?: '—' }}</td>
            <td class="text-sm text-gray-600 dark:text-gray-300 max-w-xs truncate" title="{{ $t->detalle }}">
              {{ \Illuminate\Support\Str::limit($t->detalle, 80) }}
            </td>
            <td class="text-gray-700 dark:text-gray-200">{{ $t->created_at->format('d-m-Y H:i') }}</td>
            <td>
              <span
                class="estado-badge inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold {{ $badgeClass }} {{ $isPend ? 'cursor-pointer' : '' }}"
                data-id="{{ $t->id }}"
                data-estado="{{ $t->estado }}"
                @if($isPend) data-url="{{ route('admin.tickets.resolve', $t) }}" @endif
                role="button" tabindex="{{ $isPend ? '0' : '-1' }}"
                aria-label="Estado: {{ $t->estado }}. {{ $isPend ? 'Click para resolver' : '' }}"
              >
                ● {{ ucfirst($t->estado) }}
              </span>
            </td>
            <td class="text-right">
              <div class="inline-flex items-center gap-2">
                {{-- Ver detalle (ícono ojo) --}}
                <a href="{{ route('admin.tickets.show', $t) }}" class="btn-action" title="Ver detalle">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/>
                    <circle cx="12" cy="12" r="3"/>
                  </svg>
                  Ver
                </a>
              </div>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>

  {{-- CSRF para fetch si tu layout no lo incluye --}}
  <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@push('scripts')
  {{-- DataTables (igual que en Noticias) --}}
  <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/2.1.8/js/dataTables.tailwindcss.min.js"></script>

@endpush
