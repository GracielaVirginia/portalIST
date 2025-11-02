@extends('layouts.app', ['topbar' => true, 'navbar' => true])

@section('title', 'Mis citas')

@section('content')
<div
  x-data="citasUI()"
  x-init="initCalendar()"
  class="container mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-8"
>
  {{-- Header --}}
  <div class="mb-4 flex items-center justify-between">
    <h1 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-gray-100">
      Mis citas médicas
    </h1>
    <a href="{{ route('portal.home') }}"
       class="inline-flex items-center rounded-xl border border-gray-200 dark:border-gray-700 px-3 py-1.5 text-sm
              text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-900">
      Volver
    </a>
  </div>

  {{-- Mensaje flash --}}
  @if(session('ok'))
    <div class="mb-4 rounded-xl border border-emerald-300/60 bg-emerald-50 text-emerald-800 px-4 py-2 text-sm">
      {{ session('ok') }}
    </div>
  @endif

  {{-- Calendario --}}
  <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
    <div id="calendar"></div>
  </div>

  {{-- Modal formulario --}}
  <div x-show="modalOpen" x-cloak
       class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
       @keydown.escape.window="modalOpen=false">
    <div class="w-full max-w-lg rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-xl overflow-hidden">
      <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Solicitar cita</h3>
        <button class="rounded-lg px-2 py-1 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                @click="modalOpen=false">Cerrar</button>
      </div>

      <form method="POST" action="{{ route('portal.citas.store') }}" class="p-4 space-y-3">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Fecha</label>
            <input type="date" name="fecha" x-model="form.fecha" :min="todayStr"
                   class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 text-sm"
                   required>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Hora</label>
            <input type="time" name="hora" x-model="form.hora"
                   class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 text-sm"
                   required>
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Motivo</label>
          <input type="text" name="motivo" x-model="form.motivo" maxlength="120" placeholder="Ej: Control general"
                 class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 text-sm"
                 required>
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Comentarios</label>
          <textarea name="comentarios" x-model="form.comentarios" rows="3" maxlength="500"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 text-sm"
                    placeholder="Información adicional (opcional)"></textarea>
        </div>

        <div class="mt-2 flex items-center justify-end gap-2">
          <button type="button"
                  @click="modalOpen=false"
                  class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-950 hover:bg-gray-50 dark:hover:bg-gray-900
                         px-3 py-1.5 text-sm text-gray-700 dark:text-gray-200">Cancelar</button>
          <button type="submit"
                  class="rounded-xl border border-purple-900/20 dark:border-purple-300/20 bg-purple-900 text-white px-4 py-2 text-sm font-semibold hover:opacity-90">
            Guardar cita
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- FullCalendar CDN --}}
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.11/locales/es.global.min.js"></script>

<script>
  function citasUI() {
    return {
      calendar: null,
      modalOpen: false,
      todayStr: new Date().toISOString().slice(0,10),
      form: { fecha: '', hora: '', motivo: '', comentarios: '' },

      initCalendar() {
        const el = document.getElementById('calendar');
        const self = this;

        self.calendar = new FullCalendar.Calendar(el, {
          initialView: 'dayGridMonth',
          height: 'auto',
          locale: 'es',
          firstDay: 1,
          headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek'
          },
          // Restringir días pasados
          validRange: { start: self.todayStr },

          dateClick(info) {
            const clicked = info.dateStr;
            if (clicked < self.todayStr) return;
            self.form.fecha = clicked;

            // Hora sugerida
            const now = new Date();
            self.form.hora = (clicked === self.todayStr)
              ? `${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`
              : '09:00';
            self.form.motivo = '';
            self.form.comentarios = '';
            self.modalOpen = true;
          },

          editable: false,
          selectable: false,
        });

        self.calendar.render();
      }
    }
  }
</script>
@endsection
