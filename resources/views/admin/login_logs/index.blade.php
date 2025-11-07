@extends('layouts.admin')
@section('title', 'Login Logs — Admin')

@section('admin')
    <div class="px-6 py-6">
        {{-- Top actions --}}
        <div class="flex items-center justify-between mb-4">
            <x-admin.nav-actions
                backHref="{{ route('admin.dashboard') }}"
                logoutRoute="admin.logout"
                variant="inline"
            />
        </div>

        {{-- ===== Filtros compactos + botones rápidos ===== --}}
        <form id="filterForm" method="GET" class="bg-white dark:bg-gray-900 rounded-2xl shadow p-4 mb-5">
            <div class="grid grid-cols-1 sm:grid-cols-5 gap-3 items-end">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Desde</label>
                    <input
                        type="date" name="from" value="{{ $dateFrom }}"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-violet-500"
                    >
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Hasta</label>
                    <input
                        type="date" name="to" value="{{ $dateTo }}"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-violet-500"
                    >
                </div>
                <div class="sm:col-span-1">
                    <button
                        class="w-full rounded-lg bg-violet-700 text-white font-semibold px-3 py-2 text-xs hover:bg-violet-600"
                    >Filtrar</button>
                </div>
            </div>

            {{-- Botones rápidos --}}
            <div class="mt-3 flex flex-wrap items-center gap-2">
                <span class="text-xs text-gray-500">Rango rápido:</span>

                <button type="button" data-range="today"
                    class="quick-range inline-flex items-center gap-1 rounded-full px-3 py-1.5 text-xs font-semibold
                           bg-white border border-gray-300 hover:bg-violet-50 text-gray-700
                           dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700">
                    Hoy
                </button>

                <button type="button" data-range="yesterday"
                    class="quick-range inline-flex items-center gap-1 rounded-full px-3 py-1.5 text-xs font-semibold
                           bg-white border border-gray-300 hover:bg-violet-50 text-gray-700
                           dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700">
                    Ayer
                </button>

                <button type="button" data-range="3d"
                    class="quick-range inline-flex items-center gap-1 rounded-full px-3 py-1.5 text-xs font-semibold
                           bg-white border border-gray-300 hover:bg-violet-50 text-gray-700
                           dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700">
                    3 días
                </button>

                <button type="button" data-range="5d"
                    class="quick-range inline-flex items-center gap-1 rounded-full px-3 py-1.5 text-xs font-semibold
                           bg-white border border-gray-300 hover:bg-violet-50 text-gray-700
                           dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700">
                    5 días
                </button>

                <button type="button" data-range="all"
                    class="quick-range inline-flex items-center gap-1 rounded-full px-3 py-1.5 text-xs font-semibold
                           bg-white border border-gray-300 hover:bg-violet-50 text-gray-700
                           dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700">
                    Todos
                </button>
            </div>
            <p class="mt-2 text-[11px] text-gray-500">Por defecto se muestra el día de hoy.</p>
        </form>

        {{-- ===== Layout principal: Tabla (2/3) + Chart (1/3) ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- 🟣 TABLA — 2/3 --}}
            <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-2xl shadow p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-violet-900 dark:text-violet-100">Conexiones</h3>
                    <span class="text-xs text-gray-500">Mostrando {{ $logs->count() }} de {{ $logs->total() }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs sm:text-sm">
                        <thead>
                            <tr class="text-left text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                                <th class="py-2 pr-3">Usuario</th>
                                <th class="py-2 pr-3">IP</th>
                                <th class="py-2 pr-3">User-Agent</th>
                                <th class="py-2 pr-3 whitespace-nowrap">Fecha conexión</th>
                                <th class="py-2 pr-3 whitespace-nowrap">Fecha cierre</th>
                                <th class="py-2 pr-3 whitespace-nowrap">Duración (s)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($logs as $row)
                                @php
                                    $uName = $row->user?->name;
                                    // Buscamos el RUT en varios posibles lugares:
                                    $rut   = $row->user?->rut
                                    
                                          ?? null;

                                    $loggedOutAt = $row->logged_out_at
                                        ? \Illuminate\Support\Carbon::parse($row->logged_out_at)
                                        : null;

                                    // Si no viene duration_seconds, calculamos cuando existe logged_out_at
                                    $durationSec = $row->duration_seconds
                                        ?? ($loggedOutAt
                                                ? \Illuminate\Support\Carbon::parse($row->logged_in_at)->diffInSeconds($loggedOutAt)
                                                : null);
                                @endphp

                                <tr>
                                    {{-- Usuario (nombre + RUT siempre visible) --}}
                                    <td class="py-2 pr-3">
                                        <div class="font-medium">
                                            {{ $uName ?? '—' }}
                                        </div>
                                        <div class="text-[11px] text-gray-500">
                                            RUT: {{ $row->user?->rut ?: '—' }}
                                        </div>
                                    </td>

                                    {{-- IP --}}
                                    <td class="py-2 pr-3 font-mono text-[11px] sm:text-xs">
                                        {{ $row->ip_address }}
                                    </td>

                                    {{-- User Agent --}}
                                    <td class="py-2 pr-3 truncate max-w-[28rem]" title="{{ $row->user_agent }}">
                                        <span class="text-[11px] sm:text-xs">{{ $row->user_agent }}</span>
                                    </td>

                                    {{-- Fecha conexión --}}
                                    <td class="py-2 pr-3 whitespace-nowrap">
                                        {{ \Illuminate\Support\Carbon::parse($row->logged_in_at)->format('d-m-Y H:i') }}
                                    </td>

                                    {{-- Fecha cierre --}}
                                    <td class="py-2 pr-3 whitespace-nowrap">
                                        {{ $loggedOutAt ? $loggedOutAt->format('d-m-Y H:i') : '—' }}
                                    </td>

                                    {{-- Duración en segundos --}}
                                    <td class="py-2 pr-3 whitespace-nowrap">
                                        {{ $durationSec !== null ? $durationSec : '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $logs->links() }}
                </div>
            </div>

            {{-- 🟢 CHART + KPIs — 1/3 --}}
            <div class="lg:col-span-1 bg-white dark:bg-gray-900 rounded-2xl shadow p-4">
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="rounded-xl border border-violet-200/60 dark:border-gray-700 p-3">
                        <div class="text-[11px] text-gray-500">Total Logins</div>
                        <div class="text-xl font-bold text-violet-900 dark:text-violet-200">{{ $totalLogins }}</div>
                    </div>
                    <div class="rounded-xl border border-violet-200/60 dark:border-gray-700 p-3">
                        <div class="text-[11px] text-gray-500">Usuarios Únicos</div>
                        <div class="text-xl font-bold text-violet-900 dark:text-violet-200">{{ $uniqueUsers }}</div>
                    </div>
                </div>

                <h3 class="text-xs font-semibold text-violet-900 dark:text-violet-100 mb-2">
                    {{ $chartTitle }}
                </h3>
                <canvas id="loginsChart" height="170"></canvas>
            </div>

        </div>
    </div>

    {{-- ===== JS: Rango rápido ===== --}}
    <script>
        (function () {
            const form = document.getElementById('filterForm');
            const from = form.querySelector('input[name="from"]');
            const to   = form.querySelector('input[name="to"]');

            function fmt(d) { // YYYY-MM-DD local
                const z = n => String(n).padStart(2,'0');
                return d.getFullYear()+'-'+z(d.getMonth()+1)+'-'+z(d.getDate());
            }
            function setRange(daysAgoStart, daysAgoEnd){
                const now   = new Date();
                const end   = new Date(now.getFullYear(), now.getMonth(), now.getDate() - (daysAgoEnd||0)); // hoy/ayer
                const start = new Date(now.getFullYear(), now.getMonth(), now.getDate() - (daysAgoStart||0));
                from.value = fmt(start);
                to.value   = fmt(end);
            }

            document.querySelectorAll('.quick-range').forEach(btn=>{
                btn.addEventListener('click', ()=>{
                    const r = btn.dataset.range;
                    switch(r){
                        case 'today':     setRange(0,0); break; // hoy
                        case 'yesterday': setRange(1,1); break; // ayer
                        case '3d':        setRange(2,0); break; // últimos 3 días (incluye hoy)
                        case '5d':        setRange(4,0); break; // últimos 5 días (incluye hoy)
                        case 'all':       from.value=''; to.value=''; break; // todos
                    }
                    form.submit();
                });
            });
        })();
    </script>

    {{-- ===== Chart.js ===== --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (() => {
            const ctx = document.getElementById('loginsChart')?.getContext('2d');
            if (!ctx) return;

            const labels = @json($chartLabels);
            const data   = @json($chartData);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Logins',
                        data
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: { mode: 'index', intersect: false }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                        y: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 } } }
                    }
                }
            });
        })();
    </script>
@endsection
