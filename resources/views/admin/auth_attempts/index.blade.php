@extends('layouts.admin')
@section('title', 'Reporte de Validaciones — Admin')

@section('admin')
<div class="px-6 py-6">

    {{-- === Top actions === --}}
    <div class="flex items-center justify-between mb-  overflow-visible">
        {{-- Lado izquierdo: nav-actions + icono "i" juntos --}}
        <div class="flex items-center gap-3  overflow-visible">
            <x-admin.nav-actions
                backHref="{{ route('admin.dashboard') }}"
                logoutRoute="admin.logout"
                variant="inline"
            />


        </div>
    </div>

    {{-- === Filtros === --}}
    <form id="filterForm" method="GET" class="bg-white dark:bg-gray-900 rounded-2xl shadow p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-5 gap-3 items-end">
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Desde</label>
                <input type="date" name="from" value="{{ $dateFrom }}"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-violet-500">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Hasta</label>
                <input type="date" name="to" value="{{ $dateTo }}"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-violet-500">
            </div>
            <div class="sm:col-span-1">
                <button class="w-full rounded-lg bg-violet-700 text-white font-semibold px-3 py-2 text-xs hover:bg-violet-600">
                    Filtrar
                </button>
            </div>
        </div>

        {{-- Rango rápido --}}
        <div class="mt-3 flex flex-wrap items-center gap-2">
            <span class="text-xs text-gray-500">Rango rápido:</span>
            @foreach (['today' => 'Hoy', 'yesterday' => 'Ayer', '3d' => '3 días', '5d' => '5 días', 'all' => 'Todos'] as $range => $label)
                <button type="button" data-range="{{ $range }}"
                    class="quick-range inline-flex items-center gap-1 rounded-full px-3 py-1.5 text-xs font-semibold
                           bg-white border border-gray-300 hover:bg-violet-50 text-gray-700
                           dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700">
                    {{ $label }}
                </button>
            @endforeach
        </div>
        <p class="mt-2 text-[11px] text-gray-500">Por defecto se muestra el día de hoy.</p>
    </form>

    {{-- === KPIs === --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        {{-- No pudo loguearse (rojo) --}}
        <div class="rounded-2xl border border-red-200 dark:border-red-900 bg-red-50/60 dark:bg-red-950/30 p-4">
            <div class="text-xs font-semibold text-red-700 dark:text-red-400">No pudo loguearse</div>
            <div class="mt-1 text-2xl font-bold text-red-800 dark:text-red-300">{{ $kpiNoLogin }}</div>
            <div class="text-[11px] text-red-700/80 dark:text-red-400/70">Contraseña inválida o bloqueado en login.</div>
        </div>
        {{-- Login sin validar (amarillo) --}}
        <div class="rounded-2xl border border-yellow-200 dark:border-yellow-900 bg-yellow-50/60 dark:bg-yellow-950/30 p-4">
            <div class="text-xs font-semibold text-yellow-700 dark:text-yellow-400">Login sin validar</div>
            <div class="mt-1 text-2xl font-bold text-yellow-800 dark:text-yellow-300">{{ $kpiNoValida }}</div>
            <div class="text-[11px] text-yellow-700/80 dark:text-yellow-400/70">Falló validación post-login.</div>
        </div>
        {{-- Éxito (verde) --}}
        <div class="rounded-2xl border border-green-200 dark:border-green-900 bg-green-50/60 dark:bg-green-950/30 p-4">
            <div class="text-xs font-semibold text-green-700 dark:text-green-400">Llegó al home</div>
            <div class="mt-1 text-2xl font-bold text-green-800 dark:text-green-300">{{ $kpiExito }}</div>
            <div class="text-[11px] text-green-700/80 dark:text-green-400/70">Acceso completo a portal.</div>
        </div>
    </div>

    {{-- === LAYOUT PRINCIPAL === --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- 🟣 TABLA --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-2xl shadow p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-violet-900 dark:text-violet-100">
                    Estado de login y validación por usuario
                </h3>
                            {{-- Icono de información con tooltip (manual) --}}
            <div class="relative group inline-flex items-center">
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full
                             bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-200
                             ring-1 ring-violet-300 dark:ring-violet-700 cursor-help select-none text-sm font-bold">
                    i
                </span>

                {{-- tooltip --}}
            <div class="absolute top-full mt-2 left-1/2 -translate-x-1/2
                        w-80 sm:w-[28rem] max-w-[calc(100vw-2rem)]
                        bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700
                        rounded-xl shadow-lg p-3
                        opacity-0 scale-95 -translate-y-1
                        pointer-events-none group-hover:opacity-100 group-hover:scale-100 group-hover:translate-y-0 group-hover:pointer-events-auto
                        origin-top transition ease-out duration-150
                        z-[9999]">
                    <h4 class="text-xs font-semibold text-violet-900 dark:text-violet-100 mb-1">
                        ¿Qué muestra este reporte?
                    </h4>
                    <ul class="text-[11px] leading-5 text-gray-600 dark:text-gray-300 list-disc pl-4">
                        <li><strong>No pudo loguearse:</strong> errores de login (contraseña inválida o bloqueos).</li>
                        <li><strong>Login sin validar:</strong> inició sesión pero falló la validación posterior.</li>
                        <li><strong>Éxito (Llegó al home):</strong> ingresó correctamente al portal.</li>
                    </ul>
                    <p class="mt-2 text-[11px] text-gray-500 dark:text-gray-400">
                        Consejo: si hay muchos en amarillo, revisa la UX de validación; si hay muchos en rojo,
                        refuerza recuperación de contraseña y mensajes de error.
                    </p>
                </div>
            </div>
                <span class="text-xs text-gray-500">
                    Mostrando {{ $rows->count() }} registros
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-xs sm:text-sm">
                    <thead>
                        <tr class="text-left text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                            <th class="py-2 pr-3">Documento</th>
                            <th class="py-2 pr-3">IP</th>
                            <th class="py-2 pr-3 text-center">Intentos login</th>
                            <th class="py-2 pr-3 text-center">Intentos validación</th>
                            <th class="py-2 pr-3 text-center">Bloqueado</th>
                            <th class="py-2 pr-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($rows as $r)
                            @php
                                $badge = match($r->status) {
                                    'Éxito'             => 'bg-green-100 text-green-700',
                                    'Login sin validar' => 'bg-yellow-100 text-yellow-700',
                                    'No pudo loguearse' => 'bg-red-100 text-red-700',
                                    default             => 'bg-gray-100 text-gray-700',
                                };
                                $showContacts = in_array($r->status, ['No pudo loguearse', 'Login sin validar']);
                            @endphp
                            <tr>
                                {{-- Documento + iconos contacto --}}
                                <td class="py-2 pr-3 font-mono text-[11px] sm:text-xs">
                                    <div class="flex items-center gap-2">
                                        <span>{{ $r->login_input }}</span>
                                        @if ($showContacts && ($r->telefono || $r->email))
                                            <span class="inline-flex items-center gap-1">
                                                @if ($r->telefono)
                                                    <a href="tel:{{ $r->telefono }}"
                                                       class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-gray-100 dark:bg-gray-800 hover:bg-violet-100 dark:hover:bg-violet-800 transition"
                                                       title="Llamar: {{ $r->telefono }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                             fill="currentColor" class="w-3.5 h-3.5 text-gray-700 dark:text-gray-200">
                                                            <path d="M2.25 6.75c0-1.243 1.007-2.25 2.25-2.25h2.1c.98 0 1.84.643 2.11 1.58l.54 1.88a2.25 2.25 0 01-.57 2.22l-1.13 1.13a16.5 16.5 0 006.87 6.87l1.13-1.13a2.25 2.25 0 012.22-.57l1.88.54c.937.27 1.58 1.13 1.58 2.11v2.1c0 1.243-1.007 2.25-2.25 2.25H18c-8.837 0-16-7.163-16-16v-2.1z"/>
                                                        </svg>
                                                    </a>
                                                @endif
                                                @if ($r->email)
                                                    <a href="mailto:{{ $r->email }}"
                                                       class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-gray-100 dark:bg-gray-800 hover:bg-violet-100 dark:hover:bg-violet-800 transition"
                                                       title="Correo: {{ $r->email }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                             fill="currentColor" class="w-3.5 h-3.5 text-gray-700 dark:text-gray-200">
                                                            <path d="M1.5 6.75A2.25 2.25 0 013.75 4.5h16.5A2.25 2.25 0 0122.5 6.75v10.5A2.25 2.25 0 0120.25 19.5H3.75A2.25 2.25 0 011.5 17.25V6.75zm3.28-.75a.75.75 0 00-.53 1.28l7 6.5a.75.75 0 001.02 0l7-6.5a.75.75 0 10-1.02-1.1L12 12.26 4.78 6.9a.75.75 0 00-.53-.9z"/>
                                                        </svg>
                                                    </a>
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- IP --}}
                                <td class="py-2 pr-3 font-mono text-[11px] sm:text-xs">{{ $r->last_ip ?: '—' }}</td>

                                {{-- Intentos --}}
                                <td class="py-2 pr-3 text-center font-semibold">{{ (int) $r->login_attempts }}</td>
                                <td class="py-2 pr-3 text-center font-semibold">{{ (int) $r->validation_attempts }}</td>

                                {{-- Bloqueado --}}
                                <td class="py-2 pr-3 text-center">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold
                                                 {{ $r->blocked === 'Sí' ? 'bg-red-50 text-red-600' : 'bg-gray-50 text-gray-600' }}">
                                        {{ $r->blocked }}
                                    </span>
                                </td>

                                {{-- Estado --}}
                                <td class="py-2 pr-3 text-center">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $badge }}">
                                        {{ $r->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-xs text-gray-500">
                                    No hay registros en el rango seleccionado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 🟢 CHART --}}
        <div class="lg:col-span-1 bg-white dark:bg-gray-900 rounded-2xl shadow p-4 flex flex-col justify-between">
            <h3 class="text-xs font-semibold text-violet-900 dark:text-violet-100 mb-2">
                Distribución de resultados ({{ $dateFrom }} a {{ $dateTo }})
            </h3>
            <canvas id="loginsChart" height="220"></canvas>
        </div>
    </div>
</div>

{{-- ===== JS: Rango rápido ===== --}}
<script>
(function () {
    const form = document.getElementById('filterForm');
    const from = form.querySelector('input[name="from"]');
    const to   = form.querySelector('input[name="to"]');

    function fmt(d){const z=n=>String(n).padStart(2,'0');return d.getFullYear()+'-'+z(d.getMonth()+1)+'-'+z(d.getDate());}
    function setRange(start, end){
        const now=new Date();
        const endD=new Date(now.getFullYear(),now.getMonth(),now.getDate()-(end||0));
        const startD=new Date(now.getFullYear(),now.getMonth(),now.getDate()-(start||0));
        from.value=fmt(startD);to.value=fmt(endD);
    }

    document.querySelectorAll('.quick-range').forEach(btn=>{
        btn.addEventListener('click',()=>{
            const r=btn.dataset.range;
            switch(r){
                case 'today':setRange(0,0);break;
                case 'yesterday':setRange(1,1);break;
                case '3d':setRange(2,0);break;
                case '5d':setRange(4,0);break;
                case 'all':from.value='';to.value='';break;
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
                label: 'Cantidad de usuarios',
                data,
                backgroundColor: ['#ef4444', '#facc15', '#22c55e'], // rojo, amarillo, verde
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
