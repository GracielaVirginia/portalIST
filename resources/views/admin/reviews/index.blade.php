@extends('layouts.admin')

@section('title', 'Opiniones — Admin')

@section('admin')
  <div class="px-6 py-6 relative" x-data="{openResumen:false}">
    {{-- Botones superiores --}}
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center gap-3">
  <x-admin.nav-actions
    backHref="{{ route('admin.dashboard') }}"
    logoutRoute="admin.logout"
    variant="inline"   {{-- o "sticky" si la tabla es larga --}}
  />
      </div>
      </div>

      {{-- Botón flotante (arriba derecha) para abrir el resumen --}}
  <div class="flex justify-end mb-3">
      <button type="button" @click="openResumen = true"
              class="hidden md:inline-flex items-center gap-2 bg-white dark:bg-gray-900 border border-purple-200 dark:border-gray-700
                     text-purple-900 dark:text-purple-200 px-3 py-2 rounded-full shadow hover:shadow-md">
        ⭐ Resumen
      </button>
    </div>

    {{-- Modal Resumen de ratings --}}
    <div x-show="openResumen" x-cloak>
      {{-- backdrop --}}
      <div class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40" @click="openResumen=false"></div>
      {{-- panel --}}
      <div class="fixed right-6 top-6 z-50 w-[340px]">
        <div class="rounded-2xl bg-white dark:bg-gray-900 border border-purple-100 dark:border-gray-700 shadow-xl overflow-hidden">
          <div class="flex items-center justify-between px-4 py-3 border-b border-purple-100 dark:border-gray-700">
            <h3 class="text-sm font-bold text-purple-900 dark:text-purple-200">Resumen de opiniones</h3>
            <button class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                    @click="openResumen=false">✖</button>
          </div>

          <div class="p-4 space-y-4">
            {{-- Promedio --}}
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-600 dark:text-gray-300">Promedio</div>
              <div class="flex items-center gap-2">
                <div class="text-yellow-400">
                  @for($i=1;$i<=5;$i++)
                    <span class="{{ $i <= round($avg) ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}">★</span>
                  @endfor
                </div>
                <div class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ number_format($avg,1) }}/5</div>
              </div>
            </div>

            <hr class="border-purple-100 dark:border-gray-700">

            {{-- Barras 5→1 --}}
            @php
              $t = max(1, $total);
            @endphp
            @for($s=5; $s>=1; $s--)
              @php
                $c = $counts[$s] ?? 0;
                $pct = round(($c / $t) * 100);
              @endphp
              <div class="space-y-1">
                <div class="flex items-center justify-between text-xs">
                  <div class="flex items-center gap-1">
                    <span class="text-gray-600 dark:text-gray-300">{{ $s }} estrellas</span>
                  </div>
                  <div class="text-gray-500 dark:text-gray-400">{{ $c }} ({{ $pct }}%)</div>
                </div>
                <div class="w-full h-2 rounded-full bg-purple-100 dark:bg-gray-800 overflow-hidden">
                  <div class="h-2 bg-purple-600 rounded-full" style="width: {{ $pct }}%"></div>
                </div>
              </div>
            @endfor

            <div class="text-[11px] text-gray-500 dark:text-gray-400 text-right">Total: {{ $total }}</div>
          </div>
        </div>
      </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-4">
      <table id="tablaReviews" class="display w-full">
        <thead>
          <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Rating</th>
            <th>Comentario</th>
            <th>Fecha</th>
            {{-- <th>Acciones</th> --}}
          </tr>
        </thead>
        <tbody>
        @foreach ($reviews as $r)
          <tr>
            <td class="font-semibold text-purple-900 dark:text-purple-200">{{ $r->id }}</td>
            <td class="text-gray-700 dark:text-gray-200">
              @if($r->anonimo || !$r->user)
                🕶️ Anónimo
              @else
                <div class="flex flex-col">
                  <span class="font-semibold">{{ $r->user->name }}</span>
                  <span class="text-xs text-gray-500">{{ $r->user->email }}</span>
                </div>
              @endif
            </td>
            <td class="text-yellow-500">
              @for($i=1; $i<=5; $i++)
                <span class="{{ $i <= $r->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}">★</span>
              @endfor
              <span class="ml-1 text-xs text-gray-500 dark:text-gray-400">({{ $r->rating }})</span>
            </td>
            <td class="text-sm text-gray-700 dark:text-gray-300 max-w-md truncate" title="{{ $r->comment }}">
              {{ \Illuminate\Support\Str::limit($r->comment, 80) ?: '—' }}
            </td>
            <td class="text-gray-700 dark:text-gray-200">
              {{ $r->created_at->format('d-m-Y H:i') }}
            </td>
            {{-- <td class="text-right">
              <a href="{{ route('admin.reviews.show', $r) }}" class="btn-action" title="Ver detalle">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
                Ver
              </a>
            </td> --}}
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endsection

@push('scripts')
  {{-- DataTables (CDN) --}}
  <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/2.1.8/js/dataTables.tailwindcss.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      new DataTable('#tablaReviews', {
        pageLength: 10,
        order: [[0, 'desc']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' }
      });
    });
  </script>

  {{-- Alpine (si tu layout aún no lo carga) --}}
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush
