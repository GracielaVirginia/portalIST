@extends('layouts.app', ['topbar' => true, 'navbar' => true])

@section('title', 'Tickets de Soporte — Admin')

@section('content')
  <div class="px-6 py-6">
    {{-- Botones superiores (mismos estilos que Noticias) --}}
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center gap-3">
        <a href="{{ route('admin.dashboard') }}"
           class="inline-flex items-center gap-2 bg-purple-900 text-white text-sm font-semibold px-4 py-2 rounded-full shadow-sm hover:bg-purple-800 hover:shadow-md transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
               stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
          </svg>
          Volver al dashboard
        </a>

        {{-- Botón cerrar sesión --}}
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit"
                  class="inline-flex items-center gap-2 bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-full shadow-sm hover:bg-red-500 hover:shadow-md transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
            </svg>
            Cerrar sesión
          </button>
        </form>
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
