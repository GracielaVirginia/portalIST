{{-- resources/views/test-layout.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 text-gray-900 dark:text-gray-100 space-y-4">

  {{-- (opcional) header --}}
  <div class="relative overflow-hidden rounded-2xl shadow-sm">
    <div class="absolute inset-0 bg-gradient-to-r from-purple-700 via-fuchsia-600 to-purple-500 opacity-95"></div>
    <div class="relative px-5 py-6 sm:px-8">
      <div class="flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="flex-1 min-w-0">
          <button type="button"
                  class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white/10 text-white hover:bg-white/20 ring-1 ring-white/20 focus:outline-none focus:ring-2 focus:ring-white/50"
                  onclick="document.referrer ? history.back() : window.location.assign('{{ url('/') }}')"
                  aria-label="Volver atrás">
            {{-- flecha --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <span class="hidden sm:inline">Volver</span>
          </button>
          <h1 class="text-2xl sm:text-3xl font-bold text-white">Agendar Cita</h1>
          <p class="text-purple-100/90 text-sm">
            Selecciona un profesional para ver disponibilidad.
          </p>
        </div>

        <div class="w-full sm:w-[360px]">
          <label for="selectMedico" class="block text-sm font-medium text-purple-100">Profesional</label>
          <select id="selectMedico"
                  class="mt-1 w-full px-3 py-2 rounded-lg border-0 outline-none 
                         ring-2 ring-white/20 focus:ring-white/40 bg-white/10 backdrop-blur text-white 
                         animate-pulse-shadow-white">
            <option value="">— Selecciona —</option>
            @foreach($profesionales as $p)
              <option value="{{ $p->id }}" class="text-gray-900">
                {{ trim(($p->nombres ?? '').' '.($p->apellidos ?? '')) }}
                {{ $p->tipoProfesional->nombre ? ' — '.$p->tipoProfesional->nombre : '' }}
              </option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
  </div>

  {{-- FILA INFERIOR: FLEX 3/4 + 1/4 --}}
  <div id="filaFlex" class="flex items-start gap-4 overflow-x-auto">

    {{-- IZQUIERDA: 3/4 (calendario) --}}
    <div class="basis-3/4 grow min-w-[600px] rounded-2xl border border-purple-100/60 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm">
      <div class="p-3 sm:p-4">
        <div id="calendar" class="min-h-[70vh] h-full"></div>
      </div>
    </div>

    {{-- DERECHA: 1/4 (mini-cal) --}}
    <div class="basis-1/4 shrink-0 min-w-[320px] rounded-2xl border border-purple-100/60 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm">
      <div class="p-4 sm:p-5">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Filtrar por fecha</h3>

        {{-- Mini calendario con tu skin .cal-* --}}
        <div id="miniCal" class="cal-root">
          <div class="cal-header">
            <button id="calPrev" type="button" class="cal-nav" aria-label="Mes anterior">‹</button>
            <div class="cal-title" id="calTitle">—</div>
            <button id="calNext" type="button" class="cal-nav" aria-label="Mes siguiente">›</button>
          </div>

          <div class="cal-weekdays">
            <div>Lu</div><div>Ma</div><div>Mi</div><div>Ju</div><div>Vi</div><div>Sa</div><div>Do</div>
          </div>

          <div id="calGrid" class="cal-grid"><!-- JS llena aquí --></div>
        </div>

      </div>
    </div>
  </div>
</div>

{{-- Modal crear cita desde parcial --}}
@include('modales.modal_crear_cita', ['action' => route('agenda.store')])
@endsection

@push('scripts')
{{-- FullCalendar + locales ES y SweetAlert --}}
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales-all.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Skin Morado para el mini-cal + tema púrpura para FullCalendar --}}
<style>
/* ===========================
   Calendario - Skin Morado
   =========================== */
.cal-root{max-width:320px;border-radius:16px;background:#f5f3ff;padding:12px;box-shadow:0 4px 12px rgba(109,40,217,.12);font-family:system-ui,sans-serif}
.cal-header{display:flex;align-items:center;justify-content:space-between;background:#fff;border-radius:12px;padding:10px 12px;color:#1e1b4b;border:1px solid #e9d5ff;box-shadow:0 2px 4px rgba(0,0,0,.04)}
.cal-title{font-weight:700;text-transform:uppercase;letter-spacing:.06em;font-size:.95rem;color:#7e22ce}
.cal-nav{background:transparent;border:none;color:#1e1b4b;font-size:20px;line-height:1;cursor:pointer;padding:4px 6px;border-radius:8px;transition:all .15s}
.cal-nav:hover{background:rgba(126,34,206,.1);color:#7e22ce}
.cal-weekdays{display:grid;grid-template-columns:repeat(7,1fr);gap:6px;margin-top:12px;color:#7e22ce;font-size:.8rem;font-weight:600;text-align:center}
.cal-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:6px;margin-top:8px;background:#fff;padding:12px;border-radius:12px;border:1px solid #e9d5ff;box-shadow:0 2px 4px rgba(0,0,0,.03)}
.cal-cell{width:100%;aspect-ratio:1/1;border:none;border-radius:8px;cursor:pointer;background:transparent;color:#1e1b4b;font-weight:600;font-size:.9rem;display:flex;align-items:center;justify-content:center;transition:all .15s}
.cal-cell:hover:not(.cal-disabled):not(.cal-selected){background:rgba(126,34,206,.12);transform:translateY(-1px)}
.cal-outside{color:#7e22ce;opacity:.6}
.cal-today{position:relative;color:#7e22ce;font-weight:700}
.cal-today::after{content:'';position:absolute;bottom:4px;width:4px;height:4px;background:#7e22ce;border-radius:50%}
.cal-selected{background:#6d28d9;color:#fff !important;font-weight:700;box-shadow:0 2px 6px rgba(126,34,206,.3);transform:translateY(-1px)}
.cal-disabled{opacity:.4;cursor:not-allowed;color:#7e22ce}
.dark .cal-root{background:#0f0c29;color:#e0d6ff}
.dark .cal-header{background:#1a1446;border-color:#2d1b69;color:#e0d6ff}
.dark .cal-title{color:#a78bfa}
.dark .cal-nav{color:#e0d6ff}
.dark .cal-nav:hover{background:rgba(126,34,206,.1);color:#7e22ce}
.dark .cal-weekdays{color:#a78bfa}
.dark .cal-grid{background:#1a1446;border-color:#2d1b69}
.dark .cal-cell{color:#e0d6ff}
.dark .cal-outside{color:#a78bfa}
.dark .cal-today{color:#7e22ce}
.dark .cal-selected{background:#7e22ce;color:#0b1020 !important}
.dark .cal-disabled{color:#a78bfa}

/* FullCalendar tema púrpura (variables) */
.fc{
  --fc-border-color: rgba(124,58,237,.15);
  --fc-page-bg-color: transparent;
  --fc-neutral-bg-color: rgba(124,58,237,.04);
  --fc-today-bg-color: rgba(124,58,237,.08);
  --fc-now-indicator-color:#7c3aed;
  --fc-button-text-color:#fff;
  --fc-button-bg-color:#7c3aed;
  --fc-button-border-color:#7c3aed;
  --fc-button-hover-bg-color:#6d28d9;
  --fc-button-hover-border-color:#6d28d9;
  --fc-button-active-bg-color:#5b21b6;
  --fc-button-active-border-color:#5b21b6;
  --fc-highlight-color: rgba(124,58,237,.25);
  --fc-event-bg-color:#7c3aed;
  --fc-event-border-color:#7c3aed;
  --fc-event-text-color:#fff;
}
.fc .fc-toolbar-title{ color:#6d28d9; font-weight:800; letter-spacing:-0.01em; }
.fc .fc-button-primary{ background:var(--fc-button-bg-color)!important;border:none!important; }
.fc .fc-button-primary:hover{ background:var(--fc-button-hover-bg-color)!important; }
.fc .fc-button-primary:disabled{ opacity:.55; }
.fc .fc-timegrid-slot{ height: 1.75rem; }
.fc-theme-standard td, .fc-theme-standard th{ border-color: rgba(124,58,237,.15); }

/* Borde pulsante para el select del profesional */
@keyframes pulse-shadow-white {
  0%, 100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7); }
  50%      { box-shadow: 0 0 0 12px rgba(255, 255, 255, 0); }
}
.animate-pulse-shadow-white { animation: pulse-shadow-white 3s infinite ease-in-out; }
</style>

<script>
/* ===== SweetAlert mixin ===== */
window.Swal = Swal.mixin({
  customClass: {
    confirmButton: 'bg-purple-700 hover:bg-purple-800 text-white font-semibold px-4 py-2 rounded-lg',
    cancelButton:  'bg-gray-300 hover:bg-gray-400 text-gray-900 font-semibold px-4 py-2 rounded-lg ml-2'
  },
  buttonsStyling: false,
  confirmButtonText: 'OK'
});
</script>

<script>
/* ===== Estado / helpers ===== */
const DBG = true;
function dlog(...a){ if(DBG) console.log('[AGENDA]', ...a); }

const COLOR_DISPONIBLE = '#f3e8ff';
let calendar;
let BUSINESS_HOURS  = [];
let BG_DISPONIBLES = [];
let profesionalId   = null;

function sumarMinutos(hora, minutos){
  const [h, m] = hora.split(':').map(Number);
  const d = new Date(0,0,0,h,m + parseInt(minutos||0,10));
  return `${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`;
}
function isWithinBH(dateStart, dateEnd){
  if (!Array.isArray(BUSINESS_HOURS) || BUSINESS_HOURS.length===0) return false;
  const day = dateStart.getDay();
  const toHM = (d)=> `${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`;
  const sHM = toHM(dateStart), eHM = toHM(dateEnd);
  return BUSINESS_HOURS.some(bh=>{
    if (!bh.daysOfWeek?.includes(day)) return false;
    return (sHM >= bh.startTime && eHM <= bh.endTime);
  });
}
function buildDisponiblesFromBH(bh){
  const out = [];
  (bh || []).forEach(b => {
    (b.daysOfWeek || []).forEach(d => {
      out.push({
        display:'background',
        color: COLOR_DISPONIBLE,
        groupId:'disponible',
        daysOfWeek:[d],
        startTime:b.startTime,
        endTime:b.endTime
      });
    });
  });
  return out;
}
function abrirModal(){ document.getElementById('modalCrearCita')?.classList.replace('hidden','flex'); }
function cerrarModal(){ const m = document.getElementById('modalCrearCita'); if(!m) return; m.classList.add('hidden'); m.classList.remove('flex'); }
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // ===== FullCalendar =====
  const calendarEl = document.getElementById('calendar');
  calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'timeGridWeek',
    headerToolbar: { left:'prev,next today', center:'title', right:'dayGridMonth,timeGridWeek,timeGridDay' },
    allDaySlot: false,
    slotMinTime: '08:00:00',
    slotMaxTime: '20:00:00',
    scrollTime: '08:00:00',
    slotDuration: '00:10:00',
    eventDisplay: 'block',
    businessHours: [],
    timeZone: 'local',
    locale: 'es',
    height: '100%', expandRows: true, contentHeight: 'auto',

    eventTimeFormat: { hour: '2-digit', minute: '2-digit', meridiem: false },

    eventSources: [
      // 📅 Citas reales (desde backend)
      {
        events: (info, success, failure) => {
          if (!profesionalId) { success([]); return; }

          const url = `{{ url('/agenda') }}/${encodeURIComponent(profesionalId)}/eventos-visibles`
                    + `?start=${encodeURIComponent(info.startStr)}&end=${encodeURIComponent(info.endStr)}`;

          dlog('fetch eventos-visibles:', url);
          fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(r => {
              if (!r.ok) throw new Error('HTTP ' + r.status);
              return r.json();
            })
            .then(data => {
              // Asegúrate que 'data' sea un array de eventos: [{id,title,start,end,...}]
              dlog('eventos-visibles resp:', data);
              success(Array.isArray(data) ? data : []);
            })
            .catch(err => {
              console.error('[AGENDA] eventos-visibles error:', err);
              failure(err);
            });
        }
      },

      // 🎨 Fondos de disponibilidad
      { events: (i, ok) => ok(BG_DISPONIBLES) }
    ],

    datesSet(){ calendar.updateSize(); },

    dateClick(info){
      calendar.changeView('timeGridDay', info.date);
      // mini-cal se sincroniza en datesSet
    },

    selectable: true,
    selectConstraint: 'businessHours',
    selectAllow(selInfo){
      if (!BUSINESS_HOURS.length) { Swal.fire('Sin disponibilidad','El profesional no tiene horarios configurados.','info'); return false; }
      const ok = isWithinBH(selInfo.start, selInfo.end);
      if (!ok) Swal.fire('No disponible','Ese tramo está fuera del horario disponible.','warning');
      return ok;
    },
    select(info){
      const hoy0 = new Date(); hoy0.setHours(0,0,0,0);
      if (info.start < hoy0) { Swal.fire('Aviso','No puedes crear citas en el pasado.','info'); return; }

      const fecha = info.startStr.split('T')[0];
      const horaIni = info.startStr.split('T')[1].slice(0,5);
      const horaFin = info.endStr?.split('T')[1]?.slice(0,5) || sumarMinutos(horaIni, 30);

      document.getElementById('medico_id').value = profesionalId || '';
      document.getElementById('fechaCita').value = fecha;
      document.getElementById('horaIni').value = horaIni;
      document.getElementById('horaFin').value = horaFin;

      const btnBloq = document.getElementById('btnBloquear');
      const infoBloq = document.getElementById('bloqInfo');
      if (btnBloq){
        btnBloq.disabled = false;
        infoBloq?.classList.remove('hidden');
        if (infoBloq) infoBloq.textContent = `Tramo seleccionado: ${fecha} de ${horaIni} a ${horaFin}`;
      }
      abrirModal();
    },

    eventClick(info){
      if (info.event.display === 'background') return;
      const e = info.event;
      const fecha = e.startStr?.split('T')[0] || '';
      const hIni  = e.startStr?.split('T')[1]?.slice(0,5) || '';
      const hFin  = (e.end ? e.end.toTimeString().slice(0,5) : (e.endStr?.split('T')[1]?.slice(0,5) || ''));
      Swal.fire({
        title: e.title || 'Cita',
        html: `<div class="text-left space-y-1">
                 <div><b>Fecha:</b> ${fecha}</div>
                 <div><b>Horario:</b> ${hIni} – ${hFin}</div>
                 <div><b>Estado:</b> ${e.extendedProps?.estado || 'pendiente'}</div>
                 <div><b>Tipo:</b> ${e.extendedProps?.tipo_atencion || 'presencial'}</div>
               </div>`,
        showCancelButton: true, confirmButtonText:'Editar', cancelButtonText:'Cerrar'
      }).then(res=>{ if (res.isConfirmed){ Swal.fire('Próximo paso','Abrir modal de edición (pendiente).','info'); } });
    },

    eventMouseEnter(info){
      if (info.event.display === 'background') return;
      const el = info.el;
      const host = el.querySelector('.fc-event-main, .fc-event-main-frame, .fc-event-inner') || el;
      if (!host) return;
      el.__orig = host.innerHTML;
      host.innerHTML = '<div class="text-[11px] leading-tight space-y-0.5"><span class="block">✔️ Acción</span><span class="block">🗓️ Reprogramar</span></div>';
    },
    eventMouseLeave(info){
      const el = info.el;
      const host = el.querySelector('.fc-event-main, .fc-event-main-frame, .fc-event-inner') || el;
      if (el.__orig) host.innerHTML = el.__orig;
    },

    editable: true,
    eventDrop(info){
      if (info.event.display === 'background'){ info.revert(); return; }
      const hoy0 = new Date(); hoy0.setHours(0,0,0,0);
      if (info.event.start < hoy0){ Swal.fire('Error','No puedes mover a una fecha pasada.','error'); info.revert(); return; }
      const id   = info.event.id;
      const fecha= info.event.startStr.split('T')[0];
      const hIni = info.event.startStr.split('T')[1].slice(0,5);
      const hFin = (info.event.end ? info.event.end.toTimeString().slice(0,5) : sumarMinutos(hIni, 30));

      Swal.fire({
        title:'¿Confirmar cambio?', text:`Mover a ${fecha} ${hIni} - ${hFin}`, icon:'warning',
        showCancelButton:true, confirmButtonText:'Sí, mover', cancelButtonText:'Cancelar'
      }).then(res=>{
        if (!res.isConfirmed){ info.revert(); return; }
        fetch(`{{ route('agenda.verificar-disponibilidad') }}`,{
          method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
          body:JSON.stringify({ id, fecha, hora_inicio:hIni, hora_fin:hFin })
        })
        .then(r=>r.json())
        .then(data=>{
          if(!data?.disponible){ Swal.fire('No disponible','Choque de horario o fuera de horario.','warning'); info.revert(); return; }
          const url = `{{ route('agenda.mover', ['id'=>'__ID__']) }}`.replace('__ID__', id);
          return fetch(url,{ method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body:JSON.stringify({ fecha, hora_inicio:hIni, hora_fin:hFin })});
        })
        .then(r=> r ? r.json() : null)
        .then(save=>{
          if (save && !save.success){ Swal.fire('Error', save.message || 'No se pudo guardar.','error'); info.revert(); }
          else if (save) { Swal.fire('Éxito','Cita movida.','success'); }
        })
        .catch(()=>{ Swal.fire('Error','Fallo de red.','error'); info.revert(); });
      });
    },
    eventResize(info){
      if (info.event.display === 'background'){ info.revert(); return; }
      const hoy0 = new Date(); hoy0.setHours(0,0,0,0);
      if (info.event.start < hoy0){ Swal.fire('Error','No puedes redimensionar a pasado.','error'); info.revert(); return; }
      const id   = info.event.id;
      const fecha= info.event.startStr.split('T')[0];
      const hIni = info.event.startStr.split('T')[1].slice(0,5);
      const hFin = info.event.end?.toTimeString().slice(0,5) || sumarMinutos(hIni,30);

      fetch(`{{ route('agenda.verificar-disponibilidad') }}`,{
        method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body:JSON.stringify({ id, fecha, hora_inicio:hIni, hora_fin:hFin })
      })
      .then(r=>r.json())
      .then(data=>{
        if(!data?.disponible){ Swal.fire('No disponible','Choque de horario o fuera de horario.','warning'); info.revert(); return; }
        Swal.fire('Listo','(Pendiente) Guardar nueva duración.','success');
        // TODO: POST a tu endpoint para persistir hora_fin
      })
      .catch(()=>{ Swal.fire('Error','Fallo al verificar.','error'); info.revert(); });
    },
  });
  window.fcCalendar = calendar;
  calendar.render();
  window.addEventListener('load',  ()=> calendar.updateSize());
  window.addEventListener('resize',()=> calendar.updateSize());

  // ===== SELECT PROFESIONAL =====
  const sel = document.getElementById('selectMedico');
  async function onProfesionalChange(e){
    const raw = e.target.value;
    const trimmed = (raw ?? '').toString().trim();
    profesionalId = trimmed === '' ? null : trimmed;

    if (!profesionalId){
      BUSINESS_HOURS = []; BG_DISPONIBLES = [];
      calendar.setOption('businessHours', []);
      calendar.refetchEvents(); setTimeout(()=>calendar.updateSize(),0);
      return;
    }
    try{
      const url = `{{ url('/agenda') }}/${encodeURIComponent(profesionalId)}/horarios`;
      const res  = await fetch(url, { headers:{ 'Accept':'application/json' }});
      if (!res.ok) throw new Error('HTTP ' + res.status);
      const data = await res.json();

      BUSINESS_HOURS  = Array.isArray(data.businessHours) ? data.businessHours : [];
      BG_DISPONIBLES  = buildDisponiblesFromBH(BUSINESS_HOURS);

      calendar.setOption('businessHours', BUSINESS_HOURS);
      calendar.refetchEvents(); setTimeout(()=>calendar.updateSize(),0);

      if (!BUSINESS_HOURS.length){
        Swal.fire('Sin disponibilidad','Este profesional no tiene rangos configurados.','info');
      }
    }catch(err){
      console.error('[AGENDA] Error horarios:', err);
      BUSINESS_HOURS = []; BG_DISPONIBLES = [];
      calendar.setOption('businessHours', []);
      calendar.refetchEvents(); setTimeout(()=>calendar.updateSize(),0);
      Swal.fire('Error','No se pudieron cargar los horarios.','error');
    }
  }
  sel.addEventListener('change', onProfesionalChange);
  sel.addEventListener('input',  onProfesionalChange);
  if (sel.value && sel.value.toString().trim() !== '') {
    sel.dispatchEvent(new Event('change', {bubbles:true}));
  }

  // ===== Botón “Bloquear segmento seleccionado” (si existe en tu modal) =====
  document.getElementById('btnBloquear')?.addEventListener('click', ()=>{
    const fecha = document.getElementById('fechaCita')?.value;
    const hIni  = document.getElementById('horaIni')?.value;
    const hFin  = document.getElementById('horaFin')?.value;
    if (!fecha || !hIni || !hFin){ Swal.fire('Aviso','No hay tramo seleccionado.','info'); return; }
    Swal.fire('Bloquear','(Pendiente) Llamar a endpoint de bloqueo con '+fecha+' '+hIni+'-'+hFin,'info');
  });

  /* ===== Mini calendario (usa skin .cal-*) ===== */
  (function(){
    const grid   = document.getElementById('calGrid');
    const title  = document.getElementById('calTitle');
    const btnPrev= document.getElementById('calPrev');
    const btnNext= document.getElementById('calNext');
    if (!grid || !title) return;

    let viewDate = new Date(); viewDate.setDate(1);
    let selected = new Date(); selected.setHours(0,0,0,0);
    const hoy0 = new Date(); hoy0.setHours(0,0,0,0);
    const MONTHS_ES = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];

    function isSameDay(a,b){ return a.getFullYear()===b.getFullYear() && a.getMonth()===b.getMonth() && a.getDate()===b.getDate(); }

    function renderMini(){
      title.textContent = `${MONTHS_ES[viewDate.getMonth()]?.toUpperCase() || ''} ${viewDate.getFullYear()}`;
      grid.innerHTML = '';
      const y=viewDate.getFullYear(), m=viewDate.getMonth();
      const first = new Date(y, m, 1);
      let startIdx = first.getDay() - 1; if (startIdx < 0) startIdx = 6; // lunes 0
      const daysInMonth = new Date(y, m+1, 0).getDate();
      const daysPrev    = new Date(y, m, 0).getDate();

      for (let i=0;i<42;i++){
        const cell = document.createElement('button'); cell.type='button'; cell.className='cal-cell';
        let day, dateObj, outside=false;
        if (i < startIdx){ day = daysPrev - (startIdx - 1 - i); dateObj = new Date(y, m-1, day); outside=true; }
        else if (i >= startIdx + daysInMonth){ day = i - (startIdx + daysInMonth) + 1; dateObj = new Date(y, m+1, day); outside=true; }
        else { day = i - startIdx + 1; dateObj = new Date(y, m, day); }
        dateObj.setHours(0,0,0,0);
        cell.textContent = day;
        if (outside) cell.classList.add('cal-outside');
        if (isSameDay(dateObj, new Date())) cell.classList.add('cal-today');
        if (isSameDay(dateObj, selected))   cell.classList.add('cal-selected');
        if (dateObj < hoy0)                 cell.classList.add('cal-disabled');

        cell.addEventListener('click', ()=>{
          if (dateObj < hoy0) return;
          selected = new Date(dateObj);
          renderMini();
          if (window.fcCalendar){ window.fcCalendar.changeView('timeGridDay', selected); }
        });
        grid.appendChild(cell);
      }
    }

    btnPrev?.addEventListener('click', ()=>{ viewDate.setMonth(viewDate.getMonth()-1); renderMini(); });
    btnNext?.addEventListener('click', ()=>{ viewDate.setMonth(viewDate.getMonth()+1); renderMini(); });

    if (window.fcCalendar){
      window.fcCalendar.on('datesSet', (arg)=>{
        const current = window.fcCalendar.getDate ? new Date(window.fcCalendar.getDate()) : new Date(arg.start);
        current.setHours(0,0,0,0);
        selected = current;
        if (viewDate.getFullYear() !== selected.getFullYear() || viewDate.getMonth() !== selected.getMonth()){
          viewDate = new Date(selected.getFullYear(), selected.getMonth(), 1);
        }
        renderMini();
      });
    }
    renderMini();
  })();
});
</script>
@endpush
