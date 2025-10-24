(function () {
    const pad = (n) => String(n).padStart(2, '0');
    const fmt = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
    const inRange = (iso, min, max) => {
        if (min && iso < min) return false;
        if (max && iso > max) return false;
        return true;
    };

    function monthMatrix(year, month, firstDay) {
        // month: 0-11
        const first = new Date(year, month, 1);
        const startOffset = ((first.getDay() - firstDay + 7) % 7);
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        const cells = [];
        // previous month trailing
        const prevDays = startOffset;
        const prevMonthDays = new Date(year, month, 0).getDate();
        for (let i = prevDays; i > 0; i--) {
            const d = new Date(year, month - 1, prevMonthDays - i + 1);
            cells.push({ date: d, outside: true });
        }
        // current month
        for (let d = 1; d <= daysInMonth; d++) {
            cells.push({ date: new Date(year, month, d), outside: false });
        }
        // next month leading
        while (cells.length % 7 !== 0) {
            const d = new Date(year, month, daysInMonth + (cells.length % 7) + 1);
            cells.push({ date: d, outside: true });
        }
        return cells;
    }

    function renderCalendar(root) {
        const titleEl = root.querySelector('[data-title]');
        const gridEl = root.querySelector('[data-grid]');
        const weekdaysEl = root.querySelector('[data-weekdays]');
        const firstDay = Number(root.dataset.firstDay || 1);
        const locale = root.dataset.locale || 'es-CL';
        const min = root.dataset.min || null;
        const max = root.dataset.max || null;

        // estado
        const today = new Date();
        const initValue = root.dataset.value ? new Date(root.dataset.value) : null;
        let cursor = initValue ? new Date(initValue.getFullYear(), initValue.getMonth(), 1)
            : new Date(today.getFullYear(), today.getMonth(), 1);
        let selectedISO = initValue ? fmt(initValue) : null;

        // semana (L → D si firstDay=1)
        const baseWeek = [...Array(7).keys()].map(i => new Date(2024, 8, i + 1)); // semana dummy
        const order = [...Array(7).keys()].map(i => (i + firstDay) % 7);
        weekdaysEl.innerHTML = '';
        order.forEach(dow => {
            const label = baseWeek[dow].toLocaleDateString(locale, { weekday: 'short' });
            const span = document.createElement('span');
            span.className = 'cal-week';
            span.textContent = label.charAt(0).toUpperCase() + label.slice(1, 3);
            weekdaysEl.appendChild(span);
        });

        function paint() {
            const y = cursor.getFullYear();
            const m = cursor.getMonth();
            titleEl.textContent = new Date(y, m, 1).toLocaleDateString(locale, { month: 'long', year: 'numeric' });

            const cells = monthMatrix(y, m, firstDay);
            gridEl.innerHTML = '';
            cells.forEach(({ date, outside }) => {
                const iso = fmt(date);
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'cal-cell';
                btn.dataset.date = iso;
                btn.setAttribute('role', 'gridcell');
                btn.textContent = String(date.getDate());

                if (outside) btn.classList.add('cal-outside');
                if (iso === fmt(today)) btn.classList.add('cal-today');
                if (selectedISO === iso) btn.classList.add('cal-selected');
                if (!inRange(iso, min, max)) {
                    btn.classList.add('cal-disabled');
                    btn.disabled = true;
                }

                btn.addEventListener('click', () => {
                    // actualizar selección visual
                    gridEl.querySelectorAll('.cal-cell.cal-selected').forEach(e => e.classList.remove('cal-selected'));
                    btn.classList.add('cal-selected');
                    selectedISO = iso;

                    // emitir evento global y también en el root
                    const detail = { date: iso, year: date.getFullYear(), month: date.getMonth() + 1, day: date.getDate(), sourceId: root.id };
                    console.log('[calendar] emit calendar:select', detail); 
                    window.dispatchEvent(new CustomEvent('calendar:select', { detail }));
                    root.dispatchEvent(new CustomEvent('calendar:select', { detail }));
                });

                gridEl.appendChild(btn);
            });
        }

        // navegación
        root.querySelectorAll('.cal-nav').forEach(b => {
            b.addEventListener('click', () => {
                const action = b.dataset.action;
                cursor = new Date(cursor.getFullYear(), cursor.getMonth() + (action === 'prev' ? -1 : 1), 1);
                paint();
            });
        });

        paint();
    }

    // auto-init
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-calendar]').forEach(renderCalendar);
    });
})();
