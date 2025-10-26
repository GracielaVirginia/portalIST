// resources/js/stats-chart.js

// ✅ Chart.js v4: registra todo para evitar "bar is not a registered controller"
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

console.log('[chart] módulo cargado, Chart.js =', Chart?.version);

(function () {
    function attach() {
        console.log('[chart] attach()');

        const panel = document.getElementById('statsChartPanel');
        if (!panel) return console.warn('[chart] no existe #statsChartPanel en el DOM');

        const ENDPOINT = panel.dataset.endpoint;
        const fechaEl = document.getElementById('chartFecha');
        const sedesBox = document.getElementById('chartSedes');
        const canvas = document.getElementById('chartSedesCanvas');
        const msgEl = document.getElementById('chartMsg');

        if (!ENDPOINT) {
            console.error('[chart] Falta data-endpoint en #statsChartPanel');
            return;
        }
        if (!canvas) {
            console.error('[chart] Falta <canvas id="chartSedesCanvas">');
            return;
        }

        // Forzar altura visible del canvas si el CSS no lo setea
        const parent = canvas.parentElement;
        if (parent && !parent.style.height) {
            parent.style.height = '360px';
        }

        let chart;

        function buildChart(labels, dataExamenes, dataUsuarios) {
            if (chart) chart.destroy();
            const ctx = canvas.getContext('2d');

            chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Exámenes realizados',
                            data: dataExamenes,
                            backgroundColor: '#16a34a', // verde
                            stack: 'stack1'
                        },
                        {
                            label: 'Usuarios registrados',
                            data: dataUsuarios,
                            backgroundColor: '#dc2626', // rojo
                            stack: 'stack1'
                        }
                    ]
                },
                options: {
                    indexAxis: 'y',              // barras horizontales
                    responsive: true,
                    maintainAspectRatio: false,  // respetar altura del contenedor
                    scales: {
                        x: { stacked: true, beginAtZero: true },
                        y: { stacked: true }
                    },
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: { mode: 'nearest', intersect: false }
                    }
                }
            });

            console.log('[chart] chart creado con', labels.length, 'sedes');
        }

        function readCheckedSedes() {
            return Array.from(sedesBox.querySelectorAll('input[type="checkbox"]:checked'))
                .map(chk => chk.value);
        }

        function renderSedesCheckboxes(sedes) {
            sedesBox.innerHTML = '';
            if (!sedes || sedes.length === 0) {
                sedesBox.innerHTML = '<div class="text-xs text-slate-500">Sin sedes para esta fecha</div>';
                return;
            }
            sedes.forEach(s => {
                const id = 'sede_' + btoa(unescape(encodeURIComponent(s))).replace(/=+$/, '');
                const wrap = document.createElement('label');
                wrap.className = 'flex items-center gap-2 text-sm text-slate-700 dark:text-slate-100 cursor-pointer select-none';
                wrap.innerHTML = `
          <input type="checkbox" id="${id}" value="${s}" class="accent-sky-600" checked>
          <span>${s}</span>
        `;
                sedesBox.appendChild(wrap);
            });
            sedesBox.querySelectorAll('input[type="checkbox"]').forEach(chk => {
                chk.addEventListener('change', cargarChart);
            });
        }

        async function cargarChart() {
            msgEl.textContent = 'Cargando…';

            const params = new URLSearchParams();
            if (fechaEl.value) params.set('fecha', fechaEl.value);

            const url = `${ENDPOINT}?${params.toString()}`;
            console.log('[chart] fetch', url);

            try {
                const res = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                console.log('[chart] status', res.status);

                let data;
                try {
                    data = await res.json();
                } catch {
                    const raw = await res.text();
                    console.error('[chart] respuesta no-JSON (HTML):\n', raw);
                    throw new Error('Respuesta no JSON del servidor');
                }
                console.log('[chart] data', data);

                if (!res.ok || !data.ok) {
                    const msg = (data && data.error) ? data.error : `HTTP ${res.status}`;
                    throw new Error(msg);
                }

                // Sembrar checkboxes solo la primera vez
                if (sedesBox.childElementCount === 0) {
                    renderSedesCheckboxes(data.sedes || []);
                }

                // Filtrar por sedes seleccionadas
                const selected = new Set(readCheckedSedes());
                const rows = (data.series || []).filter(r => selected.size === 0 || selected.has(r.sede));

                const labels = rows.map(r => r.sede);
                const dataEx = rows.map(r => r.examenes);
                const dataUs = rows.map(r => r.usuarios);

                buildChart(labels, dataEx, dataUs);
                msgEl.textContent = `Fecha: ${data.fecha} — Sedes mostradas: ${labels.length}`;
            } catch (e) {
                console.error('[chart] error', e);
                msgEl.textContent = 'No se pudo cargar el gráfico.';
            }
        }

        // Reaccionar al calendario
        window.addEventListener('calendar:select', (e) => {
            console.log('[chart] recibido calendar:select', e.detail);
            fechaEl.value = e.detail.date;
            cargarChart();
        });

        // Carga inicial
        if (!fechaEl.value) {
            const d = new Date(), pad = n => String(n).padStart(2, '0');
            fechaEl.value = `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
        }
        cargarChart();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', attach);
    } else {
        attach();
    }
})();
