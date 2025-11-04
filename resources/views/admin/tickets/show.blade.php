@extends('layouts.admin')

@section('title', 'Detalle Ticket #'.$ticket->id)

@section('admin')
  <div class="px-6 py-6">
    {{-- Botones superiores (idénticos a Noticias) --}}
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-3">
  <x-admin.nav-actions
    backHref="{{ route('admin.dashboard') }}"
    logoutRoute="admin.logout"
    variant="inline"   {{-- o "sticky" si la tabla es larga --}}
  />
      </div>
    </div>

    {{-- Card detalle --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow border border-purple-100 dark:border-gray-700 p-6">
      <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold text-purple-800 dark:text-purple-300">
          🎫 Ticket #{{ $ticket->id }}
        </h1>
        @php
          $badge = match($ticket->estado) {
            'pendiente' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'resuelto'  => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            default     => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300',
          };
        @endphp
        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold {{ $badge }}">
          ● {{ ucfirst($ticket->estado) }}
        </span>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <p class="text-sm text-gray-500 dark:text-gray-400 uppercase font-semibold">Correo</p>
          <p class="text-gray-800 dark:text-gray-100 font-medium break-all">{{ $ticket->email }}</p>
        </div>

        <div>
          <p class="text-sm text-gray-500 dark:text-gray-400 uppercase font-semibold">RUT</p>
          <p class="text-gray-800 dark:text-gray-100 font-medium">{{ $ticket->rut }}</p>
        </div>

        <div>
          <p class="text-sm text-gray-500 dark:text-gray-400 uppercase font-semibold">Teléfono</p>
          <p class="text-gray-800 dark:text-gray-100 font-medium">{{ $ticket->telefono ?: '—' }}</p>
        </div>

        <div>
          <p class="text-sm text-gray-500 dark:text-gray-400 uppercase font-semibold">Fecha</p>
          <p class="text-gray-800 dark:text-gray-100 font-medium">
            {{ $ticket->created_at->format('d-m-Y H:i') }}
          </p>
        </div>

        <div class="md:col-span-2">
          <p class="text-sm text-gray-500 dark:text-gray-400 uppercase font-semibold mb-1">Detalle</p>
          <div class="rounded-xl border border-purple-200/60 dark:border-gray-700 p-4 bg-purple-50/40 dark:bg-gray-800/50 text-gray-800 dark:text-gray-100 leading-relaxed whitespace-pre-line">
            {{ $ticket->detalle }}
          </div>
        </div>

        <div class="md:col-span-2">
          <p class="text-sm text-gray-500 dark:text-gray-400 uppercase font-semibold mb-1">Adjunto</p>
          @if($ticket->archivo)
            <a href="{{ Storage::disk('public')->url($ticket->archivo) }}"
               class="inline-flex items-center gap-2 btn-action"
               target="_blank" rel="noopener">
              📎 Descargar adjunto
            </a>
          @else
            <span class="text-gray-500 dark:text-gray-400 text-sm">Sin adjuntos</span>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection
