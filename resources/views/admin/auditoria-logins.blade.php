{{-- resources/views/admin/auditoria-logins.blade.php --}}
@extends('layouts.admin')
@section('title','Auditoría de Accesos — Admin')

@section('admin')
@php
  // Defaults de seguridad por si algo no viene del controlador
  $dateFrom      = $dateFrom      ?? now()->format('Y-m-d');
  $dateTo        = $dateTo        ?? now()->format('Y-m-d');
  $chartTitle    = $chartTitle    ?? 'Actividad de accesos';
  $chartLabels   = $chartLabels   ?? [];
  $chartData     = $chartData     ?? [];
  $totalLogins   = $totalLogins   ?? 0;
  $uniqueUsers   = $uniqueUsers   ?? 0;
  $totalFailures = $totalFailures ?? 0;
  $totalLockouts = $totalLockouts ?? 0;
@endphp

<div class="px-6 py-6">
  {{-- Acciones / breadcrumb --}}
  <div class="flex items-center justify-between mb-4">
    <x-admin.nav-actions
      backHref="{{ route('admin.dashboard') }}"
      logoutRoute="admin.logout"
      variant="inline"
    />
  </div>

  {{-- ================= FILTROS ================= --}}
  <form method="GET" class="bg-white dark:bg-gray-900 rounded-2xl shadow p-4 mb-4">
    <div class="flex flex-wrap items-end gap-3">
      <div>
        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Desde</label>
        <input type="date" name="from" value="{{ $dateFrom }}"
               class="w-40 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-1.5 text-sm">
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Hasta</label>
        <input type="date" name="to" value="{{ $dateTo }}"
               class="w-40 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-1.5 text-sm">
      </div>

      {{-- Botones rápidos --}}
      <div class="flex items-center gap-2">
        <button type="button" data-range="today"
                class="px-2.5 py-1.5 rounded-lg text-xs font-semibold border border-purple-300 text-purple-800 dark:text-purple-200 hover:bg-purple-50 dark:hover:bg-purple-900/30">
          Hoy
        </button>
        <button type="button" data-range="yesterday"
                class="px-2.5 py-1.5 rounded-lg text-xs font-semibold border border-purple-300 text-purple-800 dark:text-purple-200 hover:bg-purple-50 dark:hover:bg-purple-900/30">
          Ayer
        </button>
        <button type="button" data-range="3d"
                class="px-2.5 py-1.5 rounded-lg text-xs font-semibold border border-purple-300 text-purple-800 dark:text-purple-200 hover:bg-purple-50 dark:hover:bg-purple-900/30">
          3 días
        </button>
        <button type="button" data-range="5d"
                class="px-2.5 py-1.5 rounded-lg text-xs font-semibold border border-purple-300 text-purple-800 dark:text-purple-200 hover:bg-purple-50 dark:hover:bg-purple-900/30">
          5 días
        </button>
        <button type="button" data-range="all"
                class="px-2.5 py-1.5 rounded-lg text-xs font-semibold border border-purple-300 text-purple-800 dark:text-purple-200 hover:bg-purple-50 dark:hover:bg-purple-900/30">
          Todos
        </button>
      </div>

      <div class="ml-auto">
        <button class="rounded-lg bg-purple-700 text-white font-semibold px-4 py-2 text-sm hover:bg-purple-600">
          Filtrar
        </button>
      </div>
    </div>
    <p class="mt-2 text-[11px] text-gray-500">Tip: usa los botones rápidos para ajustar las fechas en 1 clic.</p>
  </form>

  {{-- ================= LAYOUT 2/3 + 1/3 ================= --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    {{-- ======= IZQUIERDA 2/3: TABLAS ======= --}}
    <div class="lg:col-span-2 space-y-4">
      <div class="bg-white dark:bg-gray-900 rounded-2xl shadow">
        {{-- Tabs encabezado --}}
        <div class="flex items-center justify-between p-3 border-b border-gray-200 dark:border-gray-700">
          <div class="flex items-center gap-1">
            <button type="button" data-tab="ok"
              class="tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-purple-100 text-purple-900 dark:bg-purple-900/40 dark:text-purple-100">
              ✅ Exitosos
            </button>
            <button type="button" data-tab="fail"
              class="tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-gray-100 dark:hover:bg-gray-800">
              ❌ Fallidos
            </button>
            <button type="button" data-tab="lock"
              class="tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-gray-100 dark:hover:bg-gray-800">
              🔒 Bloqueos
            </button>
          </div>
          <div class="text-[11px] text-gray-500 pr-2">
            Rango: {{ \Illuminate\Support\Carbon::parse($dateFrom)->format('d-m-Y') }}
            – {{ \Illuminate\Support\Carbon::parse($dateTo)->format('d-m-Y') }}
          </div>
        </div>

        {{-- Tab: Exitosos --}}
        <div id="tab-ok" class="tab-panel p-4">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-sm font-semibold text-purple-900 dark:text-purple-100">Accesos exitosos</h3>
            @if(isset($successLogs))
              <span class="text-xs text-gray-500">Mostrando {{ $successLogs->count() }} de {{ $successLogs->total() }}</span>
            @endif
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead>
                <tr class="text-left text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                  <th class="py-2 pr-3">Nombre</th>
                  <th class="py-2 pr-3">IP</th>
                  <th class="py-2 pr-3">User-Agent</th>
                  <th class="py-2 pr-3">Fecha conexión</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse(($successLogs ?? []) as $row)
                  <tr>
                    <td class="py-2 pr-3">{{ $row->user?->name ?? $row->user?->email ?? '—' }}</td>
                    <td class="py-2 pr-3 font-mono">{{ $row->ip_address }}</td>
                    <td class="py-2 pr-3 truncate max-w-[28rem]" title="{{ $row->user_agent }}">{{ $row->user_agent }}</td>
                    <td class="py-2 pr-3 whitespace-nowrap">
                      {{ \Illuminate\Support\Carbon::parse($row->logged_in_at)->format('d-m-Y H:i') }}
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="4" class="py-4 text-center text-gray-500">Sin datos</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          @if(isset($successLogs)) <div class="mt-3">{{ $successLogs->withQueryString()->links() }}</div> @endif
        </div>

        {{-- Tab: Fallidos --}}
        <div id="tab-fail" class="tab-panel p-4 hidden">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-sm font-semibold text-red-700 dark:text-red-300">Intentos fallidos</h3>
            @if(isset($failureLogs))
              <span class="text-xs text-gray-500">Mostrando {{ $failureLogs->count() }} de {{ $failureLogs->total() }}</span>
            @endif
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead>
                <tr class="text-left text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                  <th class="py-2 pr-3">Intentado por</th>
                  <th class="py-2 pr-3">IP</th>
                  <th class="py-2 pr-3">User-Agent</th>
                  <th class="py-2 pr-3">Fecha</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse(($failureLogs ?? []) as $row)
                  <tr class="align-top">
                    <td class="py-2 pr-3">
                      {{-- Puede venir email/username usado en el intento --}}
                      {{ $row->attempted ?? '—' }}
                      @if(!empty($row->extra))
                        <div class="text-[11px] text-gray-500 mt-0.5 truncate max-w-[28rem]" title="{{ $row->extra }}">
                          {{ $row->extra }}
                        </div>
                      @endif
                    </td>
                    <td class="py-2 pr-3 font-mono">{{ $row->ip_address }}</td>
                    <td class="py-2 pr-3 truncate max-w-[28rem]" title="{{ $row->user_agent }}">{{ $row->user_agent }}</td>
                    <td class="py-2 pr-3 whitespace-nowrap">
                      {{ \Illuminate\Support\Carbon::parse($row->failed_at ?? $row->created_at)->format('d-m-Y H:i') }}
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="4" class="py-4 text-center text-gray-500">Sin datos</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          @if(isset($failureLogs)) <div class="mt-3">{{ $failureLogs->withQueryString()->links() }}</div> @endif
        </div>

        {{-- Tab: Bloqueos --}}
        <div id="tab-lock" class="tab-panel p-4 hidden">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-sm font-semibold text-amber-700 dark:text-amber-300">Usuarios bloqueados</h3>
            @if(isset($lockoutLogs))
              <span class="text-xs text-gray-500">Mostrando {{ $lockoutLogs->count() }} de {{ $lockoutLogs->total() }}</span>
            @endif
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead>
                <tr class="text-left text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                  <th class="py-2 pr-3">Usuario</th>
                  <th class="py-2 pr-3">IP</th>
                  <th class="py-2 pr-3">Motivo</th>
                  <th class="py-2 pr-3">Fecha</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse(($lockoutLogs ?? []) as $row)
                  <tr class="align-top">
                    <td class="py-2 pr-3">{{ $row->user?->name ?? $row->user?->email ?? '—' }}</td>
                    <td class="py-2 pr-3 font-mono">{{ $row->ip_address }}</td>
                    <td class="py-2 pr-3">
                      {{ $row->reason ?? 'Intentos excedidos' }}
                      @if(!empty($row->extra))
                        <div class="text-[11px] text-gray-500 mt-0.5 truncate max-w-[28rem]" title="{{ $row->extra }}">
                          {{ $row->extra }}
                        </div>
                      @endif
                    </td>
                    <td class="py-2 pr-3 whitespace-nowrap">
                      {{ \Illuminate\Support\Carbon::parse($row->locked_at ?? $row->created_at)->format('d-m-Y H:i') }}
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="4" class="py-4 text-center text-gray-500">Sin datos</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          @if(isset($lockoutLogs)) <div class="mt-3">{{ $lockoutLogs->withQueryString()->links() }}</div> @endif
        </div>
      </div>
    </div>

    {{-- ======= DERECHA 1/3: KPIs + CHART ======= --}}
    <div class="space-y-4">
      <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-4">
        <div class="grid grid-cols-2 gap-3">
          <div class="rounded-xl border border-purple-200/40 dark:border-gray-700 p-3">
            <div class="text-[11px] text-gray-500">Total Logins</div>
            <div class="text-2xl font-bold text-purple-900 dark:text-purple-200">{{ $totalLogins }}</div>
          </div>
          <div class="rounded-xl border border-purple-200/40 dark:border-gray-700 p-3">
            <div class="text-[11px] text-gray-500">Usuarios Únicos</div>
            <div class="text-2xl font-bold text-purple-900 dark:text-purple-200">{{ $uniqueUsers }}</div>
          </div>
          <div class="rounded-xl border border-rose-200/60 dark:border-rose-900/50 p-3">
            <div class="text-[11px] text-rose-700 dark:text-rose-300">Intentos fallidos</div>
            <div class="text-2xl font-bold text-rose-700 dark:text-rose-300">{{ $totalFailures }}</div>
          </div>
          <div class="rounded-xl border border-amber-200/60 dark:border-amber-900/50 p-3">
            <div class="text-[11px] text-amber-700 dark:text-amber-300">Bloqueos</div>
            <div class="text-2xl font-bold text-amber-700 dark:text-amber-300">{{ $totalLockouts }}</div>
          </div>
        </div>

        <h3 class="mt-4 text-xs font-semibold text-purple-900 dark:text-purple-100">{{ $chartTitle }}</h3>
        <canvas id="loginsChart" height="150" class="mt-2"></canvas>
      </div>

      {{-- (Opcional) Hotspots IP / Top usuarios: puedes llenarlo desde el controlador --}}
      @if(!empty($topUsers ?? []))
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-4">
          <h4 class="text-xs font-semibold text-purple-900 dark:text-purple-100 mb-2">Top usuarios (logins)</h4>
          <ul class="text-sm space-y-1">
            @foreach($topUsers as $u)
              <li class="flex justify-between">
                <span>{{ $u['label'] }}</span>
                <span class="font-semibold">{{ $u['count'] }}</span>
              </li>
            @endforeach
          </ul>
        </div>
      @endif
    </div>
  </div>
</div>

{{-- ============== JS RANGOS RÁPIDOS + TABS + CHART ============== --}}
<script>
  // Botones rápidos de rango
  (function() {
    const $from = document.querySelector('input[name="from"]');
    const $to   = document.querySelector('input[name="to"]');

    function fmt(d) { const z=n=>String(n).padStart(2,'0'); return d.getFullYear() + '-' + z(d.getMonth()+1) + '-' + z(d.getDate()); }
    function setRange(days) {
      const today = new Date();
      const to    = new Date(today);
      const from  = new Date(today);
      from.setDate(today.getDate() - (days-1));
      $from.value = fmt(from); $to.value = fmt(to);
    }

    document.querySelectorAll('button[data-range]').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        const r = btn.getAttribute('data-range');
        if (r==='today') setRange(1);
        else if (r==='yesterday') { const t=new Date(); t.setDate(t.getDate()-1); $from.value=$to.value=fmt(t); }
        else if (r==='3d') setRange(3);
        else if (r==='5d') setRange(5);
        else if (r==='all') { $from.value=''; $to.value=''; }
      });
    });
  })();

  // Tabs
  (function(){
    const btns = document.querySelectorAll('.tab-btn');
    const panels = {
      ok:   document.getElementById('tab-ok'),
      fail: document.getElementById('tab-fail'),
      lock: document.getElementById('tab-lock')
    };
    function activate(key){
      Object.values(panels).forEach(p=>p.classList.add('hidden'));
      panels[key]?.classList.remove('hidden');
      btns.forEach(b=>{
        const on = b.getAttribute('data-tab')===key;
        b.classList.toggle('bg-purple-100', on);
        b.classList.toggle('text-purple-900', on);
        b.classList.toggle('dark:bg-purple-900/40', on);
        b.classList.toggle('dark:text-purple-100', on);
      });
    }
    btns.forEach(b=>b.addEventListener('click', ()=>activate(b.getAttribute('data-tab'))));
    // pestaña por defecto:
    activate('ok');
  })();
</script>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(() => {
  const el = document.getElementById('loginsChart');
  if (!el) return;

  const labels = @json($chartLabels);
  const data   = @json($chartData);

  new Chart(el.getContext('2d'), {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Logins',
        data,
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: { mode: 'index', intersect: false }
      },
      scales: {
        x: { grid: { display: false } },
        y: { beginAtZero: true, ticks: { precision: 0 } }
      }
    }
  });
})();
</script>
@endsection
